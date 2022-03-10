<? require_once '../auth.php';

$cp->frm['name']='smsSender';
$cp->frm['title']='Тест отправки СМС сообщений';

$cp->checkPermissions();

include('../struct.php');

cp_head();
cp_css();
cp_js();

cp_body();
cp_title();
?>
<style type="text/css">

    .collumn{
        overflow: hidden;
        max-width: 1200px;
        position: relative;
        margin-top: 15px;
    }
    .collumn .left{
        width: 60%;
        float: left;
    }
    .collumn .right{
        width: 35%;
        float: left;
        padding: 10px;
    }
    .sender{
        background: #EEE;
        padding: 5px 10px;
        display: inline-block;
        border-radius: 5px;;
    }
    .row{
        overflow: hidden;
        margin: 10px 0;
    }
    .row .tit{
        font-size: 1.2em;
        font-weight: bold;
        padding-right: 20px;
        width: 30%;
        float: left;
        text-align: right;
        line-height: 25px;;
    }
    .row .inp{
        font-size: 1.2em;
        width: 60%;
        float: left;
    }
    .row .inp input, .row .inp textarea{
        width: 99%;
        font-size: 1.2em;
    }
    #log{
        width: 99%;
        height: 400px;
        font-size: 0.9em;
    }
    .len{
        float: right;
        font-size: 1em;
        color: #666;
    }
    .len span{
        color: #990000;
    }
</style>

<?
$sms=SMS_Reactor::factory();
if($sms===false) {
    warn("SMS сервисы выключены");
    cp_end();
}
?>

<div class="balance">
    <fieldset class="ui">
        Баланс счета SMS Сервиса <?=$sms->service?> = <span id="balance" class="bold"></span>
    </fieldset>
</div>

<div class="collumn">

    <div class="left">
        <form>
            <input type="hidden" name="source" value="_default_">
            <div class="row">
                <div class="tit">Отправитель:</div>
                <div class="inp">
                    <span class="sender">
                        <?=Data::get('SMS_defaultSource')?>
                    </span>
                </div>
            </div>
            <div class="row">
                <div class="tit">
                    Телефон получателя:
                </div>
                <div class="inp">
                    <input name="dest">
                </div>
            </div>
            <div class="row">
                <div class="tit">
                    Текст сообщения:
                </div>
                <div class="inp">
                    <textarea name="msg" style="height: 160px"></textarea>
                    <div class="len">символов: <span>0</span></div>
                </div>
            </div>
            <div class="row">
                <div class="tit">
                    &nbsp;
                </div>
                <div class="inp">
                    <button id="send">ОТПРАВИТЬ СМС</button>
                </div>
            </div>
        </form>
    </div>

    <div class="right">
        <textarea id="log"></textarea>
    </div>
</div>
<?

cp_end();


