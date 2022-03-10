<?

require_once '../auth.php';
include_once ('../struct.php');
$gr = intval(@$_REQUEST['gr']);
if (empty($gr)) exit('Не указана группа страниц (gr)!');

$cp->frm['name'] = 'podbor_pages';
$cp->frm['title'] = 'База страниц подбора ' . ($gr == 1 ? 'шин' : 'дисков') . ' по марке автомобиля';

$cp->checkPermissions();

cp_head();
cp_css();
cp_js();
?>

    <style type="text/css">
        .choice {
            float: left;
            margin-right: 20px
        }

        option.h {
            color: #999999
        }

        #tables {
            margin-top: 15px
        }

        .nav1, .nav2 {
            margin-top: 10px
        }

        .add, .edit, .del {
            margin-right: 5px;
            vertical-align: baseline
        }

        H1.h {
            font-size: 16px;
        }
    </style>

<? cp_body() ?>
<? cp_title() ?>
    <div id="loading1" style="display:none">
        <div id="_loading1"><img src="../img/loading.gif" width="16" height="16"></div>
    </div>
    <?
    if (!empty($error))
    {
        warn($error);
    }
    ?>
    <form id="form_pp" name="form_pp" method="post" action="/cms/be/podbor_pages.php">
        <input name="post" value="true" type="hidden"/>
        <div style="width:100%; overflow:hidden">
            <div class="choice" id="_vendors" sel="<?=@$vendor_id?>"></div>
            <div class="choice" id="_models" sel="<?=@$model_id?>"></div>
            <div class="choice" id="_years" sel="<?=@$year_id?>"></div>
            <div class="choice" id="_modifs" sel="<?=@$modif_id?>"></div>
        </div>
        <br>
        <input name="gr" id="gr" value="<?= $gr ?>" type="hidden"/>
        <input name="act" id="action" value="" type="hidden"/>
        <input name="page_id" id="page_id" value="" type="hidden"/>
        <!--Основной контент-->
        <div id="tables" style="overflow:hidden; display:none">
            <div class="row">
                <input class="add_new_pp" type="button" value="+ Добавить страницу"
                       class="ui-corner-all">
                <input class="delete_selected" type="button" value="Удалить выбранное" onclick="del_cascade();"
                       class="ui-corner-all">
            </div>
            <div class="content_wrap"></div>
            <div class="row">
                <input class="add_new_pp" type="button" value="+ Добавить страницу"
                       class="ui-corner-all">
                <input class="delete_selected" type="button" value="Удалить выбранное" onclick="del_cascade();"
                       class="ui-corner-all">
            </div>
        </div>
        <!--/Основной контент-->
    </form>
<? cp_end() ?>