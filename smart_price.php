<?php
/**
 * @version		0.1
 * @package		SmartPrice
 * @author    	Jookolas
 * @copyright	Copyright (c) 2013. All rights reserved.
 * @license		GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.plugin.plugin');
if(version_compare(JVERSION, '1.6.0', 'ge')) {
	jimport('joomla.html.parameter');
}

class plgContentSmart_price extends JPlugin {

	function plgContentSmart_price( &$subject, $params ){
		parent::__construct( $subject, $params );
	}

	// Joomla! 1.5
	function onPrepareContent(&$row, &$params, $page = 0){
		$this->renderTS($row, $params, $page = 0);
	}

	// Joomla! 1.6/1.7/2.5
	function onContentPrepare($context, &$row, &$params, $page = 0){
		$this->renderTS($row, $params, $page = 0);
	}

	// The main function
	function renderTS(&$row, &$params, $page = 0){

		// Simple performance checks to determine whether plugin should process further
		if(!preg_match("#{price=.+?}#s", $row->text)) return;

		// Check if plugin is enabled
		if(JPluginHelper::isEnabled('content',$this->plg_name)==false) return;

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
		if(preg_match_all("/{price=(\d{1,2}) type=(base|city) column=(\d{1,2})( des=\"([a-zA-Zа-яА-Я0-9\s.,!?()']*)\")?}/u", $row->text, $matches, PREG_PATTERN_ORDER) > 0) {
			$counter = 0;
			
			foreach($matches[0] as $full_match) {
				// Подстановка описания цены
				$price_description = (!empty($matches[5][$counter])) ? $matches[5][$counter] : "";
				$current_template = str_replace("{price_desription}", $price_description, $price_template);
				
				// Подстановка значения цены
				$current_template = str_replace("{price_value}", "9700", $current_template);
				
				// Замена полного соответствия на шаблон
				$row->text = str_replace($full_match, $current_template, $row->text);

				$counter++;
			}
		}
	}	// End function

} // End class
