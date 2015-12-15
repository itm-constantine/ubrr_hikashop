<?php 
/**
 * @package	HikaShop payment module for Joomla!
 * @version	1.0.0
 * @author	itmosfera.ru
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

if(false) {
	defined('_JEXEC') or die('Restricted access'); 
}
$domen = str_replace('/plugins/hikashoppayment/ubrir/catcher.php', '',$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
if (isset($_POST['xmlmsg'])) {
        echo '<form name="twpg" method="post" action="'.$domenindex.'/index.php?option=com_hikashop&ctrl=checkout&task=notify&notif_payment=ubrir&tmpl=component&lang=ru&order='.htmlspecialchars($_GET['id']).'">
        <input type="hidden" value="'.htmlspecialchars($_POST['xmlmsg']).'" name="xmlmsg" hidden>
        </form>
        <script>document.forms.twpg.submit()</script>';
        die;
}

$desc = fopen('log.txt', 'a+');
fwrite($desc, print_r($_POST, true));
fwrite($desc, "----------------------------");
fclose($desc);
require(dirname(__FILE__).'/UbrirClass.php');
require('../../../configuration.php');
$conf = new JConfig; 
$db_conn = new mysqli($conf->host, $conf->user, $conf->password, $conf->db);
if ((isset($_POST['SIGN']))) {
	$sign = strtoupper(md5(md5($_POST['SHOP_ID']).'&'.md5($_POST["ORDER_ID"]).'&'.md5($_POST['STATE'])));
	if ($_POST['SIGN'] == $sign) {
		switch ($_POST['STATE']) {
			case 'paid':
				if (mysqli_connect_errno()) {
				printf("Ошибка доступа к БД: %s\n", mysqli_connect_error());
			exit();
			}
			$db_conn->query('UPDATE '.$conf->dbprefix.'hikashop_order SET `order_status`="confirmed" WHERE order_number="'.$_POST["ORDER_ID"].'"' );
			break;
			case 'canceled':
				if (mysqli_connect_errno()) {
				printf("Ошибка доступа к БД: %s\n", mysqli_connect_error());
			exit();
			}
			$db_conn->query('UPDATE '.$conf->dbprefix.'hikashop_order SET `order_status`="cancelled" WHERE order_number="'.$_POST["ORDER_ID"].'"' );
			break;
		}
	}  
}

if(isset($_GET['action'])) {
	$action = htmlspecialchars($_GET['action']);
	$twpg_id = htmlspecialchars($_GET['twpg_id']);
	$twpg_pass = htmlspecialchars($_GET['twpg_pass']);
	$order_number = htmlspecialchars($_GET['order_number']);
	if (isset($_GET['uni_pass']) & isset($_GET['uni_login'])) {
		$uni_login = htmlspecialchars($_GET['uni_login']);
		$uni_pass = htmlspecialchars($_GET['uni_pass']);
		$ubrir_uni = new Ubrir (
			Array(
				'uni_login' => $uni_login,
				'uni_pass' => $uni_pass
				));
		echo $ubrir_uni->uni_journal();
		die;
	}
	if ($action == "reconcile" | $action == "journal") {
		$ubrir_twpg = new Ubrir(
			Array(
				'shopId' => $twpg_id,
				'sert' => $twpg_pass
			)
		);
		if ($action == "reconcile") {
			echo $ubrir_twpg-> reconcile();
		}
		if ($action == "journal") {
			echo $ubrir_twpg-> extract_journal();
		}
		die;
	}
	$resp = $db_conn->query('SELECT * FROM `'.$conf->dbprefix.'twpg_orders` WHERE `shoporderid` ="'.$order_number.'"');
	if ($resp->num_rows == 1) {
		$resp_ar = $resp->fetch_assoc();
		$twpg_order = $resp_ar['OrderID'];
		$twpg_ses = $resp_ar['SessionID'];
		$ubrir = new Ubrir (
			Array(
				'shopId' => $twpg_id,
				'sert' => $twpg_pass,
				'twpg_order_id' => $twpg_order,
				'twpg_session_id' => $twpg_ses,
			)
		);
		switch ($action) {
			case 'getstatus':
				echo $ubrir->check_status();
				break;
			case 'getdetailorder':
				echo $ubrir->detailed_status();
				break;
			case 'reverse':
				echo $ubrir->reverse_order();
				break;
			case 'reconcile':
				echo $ubrir->reconcile();
				break;
		}
	} else {
		echo "Неверный номер заказа";
	}
	die;
}

if (isset($_GET['status'])) {
	$status=htmlspecialchars($_GET['status']);
	switch ($status) {
		case 'ok':
			echo '<meta charset="utf-8">';
			echo "<h2>Оплата произведена <a href='/'>вернуться в магазин</a></h2>";
			break;
		
		case 'no':
			echo '<meta charset="utf-8">';
			echo "<h2>Оплата отменена <a href='/'>вернуться в магазин</a></h2>";
			break;
	}
}

if (isset($_POST['mailsubject'])) {
	if($_POST['mailsubject'] == 'Выберите тему') {
	    echo "Не выбрана тема обращения";
	    die;
	  } elseif (empty($_POST['mailem'])) {
	    echo "Не заполнен номер телефона";
	    die;
	  } elseif(empty($_POST['maildesc'])) {
	    echo "Не заполнена причина обращения";
	    die;
	  }
	if (!empty($_POST['mailsubject']) && !empty($_POST['maildesc']) && !empty($_POST['mailem'])) {
		$to = 'ibank@ubrr.ru';
		$subject = htmlspecialchars($_POST['mailsubject'], ENT_QUOTES);
		$message = 'Отправитель: '.htmlspecialchars($_POST['mailem'], ENT_QUOTES).' | '.htmlspecialchars($_POST['maildesc'], ENT_QUOTES);
		$headers = 'From: '.$_SERVER["HTTP_HOST"];
		if(mail($to, $subject, $message, $headers)) {
			echo "Сообщение отправлено";
		} else {
			echo "Сообщение не отправлено";
		}
	}
}
die;
?>