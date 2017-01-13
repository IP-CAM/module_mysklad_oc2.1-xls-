<?php

class ModelToolmyskladoc21 extends Model {

    private $CATEGORIES = array();
    private $PROPERTIES = array();


    /**
     * Генерирует xml с заказами
     *
     * @param	int	статус выгружаемых заказов
     * @param	int	новый статус заказов
     * @param	bool	уведомлять пользователя
     * @return	string
     */
    public function queryOrders($params) {

        $this->load->model('sale/order');

        if ($params['exchange_status'] != 0) {
            $query = $this->db->query("SELECT order_id FROM `" . DB_PREFIX . "order` WHERE `order_status_id` = " . $params['exchange_status'] . "");
        } else {
            $query = $this->db->query("SELECT order_id FROM `" . DB_PREFIX . "order` WHERE `date_added` >= '" . $params['from_date'] . "'");
        }

        $document = array();
        $document_counter = 0;

        if ($query->num_rows) {

            foreach ($query->rows as $orders_data) {

                $order = $this->model_sale_order->getOrder($orders_data['order_id']);

                $date = date('Y-m-d', strtotime($order['date_added']));
                $time = date('H:i:s', strtotime($order['date_added']));

                $document['Документ' . $document_counter] = array(
                    'Ид'          => $order['order_id']
                ,'Номер'       => $order['order_id']
                ,'Дата'        => $date
                ,'Время'       => $time
                ,'Валюта'      => $params['currency']
                ,'Курс'        => 1
                ,'ХозОперация' => 'Заказ товара'
                ,'Роль'        => 'Продавец'
                ,'Сумма'       => $order['total']
                ,'Комментарий' => $order['comment']
                );

                $document['Документ' . $document_counter]['Контрагенты']['Контрагент'] = array(
                    'Ид'                 => $order['customer_id'] . '#' . $order['email']
                ,'Наименование'		    => $order['payment_lastname'] . ' ' . $order['payment_firstname']
                ,'Роль'               => 'Покупатель'
                ,'ПолноеНаименование'	=> $order['payment_lastname'] . ' ' . $order['payment_firstname']
                ,'Фамилия'            => $order['payment_lastname']
                ,'Имя'			          => $order['payment_firstname']
                ,'Адрес' => array(
                        'Представление'	=> $order['shipping_address_1'].', '.$order['shipping_city'].', '.$order['shipping_postcode'].', '.$order['shipping_country']
                    )
                ,'Контакты' => array(
                        'Контакт1' => array(
                            'Тип' => 'ТелефонРабочий'
                        ,'Значение'	=> $order['telephone']
                        )
                    ,'Контакт2'	=> array(
                            'Тип' => 'Почта'
                        ,'Значение'	=> $order['email']
                        )
                    )
                );

                // Товары
                $products = $this->model_sale_order->getOrderProducts($orders_data['order_id']);

                $product_counter = 0;
                foreach ($products as $product) {

                    #TODO нужно как то подставить все данные (описание, код (ид товара)) надо узнать API
                    $document['Документ' . $document_counter]['Товары']['Товар' . $product_counter] = array(
                        'Ид'             => $product['product_id']
                    ,'Наименование'   => $product['name']
                    ,'ЦенаЗаЕдиницу'  => $product['price']
                    ,'Количество'     => $product['quantity']
                    ,'Сумма'          => $product['total']
                    );

                    if ($this->config->get('myskladoc21_relatedoptions')) {
                        $this->load->model('module/related_options');
                        if ($this->model_module_related_options->get_product_related_options_use($product['product_id'])) {
                            $order_options = $this->model_sale_order->getOrderOptions($orders_data['order_id'], $product['order_product_id']);
                            $options = array();
                            foreach ($order_options as $order_option) {
                                $options[$order_option['product_option_id']] = $order_option['product_option_value_id'];
                            }
                            if (count($options) > 0) {
                                $ro = $this->model_module_related_options->get_related_options_set_by_poids($product['product_id'], $options);
                                if ($ro != FALSE) {
                                    $char_id = $this->model_module_related_options->get_char_id($ro['relatedoptions_id']);
                                    if ($char_id != FALSE) {
                                        $document['Документ' . $document_counter]['Товары']['Товар' . $product_counter]['Ид'] .= "#".$char_id;
                                    }
                                }
                            }

                        }

                    }

                    $product_counter++;
                }

                $document_counter++;
            }
        }

        $root = '<?xml version="1.0" encoding="utf-8"?><КоммерческаяИнформация ВерсияСхемы="2.04" ДатаФормирования="' . date('Y-m-d', time()) . '" />';
        $xml = $this->array_to_xml($document, new SimpleXMLElement($root));

        return $xml->asXML();
    }

    public function queryOrdersStatus($params){

        $this->load->model('sale/order');

        if ($params['exchange_status'] != 0) {
            $query = $this->db->query("SELECT order_id FROM `" . DB_PREFIX . "order` WHERE `order_status_id` = " . $params['exchange_status'] . "");
        } else {
            $query = $this->db->query("SELECT order_id FROM `" . DB_PREFIX . "order` WHERE `date_added` >= '" . $params['from_date'] . "'");
        }

        if ($query->num_rows) {
            foreach ($query->rows as $orders_data) {
                $this->model_sale_order->addOrderHistory($orders_data['order_id'], array(
                    'order_status_id' => $params['new_status'],
                    'comment'         => '',
                    'notify'          => $params['notify']
                ));
            }
        }

        return true;
    }


    function array_to_xml($data, &$xml) {

        foreach($data as $key => $value) {
            if (is_array($value)) {
                if (!is_numeric($key)) {
                    $subnode = $xml->addChild(preg_replace('/\d/', '', $key));
                    $this->array_to_xml($value, $subnode);
                }
            }
            else {
                $xml->addChild($key, $value);
            }
        }

        return $xml;
    }

    function format($var){
        return preg_replace_callback(
            '/\\\u([0-9a-fA-F]{4})/',
            create_function('$match', 'return mb_convert_encoding("&#" . intval($match[1], 16) . ";", "UTF-8", "HTML-ENTITIES");'),
            json_encode($var)
        );
    }

    /**
     * Парсит цены и количество
     *
     * @param    string    наименование типа цены
     */
    public function parseOffers($filename, $config_price_type, $language_id) {

        $importFile = DIR_CACHE . 'myskladoc21/' . $filename;
        $xml = simplexml_load_file($importFile);
        $price_types = array();
        $config_price_type_main = array();
        $enable_log = $this->config->get('myskladoc21_full_log');
        $myskladoc21_relatedoptions = $this->config->get('myskladoc21_relatedoptions');
        $keywordChan = 0;

        $this->load->model('catalog/option');

        if ($enable_log)
            $this->log->write("Начат разбор файла: " . $filename);

        if (!empty($config_price_type) && count($config_price_type) > 0) {
            $config_price_type_main = array_shift($config_price_type);
        }

        if ($xml->ПакетПредложений->ТипыЦен->ТипЦены) {
            foreach ($xml->ПакетПредложений->ТипыЦен->ТипЦены as $key => $type) {
                $price_types[(string)$type->Ид] = (string)$type->Наименование;
                if($key == 0 && count($config_price_type_main) == 0) {
                    $config_price_type_main['keyword'] = (string)$type->Наименование;
                    $keywordChan = $config_price_type_main['keyword'];
                }
            }
        }

        // Инициализация массива скидок для оптимизации алгоритма
        if (!empty($config_price_type) && count($config_price_type) > 0) {
            $discount_price_type = array();
            foreach ($config_price_type as $obj) {
                $discount_price_type[$obj['keyword']] = array(
                    'customer_group_id' => $obj['customer_group_id'],
                    'quantity' => $obj['quantity'],
                    'priority' => $obj['priority']
                );
            }
        }

        $offer_cnt = 0;

        if ($xml->ПакетПредложений->Предложения->Предложение) {
            foreach ($xml->ПакетПредложений->Предложения->Предложение as $offer) {

                $new_product = (!isset($data));

                $offer_cnt++;

                if (!$myskladoc21_relatedoptions || $new_product) {

                    $data = array();
                    $data['price'] = 0;

                    //UUID без номера после #
                    $uuid = explode("#", $offer->Ид);
                    $data['1c_id'] = $uuid[0];
                    if ($enable_log)
                        $this->log->write("Товар: [UUID]:" . $data['1c_id']);

                    $product_id = $this->getProductIdBy1CProductId ($uuid[0]);

                    //Цена за единицу
                    if ($offer->Цены) {

                        // Первая цена по умолчанию - $config_price_type_main
                        if (!$keywordChan) {
                            $data['price'] = (float)$offer->Цены->Цена->ЦенаЗаЕдиницу;
                        }
                        else {
                            if ($offer->Цены->Цена->ИдТипаЦены) {
                                foreach ($offer->Цены->Цена as $price) {
                                    if ($price_types[(string)$price->ИдТипаЦены] == $config_price_type_main['keyword']) {
                                        $priceChan = (float)$price->ЦенаЗаЕдиницу;
                                        $data['price'] = $priceChan;
                                        if ($enable_log)
                                            $this->log->write(" найдена цена  > " . $data['price']);

                                    }
                                }
                            }
                        }

                        // Вторая цена и тд - $discount_price_type
                        if (!empty($discount_price_type) && $offer->Цены->Цена->ИдТипаЦены) {
                            foreach ($offer->Цены->Цена as $price) {
                                $key = $price_types[(string)$price->ИдТипаЦены];
                                if (isset($discount_price_type[$key])) {
                                    $value = array(
                                        'customer_group_id'	=> $discount_price_type[$key]['customer_group_id'],
                                        'quantity'      => $discount_price_type[$key]['quantity'],
                                        'priority'      => $discount_price_type[$key]['priority'],
                                        'price'         => (float)$price->ЦенаЗаЕдиницу,
                                        'date_start'    => '0000-00-00',
                                        'date_end'      => '0000-00-00'
                                    );
                                    $data['product_discount'][] = $value;
                                    unset($value);
                                }
                            }
                        }
                    }

                    //Количество
                    $data['quantity'] = isset($offer->Количество) ? (int)$offer->Количество : 0;
                }

                //Характеристики
                if ($offer->ХарактеристикиТовара->ХарактеристикаТовара) {

                    $product_option_value_data = array();
                    $product_option_data = array();

                    $lang_id = (int)$this->config->get('config_language_id');
                    $count = count($offer->ХарактеристикиТовара->ХарактеристикаТовара);

                    foreach ($offer->ХарактеристикиТовара->ХарактеристикаТовара as $i => $opt) {
                        $name_1c = (string)$opt->Наименование;
                        $value_1c = (string)$opt->Значение;

                        if (!empty($name_1c) && !empty($value_1c)) {

                            if ($myskladoc21_relatedoptions) {
                                $uuid = explode("#", $offer->Ид);
                                if (!isset($char_id) || $char_id != $uuid[1]) {
                                    $char_id = $uuid[1];
                                    if ($enable_log) $this->log->write("Характеристика: ".$char_id);
                                }
                            }

                            if ($enable_log) $this->log->write(" Найдены характеристики: " . $name_1c . " -> " . $value_1c);

                            $option_id = $this->setOption($name_1c);

                            $option_value_id = $this->setOptionValue($option_id, $value_1c);

                            $product_option_value_data[] = array(
                                'option_value_id'         => (int) $option_value_id,
                                'product_option_value_id' => '',
                                'quantity'                => isset($data['quantity']) ? (int)$data['quantity'] : 0,
                                'subtract'                => 0,
                                'price'                   => isset($data['price']) ? (int)$data['price'] : 0,
                                'price_prefix'            => '+',
                                'points'                  => 0,
                                'points_prefix'           => '+',
                                'weight'                  => 0,
                                'weight_prefix'           => '+'
                            );

                            $product_option_data[] = array(
                                'product_option_id'    => '',
                                'name'                 => (string)$name_1c,
                                'option_id'            => (int) $option_id,
                                'type'                 => 'select',
                                'required'             => 1,
                                'product_option_value' => $product_option_value_data
                            );

                            if ($myskladoc21_relatedoptions) {

                                if ( !isset($data['relatedoptions'])) {
                                    $data['relatedoptions'] = array();
                                    $data['related_options_variant_search'] = TRUE;
                                    $data['related_options_use'] = TRUE;
                                }

                                $ro_found = FALSE;
                                foreach ($data['relatedoptions'] as $ro_num => $relatedoptions) {
                                    if ($relatedoptions['char_id'] == $char_id) {
                                        $data['relatedoptions'][$ro_num]['options'][$option_id] = $option_value_id;
                                        $ro_found = TRUE;
                                        break;
                                    }
                                }
                                if (!$ro_found) {
                                    $data['relatedoptions'][] = array('char_id' => $char_id, 'quantity' => (isset($offer->Количество) ? (int)$offer->Количество : 0), 'options' => array($option_id => $option_value_id));
                                }

                            } else {
                                $data['product_option'] = $product_option_data;
                            }
                        }
                    }
                }

                if (!$myskladoc21_relatedoptions || $new_product) {

                    if ($offer->СкидкиНаценки) {
                        $value = array();
                        foreach ($offer->СкидкиНаценки->СкидкаНаценка as $discount) {
                            $value = array(
                                'customer_group_id'	=> 1
                            ,'priority'     => isset($discount->Приоритет) ? (int)$discount->Приоритет : 0
                            ,'price'        => (int)(($data['price'] * (100 - (float)str_replace(',', '.', (string)$discount->Процент))) / 100)
                            ,'date_start'   => isset($discount->ДатаНачала) ? (string)$discount->ДатаНачала : ''
                            ,'date_end'     => isset($discount->ДатаОкончания) ? (string)$discount->ДатаОкончания : ''
                            ,'quantity'     => 0
                            );

                            $data['product_discount'][] = $value;

                            if ($discount->ЗначениеУсловия) {
                                $value['quantity'] = (int)$discount->ЗначениеУсловия;
                            }

                            unset($value);
                        }
                    }

                    if ($offer->Статус) {
                        $data['status'] = (string)$offer->Статус;
                    }
                    else {
                        $data['status'] = 1;
                    }
                }

                if (!$myskladoc21_relatedoptions || $offer_cnt == count($xml->ПакетПредложений->Предложения->Предложение)
                    || $data['1c_id'] != substr($xml->ПакетПредложений->Предложения->Предложение[$offer_cnt]->Ид, 0, strlen($data['1c_id'])) ) {

                    $this->updateProduct($data, $product_id, $language_id);
                    unset($data);
                }



            }
        }

        $this->cache->delete('product');

        if ($enable_log)
            $this->log->write("Окончен разбор файла: " . $filename );

    }

    private function setOption($name){
        $lang_id = (int)$this->config->get('config_language_id');

        $query = $this->db->query("SELECT option_id FROM ". DB_PREFIX ."option_description WHERE name='". $this->db->escape($name) ."'");

        if ($query->num_rows > 0) {
            $option_id = $query->row['option_id'];
        }
        else {
            //Нет такой опции
            $this->db->query("INSERT INTO `" . DB_PREFIX . "option` SET type = 'select', sort_order = '0'");
            $option_id = $this->db->getLastId();
            $this->db->query("INSERT INTO " . DB_PREFIX . "option_description SET option_id = '" . $option_id . "', language_id = '" . $lang_id . "', name = '" . $this->db->escape($name) . "'");
        }
        return $option_id;
    }

    private function setOptionValue($option_id, $value) {
        $lang_id = (int)$this->config->get('config_language_id');

        $query = $this->db->query("SELECT option_value_id FROM ". DB_PREFIX ."option_value_description WHERE name='". $this->db->escape($value) ."' AND option_id='". $option_id ."'");

        if ($query->num_rows > 0) {
            $option_value_id = $query->row['option_value_id'];
        }
        else {
            //Добавляем значение опции, только если нет в базе
            $this->db->query("INSERT INTO " . DB_PREFIX . "option_value SET option_id = '" . $option_id . "', image = '', sort_order = '0'");
            $option_value_id = $this->db->getLastId();
            $this->db->query("INSERT INTO " . DB_PREFIX . "option_value_description SET option_value_id = '".$option_value_id."', language_id = '" . $lang_id . "', option_id = '" . $option_id . "', name = '" . $this->db->escape($value) . "'");
        }
        return $option_value_id;
    }


    /**
     * Инициализируем данные для категории дабы обновлять данные, а не затирать
     *
     * @param	array	старые данные
     * @param	int	id родительской категории
     * @param	array	новые данные
     * @return	array
     */
    private function initCategory($category, $parent, $data = array(), $language_id) {

        $result = array(
            'status'         => isset($data['status']) ? $data['status'] : 1
        ,'top'            => isset($data['top']) ? $data['top'] : 1
        ,'parent_id'      => $parent
        ,'category_store' => isset($data['category_store']) ? $data['category_store'] : array(0)
        ,'keyword'        => isset($data['keyword']) ? $data['keyword'] : ''
        ,'image'          => (isset($category->Картинка)) ? (string)$category->Картинка : ((isset($data['image'])) ? $data['image'] : '')
        ,'sort_order'     => (isset($category->Сортировка)) ? (int)$category->Сортировка : ((isset($data['sort_order'])) ? $data['sort_order'] : 0)
        ,'column'         => 1
        );

        $result['category_description'] = array(
            $language_id => array(
                'name'             => (string)$category->Наименование
            ,'meta_keyword'     => (isset($data['category_description'][$language_id]['meta_keyword'])) ? $data['category_description'][$language_id]['meta_keyword'] : ''
            ,'meta_description'	=> (isset($data['category_description'][$language_id]['meta_description'])) ? $data['category_description'][$language_id]['meta_description'] : ''
            ,'description'		  => (isset($category->Описание)) ? (string)$category->Описание : ((isset($data['category_description'][$language_id]['description'])) ? $data['category_description'][$language_id]['description'] : '')
                //,'meta_title'        => (isset($data['category_description'][$language_id]['seo_title'])) ? $data['category_description'][$language_id]['seo_title'] : ''
            ,'seo_h1'           => (isset($data['category_description'][$language_id]['seo_h1'])) ? $data['category_description'][$language_id]['seo_h1'] : ''
            ),
        );

        return $result;
    }


    /**
     * Функция добавляет корневую категорию и всех детей
     *
     * @param	SimpleXMLElement
     * @param	int
     */
    private function insertCategory($xml, $parent = 0, $language_id) {

        $this->load->model('catalog/category');

        foreach ($xml as $category){

            if (isset($category->Ид) && isset($category->Наименование) ){
                $id =  (string)$category->Ид;

                $data = array();

                $query = $this->db->query('SELECT * FROM `' . DB_PREFIX . 'category_to_1c` WHERE `1c_category_id` = "' . $this->db->escape($id) . '"');

                if ($query->num_rows) {
                    $category_id = (int)$query->row['category_id'];
                    $data = $this->model_catalog_category->getCategory($category_id);
                    $data['category_description'] = $this->model_catalog_category->getCategoryDescriptions($category_id);
                    $data = $this->initCategory($category, $parent, $data, $language_id);
                    $this->model_catalog_category->editCategory($category_id, $data);
                }
                else {
                    $data = $this->initCategory($category, $parent, array(), $language_id);
                    //$category_id = $this->getCategoryIdByName($data['category_description'][1]['name']) ? $this->getCategoryIdByName($data['category_description'][1]['name']) : $this->model_catalog_category->addCategory($data);
                    $category_id = $this->model_catalog_category->addCategory($data);
                    $this->db->query('INSERT INTO `' . DB_PREFIX . 'category_to_1c` SET category_id = ' . (int)$category_id . ', `1c_category_id` = "' . $this->db->escape($id) . '"');
                }

                $this->CATEGORIES[$id] = $category_id;
            }

            //только если тип 'translit'
            if ($this->config->get('myskladoc21_seo_url') == 2) {
                $cat_name = "category-" . $data['parent_id'] . "-" . $data['category_description'][$language_id]['name'];
                $this->setSeoURL('category_id', $category_id, $cat_name);
            }

            if ($category->Группы) $this->insertCategory($category->Группы->Группа, $category_id, $language_id);
        }

        unset($xml);
    }



    /**
     * Функция работы с продуктом
     * @param	int
     * @return	array
     */

    private function getProductWithAllData($product_id) {
        $this->load->model('catalog/product');
        $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE p.product_id = '" . (int)$product_id . "' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

        $data = array();

        if ($query->num_rows) {

            $data = $query->row;

            $data = array_merge($data, array('product_description' => $this->model_catalog_product->getProductDescriptions($product_id)));
            $data = array_merge($data, array('product_option' => $this->model_catalog_product->getProductOptions($product_id)));

            $data['product_image'] = array();

            $results = $this->model_catalog_product->getProductImages($product_id);

            foreach ($results as $result) {
                $data['product_image'][] = array(
                    'image' => $result['image'],
                    'sort_order' => $result['sort_order']
                );
            }

            if (method_exists($this->model_catalog_product, 'getProductMainCategoryId')) {
                $data = array_merge($data, array('main_category_id' => $this->model_catalog_product->getProductMainCategoryId($product_id)));
            }

            $data = array_merge($data, array('product_discount' => $this->model_catalog_product->getProductDiscounts($product_id)));
            $data = array_merge($data, array('product_special' => $this->model_catalog_product->getProductSpecials($product_id)));
            $data = array_merge($data, array('product_download' => $this->model_catalog_product->getProductDownloads($product_id)));
            $data = array_merge($data, array('product_category' => $this->model_catalog_product->getProductCategories($product_id)));
            $data = array_merge($data, array('product_store' => $this->model_catalog_product->getProductStores($product_id)));
            $data = array_merge($data, array('product_related' => $this->model_catalog_product->getProductRelated($product_id)));
            $data = array_merge($data, array('product_attribute' => $this->model_catalog_product->getProductAttributes($product_id)));

            if (VERSION == '1.5.3.1') {
                $data = array_merge($data, array('product_tag' => $this->model_catalog_product->getProductTags($product_id)));
            }
        }

        $query = $this->db->query('SELECT * FROM ' . DB_PREFIX . 'url_alias WHERE query LIKE "product_id='.$product_id.'"');
        if ($query->num_rows) $data['keyword'] = $query->row['keyword'];

        return $data;
    }


    /**
     * Устанавливает SEO URL (ЧПУ) для заданного товара
     *
     * @param 	inf
     * @param 	string
     */
    private function setSeoURL($url_type, $element_id, $element_name) {
        $this->db->query("DELETE FROM `" . DB_PREFIX . "url_alias` WHERE `query` = '" . $url_type . "=" . $element_id . "'");
        $this->db->query("INSERT INTO `" . DB_PREFIX . "url_alias` SET `query` = '" . $url_type . "=" . $element_id ."', `keyword`='" . $this->transString($element_name) . "'");
    }

    /**
     * Транслиетрирует RUS->ENG
     * @param string $aString
     * @return string type
     */
    private function transString($aString) {
        $rus = array(" ", "/", "*", "-", "+", "`", "!", "@", "#", "$", "%", "^", "&", "*", "(", ")", "_", "+", "[", "]", "{", "}", "~", ";", ":", "'", "\"", "<", ">", ",", ".", "?", "А", "Б", "В", "Г", "Д", "Е", "З", "И", "Й", "К", "Л", "М", "Н", "О", "П", "Р", "С", "Т", "У", "Ф", "Х", "Ъ", "Ы", "Ь", "Э", "а", "б", "в", "г", "д", "е", "з", "и", "й", "к", "л", "м", "н", "о", "п", "р", "с", "т", "у", "ф", "х", "ъ", "ы", "ь", "э", "ё",  "ж",  "ц",  "ч",  "ш",  "щ",   "ю",  "я",  "Ё",  "Ж",  "Ц",  "Ч",  "Ш",  "Щ",   "Ю",  "Я");
        $lat = array("-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-",  "-", "-", "-", "-", "-", "-", "a", "b", "v", "g", "d", "e", "z", "i", "y", "k", "l", "m", "n", "o", "p", "r", "s", "t", "u", "f", "h", "",  "i", "",  "e", "a", "b", "v", "g", "d", "e", "z", "i", "j", "k", "l", "m", "n", "o", "p", "r", "s", "t", "u", "f", "h", "",  "i", "",  "e", "yo", "zh", "ts", "ch", "sh", "sch", "yu", "ya", "yo", "zh", "ts", "ch", "sh", "sch", "yu", "ya");

        $string = str_replace($rus, $lat, $aString);

        while (mb_strpos($string, '--')) {
            $string = str_replace('--', '-', $string);
        }

        $string = strtolower(trim($string, '-'));

        return $string;
    }

    /**
     * Получает product_id по артикулу
     *
     * @param 	string
     * @return 	int|bool
     */
    private function getProductBySKU($sku) {

        $query = $this->db->query("SELECT product_id FROM `" . DB_PREFIX . "product` WHERE `sku` = '" . $this->db->escape($sku) . "'");

        if ($query->num_rows) {
            return $query->row['product_id'];
        }
        else {
            return false;
        }
    }



    /**
     * Заполняет продуктами родительские категории
     */
    public function fillParentsCategories() {
        $this->load->model('catalog/product');
        if (!method_exists($this->model_catalog_product, 'getProductMainCategoryId')) {
            $this->log->write("  !!!: Заполнение родительскими категориями отменено. Отсутствует main_category_id.");
            return;
        }

        $this->db->query('DELETE FROM `' .DB_PREFIX . 'product_to_category` WHERE `main_category` = 0');
        $query = $this->db->query('SELECT * FROM `' . DB_PREFIX . 'product_to_category` WHERE `main_category` = 1');

        if ($query->num_rows) {
            foreach ($query->rows as $row) {
                $parents = $this->findParentsCategories($row['category_id']);
                foreach ($parents as $parent) {
                    if ($row['category_id'] != $parent && $parent != 0) {
                        $this->db->query('INSERT INTO `' .DB_PREFIX . 'product_to_category` SET `product_id` = ' . $row['product_id'] . ', `category_id` = ' . $parent . ', `main_category` = 0');
                    }
                }
            }
        }
    }

    /**
     * Ищет все родительские категории
     *
     * @param	int
     * @return	array
     */
    private function findParentsCategories($category_id) {
        $query = $this->db->query('SELECT * FROM `'.DB_PREFIX.'category` WHERE `category_id` = "'.$category_id.'"');
        if (isset($query->row['parent_id'])) {
            $result = $this->findParentsCategories($query->row['parent_id']);
        }
        $result[] = $category_id;
        return $result;
    }

    /**
     * Получает language_id из code (ru, en, etc)
     * Как ни странно, подходящей функции в API не нашлось
     *
     * @param	string
     * @return	int
     */
    public function getLanguageId($lang) {
        $query = $this->db->query('SELECT `language_id` FROM `' . DB_PREFIX . 'language` WHERE `code` = "'.$lang.'"');
        return $query->row['language_id'];
    }



}