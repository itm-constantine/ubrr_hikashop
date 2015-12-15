<?php
/**
 * @package	HikaShop payment module for Joomla!
 * @version	1.0.0
 * @author	itmosfera.ru
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');

require(dirname(__FILE__).'/UbrirClass.php');
class plgHikashoppaymentUbrir extends hikashopPaymentPlugin {

	// var $accepted_currencies = array('RUR','RUB','руб');

	var $multiple = true;
	var $name = 'ubrir';
	// var $pluginConfig = array(
	// 	'twpg_id' => array("twpg_id",'input'), //User's identifier on the payment platform
	// 	'twpg_private_pass' => array("twpg_private_pass",'input'), //User's password on the payment platform
	// );

	function __construct(&$subject, $config) {

		parent::__construct($subject, $config);
	}

	function onBeforeOrderCreate(&$order,&$do){
		if(parent::onBeforeOrderCreate($order, $do) === true)
			return true;

		if(empty($this->payment_params->twpg_id)) {
			$this->app->enqueueMessage('Модуль оплаты не настроен');
			$do = false;
		}
	}

	// Здесь должно быть перенаправление в банк
	function onAfterOrderConfirm(&$order,&$methods,$method_id)
	{
		parent::onAfterOrderConfirm($order,$methods,$method_id);
		// echo HIKASHOP_LIVE.'index.php?option=com_hikashop&ctrl=checkout&task=notify&notif_payment='.$this->name;
		// die;
		$amount = $order->cart->full_total->prices[0]->price_value_with_tax;
		$url = JURI::root().'plugins/hikashoppayment/ubrir/catcher.php?id='.$order->order_number;
		$readyToPay = false; // возможность платежа
		$bankHandler = new Ubrir(
		 	array( // инициализируем объект операции в TWPG
				'shopId' => $this->payment_params->twpg_id, 
				'order_id' => $order->order_number,
				'sert' => $this->payment_params->twpg_private_pass,
				'amount' => $amount,
				'approve_url' => $url,
				'cancel_url' => $url,
				'decline_url' => $url
		 	)); 
		$response_order = $bankHandler->prepare_to_pay(); // что вернул банк 
		if(!empty($response_order) & $response_order =='00') {	
			$db = JFactory::getDBO();
			$sql = " INSERT INTO #__twpg_orders  
			(`shoporderid`, `OrderID`, `SessionID`) 
			VALUES  
			('".$order->order_number."', '".$response_order->OrderID[0]."', '".$response_order->SessionID[0]."') ";
			$db->setQuery($sql);
			if(!$db->query()) exit('error_1101'); 
		} else {
			exit($response_order.' error_1102');
		}
		
		$twpg_url = $response_order->URL[0].'?orderid='.$response_order->OrderID[0].'&sessionid='.$response_order->SessionID[0];
		echo '<p>Данный заказ необходимо оплатить одним из методов, приведенных ниже: </p> <INPUT TYPE="button" value="Оплатить Visa" onclick="document.location = \''.$twpg_url.'\'">';
		if ($this->payment_params->two == 1) {
			$id = trim($this->payment_params->uniteller_id);
			$login = trim($this->payment_params->uniteller_login);
			$pass = trim($this->payment_params->uniteller_pass); 
			$orderid =$order->order_number;     
			$amount = round($amount,2);
			$sign = strtoupper(md5(md5($id).'&'.md5($login).'&'.md5($pass).'&'.md5($orderid).'&'.md5($amount)));
			echo '<form action="https://91.208.121.201/estore_listener.php" name="uniteller" method="post" hidden>
			  <input type="number" name="SHOP_ID" value="'.$id.'">
			  <input type="text" name="LOGIN" value="'.$login.'">
			  <input type="text" name="ORDER_ID" value="'.$orderid.'">
			  <input type="number" name="PAY_SUM" value="'.$amount.'">
			  <input type="text" name="VALUE_1" value="'.$orderid.'">
			  <input type="text" name="URL_OK" value="'.JURI::root().'plugins/hikashoppayment/ubrir/catcher.php?status=ok&">
			  <input type="text" name="URL_NO" value="'.JURI::root().'plugins/hikashoppayment/ubrir/catcher.php?status=no&">
			  <input type="text" name="SIGN" value="'.$sign.'">
			  <input type="text" name="LANG" value="RU">
			</form>';
			// die;
			echo '<INPUT TYPE="button" value="Оплатить MasterCard" onclick="document.forms.uniteller.submit()">';
		}
	}

	function onPaymentNotification(&$statuses) {
		$db = JFactory::getDBO();
		$getid = "SELECT `order_id` FROM `#__hikashop_order` WHERE order_number='".htmlspecialchars(JRequest::getVar('order'))."'";
		$db->setQuery($getid);
		if ($db->query()) {
			$res = $db->loadResult();
		}
		// $order_id = (int)($res[]);
		$dbOrder = $this->getOrder($res);
		$this->loadPaymentParams($dbOrder);
		if(empty($this->payment_params))
			return false;
		$this->loadOrderData($dbOrder);

		//TWPG
		// обрабатываем входные данные
		$some_var = JRequest::getVar('xmlmsg');
		if (isset($some_var)) {
			// При CancelURL xmlmsg приходит не шифрованным
			if (stripos(JRequest::getVar("xmlmsg"), "CANCELED")) {
				echo  "<meta charset='utf-8'>";
				echo "<h2>Оплата отменена <a href=".HIKASHOP_LIVE.">вернуться в магазин</a></h2>";
			  die;
			}
			// извлечь статус и передать в чекстатус
			$xml_string = base64_decode(JRequest::getVar('xmlmsg'));
			$parse_it = simplexml_load_string($xml_string);
			// Дергаем статус заказа
			$order_status = $parse_it->OrderStatus[0];
			$sql_resp = "SELECT * FROM `#__twpg_orders` WHERE OrderID=".$parse_it->OrderID[0];
			$db->setQuery($sql_resp);
			$sql_resp =$db->loadRowList();
			if (count($sql_resp) == 1) {
			    $shoporderid = $sql_resp[0][0];
			    $sessionid = $sql_resp[0][2];
			    $orderid = $parse_it->OrderID[0];
			}
			// инициализируем объект операции в TWPG
			$bankHandler = new Ubrir(
			 	array( 
					'shopId' => $this->payment_params->twpg_id, 
					'twpg_order_id' => $parse_it->OrderID[0],
					'twpg_session_id' => $sessionid,
					'sert' => $this->payment_params->twpg_private_pass,
			)); 
			if ($bankHandler->check_status($order_status)) {
				// пишем статус в базу
				// echo $order_status;
				switch ($order_status) {
				  case 'APPROVED':
				    	//действие при удачной оплате
				      $update_status = 'UPDATE `#__hikashop_order` SET `order_status`="confirmed" WHERE `order_number`= "'.trim(JRequest::getVar('order')).'"';
				      $db->setQuery($update_status);
				      if($db->query()){
				      	echo  "<meta charset='utf-8'>";
					      echo "<h2>Оплата произведена <a href=".HIKASHOP_LIVE.">вернуться в магазин</a></h2>";
				      	die;
				      }
				  break;
				  case 'DECLINED':
				  echo  "<meta charset='utf-8'>";
					echo "<h2>Оплата отклонена банком <a href=".HIKASHOP_LIVE.">вернуться в магазин</a></h2>";
					echo $desc = (string)$parse_it->ResponseDescription;
					die; 
				  break;
				}
			}
		// header("Location: http://hikashop.itmdev.ru/");
		die;
		}
		echo  "<meta charset='utf-8'>";
		echo "<h2>Оплата отменена <a href=".HIKASHOP_LIVE.">вернуться в магазин</a></h2>";
		die;
	}
	function getPaymentDefaultValues(&$element) {
		$element->payment_name = 'Ubrir_name';
		$element->payment_description='jfgjgfcgoigjomihgiuhduvgchi';
		$element->payment_images = 'hrhfgnjjjrtwjrtyjtwyj';
		$element->payment_params->invalid_status = 'cancelled';
		$element->payment_params->pending_status = 'created';
		$element->payment_params->verified_status = 'confirmed';
	}
}
?>