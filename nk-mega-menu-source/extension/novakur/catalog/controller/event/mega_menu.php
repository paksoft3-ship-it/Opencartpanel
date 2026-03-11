<?php
namespace Opencart\Catalog\Controller\Extension\Novakur\Event;

class MegaMenu extends \Opencart\System\Engine\Controller {
    public function view(string &$route, array &$args, mixed &$output): void {
        if (!$this->config->get('module_mega_menu_status')) {
            return;
        }

        $this->load->model('catalog/category');
        $this->load->model('catalog/product');
        $this->load->model('tool/image');

        $depth = (int)$this->config->get('module_mega_menu_depth');
        $show_thumbnails = $this->config->get('module_mega_menu_show_product_thumbnails');

        $args['novakur_mega_menu_categories'] = [];
        $categories = $this->model_catalog_category->getCategories(0);

        foreach ($categories as $category) {
            if ($category['top']) {
                $children_data = [];
                $children = $this->model_catalog_category->getCategories($category['category_id']);

                foreach ($children as $child) {
                    $sub_children_data = [];
                    if ($depth >= 3) {
                        $sub_children = $this->model_catalog_category->getCategories($child['category_id']);
                        foreach ($sub_children as $sub) {
                            $sub_children_data[] = [
                                'name' => $sub['name'],
                                'href' => $this->url->link('product/category', 'language=' . $this->config->get('config_language') . '&path=' . $category['category_id'] . '_' . $child['category_id'] . '_' . $sub['category_id'])
                            ];
                        }
                    }

                    $children_data[] = [
                        'name'     => $child['name'],
                        'children' => $sub_children_data,
                        'href'     => $this->url->link('product/category', 'language=' . $this->config->get('config_language') . '&path=' . $category['category_id'] . '_' . $child['category_id'])
                    ];
                }

                $products_data = [];
                if ($show_thumbnails) {
                    $filter_data = [
                        'filter_category_id' => $category['category_id'],
                        'sort'               => 'p.date_added',
                        'order'              => 'DESC',
                        'start'              => 0,
                        'limit'              => 6
                    ];
                    $products = $this->model_catalog_product->getProducts($filter_data);
                    foreach ($products as $product) {
                        $image = '';
                        if ($product['image'] && is_file(DIR_IMAGE . html_entity_decode($product['image'], ENT_QUOTES, 'UTF-8'))) {
                            $image = $this->model_tool_image->resize(html_entity_decode($product['image'], ENT_QUOTES, 'UTF-8'), $this->config->get('config_image_product_width'), $this->config->get('config_image_product_height'));
                        } else {
                            $image = rtrim($this->config->get('config_url'), '/') . '/image/placeholder.png';
                        }
                        $products_data[] = [
                            'name'  => $product['name'],
                            'thumb' => $image,
                            'href'  => $this->url->link('product/product', 'language=' . $this->config->get('config_language') . '&path=' . $category['category_id'] . '&product_id=' . $product['product_id'])
                        ];
                    }
                }

                $args['novakur_mega_menu_categories'][] = [
                    'name'     => $category['name'],
                    'children' => $children_data,
                    'products' => $products_data,
                    'href'     => $this->url->link('product/category', 'language=' . $this->config->get('config_language') . '&path=' . $category['category_id'])
                ];
            }
        }

        $args['show_promo_column'] = $this->config->get('module_mega_menu_show_promo_column');
        if ($this->config->get('module_mega_menu_promo_image') && is_file(DIR_IMAGE . $this->config->get('module_mega_menu_promo_image'))) {
            $args['promo_image'] = $this->model_tool_image->resize($this->config->get('module_mega_menu_promo_image'), 300, 400); 
        } else {
            $args['promo_image'] = '';
        }
        $args['promo_link'] = $this->config->get('module_mega_menu_promo_link');

        $args['columns'] = $this->config->get('module_mega_menu_columns');

        $args['nk_mega_menu_css'] = rtrim($this->config->get('config_url'), '/') . '/extension/novakur/catalog/view/assets/css/mega_menu.css';
        $args['nk_mega_menu_js'] = rtrim($this->config->get('config_url'), '/') . '/extension/novakur/catalog/view/assets/js/mega_menu.js';
        
        $args['bg_color'] = $this->config->get('module_mega_menu_background_color') ?: '#FFFFFF';
        $args['hover_color'] = $this->config->get('module_mega_menu_hover_color') ?: '#2563EB';

        // Override route to output our custom mega menu layout instead of default
        $route = 'extension/novakur/component/layout/mega_menu';
    }
}
