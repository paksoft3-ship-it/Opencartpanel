<?php
namespace Opencart\Admin\Controller\Extension\NovakurMinimal\Theme;

class NovakurMinimal extends \Opencart\System\Engine\Controller {
    private array $error = [];

    public function index(): void {
        $this->load->language('extension/novakur_minimal/theme/novakur_minimal');

        $this->document->setTitle($this->language->get('heading_title'));

        $store_id = isset($this->request->get['store_id']) ? (int)$this->request->get['store_id'] : 0;

        $language_keys = [
            'heading_title', 'text_edit', 'tab_branding', 'tab_colors', 'tab_layout',
            'tab_homepage', 'tab_typography', 'entry_logo', 'entry_favicon',
            'entry_accent_color', 'entry_container_width', 'entry_base_font',
            'entry_enable_hero_banner', 'entry_enable_featured_products',
            'entry_enable_category_grid', 'entry_enable_benefits_bar',
            'help_container_width', 'text_layout_standard', 'text_layout_minimal'
        ];

        foreach ($language_keys as $key) {
            $data[$key] = $this->language->get($key);
        }

        $data['breadcrumbs'] = [
            [
                'text' => $this->language->get('text_home'),
                'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'])
            ],
            [
                'text' => $this->language->get('text_extension'),
                'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=theme')
            ],
            [
                'text' => $this->language->get('heading_title'),
                'href' => $this->url->link('extension/novakur_minimal/theme/novakur_minimal', 'user_token=' . $this->session->data['user_token'] . '&store_id=' . $store_id)
            ]
        ];

        $data['save'] = $this->url->link('extension/novakur_minimal/theme/novakur_minimal.save', 'user_token=' . $this->session->data['user_token'] . '&store_id=' . $store_id);
        $data['back'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=theme');

        $this->load->model('setting/setting');
        $setting_info = $this->model_setting_setting->getSetting('theme_novakur_minimal', $store_id);

        $this->load->model('tool/image');

        $defaults = [
            'theme_novakur_minimal_logo'                     => '',
            'theme_novakur_minimal_favicon'                  => '',
            'theme_novakur_minimal_accent_color'             => '#111111',
            'theme_novakur_minimal_container_width'          => '1200',
            'theme_novakur_minimal_base_font'                => 'Inter',
            'theme_novakur_minimal_enable_hero_banner'       => '1',
            'theme_novakur_minimal_enable_featured_products' => '1',
            'theme_novakur_minimal_enable_category_grid'     => '1',
            'theme_novakur_minimal_enable_benefits_bar'      => '0',
            'theme_novakur_minimal_status'                   => '0'
        ];

        foreach ($defaults as $key => $default) {
            if (isset($this->request->post[$key])) {
                $data[$key] = $this->request->post[$key];
            } elseif (isset($setting_info[$key])) {
                $data[$key] = $setting_info[$key];
            } else {
                $data[$key] = $default;
            }
        }

        foreach (['theme_novakur_minimal_logo', 'theme_novakur_minimal_favicon'] as $img_key) {
            $thumb_key = 'thumb_' . str_replace('theme_novakur_minimal_', '', $img_key);
            if ($data[$img_key] && is_file(DIR_IMAGE . html_entity_decode($data[$img_key], ENT_QUOTES, 'UTF-8'))) {
                $data[$thumb_key] = $this->model_tool_image->resize($data[$img_key], 100, 100);
            } else {
                $data[$thumb_key] = $this->model_tool_image->resize('no_image.png', 100, 100);
            }
        }

        $data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);

        $data['font_options'] = [
            ['value' => 'Inter',      'text' => 'Inter'],
            ['value' => 'DM Sans',    'text' => 'DM Sans'],
            ['value' => 'Lato',       'text' => 'Lato'],
            ['value' => 'Roboto',     'text' => 'Roboto'],
            ['value' => 'Open Sans',  'text' => 'Open Sans'],
        ];

        $data['error_warning'] = $this->error['warning'] ?? '';

        $data['header']      = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer']      = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/novakur_minimal/theme/novakur_minimal', $data));
    }

    public function save(): void {
        $this->load->language('extension/novakur_minimal/theme/novakur_minimal');

        $json = [];
        $store_id = isset($this->request->get['store_id']) ? (int)$this->request->get['store_id'] : 0;

        if (!$this->user->hasPermission('modify', 'extension/novakur_minimal/theme/novakur_minimal')) {
            $json['error']['warning'] = $this->language->get('error_permission');
        }

        $accent = $this->request->post['theme_novakur_minimal_accent_color'] ?? '';
        if (!preg_match('/^#(?:[0-9a-fA-F]{3}){1,2}$/', trim($accent))) {
            $json['error']['theme_novakur_minimal_accent_color'] = 'Please enter a valid hex color.';
        }

        if (!isset($json['error'])) {
            $this->load->model('setting/setting');
            $this->model_setting_setting->editSetting('theme_novakur_minimal', $this->request->post, $store_id);
            $json['success'] = $this->language->get('text_success');
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function install(): void {
        $this->load->model('setting/event');

        $this->model_setting_event->addEvent([
            'code'        => 'novakur_minimal',
            'description' => 'NovaKur Minimal theme template override',
            'trigger'     => 'catalog/view/*/before',
            'action'      => 'extension/novakur_minimal/event/novakur_minimal.view',
            'status'      => 1,
            'sort_order'  => 0
        ]);
    }

    public function uninstall(): void {
        $this->load->model('setting/event');
        $this->model_setting_event->deleteEventByCode('novakur_minimal');

        $this->load->model('setting/setting');
        $this->model_setting_setting->deleteSetting('theme_novakur_minimal');
    }
}
