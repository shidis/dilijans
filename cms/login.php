<? require_once ($_SERVER['DOCUMENT_ROOT'].'/config/init.php'); 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>CMS: <?=strtoupper(str_replace('www.','',Cfg::get('site_url')))?> - Авторизация</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link href="/cms/css/login.css" rel="stylesheet" type="text/css" />
    <script src="/assets/js/jquery.min.js" type="text/javascript"></script>
    <script src="/cms/js/lib/jquery.easing.js" type="text/javascript"></script>
	<script src="/assets/js/func.lib.js" type="text/javascript"></script>
	<SCRIPT language=JavaScript src="/cms/js/ax_global.js" type=text/javascript></SCRIPT>

    <script src="/cms/js/comp/login.js" type="text/javascript"></script>
<style type="text/css">
.remindPass{
}
.signup{
}
</style>
</head>
<body>
    <div id="vrWrapper">
        <div id="wr" class="wr">
            <div class='loginBlock' id="call-shaman">
                <p align="center"><a href="http://webmaster.yandex.ru/"><img src="/cms/img/shaman.jpg" width="200" border="1" title="это Платон Щукин - у него есть бубен!" /></a></p>
                <div id="error0" class="error displaynone"></div>
                <div class='additional'>
                    <a href="#" class='remindPass'>Вспомнить пароль</a><a href="#" class='signin'>Войти</a></div>
            </div>
            
            
            <div class='loginBlock' id="signin">
                <form id="signin-form" action="" method="post">
                    <label for="email">Логин:</label>
    
                    <input id="login" name="login" type="text" class='textinput' />
                    
                    <label for="password">Пароль:</label>
                    <input id="pw" name="pw" type="password" class='textinput' />
                    
                    <div id="error1" class="error displaynone"></div>
                    
                    <div class='buttonDiv'>
                        <input id="signin-but" type="submit" value="Войти" />
                    </div>
                    
                </form>
            </div>
            
            
            
            <div class='loginBlock' id="remindPass">
                <div class="description">
                    Чтобы вспомнить пароль, вспомните для начала хотя бы email.
                </div>

                <label for="email">
                    Email:</label>
                <input id="remindEmail" type="text" class='textinput' />
                <div id="error2" class="error displaynone">
                </div>
                <div id="message0" class="message displaynone">
                </div>
                <div class='buttonDiv'>

                    <input id="Button2" type="button" value="Выслать пароль" onclick="RemindPassword()" /></div>
                <div class='additional'>
                    <a href="#" class='signin'>Войти</a><a href="#" class='call-shaman'>Вызвать шамана</a></div>
            </div>
        </div>
    </div>
</body>
</html>
