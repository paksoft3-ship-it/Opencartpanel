<?php
namespace Opencart\Catalog\Controller\Extension\Novakur\Event;

class Novakur extends \Opencart\System\Engine\Controller {

    public function view(string &$route, array &$data, string &$code, string &$output): void {
        if ($this->config->get('config_theme') !== 'novakur_base') {
            return;
        }

        $template_route = $route;

        if (str_starts_with($template_route, 'extension/novakur/')) {
            $template_route = substr($template_route, strlen('extension/novakur/'));
        }

        $template_file = DIR_EXTENSION . 'novakur/catalog/view/template/' . $template_route . '.twig';

        if (!is_file($template_file)) {
            return;
        }

        if ($template_route === 'common/header') {
            $data['novakur_primary_color']            = (string)$this->config->get('theme_novakur_base_primary_color') ?: '#2463eb';
            $data['novakur_secondary_color']          = (string)$this->config->get('theme_novakur_base_secondary_color') ?: '#1E3A8A';
            $data['novakur_container_width']          = (int)$this->config->get('theme_novakur_base_container_width') ?: 1200;
            $data['novakur_header_layout']            = (string)$this->config->get('theme_novakur_base_header_layout') ?: 'standard';
            $data['novakur_footer_layout']            = (string)$this->config->get('theme_novakur_base_footer_layout') ?: 'standard';
            $data['novakur_base_font']                = (string)$this->config->get('theme_novakur_base_base_font') ?: 'Inter';
            $data['novakur_heading_font']             = (string)$this->config->get('theme_novakur_base_heading_font') ?: 'Inter';
            $data['novakur_enable_hero_banner']       = (bool)$this->config->get('theme_novakur_base_enable_hero_banner');
            $data['novakur_enable_featured_products'] = (bool)$this->config->get('theme_novakur_base_enable_featured_products');
            $data['novakur_enable_category_grid']     = (bool)$this->config->get('theme_novakur_base_enable_category_grid');
            $data['novakur_enable_benefits_bar']      = (bool)$this->config->get('theme_novakur_base_enable_benefits_bar');

            $data['cart_count'] = $this->cart->countProducts();

            if (!isset($data['menu_items'])) {
                $data['menu_items'] = [];
            }
        }

        if ($template_route === 'checkout/checkout') {
            $this->load->model('tool/image');
            $this->load->model('checkout/cart');

            $lang     = $this->config->get('config_language') ?: 'en-gb';
            $currency = $this->session->data['currency'] ?? $this->config->get('config_currency');

            $data['products'] = [];
            foreach ($this->model_checkout_cart->getProducts() as $product) {
                $image = !empty($product['image']) ? $product['image'] : 'placeholder.png';
                $data['products'][] = [
                    'name'     => $product['name'],
                    'thumb'    => $this->model_tool_image->resize($image, 80, 80),
                    'quantity' => (int)$product['quantity'],
                    'price'    => $this->currency->format($this->tax->calculate((float)$product['price'], (int)$product['tax_class_id'], (bool)$this->config->get('config_tax')), $currency),
                    'total'    => $this->currency->format($this->tax->calculate((float)$product['price'], (int)$product['tax_class_id'], (bool)$this->config->get('config_tax')) * (int)$product['quantity'], $currency),
                ];
            }

            $subtotal = $this->cart->getSubTotal();
            $total_value = $this->cart->getTotal();
            $data['totals'] = [];
            if ($subtotal) $data['totals'][] = ['title' => 'Sub-Total', 'text' => $this->currency->format($subtotal, $currency)];
            if ($total_value) $data['totals'][] = ['title' => 'Total', 'text' => $this->currency->format($total_value, $currency)];

            $data['language'] = $lang;
        }

        if ($template_route === 'account/wishlist') {
            $lang     = $this->config->get('config_language') ?: 'en-gb';
            $currency = $this->session->data['currency'] ?? $this->config->get('config_currency');
            $token    = $this->session->data['customer_token'] ?? '';

            $this->load->model('account/wishlist');
            $this->load->model('catalog/product');
            $this->load->model('tool/image');

            $data['products']      = [];
            $data['language']      = $lang;
            $data['customer_token'] = $token;
            $data['home']          = $this->url->link('common/home', 'language=' . $lang);

            $results = $this->model_account_wishlist->getWishlist($this->customer->getId());

            foreach ($results as $result) {
                $product_info = $this->model_catalog_product->getProduct($result['product_id']);
                if (!$product_info) { continue; }

                $image = !empty($product_info['image']) ? $product_info['image'] : 'placeholder.png';
                $price = $this->currency->format(
                    $this->tax->calculate((float)$product_info['price'], (int)$product_info['tax_class_id'], (bool)$this->config->get('config_tax')),
                    $currency
                );
                $special = (float)$product_info['special'] ? $this->currency->format(
                    $this->tax->calculate((float)$product_info['special'], (int)$product_info['tax_class_id'], (bool)$this->config->get('config_tax')),
                    $currency
                ) : false;

                $data['products'][] = [
                    'product_id' => (int)$product_info['product_id'],
                    'name'       => $product_info['name'],
                    'thumb'      => $this->model_tool_image->resize($image, 300, 300),
                    'price'      => $price,
                    'special'    => $special,
                    'stock'      => $product_info['quantity'] > 0 ? 'In Stock' : 'Out of Stock',
                    'href'       => $this->url->link('product/product', 'language=' . $lang . '&product_id=' . (int)$product_info['product_id']),
                ];
            }
        }

        if ($template_route === 'checkout/cart') {
            $this->load->model('tool/image');
            $this->load->model('checkout/cart');

            $lang     = $this->config->get('config_language') ?: 'en-gb';
            $currency = $this->session->data['currency'] ?? $this->config->get('config_currency');

            // Build Tailwind-ready products array
            $data['products'] = [];

            foreach ($this->model_checkout_cart->getProducts() as $product) {
                $price = $this->currency->format(
                    $this->tax->calculate((float)$product['price'], (int)$product['tax_class_id'], (bool)$this->config->get('config_tax')),
                    $currency
                );
                $total = $this->currency->format(
                    $this->tax->calculate((float)$product['price'], (int)$product['tax_class_id'], (bool)$this->config->get('config_tax')) * (int)$product['quantity'],
                    $currency
                );

                $image = !empty($product['image']) ? $product['image'] : 'placeholder.png';

                $data['products'][] = [
                    'cart_id'  => $product['cart_id'],
                    'product_id' => $product['product_id'],
                    'name'     => $product['name'],
                    'thumb'    => $this->model_tool_image->resize($image, 120, 120),
                    'price'    => $price,
                    'total'    => $total,
                    'quantity' => (int)$product['quantity'],
                    'option'   => $product['option'] ?? [],
                    'href'     => $this->url->link('product/product', 'language=' . $lang . '&product_id=' . (int)$product['product_id']),
                    'remove'   => $this->url->link('checkout/cart.remove', 'language=' . $lang . '&key=' . $product['cart_id']),
                ];
            }

            // Simple subtotal + total from cart library
            $subtotal = $this->cart->getSubTotal();
            $total_value = $this->cart->getTotal();
            $data['totals'] = [];
            if ($subtotal) {
                $data['totals'][] = ['title' => 'Sub-Total', 'text' => $this->currency->format($subtotal, $currency)];
            }
            if ($total_value) {
                $data['totals'][] = ['title' => 'Total', 'text' => $this->currency->format($total_value, $currency)];
            }

            $data['checkout'] = $this->url->link('checkout/checkout', 'language=' . $lang);
            $data['continue'] = $this->url->link('common/home', 'language=' . $lang);
        }

        if ($template_route === 'common/home') {
            $products = $this->getNovakurHomepageProducts();

            if ($products) {
                $data['products'] = $products;
            }

            // Inject home URL and categories so homepage sections render correct links
            $lang = $this->config->get('config_language') ?: 'en-gb';
            $data['home'] = $this->url->link('common/home', 'language=' . $lang);

            if (!isset($data['categories']) || !$data['categories']) {
                $this->load->model('catalog/category');
                $data['categories'] = [];
                foreach ($this->model_catalog_category->getCategories(0) as $cat) {
                    $data['categories'][] = [
                        'name' => $cat['name'],
                        'href' => $this->url->link('product/category', 'language=' . $lang . '&path=' . $cat['category_id'])
                    ];
                }
            }
        }

        if (!str_starts_with($route, 'extension/novakur/')) {
            $route = 'extension/novakur/' . $route;
        }
    }

    private function getNovakurHomepageProducts(): array {
        $this->load->model('tool/image');

        $language_id = (int)$this->config->get('config_language_id');

        if (!$language_id) {
            $language_code = (string)$this->config->get('config_language') ?: 'en-gb';
            $language_query = $this->db->query("SELECT language_id FROM `" . DB_PREFIX . "language` WHERE code = '" . $this->db->escape($language_code) . "' AND status = '1' LIMIT 1");
            $language_id = $language_query->num_rows ? (int)$language_query->row['language_id'] : 1;
        }

        $store_id = (int)$this->config->get('config_store_id');
        if ($store_id < 0) {
            $store_id = 0;
        }

        $lang = $this->config->get('config_language') ?: 'en-gb';
        $currency = $this->session->data['currency'] ?? $this->config->get('config_currency');

        // Pull the latest 16 active products from the real store DB
        $sql = "SELECT p.product_id, p.image, p.price, p.tax_class_id, pd.name, pd.description,
                    (SELECT pd2.price FROM `" . DB_PREFIX . "product_discount` pd2
                     WHERE pd2.product_id = p.product_id AND pd2.special = 1
                       AND (pd2.customer_group_id = '1' OR pd2.customer_group_id = '0')
                       AND (pd2.date_start IS NULL OR pd2.date_start <= NOW())
                       AND (pd2.date_end IS NULL OR pd2.date_end >= NOW())
                     ORDER BY pd2.priority ASC, pd2.price ASC LIMIT 1) AS special
                FROM `" . DB_PREFIX . "product` p
                LEFT JOIN `" . DB_PREFIX . "product_description` pd
                    ON (p.product_id = pd.product_id AND pd.language_id = '" . (int)$language_id . "')
                LEFT JOIN `" . DB_PREFIX . "product_to_store` p2s
                    ON (p.product_id = p2s.product_id)
                WHERE p.status = '1'
                  AND p.date_available <= NOW()
                  AND p2s.store_id = '" . (int)$store_id . "'
                ORDER BY p.date_added DESC
                LIMIT 16";

        $query = $this->db->query($sql);

        if (!$query->num_rows) {
            return [];
        }

        $products = [];

        foreach ($query->rows as $row) {
            $image = !empty($row['image']) ? $row['image'] : 'placeholder.png';

            $price = $this->currency->format(
                $this->tax->calculate((float)$row['price'], (int)$row['tax_class_id'], (bool)$this->config->get('config_tax')),
                $currency
            );

            $special = false;
            if (!empty($row['special'])) {
                $special = $this->currency->format(
                    $this->tax->calculate((float)$row['special'], (int)$row['tax_class_id'], (bool)$this->config->get('config_tax')),
                    $currency
                );
            }

            $products[] = [
                'product_id'  => (int)$row['product_id'],
                'thumb'       => $this->model_tool_image->resize($image, 600, 600),
                'name'        => $row['name'],
                'description' => oc_substr(strip_tags(html_entity_decode((string)$row['description'], ENT_QUOTES, 'UTF-8')), 0, 100) . '..',
                'price'       => $price,
                'special'     => $special,
                'href'        => $this->url->link('product/product', 'language=' . $lang . '&product_id=' . (int)$row['product_id']),
            ];
        }

        return $products;
    }
}
