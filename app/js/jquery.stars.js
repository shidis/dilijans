(function () {
    /**
     * @var Object Default plugin options
     */
    var defaultOptions = {
        onClick: function (e, currentValue) {
            return false;
        },
        inum: 5,
        fieldName: null,
        fieldKey: null,
        editable: true
    };

    /**
     * @param current jQuery element
     */
    var $this = null;

    var fieldName;
    var fieldKey;
    var v;

    /**
     * @param all plugin methods
     */
    var methods = {
        init: function (options) {
            var settings = $.extend({},defaultOptions, options);
            return this.each(function () {
                $this = $(this); // это $(this)[0]
                if($this.attr('v') != undefined) v = $this.attr('v'); else v=0;
                $.data($(this)[0], 'newval', v);
                $.data($(this)[0], 'cls', []);

                if($this.attr('fieldname') != undefined) fieldName=  $this.attr('fieldname'); else fieldName = settings.fieldName;
                if($this.attr('fieldkey') != undefined) fieldKey = $this.attr('fieldkey'); else fieldKey = settings.fieldKey;

                if(fieldName !== null && fieldKey !== null){
                    $this.append('<input type="hidden" name="'+fieldName+'['+fieldKey+']" value="'+v+'">');
                }else if(fieldName !== null && fieldKey === null){
                    $this.append('<input type="hidden" name="'+fieldName+'" value="'+v+'">');
                }

                for (var i = 1; i <= settings.inum; i++) {
                    var $li = $('<li></li>').appendTo($this);
                    if (v > 0) {
                        if (i <= Math.floor(v) || i == v) $li.addClass('full');
                        else if (i == Math.round(v)) $li.addClass('right');
                    }

                    if(settings.editable){
                        $li.mouseover(function () {
                            $(this).prop('class', 'full');
                            $(this).prevAll('li').prop('class', 'full');
                            $(this).nextAll('li').prop('class', '');
                        }).click(function () {
                            var a=[];
                            var $ul=$(this).parent('ul');
                            $ul.find('li').each(function () {
                                a.push($(this).prop('class'));
                            });
                            $.data($(this).parent('ul')[0], 'cls', a);
                            $ul.animate(
                                {opacity: 0.5}, 100,
                                function () {
                                    $(this).animate({opacity: 1}, 100)
                                }
                            );
                            var newval=$ul.find('li').index($(this))+1;
                            $.data($(this).parent('ul')[0], 'newval', newval);
                            $ul.find('input[type=hidden]').val(newval);
                            settings.onClick(this, newval);

                            return false;
                        });

                        var a=$.data($(this)[0], 'cls');
                        a.push($li.prop('class'));
                        $.data($(this)[0], 'cls', a);
                    }
                }
                if(settings.editable){
                    $this.mouseout(function () {
                        var i = 0;
                        var $ul=$(this);
                        _.each($.data($(this)[0], 'cls'), function (c) {
                            $ul.find('li:eq(' + i + ')').prop('class', c);
                            i++;
                        });
                    });
                    $this.find('li').css({'cursor':'pointer'});
                }else{
                    $this.find('li').css({'cursor':'default'});
                }
            });
        },

        getValue: function()
        {
            return $.data($(this)[0], 'newval');
        },

        setValue: function(v)
        {
            $.data($(this)[0], 'newval', v);
            if(!v) $(this).find('li').prop('class','');
            else{
                $(this).find('li').prop('class','full');
                $(this).find('li:gt('+(v-1)+')').prop('class','');
            }
            var a=[];
            $(this).find('li').each(function () {
                a.push($(this).prop('class'));
            });
            $.data($(this)[0], 'cls', a);
            return this;
        }
    };


    /**
     * Plugin constructor
     * @param method
     */
    jQuery.fn.stars = function (method) {
        if (methods[method]) {
            return methods[ method ].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            jQuery.error('Method ' + method + ' does not exist on jQuery.stars');
        }
    };
})(jQuery);