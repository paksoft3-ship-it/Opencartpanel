#!/usr/bin/env node
/**
 * sync-direct-db.mjs
 * Syncs product manifests directly to the Neon PostgreSQL database.
 * Use this when the MarketPlace dev server is not running.
 *
 * Reads DATABASE_URL from MarketPlace/.env.local
 */

import { readdir, readFile, stat } from "fs/promises";
import { createHash } from "crypto";
import path from "path";
import { fileURLToPath } from "url";
import { createRequire } from "module";

const __dirname = path.dirname(fileURLToPath(import.meta.url));

// Use pg from MarketPlace's node_modules
const require = createRequire(
  path.resolve(__dirname, "../../MarketPlace/package.json")
);
const { Pool } = require("pg");
const ROOT = path.resolve(__dirname, "..");
const MARKETPLACE_ROOT = path.resolve(ROOT, "../MarketPlace");

// ── Load .env.local from MarketPlace ─────────────────────────────────────────
async function loadEnv() {
  const envPath = path.join(MARKETPLACE_ROOT, ".env.local");
  const content = await readFile(envPath, "utf8");
  for (const line of content.split("\n")) {
    const trimmed = line.trim();
    if (!trimmed || trimmed.startsWith("#")) continue;
    const eq = trimmed.indexOf("=");
    if (eq === -1) continue;
    const key = trimmed.slice(0, eq).trim();
    let val = trimmed.slice(eq + 1).trim();
    if ((val.startsWith('"') && val.endsWith('"')) || (val.startsWith("'") && val.endsWith("'"))) {
      val = val.slice(1, -1);
    }
    if (!process.env[key]) process.env[key] = val;
  }
}

async function sha256File(filePath) {
  const buf = await readFile(filePath);
  return createHash("sha256").update(buf).digest("hex");
}

// ── Main ──────────────────────────────────────────────────────────────────────
async function main() {
  await loadEnv();

  const url = process.env.DATABASE_URL || process.env.POSTGRES_URL;
  if (!url) { console.error("❌ DATABASE_URL not found"); process.exit(1); }

  const pool = new Pool({ connectionString: url, ssl: { rejectUnauthorized: false } });

  console.log("\n🔄 Syncing products → Neon PostgreSQL (direct)\n");

  // 1. Read all manifests
  const productsDir = path.join(ROOT, "products");
  const folders = await readdir(productsDir);
  const manifests = [];
  for (const folder of folders) {
    const mp = path.join(productsDir, folder, "manifest.json");
    try {
      const raw = await readFile(mp, "utf8");
      manifests.push({ ...JSON.parse(raw), _folder: folder, _manifestPath: mp });
    } catch { /* not a product folder */ }
  }

  if (manifests.length === 0) {
    console.log("⚠️  No manifests found"); process.exit(0);
  }

  console.log(`📦 Found ${manifests.length} product manifest(s):\n`);

  // 2. Fetch existing by slug
  const { rows: existing } = await pool.query(`SELECT id, slug, version, status, price, name, demo_url FROM admin_products`);
  const bySlug = Object.fromEntries(existing.map(r => [r.slug, r]));

  let created = 0, updated = 0, skipped = 0;

  for (const manifest of manifests) {
    const { _folder, _manifestPath, buildFile, demoUrl, ...payload } = manifest;
    const demo_url = demoUrl || "";
    console.log(`→ ${payload.name} (${payload.slug})`);

    const ex = bySlug[payload.slug];
    let productId;

    try {
      if (!ex) {
        // CREATE
        const { rows } = await pool.query(
          `INSERT INTO admin_products
            (slug,name,short_description,description,price,category_id,developer_id,
             compatibility,images,features,tags,version,status,demo_url)
           VALUES ($1,$2,$3,$4,$5,$6,$7,$8,$9,$10,$11,$12,$13,$14)
           RETURNING id`,
          [
            payload.slug, payload.name, payload.shortDescription, payload.description,
            payload.price, payload.categoryId, payload.developerId,
            JSON.stringify(payload.compatibility), JSON.stringify(payload.images),
            JSON.stringify(payload.features), JSON.stringify(payload.tags),
            payload.version, payload.status, demo_url,
          ]
        );
        productId = rows[0].id;
        console.log(`  ✅ Created  (id: ${productId})`);
        created++;
      } else {
        productId = ex.id;
        const changed =
          ex.version !== payload.version ||
          parseFloat(ex.price) !== payload.price ||
          ex.status !== payload.status ||
          ex.name !== payload.name ||
          (ex.demo_url || "") !== demo_url;

        if (changed) {
          await pool.query(
            `UPDATE admin_products SET
               name=$2, short_description=$3, description=$4, price=$5,
               category_id=$6, developer_id=$7, compatibility=$8, images=$9,
               features=$10, tags=$11, version=$12, status=$13, demo_url=$14,
               updated_at=NOW()
             WHERE id=$1`,
            [
              productId, payload.name, payload.shortDescription, payload.description,
              payload.price, payload.categoryId, payload.developerId,
              JSON.stringify(payload.compatibility), JSON.stringify(payload.images),
              JSON.stringify(payload.features), JSON.stringify(payload.tags),
              payload.version, payload.status, demo_url,
            ]
          );
          console.log(`  🔄 Updated  (id: ${productId})`);
          updated++;
        } else {
          console.log(`  ⏭️  Skipped  (no changes)`);
          skipped++;
        }
      }

      // 3. Register build file
      if (buildFile) {
        const absPath = path.resolve(path.dirname(_manifestPath), buildFile);
        try {
          const s = await stat(absPath);
          const checksum = await sha256File(absPath);
          const { rows: files } = await pool.query(
            `SELECT id FROM product_files WHERE product_id=$1 AND checksum_sha256=$2`,
            [productId, checksum]
          );
          if (files.length === 0) {
            const fileName = path.basename(absPath);
            await pool.query(
              `INSERT INTO product_files
                (product_id,storage_provider,url,path,mime_type,size_bytes,checksum_sha256,is_primary)
               VALUES ($1,$2,$3,$4,$5,$6,$7,$8)`,
              [
                productId, "external",
                `https://your-marketplace.vercel.app/downloads/${fileName}`,
                fileName, "application/zip", s.size, checksum, true,
              ]
            );
            console.log(`  📎 Registered: ${fileName} (${(s.size / 1024).toFixed(1)} KB)`);
          } else {
            console.log(`  📎 File already registered`);
          }
        } catch (err) {
          if (err.code === "ENOENT") console.log(`  ⚠️  Build file not found: ${absPath}`);
          else throw err;
        }
      }
    } catch (err) {
      console.error(`  ❌ Error: ${err.message}`);
    }
    console.log();
  }

  await pool.end();
  console.log(`✅ Done: ${created} created, ${updated} updated, ${skipped} skipped\n`);
}

main().catch(err => { console.error("Fatal:", err); process.exit(1); });
