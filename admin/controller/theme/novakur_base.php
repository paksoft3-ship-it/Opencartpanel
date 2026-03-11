<?php
namespace Opencart\Admin\Controller\Extension\Novakur\Theme;

class NovakurBase extends \Opencart\System\Engine\Controller {
    private array $error = [];

    public function index(): void {
        $this->load->language('extension/novakur/theme/novakur_base');

        $this->document->setTitle($this->language->get('heading_title'));

        if (isset($this->request->get['store_id'])) {
            $store_id = (int)$this->request->get['store_id'];
        } else {
            $store_id = 0;
        }

        $language_keys = [
            'heading_title',
            'text_edit',
            'tab_branding',
            'tab_colors',
            'tab_layout',
            'tab_homepage',
            'tab_typography',
            'entry_logo',
            'entry_favicon',
            'entry_primary_color',
            'entry_secondary_color',
            'entry_container_width',
            'entry_header_layout',
            'entry_footer_layout',
            'entry_enable_hero_banner',
            'entry_enable_featured_products',
            'entry_enable_category_grid',
            'entry_enable_benefits_bar',
            'entry_base_font',
            'entry_heading_font',
            'help_container_width',
            'help_color',
            'text_layout_standard',
            'text_layout_centered',
            'text_layout_split',
            'text_layout_minimal',
            'text_layout_columns'
        ];

        foreach ($language_keys as $language_key) {
            $data[$language_key] = $this->language->get($language_key);
        }

        $data['breadcrumbs'] = [];

        $data['breadcrumbs'][] = [
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'])
        ];

        $data['breadcrumbs'][] = [
            'text' => $this->language->get('text_extension'),
            'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=theme')
        ];

        $data['breadcrumbs'][] = [
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/novakur/theme/novakur_base', 'user_token=' . $this->session->data['user_token'] . '&store_id=' . $store_id)
        ];

        $data['save'] = $this->url->link('extension/novakur/theme/novakur_base.save', 'user_token=' . $this->session->data['user_token'] . '&store_id=' . $store_id);
        $data['back'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=theme');

        $this->load->model('setting/setting');
        $setting_info = $this->model_setting_setting->getSetting('theme_novakur_base', $store_id);

        $this->load->model('tool/image');

        $settings = [
            'theme_novakur_base_logo'                    => '',
            'theme_novakur_base_favicon'                 => '',
            'theme_novakur_base_primary_color'           => '#2563EB',
            'theme_novakur_base_secondary_color'         => '#1E3A8A',
            'theme_novakur_base_container_width'         => '1200',
            'theme_novakur_base_header_layout'           => 'standard',
            'theme_novakur_base_footer_layout'           => 'standard',
            'theme_novakur_base_enable_hero_banner'      => '1',
            'theme_novakur_base_enable_featured_products'=> '1',
            'theme_novakur_base_enable_category_grid'    => '1',
            'theme_novakur_base_enable_benefits_bar'     => '1',
            'theme_novakur_base_base_font'               => 'Segoe UI',
            'theme_novakur_base_heading_font'            => 'Segoe UI',
            'theme_novakur_base_status'                  => '0'
        ];

        foreach ($settings as $key => $default) {
            if (isset($this->request->post[$key])) {
                $data[$key] = $this->request->post[$key];
            } elseif (isset($setting_info[$key])) {
                $data[$key] = $setting_info[$key];
            } else {
                $data[$key] = $default;
            }
        }

        if ($data['theme_novakur_base_logo'] && is_file(DIR_IMAGE . html_entity_decode($data['theme_novakur_base_logo'], ENT_QUOTES, 'UTF-8'))) {
            $data['thumb_logo'] = $this->model_tool_image->resize($data['theme_novakur_base_logo'], 100, 100);
        } else {
            $data['thumb_logo'] = $this->model_tool_image->resize('no_image.png', 100, 100);
        }

        if ($data['theme_novakur_base_favicon'] && is_file(DIR_IMAGE . html_entity_decode($data['theme_novakur_base_favicon'], ENT_QUOTES, 'UTF-8'))) {
            $data['thumb_favicon'] = $this->model_tool_image->resize($data['theme_novakur_base_favicon'], 100, 100);
        } else {
            $data['thumb_favicon'] = $this->model_tool_image->resize('no_image.png', 100, 100);
        }

        $data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);

        $data['header_layouts'] = [
            ['value' => 'standard', 'text' => $this->language->get('text_layout_standard')],
            ['value' => 'centered', 'text' => $this->language->get('text_layout_centered')],
            ['value' => 'split',    'text' => $this->language->get('text_layout_split')]
        ];

        $data['footer_layouts'] = [
            ['value' => 'standard', 'text' => $this->language->get('text_layout_standard')],
            ['value' => 'minimal',  'text' => $this->language->get('text_layout_minimal')],
            ['value' => 'columns',  'text' => $this->language->get('text_layout_columns')]
        ];

        $data['font_options'] = [
            ['value' => 'Segoe UI',  'text' => 'Segoe UI'],
            ['value' => 'Roboto',    'text' => 'Roboto'],
            ['value' => 'Open Sans', 'text' => 'Open Sans'],
            ['value' => 'Poppins',   'text' => 'Poppins'],
            ['value' => 'Lato',      'text' => 'Lato']
        ];

        $data['error_warning'] = $this->error['warning'] ?? '';

        $data['header']      = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer']      = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/novakur/theme/novakur_base', $data));
    }

    public function save(): void {
        $this->load->language('extension/novakur/theme/novakur_base');

        $json = [];

        if (isset($this->request->get['store_id'])) {
            $store_id = (int)$this->request->get['store_id'];
        } else {
            $store_id = 0;
        }

        if (!$this->user->hasPermission('modify', 'extension/novakur/theme/novakur_base')) {
            $json['error']['warning'] = $this->language->get('error_permission');
        }

        $primary_color   = $this->request->post['theme_novakur_base_primary_color'] ?? '';
        $secondary_color = $this->request->post['theme_novakur_base_secondary_color'] ?? '';

        if (!$this->isValidHexColor($primary_color)) {
            $json['error']['theme_novakur_base_primary_color'] = $this->language->get('error_primary_color');
        }

        if (!$this->isValidHexColor($secondary_color)) {
            $json['error']['theme_novakur_base_secondary_color'] = $this->language->get('error_secondary_color');
        }

        if (!isset($json['error'])) {
            $this->load->model('setting/setting');
            $this->model_setting_setting->editSetting('theme_novakur_base', $this->request->post, $store_id);

            $json['success'] = $this->language->get('text_success');
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function install(): void {
        $this->load->model('setting/event');

        $this->model_setting_event->addEvent([
            'code'        => 'novakur_base',
            'description' => 'NovaKur theme template override',
            'trigger'     => 'view/*/before',
            'action'      => 'extension/novakur/event/novakur.view',
            'status'      => 1,
            'sort_order'  => 0
        ]);
    }

    public function uninstall(): void {
        $this->load->model('setting/event');
        $this->model_setting_event->deleteEventByCode('novakur_base');

        $this->load->model('setting/setting');
        $this->model_setting_setting->deleteSetting('theme_novakur_base');
    }

    private function isValidHexColor(string $color): bool {
        return (bool)preg_match('/^#(?:[0-9a-fA-F]{3}){1,2}$/', trim($color));
    }
}
