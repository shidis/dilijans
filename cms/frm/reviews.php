<? 
require_once '../auth.php';
include('../struct.php');

$gr=@$_REQUEST['gr'];
if(empty($gr)) $gr=2;

$cp->frm['name']='reviews';
$cp->frm['title']='Модерация отзывов';

$cp->checkPermissions();

cp_head();
cp_css();
cp_js();

?>
<? cp_body()?>
<? cp_title();

$rev=new Users_Reviews();

$setup=array();
$d=$rev->fetchAll("SELECT postedByAdmin FROM reviews GROUP BY postedByAdmin");

$u=array();
foreach($d as $v) $u[]=$v[0];
$users=CU::usersList(array('users'=>$u));

$d=$rev->getOne("SELECT DATE_FORMAT(min(dt_add),'%d-%m-%Y'), DATE_FORMAT(max(dt_add),'%d-%m-%Y') FROM reviews");
$setup['date1']=$d[0];
$setup['date2']=$d[1];

$setup['revListLimit']=10;

?>

<style type="text/css">

    #rvws{
        min-width: 800px;
    }

    .wrapper{
        position: relative;
    }
    .left{
        position: absolute;
        top:22px;
        left: 0;
        width: 250px;
        margin-bottom: 50px;
    }
    .left .tree{
        overflow: hidden;
    }
    .right{
        float: left;
        margin-top: 50px;
        padding-left: 290px;
        margin-bottom: 50px;;
    }
    .hor{
        overflow: visible;
        box-shadow: 0 0 7px rgba(0,0,0,0.5);
        padding: 6px 15px;
        background: white;
        min-width: 800px;
    }
    .hor label{
        display: inline-block;
        vertical-align: middle;
        margin-right: 10px;
    }
    .hor select{
        display: inline-block;
    }
    .hor .chzn-container{
        vertical-align: middle;
    }
    .hor .dates{
        width: 70px;
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
    ul.fancytree-container{
        border: none;
    }
    ul.fancytree-treefocus{
        border: none;
    }
    .review{
        margin-bottom: 15px;
        overflow: hidden;
    }
    .review .t{
        background: #EAF1FE;
        padding: 10px 10px 0 10px;
        overflow: hidden;
        border-radius: 5px;
    }
    .review .subt{
        float: right;
        overflow: hidden;
        margin-bottom: 10px;
        margin-left: 25px;

    }
    .review .subt .state{
        text-shadow: 0 0 2px rgba(0,0,0,0.5);
        font-weight: bold;
        text-align: center;
        padding: 3px 10px;
        border-radius: 3px;
    }
    .review .subt .state0, .review .subt .state2{
        background: #CC0000;
        color: white;
    }
    .review .subt .state-1{
        background: #336699;
        color: white;
    }
    .review .subt .state1{
        background: #339900;
        color: white;
    }
    .review .it1{
        float: left;
        margin-bottom: 10px;
    }
    .review .it2{
        float: right;
        margin-left: 25px;
        width: 140px;
        margin-bottom: 10px;
    }
    .review .it3{
        float: right;
        margin-left: 25px;
        margin-bottom: 10px;
    }
    .review .wrap{
        margin-top: 17px;
    }
    .review .row{
        margin-bottom: 17px;
        overflow: hidden;
        position: relative;
        clear: both;
    }
    .review .row .l{
        position: absolute;
        left: 0;
        top: 0;
    }
    .review .row .l div{
        width: 100px;
        font-weight: bold;
        text-align: right;

    }
    .review .row .r{
        float: left;
        padding-left: 120px;
    }
    .review .rating .r{
        font-size: 1.1em;
        font-weight: bold;
    }
    .review .rating .r span{
        margin-left: 20px;
        font-size: 1em;
        font-weight: normal;
    }
    .review .row .in{
        display: inline-block;
        margin: 0 25px 17px 0;
    }
    .review .row .approve{
        float: left;
        margin-right: 20px;
    }
</style>

<script id="revItem" type="text/x-jsrender">
<div class="review" rid="{{:id}}">
    <div class="t">
        <span class="it1" title="id={{:id}}"><a href="{{:turl}}" target="_blank">{{:bname}} {{:mname}}</a></span>
        <span class="it2">{{:dt_add}}</span>
        <span class="it3"><b>Автор:</b>
            {{if postedByAdmin!=0}}
                {{:postedBy_shortName}}
            {{else}}
                {{:userName}}
                ({{:userIP}})
            {{/if}}
        </span>
        <div class="subt">
            <div class="state state{{:state}}">
                {{if state==0 || state==2}}
                    не проверен
                {{else state==-1}}
                    отменен
                {{else state==1}}
                    допущен
                {{/if}}
            </div>
        </div>
    </div>
    <div class="wrap">

        <div class="row rating">
            <div class="l"><div>Рейтинг:</div></div>
            <div class="r">{{:rating}} <span>Оценок: {{:valsNum}}</span> </div>
        </div>
        {{if advants}}
        <div class="row">
            <div class="l"><div>Достоинства:</div></div>
            <div class="r">{{:_advants}}</div>
        </div>
        {{/if}}
        {{if defects}}
        <div class="row">
            <div class="l"><div>Недостатки:</div></div>
            <div class="r">{{:_defects}}</div>
        </div>
        {{/if}}
        {{if comment!=''}}
        <div class="row">
            <div class="l"><div>Комментарий:</div></div>
            <div class="r">{{:_comment}}</div>
        </div>
        {{/if}}

        {{if __af}}
            <div class="row"><a href="#" class="dinfo">Дополнительная информация</a></div>

            <div class="dinfo-wrap" style="display: none">
                <div class="row">
                    {{if postedByAdmin==1}}
                        <div class="in"><b>Имя:</b> {{:userName}}</div>
                    {{/if}}
                    {{for __af}}
                        <div class="in"><b>{{:caption}}</b> {{:v}}</div>
                    {{/for}}
                </div>

                <div class="row moder" {{if cUserId==0}}style="display:none"{{/if}}>
                    <div class="l"><div>Отмодерирован:</div></div>
                    <div class="r">
                        <span style="margin-right: 30px" class="cuName">{{:cUser_shortName}}</span>
                        <span class="cuStateDt">{{:dt_state}}</span>
                    </div>
                </div>
            </div>
        {{/if}}

        {{if CM}}
            <div class="row moderBtns">
                {{if state!=1}}
                    <button class="approve">Проверено!</button>
                {{/if}}
                {{if state!=-1}}
                    <button class="cancel">Отказать</button>
                {{/if}}
            </div>
        {{/if}}

    </div>
</div>
</script>

<div id="rvws">
    <div class="hor" style="display: none">
        <label>Статус отзывов:</label>
        <select class="state">
            <option value="">все</option>
            <option value="0">не проверенные</option>
            <option value="1">одобренные</option>
            <option value="-1">отмененные</option>
        </select>

        <i style="margin-right: 20px"></i>

        <label>Авторы отзывов: </label>
        <select class="postedBy">
            <option value="">все</option>
            <option value="-1">админы сайта</option>
            <option value="-2">пользователи</option>
            <? if(!empty($users)){?>
            <optgroup label="Авторы">
                <? foreach($users as $k=>$v){?>
                <option value="<?=$k?>"><?=$v['shortName']?></option>
                <? }?>
            </optgroup>
            <? }?>
        </select>

        <i style="margin-right: 20px"></i>

        <label>Диапазон дат:</label>
        <input class="date1 dates" value="<?=$setup['date1']?>">
        <b>-</b >
        <input class="date2 dates" value="<?=$setup['date2']?>">
        <button class="reload">&orarr;</button>

        <i style="margin-right: 20px"></i>

        <label title="Подсчет ведется в полях: достоинства, недостатки, коммент.">Кол-во символов:</label>
        <span class="symbols">-1</span>

    </div>

    <div class="wrapper">

        <div class="left">

            <fieldset class="ui" style="margin: 20px 0 10px; border-color: #EEE; padding: 10px 0 10px"><legend style="color: #000000; font-weight: bold">Модели</legend>
                <div class="tree"></div>
            </fieldset>

        </div>

        <div class="right"></div>

    </div>

    <div class="more" style="display: none">
        <button class="moreBtn"><span>больше отзывов (страница <i>2</i>)</span></button>
    </div>

    <div class="ax1 hide"><img src="/assets/images/ax/siteheart.gif"> </div>
    <div class="ax2 hide"><img src="/assets/images/ax/10.gif"> </div>

</div>

<script type="text/javascript">
    var setup=<?=json_encode($setup)?>;

    $(document).ready(function(){

        var rvws = new Reviews();
    });

</script>


<? cp_end()?>
