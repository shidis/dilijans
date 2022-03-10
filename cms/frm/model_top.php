<? require_once '../auth.php'?>
<?
$cp->frm['name']='models_top';
$cp->checkPermissions();

require_once($_SERVER['DOCUMENT_ROOT'].'/cms/inc/global.php');
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <title></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link rel="stylesheet" type="text/css" media="screen" href="/cms/themes/redmond/ui-custom.css" />
    <link rel="stylesheet" type="text/css" media="screen" title="basic" href="/cms/css/ui.css" />
    <SCRIPT language=JavaScript src="/assets/js/jquery.min.js" type=text/javascript></SCRIPT>
    <SCRIPT language=JavaScript src="/cms/js/ax_global.js" type=text/javascript></SCRIPT>
    <script type="text/javascript" src="/cms/js/lib/jquery-ui.custom.min.js"></script>
    <SCRIPT language=JavaScript src="/assets/js/func.lib.js" type=text/javascript></SCRIPT>
    <SCRIPT language=JavaScript src="/cms/js/jquery.ext.js" type=text/javascript></SCRIPT>
    <SCRIPT language=JavaScript src="/cms/js/jquery.ui.ext.js" type=text/javascript></SCRIPT>
    <SCRIPT language=JavaScript src="/cms/js/lib/jquery.placeholder.js" type=text/javascript></SCRIPT>
    <SCRIPT language=JavaScript src="/cms/js/ui.js" type=text/javascript></SCRIPT>
    <script type="text/javascript"></script>

    <style type="text/css">
        body{
            color:#FFF;
            margin:0;
            border: 1px;
            background-color:#88B6D9;
        }
        OPTION.isH{background:#ccc}
    </style>

    <script type="text/javascript">

        $(document).ready(function(){

            var form1=$('[name=form1]').get(0);

            $('input[type=text]').placeholder();

            var loader=$('body').cloader();


            $('[name=brand_id], [name=sup_id]').change(function(e){
                form1.action='model_bot.php';
                form1.target='model_bot';
                if($('[name=brand_id]').val()!='') form1.submit();
            });

            $('[name=lines],[name=tagWork],[name=noImg],[name=p1]').change(function(e){
                form1.action='model_bot.php';
                form1.target='model_bot';
                if($('[name=brand_id]').val()!='' || $('[name=q]').val()!='' || $('#inDSonly').get(0).checked || $('#mczero').get(0).checked || $('#mcNOTzero').get(0).checked || $('[name=lines]').val()>0) form1.submit();
            });

            $('[type=submit]').click(function(e){
                form1.action='model_bot.php';
                form1.target='model_bot';
                if($('[name=brand_id]').val()!='' ||
                    $('[name=sup_id]').val()!=='' ||
                    $('[name=q]').val()!='' ||
                    $('[name=p1]').length>0 && $('[name=p1]').val()!='' ||
                    $('#mczero').get(0).checked ||
                    $('#show_only_h').get(0).checked ||
                    $('#mcNOTzero').get(0).checked ||
                    $('#noImg').get(0).checked ||
                    $('[name=lines]').val()>0 ||
                    $('[name=mspez_id]').val()!=''
                )
                    form1.submit();

                else e.preventDefault();
            });

        });
    </script>

</head>

<!--<a href="#" onClick="parent.document.getElementsByTagName('frameset')[0].rows='200,*';return false">расширенный</a>-->

<body style="padding: 0; margin: 0" >
<?
$gr=@$_GET['gr'];
if($gr!=1 && $gr!=2) {warn('gr incorrect. exit.'); exit();}

$cc=new CC_Ctrl();

if(!empty(Cfg::$config['dbsync'])){
    $sync=Cfg::$config['dbsync'];
    $rdb=new DB();
    $rdb->set_db($sync['remote_sql_host'],$sync['remote_sql_db'],$sync['remote_sql_user'],$sync['remote_sql_pass']);
    if(!$rdb->sql_connect()) unset($sync);
}

$cc->load_mspez($gr);

?>
<form name="form1" method="get">
    <input type="hidden" name="gr" value="<?=$gr?>">
    <table  border="0" cellspacing="0" cellpadding="5">
        <tr>
            <td><select name="brand_id" id="brand_id">
                    <option value="">Бренд</option>
                    <?

                    $cc->query("SELECT cc_brand.*, (cc_brand.sup_id DIV cc_brand.sup_id) as supdiv FROM cc_brand WHERE cc_brand.gr='$gr' AND NOT cc_brand.LD ORDER BY cc_brand.replica ASC, supdiv ASC,  cc_brand.name ASC");
                    $r=-1;
                    $sd=-1;
                    while($cc->next()!=false){
                    if($sd!=$cc->qrow['supdiv'] && $cc->qrow['supdiv']){
                    $sd=1;
                    ?><optgroup label="Бренд-реплика"><?
                }
                if($sd && !$cc->qrow['supdiv']){
                    $sd=0;
                    ?></optgroup><?
                }
                if(!$sd && $r!=$cc->qrow['replica']){
                $r=$cc->qrow['replica'];
                if($r==1){?><optgroup label="Replica"><? }
                        }

                        ?><option <?= ($cc->qrow['H']!=0?'class="isH"':'') ?> value="<?=$cc->qrow['brand_id']?>" <?=@$_GET['brand_id']==$cc->qrow['brand_id']?'selected':''?>><?=Tools::unesc($cc->qrow['name'])?></option><?
                        }
                        if($r==1){?></optgroup><? }
                ?></select></td><?

            if($gr==1){
                ?><td><?
                ?><select name="p1"><?
                    ?><option value="">сезон</option><?
                    ?><option value="1"<?=@$_REQUEST['p1']==1?' selected="selected"':''?>>лето</option><?
                    ?><option value="2"<?=@$_REQUEST['p1']==2?' selected="selected"':''?>>зима</option><?
                    ?><option value="3"<?=@$_REQUEST['p1']==3?' selected="selected"':''?>>вссезон</option><?
                    ?></select><?
                ?></td><?
	        } else {
                ?><td><?
                $cc->load_sup(2); $cc->sup_arr[0]='без поставщика';
                ?><select name="sup_id">
                <option value="">реплика</option><?
                foreach($cc->sup_arr as $k=>$v){
                    ?><option value="<?=$k?>"><?=$v?></option><?
                }?></select><?
                ?></td><?
            }?>

            <td>
                <input type="checkbox" name="noImg" value="1" id="noImg"<?=@$_GET['noImg']?' checked':''?>><label for="noImg">без фото</label><br>
                <? if(@Cfg::$config['ccTags']['enabled']){?><input type="checkbox" name="tagWork" value="1" id="tagWork"><label for="tagWork">теги</label><? }?>
            </td>
            <td nowrap >
                <input type="checkbox" name="hide_h" value="1" id="hide_h"<?=@$_GET['hide_h']?' checked':''?>><label for="hide_h">Не показывать скрытые модели</label><br>
                <input type="checkbox" name="show_only_h" value="1" id="show_only_h"<?=@$_GET['show_only_h']?' checked':''?>><label for="show_only_h">Показать только скрытые модели</label>
            </td>
            <td nowrap >
                <input type="checkbox" name="mczero" value="1" id="mczero"<?=@$_GET['mczero']?' checked':''?>><label for="mczero">модели без типоразмеров</label><br>
                <input type="checkbox" name="mcNOTzero" value="1" id="mcNOTzero"<?=@$_GET['mcNOTzero']?' checked':''?>><label for="mcNOTzero">модели <strong>с</strong> типоразмерами</label>
            </td>
            <?if ($gr == 2):?>
            <td nowrap >
                <input type="checkbox" name="no_stickers" value="1" id="no_stickers"<?=@$_GET['no_stickers']?' checked':''?>><label for="no_stickers">модели без стикеров</label><br>
                <input type="checkbox" name="show_stickers" value="1" id="show_stickers"<?=@$_GET['show_stickers']?' checked':''?>><label for="show_stickers">модели со стикерами</label><br>
            </td>
            <?endif;?>
            <td nowrap valign="top">
                <input type="checkbox" name="has_video" value="1" id="has_video"<?=@$_GET['has_video']?' checked':''?>><label for="has_video">с видео</label><br>
                <?if ($gr == 2):?><input type="checkbox" name="priority" value="1" id="priority"<?=@$_GET['priority']?' checked':''?>><label for="priority">с приоритетом</label><br><?endif;?>
            </td>
        </tr>
    </table>
    <table border="0" cellspacing="0" cellpadding="5">
        <tr>
            <td><input style="width: 230px" type="text" value="<?=@$_REQUEST['q']?>" name="q" placeholder="поиск по бренду и модели"></td>
            <? if(!empty($cc->mspez_arr)){?>
                <td><select name="mspez_id">
                    <option value="">доп. параметр</option>
                    <? foreach($cc->mspez_arr as $k=>$v){?>
                        <option value="<?=$k?>"><?=$v?></option>
                        <? }?>
                </select> </td>
            <? }?>
            <td><input type="submit" value="&nbsp;&nbsp;&nbsp;Искать&nbsp;&nbsp;&nbsp;"></td>
            <td nowrap >строк:
                <select name="lines">
                    <option value="">все</option>
                    <option value="3">3</option>
                    <option value="5">5</option>
                    <option value="8">8</option>
                    <option value="10">10</option>
                    <option value="15">15</option>
                    <option value="20">20</option>
                    <option value="25">25</option>
                    <option value="30">30</option>
                    <option value="35">35</option>
                    <option value="50">50</option>
                    <option value="70">70</option>
                    <option value="90">90</option>
                    <option value="110">110</option>
                    <option value="130">130</option>
                    <option value="150">150</option>
                    <option value="170">170</option>
                </select>
                <input name="inDSonly" id="inDSonly" value="1" type="checkbox"<?=@$_GET['inDSonly']?' checked':''?><?=!@$_GET['dataset_id']?' style="display:none"':''?>><label<?=!@$_GET['dataset_id']?' style="display:none"':''?> for="inDSonly">только набор</label>
                <? if(!empty($sync)){?><input type="checkbox" name="dop" value="syncGetParams" id="dop"<?=@$_GET['dop']?' checked':''?>><label for="dop">sync: сверка</label><? }?>
            </td>
        </tr>
    </table>
</form>
</body>
</html>