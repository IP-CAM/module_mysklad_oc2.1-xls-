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
            $this->response->redirect($this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=module', true));
        }

        $data['heading_title'] = $this->language->get('heading_title');
        $data['entry_username'] = $this->language->get('entry_username');
        $data['entry_password'] = $this->language->get('entry_password');
        $data['entry_allow_ip'] = $this->language->get('entry_allow_ip');
        $data['text_price_default'] = $this->language->get('text_price_default');
        $data['entry_config_price_type'] = $this->language->get('entry_config_price_type');

        $data['entry_customer_group'] = $this->language->get('entry_customer_group');
        $data['entry_quantity'] = $this->language->get('entry_quantity');
        $data['entry_priority'] = $this->language->get('entry_priority');
        $data['entry_flush_product'] = $this->language->get('entry_flush_product');
        $data['entry_flush_category'] = $this->language->get('entry_flush_category');
        $data['entry_flush_manufacturer'] = $this->language->get('entry_flush_manufacturer');
        $data['entry_flush_quantity'] = $this->language->get('entry_flush_quantity');
        $data['entry_flush_attribute'] = $this->language->get('entry_flush_attribute');
        $data['entry_fill_parent_cats'] = $this->language->get('entry_fill_parent_cats');
        $data['entry_seo_url'] = $this->language->get('entry_seo_url');
        $data['entry_full_log'] = $this->language->get('entry_full_log');
        $data['entry_apply_watermark'] = $this->language->get('entry_apply_watermark');
        $data['no_image'] = $this->model_tool_image->resize('no_image.jpg', 100, 100);
        $data['text_image_manager'] = $this->language->get('text_image_manager');
        $data['text_browse'] = $this->language->get('text_browse');
        $data['text_clear'] = $this->language->get('text_clear');
        $data['entry_name'] = $this->language->get('entry_name');
        $data['entry_image'] = $this->language->get('entry_image');

        $data['entry_relatedoptions'] = $this->language->get('entry_relatedoptions');
        $data['entry_relatedoptions_help'] = $this->language->get('entry_relatedoptions_help');
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
        $data['entry_order_notify'] = $this->language->get('entry_order_notify');
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
            'text' => $this->language->get('text_extension'),
            'href' => $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=module', true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('module/myskladoc21', 'token=' . $this->session->data['token'], true)
        );
        $data['token'] = $this->session->data['token'];

        $data['action'] = $this->url->link('module/myskladoc21', 'token=' . $this->session->data['token'], true);

        $data['cancel'] = $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=module', true);

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

        if (isset($this->request->post['myskladoc21_flush_product'])) {
            $data['myskladoc21_flush_product'] = $this->request->post['myskladoc21_flush_product'];
        }
        else {
            $data['myskladoc21_flush_product'] = $this->config->get('myskladoc21_flush_product');
        }

        if (isset($this->request->post['myskladoc21_flush_category'])) {
            $data['myskladoc21_flush_category'] = $this->request->post['myskladoc21_flush_category'];
        }
        else {
            $data['myskladoc21_flush_category'] = $this->config->get('myskladoc21_flush_category');
        }

        if (isset($this->request->post['myskladoc21_flush_manufacturer'])) {
            $data['myskladoc21_flush_manufacturer'] = $this->request->post['myskladoc21_flush_manufacturer'];
        }
        else {
            $data['myskladoc21_flush_manufacturer'] = $this->config->get('myskladoc21_flush_manufacturer');
        }

        if (isset($this->request->post['myskladoc21_flush_quantity'])) {
            $data['myskladoc21_flush_quantity'] = $this->request->post['myskladoc21_flush_quantity'];
        }
        else {
            $data['myskladoc21_flush_quantity'] = $this->config->get('myskladoc21_flush_quantity');
        }

        if (isset($this->request->post['myskladoc21_flush_attribute'])) {
            $data['myskladoc21_flush_attribute'] = $this->request->post['myskladoc21_flush_attribute'];
        }
        else {
            $data['myskladoc21_flush_attribute'] = $this->config->get('myskladoc21_flush_attribute');
        }

        if (isset($this->request->post['myskladoc21_fill_parent_cats'])) {
            $data['myskladoc21_fill_parent_cats'] = $this->request->post['myskladoc21_fill_parent_cats'];
        }
        else {
            $data['myskladoc21_fill_parent_cats'] = $this->config->get('myskladoc21_fill_parent_cats');
        }

        if (isset($this->request->post['myskladoc21_relatedoptions'])) {
            $data['myskladoc21_relatedoptions'] = $this->request->post['myskladoc21_relatedoptions'];
        } else {
            $data['myskladoc21_relatedoptions'] = $this->config->get('myskladoc21_relatedoptions');
        }
        if (isset($this->request->post['myskladoc21_order_status_to_exchange'])) {
            $data['myskladoc21_order_status_to_exchange'] = $this->request->post['myskladoc21_order_status_to_exchange'];
        } else {
            $data['myskladoc21_order_status_to_exchange'] = $this->config->get('myskladoc21_order_status_to_exchange');
        }

        if (isset($this->request->post['myskladoc21_seo_url'])) {
            $data['myskladoc21_seo_url'] = $this->request->post['myskladoc21_seo_url'];
        }
        else {
            $data['myskladoc21_seo_url'] = $this->config->get('myskladoc21_seo_url');
        }

        if (isset($this->request->post['myskladoc21_full_log'])) {
            $data['myskladoc21_full_log'] = $this->request->post['myskladoc21_full_log'];
        }
        else {
            $data['myskladoc21_full_log'] = $this->config->get('myskladoc21_full_log');
        }

        if (isset($this->request->post['myskladoc21_apply_watermark'])) {
            $data['myskladoc21_apply_watermark'] = $this->request->post['myskladoc21_apply_watermark'];
        }
        else {
            $data['myskladoc21_apply_watermark'] = $this->config->get('myskladoc21_apply_watermark');
        }

        if (isset($this->request->post['myskladoc21_watermark'])) {
            $data['myskladoc21_watermark'] = $this->request->post['myskladoc21_watermark'];
        }
        else {
            $data['myskladoc21_watermark'] = $this->config->get('myskladoc21_watermark');
        }

        if (isset($data['myskladoc21_watermark'])) {

            $data['thumb'] = $this->model_tool_image->resize($data['myskladoc21_watermark'], 100, 100);
        }
        else {
            $data['thumb'] = $this->model_tool_image->resize('no_image.jpg', 100, 100);
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

        if (isset($this->request->post['myskladoc21_order_notify'])) {
            $data['myskladoc21_order_notify'] = $this->request->post['myskladoc21_order_notify'];
        }
        else {
            $data['myskladoc21_order_notify'] = $this->config->get('myskladoc21_order_notify');
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

        // Разрешен ли IP
        if ($this->config->get('myskladoc21_allow_ip') != '') {
            $ip = $_SERVER['REMOTE_ADDR'];
            $allow_ips = explode("\r\n", $this->config->get('myskladoc21_allow_ip'));

            if (!in_array($ip, $allow_ips)) {
                echo "failure\n";
                echo "IP is not allowed";
                exit;
            }
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

    public function manualImport() {
        $this->load->language('module/myskladoc21');

        $cache = DIR_CACHE . 'myskladoc21/';
        $json = array();

        if (!empty($this->request->files['file']['name'])) {

            $zip = new ZipArchive;

            if ($zip->open($this->request->files['file']['tmp_name']) === true) {
                $this->modeCatalogInit(false);

                $zip->extractTo($cache);
                $files = scandir($cache);

                foreach ($files as $file) {
                    if (is_file($cache . $file)) {
                        $this->modeImport($file);
                    }
                }

                if (is_dir($cache . 'import_files')) {
                    $images = DIR_IMAGE . 'import_files/';

                    if (is_dir($images)) {
                        $this->cleanDir($images);
                    }

                    rename($cache . 'import_files/', $images);
                }

            }
            else {

                // Читаем первые 1024 байт и определяем файл по сигнатуре, ибо мало ли, какое у него имя
                $handle = fopen($this->request->files['file']['tmp_name'], 'r');
                $buffer = fread($handle, 1024);
                fclose($handle);

                if (strpos($buffer, 'Классификатор')) {
                    $this->modeCatalogInit(false);
                    move_uploaded_file($this->request->files['file']['tmp_name'], $cache . 'import.xml');
                    $this->modeImport('import.xml');

                }
                else if (strpos($buffer, 'ПакетПредложений')) {
                    move_uploaded_file($this->request->files['file']['tmp_name'], $cache . 'offers.xml');
                    $this->modeImport('offers.xml');
                }
                else {
                    $json['error'] = $this->language->get('text_upload_error');
                    exit;
                }
            }

            $json['success'] = $this->language->get('text_upload_success');
        }

        $this->response->setOutput(json_encode($json));
    }

    public function modeCatalogInit($echo = true) {

        $this->load->model('tool/myskladoc21');

        // чистим кеш, убиваем старые данные
        $this->cleanCacheDir();

        // Проверяем естль ли БД для хранения промежуточных данных.
        $this->model_tool_myskladoc21->checkDbSheme();

        // Очищаем таблицы
        $this->model_tool_myskladoc21->flushDb(array(
            'product'       => $this->config->get('myskladoc21_flush_product'),
            'category'      => $this->config->get('myskladoc21_flush_category'),
            'manufacturer'  => $this->config->get('myskladoc21_flush_manufacturer'),
            'attribute'     => $this->config->get('myskladoc21_flush_attribute'),
            'full_log'      => $this->config->get('myskladoc21_full_log'),
            'apply_watermark'   => $this->config->get('myskladoc21_apply_watermark'),
            'quantity'      => $this->config->get('myskladoc21_flush_quantity')
        ));

        $limit = 100000 * 1024;

        if ($echo) {
            echo "zip=no\n";
            echo "file_limit=".$limit."\n";
        }

    }

    public function modeSaleInit() {
        $limit = 100000 * 1024;

        echo "zip=no\n";
        echo "file_limit=".$limit."\n";
    }

    public function modeFile() {

        if (!isset($this->request->cookie['key'])) {
            return;
        }

        if ($this->request->cookie['key'] != md5($this->config->get('myskladoc21_password'))) {
            echo "failure\n";
            echo "Session error";
            return;
        }

        $cache = DIR_CACHE . 'myskladoc21/';

        // Проверяем на наличие имени файла
        if (isset($this->request->get['filename'])) {
            $uplod_file = $cache . $this->request->get['filename'];
        }
        else {
            echo "failure\n";
            echo "ERROR 10: No file name variable";
            return;
        }

        // Проверяем XML или изображения
        if (strpos($this->request->get['filename'], 'import_files') !== false) {
            $cache = DIR_IMAGE;
            $uplod_file = $cache . $this->request->get['filename'];
            $this->checkUploadFileTree(dirname($this->request->get['filename']) , $cache);
        }

        // Получаем данные
        $data = file_get_contents("php://input");

        if ($data !== false) {
            if ($fp = fopen($uplod_file, "wb")) {
                $result = fwrite($fp, $data);

                if ($result === strlen($data)) {
                    echo "success\n";

                    chmod($uplod_file , 0777);
                    //echo "success\n";
                }
                else {
                    echo "failure\n";
                }
            }
            else {
                echo "failure\n";
                echo "Can not open file: $uplod_file\n";
                echo $cache;
            }
        }
        else {
            echo "failure\n";
            echo "No data file\n";
        }


    }

    public function modeImport($manual = false) {

        $cache = DIR_CACHE . 'myskladoc21/';

        if ($manual) {
            $filename = $manual;
            $importFile = $cache . $filename;
        }
        else if (isset($this->request->get['filename'])) {
            $filename = $this->request->get['filename'];
            $importFile = $cache . $filename;
        }
        else {
            echo "failure\n";
            echo "ERROR 10: No file name variable";
            return 0;
        }

        $this->load->model('tool/myskladoc21');

        // Определяем текущую локаль
        $language_id = $this->model_tool_myskladoc21->getLanguageId($this->config->get('config_language'));

        if (strpos($filename, 'import') !== false) {

            $this->model_tool_myskladoc21->parseImport($filename, $language_id);

            if ($this->config->get('myskladoc21_fill_parent_cats')) {
                $this->model_tool_myskladoc21->fillParentsCategories();
            }

            if ($this->config->get('myskladoc21_seo_url')) {
                $this->load->model('module/deadcow_seo');
                $this->model_module_deadcow_seo->generateCategories($this->config->get('deadcow_seo_categories_template'), 'Russian');
                $this->model_module_deadcow_seo->generateProducts($this->config->get('deadcow_seo_products_template'), 'Russian');
                $this->model_module_deadcow_seo->generateManufacturers($this->config->get('deadcow_seo_manufacturers_template'), 'Russian');
            }

            if (!$manual) {
                echo "success\n";
            }

        }
        else if (strpos($filename, 'offers') !== false) {
            $myskladoc21_price_type = $this->config->get('myskladoc21_price_type');
            $this->model_tool_myskladoc21->parseOffers($filename, $myskladoc21_price_type, $language_id);

            if (!$manual) {
                echo "success\n";
            }
        }
        else {
            echo "failure\n";
            echo $filename;
        }

        $this->cache->delete('product');
        return;
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
        ,'notify'       => $this->config->get('myskladoc21_order_notify')
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
            'notify'            => $this->config->get('myskladoc21_order_notify')
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


    // -- Системные процедуры
    private function cleanCacheDir() {

        // Проверяем есть ли директория
        if (file_exists(DIR_CACHE . 'myskladoc21')) {
            if (is_dir(DIR_CACHE . 'myskladoc21')) {
                return $this->cleanDir(DIR_CACHE . 'myskladoc21/');
            }
            else {
                unlink(DIR_CACHE . 'myskladoc21');
            }
        }

        mkdir (DIR_CACHE . 'myskladoc21');

        return 0;
    }

    private function checkUploadFileTree($path, $curDir = null) {

        if (!$curDir) $curDir = DIR_CACHE . 'myskladoc21/';

        foreach (explode('/', $path) as $name) {

            if (!$name) continue;

            if (file_exists($curDir . $name)) {
                if (is_dir( $curDir . $name)) {
                    $curDir = $curDir . $name . '/';
                    continue;
                }

                unlink ($curDir . $name);
            }

            mkdir ($curDir . $name );
            $curDir = $curDir . $name . '/';
        }

    }


    private function cleanDir($root, $self = false) {

        $dir = dir($root);

        while ($file = $dir->read()) {
            if ($file == '.' || $file == '..') continue;
            if (file_exists($root . $file)) {
                if (is_file($root . $file)) { unlink($root . $file); continue; }
                if (is_dir($root . $file)) { $this->cleanDir($root . $file . '/', true); continue; }
                var_dump ($file);
            }
            var_dump($file);
        }

        if ($self) {
            if(file_exists($root) && is_dir($root)) {
                rmdir($root); return 0;
            }

            var_dump($root);
        }
        return 0;
    }

}
?>