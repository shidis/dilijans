Qu = function ()
{
    this.current = {frm: null};  // запрос который выполняется сейчас
    this.next = {frm: null};  // следующий запрос
    this.exnum = null; // кол-во размеров без применения фильтра
    this.ext_filter = false; // флаг фильтра подбора дисков по марке авто
    this.baseUrl = ''; // урл без уточняющих парметров.
    this.onLoad = false;  // первый обсчет при загрузке страницы
    this.lastNum = null;   // кол-во результатов с учетом уточнения
    this.$frm = null;  //  $(форма) фильтра
    this.lastResult = null;  // последние возвращенные данные для формы
    this.groups = [];  // группы параметров (_at, _bids, etc...)
    this.sMode = 0;  // режим спарок
    this.chVars = 0; // подменять парметры на сервере (ex. sv => _sv)
    this.msgPrefix="Будет найдено размеров:";
    this.loaderId='#ax-loader1';
   /* this.udeleted = false;
    this.deleteUnaval = false;*/
}

Qu.prototype.makeUrl = function ()
{
    if (this.current.frm != null)
    {      
        if (this.ext_filter) 
        {
            if (this.baseUrl.indexOf('?') != -1)
            {
                var url_parts = this.baseUrl.split('?');
                return url_parts[0] + '?' + this.current.frm;
            }
            else 
            {
                return this.baseUrl + '?' + this.current.frm;
            }
        }
        else
        {
            return this.baseUrl + (this.baseUrl.indexOf('?') != -1 ? '&' : '?') + this.current.frm;
        }
    }
    else {
        logit('Qu.makeUrl: error! empty form data for query');
        return '';
    }
}

// отправляет форму в очередь запросов
Qu.prototype.putNewQuery = function ()
{
    if (this.current.frm == null) {   // очередь пуста
        this.current.frm = this.$frm.serialize();
        logit('Qu.putNewQuery('+this.$frm.attr('class')+'): current queue is empty => setting it');
        this.q(); // выполняем запрос
    }
    else {
        // запрос уже выпоняется, ставим в очередь
        this.next.frm = this.$frm.serialize();
        logit('Qu.putNewQuery('+this.$frm.attr('class')+': current is bysy => push to queue');
    }

}

// выполняет запрос в current и по завершению проверяет next, если next есть, то next станет current и вызывает саму себя с новым current
Qu.prototype.q = function ()
{
    // снчала проверим может ни одна галочка не стоит, тогда нет смысла делать запрос
    if (this.exnum != null && this.$frm.find('input[type="checkbox"]:checked').length == 0 && this.$frm.find('option[value!=""]:selected').length == 0 && !this.ext_filter && !this.onLoad) {
        logit('Qu.q: no checked fields ('+this.$frm.attr('class')+')')
        // и проверяем очередь
        if (this.next.frm != null) {
            // есть в очереди, перемещаем данные из некст в каррент
            this.current = _.clone(this.next);
            this.next.frm = null;
            this.q();
            return;
        } else {
            this.lastResult = null;  // == null делаем всегда когда небыло запроса а форму надо пересчитать
            this.lastNum = this.exnum;
            this.current.frm = null;
            this.doForm();
            return;
        }
    }
    logit('Qu.q: ajax query ('+this.$frm.attr('class')+')...;');

    this.$frm.find('.result-label').hide();
    this.$frm.find('.loader').show().html($(this.loaderId).html());

    var self = this;
    $.ajax({
        dataType: 'json',
        type: 'POST',
        cache: false,
        url: this.makeUrl(),
        data: {
            groups: this.groups,
            chVars: this.chVars
        },
        success: function (r)
        {
            self.lastNum = r.tn;
            self.lastResult = r.formdata;
            if (self.deleteUnaval && !self.udeleted)
            {
                self.udeleted = true;
                self.deleteUnavail();
            }
        },
        complete: function (r)
        {        
            self.$frm.find('.loader').html('').hide();
            if (self.next.frm != null) {
                // есть в очереди, перемещаем данные из некст в каррент
                self.current = _.clone(self.next);
                self.next.frm = null;
                self.q();
            } else {
                self.current.frm = null;
                self.doForm();
            }
        },
        error: function ()
        {
        }
    });
}


Qu.prototype.doForm = function ()
{
    // заполняем форму фильтра статусами

    logit('Qu.doForm('+this.$frm.attr('class')+')');

    if (this.lastNum == 0) {
        if(!this.onLoad) this.$frm.find('.result-label').fadeIn('fast').html("<span style=\"color:red\">найдено: 0</span>").show();
        this.$frm.find('.lfGo').show();
    } else {

        if(!this.onLoad) {
			this.$frm.find('.result-label').fadeIn('fast').html(this.msgPrefix+" <b>" + this.lastNum + "</b> <i></i>").show()
			this.$frm.find('.mobile-result-label__value b').text(this.lastNum);
		};

        this.$frm.find('.lfGo').show();
    }

    /*
    расставляем состояния
    структура каждого переключателя:
    label - может иметь класс unavail
    внутри label лежит input который может быть disabled
    идентификация по input#id
    id - равен возвращаемому ключу из ajax запроса обсчета состояний Qu.lasResult[id]=1
    у label еще может быть класс active, но он чисто интеррактиывный - его менять здесь не надо

    также меняем ссотояния у option
    идентификация аналогично - по option#id
    */
    if (this.lastResult == null) {
        // снимаем все unavail, null означанет что небыло запроса ибо не стоит ни одной галочки
        this.$frm.find('input[type!=button]').prop('disabled', false);
        this.$frm.find('label').removeClass('unavail');
        //this.$frm.find('.lfGo').hide();
        this.$frm.find('.lfGo').show();
    } else if (this.lastNum > 0) {
        var self = this;
        this.$frm.find('[type=checkbox]').each(function ()
            {
                var id = this.id;
                var $label = $(this).parent('label');
                if (isDefined(self.lastResult[id]) && self.lastResult[id] > 0) {
                    $label.removeClass('unavail');
                    $(this).prop('disabled', false);

                } else {
                    if (!isDefined($(this).attr('undisabled'))) // Костыль для новых вкладок (позволяет отключить дизейбл чекбоксов, если их нет в FormData)
                    {
                        $label.addClass('unavail');
                        $(this).prop('disabled', true);
                    }
                }
        });
        this.$frm.find('option').each(function ()
            {
                var id = this.id;
                if (id) {
                    if (isDefined(self.lastResult[id]) && self.lastResult[id] > 0) {
                        //$(this).prop('disabled',false);
                        $(this).removeClass('unavail');

                    } else {
                        $(this).addClass('unavail');
                    }
                }
        });
    }
    if(this.$frm.find('input[type="checkbox"]:checked').length == 0
        && this.$frm.find('option[value!=""]:selected').length == 0
        && $('.search_q_info_str').length == 0
    )
    {
        //this.$frm.find('.lfGo').hide();
        this.$frm.find('.result-label').hide();
    }
    this.onLoad  = false;
}

// удалить все недоступные позиции
/*Qu.prototype.deleteUnavail = function ()
{
    if (this.lastResult != null) {
        var self = this;
        this.$frm.find('[type=checkbox]').each(function ()
            {
                var id = this.id;
                var $label = $(this).parent('label');
                if (isDefined(self.lastResult[id]) && self.lastResult[id] > 0) {
                // ничего 
                } else {
                    $label.remove();
                    $(this).remove();
                }
        });
        this.$frm.find('option').each(function ()
            {
                var id = this.id;
                if (id) {
                    if (isDefined(self.lastResult[id]) && self.lastResult[id] > 0) {
                    // ничего 
                    } else {
                        $(this).remove();
                    }
                }
        });  
    }
} */

// снять все галки
Qu.prototype.resetForm = function ()
{
    logit('Qu.resetForm('+this.$frm.attr('class')+')');
    this.$frm.find('input[type=checkbox]').prop('checked', false);
    this.$frm.find('select').each(function(){
        var $sel=$('option[selected=selected]', this);
        if($sel.length) $(this).val($sel.val()).change();
        else if($(this).val()!='') $(this).val('').change();
    });
    this.$frm.find('label.active').removeClass('active');
    this.$frm.find('label').removeClass('unavail');
    this.$frm.find('.result-label').hide();

    this.$frm.find('option').removeClass('unavail');
}

Qu.prototype.restoreForm = function ()
{
    logit('Qu.restoreForm('+this.$frm.attr('class')+')');
    this.$frm.find('label').removeClass('unavail').each(function ()
        {
            if ($(this).hasClass('active'))
                $(this).children('input').prop('checked', true);
            else
                $(this).children('input').prop('checked', false);
    });

}

Qu.prototype.initGroups = function ()
{
    var self = this;
    this.$frm.find('[group!=""]').each(function ()
        {
            var group = $(this).attr('group');
            if (typeof group != 'undefined') self.groups.push(group);
    });
    this.groups = _.uniq(self.groups);
}

$(function ($)
    {

        /*
        ВСЕ УТОЧНЯЮЩИЕ ФИЛЬТРЫ

        */
        if ($('.livef').length) {
            $('.livef').each(function(form_index){
                var qu = new Qu();
                qu.$frm = $(this);

                if (isDefined(window.exnum)) qu.exnum = window.exnum;
                if (isDefined(window.ext_filter)) qu.ext_filter = true;

                if (qu.$frm.attr('chVars') == 1) qu.chVars = 1;

                qu.initGroups();

                // проверяем на необходимость обсчета при загрузке страницы
                if (location.href.indexOf('?') != -1) {
                    var a = location.href.split('?');
                    a = a[1].split('&');
                    for (var i = 0; i < a.length; i++) {
                        if (a[i].substr(0, 1) == '_') qu.onLoad = true;
                    }
                }
                //else qu.deleteUnaval = true;

                if (qu.$frm.attr('onload_refresh'))
                {
                    qu.onLoad = true;
                }

                if (qu.onLoad) {
                    logit('liveFilters: onLoad = TRUE');
                    // восстанавливает состоние формы
                    qu.restoreForm();
                    // ставим в очередь запрос
                    qu.putNewQuery();
                } else {
                    // при history.back() галочки могут остаться установленными, снимаем их  (на всякий случай)
                    qu.resetForm();
                }

                qu.$frm.find('[type=checkbox], select').change(function ()
                {
                    logit('liveFilters: click');
                    qu.putNewQuery();
                });

                qu.baseUrl = qu.$frm.attr('action');

                qu.sMode = parseInt(qu.$frm.attr('sMode'));

                if(qu.sMode) qu.msgPrefix="Будет найдено спарок:";

                $('<div id="ax-loader' + form_index + '"><img align="center" src="/assets/images/ax/3.gif"></div>').appendTo(qu.$frm);
                qu.loaderId='#ax-loader' + form_index;
            });
        }

        /* все основные фильтры (в сайдбаре и в центре)
        * .liveSB - в сайдбаре
        * .liveC - центральрные на главной и на страницах поиска по размеру
        * */

        var quCollect=[];
        var qi=0;
        $('.liveSB, .liveC').each(function(e)
            {
                quCollect[qi] = new Qu();
                quCollect[qi].$frm = $(this);

                if (quCollect[qi].$frm.attr('chVars') == 1)  quCollect[qi].chVars = 1;

                quCollect[qi].initGroups();

                quCollect[qi].$frm.find('select, [type=checkbox]').change((function (i)
                    {
                        return function (e){
                            quCollect[i].putNewQuery();
                        }
                    })(qi));

                quCollect[qi].baseUrl = quCollect[qi].$frm.attr('action');

                quCollect[qi].sMode = parseInt(quCollect[qi].$frm.attr('sMode'));

                // для сайдбар фильтров
                if(quCollect[qi].$frm.hasClass('liveSB')) {
                    quCollect[qi].msgPrefix="размеров:";
                }
                $('<div style="display: none" id="ax_loader2"><div class="ax_loader2-wrap">считаю...</div> </div>').appendTo(quCollect[qi].$frm);
                quCollect[qi].loaderId='#ax_loader2';

                // проверяем на необходимость обсчета при загрузке страницы
                quCollect[qi].$frm.find('select').each(function(){
                    if($(this).val()!='') quCollect[qi].onLoad=true;
                });
                quCollect[qi].$frm.find('[type=checkbox]').each(function(){
                    if($(this).prop('checked')) quCollect[qi].onLoad=true;
                });

                if (quCollect[qi].onLoad) {
                    quCollect[qi].putNewQuery();
                }

                qi++;
        });


        //переключение инпутов
        $('.black label i').click(function ()
            {
                var $e = $(this).siblings('input');
                if ($e.prop('disabled') == true) return;
                if ($e.prop('checked') == false)
                    $(this).parent().addClass('active');
                else
                    $(this).parent().removeClass('active');
        });

        var $tmarkir=$('.tmarkir');
        if($tmarkir.length){

            var $f=$('.tsForm');
            var tma= [
                [$f.find('.pp1'), $('.tmarkir__p1'), $tmarkir.find('.im1')],
                [$f.find('.pp2'), $('.tmarkir__p2'), $tmarkir.find('.im2')],
                [$f.find('.pp3'), $('.tmarkir__p3'), $tmarkir.find('.im3')]
            ];

            (function remake()
                {
                    for (var k in tma) {
                        if (tma[k][0].val() == '') {
                            tma[k][1].fadeIn(400);
                            tma[k][2].removeClass('a1').addClass('a0');
                        } else {
                            tma[k][1].fadeOut(400);
                            tma[k][2].removeClass('a0').addClass('a1');
                        }
                    }
            })();


            for (var k in tma) {
                (function(k){
                    tma[k][0].change(function()
                        {
                            if ($(this).val() == '') {
                                tma[k][1].fadeIn(400);
                                tma[k][2].removeClass('a1').addClass('a0');
                            } else {
                                tma[k][1].fadeOut(400);
                                tma[k][2].removeClass('a0').addClass('a1');
                            }
                    });
                })(k);
            }

        }
        // Скроллинг окна
        if ($('.search_q_info_str').length > 0){
            setTimeout(function() {
                var scrollTop = $('.search_q_info_str').offset().top;
                $('html, body').animate({scrollTop: scrollTop}, 1000);
            }, 500);
        }
		
		
		// Мобильная кнопка "Найти"
		$(document).ready(function () {
			var buttonShowClass = 'search-filter-active-control',
			$filterCheckboxes = $('.form-style-01.livef').find('input[type=checkbox]');
			
			//$('.form-style-01.livef').find('input[type=checkbox]:checked').first().closest('label').addClass(buttonShowClass);
		
			$filterCheckboxes.change(function () {
				if($(this).is(':checked')) {
					$(this).closest('form').find('.' + buttonShowClass).removeClass(buttonShowClass);
					$(this).closest('label').addClass(buttonShowClass);
				} else {
					$(this).closest('label').removeClass(buttonShowClass);
					$(this).closest('form').find('input[type=checkbox]:checked').first().closest('label').addClass(buttonShowClass);
				}
			});			
		});
});

