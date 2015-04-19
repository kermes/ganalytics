<?php
/**
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2015 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class GanalyticsAjaxModuleFrontController extends ModuleFrontController
{
	public $ssl = true;
	/*
	 * @see FrontController::initContent()
	 */
	public function initContent()
	{
		parent::initContent();
                $checkorderid = (int)Tools::getValue('checkorderid');
                if ($checkorderid > 0) {
                    $ga_order_sent = Db::getInstance()->getValue('SELECT sent FROM `'._DB_PREFIX_.'ganalytics` WHERE id_order = '.$checkorderid.' AND `lock` = 0 AND id_google_analytics = (select MIN(id_google_analytics) from ps_ganalytics where id_order='.$checkorderid.')',false);
                    
                    if ($ga_order_sent == 0) {
                        $response = array("result"=>"OK","value"=>$ga_order_sent);
                        Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'ganalytics` SET `lock` = 1 WHERE id_order = '.$checkorderid);
                    } else {
                        $sql = 'UPDATE `'._DB_PREFIX_.'ganalytics` SET sent = 1, `lock` = 0 WHERE id_order = '.$checkorderid;
                        Db::getInstance()->execute($sql,false);
                        $response = array("result"=>"KO","value"=>$ga_order_sent);
                    }
                    die(json_encode($response));
                }
		$order = new Order((int)Tools::getValue('orderid'));
		if (!Validate::isLoadedObject($order) || $order->id_customer != $this->context->cookie->id_customer)
			die;
		Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'ganalytics` SET sent = 1,`lock` = 0, date_add = NOW() WHERE id_order = '.(int)Tools::getValue('orderid'));
		die;
	}
}
