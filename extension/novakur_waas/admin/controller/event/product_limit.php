<?php
declare(strict_types=1);

namespace Opencart\Admin\Controller\Extension\NovakurWaas\Event;

class ProductLimit extends \Opencart\System\Engine\Controller {
    public function check(string &$route, array &$data, mixed &$output): void {
        if (isset($data[0]) && is_array($data[0]) && !empty($data[0]['product_id'])) {
            return;
        }

        $total_query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "product` WHERE status = '1'");
        $limit_query = $this->db->query("SELECT value FROM `" . DB_PREFIX . "setting` WHERE store_id = '0' AND `key` = 'novakur_product_limit' LIMIT 1");

        if (!$limit_query->num_rows) {
            return;
        }

        $total = (int)($total_query->row['total'] ?? 0);
        $limit = (int)($limit_query->row['value'] ?? 0);

        if ($total >= $limit) {
            $output = json_encode([
                'error' => [
                    'warning' => 'Product limit reached (' . $total . '/' . $limit . '). Upgrade your plan.'
                ]
            ], JSON_UNESCAPED_SLASHES);

            $route = '';
        }
    }
}
