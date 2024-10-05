<?
//die('Overload....');

if(!strpos("xxx".$_SERVER["HTTP_HOST"],"dilijans.org")){
	header('HTTP/1.0 403 Forbidden');
	die();
}

@define(true_enter, 1);

require_once($_SERVER['DOCUMENT_ROOT'] . '/config/init.php');

Request::checkMethod();
Url::hackDetect();
Url::setUrlSuffix(Cfg::get('url_suffix'));
Url::parseUrl();

if (($ns = Namespaces::getNS()) == 'app')
{
    Request::checkRedirect();
    Request::checkDomain();
}

try
{

    call_user_func([Tools::mb_ucfirst($ns) . '_Route', '_getController']);

    $appName = Tools::mb_ucfirst($ns) . '_App';

    $app = new $appName();

    if (method_exists($app->controllerInstance, 'init')) $app->controllerInstance->init();

    if (!$app->execute())
    {
        die($app->getError());
    }

    if (Request::$ajax)
    {
        $app->output();
    }
    else
    {
        if (method_exists($app->controllerInstance, 'preRender')) $app->controllerInstance->preRender();
        $app->output();
    }


} catch (DBException $e)
{
    $e->getError();

} catch (AppException $e)
{
    $e->getError();
}

