<?php
namespace Opencart\Catalog\Controller\Extension\NovakurMinimal\Event;

class NovakurMinimal extends \Opencart\System\Engine\Controller {

    public function view(string &$route, array &$data, string &$code, string &$output): void {
        if ($this->config->get('config_theme') !== 'novakur_minimal') {
            return;
        }

        $template_route = $route;

        if (str_starts_with($template_route, 'extension/novakur_minimal/')) {
            $template_route = substr($template_route, strlen('extension/novakur_minimal/'));
        }

        $template_file = DIR_EXTENSION . 'novakur_minimal/catalog/view/template/' . $template_route . '.twig';

        if (!is_file($template_file)) {
            return;
        }

        if ($template_route === 'common/header') {
            $data['nm_accent_color']    = (string)$this->config->get('theme_novakur_minimal_accent_color') ?: '#111111';
            $data['nm_container_width'] = (int)$this->config->get('theme_novakur_minimal_container_width') ?: 1200;
            $data['nm_base_font']       = (string)$this->config->get('theme_novakur_minimal_base_font') ?: 'Inter';

            $data['nm_enable_hero_banner']       = (bool)$this->config->get('theme_novakur_minimal_enable_hero_banner');
            $data['nm_enable_featured_products'] = (bool)$this->config->get('theme_novakur_minimal_enable_featured_products');
            $data['nm_enable_category_grid']     = (bool)$this->config->get('theme_novakur_minimal_enable_category_grid');
            $data['nm_enable_benefits_bar']      = (bool)$this->config->get('theme_novakur_minimal_enable_benefits_bar');

            $data['cart_count'] = $this->cart->countProducts();

            if (!isset($data['menu_items'])) {
                $data['menu_items'] = [];
            }
        }

        if ($template_route === 'common/home') {
            $products = $this->getHomepageProducts();
            if ($products) {
                $data['products'] = $products;
            }

            $lang = $this->config->get('config_language') ?: 'en-gb';
            $data['home'] = $this->url->link('common/home', 'language=' . $lang);

            if (empty($data['categories'])) {
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

        if ($template_route === 'checkout/cart') {
            $this->load->model('tool/image');
            $this->load->model('checkout/cart');

            $lang     = $this->config->get('config_language') ?: 'en-gb';
            $currency = $this->session->data['currency'] ?? $this->config->get('config_currency');
            $data['products'] = [];

            foreach ($this->model_checkout_cart->getProducts() as $product) {
                $price = $this->currency->format(
                    $this->tax->calculate((float)$product['price'], (int)$product['tax_class_id'], (bool)$this->config->get('config_tax')),
                    $currency
                );
                $image = !empty($product['image']) ? $product['image'] : 'placeholder.png';

                $data['products'][] = [
                    'cart_id'    => $product['cart_id'],
                    'product_id' => $product['product_id'],
                    'name'       => $product['name'],
                    'thumb'      => $this->model_tool_image->resize($image, 120, 120),
                    'price'      => $price,
                    'quantity'   => (int)$product['quantity'],
                    'option'     => $product['option'] ?? [],
                    'href'       => $this->url->link('product/product', 'language=' . $lang . '&product_id=' . (int)$product['product_id']),
                    'remove'     => $this->url->link('checkout/cart.remove', 'language=' . $lang . '&key=' . $product['cart_id']),
                ];
            }

            $subtotal    = $this->cart->getSubTotal();
            $total_value = $this->cart->getTotal();
            $data['totals'] = [];
            if ($subtotal)    $data['totals'][] = ['title' => 'Sub-Total', 'text' => $this->currency->format($subtotal, $currency)];
            if ($total_value) $data['totals'][] = ['title' => 'Total',     'text' => $this->currency->format($total_value, $currency)];

            $data['checkout'] = $this->url->link('checkout/checkout', 'language=' . $lang);
            $data['continue'] = $this->url->link('common/home', 'language=' . $lang);
        }

        if ($template_route === 'account/wishlist') {
            $lang     = $this->config->get('config_language') ?: 'en-gb';
            $currency = $this->session->data['currency'] ?? $this->config->get('config_currency');

            $this->load->model('account/wishlist');
            $this->load->model('catalog/product');
            $this->load->model('tool/image');

            $data['products']       = [];
            $data['language']       = $lang;
            $data['customer_token'] = $this->session->data['customer_token'] ?? '';
            $data['home']           = $this->url->link('common/home', 'language=' . $lang);

            foreach ($this->model_account_wishlist->getWishlist($this->customer->getId()) as $result) {
                $product_info = $this->model_catalog_product->getProduct($result['product_id']);
                if (!$product_info) continue;

                $image   = !empty($product_info['image']) ? $product_info['image'] : 'placeholder.png';
                $price   = $this->currency->format($this->tax->calculate((float)$product_info['price'], (int)$product_info['tax_class_id'], (bool)$this->config->get('config_tax')), $currency);
                $special = !empty($product_info['special']) ? $this->currency->format($this->tax->calculate((float)$product_info['special'], (int)$product_info['tax_class_id'], (bool)$this->config->get('config_tax')), $currency) : false;

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
                ];
            }

            $subtotal    = $this->cart->getSubTotal();
            $total_value = $this->cart->getTotal();
            $data['totals'] = [];
            if ($subtotal)    $data['totals'][] = ['title' => 'Sub-Total', 'text' => $this->currency->format($subtotal, $currency)];
            if ($total_value) $data['totals'][] = ['title' => 'Total',     'text' => $this->currency->format($total_value, $currency)];

            $data['language'] = $lang;
        }

        if (!str_starts_with($route, 'extension/novakur_minimal/')) {
            $route = 'extension/novakur_minimal/' . $route;
        }
    }

    private function getHomepageProducts(): array {
        $this->load->model('tool/image');

        $language_id = (int)$this->config->get('config_language_id');
        if (!$language_id) {
            $q = $this->db->query("SELECT language_id FROM `" . DB_PREFIX . "language` WHERE code = '" . $this->db->escape($this->config->get('config_language') ?: 'en-gb') . "' AND status = '1' LIMIT 1");
            $language_id = $q->num_rows ? (int)$q->row['language_id'] : 1;
        }

        $store_id = max(0, (int)$this->config->get('config_store_id'));
        $lang     = $this->config->get('config_language') ?: 'en-gb';
        $currency = $this->session->data['currency'] ?? $this->config->get('config_currency');

        $sql = "SELECT p.product_id, p.image, p.price, p.tax_class_id, pd.name,
                    (SELECT ps.price FROM `" . DB_PREFIX . "product_special` ps
                     WHERE ps.product_id = p.product_id
                       AND (ps.customer_group_id = '1' OR ps.customer_group_id = '0')
                       AND (ps.date_start = '0000-00-00' OR ps.date_start <= NOW())
                       AND (ps.date_end = '0000-00-00' OR ps.date_end >= NOW())
                     ORDER BY ps.priority ASC, ps.price ASC LIMIT 1) AS special
                FROM `" . DB_PREFIX . "product` p
                LEFT JOIN `" . DB_PREFIX . "product_description` pd ON (p.product_id = pd.product_id AND pd.language_id = '" . (int)$language_id . "')
                LEFT JOIN `" . DB_PREFIX . "product_to_store` p2s ON (p.product_id = p2s.product_id)
                WHERE p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$store_id . "'
                ORDER BY p.date_added DESC LIMIT 16";

        $query = $this->db->query($sql);
        if (!$query->num_rows) return [];

        $products = [];
        foreach ($query->rows as $row) {
            $image   = !empty($row['image']) ? $row['image'] : 'placeholder.png';
            $price   = $this->currency->format($this->tax->calculate((float)$row['price'], (int)$row['tax_class_id'], (bool)$this->config->get('config_tax')), $currency);
            $special = !empty($row['special']) ? $this->currency->format($this->tax->calculate((float)$row['special'], (int)$row['tax_class_id'], (bool)$this->config->get('config_tax')), $currency) : false;

            $products[] = [
                'product_id' => (int)$row['product_id'],
                'thumb'      => $this->model_tool_image->resize($image, 600, 600),
                'name'       => $row['name'],
                'price'      => $price,
                'special'    => $special,
                'href'       => $this->url->link('product/product', 'language=' . $lang . '&product_id=' . (int)$row['product_id']),
            ];
        }

        return $products;
    }
}
