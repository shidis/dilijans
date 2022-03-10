<? require_once '../auth.php';
include('../struct.php');

$cp->frm['name']='dc_bot';

$cp->checkPermissions();

cp_head();
cp_css();
cp_js();

cp_body();

$os=new App_Discount();

?><table width="100%" border="0" cellspacing="10" cellpadding="0">
  <tr>
    <td valign="top"><fieldset class="ui"><legend class="ui">Групповые опперации</legend>
<? if(isset($_POST['n1n2'])){
	$ifrm=true;
	$n1n2=$_POST['n1n2'];
	$dcstate=$_POST['state_id'];
	$na=preg_split("\r\n",$_POST['n1n2']);
	$dt=date("Y-m-d H:i:s");
	foreach($na as $k=>$v)if(trim($v)!=''){
		$v=explode(' <--',$v);
		$v=$v[0];
		$v1=preg_split("[^0-9]",trim($v));
		if(count($v1)>2) {$n1n2=str_replace($v,$v.' <-- неформат',$n1n2); $ifrm=false;}
		$v1[0]=Tools::zeroFill($v1[0],Cfg::get('digit_n1_num'));
		@$v1[1]=trim($v1[1]);
		$os->query("SELECT * FROM os_dc WHERE (n1 LIKE '{$v1[0]}'))");
		if($os->qnum()){
			$os->next();
			if($v1[1]==$os->qrow['n2']){
				if($os->qrow['state_id']==2){$n1n2=str_replace($v,$v.' <-- уже активирована клиентом',$n1n2); $ifrm=false;}
				elseif($os->qrow['state_id']==4 && $os->qrow['order_count']){$n1n2=str_replace($v,$v.' <-- анулирована после активации',$n1n2); $ifrm=false;}
				elseif($dcstate==5){
					$os->query("DELETE FROM os_dc WHERE dc_id='{$os->qrow['dc_id']}'");
				}else{
					$os->query("UPDATE os_dc SET dt_state='$dt', state_id='$dcstate', user_id=0 WHERE dc_id='{$os->qrow['dc_id']}'");
				}
			}else{
				$n1n2=str_replace($v,$v.' <-- номер есть. неверный пин',$n1n2); $ifrm=false;
			}
		}elseif($dcstate!=5){
			$os->query("INSERT INTO os_dc (n1,n2,state_id,dt_state,user_id) VALUES('{$v1[0]}','{$v1[1]}','$dcstate','$dt',0)");
		}
	}
	 if($ifrm) echo '<strong>Выполнено</strong>'; else echo '<strong>Выполнено с ошибками</strong>';
	
}?>    
<form method="post" name="ifrm" style="margin:15px;">
        <p>Здесь вы можете ввести номера карт и сразу присвоить им статус. Если карта с вводимым номером существует - происходит изменение ее статуса (если это возможно)</p>
        <p><em><strong>Статус карт:</strong></em><br>  
          <select name="state_id" id="state_id">
            <option value="0">Добавленные</option>
            <option value="1">Доступна для активации</option>
            <option value="3">Анулирована до активации</option>
            <option value="5">Удалить</option>
          </select>
        </p>
        <p>Формат построчно: <strong>номер карты;пин код</strong><br>
          <em>(можно без пин-кода</em>)<br>
          <textarea name="n1n2" id="n1n2" cols="30" rows="15"><?=!@$ifrm?@$n1n2:''?></textarea>
</p>
        <p>
          <input type="submit" value="Добавить / изменить">
        </p></form>
    </fieldset>
    </td>
    <td width="100%" valign="top">
<? if(isset($_GET['dc_state_id'])){
$from=Tools::esc($_GET['from_y']).'.'.Tools::esc($_GET['from_m']).'.'.Tools::esc($_GET['from_d']).' 00:00:00';
$to=Tools::esc($_GET['to_y']).'.'.Tools::esc($_GET['to_m']).'.'.Tools::esc($_GET['to_d']).' 23:59:59';
$sea_dcnum=Tools::esc($_GET['sea_dcnum']);

?>
<div class="ui-widget msg-block"><div class="ui-state-highlight ui-corner-all" style="margin-top: 20px; padding: 0pt 0.7em;"><p><span class="ui-icon ui-icon-info" style="float: left; margin-right: 0.3em;"></span>

<?  if(@$_GET['sea_dcnum']!=''){?><p class="bold">Поиск номера карты <font id="red" class="bold"><?=$_GET['sea_dcnum']?></font> <? }else echo 'Поиск карт ';?>
в интервале <font id="red" class="bold"><?=$_GET['from_d']?>.<?=$_GET['from_m']?>.<?=$_GET['from_y']?> - <?=$_GET['to_d']?>.<?=$_GET['to_m']?>.<?=$_GET['to_y']?></font> с установленным статусом 
<font id="red" class="bold"><? switch ($_GET['dc_state_id']){case '0':echo 'Добавлена';break;case '1':echo 'Доступна для активации';break;case '2':echo 'Активирована';break; case '3': echo 'Анулирована до активации';break;case '4': echo 'Анулирована после активации';break;case '5': echo 'Анулирована по замене карты';break;}?></font>
</p></div></div>

<? $os->query("SELECT * FROM os_dc WHERE (dt_state>='$from')AND(dt_state<='$to')AND(state_id='{$_GET['dc_state_id']}')".($sea_dcnum!=''?"AND(n1 LIKE '{$sea_dcnum}')":''));
if($os->qnum()){?>
<table class="ui-table ltable" width="100%">
<tr><th>#</th><th>Номер карты</th><th>Пин-код</th><th>Дата изменения <br>статуса</th><th>Статус</th></tr>
<? while($os->next()!==false){?>
<tr><td align="center"><?=$os->qrow['dc_id']?></td>
<td align="center"><?=$os->qrow['n1']?></td>
<td align="center">&nbsp;<?=Tools::unesc($os->qrow['n2'])?>&nbsp;</td>
<td align="center"><?=sdate($os->qrow['dt_state'])?></td>
<td align="center"><? switch($os->qrow['state_id']){case '0':echo 'Добавлена';break;case '1':echo 'Доступна для активации';break;case '2':echo 'Активирована';break; case '3': echo 'Анулирована до активации';break;case '4': echo 'Анулирована после активации';}?></td>
</tr>
<? }?>
</table>
<? }?>
<? }?>
    </td>
  </tr>
</table>
<? if(isset($ifrm)) if($ifrm){?><script>window.alert('Операция прошла успешно без ошибок')</script><? }else{?><script>window.alert('Операция завершена. Есть ошибки. Проверьте список')</script><? }?>

<? cp_end()?>
