<? 
require_once '../auth.php';
include('../struct.php');

$gr=intval(@$_GET['gr']);
if(!$gr) die('Группа товаров не задана [gr]');

$cp->frm['name']='gallery';
$cp->frm['title']='Галерея '.($gr==2?'дисков':'шин');

$cp->checkPermissions();

cp_head();
cp_css();
cp_js();

?>
<script language="JavaScript">

    var options_cache=false;

    $('document').ready(function()
    {
        $('#at_frm').submit(function(e){
            e.preventDefault();
            doLoad('save_discr',{'gal_id': $('#at_frm input[name=gal_id]').val(),'gtext': $('#at_frm #gtext').val()});
            $('#g'+$('#at_frm input[name=gal_id]').val()+' a').attr('onClick','return hs.expand(this, {captionText:"'+$('#at_frm #gtext').val()+'"})');
            $('#at_frm #gtext').val('');
        });

        $('img.jqmdX')
            .hover(
            function(){
                $(this).addClass('jqmdXFocus');
            },
            function(){
                $(this).removeClass('jqmdXFocus');
            })
            .focus(function(){
                this.hideFocus=true; $(this).addClass('jqmdXFocus');
            })
            .blur(function(){
                $(this).removeClass('jqmdXFocus');
            });

        $('#teditWin')
            .jqDrag('.jqDrag')
//	  .jqResize('.jqResize')
            .jqm({
                trigger:'.tedit',
                overlay: 1,
                onShow: function(h) {
                    $('.dlg').css('margin-left','-100px').css('left',h.t.offsetLeft+110).css('top',h.t.offsetTop+20);
                    h.w.css('opacity',0.89).show('fast');
                    $('#at_frm input[name=gal_id]').val(h.t.value);
                    doLoad('load_discr',{gal_id: h.t.value});
                },
                onHide: function(h) {
                    h.w.hide("fast",function() {
                        if(h.o) h.o.remove();
                    });
                }
            });

    });

    function teditWinBind(){
        $('#teditWin').jqmAddTrigger('.tedit')
    }

    function doLoad(act, data) {
        var req = new JsHttpRequest();
        switch (act) {
            case 'load_discr':
                saving('tedit_but', 'tedit_saving', 'Загружаю...');
                req.onreadystatechange = function () {
                    if (req.readyState == 4) {
                        //document.getElementById('debug').innerHTML = req.responseJS.debug;
                        saved('tedit_but', 'tedit_saving', '&nbsp;');
                        $('#at_frm #gtext').val(req.responseJS.gtext);
                    }
                }
                break;
            case 'save_discr':
                saving('tedit_but', 'tedit_saving', '');
                req.onreadystatechange = function () {
                    if (req.readyState == 4) {
                        //document.getElementById('debug').innerHTML = req.responseJS.debug;
                        saved('tedit_but', 'tedit_saving', req.responseText);
                        if (req.responseJS.fres == 1) $('#teditWin').jqmHide();
                    }
                }
                break;
            case 'brands':
                loading('brands', '');
                req.onreadystatechange = function () {
                    if (req.readyState == 4) {
                        document.getElementById('brands').innerHTML = req.responseText;
                        //document.getElementById('debug').innerHTML = req.responseJS.debug;
                        document.getElementById('gal_loader').innerHTML = '';
                    }
                }
                break;
            case 'brand_sel':
                loading('models', '');
                req.onreadystatechange = function () {
                    if (req.readyState == 4) {
                        document.getElementById('models').innerHTML = req.responseText;
                        //document.getElementById('debug').innerHTML = req.responseJS.debug;
                        document.getElementById('gal_loader').innerHTML = '';
                    }
                }
                break;
            case 'model_sel':
                loading('gal_loader', 'large');
                req.onreadystatechange = function () {
                    if (req.readyState == 4) {
                        document.getElementById('gal_loader').innerHTML = req.responseText;
                        teditWinBind();
                        //document.getElementById('debug').innerHTML = req.responseJS.debug;
                    }
                }
                break;
            case 'first':
                loading('gal_loader', 'large');
                req.onreadystatechange = function () {
                    if (req.readyState == 4) {
                        document.getElementById('gal_loader').innerHTML = req.responseText;
                        teditWinBind();
                        //document.getElementById('debug').innerHTML = req.responseJS.debug;
                    }
                }
                break;
            case 'model_sel_upd':
                loading('gal_data', 'large');
                req.onreadystatechange = function () {
                    if (req.readyState == 4) {
                        document.getElementById('gal_data').innerHTML = req.responseText;
                        teditWinBind();
                        //document.getElementById('debug').innerHTML = req.responseJS.debug;
                    }
                }
                break;
            case 'options':
                loading('options', 'large');
                req.onreadystatechange = function () {
                    if (req.readyState == 4) {
                        document.getElementById('options').innerHTML = req.responseText;
                        //document.getElementById('debug').innerHTML = req.responseJS.debug;
                    }
                }
                break;
            case 'options_save':
                saving('options_sbut', 'options_saving', 'Применяю новые параметры....');
                req.onreadystatechange = function () {
                    if (req.readyState == 4) {
                        //document.getElementById('debug').innerHTML = req.responseJS.debug;
                        saved('options_sbut', 'options_saving', req.responseText);
                    }
                }
                break;
            case 'delete':
                ss = document.getElementById('g' + data.gal_id).innerHTML;
                saving('g' + data.gal_id, 'g' + data.gal_id, '<br><br>Удаляю...');
                req.onreadystatechange = function () {
                    if (req.readyState == 4) {
                        //document.getElementById('debug').innerHTML = req.responseJS.debug;
                        saved('g' + data.gal_id, 'g' + data.gal_id, 'Удалено');
                        if (!req.responseJS.deleted) {
                            document.getElementById('g' + data.gal_id).innerHTML = ss;
                        } else document.getElementById('g' + data.gal_id).innerHTML = req.responseJS.block;
                    }
                }
                break;
            case 'gal_add':
                saving('gal_add_sbut', 'gal_add_saving', '');
                req.onreadystatechange = function () {
                    if (req.readyState == 4) {
                        //document.getElementById('debug').innerHTML = req.responseJS.debug;
                        saved('gal_add_sbut', 'gal_add_saving', req.responseText);
                        if (req.responseJS.gal_id > 0) {
                            doLoad('model_sel_upd', {model_id: req.responseJS.model_id});
                            document.getElementById('gal').innerHTML += req.responseJS.block;
                            teditWinBind();
                        }
                    }
                }
                break;
            default:
                window.alert('doLoad(' + act + ') not defined!');
        }
        // Prepare request object (automatically choose GET or POST).
        req.caching = false;
        req.open(null, '../be/gallery.php?gr=<?=$gr?>&act='+act, true);
        // Send data to backend.
        req.send( data );
        return false;
    }

</script>

<? cp_body()?>
<? cp_title()?>

<style type="text/css">
    .rama_green{border:1px dashed green; padding:5px; margin:10px 0px}
    .rama_red{border:1px dashed red; padding:5px; margin:10px 0px}
    TD{padding-right:10px;}
    FIELDSET{margin-top:10px}
    img.jqResize {
        position: absolute; right: 2px; bottom: 2px;
    }
    div.dlg {
        display: none;
        position: fixed;
        top: 17%;
        left: 50%;
        margin-left: -300px;
        font-family:verdana,tahoma,helvetica;
        font-size:11px;
        width: 420px;
        height:200px;
        background:#FFFFCC url(../img/note_icon.png) 5px 5px no-repeat;
        border: 1px solid #000;
        padding: 0;
        overflow:auto;
    }
    img.jqmdX {
        position: absolute;
        cursor: pointer;
        right: 4px;
        top: 6px;
        height: 19px;
        width: 0px;
        padding: 0 0 0 19px;

        background: url(../img/dlg_close.gif) no-repeat bottom left;
        overflow: hidden;
    }
    img.jqmdXFocus {background-position: top left; outline: none;}
    .dTitle{
        margin: 5px 0;
        margin-left:25px;
        margin-right:25px;
        padding:3px 5px;
        cursor:move;
        font-size:11px;
        color:#FFFFCC;
        font-weight:bold;
        background-color:#505050;
    }

    .dContent{
        border-top:1px;
        color:#000;
        text-align:center;
        padding:0 20px 5px;
    }
</style>

<fieldset class="ui"><legend class="ui">Галерея</legend>
<form>
<div style="float:left;padding-right:30px;" id="brands"></div>
<div style="" id="models"></div>
</form>
<div style=" padding-top:10px" id="gal_loader"></div>
</fieldset>
<?
if(in_array(CMS_LEVEL_ACCESS,array(1,2))){
?>
<fieldset class="ui"><legend class="ui">Настройки галереи</legend>

<div id="options_on" style=" display:block"><a href="#" onClick="toggle('options_on');toggle('options'); if(!options_cache) {options_cache=true; return doLoad('options',{})}"><img class="nob" src="../img/folder-open.gif" border="0" align="baseline"> показать настройки</a></div>
<div id="options" style="display:none;" class="rama_green"></div>

<div id="debug_on" style=" display:none; padding-top:15px"><a href="javascript:toggle('debug_on');toggle('debug')"><img class="nob" border="0" src="../img/folder-open.gif" align="baseline">Debug on</a></div>
<div id="debug" style="display:none; border:1px dashed red; padding:5px; margin-top:15px"></div>

</fieldset><?
}
?>

<div id="teditWin" class="dlg">
<div class="dTitle jqDrag">Текстовое описание</div>
<div class="dContent">
<form id="at_frm" name="at_frm">
<input type="hidden" name="gal_id">
<textarea  id="gtext" style="width:100%; height:100px"></textarea>
<nobr><input type="submit" id="tedit_but" value="Записать" style="float:left"><div style="float:left; margin-left:5px" id="tedit_saving"></div><input style="float:right; position:relative" type="button" value="Отмена" class="jqmClose"></nobr>
</form>
</div>
<img src="../img/dlg_close.gif" alt="close" class="jqmClose jqmdX " />
<img src="../img/resize.gif" alt="resize" class="jqResize" />
</div>

<script language="javascript">doLoad('brands',{})</script>

<? cp_end();
