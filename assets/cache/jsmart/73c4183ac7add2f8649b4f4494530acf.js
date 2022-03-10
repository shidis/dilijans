Qu=function()
{this.current={frm:null};this.next={frm:null};this.exnum=null;this.ext_filter=false;this.baseUrl='';this.onLoad=false;this.lastNum=null;this.$frm=null;this.lastResult=null;this.groups=[];this.sMode=0;this.chVars=0;this.msgPrefix="Будет найдено размеров:";this.loaderId='#ax-loader1';}
Qu.prototype.makeUrl=function()
{if(this.current.frm!=null)
{if(this.ext_filter)
{if(this.baseUrl.indexOf('?')!=-1)
{var url_parts=this.baseUrl.split('?');return url_parts[0]+'?'+this.current.frm;}
else
{return this.baseUrl+'?'+this.current.frm;}}
else
{return this.baseUrl+(this.baseUrl.indexOf('?')!=-1?'&':'?')+this.current.frm;}}
else{logit('Qu.makeUrl: error! empty form data for query');return'';}}
Qu.prototype.putNewQuery=function()
{if(this.current.frm==null){this.current.frm=this.$frm.serialize();logit('Qu.putNewQuery('+this.$frm.attr('class')+'): current queue is empty => setting it');this.q();}
else{this.next.frm=this.$frm.serialize();logit('Qu.putNewQuery('+this.$frm.attr('class')+': current is bysy => push to queue');}}
Qu.prototype.q=function()
{if(this.exnum!=null&&this.$frm.find('input[type="checkbox"]:checked').length==0&&this.$frm.find('option[value!=""]:selected').length==0&&!this.ext_filter){logit('Qu.q: no checked fields ('+this.$frm.attr('class')+')')
if(this.next.frm!=null){this.current=_.clone(this.next);this.next.frm=null;this.q();return;}else{this.lastResult=null;this.lastNum=this.exnum;this.current.frm=null;this.doForm();return;}}
logit('Qu.q: ajax query ('+this.$frm.attr('class')+')...;');this.$frm.find('.result-label').hide();this.$frm.find('.loader').show().html($(this.loaderId).html());var self=this;$.ajax({dataType:'json',type:'POST',cache:false,url:this.makeUrl(),data:{groups:this.groups,chVars:this.chVars},success:function(r)
{self.lastNum=r.tn;self.lastResult=r.formdata;if(self.deleteUnaval&&!self.udeleted)
{self.udeleted=true;self.deleteUnavail();}},complete:function(r)
{self.$frm.find('.loader').html('').hide();if(self.next.frm!=null){self.current=_.clone(self.next);self.next.frm=null;self.q();}else{self.current.frm=null;self.doForm();}},error:function()
{}});}
Qu.prototype.doForm=function()
{logit('Qu.doForm('+this.$frm.attr('class')+')');if(this.lastNum==0){if(!this.onLoad)this.$frm.find('.result-label').fadeIn('fast').html("<span style=\"color:red\">найдено: 0</span>").show();this.$frm.find('.lfGo').hide();}else{if(!this.onLoad)this.$frm.find('.result-label').fadeIn('fast').html(this.msgPrefix+" <b>"+this.lastNum+"</b> <i></i>").show();this.$frm.find('.lfGo').show();}
if(this.lastResult==null){this.$frm.find('input[type!=button]').prop('disabled',false);this.$frm.find('label').removeClass('unavail');this.$frm.find('.lfGo').hide();this.$frm.find('.lfGo').show();}else if(this.lastNum>0){var self=this;this.$frm.find('[type=checkbox]').each(function()
{var id=this.id;var $label=$(this).parent('label');if(isDefined(self.lastResult[id])&&self.lastResult[id]>0){$label.removeClass('unavail');$(this).prop('disabled',false);}else{if(!isDefined($(this).attr('undisabled')))
{$label.addClass('unavail');$(this).prop('disabled',true);}}});this.$frm.find('option').each(function()
{var id=this.id;if(id){if(isDefined(self.lastResult[id])&&self.lastResult[id]>0){$(this).removeClass('unavail');}else{$(this).addClass('unavail');}}});}
this.onLoad=false;}
Qu.prototype.resetForm=function()
{logit('Qu.resetForm('+this.$frm.attr('class')+')');this.$frm.find('input[type=checkbox]').prop('checked',false);this.$frm.find('select').each(function(){var $sel=$('option[selected=selected]',this);if($sel.length)$(this).val($sel.val()).change();else if($(this).val()!='')$(this).val('').change();});this.$frm.find('label.active').removeClass('active');this.$frm.find('label').removeClass('unavail');this.$frm.find('.result-label').hide();this.$frm.find('option').removeClass('unavail');}
Qu.prototype.restoreForm=function()
{logit('Qu.restoreForm('+this.$frm.attr('class')+')');this.$frm.find('label').removeClass('unavail').each(function()
{if($(this).hasClass('active'))
$(this).children('input').prop('checked',true);else
$(this).children('input').prop('checked',false);});}
Qu.prototype.initGroups=function()
{var self=this;this.$frm.find('[group!=""]').each(function()
{var group=$(this).attr('group');if(typeof group!='undefined')self.groups.push(group);});this.groups=_.uniq(self.groups);}
$(function($)
{if($('.livef').length){var qu=new Qu();qu.$frm=$('.livef');if(isDefined(window.exnum))qu.exnum=window.exnum;if(isDefined(window.ext_filter))qu.ext_filter=true;if(qu.$frm.attr('chVars')==1)qu.chVars=1;qu.initGroups();if(location.href.indexOf('?')!=-1){var a=location.href.split('?');a=a[1].split('&');for(var i=0;i<a.length;i++){if(a[i].substr(0,1)=='_')qu.onLoad=true;}}
if($('.livef').attr('onload_refresh'))
{qu.onLoad=true;}
if(qu.onLoad){logit('liveFilters: onLoad = TRUE');qu.restoreForm();qu.putNewQuery();}else{qu.resetForm();}
$('.livef [type=checkbox], .livef select').change(function()
{logit('liveFilters: click');qu.putNewQuery();});qu.baseUrl=qu.$frm.attr('action');qu.sMode=parseInt(qu.$frm.attr('sMode'));if(qu.sMode)qu.msgPrefix="Будет найдено спарок:";$('<div id="ax-loader1"><img align="center" src="/assets/images/ax/3.gif"></div>').appendTo(qu.$frm);qu.loaderId='#ax-loader1';}
var quCollect=[];var qi=0;$('.liveSB, .liveC').each(function(e)
{quCollect[qi]=new Qu();quCollect[qi].$frm=$(this);if(quCollect[qi].$frm.attr('chVars')==1)quCollect[qi].chVars=1;quCollect[qi].initGroups();quCollect[qi].$frm.find('select, [type=checkbox]').change((function(i)
{return function(e){quCollect[i].putNewQuery();}})(qi));quCollect[qi].baseUrl=quCollect[qi].$frm.attr('action');quCollect[qi].sMode=parseInt(quCollect[qi].$frm.attr('sMode'));if(quCollect[qi].$frm.hasClass('liveSB')){quCollect[qi].msgPrefix="размеров:";}
$('<div style="display: none" id="ax_loader2"><div class="ax_loader2-wrap">считаю...</div> </div>').appendTo(quCollect[qi].$frm);quCollect[qi].loaderId='#ax_loader2';quCollect[qi].$frm.find('select').each(function(){if($(this).val()!='')quCollect[qi].onLoad=true;});quCollect[qi].$frm.find('[type=checkbox]').each(function(){if($(this).prop('checked'))quCollect[qi].onLoad=true;});if(quCollect[qi].onLoad){quCollect[qi].putNewQuery();}
qi++;});$('.black label i').click(function()
{var $e=$(this).siblings('input');if($e.prop('disabled')==true)return;if($e.prop('checked')==false)
$(this).parent().addClass('active');else
$(this).parent().removeClass('active');});var $tmarkir=$('.tmarkir');if($tmarkir.length){var $f=$('.tsForm');var tma=[[$f.find('.pp1'),$('.tmarkir__p1'),$tmarkir.find('.im1')],[$f.find('.pp2'),$('.tmarkir__p2'),$tmarkir.find('.im2')],[$f.find('.pp3'),$('.tmarkir__p3'),$tmarkir.find('.im3')]];(function remake()
{for(var k in tma){if(tma[k][0].val()==''){tma[k][1].fadeIn(400);tma[k][2].removeClass('a1').addClass('a0');}else{tma[k][1].fadeOut(400);tma[k][2].removeClass('a0').addClass('a1');}}})();for(var k in tma){(function(k){tma[k][0].change(function()
{if($(this).val()==''){tma[k][1].fadeIn(400);tma[k][2].removeClass('a1').addClass('a0');}else{tma[k][1].fadeOut(400);tma[k][2].removeClass('a0').addClass('a1');}});})(k);}}});