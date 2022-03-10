<?
class App_Yml_Controller extends App_Common_Controller {
    public function index() {
        global $app;
        $cc = new CC_Base();
        //
        $n=$cc->cat_view(array(
            'gr'=>'all',
            'start'=>0,
            'lines'=>50,
            'where'=>array($this->minQtyRadiusSQL),
            'order'=>'cc_cat.dt_added DESC, cc_cat.gr ASC'
        ));
        $this->lenta = Array();
        if($n){
            while($cc->next()!==false){
                $v = $cc->qrow;
                $url = '/'.($v['gr'] == 2 ? App_Route::_getUrl('dTipo') : App_Route::_getUrl('tTipo')).'/';
                if ($v['gr'] == 1){
                    $fullSize="{$v['P3']}/{$v['P2']} R{$v['P1']}".($v['csuffix']!=''?" {$v['csuffix']}":'');
                }else{
                    $fullSize=trim("{$v['P2']}x{$v['P5']} {$v['P4']}/{$v['P6']} ET{$v['P1']}".' '.($v['P3']!=0?"DIA {$v['P3']}":''));
                }
                $this->lenta[] = Array(
                    'name' => $v['bname'].' '.$v['mname'].' '.$fullSize,
                    'link' => Cfg::get('site_url').$url.$v['cat_sname'].'.html',
                    'date' => $v['dt_added']
                );
            }
        }
        //
        if (is_file($app->namespace . '/view/yml.php')) {
            extract((array)$app->controllerInstance, EXTR_OVERWRITE);
            extract($app->controllerInstance->_data, EXTR_OVERWRITE);
            include $app->namespace . '/view/yml.php';
        } else
            throw new AppException ('[App::output()]: ' . $app->namespace . '/view/yml.php open fault.');
        unset($cc);
        exit(200);
    }
}