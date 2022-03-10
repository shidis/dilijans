scrollPage={};

scrollPage.enabled=true;

/*
возвращает строку scroll=scrollTop,offsetTop,elementId для приставки к урлу.
у элемента $(e.target) должен быть уникальный на странице id
 */
scrollPage.getHash=function(e)
{
    if(!this.enabled) return '';
    var scrollTop=Math.ceil($(window).scrollTop());
    var id= e.target.id;
    var o=$(e.target).offset();
    //logit('scroll='+scrollTop+','+ Math.ceil(o.top)+','+id);
    return 'scroll='+scrollTop+','+ Math.ceil(o.top)+','+id;
}

scrollPage.onLoad=function ()
{
    if(!this.enabled) return false;
    var uh=window.location.hash;
    if(uh=='') return false;
    var re=/scroll=([0-9]+),([0-9]+),([a-zA-Z0-9\-_]+)/;
    var ah=re.exec(uh);
    if(ah===null) return false;
    var $el=$('#'+ah[3]);
    var o=$el.offset();
    var stop=ah[1]*1;
    if(o.top!=ah[2]){
        stop=ah[1]*1+(o.top-ah[2]);
    }
    $(window).scrollTop(stop);
}

$(document).ready(function(){
    scrollPage.onLoad();
});


if (typeof window.hs != 'undefined') {
    hs.graphicsDir = '/cms/img/hs/';
    hs.showCredits = false;
    hs.expandDuration = 100;
    hs.restoreDuration = 100;
    hs.outlineType = 'rounded-white';
    hs.wrapperClassName = 'colored-border';
}

function UVarSet(key, value)
{
    if (!isDefined(window.UDATA)) window.UDATA = {};
    window.UDATA[key] = value;
    var coo = getCookie('USetVars');
    if (isObject(coo)) coo = unserialize(Base64.decode(coo)); else coo = {};
    coo=array_merge(coo,window.UDATA);
    setCookie('USetVars', Base64.encode(serialize(coo)));
}

function SVarSet(key, value)
{
    if (!isDefined( window.SDATA)) window.SDATA = {};
    window.SDATA[key] = value;
    var coo = getCookie('SSetVars');
    if (isObject(coo)) coo = unserialize(Base64.decode(coo)); else coo = {};
    coo=array_merge(coo,window.SDATA);
    setCookie('SSetVars', Base64.encode(serialize(coo)));
}


$(document).ready(function ()
{
    /*
     var selRow={color:'',id:''};

     $('.ui-table td').bind('mouseover',function(e){
     //		alert($(e.target).parent().attr('cat_id'));
     if(selRow.id==''){
     selRow.id=$(e.target).closest('tr').attr('id');
     selRow.color=$(e.target).closest('tr').children('td').css('background-color');
     }else{high
     $('.ltable tr[cat_id='+selRow.id+'] td').css({'background-color':selRow.color});
     selRow.color=$(e.target).closest('tr').children('td').css('background-color');
     selRow.id=$(e.target).closest('tr').attr('cat_id');
     }
     $('.ltable tr[cat_id='+selRow.id+'] td').css({'background-color':'#FF9'});
     });

     */
    $('.highslide').click(function ()
    {
        return hs.expand(this, {
            captionText: $(this).attr('alt'),
            src: $(this).attr('img')
        })
    })


    applyStyles();

});

function wrapNote(msg, id)
{
    return '<div class="ui-widget msg-block" id="' + (id || '') + '"><div class="ui-state-highlight ui-corner-all" style="margin-top: 20px; padding: 0pt 0.7em;"><p><span class="ui-icon ui-icon-info" style="float: left; margin-right: 0.3em;"></span>' + msg + '</p></div></div>';
}

function wrapWarn(msg, id)
{
    return '<div class="ui-widget msg-block" id="' + (id || '') + '"><div class="ui-state-error ui-corner-all" style="padding: 0pt 0.7em;"><p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: 0.3em;"></span>' + msg + '</p></div></div>';
}

function applyStyles()
{
    $('.ui-table th').addClass('ui-widget-header');
    $('input, select, textarea').addClass('ui-corner-all');
//	$('.ui-table td, .ui-table th').addClass('ui-corner-all');
    $('.ui-table tr').each(function (i, e)
    {
        if (i)
            if ((i / 2) == Math.round(i / 2)) $(this).children('td').addClass('ui-table-td-odd');
            else $(this).children('td').addClass('ui-table-td-even');
    });
}