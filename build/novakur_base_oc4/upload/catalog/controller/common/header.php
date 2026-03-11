<?php
namespace Opencart\Catalog\Controller\Common;

class Header extends \Opencart\System\Engine\Controller {
    public function index(): string {
        $this->load->language('common/header');

        $data['title'] = $this->document->getTitle();
        $data['base'] = $this->request->server['HTTPS'] ? HTTPS_SERVER : HTTP_SERVER;
        $data['description'] = $this->document->getDescription();
        $data['keywords'] = $this->document->getKeywords();
        $data['links'] = $this->document->getLinks();
        $data['styles'] = $this->document->getStyles();
        $data['scripts'] = $this->document->getScripts('header');
        $data['lang'] = $this->language->get('code');
        $data['direction'] = $this->language->get('direction');

        $data['name'] = $this->config->get('config_name');
        $data['home'] = $this->url->link('common/home');
        $data['novakur_main_css'] = rtrim($data['base'], '/') . '/catalog/view/assets/dist/css/main.css';

        $data['novakur_primary_color'] = (string)$this->config->get('theme_novakur_base_primary_color') ?: '#2563EB';
        $data['novakur_secondary_color'] = (string)$this->config->get('theme_novakur_base_secondary_color') ?: '#1E3A8A';
        $data['novakur_container_width'] = (int)$this->config->get('theme_novakur_base_container_width') ?: 1200;
        $data['novakur_header_layout'] = (string)$this->config->get('theme_novakur_base_header_layout') ?: 'standard';
        $data['novakur_footer_layout'] = (string)$this->config->get('theme_novakur_base_footer_layout') ?: 'standard';
        $data['novakur_base_font'] = (string)$this->config->get('theme_novakur_base_base_font') ?: 'Segoe UI';
        $data['novakur_heading_font'] = (string)$this->config->get('theme_novakur_base_heading_font') ?: 'Segoe UI';

        $data['novakur_enable_hero_banner'] = (bool)$this->config->get('theme_novakur_base_enable_hero_banner');
        $data['novakur_enable_featured_products'] = (bool)$this->config->get('theme_novakur_base_enable_featured_products');
        $data['novakur_enable_category_grid'] = (bool)$this->config->get('theme_novakur_base_enable_category_grid');
        $data['novakur_enable_benefits_bar'] = (bool)$this->config->get('theme_novakur_base_enable_benefits_bar');

        // Starter nav structure for theme header component.
        $data['menu_items'] = [];

        return $this->load->view('common/header', $data);
    }
}
