<? 
$ab=new CC_AB();
$ab->getTree(array());
?>
<div class="row-ap">
<form id="amark_frm" method="get" action="/podbor_sd.html">

<div class="i">
<select id="mark">
<option value="">Марка авто</option>
  <? if(isset($ab->tree['vendors'])){
  foreach($ab->tree['vendors'] as $k=>$v){?>
  <option value="<?=$v['sname']?>" <?=$abCookie['svendor']==$v['sname']?'selected':''?>><?=Tools::html($v['name'],false)?></option><? }
  }?>
</select>
</div>
<div class="i">
<select name="model" id="model">
        <option value="">Модель авто</option>
			<? if(isset($ab->tree['models'])){
            foreach($ab->tree['models'] as $k=>$v){?>
            <option value="<?=$v['sname']?>" <?=$abCookie['smodel']==$v['sname']?'selected':''?>><?=($v['name'])?></option><? }
            }?>
      </select>
</div>
<div class="i">     
      <select name="year" id="year"><option value="">Год выпуска</option>
			<? if(isset($ab->tree['years'])){
            foreach($ab->tree['years'] as $k=>$v){?>
            <option value="<?=$v['sname']?>" <?=$abCookie['syear']==$v['sname']?'selected':''?>><?=($v['name'])?></option><? }
            }?>
      </select>
 </div>
 <div class="i">     
      <select id="modif" name="modif">
        <option value="">Двигатель</option>
			<? if(isset($ab->tree['modifs'])){
            foreach($ab->tree['modifs'] as $k=>$v){?>
            <option value="<?=$v['sname']?>" <?=$abCookie['smodif']==$v['sname']?'selected':''?>><?=($v['name'])?></option><? }
            }?>
      </select>
</div>    

<input id="amark_but" style="display:none" type="image" src="/images/but_search1.gif" alt="Искать" width="93" height="22"  />
</form>
</div>