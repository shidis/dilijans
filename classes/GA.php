<?
class GA
{
    // установить в null если админов тоже счиатать будем
    static private $forAdmin=false;
    static public $remoteOnly=true;
    /*
    static public $pushList=array(
        "['_addOrganic', 'images.yandex.ru','q',true]",
        "['_addOrganic', 'blogsearch.google.ru','q',true]",
        "['_addOrganic', 'blogs.yandex.ru','text', true]",
        "['_addOrganic', 'go.mail.ru','q']",
        "['_addOrganic', 'nova.rambler.ru','query']",
        "['_addOrganic', 'nigma.ru','s']",
        "['_addOrganic', 'tut.by','query']",
        "['_addOrganic', 'ukr.net','search_query']",
        "['_addOrganic', 'search.com.ua','q']",
        "['_addOrganic', 'search.ua','query']",
        "['_addOrganic', 'search.ukr.net','search_query']",
        "['_addOrganic', 'webalta.ru','q']",
        "['_addOrganic', 'search.qip.ru','query']",
        "['_addOrganic', 'ru.yahoo.com','p']",
        "['_addOrganic', 'market.yandex.ru','text', true]"
    );
    */
    static public $pushList=array();
    static private $events=array();
    static private $attrEnhance=false;
    static private $subDomainEnable=false;
    static private $trans=array();
    static public $errorEvent=array('Interface','GA:error');
    static private $doubleClickCode=false;
    static private $customVars=array();

    //https://developers.google.com/analytics/devguides/collection/gajs/methods/gaJSApiEventTracking

    static function event($category='', $action='', $opt_label='', $opt_value='', $opt_noninteraction=true)
    {
        if($category!='' && $action!=''){
            $v=array(
                'pageView'=>true,
                'category'=>$category,
                'action'=>$action,
                'opt_label'=>$opt_label===''?"$action;":(string)$opt_label
            );
            if($opt_value!=='') $v['opt_value']=(int)$opt_value;
            if($opt_noninteraction) {
                if($opt_value==='') $v['opt_value']='undefined';
                $v['opt_noninteraction']=true;
            }
            static::$events[]=$v;
        }
    }

    static function _event($category='', $action='', $opt_label='', $opt_value='', $opt_noninteraction=true)
    {
        if($category!='' && $action!=''){
            $v=array(
                'category'=>(string)$category,
                'action'=>(string)$action,
                'opt_label'=>$opt_label===''?"$action;":(string)$opt_label
            );
            if($opt_value!=='') $v['opt_value']=(int)$opt_value;
            if($opt_noninteraction) {
                if($opt_value==='') $v['opt_value']='undefined';
                $v['opt_noninteraction']=true;
            }
            static::$events[]=$v;
        }
    }

    /*
     *  $slot = {1-5}
     *  $level : 1 (visitor-level), 2 (session-level), or 3 (page-level)
     */
    public static function setVar($slot, $name, $v, $level=3)
    {
        static::$customVars[$name]=array($slot*1, $name.'', $v.'', $level*1);
    }


    static function adminCounter($forAdmin)
    {
        static::$forAdmin=$forAdmin;
    }

    static function attrEnhance($state)
    {
        static::$attrEnhance=$state;
    }

    static function subDomainsEnable($state)
    {
        static::$subDomainEnable=$state;
    }

    static function getCounter()
    {
        if(server_loc!='remote' && static::$remoteOnly  || !static::$forAdmin && CU::isLogged()) return '';

        $s="<script type=\"text/javascript\">\n"
            ."var _gaq = _gaq || [];\n";
        if(static::$attrEnhance) {
            $s.="var pluginUrl ='//www.google-analytics.com/plugins/ga/inpage_linkid.js';\n"
                ."_gaq.push(['_require', 'inpage_linkid', pluginUrl]);\n";
        }
        $s.="_gaq.push(['_setAccount', '".Cfg::$config['GA']['account']."']);\n";

        if(static::$subDomainEnable) $s.="_gaq.push(['_setDomainName', '".Cfg::$config['GA']['domainName']."']);\n";

        $s.="_gaq.push(['_trackPageview']);\n";

        foreach(static::$pushList as $v)
            $s.="_gaq.push({$v});\n";

        foreach(static::$customVars as $v) {
            $s.="_gaq.push(['_setCustomVar', {$v[0]}, '{$v[1]}', '{$v[2]}', {$v[3]}]);\n";
        }

        foreach(static::$events as $v) {

            $s.="_gaq.push(['_trackEvent', '{$v['category']}', '{$v['action']}', '{$v['opt_label']}'".(isset($v['opt_value']) || @$v['opt_noninteraction']?", {$v['opt_value']}":'').(@$v['opt_noninteraction']?", true":'')."]);\n";

            if(@$v['pageView'])
                $s.="_gaq.push(['_trackPageview', '/{$v['category']}/{$v['action']}".($v['opt_label']!=''?"/{$v['opt_label']}":'').".event']);\n";

        }

        // для аякс корзин никогда не используется
        if(!empty(static::$trans)){
            $vv=static::$trans;

            if(!empty($vv['customerPType']))
                $s.="_gaq.push(['_trackEvent',{$vv['GA_customVarsSlot']},'customerPType','{$vv['customerPType']}',1]);\n";

            $s.="_gaq.push(['_addTrans', '{$vv['transId']}', '', '{$vv['total']}',  '', '{$vv['shipping']}', '{$vv['city']}', '', '{$vv['country']}']);\n";

            foreach(static::$trans['items'] as $v){
                $s.="_gaq.push(['_addItem', '{$vv['transId']}', '{$v['SKU']}', '{$v['name']}', '{$v['category']}', '{$v['price']}', '{$v['quantity']}']);\n";
            }
            $s.="_gaq.push(['_trackTrans']);\n";
        }

        $s.="\n(function() {".
            "var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;".
            (static::$doubleClickCode
                ?
                "ga.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'stats.g.doubleclick.net/dc.js';"
                :
                "ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';"
            ).
            "var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);".
            "})();";

        $s.="\n</script>\n";

        return $s;

    }

    /*
     * транзакция: заказ отравлен
     */
    static function trans($a=array())
    {
        static::$trans=array();
        if(empty($a['order_num'])) return static::putErr('Transaction:*order_num* empty');
        if(!isset($a['bcost'])) return static::putErr('Transaction:*bcost* not set');
        if(empty($a['list'])) return static::putErr('Transaction:*items[]* empty');

        static::$trans['transId']=(string)$a['order_num'];
        static::$trans['total']=(string)$a['bcost'];
        static::$trans['shipping']=(string)@$a['delivery_cost'];
        static::$trans['city']=(string)@$a['city'];
        static::$trans['country']='Россия';
        if(isset($a['ptype'])){
            static::$trans['customerPType']=$a['ptype']==1?'urik':'fizik';
            static::$trans['GA_customVarsSlot']=!empty($a['GA_customVarsSlot'])?$a['GA_customVarsSlot']:1;
        }

        static::$trans['items']=array();
        foreach($a['list'] as $k=>$v){
            static::$trans['items'][]=array(
                'transId'=>(string)$a['order_num'],
                'SKU'=>(string)@$v['cat_id'],
                'name'=>Tools::esc(@$v['name']),
                'category'=>@$v['gr']==1?'Шины':'Диски',
                'quantity'=>(string)@$v['amount'],
                'price'=>(string)@$v['_price']
            );
        }
        return static::$trans;

    }

    private static function putErr($estr)
    {
        static::_event(static::$errorEvent[0], static::$errorEvent[1], $estr.'');
        static::$errorEvent[2]=$estr;
        return false;
    }

    public static function doubleClick($enabled)
    {
        static::$doubleClickCode=$enabled;
    }

}