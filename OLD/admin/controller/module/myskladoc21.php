<?php
ini_set('display_errors',1);
error_reporting(E_ALL ^E_NOTICE);

class Controllermodulemyskladoc21 extends Controller {
    private $error = array();

    public function index() {

        $this->load->language('module/myskladoc21');
        $this->load->model('tool/image');

        //$this->document->title = $this->language->get('heading_title');
        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->request->post['myskladoc21_order_date'] = $this->config->get('myskladoc21_order_date');
            $this->model_setting_setting->editSetting('myskladoc21', $this->request->post);
            $this->session->data['success'] = $this->language->get('text_success');
            $this->response->redirect($this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'));
        }

        $data['heading_title'] = $this->language->get('heading_title');
        $data['entry_username'] = $this->language->get('entry_username');
        $data['entry_password'] = $this->language->get('entry_password');


        $data['entry_customer_group'] = $this->language->get('entry_customer_group');
        $data['entry_quantity'] = $this->language->get('entry_quantity');
        $data['entry_priority'] = $this->language->get('entry_priority');
        $data['text_image_manager'] = $this->language->get('text_image_manager');
        $data['text_browse'] = $this->language->get('text_browse');
        $data['text_clear'] = $this->language->get('text_clear');
        $data['entry_name'] = $this->language->get('entry_name');
        $data['entry_image'] = $this->language->get('entry_image');

        $data['entry_order_status_to_exchange'] = $this->language->get('entry_order_status_to_exchange');
        $data['entry_order_status_to_exchange_not'] = $this->language->get('entry_order_status_to_exchange_not');

        $data['text_yes'] = $this->language->get('text_yes');
        $data['text_no'] = $this->language->get('text_no');
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');
        $data['text_tab_general'] = $this->language->get('text_tab_general');
        $data['text_tab_product'] = $this->language->get('text_tab_product');
        $data['text_tab_order'] = $this->language->get('text_tab_order');
        $data['text_tab_manual'] = $this->language->get('text_tab_manual');
        $data['text_empty'] = $this->language->get('text_empty');
        $data['text_max_filesize'] = sprintf($this->language->get('text_max_filesize'), @ini_get('max_file_uploads'));
        $data['text_homepage'] = $this->language->get('text_homepage');
        $data['entry_status'] = $this->language->get('entry_status');
        $data['entry_order_status'] = $this->language->get('entry_order_status');
        $data['entry_order_currency'] = $this->language->get('entry_order_currency');
        $data['entry_upload'] = $this->language->get('entry_upload');
        $data['button_upload'] = $this->language->get('button_upload');

        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');
        $data['button_insert'] = $this->language->get('button_insert');
        $data['button_remove'] = $this->language->get('button_remove');

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        }
        else {
            $data['error_warning'] = '';
        }

        if (isset($this->error['image'])) {
            $data['error_image'] = $this->error['image'];
        } else {
            $data['error_image'] = '';
        }

        if (isset($this->error['myskladoc21_username'])) {
            $data['error_myskladoc21_username'] = $this->error['myskladoc21_username'];
        }
        else {
            $data['error_myskladoc21_username'] = '';
        }

        if (isset($this->error['myskladoc21_password'])) {
            $data['error_myskladoc21_password'] = $this->error['myskladoc21_password'];
        }
        else {
            $data['error_myskladoc21_password'] = '';
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
        );


        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('module/myskladoc21', 'token=' . $this->session->data['token'], true)
        );
        $data['token'] = $this->session->data['token'];

        $data['action'] = $this->url->link('module/myskladoc21', 'token=' . $this->session->data['token'], true);

        $data['cancel'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');;

        if (isset($this->request->post['myskladoc21_username'])) {
            $data['myskladoc21_username'] = $this->request->post['myskladoc21_username'];
        }
        else {
            $data['myskladoc21_username'] = $this->config->get('myskladoc21_username');
        }

        if (isset($this->request->post['myskladoc21_password'])) {
            $data['myskladoc21_password'] = $this->request->post['myskladoc21_password'];
        }
        else {
            $data['myskladoc21_password'] = $this->config->get('myskladoc21_password');
        }

        if (isset($this->request->post['myskladoc21_allow_ip'])) {
            $data['myskladoc21_allow_ip'] = $this->request->post['myskladoc21_allow_ip'];
        }
        else {
            $data['myskladoc21_allow_ip'] = $this->config->get('myskladoc21_allow_ip');
        }

        if (isset($this->request->post['myskladoc21_status'])) {
            $data['myskladoc21_status'] = $this->request->post['myskladoc21_status'];
        }
        else {
            $data['myskladoc21_status'] = $this->config->get('myskladoc21_status');
        }

        if (isset($this->request->post['myskladoc21_price_type'])) {
            $data['myskladoc21_price_type'] = $this->request->post['myskladoc21_price_type'];
        }
        else {
            $data['myskladoc21_price_type'] = $this->config->get('myskladoc21_price_type');
            if(empty($data['myskladoc21_price_type'])) {
                $data['myskladoc21_price_type'][] = array(
                    'keyword'           => '',
                    'customer_group_id'     => 0,
                    'quantity'          => 0,
                    'priority'          => 0
                );
            }
        }


        if (isset($this->request->post['myskladoc21_order_status_to_exchange'])) {
            $data['myskladoc21_order_status_to_exchange'] = $this->request->post['myskladoc21_order_status_to_exchange'];
        } else {
            $data['myskladoc21_order_status_to_exchange'] = $this->config->get('myskladoc21_order_status_to_exchange');
        }


        if (isset($this->request->post['myskladoc21_order_status'])) {
            $data['myskladoc21_order_status'] = $this->request->post['myskladoc21_order_status'];
        }
        else {
            $data['myskladoc21_order_status'] = $this->config->get('myskladoc21_order_status');
        }

        if (isset($this->request->post['myskladoc21_order_currency'])) {
            $data['myskladoc21_order_currency'] = $this->request->post['myskladoc21_order_currency'];
        }
        else {
            $data['myskladoc21_order_currency'] = $this->config->get('myskladoc21_order_currency');
        }


        // Группы
        $this->load->model('customer/customer_group');
        $data['customer_groups'] = $this->model_customer_customer_group->getCustomerGroups();

        $this->load->model('localisation/order_status');

        $order_statuses = $this->model_localisation_order_status->getOrderStatuses();

        foreach ($order_statuses as $order_status) {
            $data['order_statuses'][] = array(
                'order_status_id' => $order_status['order_status_id'],
                'name'            => $order_status['name']
            );
        }

        $this->template = 'module/myskladoc21.tpl';
        $this->children = array(
            'common/header',
            'common/footer' 
        );

        $data['heading_title'] = $this->language->get('heading_title');
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $this->response->setOutput($this->load->view('module/myskladoc21.tpl', $data));

        //$this->response->setOutput($this->render(), $this->config->get('config_compression'));
    }

    private function validate() {

        if (!$this->user->hasPermission('modify', 'module/myskladoc21')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;

    }

    public function install() {}

    public function uninstall() {}

    // ---
    public function modeCheckauth() {

        // Проверяем включен или нет модуль
        if (!$this->config->get('myskladoc21_status')) {
            echo "failure\n";
            echo "myskladoc21 module OFF";
            exit;
        }

        // Авторизуем
        if (($this->config->get('myskladoc21_username') != '') && (@$_SERVER['PHP_AUTH_USER'] != $this->config->get('myskladoc21_username'))) {
            echo "failure\n";
            echo "error login";
        }

        if (($this->config->get('myskladoc21_password') != '') && (@$_SERVER['PHP_AUTH_PW'] != $this->config->get('myskladoc21_password'))) {
            echo "failure\n";

            echo "error password";
            exit;
        }

        echo "success\n";
        echo "key\n";
        echo md5($this->config->get('myskladoc21_password')) . "\n";
    }

    public function modeSaleInit() {
        $limit = 100000 * 1024;

        echo "zip=no\n";
        echo "file_limit=".$limit."\n";
    }



    public function modeQueryOrders() {
        if (!isset($this->request->cookie['key'])) {
            echo "Cookie fail\n";
            return;
        }

        if ($this->request->cookie['key'] != md5($this->config->get('myskladoc21_password'))) {
            echo "failure\n";
            echo "Session error";
            return;
        }

        $this->load->model('tool/myskladoc21');

        $orders = $this->model_tool_myskladoc21->queryOrders(array(
            'from_date'     => $this->config->get('myskladoc21_order_date')
        ,'exchange_status'  => $this->config->get('myskladoc21_order_status_to_exchange')
        ,'new_status'   => $this->config->get('myskladoc21_order_status')
        ,'currency'     => $this->config->get('myskladoc21_order_currency') ? $this->config->get('myskladoc21_order_currency') : 'руб.'
        ));
        
 
        echo iconv('utf-8', 'cp1251', $orders);
    }

public function modeOrdersChangeStatus(){
        if (!isset($this->request->cookie['key'])) {
            echo "Cookie fail\n";
            return;
        }

        if ($this->request->cookie['key'] != md5($this->config->get('myskladoc21_password'))) {
            echo "failure\n";
            echo "Session error";
            return;
        }

        $this->load->model('tool/myskladoc21');

        $result = $this->model_tool_myskladoc21->queryOrdersStatus(array(
            'from_date'         => $this->config->get('myskladoc21_order_date'),
            'exchange_status'   => $this->config->get('myskladoc21_order_status_to_exchange'),
            'new_status'        => $this->config->get('myskladoc21_order_status'),
        ));

        if($result){
            $this->load->model('setting/setting');
            $config = $this->model_setting_setting->getSetting('myskladoc21');
            $config['myskladoc21_order_date'] = date('Y-m-d H:i:s');
            $this->model_setting_setting->editSetting('myskladoc21', $config);
        }

        if($result)
            echo "success\n";
        else
            echo "fail\n";
    }

}
?>