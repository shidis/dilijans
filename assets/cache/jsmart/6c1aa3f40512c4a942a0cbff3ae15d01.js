$(document).ready(function()
{$('#cart_frm [name=ptype]').change(function(e)
{if($(this).val()==1){$('#ptype-fiz').hide();$('#ptype-ur').show();}else{$('#ptype-fiz').show();$('#ptype-ur').hide();}});$('.input-basket').each(function()
{var $buttons=$(this).find('a'),$input=$(this).find('input'),maxQty=$input.attr('maxQty'),minQty=$input.attr('minQty'),cat_id=$input.attr('cat_id');$buttons.click(function()
{var val=$input.val();if($(this).index()){$input.val(++val);}else{if(val>1)
$input.val(--val);}
checkLimit($input,$(this),minQty,maxQty);cartChangeAmount(cat_id,$input.val());return false;});$input.change(function()
{if(!/^[1-9][0-9]*$/.test($(this).val()))return true;checkLimit($input,$(this),minQty,maxQty);cartChangeAmount(cat_id,$(this).val());});});$('.input-basket2').each(function()
{var button=$(this).find('a'),$input=$(this).find('input'),maxQty=$input.attr('maxQty'),minQty=$input.attr('minQty');button.click(function()
{var val=$input.val();if($(this).index()){$input.val(++val);}else{if(val>1)
$input.val(--val);}
checkLimit($input,$(this),minQty,maxQty);return false;});$input.change(function()
{if(!/^[1-9][0-9]*$/.test($(this).val()))$(this).val(minQty);checkLimit($input,$(this),minQty,maxQty);});});$('.cart_tbl .btn-del').click(function(e)
{e.preventDefault();cartDel(e);});$('.cart_tbl .am').bind('keyup',function(e)
{cartChangeAmount(e);});$('#cart_frm .btn-reset').click(function(e)
{e.preventDefault();$('#cart_frm input[type!="radio"][type!=button], #cart_frm textarea').val('');});$('.btn-cart-clear').click(function(e)
{e.preventDefault();clearCart(e);});$('.btn-order-send').click(function(e)
{e.preventDefault();orderSend(e);});$('.tocart').click(function()
{var $dataEl=$('[id='+$(this).attr('pid')+']');var cat_id=$dataEl.attr('cid');var maxQty=$dataEl.attr('maxQty')*1;if(typeof $dataEl.attr('value')!='undefined')var am=$dataEl.val()*1;else var am=$dataEl.attr('defQty')*1;if(maxQty==0){var altt=$dataEl.attr('altt');if(altt!=''&&altt!=undefined)
$.confirm({title:'Добавление в корзину товара',message:'Этого товара сейчас нет на складе. Но вы можете посмотреть аналогичные типразмеры других производителей.',buttons:{'закрыть окно':{},'посмотреть аналоги':{action:function()
{location.href=urldecode(altt);}}}});else
$.confirm({title:'Добавление в корзину товара',message:'Этого товара сейчас нет на складе.',buttons:{'закрыть окно':{}}});}else if(am>maxQty){$.confirm({title:'Добавление в корзину товара',message:'Этого товара на складе меньше '+am+' шт. Все равно добавить в корзину?',buttons:{'не добавлять':{},'все равно добавить':{action:function()
{addToCart(cat_id,am);}}}});}else addToCart(cat_id,am);return false;});$('.tocart-d').click(function()
{var id12=this.id;var am1=$('[amid1="'+id12+'"]').val();var am2=$('[amid2="'+id12+'"]').val();var cat_id1=$(this).attr('cid1');var cat_id2=$(this).attr('cid2');$.ajax({url:'/cart/add2',data:{list:[{cat_id:cat_id1,amount:am1},{cat_id:cat_id2,amount:am2}]},success:function(d)
{if(d.fres){$.confirm({title:'Товар в корзине',message:'<p>'+d.fn+'</p><p>Вы можете остаться на этой странице или перейти в корзину для оформления заказа.</p>',buttons:{'Продолжить покупки':{class:'button-grey'},'Оформить заказ':{action:function()
{location.href='/cart.html';}}}});if($('.box-logo .basket i:first').length==0)
{$('.empty_basket_title').remove();$('.basket').prepend('<i>0</i><p><a href="/cart.html">Корзина:</a><b>0 руб.</b></p><a href="/cart.html" class="buy" title="Перейти к оформлению заказа">Оформить<i></i></a>');}
$('.box-logo .basket i:first').html(d.b_count);$('.box-logo .basket b').html(d.bsum);if(typeof _gaq!='undefined'){if(d.gr==1)_gaq.push(['_trackEvent','Cart','addDoubleTyres',d.fn_]);if(d.gr==2)_gaq.push(['_trackEvent','Cart','addDoubleDisks',d.fn_]);}
if(typeof window['yaCounter'+YAM]!='undefined'){window['yaCounter'+YAM].reachGoal("ADD-TO-CART");}}else{if(typeof _gaq!='undefined'){_gaq.push(['_trackEvent','Interface','JSError:btn-buy2',d.err_msg,undefined,true]);}
return emsg(d);}}});return false;});});function addToCart(cat_id,am)
{$.ajax({url:'/cart/add',data:{cat_id:cat_id,amount:am},success:function(d)
{if(d.fres){if(!isDefined(d.exist)){$.confirm({title:'Товар в корзине',message:(d.img1!=''?('<img src="'+d.img1+'" class="fl-l">'):'')+'<p>'+d.fn+'</p><p>Вы можете остаться на этой странице или перейти в корзину для оформления заказа.</p>',buttons:{'Оформить заказ':{action:function()
{location.href='/cart.html';}},'Продолжить покупки':{class:'button-grey'}}});if($('.box-logo .basket i:first').length==0)
{$('.empty_basket_title').remove();$('.basket').prepend('<i>0</i><p><a href="/cart.html">Корзина:</a><b>0 руб.</b></p><a href="/cart.html" class="buy" title="Перейти к оформлению заказа">Оформить<i></i></a>');}
$('.box-logo .basket i:first').html(d.b_count);$('.box-logo .basket b').html(d.bsum);if(typeof _gaq!='undefined'){if(d.gr==1)_gaq.push(['_trackEvent','Cart','addTyre',d.fn_,d.price*1]);if(d.gr==2)_gaq.push(['_trackEvent','Cart','addDisk',d.fn_,d.price*1]);}
if(typeof window['yaCounter'+YAM]!='undefined'){window['yaCounter'+YAM].reachGoal("ADD-TO-CART");}}else $.confirm({title:'Товар уже в корзине',message:(d.img1!=''?('<img src="'+d.img1+'" class="fl-l">'):'')+'<p>'+d.fn+'</p><p>Вы можете остаться на этой странице или перейти в корзину для оформления заказа.</p>',buttons:{'Оформить заказ':{action:function()
{location.href='/cart.html';}},'Продолжить покупки':{class:'button-grey'}}});}else{if(typeof _gaq!='undefined'){_gaq.push(['_trackEvent','Interface','JSError:btn-buy',d.err_msg,undefined,true]);}
return emsg(d);}}})}
function cartChangeAmount(cat_id,am)
{if((am*1)>0){$.ajax({url:'/cart/changeAmount',type:'GET',data:{amount:am,cat_id:cat_id},success:function(d)
{$('#sum_'+cat_id).text(d.itemSum);$('#cartSum').text(d.summa);$('#cartItog').text(d.itog);$('#cartDelivery').text(d.dcost);$('.box-logo .basket p b').text(d.summa);}});}}
function clearCart(e)
{$.ajax({url:'/cart/clear',type:'GET',success:function(d)
{if(d.fres){msg('Корзина очищена.');location.href='/';}else emsg(d);}});}
function orderSend(e)
{$(e.target).attr('disabled','disabled');var self=e.target;if(typeof _gaq!='undefined'){_gaq.push(['_trackEvent','Cart','clickSendButton','clickSendButton;']);}
$('#cart_frm div').removeClass('uncorrect');$.ajax({url:'/cart/send',type:'POST',data:{f:$('#cart_frm form').serialize()},success:function(r)
{if(r.fres){$('.main-cart').html('<div class="ctext">'+r.html+'</div>');$('#cart_frm').slideUp(200);$.scrollTo(0,1000);var d=new Date();var dt=Math.round((d.getTime()-window.TSinited)/1000);if(typeof window['yaCounter'+YAM]!='undefined'){window['yaCounter'+YAM].reachGoal("ORDER-SEND");}
if(typeof _gaq!='undefined'){_gaq.push(['_trackPageview','/OrderSend.event']);_gaq.push(['_trackEvent','Cart','OrderSend',dt+'',undefined,true]);if(r.GA_trans!==false){_gaq.push(['_setCustomVar',r.GA_trans['GA_customVarsSlot'],'customerPType',r.GA_trans['customerPType'],2]);_gaq.push(['_addTrans',r.GA_trans['transId'],'',r.GA_trans['total'],'',r.GA_trans['shipping'],r.GA_trans['city'],'',r.GA_trans['country']]);for(var k in r.GA_trans['items']){var item=['_addItem',r.GA_trans['transId'],r.GA_trans['items'][k]['SKU'],r.GA_trans['items'][k]['name'],r.GA_trans['items'][k]['category'],r.GA_trans['items'][k]['price'],r.GA_trans['items'][k]['quantity']]
_gaq.push(item);}
_gaq.push(['_trackTrans']);}else{_gaq.push(['_trackEvent',r.GA_transErr[0],r.GA_transErr[1],r.GA_transErr[2]]);}}}else{if(r.err_msg!='')emsg(r);else{if(isDefined(r.eid)){$.scrollTo('#'+r.eid,1000);$('#'+r.eid).addClass('uncorrect');}}}},complete:function()
{$(self).removeAttr('disabled');}});}
function cartDel(e)
{var cat_id=$(e.target).attr('cat_id');var td=$(e.target).parent();var tr=$(e.target).parent().parent();tr.children('td').css('background','#F00');$(e.target).attr('disabled','disabled');var self=$(this);$.ajax({url:'/cart/del',type:'GET',data:{cat_id:$(e.target).attr('cat_id')},success:function(d)
{if(d.fres){$('#cartSum').text(d.summa);$('#cartDelivery').text(d.dcost);$('#cartItog').text(d.itog);$('.box-logo .basket p b').text(d.summa);$('.box-logo .basket > i').text(d.count);tr.fadeOut('slow',function()
{tr.remove();if($(".cart_tbl tr").length==0){$('.box-logo .basket').html('');$('.basket').prepend('<div class="empty_basket_title">Корзина<br> пуста</div>');}});}else{emsg(d);tr.children('td').css('background','white');}}});}
function checkLimit($input,$target,minQty,maxQty)
{if(($input.val()*1)>maxQty){$input.val(maxQty);$input.qtip({content:'Слишком много. Такого количества нет на складе',show:true,hide:'mouseout',position:{my:'top left',at:'bottom right',target:$target},style:{classes:'qtip-shadow qtip-rounded'}});}else if(($input.val()*1)<minQty){$input.val(minQty);$input.qtip({content:'К сожалению, этот товар продается от '+minQty+' штук',show:true,hide:'mouseout',position:{my:'top right',at:'bottom left',target:$target},style:{classes:'qtip-shadow qtip-rounded'}});}else{$input.qtip('disable');}}