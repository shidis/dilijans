
var reviews={

    init: function()
    {
        this.note='Отзыв появится на сайте после прохождения модерации';

        var self=this;

        $('.stars-fix').stars({
            editable: false,
            inum: VJS.revRatingScale
        });

        this.$rvws=$('.reviews');

        if(!this.$rvws.length) {
            // карточка размера
            $('.rvws-tab-nav').click(function()
            {
                var $rc=$('.rvws-c');
                if($rc.html()=='') {
                    $rc.html('<img src="/assets/images/ax/3.gif">');
                    $.ajax({
                        dataType: 'html',
                        url: '/ax/getReviewsHtml',
                        data: {
                            mid: VJS.mid
                        },
                        success: function(data)
                        {
                            $rc.hide().html(data).fadeIn(400);
                            self.$rvws=$('.reviews');
                            self.$rvws.find('.rvws-item').each(function()
                            {
                                self.itemBind($(this));
                            });
                            self.addFormBind();
                        }
                    });
                }
            });
        }else{
            // карточка модели
            this.$rvws.find('.rvws-item').each(function()
            {
                self.itemBind($(this))
            });

            this.addFormBind();
        }
    },

    itemBind: function($el)
    {
        var self=this;

        $el.find('.stars').stars({
            editable: true,
            inum: VJS.revRatingScale
        });

        $el.find('.rev-edit').click(function()
        {
            return self.modReview($(this).parents('.rvws-item').attr('rid'));
        });

        $el.find('.rev-del').click(function(e)
        {
            return self.delReview($(this).parents('.rvws-item').attr('rid'));
        });

        return this;
    },

    modReview: function(rid)
    {
        if(this.$rvws.find('.items .review-form').length){
            alert('Прежде, закончите с редактированием другого отзыва.');
            return false;
        }
        this.$rvws.find('.form-add-c').html('');
        this.$rvws.find('.add-new').hide();
        var self=this;
        var $i=self.$rvws.find('.rvws-item[rid='+rid+']');
        $i.animate({opacity: 0.2},100);
        $.ajax({
            dataType: 'html',
            url: '/ax/modReviewFormHtml',
            data: {
                reviewId: rid,
                mid: VJS.mid
            },
            success: function (data)
            {
                $i.html(data);
                $i.animate({opacity: 1},500);
                self.$form=$i.find('.review-form');
                self.formBind().submitReviewBind();
            }
        });

        return false;
    },

    delReview: function(rid)
    {

        var self=this;

        if(!confirm('Отзыв будет безвозвратно удален. Продолжить?')) return false;

        $.ajax({
            url: '/ax/delReview',
            data: {
                reviewId: rid
            },
            success: function(r)
            {
                if(r.fres){
                    self.$rvws.find('.rvws-item[rid='+rid+']')
                        .css({background: '#E86F6F'})
                        .slideUp(500, function()
                        {
                            $(this).remove();
                        });
                }else emsg(r);
            }

        });

        return false;
    },

    addFormBind: function()
    {
        var self=this;

        this.$rvws.find('.add-new').click(function()
        {
            var $fc=self.$rvws.find('.form-add-c');
            if(!$fc.find('form').length) {

                $fc.html('<img src="/assets/images/ax/3.gif">').show();
                $.ajax({
                    url: '/ax/reviewForm',
                    dataType: 'html',
                    data: {
                        mid: VJS.mid
                    },
                    success: function(html)
                    {
                        $fc.hide().html(html).slideDown(500);

                        self.$form=$fc.find('.review-form');
                        self.formBind().submitReviewBind();
                    }
                });
            }else {
                $fc.slideUp(500, function()
                {
                    $fc.html('');
                });
            }
            return false;
        });

        return this;

    },

    formBind: function()
    {
        this.$form.find('.note').html(this.note);
        var self=this;
        this.$form.find('.stars').stars({
            editable: true,
            inum: VJS.revRatingScale,
            onClick: function (e, currentValue)
            {
                if($(e).parent('ul').hasClass('stars-dash')){
                    var vals= 0, i=0;
                    self.$form.find('[name^=vals]').each(function()
                    {
                        if($(this).val()>0) {
                            vals += parseInt($(this).val());
                            i++;
                        }
                    });
                    vals=Math.round(vals/i*10)/10;
                    self.$form.find('.rating-s').stars('setValue', Math.round(vals));
                    if((vals - Math.floor(vals))) vals+=''; else vals+='.0';
                    if(i) self.$form.find('.rating-n').html(vals); else self.$form.find('.rating-n').html('0.0');

                }else{
                    self.$form.find('.rating-n').html(currentValue+'.0');
                }
                return false;
            }
        });

        this.$form.find('.btn-reset').click(function()
        {
            self.resetForm();
            return false;
        });

        return this;
    },

    resetForm: function()
    {
        this.$form.find('input[type=text], input[type=hidden], select, textarea').val('');
        this.$form.find('*').removeClass('uncorrect full');
    },

    submitReviewBind: function()
    {
        var self=this;

        var rid=this.$form.attr('rid');

        this.$form.submit(function()
        {
            self.$form.find('.note').html('<img src="/assets/images/ax/3.gif">');
            $.ajax({
                url: '/ax/postReview',
                data: {
                    mid: VJS.mid,
                    reviewId: rid,
                    f: self.$form.find('form').serialize()
                },
                success: function(r)
                {
                    self.$form.find('*').removeClass('uncorrect');

                    if(r.fres){

                        if(rid){
                            // редактирование
                            var $newItem=self.$rvws.find('.rvws-item[rid='+rid+']');
                            $newItem.animate({opacity: 0.2},400);
                        }else{
                            //добавление
                            var $newItem=$('<div class="new-rw-item"><img src="/assets/images/ax/3.gif"></div>').appendTo(self.$rvws.find('.items'));
                        }

                        $.ajax({
                            dataType: 'html',
                            url: '/ax/getReviewHtml',
                            data: {
                                reviewId: r.data['id']
                            },
                            success: function(data)
                            {
                                if(rid){
                                    self.$form.remove();
                                    $newItem.html(data).css({opacity:0.2}).animate({opacity:1},400);
                                    self.itemBind($newItem);
                                    if(isDefined(window.adminLogged)) self.$rvws.find('.add-new').show();
                                }else {
                                    $newItem.hide().html(data).fadeIn(800);
                                    self.itemBind($newItem);
                                    if(!isDefined(window.adminLogged)) self.$rvws.find('.add-new').hide();
                                    self.$form.remove();
                                }
                            },
                            error: function()
                            {
                                if(rid)
                                    self.$rvws.find('.rvws-item[rid='+rid+']').html('Ошибка загрузки.').css({opacity:0.2}).animate({opacity:1},400);
                                else
                                    $newItem.html('Ошибка загрузки.');
                            }
                        });


                    }
                    else if(r.err_msg!='') msg('Извините, отзыв не опубликован. '+ r.err_msg);
                    else if(isDefined(r['incorrect'])){
                        _.each(r.incorrect, function(v)
                        {
                            self.$form.find('[for='+v+']').addClass('uncorrect');
                        });
                        if(r.fres_msg!='') msg(r.fres_msg);
                    }
                },
                complete: function()
                {
                    self.$form.find('.note').html(self.note);
                }
            });

            return false;
        });

        return this;
    }
}

$(document).ready(function()
{

    reviews.init();
});