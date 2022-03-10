<?
if (!defined('true_enter')) die ("Direct access not allowed!");

require_once ($_SERVER['DOCUMENT_ROOT'].'/config/init.php');

require_once($_SERVER['DOCUMENT_ROOT'].'/cms/inc/global.php');



function cp_css()
{
    $cCSS=App_ExLib::loadCSSId();
    if(!empty($cCSS)) $cCSS='?'.$cCSS;
    ?>
    <link href="/cms/themes/redmond/ui-custom.css<?=$cCSS?>" rel="stylesheet" type="text/css" />

    <? switch ($GLOBALS['cp']->frm['name']) {
        case 'avto2':?>
            <link href="/cms/inc/jqGrid/css/ui.jqgrid.css<?=$cCSS?>" rel="stylesheet" type="text/css" />
            <link href="/cms/css/jqModal.css<?=$cCSS?>" rel="stylesheet" type="text/css" />
            <? break;
        case 'pages':?>
            <link href="/cms/inc/jqGrid/css/ui.jqgrid.css<?=$cCSS?>" rel="stylesheet" type="text/css" />
            <? break;
        case 'datasets':?>
            <? break;
        case 'reviews':?>
            <link href="/cms/css/chosen.css<?=$cCSS?>" rel="stylesheet" type="text/css">
            <link href="/cms/inc/fancytree/skin-lion/ui.fancytree.css<?=$cCSS?>" rel="stylesheet" type="text/css" />
            <? break;
        case 'waitlist':?>
            <link href="/cms/css/chosen.css<?=$cCSS?>" rel="stylesheet" type="text/css">
            <link href="/cms/inc/fancytree/skin-lion/ui.fancytree.css<?=$cCSS?>" rel="stylesheet" type="text/css" />
            <? break;
        case 's-matrix':?>
            <link href="/cms/inc/jqGrid/css/ui.jqgrid.css<?=$cCSS?>" rel="stylesheet" type="text/css" />
            <link href="/cms/inc/jqGrid/plugins/ui.multiselect.css<?=$cCSS?>" rel="stylesheet" type="text/css" />
            <link href="/cms/css/chosen.css<?=$cCSS?>" rel="stylesheet" type="text/css">
            <? break;
        case 'main':?>
            <link href="/cms/css/style.css<?=$cCSS?>" rel="stylesheet" type="text/css" />
            <link href="/cms/inc/jqGrid/css/ui.jqgrid.css<?=$cCSS?>" rel="stylesheet" type="text/css" />
            <? break;
        case 'brands':
        case 'models_bot':?>
            <link href="/cms/css/jquery.tooltip.css<?=$cCSS?>" rel="stylesheet" type="text/css">
            <link href="/cms/css/jquery.contextmenu.css<?=$cCSS?>" rel="stylesheet" type="text/css">
            <link href="/cms/css/chosen.css<?=$cCSS?>" rel="stylesheet" type="text/css">
            <link href="/cms/css/highslide.css<?=$cCSS?>" rel="stylesheet" type="text/css">
            <? break;
        case 'catalog_bot':?>
            <link href="/cms/css/jquery.tooltip.css<?=$cCSS?>" rel="stylesheet" type="text/css">
            <link href="/cms/css/highslide.css<?=$cCSS?>" rel="stylesheet" type="text/css">
            <? break;
        case 'extra_min':?>
            <link href="/cms/inc/jqGrid/css/ui.jqgrid.css<?=$cCSS?>" rel="stylesheet" type="text/css" />
            <? break;
        case 'catImport':?>
            <link href="/cms/inc/jqGrid/css/ui.jqgrid.css<?=$cCSS?>" rel="stylesheet" type="text/css" />
            <link href="/cms/css/ui-layout.css<?=$cCSS?>" rel="stylesheet" type="text/css" />
            <link href="/cms/inc/jqGrid/plugins/ui.multiselect.css<?=$cCSS?>" rel="stylesheet" type="text/css" />
            <link href="/cms/inc/uploadify/css/uploadify.css<?=$cCSS?>" rel="stylesheet" type="text/css" />
            <? break;
        case 'catImportII':?>
            <link href="/cms/inc/jqGrid/css/ui.jqgrid.css<?=$cCSS?>" rel="stylesheet" type="text/css" />
            <link href="/cms/inc/jqGrid/plugins/ui.multiselect.css<?=$cCSS?>" rel="stylesheet" type="text/css" />
            <link href="/cms/inc/jqGrid/plugins/searchFilter.css<?=$cCSS?>" rel="stylesheet" type="text/css" />
            <link href="/cms/inc/uploadify/css/uploadify.css<?=$cCSS?>" rel="stylesheet" type="text/css" />
            <? break;
        case 'catImportII2':?>
            <link href="/cms/inc/jqGrid/css/ui.jqgrid.css<?=$cCSS?>" rel="stylesheet" type="text/css" />
            <link href="/cms/inc/jqGrid/plugins/ui.multiselect.css<?=$cCSS?>" rel="stylesheet" type="text/css" />
            <link href="/cms/inc/uploadify/css/uploadify.css<?=$cCSS?>" rel="stylesheet" type="text/css" />
            <? break;
        case 'galleries':?>
            <link href="/cms/inc/uploadify/css/uploadify.css<?=$cCSS?>" rel="stylesheet" type="text/css" />
            <? break;
        case 'gallery':?>
            <link href="/cms/css/highslide.css<?=$cCSS?>" rel="stylesheet" type="text/css">
            <link  href="/cms/css/jqModal.css<?=$cCSS?>" rel="stylesheet" type="text/css" />
            <? break;
        case 'certificates':?>
            <link href="/cms/css/highslide.css<?=$cCSS?>" rel="stylesheet" type="text/css">
            <link  href="/cms/css/jqModal.css<?=$cCSS?>" rel="stylesheet" type="text/css" />
            <? break;
        case 'cgal':?>
            <link href="/cms/css/highslide.css<?=$cCSS?>" rel="stylesheet" type="text/css">
            <link href="/cms/css/chosen.css<?=$cCSS?>" rel="stylesheet" type="text/css">
            <link href="/cms/inc/x-editable/jqueryui-editable/css/jqueryui-editable.css<?=$cCSS?>" rel="stylesheet" type="text/css">
            <? break;
        case 'cnt':?>
            <link href="/cms/css/highslide.css<?=$cCSS?>" rel="stylesheet" type="text/css">
            <? break;
        case 'sconfig':?>
            <link href="/cms/inc/x-editable/jqueryui-editable/css/jqueryui-editable.css<?=$cCSS?>" rel="stylesheet" type="text/css">
            <? break;
        case 'config':?>
            <link href="/cms/inc/jqGrid/css/ui.jqgrid.css<?=$cCSS?>" rel="stylesheet" type="text/css" />
            <? break;
        case 'orders_sidebar':?>
            <link href="/cms/css/chosen.css<?=$cCSS?>" rel="stylesheet" type="text/css">
            <? break;
        case 'order_edit':?>
            <link href="/cms/css/chosen.css<?=$cCSS?>" rel="stylesheet" type="text/css">
            <link href="/cms/inc/x-editable/jqueryui-editable/css/jqueryui-editable.css<?=$cCSS?>" rel="stylesheet" type="text/css">
            <link href="/cms/css/comp/order_edit.css<?=$cCSS?>" rel="stylesheet" type="text/css">
            <? break;
        case 'orders_c':?>
            <link href="/cms/css/chosen.css<?=$cCSS?>" rel="stylesheet" type="text/css">
            <link href="/cms/css/comp/orders.css<?=$cCSS?>" rel="stylesheet" type="text/css">
            <? break;
        case 'extra':?>
            <link href="/cms/inc/jqGrid/css/ui.jqgrid.css<?=$cCSS?>" rel="stylesheet" type="text/css" />
            <? break;
    }?>
    <link href="/cms/css/jquery.jgrowl.css<?=$cCSS?>" rel="stylesheet" type="text/css" />
    <link href="/cms/css/ui.css<?=$cCSS?>" rel="stylesheet" type="text/css" />
    <? }


function cp_js()
{
    $cJS=App_ExLib::loadJSId();
    if(!empty($cJS)) $cJS='?'.$cJS;

    ?>
    <script src="/assets/js/jquery-1.11.2.min.js<?=$cJS?>" type="text/javascript"></script>
    <script src="/assets/js/underscore.js<?=$cJS?>" type="text/javascript"></script>
    <script src="/assets/js/func.lib.js<?=$cJS?>" type="text/javascript"></script>
    <script src="/cms/js/jquery.ext.js<?=$cJS?>" type="text/javascript"></script>
    <script src="/cms/js/lib/jquery.jgrowl.min.js<?=$cJS?>" type="text/javascript"></script>
    <script src="/cms/js/ax_global.js<?=$cJS?>" type="text/javascript"></script>
    <?
    switch ($GLOBALS['cp']->frm['name']) {
        case 'avto2':?>
            <script src="/cms/js/main.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/js/lib/jquery.jqModal.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/js/lib/jqDnR.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/inc/jqGrid/js/i18n/grid.locale-en.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/inc/jqGrid/js/jquery.jqGrid.min.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/js/lib/jquery.form.js<?=$cJS?>" type="text/javascript"></script>
            <? break;
        case 'extra_min':?>
            <script src="/cms/inc/jqGrid/js/i18n/grid.locale-en.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/inc/jqGrid/js/jquery.jqGrid.min.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/js/comp/min_extra.js<?=$cJS?>" type="text/javascript"></script>
            <? break;
        case 'datasets':?>
            <script type="text/javascript" src="/cms/js/lib/jquery-ui.custom.min.js<?=$cJS?>"></script>
            <script src="/cms/js/jquery.ui.ext.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/js/comp/datasets.js<?=$cJS?>" type="text/javascript"></script>
            <? break;
        case 'balances':?>
            <script type="text/javascript" src="/cms/js/lib/jquery-ui.custom.min.js<?=$cJS?>"></script>
            <script src="/cms/js/jquery.ui.ext.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/js/comp/balances.js<?=$cJS?>" type="text/javascript"></script>
            <? break;
        case 'slider':?>
            <script type="text/javascript" src="/cms/js/lib/jquery-ui.custom.min.js<?=$cJS?>"></script>
            <script src="/cms/js/jquery.ui.ext.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/js/comp/slider.js<?=$cJS?>" type="text/javascript"></script>
            <? break;
        case 'waitlist':?>
            <script src="/cms/js/lib/i18n/jquery.ui.datepicker.ru.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/js/lib/jquery-ui.custom.min.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/js/lib/jquery.chosen.min.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/js/lib/jsrender.min.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/js/lib/jquery.maskedinput.min.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/js/lib/jquery.scrollTo.min.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/js/jquery.ui.ext.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/js/comp/waitlist.js<?=$cJS?>" type="text/javascript"></script>
            <? break;
        case 'reviews':?>
            <script src="/cms/js/lib/i18n/jquery.ui.datepicker.ru.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/js/lib/jquery-ui.custom.min.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/js/lib/jquery.chosen.min.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/inc/fancytree/jquery.fancytree.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/js/lib/jsrender.min.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/js/lib/jquery.maskedinput.min.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/js/lib/jquery.scrollTo.min.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/js/jquery.ui.ext.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/js/comp/reviews.js<?=$cJS?>" type="text/javascript"></script>
            <? break;
        case 's-matrix':?>
            <script src="/cms/js/lib/jquery-ui.custom.min.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/inc/jqGrid/js/i18n/grid.locale-en.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/inc/jqGrid/js/jquery.jqGrid.min.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/js/lib/jquery.editable.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/js/lib/jquery.chosen.min.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/js/jquery.ui.ext.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/js/comp/s-matrix.js<?=$cJS?>" type="text/javascript"></script>
            <? break;
        case 'brands':?>
            <script src="/cms/js/lib/jquery-ui.custom.min.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/js/jquery.ui.ext.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/js/lib/jquery.tablesorter.min.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/js/lib/jquery.chosen.min.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/inc/tinymce/tiny_mce.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/inc/tinymce/jquery.tinymce.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/inc/tinybrowser/tb_tinymce.js.php<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/js/tinymce.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/js/comp/brands.js<?=$cJS?>" type="text/javascript"></script>
            <? break;
        case 'models_bot':?>
            <script src="/cms/js/lib/jquery-ui.custom.min.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/js/jquery.ui.ext.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/js/lib/jquery.tablesorter.min.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/js/lib/jquery.tooltip.min.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/js/lib/jquery.contextmenu.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/js/lib/jquery.chosen.min.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/js/lib/jquery.editable.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/js/lib/highslide.min.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/js/imgPreview.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/js/comp/model_bot.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/js/comp/tags.js<?=$cJS?>" type="text/javascript"></script>
            <? break;
        case 'catalog_bot':?>
            <script src="/cms/js/lib/jquery-ui.custom.min.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/js/jquery.ui.ext.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/js/lib/jquery.tooltip.min.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/js/lib/highslide.min.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/js/imgPreview.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/js/comp/catalog_bot.js<?=$cJS?>" type="text/javascript"></script>
            <? break;
        case 'add_model':
        case 'add_cat':?>
            <script src="/cms/inc/tinymce/tiny_mce.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/inc/tinymce/jquery.tinymce.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/inc/tinybrowser/tb_tinymce.js.php<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/js/tinymce.js<?=$cJS?>" type="text/javascript"></script>
            <script type="text/javascript" src="/cms/js/ui.js<?=$cJS?>"></script>
            <? break;
        case 'main':?>
            <script src="/cms/js/lib/jquery-ui.custom.min.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/js/jquery.ui.ext.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/inc/jqGrid/js/i18n/grid.locale-en.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/inc/jqGrid/js/jquery.jqGrid.min.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/js/comp/home.js<?=$cJS?>" type="text/javascript"></script>
            <? break;
        case 'config':?>
            <script src="/cms/js/lib/jquery-ui.custom.min.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/js/jquery.ui.ext.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/inc/jqGrid/js/i18n/grid.locale-en.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/inc/jqGrid/js/jquery.jqGrid.min.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/js/comp/config.js<?=$cJS?>" type="text/javascript"></script>
            <? break;
        case 'extra':?>
            <script src="/cms/js/lib/jquery-ui.custom.min.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/inc/jqGrid/js/i18n/grid.locale-en.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/inc/jqGrid/js/jquery.jqGrid.min.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/js/comp/extra.js<?=$cJS?>" type="text/javascript"></script>
            <? break;
        case 'pages':?>
            <script src="/cms/js/lib/jquery-ui.custom.min.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/js/jquery.ui.ext.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/inc/jqGrid/js/i18n/grid.locale-en.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/inc/jqGrid/js/jquery.jqGrid.min.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/inc/tinymce/tiny_mce.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/inc/tinymce/jquery.tinymce.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/inc/tinybrowser/tb_tinymce.js.php<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/js/tinymce.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/js/comp/pages.js<?=$cJS?>" type="text/javascript"></script>
            <? break;
        case 'dict':?>
            <script src="/cms/inc/tinymce/tiny_mce.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/inc/tinymce/jquery.tinymce.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/inc/tinybrowser/tb_tinymce.js.php" type="text/javascript"></script>
            <script src="/cms/js/tinymce.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/js/comp/dict.js<?=$cJS?>" type="text/javascript"></script>
            <? break;
        case 'tc':?>
            <script src="/cms/js/comp/tc.js<?=$cJS?>" type="text/javascript"></script>
            <? break;
        case 'tc2':?>
            <script src="/cms/js/lib/jquery-ui.custom.min.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/js/jquery.ui.ext.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/js/comp/tc.js<?=$cJS?>" type="text/javascript"></script>
            <? break;
        case 'go':?>
            <script src="/cms/js/lib/jquery-ui.custom.min.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/js/jquery.ui.ext.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/js/comp/go/init.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/js/comp/go/actions.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/js/comp/go/unitsActions.js<?=$cJS?>" type="text/javascript"></script>
            <? break;
        case 'catImport':?>
            <script src="/cms/js/lib/jquery-ui.custom.min.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/js/jquery.ui.ext.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/inc/jqGrid/js/i18n/grid.locale-en.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/inc/jqGrid/plugins/ui.multiselect.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/inc/jqGrid/js/jquery.jqGrid.min.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/js/lib/jquery.layout.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/inc/uploadify/jquery.uploadify.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/js/core.js<?=$cJS?>" type="text/javascript"></script>
            <? break;
        case 'catImportII':?>
            <script src="/cms/js/lib/jquery-ui.custom.min.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/js/jquery.ui.ext.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/js/lib/i18n/jquery.ui.datepicker.ru.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/inc/jqGrid/js/i18n/grid.locale-ru.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/inc/jqGrid/plugins/ui.multiselect.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/inc/jqGrid/js/jquery.jqGrid.min.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/js/lib/jquery.damnUploader.min.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/inc/uploadify/jquery.uploadify.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/js/comp/dl/import_v4_dm.js" type="text/javascript"></script>
            <? break;
        case 'catImportII2':?>
            <script src="/cms/js/lib/jquery-ui.custom.min.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/js/jquery.ui.ext.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/inc/jqGrid/js/i18n/grid.locale-en.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/inc/jqGrid/plugins/ui.multiselect.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/inc/jqGrid/js/jquery.jqGrid.min.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/inc/uploadify/jquery.uploadify.js<?=$cJS?>" type="text/javascript"></script>
            <? break;
        case 'galleries':?>
            <script src="/cms/js/lib/jquery-ui.custom.min.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/js/jquery.ui.ext.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/inc/uploadify/jquery.uploadify.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/js/comp/galleries.js<?=$cJS?>" type="text/javascript"></script>
            <? break;
        case 'gallery':?>
            <script src="/cms/js/main.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/js/lib/jshr.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/js/lib/highslide.min.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/js/lib/jquery.jqModal.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/js/lib/jqDnR.js<?=$cJS?>" type="text/javascript"></script>
            <? break;
        case 'certificates':?>
            <script src="/cms/js/main.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/js/lib/jshr.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/js/lib/highslide.min.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/js/lib/jquery.jqModal.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/js/lib/jqDnR.js<?=$cJS?>" type="text/javascript"></script>
            <? break;
        case 'cgal':?>
            <script src="/cms/js/lib/jquery-ui.custom.min.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/js/jquery.ui.ext.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/lib/jquery.damnUploader.min.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/js/lib/jquery.chosen.min.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/inc/x-editable/jqueryui-editable/js/jqueryui-editable.min.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/js/lib/jquery.placeholder.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/js/lib/jsrender.min.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/js/comp/cgal.js<?=$cJS?>" type="text/javascript"></script>
            <? break;
        case 'sconfig':?>
            <script src="/cms/js/lib/highslide.min.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/js/lib/jquery-ui.custom.min.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/inc/x-editable/jqueryui-editable/js/jqueryui-editable.min.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/js/jquery.ui.ext.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/js/comp/sconfig.js<?=$cJS?>" type="text/javascript"></script>
            <? break;
        case 'cnt':?>
            <script src="/cms/inc/tinymce/tiny_mce.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/inc/tinymce/jquery.tinymce.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/inc/tinybrowser/tb_tinymce.js.php<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/js/tinymce.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/js/lib/highslide.min.js<?=$cJS?>" type="text/javascript"></script>
            <? break;
        case 'news':?>
            <script src="/cms/inc/tinymce/tiny_mce.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/inc/tinymce/jquery.tinymce.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/inc/tinybrowser/tb_tinymce.js.php<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/js/tinymce.js<?=$cJS?>" type="text/javascript"></script>
            <? break;
        case 'orders_sidebar':?>
            <script src="/cms/js/lib/jquery.placeholder.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/js/lib/jquery.chosen.min.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/js/comp/orders_sidebar.js<?=$cJS?>" type="text/javascript"></script>
            <? break;
        case 'orders':?>
            <script src="/cms/js/comp/orders.js<?=$cJS?>" type="text/javascript"></script>
            <? break;
        case 'orders_c':?>
            <script src="/cms/js/lib/jquery-ui.custom.min.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/js/jquery.ui.ext.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/js/lib/jquery.chosen.min.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/js/comp/orders-common.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/js/comp/orders.js<?=$cJS?>" type="text/javascript"></script>
            <? break;
        case 'order_edit':?>
            <script src="/cms/js/lib/i18n/jquery.ui.datepicker.ru.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/js/lib/jquery-ui.custom.min.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/js/jquery.ui.ext.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/js/lib/jquery.chosen.min.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/inc/x-editable/jqueryui-editable/js/jqueryui-editable.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/js/lib/jquery.placeholder.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/js/lib/jsrender.min.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/js/lib/jquery.maskedinput.min.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/js/lib/jquery.damnUploader.min.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/js/comp/orders-common.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/js/comp/orderEdit.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/js/comp/orderEditSLog.js<?=$cJS?>" type="text/javascript"></script>
            <? break;
        case 'smsSender':?>
            <script src="/cms/js/lib/jquery-ui.custom.min.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/js/jquery.ui.ext.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/js/lib/jquery.placeholder.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/js/lib/jquery.maskedinput.min.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/js/comp/smsSender.js<?=$cJS?>" type="text/javascript"></script>
            <? break;
        case 'oshed':?>
            <script src="/cms/js/lib/jquery-ui.custom.min.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/js/jquery.ui.ext.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/js/lib/jsrender.min.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/js/comp/oshed.js<?=$cJS?>" type="text/javascript"></script>
            <? break;
         case 'podbor_pages':?>
            <script src="/cms/js/lib/jquery-ui.custom.min.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/js/jquery.ui.ext.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/js/lib/jquery.tablesorter.min.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/js/lib/jquery.chosen.min.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/inc/tinymce/tiny_mce.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/inc/tinymce/jquery.tinymce.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/inc/tinybrowser/tb_tinymce.js.php<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/js/tinymce.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/js/comp/podbor_pages.js<?=$cJS?>" type="text/javascript"></script>
            <? break;
        case 'entry':?>
            <script src="/cms/inc/tinymce/tiny_mce.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/inc/tinymce/jquery.tinymce.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/inc/tinybrowser/tb_tinymce.js.php<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/js/tinymce.js<?=$cJS?>" type="text/javascript"></script>
            <? break;
        case 'entry_section':?>
            <script src="/cms/inc/tinymce/tiny_mce.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/inc/tinymce/jquery.tinymce.js<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/inc/tinybrowser/tb_tinymce.js.php<?=$cJS?>" type="text/javascript"></script>
            <script src="/cms/js/tinymce.js<?=$cJS?>" type="text/javascript"></script>
            <? break;

        case 'dc_bot':
        case 'dc_top':
        case 'icq_list':
        case 'cc_mspez':
        case 'sup':
        case 'cc_classes':
            break;
    }
    ?>
    <script src="/cms/js/ui.js<?=$cJS?>" type="text/javascript"></script>
    <?
}

function cp_title($header=true,$workspace=true,$userName=true){
    if($header===true){
        ?><div class="ui-widget-header"><?
            ?><div class="page-header"><?
                if($userName){
                    ?><div style="float:right; padding-right:20px"><?
                    /*
                        $isOfficeWork = Tools::isOfficeWorking(Data::get('cc_workHoursArray'), Data::get('cc_workDaysArray'));
                        switch ($isOfficeWork['status'])
                        {
                            case 'working':
                                ?><img style="float: left; margin-right: 7px;" src="/cms/img/work_icons/rabotaem.gif" border="0" width="28" height="22" alt="Сайт работает" title="Сайт работает"><?
                                break;
                            case 'not_working':
                                ?><img style="float: left; margin-right: 7px;" src="/cms/img/work_icons/ne-rabotaem.gif" border="0" width="28" height="22" alt="Сайт НЕ работает" title="Сайт НЕ работает"><?
                                break;
                            case 'errors':  
                                ?><img style="float: left; margin-right: 7px;" src="/cms/img/work_icons/oshibka.gif" border="0" width="28" height="22" alt="Ошибки в переменных!" title="Ошибки в переменных:<?
                                if (!empty($isOfficeWork['errors']))
                                {
                                    echo "\n";
                                    foreach ($isOfficeWork['errors'] as $error) echo 'Переменная: '. $error['var'] .', позиция: '. $error['position'] .', сообщение: '.$error['message']."\n";
                                }
                                ?>"><?
                                break;
                        }
                    */
                        echo CU::$fullName;
                    ?></div><?
                }
                echo @$GLOBALS['cp']->frm['title'];
            ?></div><?
        ?></div><?
    }
    if($workspace) echo '<div class="workspace">';
}

function cp_head(){?>
    <!DOCTYPE html>
    <html>
        <head>
            <meta http-equiv="content-type" content="text/html; charset=utf-8" />
            <title>CMS: <?=@$GLOBALS['cp']->frm['title']?> :: <?=strtoupper(str_replace('www.','',Cfg::get('site_url')))?></title>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8" lang="ru">
            <script type="text/javascript"><?
                if(is_array(CU::$sdata)){
                    ?>window.SDATA=<?=json_encode(CU::$sdata,JSON_FORCE_OBJECT)?>;<?
                }
                if(is_array(CU::$udata)){
                    ?>window.UDATA=<?=json_encode(CU::$udata,JSON_FORCE_OBJECT)?>;<?
                }
            ?></script>
            <? }

        function cp_body(){?>
        </head>
        <body>
            <div id="overlay"><div style="background:url(/cms/img/ajaxLoader.gif) no-repeat center; width:100%; height:100%;"></div></div>
            <? }


        function cp_end($div=true){?>
            <? if($div===true){?><? }?>
            </div>
        </body>
    </html>
    <? }


