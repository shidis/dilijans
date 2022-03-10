var CMP={};CMP.data={t:[],d:[]}
CMP.setCookie=function()
{setCookie('cmp',Base64.encode(serialize(this.data)));}
CMP.popupHide=function(e)
{var $e=$(e);$e.find('.close').unbind('click');$e.fadeOut(40,function(){$e.remove();});}
CMP.popupShow=function()
{if($('.compare-popup').length)$('.compare-popup').remove();var el=$('<div class="compare-popup">'+'<h1><a href="#" class="close">закрыть</a>Список сравнения</h1>'+'<div class="wrap ctext"><img src="/assets/images/ax/3.gif"></div>'+'</div>')
.appendTo('body')
.css({'left':this.o.left+22,'top':this.o.top-7})
.fadeIn()
.get(0);$(el).find('.close').click(function(){CMP.popupHide(el);return false;});var $wrap=$(el).find('.wrap');$.ajax({url:compareUrl[this.gr],success:function(r){if(r.fres){$wrap.html(r.html);$(el).find('.delete').click(function(){var cid=$(this).attr('cid')*1;if(_.indexOf(CMP.data[CMP.gr],cid)!=-1){logit(cid);CMP.data[CMP.gr]=_.without(CMP.data[CMP.gr],cid);CMP.setCookie();$(this).parent().fadeOut();$('.compare[cid='+cid+']').prop('checked',false);if(!CMP.data[CMP.gr].length)CMP.popupHide(el);}
return false;});}else $wrap.html(r.err_msg);}});}
CMP.initEvents=function()
{$('.compare').change(function(e){var cid=$(this).attr('cid')*1;CMP.gr=$(this).attr('gr');var chk=$(this).prop('checked');var nopopup=$(this).hasClass('nopopup');if(!cid||!CMP.gr)return _alert('Нет параметров для работы с сравнением');if(CMP.gr==1)CMP.gr='t';else CMP.gr='d';if(chk){if((CMP.data['t'].length+CMP.data['d'].length)>40){showNotification({message:'Уже больше сорока товаров в сравнении. Больше не осилю!',type:"warning"});}
if(_.indexOf(CMP.data[CMP.gr],cid)==-1){CMP.data[CMP.gr].push(cid);CMP.setCookie();}}else{if(_.indexOf(CMP.data[CMP.gr],cid)!=-1){CMP.data[CMP.gr]=_.without(CMP.data[CMP.gr],cid);CMP.setCookie();}}
if(nopopup)return;CMP.o=$(this).offset();CMP.popupShow();});$('.box-compare .del').click(function(){var cid=$(this).attr('cid')*1;if(_.indexOf(CMP.data['t'],cid)!=-1){CMP.data['t']=_.without(CMP.data['t'],cid);CMP.gr='t';}else{if(_.indexOf(CMP.data['d'],cid)!=-1){CMP.data['d']=_.without(CMP.data['d'],cid);CMP.gr='d';}}
CMP.setCookie();$('.box-compare li[cid='+cid+']').fadeOut(function(){if(CMP.data[CMP.gr].length==0){$('#cmp-wrapper').html('<div class="box-no-nal" style="margin: 50px 0 300px">Больше ничего нет.</div>');}});return false;})}
$(function($){CMP.initEvents();});