<?php
/**
 * @package	HikaShop for Joomla!
 * @version	2.5.0
 * @author	hikashop.com
 * @copyright	(C) 2010-2015 HIKARI SOFTWARE. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access'); 
?>
<?php 
    $create = "CREATE TABLE IF NOT EXISTS `#__twpg_orders`(`shoporderid` VARCHAR(11) NOT NULL, `OrderID` int(11) NOT NULL,`SessionID` VARCHAR(40) NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8";
    $db = JFactory::getDbo();
    $db->setQuery($create);
    if ($db->query()) {
    	echo "<h3>Первичная настройка выполнена</h3>";
    } else {
    	echo "<h3>Ошибка при первоначальной настройке</h3>";
    }
?>
<tr>
	<td class="key">
		<label for="data[payment][payment_params][two]"><?php
			echo 'Два процессинга';
		?></label>
	</td>
	<td>
		<?php echo JHTML::_('hikaselect.booleanlist', "data[payment][payment_params][two]", '', @$this->element->payment_params->two); ?>
	</td>
</tr>
<tr>
	<td>Настройки VISA</td>
</tr>
<tr>
	<td class="key">
		<label for="data[payment][payment_params][twpg_id]"><?php
			echo 'ID интернет-магазина для VISA';
		?></label>
	</td>
	<td>
		<input type="text" name="data[payment][payment_params][twpg_id]" value="<?php echo $this->escape(@$this->element->payment_params->twpg_id); ?>" />
	</td>
</tr>
<tr>
	<td class="key">
		<label for="data[payment][payment_params][twpg_private_pass]"><?php
			echo 'Пароль к сертификату VISA';
		?></label>
	</td>
	<td>
		<input type="text" name="data[payment][payment_params][twpg_private_pass]" value="<?php echo $this->escape(@$this->element->payment_params->twpg_private_pass); ?>" />
	</td>
</tr>
<tr><td></td>
<td class="requests">
		<input style="clear:both;float:left;" id="order_number" type="text" name="order_status" placeholder="# заказа" />
		<input style="clear:both;float:left;" id="getorder" type="button" value="Запросить статус заказа">
		<input style="clear:both;float:left;" id="getdetailorder" type="button" value="Информация о заказе">
		<input style="clear:both;float:left;" id="reverse" type="button" value="Отмена заказа">
		<input style="clear:both;float:left;" id="reconcile" type="button" value="Сверка итогов">
		<input style="clear:both;float:left;" id="journal" type="button" value="Журнал операций Visa">
</td>
</tr>
<tr>
	<td >Настройки MasterCard</td>
</tr>
<tr>
	<td class="key">
		<label>
			<p>Обработчик ответов ПЦ</p>
		</label>
	</td>
	<td>
		<p style="color:red;font-weight:600">
			<?php echo $_SERVER['HTTP_HOST']."/plugins/hikashoppayment/ubrir/catcher.php"; ?>	
		</p>
	</td>
</tr>
<tr>
	<td class="key">
		<label for="data[payment][payment_params][uniteller_id]">
			<p>ID интернет-магазина для MasterCard</p>
		</label>
	</td>
	<td>
		<input type="text" name="data[payment][payment_params][uniteller_id]" value="<?php echo $this->escape(@$this->element->payment_params->uniteller_id); ?>" />
	</td>
</tr>
<tr>
	<td class="key">
		<label for="data[payment][payment_params][uniteller_login]">
			<p>Логин личного кабинета MasterCard</p>
		</label>
	</td>
	<td>
		<input type="text" name="data[payment][payment_params][uniteller_login]" value="<?php echo $this->escape(@$this->element->payment_params->uniteller_login); ?>" />
	</td>
</tr>
<tr>
	<td class="key">
		<label for="data[payment][payment_params][uniteller_pass]">
			<p>Пароль интернет-магазина для MasterCard</p>
		</label>
	</td>
	<td>
		<input type="text" name="data[payment][payment_params][uniteller_pass]" value="<?php echo $this->escape(@$this->element->payment_params->uniteller_pass); ?>" />
	</td>
</tr>
<tr>
	<td class="key">
		<label for="data[payment][payment_params][uniteller_user_pass]">
			<p>Пароль личного кабинета MasterCard</p>
		</label>
	</td>
	<td>
		<input type="text" name="data[payment][payment_params][uniteller_user_pass]" value="<?php echo $this->escape(@$this->element->payment_params->uniteller_user_pass); ?>" />
	</td>
</tr>
<tr>
<td></td><td>
	<input style="clear:both;float:left;" id="journal_uni" type="button" value="Журнал операций MasterCard"></tr>
</td>
</tr>
<tr>
	<div id="callback" class="hide">
	  <table>
	    <tr>
	      <h2 onclick="show(this);" style="cursor:pointer;">Обратная связь</h2>
	    </tr>
	    <tr>
         <td>Тема</td>
            <td>
            <select name="subject" id="mailsubject" style="width:150px">
              <option selected disabled>Выберите тему</option>
              <option value="Подключение услуги">Подключение услуги</option>
              <option value="Продление Сертификата">Продление Сертификата</option>
              <option value="Технические вопросы">Технические вопросы</option>
              <option value="Юридические вопросы">Юридические вопросы</option>
			  <option value="Бухгалтерия">Бухгалтерия</option>
              <option value="Другое">Другое</option>
            </select>
            </td>
          </tr>
		  <tr>
 <td>Телефон</td>
 <td>
 <input type="text" name="email" id="mailem" style="width:150px">
 </td>
 </tr>
	    <td>Сообщение</td>
	      <td>
	        <textarea name="maildesc" id="maildesc" cols="30" rows="10" style="width:150px;resize:none;"></textarea>
	      </td>
	    </tr>
	    <tr><td></td>
	      <td><input id="sendmail" type="button" name="sendmail" value="Отправить">
	    </tr>
		<tr>
			</tr>
			<tr><td></td><td id="mailresponse"></td><td>8 (800) 1000-200</td></tr>
	  </table>
	</div>
</tr>
<script>


    // // Обработчик кнопки статус заказа
    // function fn_send_mail() {
    //     var ajax = new XMLHttpRequest();
    //     ajax.open("POST", url+"&payment=ubrir");
    //     ajax.send(formData);
    //     ajax.onreadystatechange = function() {
    //     if (ajax.readyState == 4) {
    //         console.log(ajax.responseText);
    //         $('#ajaxresponse').html(ajax.responseText);
    //         $('#maildesc').val(null);
    //         $('#mailsubject').val(null);
    //         $('#mailem').val(null);
    //     }
    //   }
    // }

	$("sendmail").addEventListener('click', function(){
		var mailsubject = $('mailsubject').value;
		var maildesc = $('maildesc').value;
		var mailem = $('mailem').value;
		var formData = new FormData();
		formData.append("mailsubject", mailsubject);
		formData.append("maildesc", maildesc);
		formData.append("mailem", mailem);
		ajax = new XMLHttpRequest();
		ajax.open('POST', "/plugins/hikashoppayment/ubrir/catcher.php", true);
		ajax.onreadystatechange = function() {
			if (ajax.readyState == 4) {
					$('mailresponse').innerHTML = ajax.responseText;
				}
			}
        		ajax.send(formData);
	});
	// Обработчик кнопки статус заказа
	$("getorder").addEventListener('click', function(){
	  var order_number = $('order_number').value;
	  twpg_id = document.getElementsByName('data[payment][payment_params][twpg_id]')[0].value;
	  twpg_pass = document.getElementsByName('data[payment][payment_params][twpg_private_pass]')[0].value;
	  ajax = new XMLHttpRequest();
	  ajax.open('GET', "/plugins/hikashoppayment/ubrir/catcher.php?"+"action=getstatus&twpg_id="+twpg_id+"&twpg_pass="+twpg_pass+"&order_number="+order_number, true);
	  ajax.onreadystatechange = function() {
	  	if (ajax.readyState == 4) {
			$('system-message-container').innerHTML = ajax.responseText;
		}
	  }
	  ajax.send(null);
	});
	// Обработчик кнопки детальная информация
	$("getdetailorder").addEventListener('click', function(){
	  var order_number = $('order_number').value;
	  twpg_id = document.getElementsByName('data[payment][payment_params][twpg_id]')[0].value;
	  twpg_pass = document.getElementsByName('data[payment][payment_params][twpg_private_pass]')[0].value;
	  ajax = new XMLHttpRequest();
	  ajax.open('GET', "/plugins/hikashoppayment/ubrir/catcher.php?"+"action=getdetailorder&twpg_id="+twpg_id+"&twpg_pass="+twpg_pass+"&order_number="+order_number, true);
	  ajax.onreadystatechange = function() {
	  	if (ajax.readyState == 4) {
			$('system-message-container').innerHTML = ajax.responseText;
		}
	  }
	  ajax.send(null);
	});
	// Обработчик кнопки реверс
	$("reverse").addEventListener('click', function(){
	  var order_number = $('order_number').value;
	  twpg_id = document.getElementsByName('data[payment][payment_params][twpg_id]')[0].value;
	  twpg_pass = document.getElementsByName('data[payment][payment_params][twpg_private_pass]')[0].value;
	  ajax = new XMLHttpRequest();
	  ajax.open('GET', "/plugins/hikashoppayment/ubrir/catcher.php?"+"action=reverse&twpg_id="+twpg_id+"&twpg_pass="+twpg_pass+"&order_number="+order_number, true);
	  ajax.onreadystatechange = function() {
	  	if (ajax.readyState == 4) {
			$('system-message-container').innerHTML = ajax.responseText;
		}
	  }
	  ajax.send(null);
	});
	// Обработчик кнопки сверка итогов
	$("reconcile").addEventListener('click', function(){
	  var order_number = $('order_number').value;
	  twpg_id = document.getElementsByName('data[payment][payment_params][twpg_id]')[0].value;
	  twpg_pass = document.getElementsByName('data[payment][payment_params][twpg_private_pass]')[0].value;
	  ajax = new XMLHttpRequest();
	  ajax.open('GET', "/plugins/hikashoppayment/ubrir/catcher.php?"+"action=reconcile&twpg_id="+twpg_id+"&twpg_pass="+twpg_pass+"&order_number="+order_number, true);
	  ajax.onreadystatechange = function() {
	  	if (ajax.readyState == 4) {
			$('system-message-container').innerHTML = ajax.responseText;
		}
	  }
	  ajax.send(null);
	});
	// Обработчик кнопки сверка итогов
	$("journal").addEventListener('click', function(){
	  var order_number = $('order_number').value;
	  twpg_id = document.getElementsByName('data[payment][payment_params][twpg_id]')[0].value;
	  twpg_pass = document.getElementsByName('data[payment][payment_params][twpg_private_pass]')[0].value;
	  ajax = new XMLHttpRequest();
	  ajax.open('GET', "/plugins/hikashoppayment/ubrir/catcher.php?"+"action=journal&twpg_id="+twpg_id+"&twpg_pass="+twpg_pass+"&order_number="+order_number, true);
	  ajax.onreadystatechange = function() {
	  	if (ajax.readyState == 4) {
			$('system-message-container').innerHTML = ajax.responseText;
		}
	  }
	  ajax.send(null);
	});
	// Обработчик кнопки журнал юнителлер
	$("journal_uni").addEventListener('click', function(){
	  var order_number = $('order_number').value;
	  twpg_id = document.getElementsByName('data[payment][payment_params][twpg_id]')[0].value;
	  twpg_pass = document.getElementsByName('data[payment][payment_params][twpg_private_pass]')[0].value;
	  uni_pass = document.getElementsByName('data[payment][payment_params][uniteller_user_pass]')[0].value;
	  uni_login = document.getElementsByName('data[payment][payment_params][uniteller_login]')[0].value;
	  ajax = new XMLHttpRequest();
	  ajax.open('GET', "/plugins/hikashoppayment/ubrir/catcher.php?"+"action=journal_uni&twpg_id="+twpg_id+"&twpg_pass="+twpg_pass+"&order_number="+order_number+"&uni_login="+uni_login+"&uni_pass="+uni_pass, true);
	  ajax.onreadystatechange = function() {
	  	if (ajax.readyState == 4) {
			$('system-message-container').innerHTML = ajax.responseText;
		}
	  }
	  ajax.send(null);
	});
</script>