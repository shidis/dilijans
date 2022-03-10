$(function($){if(isDefined(window.JSD)){for(var k in JSD){$(k).html(Base64.decode(JSD[k]));}}
if(isDefined(window.JSDW)){for(var k in JSDW){$(k).wrap(Base64.decode(JSDW[k]));}}
$.ajaxSetup({type:'POST',dataType:'json',cache:false,error:Err});$.prettyLoader({theme:'ttt',delay:false,loader:'/app/images/prettyLoader/ajax-loader.gif'});$('input[placeholder]').placeholder();$('.atip, .ntatip').qtip({position:{my:'top left',at:'bottom center'},style:{classes:'qtip-shadow qtip-rounded'},content:{text:function(event,api){$
.ajax({url:$(this).attr('rel'),dataType:'html'})
.done(function(html){api.set('content.text',html)})
.fail(function(xhr,status,error){api.set('content.text',status+': '+error)});return'Загрузка...';}}});$('.nttip').qtip({position:{my:'top left',at:'bottom center'},style:{classes:'qtip-shadow qtip-rounded'}});$('.ntatip, .atip').click(function(){return false;});$("a[rel^='zoom']").prettyPhoto({deeplinking:false,show_title:false,social_tools:''});$('.qbrands').change(function(){if($(this).val()!='')location.href=$(this).val();});$('.search [type=button]').click(function(){$('.search form').submit();});$('#subscribe').submit(subscribe);$('.tsGo').click(function(){var $f=$(this).parents('.tsForm');var empty=true;$f.find('select, [type=checkbox]').each(function(){if(this.nodeName=='SELECT'&&$(this).val()!=''||this.nodeName=='INPUT'&&$(this).prop('checked'))empty=false;});if(!empty)$f.submit();else return false;});$('.dsGo').click(function(){var $f=$(this).parents('.dsForm');var empty=true;$f.find('select, [type=checkbox]').each(function(){if(this.nodeName=='SELECT'&&$(this).val()!=''||(this.nodeName=='INPUT'&&$(this).prop('checked')&&$(this).attr('name')!='ap'))empty=false;});if(!empty)$f.submit();else
{if($f.find('select[name="sv"]').length>0)
{$f.find('select[name="sv"]').parents('.select-01').addClass('error');}
return false;}});$('.tsForm input, .tsForm select').change(tsf1);$('.dsForm input, .dsForm select').change(tsf2);$('.lfGo').click(function(e){$(this).closest('form').submit();return false;});$('#cityId, #rc_cityId').change(function(){setCookie('cityId',$(this).val());});$('#rc-btn').click(function()
{$('#rc-result').html('<img align="center" src="/assets/images/ax/3.gif">&nbsp;&nbsp;&nbsp;Идет расчет...');$.ajax({url:'/ax/regionDelivery',data:{f:$('#rc-form').serialize()},success:function(r){if(r.fres!==false){$('#rc-result').html(r.fres);}else $('#rc-result').html('Расчет не возможен');}});var cityId=$('#rc_cityId').val();if(typeof _gaq!='undefined'){_gaq.push(['_trackEvent','Interface','delveryCostByCity',$('#rc_cityId option[value='+cityId+']').html()]);}
return false;});$('.vids .setLentaMode').click(function(){setCookie('stype','lenta');location.reload();return false;});$('.vids .setBlockMode').click(function(){setCookie('stype','block');location.reload();return false;});$('.vids .active').click(function(){return false;});$('.tsort')
.chosen({disable_search_threshold:10})
.change(function()
{var a=location.href.replace(/[&\?]{1}ord=[0-9\-]*/,'').replace(/&?\??page=[0-9]*/,'');location.href=a+(a.indexOf('?')!=-1?'&':'?')+'ord='+$(this).val();});$('.limits')
.chosen({disable_search_threshold:20})
.change(function()
{var a=location.href.replace(/[&\?]{1}num=[0-9\-]*/,'').replace(/&?\??page=[0-9]*/,'');location.href=a+(a.indexOf('?')!=-1?'&':'?')+'num='+$(this).val();});var navDiametrActive=null;$('.nav-diametr > a').click(function()
{var r=$(this).attr('r');if(typeof r!='undefined'){if(navDiametrActive){$('.table-list tbody.rad__'+navDiametrActive).hide();navDiametrActive=r;$('.table-list tbody.rad__'+navDiametrActive).show(400);}else{navDiametrActive=r;$('.table-list tbody:not(.rad__'+navDiametrActive+')').hide(400);}}else{$('.table-list tbody').show(400);navDiametrActive=r;}
$('.nav-diametr a').removeClass('active');$(this).addClass('active');return false;});$('.alt-brands > a').click(function()
{var bid=$(this).attr('r');$('#alt_brand').val(bid);if(typeof bid!='undefined'){$.ajax({url:window.location.href,data:{bid:bid},success:function(r){if(r.fres){$('.tab-box.analog .replaceable_content').html(r.data);$('.vids .setLentaMode').click(function(){setCookie('stype','lenta');location.reload();return false;});$('.vids .setBlockMode').click(function(){setCookie('stype','block');location.reload();return false;});$('.vids .active').click(function(){return false;});}else{console.log(r);}}});}
$('.alt-brands a').removeClass('active');$(this).addClass('active');return false;});$('#altt-bybrand').click(function()
{$(this).closest('form').submit();});feedbackSubmitInit();singleRunInit();});function singleRunInit()
{if(getCookie('region')===null){$.ajax({url:'/ax/geoCity',success:function(r)
{if(r.fres){var region=0;var _region='';if(r.geo.sx_country_code=='RU'){if(r.geo.sx_region=='Москва'){region=77;_region='Москва';}
else if(r.geo.sx_region=='Московская область'){region=50;_region='Московская область';}
else{region=-7750;_region='Россия без Москвы и области (8-800)';}}
else if((r.geo.sx_country_code+'').length==2){region=-7;_region='Не Россия'}
else{region=-1;_region='Не определен';}
setCookie('region',region,true);if(region==-7750)$('header .phones').addClass('p800');citySelect(r.geo.cityId);if(typeof _gaq!='undefined'){_gaq.push(['_trackEvent','Interface','region',_region,undefined,true]);_gaq.push(['_trackEvent','Interface','geo',r.geo.sx_country_code+' / '+r.geo.sx_region+' / '+r.geo.sx_city+(r.geo.cityId?'*1*':'*0*'),undefined,true]);if(r.geo.sx_city=='')
_gaq.push(['_trackEvent','Interface','geoIPNotResolved',r.geo.ip,undefined,true]);}}else{if(typeof _gaq!='undefined'){_gaq.push(['_trackEvent','Interface','GEOError',r.geo.ip+' * '+r.err_msg,undefined,true]);}}}});}}
function citySelect(cityId)
{if(cityId!=0)$('header #cityId').val(cityId).change();}
function feedbackSubmitInit()
{$('form.feedback').submit(function(){$.ajax({url:'/ax/feedback',data:{f:$(this).serialize()},success:function(r){if(r.fres){$.scrollTo($('div.feedback'),300);$('div.feedback').slideUp('fast',function(){$(this).html('Сообщение отправлено.').slideDown('fast');});}else{$('form.feedback *').removeClass('uncorrect');if(r.err_msg!='')emsg(r);else{$.scrollTo($('div.feedback'),300);for(var k in r.uncorrect){$('form.feedback [for='+k+']').addClass('uncorrect');}}}}});return false;});}
function subscribe()
{$.ajax({url:'/ax/subscribe',data:{email:$('#subscribe [name=email]').val()},success:function(r){if(r.fres){$('#subscribe').html('Подписка прошла успешно.');}else emsg(r);}});return false;}
function tsf1(e)
{mergeFormWithCookie('tsf1',$(e.target).parents('form:eq(0)'));}
function tsf2(e){mergeFormWithCookie('tsf2',$(e.target).parents('form:eq(0)'));$(this).parents('.dsForm').find('.select-01').removeClass('error');}