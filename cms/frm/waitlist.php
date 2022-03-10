<? 

require_once '../auth.php';
include('../struct.php');

$cp->frm['name']='waitlist';
$cp->frm['title']='Заявки клиентов на уведомление о поступление товара';

$cp->checkPermissions();

cp_head();
cp_css();
cp_js();

$db=new DB();
$d=$db->getOne("SELECT DATE_FORMAT(min(dt_added),'%d-%m-%Y'), DATE_FORMAT(max(dt_added),'%d-%m-%Y') FROM os_waitList");
$setup['addedDate1']=$d[0];
$setup['addedDate2']=$d[1];

$setup['wlLimit']=100;
$setup['wlNotification'] = Cfg::_get('waitList');


?>

<style type="text/css">
    .hor{
        overflow: visible;
        box-shadow: 0 0 7px rgba(0,0,0,0.5);
        padding: 6px 15px;
        background: white;
        min-width: 800px;
    }

    .wrapper{
        position: relative;
        margin: 45px 0 45px;
        overflow: hidden;
    }
    .hor label{
        display: inline-block;
        vertical-align: middle;
        margin-right: 5px;
        margin-left: 10px;
    }
    .hor select{
        display: inline-block;
    }
    .hor .chzn-container{
        vertical-align: middle;
    }
    .moreBtn{
        background: #EEE;
        border-radius: 3px;
        padding: 10px 40px;
        text-align: center;
        border: #CCC solid 1px;
        -moz-box-shadow: 0 0 2px rgba(0,0,0,0.5);
        margin-top: 30px;
        cursor: pointer;
    }
    .moreBtn i{
        font-style: normal;
    }
    .item{
        border: 1px solid #CCC;
        border-radius: 5px;
        margin-bottom: 20px;
        overflow: hidden;
        padding-top: 10px;
    }
    .item:hover{
        border-color: #DB6484;
    }
    .item-actual1{
        background-color: #9FF2A1;
    }
    .item .sb{
        display: inline-block;
        vertical-align: top;
        width: 100px;
        padding: 0 0 0 10px;
    }
    .item .w{
        display: inline-block;
    }
    .item .row{
        overflow: hidden;
    }
    .item dl{
        display: inline-block;
        margin: 0 10px 10px;
    }
    .item dt{
        display: inline-block;
        margin-right: 5px;
    }
    .item dd{
        display: inline-block;
        -webkit-margin-start:0;
        margin-left: 0;
        font-weight: bold;
    }
    .item .sb div {
        border-radius: 3px;
        padding: 4px 10px;
        border-width: 1px;
        border-style: dashed;
        border-color: #CCC;
        text-align: center;
        text-shadow: 0 0 2px rgba(0,0,0,0.5);
        margin-bottom: 10px;
    }
    .item .sb button{
        margin-bottom: 10px;;
    }
    .item .sb div.noticed0{
        background-color: #FCC2F9;
    }
    .item .sb div.noticed1{
        background-color: #FCFC92;
    }
    .item .sb div.noticed5{
        background-color: #92CAFC;
    }
</style>


    <script id="WLItem" type="text/x-jsrender">

        <div class="item item-actual{{:actual}}" rid="{{:id}}">
            <div class="sb"></div>
            <div class="w">
                <div class="row">
                    <dl><dd><b>{{:gr_}} {{:tname}}</b></dd></dl>
                    <dl><dt>Кол-во:</dt><dd>{{:am}}</dd></dl>
                    {{if turl}}
                        <dl><dt>Артикул:</dt><dd><a href="{{:turl}}" target="_blank">{{:cat_id}}</a></dd></dl>
                    {{else}}
                        <dl><dt>Артикул:</dt><dd>{{:cat_id}}</dd></dl>
                    {{/if}}
                    {{if actual==1}}
                        <dl>
                            <dt>Сейчас наличие:</dt>
                            {{if sc>0}}
                                <dd>{{:sc}} шт.</dd>
                            {{else sc==-1}}
                                <dd>нет товара на сайте</dd>
                            {{else sc==0}}
                                <dd>нет в наличии</dd>
                            {{/if}}
                        </dl>
                    {{/if}}
                </div>
                <div class="row">
                    {{if userName}}
                        <dl><dt>Имя клиента:</dt><dd>{{:userName}}</dd></dl>
                    {{/if}}
                    {{if email}}
                        <dl><dt>E-mail:</dt><dd>{{:email}}</dd></dl>
                    {{/if}}
                    {{if tel}}
                        <dl><dt>Телефон:</dt><dd>{{:tel}}</dd></dl>
                    {{/if}}
                    <dl><dt>IP адрес:</dt><dd>{{:userIP}}</dd></dl>
                </div>
                <div class="row">
                    {{if noticed==1}}
                        <dl><dt>Время уведомления:</dt><dd>{{:dt_noticed}}</dd></dl>
                    {{/if}}
                    {{if noticed==5}}
                        <dl><dt>Время заказа:</dt><dd>{{:dt_noticed}}</dd></dl>
                    {{/if}}
                    <dl><dt>Дата заявки:</dt><dd>{{:dt_added}}</dd></dl>
                    <dl><dt>Актуально дней:</dt><dd>{{:days_lifeTime}}</dd></dl>
                </div>
               {{if comment!=''}}
                <div class="row">
                        <dl><dt>Комментарий клиента:</dt><dd>{{:comment}}</dd></dl>
                </div>
                {{/if}}
            </div>
        </div>

    </script>

    <script language="javascript">
        var setup=<?=json_encode($setup)?>;

        $(document).ready(function(){

            new WaitList();

        });
    </script>

<? cp_body()?>
<? cp_title()?>

<? if(empty($setup['wlNotification'])){
    warn('Модуль уведомления о поступлении товаров выключен.');
}else{?>

<div class="wl">
    <div class="hor" style="display: none">

        <label>Категория:</label>
        <select class="gr">
            <option value="0">все</option>
            <option value="1">шины</option>
            <option value="2">диски</option>
        </select>

        <label>Статус заявок:</label>
        <select class="state">
            <option value="1">актуальные по сроку</option>
            <option value="0">все</option>
            <option value="2">с сделанным уведомлением</option>
            <option value="3">с сделаными отсюда заказами</option>
            <option value="4">просроченые</option>
        </select>

        <label>Сортировка:</label>
        <select class="sortBy">
            <option value="1">сначала новые</option>
            <option value="2">сначала старые</option>
        </select>

        <label class="itemsNum"></label>

        <i style="margin-right: 20px"></i>

        <button class="reload">&orarr;</button>

    </div>

    <div class="wrapper">

    </div>

    <div class="more" style="display: none">
        <button class="moreBtn"><span>больше заявок (страница <i>2</i>)</span></button>
    </div>

    <div class="ax1 hide"><img src="/assets/images/ax/siteheart.gif"> </div>
    <div class="ax2 hide"><img src="/assets/images/ax/10.gif"> </div>

    <? note("Настройками сайта включен режим уведомления о поступлении товара: ".($setup['wlNotification']==1?'отправка email на главную почту магазина':'автоматическое размещение заказа'));?>

</div>

<? }?>




<? cp_end()?>