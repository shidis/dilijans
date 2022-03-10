<? require_once '../auth.php';
include('../struct.php');

$cp->frm['name']='cc_dop';

$cp->checkPermissions();

$brand_id=(int)@$_GET['brand_id'];
if(!$brand_id) die('Неверный параметр');
$cc=new CC_Ctrl;
$cc->que('brand_by_id',$brand_id);
if($cc->qnum()) $cc->next(); else die('Неверный производитель');
$bname=Tools::unesc($cc->qrow['name']);



?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Дополнительная комплектация <?=$bname?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="../css/style.css" rel="stylesheet" type="text/css">
<SCRIPT language=JavaScript src="/cms/js/lib/jshr.js" type=text/javascript></SCRIPT>
<SCRIPT language=JavaScript src="/cms/js/main.js" type=text/javascript></SCRIPT>

<script language="JavaScript">
function doLoad(act, data) {
var req = new JsHttpRequest();
switch (act){
	case 'del': 
    	req.onreadystatechange = function() {
        if (req.readyState == 4) {
			document.getElementById('debug').innerHTML = req.responseJS.debug;
			doLoad('list',{brand_id: '<?=$brand_id?>'});
        }}
		break;
	case 'list': 
		loading('list','');
    	req.onreadystatechange = function() {
        if (req.readyState == 4) {
           	document.getElementById('list').innerHTML = req.responseText;
			document.getElementById('debug').innerHTML = req.responseJS.debug;
        }}
		break;
	case 'save': 
		if(document.getElementById('earea').style.display=='none') saving('save_but','save_loader','');
    	req.onreadystatechange = function() {
        if (req.readyState == 4) {
			document.getElementById('debug').innerHTML = req.responseJS.debug;
			saved('save_but','save_loader',req.responseText);
			if(req.responseText==''){
//				if(req.responseJS.dop_id) document.forms['eform'].dop_id.value=req.responseJS.dop_id;
				if(req.responseJS.edit!=1) document.forms['eform'].reset();
				doLoad('list',{brand_id: '<?=$brand_id?>'});
			}
        }}
		break;
	case 'add': 
		if(document.getElementById('earea').style.display=='none') loading('earea','');
    	req.onreadystatechange = function() {
        if (req.readyState == 4) {
           	document.getElementById('earea').innerHTML = req.responseText;
			document.getElementById('debug').innerHTML = req.responseJS.debug;
			document.getElementById('add_but').style.display='none';
        }}
		break;
	case 'edit': 
		if(document.getElementById('earea').style.display=='none')loading('earea','');
    	req.onreadystatechange = function() {
        if (req.readyState == 4) {
           	document.getElementById('earea').innerHTML = req.responseText;
			document.getElementById('debug').innerHTML = req.responseJS.debug;
			document.getElementById('add_but').style.display='block';
        }}
		break;
	default: window.alert('doLoad('+act+') not defined!');
}
    // Prepare request object (automatically choose GET or POST).
	req.caching = false;
    req.open(null, '../be/dop.php?act='+act, true);
    // Send data to backend.
    req.send( data );
	return false;
}
</script>

</head>
<body>
<h2>Дополнительная комплектация <font id="red"><?=$bname?></font></h2>
<a name="_edit"></a>
<div id="earea"></div>
<fieldset><legend>Список комплектации</legend>
<div id="add_but"><input type="button" value="+ Добавить комплектацию" onClick="doLoad('add',{brand_id:<?=$brand_id?>})"></div>
<div id="list"></div>
</fieldset>
<br><input type="button" value="Закрыть окно" onClick="window.close()">


<div id="debug_on" style=" display:block; padding-top:15px"><a href="javascript:toggle('debug_on');toggle('debug')"><img class="nob" src="../img/folder-open.gif" align="baseline">Debug on</a></div>
<br><br><div id="debug" style="display:none; border:1px dashed red; padding:5px; margin-top:15px"></div>

<script language="javascript">doLoad('list',{brand_id: '<?=$brand_id?>'})</script>
</body>
</html>