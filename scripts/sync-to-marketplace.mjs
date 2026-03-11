#!/usr/bin/env node
/**
 * sync-to-marketplace.mjs
 *
 * Reads every products/{product}/manifest.json and creates/updates
 * the corresponding product in the MarketPlace via its REST API.
 *
 * Usage:
 *   node scripts/sync-to-marketplace.mjs
 *   MARKETPLACE_URL=https://your-marketplace.vercel.app node scripts/sync-to-marketplace.mjs
 *   DRY_RUN=1 node scripts/sync-to-marketplace.mjs   ← preview without writing
 */

import { readdir, readFile, stat } from "fs/promises";
import { createHash } from "crypto";
import path from "path";
import { fileURLToPath } from "url";

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const ROOT = path.resolve(__dirname, "..");

// ── Config ────────────────────────────────────────────────────────────────────
const MARKETPLACE_URL = (process.env.MARKETPLACE_URL || "http://localhost:3000").replace(/\/$/, "");
const DRY_RUN = process.env.DRY_RUN === "1";
const ACTOR = process.env.SYNC_ACTOR || "sync-pipeline";

// ── Helpers ───────────────────────────────────────────────────────────────────
async function api(method, endpoint, body) {
  if (DRY_RUN) {
    console.log(`  [DRY RUN] ${method} ${MARKETPLACE_URL}${endpoint}`);
    if (body) console.log("  Payload:", JSON.stringify(body, null, 2));
    return { id: "dry-run-id", slug: body?.slug };
  }
  const res = await fetch(`${MARKETPLACE_URL}${endpoint}`, {
    method,
    headers: {
      "Content-Type": "application/json",
      "x-admin-actor": ACTOR,
    },
    body: body ? JSON.stringify(body) : undefined,
  });
  const json = await res.json();
  if (!res.ok) throw new Error(`${method} ${endpoint} → ${res.status}: ${JSON.stringify(json)}`);
  return json;
}

async function getAllProducts() {
  const data = await api("GET", "/api/admin/products");
  return data.items || [];
}

async function sha256File(filePath) {
  const buf = await readFile(filePath);
  return createHash("sha256").update(buf).digest("hex");
}

async function fileSizeBytes(filePath) {
  const s = await stat(filePath);
  return s.size;
}

// ── Main ──────────────────────────────────────────────────────────────────────
async function main() {
  console.log(`\n🔄 Syncing OpenCartThemes products → MarketPlace`);
  console.log(`   Target: ${MARKETPLACE_URL}`);
  if (DRY_RUN) console.log("   Mode: DRY RUN (no changes will be written)\n");

  // 1. Discover all manifests
  const productsDir = path.join(ROOT, "products");
  let productFolders;
  try {
    productFolders = await readdir(productsDir);
  } catch {
    console.error(`❌ No products/ directory found at ${productsDir}`);
    process.exit(1);
  }

  const manifests = [];
  for (const folder of productFolders) {
    const manifestPath = path.join(productsDir, folder, "manifest.json");
    try {
      const raw = await readFile(manifestPath, "utf8");
      manifests.push({ ...JSON.parse(raw), _folder: folder, _manifestPath: manifestPath });
    } catch {
      // not a product folder
    }
  }

  if (manifests.length === 0) {
    console.log("⚠️  No product manifests found in products/*/manifest.json");
    process.exit(0);
  }

  console.log(`📦 Found ${manifests.length} product manifest(s):\n`);

  // 2. Fetch existing products from MarketPlace
  let existing = [];
  try {
    existing = await getAllProducts();
  } catch (err) {
    console.error(`❌ Cannot reach MarketPlace API at ${MARKETPLACE_URL}`);
    console.error(`   Make sure the MarketPlace dev server is running (npm run dev in MarketPlace/)`);
    console.error(`   Or set MARKETPLACE_URL env var to the deployed URL`);
    console.error(`   Error: ${err.message}`);
    process.exit(1);
  }

  const existingBySlug = Object.fromEntries(existing.map((p) => [p.slug, p]));

  // 3. Create or update each product
  let created = 0, updated = 0, skipped = 0;

  for (const manifest of manifests) {
    const { _folder, _manifestPath, buildFile, ...productPayload } = manifest;
    console.log(`→ ${productPayload.name} (${productPayload.slug})`);

    const existing = existingBySlug[productPayload.slug];

    try {
      let product;
      if (!existing) {
        // CREATE
        product = await api("POST", "/api/admin/products", productPayload);
        console.log(`  ✅ Created  (id: ${product.id})`);
        created++;
      } else {
        // UPDATE — only if version or status changed
        const versionChanged = existing.version !== productPayload.version;
        const statusChanged = existing.status !== productPayload.status;
        const nameChanged = existing.name !== productPayload.name;
        const priceChanged = existing.price !== productPayload.price;
        const demoChanged = existing.demoUrl !== productPayload.demoUrl;

        if (versionChanged || statusChanged || nameChanged || priceChanged || demoChanged) {
          product = await api("PATCH", `/api/admin/products/${existing.id}`, productPayload);
          console.log(`  🔄 Updated  (id: ${existing.id})`);
          updated++;
        } else {
          console.log(`  ⏭️  Skipped  (no changes detected)`);
          skipped++;
          product = existing;
        }
      }

      // 4. Register build file if specified
      if (buildFile && product.id && product.id !== "dry-run-id") {
        const absPath = path.resolve(path.dirname(_manifestPath), buildFile);
        try {
          await stat(absPath);
          // Check if file already registered
          const files = await api("GET", `/api/admin/product-files?productId=${product.id}`);
          const fileItems = files.items || [];
          const checksum = await sha256File(absPath);
          const alreadyRegistered = fileItems.some((f) => f.checksumSha256 === checksum);

          if (!alreadyRegistered) {
            const sizeBytes = await fileSizeBytes(absPath);
            const fileName = path.basename(absPath);
            await api("POST", "/api/admin/product-files", {
              productId: product.id,
              storageProvider: "external",
              // In production replace this with your actual CDN/Vercel Blob URL
              url: `${MARKETPLACE_URL}/downloads/${fileName}`,
              path: fileName,
              mimeType: "application/zip",
              sizeBytes,
              checksumSha256: checksum,
              isPrimary: true,
            });
            console.log(`  📎 Registered build file: ${fileName} (${(sizeBytes / 1024).toFixed(1)} KB)`);
          } else {
            console.log(`  📎 Build file already registered (checksum match)`);
          }
        } catch (err) {
          if (err.code === "ENOENT") {
            console.log(`  ⚠️  Build file not found: ${absPath}`);
          } else {
            throw err;
          }
        }
      }
    } catch (err) {
      console.error(`  ❌ Error: ${err.message}`);
    }
    console.log();
  }

  console.log(`\n✅ Sync complete: ${created} created, ${updated} updated, ${skipped} skipped\n`);
}

main().catch((err) => {
  console.error("Fatal:", err);
  process.exit(1);
});
