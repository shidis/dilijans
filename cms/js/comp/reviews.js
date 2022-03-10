function Reviews()
{
    $.ajaxSetup({
        type:'POST',
        global: true,
        cache:false,
        dataType: 'json',
        url: '../be/reviews.php',
        error: Err
    });

    this.itemsPage=0;
    this.brand_id=0;
    this.model_id=0;
    this.$rvws=$('#rvws');
    this.date1=setup.date1;
    this.date2=setup.date2;

    this.pageHeaderHeight=$('.page-header').parent().outerHeight();

    var $e=this.$rvws.find('.hor');
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

    this.$hor.find('.postedBy')
        .chosen({
            disable_search_threshold:20
        })
        .change(function()
        {
            self.setBrandId(0).setModelId(0);
            self.loadItems(1,true);
        });

    this.$hor.find('.state')
        .chosen({
            disable_search_threshold:20
        })
        .change(function()
        {
            self.setBrandId(0).setModelId(0);
            self.loadItems(1,true);
        });

    this.$hor.find('.dates')
        .mask("99-99-9999")
        .datepicker({
            dateFormat: 'dd-mm-yy',
            changeMonth: true
        })
        .change(function()
        {
            if($(this).hasClass('date1')) self.setDate(1,$(this).val());
            else if($(this).hasClass('date2')) self.setDate(2,$(this).val());
        });

    this.$hor.find('.reload')
        .button()
        .click(function(){
            self.setBrandId(0).setModelId(0);
            self.loadItems(1,true);
        });

    this.modelsTreeInit();
    this.loadItems(1,true);

    return this;
}

Reviews.prototype.setDate=function(i,d)
{
    this['date'+i]=d;
    return this;
}

Reviews.prototype.onScroll=function()
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
}

Reviews.prototype.setPage=function(page)
{
    this.itemsPage=page;
    return this;
}

Reviews.prototype.setModelId=function(id)
{
    this.model_id=id;
    return this;
}

Reviews.prototype.setBrandId=function(id)
{
    this.brand_id=id;
    return this;
}

Reviews.prototype.loadItems=function(page, reloadTree)
{
    var self=this;
    if(isNaN(page)) page=this.itemsPage+1;
    var $r=self.$rvws.find('.right');
    var $l=self.$rvws.find('.left');

    if(page==1) {
        $r.html('<div class="loader" style="margin-top: 30px">'+self.$rvws.find('.ax2').html()+'</div>');
        if(self.itemsPage!==0) $.scrollTo(self.$rvws,600);
    }else{
        $r.find('.moreBtn').append('<span class="loader" style="margin-left: 30px">'+self.$rvws.find('.ax2').html()+'</span>');
    }

    if(reloadTree){
        self.modelsTree.before('<div class="loader" style="margin: 10px 0; text-align: center">'+self.$rvws.find('.ax2').html()+'</div>').hide();
    }else{
    }

    $.ajax({
        data:{
            act: 'items',
            state: self.$hor.find('.state').val(),
            postedBy: self.$hor.find('.postedBy').val(),
            page: page, // первая страница ==1
            limit: window.setup.revListLimit,
            model_id: self.model_id,
            brand_id: self.brand_id,
            date1: self.date1,
            date2: self.date2
        },
        success: function(r)
        {
            if(r.fres){
                self.setPage(page);
                if(page==1 && !_.size(r.revs.data)) $(wrapNote('Не найдено отзывов по заданному критерию')).appendTo($r);
                else{
                    for(var i in r.revs.data){
                        $($('#revItem').render(r.revs.data[i])).appendTo($r);
                    }

                    $r.find('.review .dinfo').unbind('click').click(function()
                    {
                        var $el=$(this).parents('.row').siblings('.dinfo-wrap');
                        if($el.attr('hidden')){
                            $el.removeAttr('hidden').slideUp(200);
                        }else{
                            $el.attr('hidden',1).slideDown(200);
                        }
                        return false;
                    });
                    $r.find('.review .approve')
                        .button()
                        .unbind('click')
                        .click(function(e){
                            return self.moderate(e);
                        });
                    $r.find('.review .cancel')
                        .button()
                        .unbind('click')
                        .click(function(e){
                            return self.moderate(e);
                        });
                }

                $r.find('.moreBtn').remove();

                if(page < r.revs.pages){
                    var $more=$(self.$rvws.find('.more').html()).appendTo($r);
                    $more.find('i').html(page+1);
                    $more.unbind('click').click(function()
                    {
                        self.loadItems(page+1);
                    })
                }

                if(reloadTree && isDefined(r.models)){
                    var tree=self.modelsTree.fancytree('getTree');
                    tree.reload(r.models);
                }

                self.$hor.find('.symbols').html(r.revs.symbols);

            }else err(r.fres_msg);
        },
        complete: function()
        {
            $l.find('.loader').remove();
            $r.find('.loader').remove();
            self.modelsTree.show();
        }
    });

    return this;
}

Reviews.prototype.moderate=function(e)
{
    var $el=$(e.target);
    var rid=$el.parents('.review').attr('rid');
    var state='';
    if($el.hasClass('approve') || $el.parents('button').hasClass('approve')) state=1;
    else if($el.hasClass('cancel') || $el.parents('button').hasClass('cancel')) state=-1;

    if(state==='') {
        err('state не задан');
        return false;
    }

    var self=this;

    $.ajax({
        data: {
            act: 'moderate',
            rid: rid,
            state: state
        },
        success: function(r)
        {
            if(r.fres){
                var $rev=self.$rvws.find('.review[rid='+rid+']');
                var $moder=$rev.find('.moder');
                $rev.find('.dinfo').click();
                $moder.show(500);
                $moder.find('.cuName').html(r.rev.cUser_shortName);
                $moder.find('.cuStateDt').html(r.rev.dt_state);
                $rev.find('.moderBtns').fadeOut(500);
                if(state==1){
                    $rev.find('.subt .state').html('допущен').removeAttr('class').addClass('state state1');
                }else{
                    $rev.find('.subt .state').html('отменен').removeAttr('class').addClass('state state-1');
                }
            }else err(r.fres_msg);
        }
    });

    return false;
}

Reviews.prototype.modelsTreeInit=function()
{
    var self=this;
    this.modelsTree=this.$rvws.find('.left .tree').fancytree({
        source: [
            {title: "Все отзывы", key: "0"}
        ],
        clickFolderMode: 3,
        activate: function(e,d)
        {
            /*
             d.node.key
             d.node.folder
             */
            if(d.node.folder == undefined) {
                self.setBrandId(0).setModelId(d.node.key).loadItems(1);
            }else{
                self.setBrandId(d.node.key).setModelId(0).loadItems(1);
            }
        }
    });
    return this;
}
