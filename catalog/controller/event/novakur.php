<?php
namespace Opencart\Catalog\Controller\Extension\Novakur\Event;

/**
 * NovaKur Theme Event Controller
 *
 * Intercepts view/*/before to redirect template rendering to extension
 * templates when the NovaKur theme is active (config_theme = novakur_base).
 * Also injects theme settings data for the header template.
 */
class Novakur extends \Opencart\System\Engine\Controller {

    /**
     * View before event — redirects OC core template routes to NovaKur
     * extension templates and injects theme config data.
     *
     * @param string $route   Template route being requested (passed by ref)
     * @param array  $data    Data array passed to the template (passed by ref)
     * @param string $code    OCmod code override (passed by ref)
     * @param string $output  Pre-rendered output (passed by ref, bypass if set)
     */
    public function view(string &$route, array &$data, string &$code, string &$output): void {
        // Only intercept when novakur_base is the active store theme
        if ($this->config->get('config_theme') !== 'novakur_base') {
            return;
        }

        // Check if NovaKur has an override template for this route
        $template_file = DIR_EXTENSION . 'novakur/catalog/view/template/' . $route . '.twig';

        if (!is_file($template_file)) {
            return;
        }

        // Inject theme settings into header template data
        if ($route === 'common/header') {
            $base_url = ($this->request->server['HTTPS'] ? 'https://' : 'http://') . $this->request->server['HTTP_HOST'];

            // Assets are served from inside the extension directory
            $data['novakur_main_css'] = HTTP_SERVER . 'extension/novakur/catalog/view/assets/dist/css/main.css';

            $data['novakur_primary_color']            = (string)$this->config->get('theme_novakur_base_primary_color') ?: '#2563EB';
            $data['novakur_secondary_color']          = (string)$this->config->get('theme_novakur_base_secondary_color') ?: '#1E3A8A';
            $data['novakur_container_width']          = (int)$this->config->get('theme_novakur_base_container_width') ?: 1200;
            $data['novakur_header_layout']            = (string)$this->config->get('theme_novakur_base_header_layout') ?: 'standard';
            $data['novakur_footer_layout']            = (string)$this->config->get('theme_novakur_base_footer_layout') ?: 'standard';
            $data['novakur_base_font']                = (string)$this->config->get('theme_novakur_base_base_font') ?: 'Segoe UI';
            $data['novakur_heading_font']             = (string)$this->config->get('theme_novakur_base_heading_font') ?: 'Segoe UI';
            $data['novakur_enable_hero_banner']       = (bool)$this->config->get('theme_novakur_base_enable_hero_banner');
            $data['novakur_enable_featured_products'] = (bool)$this->config->get('theme_novakur_base_enable_featured_products');
            $data['novakur_enable_category_grid']     = (bool)$this->config->get('theme_novakur_base_enable_category_grid');
            $data['novakur_enable_benefits_bar']      = (bool)$this->config->get('theme_novakur_base_enable_benefits_bar');

            // Minimal menu items (extend here or via a catalog model as needed)
            if (!isset($data['menu_items'])) {
                $data['menu_items'] = [];
            }
        }

        // Redirect the route to the extension namespace so the twig loader
        // finds the file in extension/novakur/catalog/view/template/
        $route = 'extension/novakur/' . $route;
    }
}
