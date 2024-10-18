<?php
require_once '../auth.php';
include('../struct.php');

$cp->frm['title'] = 'Выбор аксесуаров для брендов';
$cp->frm['name'] = 'accessories_bind';

$cp->checkPermissions();

cp_head();
cp_css();
cp_js();
cp_body();
cp_title();

?>
<link rel="stylesheet" href="//cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css">
<script src="../js/lib/jquery.dataTables.min.js"></script>
<link rel="stylesheet" href="../css/accessories.css">
<script src="../js/accessories.js"></script>

<?
$gr = 2;
$ccAcc = new CC_Ctrl();
$ccAcc->query("SELECT acc_id, name FROM `cc_accessories` WHERE `gr` = '{$gr}' AND NOT `LD` ORDER BY 'pos';", MYSQLI_ASSOC);

$arAccessories = [];

while ($accessory = $ccAcc->next()) {
    $arAccessories[] = $accessory;
}
?>

<fieldset class="accessories-choice-box">
    <legend>Выбор аксесуаров для брендов</legend>

    <div class="accessories-choice-box__top-panel">
            <span class="accessories-choice-box__group-name"><b>Группа:</b>
            <select class="brands-groups-list" title="Выбор группы товаров">
                <option class="brands-groups-list__item" value="1">Шины</option>
                <option class="brands-groups-list__item" value="2">Диски</option>
            </select>
            </span>
        <span class="accessories-choice-box__brand-name"></span>
        <button class="accessories-choice-box__save">Сохранить</button>
        <span class="accessories-choice-box__save-result"></span>
        <img class="ajax-loader_save" src="../img/ajaxLoaderCircle.gif">
    </div>

    <fieldset class="accessories-choice-box__column brands-box">
        <legend class="brands-box__title">Список брендов</legend>

        <table class="brands-box__table">
            <thead>
            <tr>
                <th></th>
                <th>ID</th>
                <th>Название</th>
                <th>Аксессуары</th>
            </tr>
            </thead>
            <tbody class="brands-box__list"></tbody>
        </table>
    </fieldset>


    <fieldset class="accessories-choice-box__column brand-accessories-box">
        <legend class="brand-accessories-box__title">Выбранные аксесуары</legend>

        <div class="table-scroll-container">
            <form class="brand-accessories-form" name="form1" method="post">
                <input type="hidden" name="brandID">
                <input type="hidden" name="group">

                <table class="brand-accessories-table">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Название</th>
                        <th>Цена(руб)</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody class="brand-accessories-box__list"></tbody>
                </table>
                <div class="brand-accessories-box__message"></div>
                <div class="ajax-loader_brand-accessories">
                    <img src="../img/ajaxLoaderCircle.gif">
                </div>
            </form>
        </div>
    </fieldset>

    <fieldset class="accessories-choice-box__column accessories-box">
        <legend class="accessories-box__title">Доступные аксесуары</legend>

        <table class="accessories-box__table">
            <thead>
            <tr>
                <th>ID</th>
                <th>Название</th>
                <th>Цена</th>
            </tr>
            </thead>
            <tbody class="accessories-box__list"></tbody>
        </table>
    </fieldset>
    <div style="clear: both"></div>
    <img class="ajax-loader_accessories-choice-box" src="../img/ajaxLoaderCircle.gif">

    <div class="confirm-save-changes-modal">

    </div>
</fieldset>