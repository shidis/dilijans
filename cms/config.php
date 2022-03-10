<? require_once 'auth.php';
include('struct.php');

$cp->frm['name']='sconfig';
$cp->frm['title']='Настройки панели';

$cp->checkPermissions();

cp_head();
cp_css();
cp_js();

cp_body();
cp_title();

$users=CU::usersList();

?>
    <script type="text/javascript">

        $(document).ready(function(){

            $('#tabs').tabs();
        });
    </script>

    <p><a href="/cms/"><b>Вернуться в панель управления</b></a></p>


    <div id="tabs">
    <ul>
        <li><a href="#tab-1">Пользователи</a></li>
        <? if(CMS_LEVEL_ACCESS==1){
            ?><li><a href="#tab-2">Пункты меню панели</a></li><?
        }?>
    </ul>

    <style type="text/css">
        .editable-popup1{
            width: 250px;
        }
    </style>

    <div id="tab-1">

        <form method="post" name="form1" action="#tab-1">
            <input type="hidden" name="act" value="">
            <input type="hidden" name="id" value="0">
            <table class="ui-table ltable" id="users">
                <tr>
                    <th>ID</th>
                    <th>Уровень доступа</th>
                    <th>Логин</th>
                    <th>Пароль</th>
                    <th>Имя</th>
                    <th>Фамилия</th>
                    <th>Обрабатывает заказы</th>
                    <th>E-mail</th>
                    <th>Skype</th>
                    <th>ICQ UIN</th>
                    <th>Время жизни сессии (мин)</th>
                    <th>CMS url</th>
                    <th>Включен</th>
                    <th>Кол-во сессий</th>
                    <th>Время последней активности</th>
                    <th>Операции</th>
                    <th>Удалить</th>
                </tr>
                <?
                foreach($users as $k=>$v){?>
                    <tr userId="<?=$k?>">
                        <td align="center"><?=$k?></td>
                        <td align="center" class="role"><?=$v['roleId']?></td>
                        <td align="center" class="login"><?=$v['login']?></td>
                        <td align="center"><a class="pw" href="#">изменить</a></td>
                        <td align="center" class="firstName"><?=$v['firstName']?></td>
                        <td align="center" class="lastName"><?=$v['lastName']?></td>
                        <td align="center" class="os"><?=$v['os']?'разрешено':'запрещено'?></td>
                        <td align="center" class="email"><?=$v['email']?></td>
                        <td align="center" class="skype"><?=$v['skype']?></td>
                        <td align="center" class="icq"><?=$v['icq']?></td>
                        <td align="center" class="lifeTime"><?=$v['lifeTime']?></td>
                        <td align="center" class="cmsStartUrl"><?=$v['cmsStartUrl']?></td>
                        <td align="center"><a href="#" class="disabled-sw"><?=$v['disabled']?'выключен':'включен'?></a></td>
                        <td align="center" class="bold"><?=$v['sCount']?></td>
                        <td align="center" class="bold"><?=$v['lastHit']?></td>
                        <td align="center" nowrap><button class="token-reset">изменить token</button> <button class="logout-all">разлогиниться</button> </td>
                        <td align="center"><button class="user-del">удалить</button></td>
                    </tr>
                <? }?>
            </table>

        </form>

        <style type="text/css">
            #new-user, #new-driver{
                margin-top: 20px;
            }
            #new-user table, #new-driver table{
                border-collapse: collapse;
                width: 800px;
            }
            #new-user th, #new-user td, #new-driver th, #new-driver td{
                padding: 5px 10px;
            }
            #new-user th, #new-driver th{
                width: 150px;
                font-weight: bold;
                text-align: right;
            }
            #new-user td input,  #new-driver td input{
                width: 99%;
            }

        </style>

        <div id="ntabs">

            <ul>
                <li><a href="#new-user">Новый пользователь</a> </li>
                <li><a href="#new-driver">Новый водитель</a> </li>
            </ul>

            <div id="new-user">

                <form>

                    <fieldset class="ui"><legend>Добавить пользователя</legend>

                        <table>
                            <tr>
                                <th>Уровень доступа</th>
                                <td><input type="text" name="roleId"></td>
                            </tr>
                            <tr>
                                <th>Логин</th>
                                <td><input type="text" name="login"></td>
                            </tr>
                            <tr>
                                <th>Пароль</th>
                                <td><input type="password" name="pw"></td>
                            </tr>
                            <tr>
                                <th>Имя</th>
                                <td><input type="text" name="firstName"></td>
                            </tr>
                            <tr>
                                <th>Фамилия</th>
                                <td><input type="text" name="lastName"></td>
                            </tr>
                            <tr>
                                <th>Обрабатывать заказы</th>
                                <td><select name="os"><option value="0">запрещено</option><option value="1">разрешено</option> </select> </td>
                            </tr>
                            <tr>
                                <th>E-Mail</th>
                                <td><input type="text" name="email"></td>
                            </tr>
                            <tr>
                                <th>Skype</th>
                                <td><input type="text" name="skype"></td>
                            </tr>
                            <tr>
                                <th>ICQ UIN</th>
                                <td><input type="text" name="icq"></td>
                            </tr>
                            <tr>
                                <th>Время жизни сессии в минутах</th>
                                <td><input type="text" name="lifeTime" value="<?=CU::$defaultLifeTime?>"></td>
                            </tr>
                            <td></td>
                            <td><button id="add-user">Добавить пользователя</button> </td>

                        </table>

                    </fieldset>

                </form>

            </div>
            <div id="new-driver">

                <form >

                    <fieldset class="ui"><legend>Добавить водителя</legend>

                        <table>
                            <tr>
                                <th>Уровень доступа</th>
                                <td>100</td>
                            </tr>
                            <tr>
                                <th>Имя</th>
                                <td><input type="text" name="firstName"></td>
                            </tr>
                            <tr>
                                <th>Фамилия</th>
                                <td><input type="text" name="lastName"></td>
                            </tr>
                            <tr>
                                <th>E-Mail</th>
                                <td><input type="text" name="email"></td>
                            </tr>
                            <tr>
                                <th>Skype</th>
                                <td><input type="text" name="skype"></td>
                            </tr>
                            <tr>
                                <th>ICQ UIN</th>
                                <td><input type="text" name="icq"></td>
                            </tr>
                            <td></td>
                            <td><button id="add-driver">Добавить водителя</button> </td>

                        </table>

                    </fieldset>

                </form>

            </div>
        </div>
    </div>

    <? if(CMS_LEVEL_ACCESS==1){?>

        <div id="tab-2">

            <?

            if(@$_POST['act']=='add' && @$_POST['title']!='' && @$_POST['gr']!=''&& @$_POST['pos']>0){
                $gr=Tools::esc($_POST['gr']);
                $title=Tools::esc($_POST['title']);
                $class_exists=Tools::esc($_POST['class_exists']);
                $path=Tools::esc($_POST['path']);
                $pos=intval($_POST['pos']);
                if($cp->query("INSERT INTO cp_menu (gr,title,pos,path,class_exists) VALUES('$gr','$title','$pos','$path','$class_exists')")) note("<p>Добавлено <strong>\"$title\"</strong></p>");
                else warn ('<p>Ошибка записи</p>');
            }
            if(@$_POST['act']=='e_post'  && @$_POST['e_title']!='' && @$_POST['e_gr']!=''&& @$_POST['e_pos']>0){
                $gr=Tools::esc($_POST['e_gr']);
                $title=Tools::esc($_POST['e_title']);
                $class_exists=Tools::esc($_POST['e_class_exists']);
                $path=Tools::esc($_POST['e_path']);
                $pos=intval($_POST['e_pos']);
                if(!$cp->query("UPDATE cp_menu SET gr='$gr', title='$title', pos='$pos', path='$path', class_exists='$class_exists' WHERE  menu_id='{$_POST['id']}'"))  warn ('<p>Ошибка записи</p>');
            }
            if(@$_POST['act']=='del' && @$_POST['id']){
                if($cp->query("DELETE FROM cp_menu WHERE menu_id='{$_POST['id']}'")) note ('<p>Удалено</p>');
                else warn ('<p>Ошибка БД</p>');
            }
            if((@$_POST['act']=='show' || @$_POST['act']=='hide') && @$_POST['id']){
                if(!$cp->query("UPDATE cp_menu SET H='".(@$_POST['act']=='hide'?1:0)."' WHERE menu_id='{$_POST['id']}'")) warn ('<p>Ошибка БД</p>');
            }
            ?>

            <form name="form2" method="post" action="#tab-2">
                <input type="hidden" name="id" value="0">
                <input type="hidden" name="act" value="add">
                <table class="ui-table ltable">
                    <tr>
                        <th>gr</th>
                        <th>Title</th>
                        <th>Script Path </th>
                        <th>Levels (not)Permitted</th>
                        <th>Порядок</th>
                        <th colspan="3">&nbsp;</th>
                    </tr>
                    <? $l=0;
                    $cp->getMenuList();
                    if($cp->qnum()) while($cp->next()!==false){?>
                        <tr><td align="center">
                                <? if(@$_POST['act']=='edit' && @$_POST['id']==$cp->qrow['menu_id']){?>
                                    <input name="e_gr" type="text" id="e_gr" size="20" value="<?=$cp->qrow['gr']?>">
                                <? }else echo $cp->qrow['gr'];?>
                            </td>
                            <td>
                                <? if(@$_POST['act']=='edit' && @$_POST['id']==$cp->qrow['menu_id']){?>
                                    <input name="e_title" type="text" id="e_title" style="width:180px" value="<?=$cp->qrow['title']?>">
                                <? }else echo $cp->qrow['title'];?>
                            </td>
                            <td width="20%" align="left">
                                <? if(@$_POST['act']=='edit' && @$_POST['id']==$cp->qrow['menu_id']){?>
                                    <input name="e_path" type="text" id="e_path" style="width:290px" value="<?=$cp->qrow['path']?>">
                                <? }else echo $cp->qrow['path'];?>
                            </td>
                            <td align="center">
                                <? if(@$_POST['act']=='edit' && @$_POST['id']==$cp->qrow['menu_id']){?>
                                    <input name="e_class_exists" type="text" id="e_class_exists" style="width:90px" value="<?=$cp->qrow['class_exists']?>">
                                <? }else echo $cp->qrow['class_exists'];?>
                            </td>
                            <td align="center">
                                <? if(@$_POST['act']=='edit' && @$_POST['id']==$cp->qrow['menu_id']){?>
                                    <input type="text" name="e_pos" style="width:40px; text-align:center" value="<?=$cp->qrow['pos']?>">
                                <? }else echo $cp->qrow['pos'];?>
                            </td>
                            <td align="center">
                                <? if(@$_POST['act']=='edit' && @$_POST['id']==$cp->qrow['menu_id']){?>
                                    <input type="image" src="img/checked.gif" onClick="document.forms['form2'].id.value='<?=$cp->qrow['menu_id']?>'; document.forms['form2'].act.value='e_post';">
                                <? }else{?>
                                    <input name="image" type="image" onClick="document.forms['form2'].id.value='<?=$cp->qrow['menu_id']?>'; document.forms['form2'].act.value='edit';" src="img/b_edit.png">
                                <? }?>
                            </td>
                            <td align="center"><a href="javascript:;" onClick="document.forms['form2'].act.value='<?=$cp->qrow['H']=='1'?'show':'hide'?>'; document.forms['form2'].id.value='<?=$cp->qrow['menu_id']?>';document.forms['form2'].submit()"><?=$cp->qrow['H']==1?'отобразить':'скрыть'?></a></td>
                            <td align="center"><input type="image" src="img/b_drop.png" onClick="if(window.confirm('Вы уверены?')){document.forms['form2'].act.value='del';document.forms['form2'].id.value='<?=$cp->qrow['menu_id']?>'} else return false"></td>
                        </tr>

                    <? }?>

                    <tr>
                        <td align="center"><input name="gr" type="text" id="gr" size="20"></td>
                        <td><input name="title" type="text" id="title" style="width:180px"></td>
                        <td align="center"><input name="path" type="text" id="path" style="width:290px"></td>
                        <td align="center"><input name="class_exists" type="text" id="class_exists" size="6"></td>
                        <td align="center"><input type="text" name="pos" style="width:40px; text-align:center"></td>
                        <td align="center" colspan="4"><input type="image" src="img/add.gif"></td></tr>
                </table>
            </form>


        </div>

    <? }?>

    </div>

<? cp_end();