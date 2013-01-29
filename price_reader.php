<?php

/**
 * @version		0.1
 * @package		SmartPrice
 * @author    	Jookolas
 * @copyright	Copyright (c) 2013. All rights reserved.
 * @license		GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */
// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');
if (version_compare(JVERSION, '1.6.0', 'ge')) {
    jimport('joomla.html.parameter');
}

class plgContentSmart_price extends JPlugin {

    protected $_db = null;

    function plgContentSmart_price(&$subject, $params) {
        parent::__construct($subject, $params);
        if (is_null($this->_db))
            $this->_db = JFactory::getDbo();
    }

    // Joomla! 1.5
    function onPrepareContent(&$row, &$params, $page = 0) {
        $this->renderTS($row, $params, $page = 0);
    }

    // Joomla! 1.6/1.7/2.5
    function onContentPrepare($context, &$row, &$params, $page = 0) {
        $this->renderTS($row, $params, $page = 0);
    }

    // The main function
    function renderTS(&$row, &$params, $page = 0) {

        // Simple performance checks to determine whether plugin should process further
        if (!preg_match("#{price=.+?}#s", $row->text))
            return;

        //  Шаблон для замены цены
        $price_template = "
				<p class='cena'>
					{price_desription} 
					<span class='rur'>
						{price_value} руб.
					</span>
				</p>
			";

        //	Проверка на соответствие формату
        //                0         1              2                  3        4       5
        if (preg_match_all("/{price=(\d{1,2}) type=(base|city) column=(\d{1,2})( des=\"([a-zA-Zа-яА-Я0-9\s.,!?()']*)\")?}/u", $row->text, $matches, PREG_PATTERN_ORDER) > 0) {
            $counter = 0;

            // Составление списков ипользуемых на странице ID
            $base_id_list = $this->_prepare_id_list($matches);
            $city_id_list = $this->_prepare_id_list($matches, 'city');
            
            // Загрузка соответсвующих данных
            $data = array(
                'base' => $this->_load_data($base_id_list),
                'city' => $this->_load_data($city_id_list, 'city')
            );
            
            unset($base_id_list);
            unset($city_id_list);

            foreach ($matches[0] as $full_match) {
                // Подстановка описания цены
                $price_description = (!empty($matches[5][$counter])) ? $matches[5][$counter] : "";
                $current_template = str_replace("{price_desription}", $price_description, $price_template);

                // Подстановка значения цены
                $id = $matches[1][$counter];
                $type = $matches[2][$counter];
                $column = $matches[3][$counter];
                
                $price = "<Несуществующая цена>";
                if(isset($data[$type][$id]))
                    $price = $data[$type][$id][$column + 2];
                
                $current_template = str_replace("{price_value}", $price, $current_template);

                // Замена полного соответствия на шаблон
                $row->text = str_replace($full_match, $current_template, $row->text);

                $counter++;
            }
        }
    }

// End function

    /**
     * Загрузка списка цен из выбранной таблицы
     * @param array $ids - загружаемые цены
     * @param string $table - имя таблицы
     * @return mixed - массив со строками из таблицы или false
     */
    private function _load_data($ids = array(), $table = 'base') {
        // Подготовка входных данных
        $table_name = "#__pricelist" . $table;
        $id_list = join(',', $ids);

        // Создание запроса
        $query = $this->_db->getQuery(true)
                ->select('*')->from($table_name)
                ->where("`id` IN ($id_list)");

        // Выполнение запроса и проверка результата
        if (($result = $this->_db->setQuery($query)->loadRowList("0")) !== null)
            return $result;
        echo $query->dump();
        return false;
    }

    /**
     * Подготавливает список ID цен для загрузки из БД, которые были найдены
     * в тексте
     * @param array $matches - массив вхождений регулярного выражения
     * @param string $type - имя таблицы base или city
     * @return array - массив ID
     */
    private function _prepare_id_list($matches, $type = 'base') {
        $result = array();

        // Выборка ключей строк для требуемого $type
        $id_list_keys = array_keys($matches[2], $type);

        // Заполнение массива с ID по выбранным ключам
        foreach ($id_list_keys as $key)
            array_push($result, $matches[1][$key]);
        
        unset($id_list_keys);

        // Убирает дубликаты и возвращает результат
        return array_unique($result);
    }
}

// End class
