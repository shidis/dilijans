<?
@define (true_enter,1);
require_once ($_SERVER['DOCUMENT_ROOT'].'/config/init.php');

$cc=new CC_Ctrl();

$cc->query("select cc_brand.name AS bname, cc_brand.alt AS balt, cc_brand.hit_quant, cc_brand.replica, cc_brand.gr, LENGTH(cc_brand.text) AS textLen FROM cc_brand WHERE NOT cc_brand.H AND  NOT cc_brand.LD ORDER BY cc_brand.gr, cc_brand.replica ASC,  cc_brand.hit_quant DESC, cc_brand.name");

?><table border="1" cellpadding="5"><tr><th>�����</th><th>Hit_quant</th><th>&nbsp;</th><th>����� ��������</th></tr><?
while($cc->next()!==false){
	$s0=$s=Tools::unesc($cc->qrow['bname']);
	$s0=preg_replace("/\(.+?\)/iu","",$s0);  // ������� ��� � ��� � �������
	$s0=trim(preg_replace("/[\+\.\-]/iu",' ',$s0));
	?><tr><?
    ?><td><?=$s?></td><?
	?><td><?=$cc->qrow['hit_quant']?></td><?
    ?><td><?
    	if($cc->qrow['gr']==1) echo trim('���� '.$s0);
		elseif($cc->qrow['replica']) echo trim('replica '.$s0);
		else echo trim('����� '.$s0);
		?></td><?
    ?><td><?=$cc->qrow['textLen']?></td><?
	?></tr><?
}

?></table>