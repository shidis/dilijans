function WaitList()
{

    $.ajaxSetup({
        type:'POST',
        global: true,
        cache:false,
        dataType: 'json',
        url: '../be/waitlist.php',
        error: Err
    });

    this.page=0;
    this.$wl=$('.wl');

    this.addedDate1=setup.addedDate1;
    this.addedDate2=setup.addedDate2;
    this.lastOrderId=0;
    this.lastWLID=0;


    this.pageHeaderHeight=$('.page-header').parent().outerHeight();

    var $e=this.$wl.find('.hor');
    this.$hor=$('<div class="hor">'+$e.html()+'</div>')
        .appendTo('body')
        .css({
            top: this.pageHeaderHeight,
            position: 'absolute'
        });
    $e.remove();

    window.loader = $('.workspace').cloader();
    window.axActive = false;

    $(document)
        .ajaxStart(function ()
        {
            loader.cloader('show');
            window.axActive = true;
        })
        .ajaxStop(function ()
        {
            loader.cloader('hide');
            window.axActive = false;
        });

    this.onScroll();

    var self=this;

    this.$hor.find('.gr')
        .chosen({
            disable_search_threshold:20
        })
        .css({width:'80px'})
        .change(function()
        {
            self.loadItems(1);
        });

    this.$hor.find('.state')
        .chosen({
            disable_search_threshold:20
        })
        .css({width:'100px'})
        .change(function()
        {
            self.loadItems(1);
        });

    this.$hor.find('.sortBy')
        .chosen({
            disable_search_threshold:20
        })
        .css({width:'100px'})
        .change(function()
        {
            self.loadItems(1);
        });

    this.$hor.find('.reload')
        .click(function(){
            self.loadItems(1);
        });

    $('<div id="overlayDlg" title="Подождите"></div>').dialog({
        autoOpen: false,
        modal: true,
        resizable: true,
        closeOnEscape: false,
        height: 80,
        width: 300
    });

    this.loadItems(1);
    return this;

}

WaitList.prototype.createdOrder = function(order_id, wl_id)
{
    this.lastOrderId=order_id;
    this.lastWLID=wl_id;
    return this;
}

WaitList.prototype.createOrder=function(wl_id)
{
    /*
    заказы можно размещать только с заявок noticed==0
     */
    var self=this;
    $('#overlayDlg').html('Размещаю заказ....').dialog('open');
    $.ajax({
        data:{
            act: 'createOrder',
            wlID: wl_id
        },
        success: function(r)
        {
            if(r.fres) {

                if(r.fres && r.order_id){
                    var $item=self.$wl.find('.item[rid='+wl_id+'] .create-order');
                    $item
                        .button('option', 'label', 'Заказ №'+ r.order_num)
                        .unbind('click')
                        .click(function()
                        {
                            window.open("order_edit.php?order_id="+ r.order_id, '_blank');
                            return false;
                        })
                        .click();
                    //$('<form method="get" action="order_edit.php" target="_blank"><input type="submit" /><input type="hidden" name="order_id" value="'+ r.order_id+'" /></form>').appendTo('body').submit();
                }else {
                    err(r.fres_msg);
                }

                logit('bind '+wl_id);

                self.createdOrder(r.order_id, wl_id);


                $(window).focus(function()
                {
                    logit('focused after '+self.lastWLID);
                    $.ajax({
                        data:{
                            act: 'checkOrder',
                            order_id: self.lastOrderId,
                            wlID: self.lastWLID
                        },
                        success: function(r)
                        {
                            if(r.fres){
                                if(r.state=='saved') {
                                    $(window).unbind('focus');
                                    logit('order ' + self.lastOrderId + ' saved.');
                                }else if(r.state=='deleted'){
                                    $(window).unbind('focus');
                                    var $item=self.$wl.find('.item[rid='+self.lastWLID+'] .create-order');
                                    $item
                                        .button('option', 'label', 'сделать заказ')
                                        .unbind('click')
                                        .click(function()
                                        {
                                            self.createOrder($(this).parents('.item').attr('rid'));
                                            return false;
                                        });
                                    logit('order ' + self.lastOrderId + ' Deleted!.')
                                }else{
                                    logit('order '+self.lastOrderId+' NOT saved.')
                                }
                            }else err(r.fres_msg);
                        }
                    });

                });

            } else err(r.fres_msg);
        },
        complete: function ()
        {
            $('#overlayDlg').dialog('close');
        }
    });

};

WaitList.prototype.setPage = function(page)
{
    this.page=page;
    return this;
};

WaitList.prototype.loadItems = function (page)
{
    var self=this;
    if(isNaN(page)) page=this.page+1;
    var $wrp=self.$wl.find('.wrapper');

    if(page==1) {
        $wrp.html('<div class="loader" style="margin-top: 30px">'+self.$wl.find('.ax2').html()+'</div>');
    }else{
        $wrp.find('.moreBtn').append('<span class="loader" style="margin-left: 30px">'+self.$wl.find('.ax2').html()+'</span>');
    }

    $.ajax({
        data:{
            act: 'items',
            state: self.$hor.find('.state').val(),
            sortBy: self.$hor.find('.sortBy').val(),
            page: page, // первая страница ==1
            gr: self.$hor.find('.gr').val(),
            limit: window.setup.wlLimit,
            addedDate1: self.addedDate1,
            addedDate2: self.addedDate2
        },
        success: function(r)
        {
            if(r.fres){
                self.setPage(page);
                if(page==1 && !_.size(r.data)) $(wrapNote('Не найдено заявок по заданному критерию')).appendTo($wrp);
                else{
                    var $i, $sb;
                    for(var i in r.data){
                        $i=$($('#WLItem').render(r.data[i])).appendTo($wrp);
                        $sb=$i.find('.sb');
                        if(r.data[i].actual==1){
                            $('<button class="create-order">сделать заказ</button>')
                                .appendTo($sb)
                                .button()
                                .click(function()
                                {
                                    self.createOrder($(this).parents('.item').attr('rid'));
                                    return false;
                                });
                        }else{
                            if(r.data[i].noticed==5){
                                $('<div class="noticed5">ручной заказ</div>').appendTo($sb);
                            }else if(r.data[i].noticed==1){
                                $('<div class="noticed1">было уведомление</div>').appendTo($sb);
                            }else if(r.data[i].noticed==0) {
                                $('<div class="noticed0">просрочен</div>').appendTo($sb);
                            }
                        }
                    }

                }
                $wrp.find('.moreBtn').remove();
                if(page < r.pages){
                    var $more=$(self.$wl.find('.more').html()).appendTo($wrp);
                    $more.find('i').html(page+1);
                    $more.unbind('click').click(function()
                    {
                        self.loadItems(page+1);
                    })
                }
                self.$hor.find('.itemsNum').html('Всего: '+r.total);

            }else err(r.fres_msg);
        },
        complete: function()
        {
            $wrp.find('.loader').remove();
        }
    });

    return this;
};

WaitList.prototype.onScroll=function()
{
    var self=this;

    $(window).scroll(function()
    {
        var o=$(window).scrollTop();
        if(o > self.pageHeaderHeight){
            self.$hor.css({
                position:'fixed',
                top:0
            });
        }else{
            self.$hor.css({
                top: self.pageHeaderHeight,
                position:'absolute'
            });
        }
    });
    return this;
};

