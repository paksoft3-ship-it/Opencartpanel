<?php
namespace Opencart\Admin\Controller\Extension\Novakur\Module;

class MegaMenu extends \Opencart\System\Engine\Controller {
    public function index(): void {
        $this->load->language('extension/novakur/module/mega_menu');
        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('module_mega_menu', $this->request->post);
            $this->session->data['success'] = $this->language->get('text_success');
            $this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
        }

        $data['breadcrumbs'] = [];
        $data['breadcrumbs'][] = [
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        ];
        $data['breadcrumbs'][] = [
            'text' => $this->language->get('text_extension'),
            'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
        ];
        $data['breadcrumbs'][] = [
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/novakur/module/mega_menu', 'user_token=' . $this->session->data['user_token'], true)
        ];

        $data['action'] = $this->url->link('extension/novakur/module/mega_menu', 'user_token=' . $this->session->data['user_token'], true);
        $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

        // Populate fields
        $fields = [
            'module_mega_menu_status' => 0,
            'module_mega_menu_depth'  => 2,
            'module_mega_menu_show_product_thumbnails' => 0,
            'module_mega_menu_show_promo_column' => 0,
            'module_mega_menu_promo_image' => '',
            'module_mega_menu_promo_link' => '',
            'module_mega_menu_columns' => 4,
            'module_mega_menu_background_color' => '#FFFFFF',
            'module_mega_menu_hover_color' => '#2563EB'
        ];

        foreach ($fields as $field => $default) {
            if (isset($this->request->post[$field])) {
                $data[$field] = $this->request->post[$field];
            } else {
                $data[$field] = $this->config->get($field) ?? $default;
            }
        }

        $this->load->model('tool/image');
        if (isset($this->request->post['module_mega_menu_promo_image']) && is_file(DIR_IMAGE . $this->request->post['module_mega_menu_promo_image'])) {
            $data['thumb'] = $this->model_tool_image->resize($this->request->post['module_mega_menu_promo_image'], 100, 100);
        } elseif (is_file(DIR_IMAGE . $this->config->get('module_mega_menu_promo_image'))) {
            $data['thumb'] = $this->model_tool_image->resize($this->config->get('module_mega_menu_promo_image'), 100, 100);
        } else {
            $data['thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
        }

        $data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/novakur/module/mega_menu', $data));
    }

    protected function validate(): bool {
        if (!$this->user->hasPermission('modify', 'extension/novakur/module/mega_menu')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }

    public function install(): void {
        $this->load->model('setting/setting');
        $this->model_setting_setting->editSetting('module_mega_menu', [
            'module_mega_menu_status' => 0,
            'module_mega_menu_depth' => 2,
            'module_mega_menu_show_product_thumbnails' => 0,
            'module_mega_menu_show_promo_column' => 0,
            'module_mega_menu_promo_image' => '',
            'module_mega_menu_promo_link' => '',
            'module_mega_menu_columns' => 4,
            'module_mega_menu_background_color' => '#FFFFFF',
            'module_mega_menu_hover_color' => '#2563EB'
        ]);

        $this->load->model('setting/event');
        $this->model_setting_event->addEvent([
            'code'        => 'novakur_mega_menu',
            'description' => 'NovaKur Mega Menu Injection',
            'trigger'     => 'catalog/view/common/header/before',
            'action'      => 'extension/novakur/event/mega_menu.view',
            'status'      => 1,
            'sort_order'  => 0
        ]);
    }

    public function uninstall(): void {
        $this->load->model('setting/setting');
        $this->model_setting_setting->deleteSetting('module_mega_menu');

        $this->load->model('setting/event');
        $this->model_setting_event->deleteEventByCode('novakur_mega_menu');
    }
}
