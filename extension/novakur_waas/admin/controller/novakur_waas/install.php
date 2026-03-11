<?php
declare(strict_types=1);

namespace Opencart\Admin\Controller\Extension\NovakurWaas\NovakurWaas;

class Install extends \Opencart\System\Engine\Controller {
    public function install(): void {
        $this->load->model('setting/event');

        $this->model_setting_event->addEvent(
            'novakur_waas_product_limit',
            'NovaKur WaaS Product Limit',
            'model/catalog/product/addProduct/before',
            'extension/novakur_waas/event/product_limit.check',
            true,
            0
        );
    }

    public function uninstall(): void {
        $this->load->model('setting/event');
        $this->model_setting_event->deleteEventByCode('novakur_waas_product_limit');
    }
}
