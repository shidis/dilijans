<?
class App_Route extends Route {

    static $actions=array(

        'tSearch'=>		array('ord'=>0,'url'=>'t_filter',								'spathLength'=>1,	'noGET'=>false),
        'dSearch'=>		array('ord'=>0,'url'=>'d_filter',			    				'spathLength'=>1,	'noGET'=>false),

        'tCat'=>		array('ord'=>0,'url'=>'tb',						    			'spathLength'=>3,	'noGET'=>false),
        'tWinter'=>		array('ord'=>0,'url'=>'zimnie_shiny',							'spathLength'=>3,	'noGET'=>false),
        'tSummer'=>		array('ord'=>0,'url'=>'letnie_shiny',							'spathLength'=>3,	'noGET'=>false),
        'tAllW'=>		array('ord'=>0,'url'=>'vsesezonnie_shiny',						'spathLength'=>3,	'noGET'=>false),

        'tSUV'=>		array('ord'=>0,'url'=>'shiny-dlya-vnedorozhnikov',				'spathLength'=>3,	'noGET'=>true),
        'tSummerSUV'=>	array('ord'=>0,'url'=>'letnie-shiny-dlya-vnedorozhnikov',		'spathLength'=>3,	'noGET'=>true),
        'tWinterSUV'=>	array('ord'=>0,'url'=>'zimnie-shiny-dlya-vnedorozhnikov',		'spathLength'=>3,	'noGET'=>true),
        'tAllWSUV'=>	array('ord'=>0,'url'=>'vsesezonnie-shiny-dlya-vnedorozhnikov',	'spathLength'=>3,	'noGET'=>true),

        'tLight'=>		array('ord'=>0,'url'=>'legkovie-shiny',         				'spathLength'=>3,	'noGET'=>true),
        'tSummerLight'=>array('ord'=>0,'url'=>'letnie-legkovie-shiny',         			'spathLength'=>3,	'noGET'=>true),
        'tWinterLight'=>array('ord'=>0,'url'=>'zimnie-legkovie-shiny',         			'spathLength'=>3,	'noGET'=>true),
        'tAllWLight'=>	array('ord'=>0,'url'=>'vsesezonnie-legkovie-shiny',     		'spathLength'=>3,	'noGET'=>true),

        'tStrong'=>		array('ord'=>0,'url'=>'usilennie-shiny',         				'spathLength'=>3,	'noGET'=>true),
        'tSummerStrong'=>array('ord'=>0,'url'=>'letnie-usilennie-shiny',            	'spathLength'=>3,	'noGET'=>true),
        'tWinterStrong'=>array('ord'=>0,'url'=>'zimnie-usilennie-shiny',        		'spathLength'=>3,	'noGET'=>true),
        'tAllWStrong'=>	array('ord'=>0,'url'=>'vsesezonnie-usilennie-shiny',  			'spathLength'=>3,	'noGET'=>true),

        'tShip'=>       array('ord'=>0,'url'=>'zimnie-shipovanye-shiny',                'spathLength'=>3,	'noGET'=>true),
        'tNeShip'=>       array('ord'=>0,'url'=>'zimnie-neshipovanye-shiny',           'spathLength'=>3,	'noGET'=>true),
        'tSUVShip'=>       array('ord'=>0,'url'=>'zimnie-shipovanye-shiny-dlya-vnedorozhnikov',                'spathLength'=>3,	'noGET'=>true),
        'tSUVNeShip'=>       array('ord'=>0,'url'=>'zimnie-neshipovanye-shiny-dlya-vnedorozhnikov',           'spathLength'=>3,	'noGET'=>true),

        'tModel'=>	    array('ord'=>0,'url'=>'shiny',	    			    			'spathLength'=>2,	'noGET'=>false),
        'tTipo'=>	    array('ord'=>0,'url'=>'shina',	    			    			'spathLength'=>2,	'noGET'=>false),

        'tBySize'=>	    array('ord'=>0,'url'=>'shiny-po-razmeru',	  	    			'spathLength'=>1,	'noGET'=>true),
        'dBySize'=>	    array('ord'=>0,'url'=>'diski-po-razmeru',	   	    			'spathLength'=>1,	'noGET'=>true),

        'dCat'=>		array('ord'=>0,'url'=>'db',							    		'spathLength'=>2,	'noGET'=>false),
        'dShtamp'=>		array('ord'=>0,'url'=>'shtampovannye-diski',					'spathLength'=>2,	'noGET'=>false),
        'dKovanye'=>	array('ord'=>0,'url'=>'kovanye-diski',							'spathLength'=>2,	'noGET'=>false),
        'replicaCat'=>	array('ord'=>0,'url'=>'replica',	    						'spathLength'=>2,	'noGET'=>false),
        'dModel'=>	    array('ord'=>0,'url'=>'diski',	    			    			'spathLength'=>2,	'noGET'=>false),
        'dTipo'=>	    array('ord'=>0,'url'=>'disk',	    			    			'spathLength'=>2,	'noGET'=>false),
        // ***
        'avtoPodborShin'=>          array('ord'=>0,'url'=>'podbor-shin',                'spathLength'=>2,    'noGET'=>false),
        'avtoPodborDiskovIndex'=>   array('ord'=>0,'url'=>'podbor_sd',                  'spathLength'=>1,    'noGET'=>true),
        'avtoPodborDiskov'=>        array('ord'=>0,'url'=>'avto-podbor',                'spathLength'=>2,    'noGET'=>false),
        'ajaxAvtoPodbor'=>	        array('ord'=>0,'url'=>'ajax-podbor',       			'spathLength'=>2,	 'noGET'=>true),  

        'byCity'=>	    array('ord'=>0,'url'=>'avtoshiny-i-litie-diski',				'spathLength'=>2,	'noGET'=>true),

        'page'=>		array('ord'=>0,'url'=>'i',										'spathLength'=>2,	'noGET'=>true),

        'novinkiSezona'=>array('ord'=>0,'url'=>'novinki-sezona',						'spathLength'=>2,	'noGET'=>true),
        'articles'=>	array('ord'=>0,'url'=>'articles',				    			'spathLength'=>2,	'noGET'=>false),
        'entrysection'=>array('ord'=>0,'url'=>'entrysection',				    		'spathLength'=>2,	'noGET'=>false),
        'entry'=>array('ord'=>0,'url'=>'entry',				    		            'spathLength'=>2,	'noGET'=>false),
        'news'=>		array('ord'=>0,'url'=>'lenta',									'spathLength'=>2,	'noGET'=>false),
        'faq'=>		    array('ord'=>0,'url'=>'faq',									'spathLength'=>1,	'noGET'=>false),

        'cart'=>		array('ord'=>0,'url'=>'cart',									'spathLength'=>2,	'noGET'=>false),
        'search'=>		array('ord'=>0,'url'=>'search',									'spathLength'=>1,	'noGET'=>false),
        'ax'=>			array('ord'=>0,'url'=>'ax',										'spathLength'=>3,	'noGET'=>false),
        'yaSearch'=>	array('ord'=>0,'url'=>'yasearch',								'spathLength'=>1,	'noGET'=>false),
        'compare'=>	    array('ord'=>0,'url'=>'compare',								'spathLength'=>2,	'noGET'=>true),
        'sitemap'=>	    array('ord'=>0,'url'=>'sitemaps',						   		'spathLength'=>2,	'noGET'=>true),
        'scalc'=>		array('ord'=>0,'url'=>'s_calc',					    			'spathLength'=>1,	'noGET'=>true),

        'rss'=>		    array('ord'=>0,'url'=>'rss',					    			'spathLength'=>1,	'noGET'=>true),
        'yml'=>		    array('ord'=>0,'url'=>'yml',					    			'spathLength'=>1,	'noGET'=>true)

    );

    static function ax()
    {
        //sleep(1);
        //        Request::$ajax=true;
        if(!Request::$ajax || @Url::$spath[2]=='') return static::redir404();
        switch(Url::$spath[2]){
            case 'scl': return 'ax/scl'; break;
            case 'geoCity': return 'ax/geoCity'; break;
            case 'subscribe': return 'ax/subscribe'; break;
            case 'feedback': return 'ax/feedback'; break;
            case 'calculateTyres': return 'ax/calculateTyres'; break;
                //case 'delveryCostByCity': return 'ax/delveryCostByCity'; break;
            case 'callback': return 'ax/callback'; break;
            case 'announce': return 'ax/announce'; break;
            case 'regionDelivery': return 'ax/regionDeliveryCost'; break;
            case 'galleryForm':
                Request::ajaxMethod('html');
                return 'ax/galleryForm';
                break;
            case 'callbackForm':
                Request::ajaxMethod('html');
                return 'ax/callbackForm';
                break;            
            case 'announceForm':
                Request::ajaxMethod('html');
                return 'ax/announceForm';
                break;
            case 'tip':
                Request::ajaxMethod('html');
                return 'ax/tip';
                break;
            case 'explain':
                Request::ajaxMethod('html');
                return 'ax/explain';
                break;
            case 'reviewForm':
                Request::ajaxMethod('html');
                return 'catalog/tyres/common/getReviewForm';
                break;
            case 'modReviewFormHtml':
                Request::ajaxMethod('html');
                return 'catalog/tyres/common/getModReviewForm';
                break;
            case 'getReviewHtml':
                Request::ajaxMethod('html');
                return 'catalog/tyres/common/getReviewHtml';
                break;
            case 'getReviewsHtml':
                Request::ajaxMethod('html');
                return 'catalog/tyres/common/getReviewsHtml';
                break;
            case 'postReview':
                return 'catalog/tyres/common/postReview';
                break;
            case 'delReview':
                return 'catalog/tyres/common/delReview';
                break;
            case 'quickOrderForm':
                return 'cart/quickOrderForm';
                break;
            case 'quickOrderSend':
                return 'cart/quickOrderSend';
                break;
            default: return static::redir404();
        }
    }

    static function byCity()
    {
        if(Request::$ajax) return static::redir404();
        if(empty(Url::$spath[2])) return static::redir404();
        return 'page/city';
    }


    static function tSearch()
    {
        static::$param['gr']=1;
        if(Request::$ajax) {
            // Костыль для отображения каталога через ajax
            if (@Url::$sq['ajax_tsort']) {
                return 'catalog/tyres/search/axView';
            } else return 'catalog/tyres/search/axSearch';
        }
        else return 'catalog/tyres/search/search';
    }

    static function dSearch()
    {
        static::$param['gr']=2;
        if(Request::$ajax) {
            // Костыль для отображения каталога через ajax
            if (@Url::$sq['ajax_tsort']) {
                return 'catalog/disks/search/axView';
            } else return 'catalog/disks/search/axSearch';
        }
        else return 'catalog/disks/search/search';
    }

    static function tCat()
    {
        if(Request::$ajax) return static::redir404();
        static::$param['gr']=1;
        if(preg_match("~^R([0-9\.]{2,})$~",@Url::$spath[3],$m)) static::$param['radius']=$m[1];
        if(@Url::$spath[2]!='') return 'catalog/tyres/models/index';
        else return 'catalog/tyres/brands/index';
    }
    static function tWinter()
    {
        if(Request::$ajax) return static::redir404();
        static::$param['gr']=1;
        static::$param['M1']=2;
        if(preg_match("~^R([0-9\.]{2,})$~",@Url::$spath[3],$m)) static::$param['radius']=$m[1];
        if(@Url::$spath[2]!='') return 'catalog/tyres/models/index';
        else return 'catalog/tyres/brands/bySezon';
    }

    static function tSummer()
    {
        if(Request::$ajax) return static::redir404();
        static::$param['gr']=1;
        static::$param['M1']=1;
        if(preg_match("~^R([0-9\.]{2,})$~",@Url::$spath[3],$m)) static::$param['radius']=$m[1];
        if(@Url::$spath[2]!='') return 'catalog/tyres/models/index';
        else return 'catalog/tyres/brands/bySezon';
    }
    static function tAllW()
    {
        if(Request::$ajax) return static::redir404();
        static::$param['gr']=1;
        static::$param['M1']=3;
        if(preg_match("~^R([0-9\.]{2,})$~",@Url::$spath[3],$m)) static::$param['radius']=$m[1];
        if(@Url::$spath[2]!='') return 'catalog/tyres/models/index';
        else return 'catalog/tyres/brands/bySezon';
    }



    static function tSUV()
    {
        if(Request::$ajax) return static::redir404();
        static::$param['gr']=1;
        static::$param['M2']=2;
        if(preg_match("~^R([0-9\.]{2,})$~",@Url::$spath[3],$m)) static::$param['radius']=$m[1];
        if(@Url::$spath[2]!='') return 'catalog/tyres/models/index';
        else return 'catalog/tyres/brands/bySUV';
    }
    static function tWinterSUV()
    {
        if(Request::$ajax) return static::redir404();
        static::$param['gr']=1;
        static::$param['M1']=2;
        static::$param['M2']=2;
        if(preg_match("~^R([0-9\.]{2,})$~",@Url::$spath[3],$m)) static::$param['radius']=$m[1];
        if(@Url::$spath[2]!='') return 'catalog/tyres/models/index';
        else return 'catalog/tyres/brands/bySezonSUV';
    }
    static function tSummerSUV()
    {
        if(Request::$ajax) return static::redir404();
        static::$param['gr']=1;
        static::$param['M1']=1;
        static::$param['M2']=2;
        if(preg_match("~^R([0-9\.]{2,})$~",@Url::$spath[3],$m)) static::$param['radius']=$m[1];
        if(@Url::$spath[2]!='') return 'catalog/tyres/models/index';
        else return 'catalog/tyres/brands/bySezonSUV';
    }
    static function tAllWSUV()
    {
        if(Request::$ajax) return static::redir404();
        static::$param['gr']=1;
        static::$param['M1']=3;
        static::$param['M2']=2;
        if(preg_match("~^R([0-9\.]{2,})$~",@Url::$spath[3],$m)) static::$param['radius']=$m[1];
        if(@Url::$spath[2]!='') return 'catalog/tyres/models/index';
        else return 'catalog/tyres/brands/bySezonSUV';
    }

    static function tSUVShip()
    {
        if(Request::$ajax) return static::redir404();
        static::$param['gr']=1;
        static::$param['M1']=2;
        static::$param['M2']=2;
        static::$param['M3']=1;
        if(preg_match("~^R([0-9\.]{2,})$~",@Url::$spath[3],$m)) static::$param['radius']=$m[1];
        if(@Url::$spath[2]!='') return 'catalog/tyres/models/index';
        else return 'catalog/tyres/brands/bySeazonSUV';
    }

    static function tSUVNeShip()
    {
        if(Request::$ajax) return static::redir404();
        static::$param['gr']=1;
        static::$param['M1']=2;
        static::$param['M2']=2;
        static::$param['M3']=0;
        if(preg_match("~^R([0-9\.]{2,})$~",@Url::$spath[3],$m)) static::$param['radius']=$m[1];
        if(@Url::$spath[2]!='') return 'catalog/tyres/models/index';
        else return 'catalog/tyres/brands/bySeazonSUV';
    }

    static function tShip()
    {
        if(Request::$ajax) return static::redir404();
        static::$param['gr']=1;
        static::$param['M1']=2;
        static::$param['M3']=1;
        if(preg_match("~^R([0-9\.]{2,})$~",@Url::$spath[3],$m)) static::$param['radius']=$m[1];
        if(@Url::$spath[2]!='') return 'catalog/tyres/models/index';
        else return 'catalog/tyres/brands/byShip';
    }

    static function tNeShip()
    {
        if(Request::$ajax) return static::redir404();
        static::$param['gr']=1;
        static::$param['M1']=2;
        static::$param['M3']=0;
        if(preg_match("~^R([0-9\.]{2,})$~",@Url::$spath[3],$m)) static::$param['radius']=$m[1];
        if(@Url::$spath[2]!='') return 'catalog/tyres/models/index';
        else return 'catalog/tyres/brands/byShip';
    }



    static function tLight()
    {
        if(Request::$ajax) return static::redir404();
        static::$param['gr']=1;
        static::$param['M2']=1;
        if(preg_match("~^R([0-9\.]{2,})$~",@Url::$spath[3],$m)) static::$param['radius']=$m[1];
        if(@Url::$spath[2]!='') return 'catalog/tyres/models/index';
        else return 'catalog/tyres/brands/byLight';
    }
    static function tWinterLight()
    {
        if(Request::$ajax) return static::redir404();
        static::$param['gr']=1;
        static::$param['M1']=2;
        static::$param['M2']=1;
        if(preg_match("~^R([0-9\.]{2,})$~",@Url::$spath[3],$m)) static::$param['radius']=$m[1];
        if(@Url::$spath[2]!='') return 'catalog/tyres/models/index';
        else return 'catalog/tyres/brands/bySezonLight';
    }
    static function tSummerLight()
    {
        if(Request::$ajax) return static::redir404();
        static::$param['gr']=1;
        static::$param['M1']=1;
        static::$param['M2']=1;
        if(preg_match("~^R([0-9\.]{2,})$~",@Url::$spath[3],$m)) static::$param['radius']=$m[1];
        if(@Url::$spath[2]!='') return 'catalog/tyres/models/index';
        else return 'catalog/tyres/brands/bySezonLight';
    }
    static function tAllWLight()
    {
        if(Request::$ajax) return static::redir404();
        static::$param['gr']=1;
        static::$param['M1']=3;
        static::$param['M2']=1;
        if(preg_match("~^R([0-9\.]{2,})$~",@Url::$spath[3],$m)) static::$param['radius']=$m[1];
        if(@Url::$spath[2]!='') return 'catalog/tyres/models/index';
        else return 'catalog/tyres/brands/bySezonLight';
    }




    static function tStrong()
    {
        if(Request::$ajax) return static::redir404();
        static::$param['gr']=1;
        static::$param['M2']=3;
        if(preg_match("~^R([0-9\.]{2,})$~",@Url::$spath[3],$m)) static::$param['radius']=$m[1];
        if(@Url::$spath[2]!='') return 'catalog/tyres/models/index';
        else return 'catalog/tyres/brands/byStrong';
    }
    static function tWinterStrong()
    {
        if(Request::$ajax) return static::redir404();
        static::$param['gr']=1;
        static::$param['M1']=2;
        static::$param['M2']=3;
        if(preg_match("~^R([0-9\.]{2,})$~",@Url::$spath[3],$m)) static::$param['radius']=$m[1];
        if(@Url::$spath[2]!='') return 'catalog/tyres/models/index';
        else return 'catalog/tyres/brands/bySezonStrong';
    }
    static function tSummerStrong()
    {
        if(Request::$ajax) return static::redir404();
        static::$param['gr']=1;
        static::$param['M1']=1;
        static::$param['M2']=3;
        if(preg_match("~^R([0-9\.]{2,})$~",@Url::$spath[3],$m)) static::$param['radius']=$m[1];
        if(@Url::$spath[2]!='') return 'catalog/tyres/models/index';
        else return 'catalog/tyres/brands/bySezonStrong';
    }
    static function tAllWStrong()
    {
        if(Request::$ajax) return static::redir404();
        static::$param['gr']=1;
        static::$param['M1']=3;
        static::$param['M2']=3;
        if(preg_match("~^R([0-9\.]{2,})$~",@Url::$spath[3],$m)) static::$param['radius']=$m[1];
        if(@Url::$spath[2]!='') return 'catalog/tyres/models/index';
        else return 'catalog/tyres/brands/bySezonStrong';
    }


    static function tModel()
    {
        if(Request::$ajax) return static::redir404();
        static::$param['gr']=1;
        if(@Url::$spath[2]!='') return 'catalog/tyres/model/index';
        else return static::redir404();
    }

    static function tTipo()
    {
        // Костыль для отображения каталога через ajax
        if((Request::$ajax && @Url::$sq['ajax_tsort']) || (Request::$ajax && @Url::$spath[2]!='' && !empty($_REQUEST['bid']))) {
            return 'catalog/tyres/tipo/axView';
        }
        elseif(Request::$ajax) return static::redir404();

        static::$param['gr']=1;
        if(@Url::$spath[2]!='') return 'catalog/tyres/tipo/index';
        else return static::redir404();
    }




    static function dCat()
    {
        static::$param['gr']=2;
        static::$param['d_type']=2;
        if(Request::$ajax && @Url::$sq['ajax_tsort']) {
            return 'catalog/disks/models/axModels';
        }
        elseif (Request::$ajax) return static::redir404();
        if(@Url::$spath[2]!='') return 'catalog/disks/models/index';
        else return 'catalog/disks/brands/index';

    }

    static function dShtamp()
    {
        static::$param['gr']=2;
        static::$param['d_type']=3;
        if(Request::$ajax && @Url::$sq['ajax_tsort']) {
            return 'catalog/disks/skmodels/axModels';
        }elseif(Request::$ajax) return 'catalog/disks/skmodels/axSearch';
        else return 'catalog/disks/skmodels/search';

    }

    static function dKovanye()
    {
        static::$param['gr']=2;
        static::$param['d_type']=1;
        if(Request::$ajax && @Url::$sq['ajax_tsort']) {
            return 'catalog/disks/skmodels/axModels';
        }elseif(Request::$ajax) return 'catalog/disks/skmodels/axSearch';
        else return 'catalog/disks/skmodels/search';
    }

    static function replicaCat()
    {
        if(Request::$ajax) return static::redir404();
        static::$param['replica']=1;
        static::$param['gr']=2;
        if(@Url::$spath[2]!='') return static::redir404();
        else return 'catalog/disks/brands/replica';
    }


    static function dModel()
    {
        if(Request::$ajax) return static::redir404();
        static::$param['gr']=2;
        if(@Url::$spath[2]!='') return 'catalog/disks/model/index';
        else return static::redir404();
    }

    static function dTipo()
    {
        // Костыль для отображения каталога через ajax
        if ((Request::$ajax && @Url::$sq['ajax_tsort']) || (Request::$ajax && @Url::$spath[2]!='' && !empty($_REQUEST['bid']))){
                return 'catalog/disks/tipo/axView';
        }
        elseif(Request::$ajax) return static::redir404();

        static::$param['gr']=2;
        if(@Url::$spath[2]!='') return 'catalog/disks/tipo/index';
        else return static::redir404();
    }



    static function scalc()
    {
        if(Request::$ajax) return static::redir404();
        return 'calculator/index';
    }

    static function avtoPodborShin()
    {
        static::$param['gr']=1;
        if(!empty(Url::$spath[2]))
        {
            $s=explode('--',@Url::$spath[2]);
            if(Request::$ajax)
            {
                static::$param['ap'] = $s;
                // Костыль для отображения каталога через ajax
                if (@Url::$sq['ajax_tsort']){
                    if(@$s[3]!=''){
                        return 'podborshin/ax_result';
                    }elseif(@$s[2]!=''){
                        return 'podborshin/ax_modifs';
                    }elseif(@$s[1]!=''){
                        return 'podborshin/ax_years';
                    }
                }
                else {
                    return 'podborshin/axSearch';
                }
            }
            if(count($s)==4){
                static::$param['ap']=$s;
                return 'podborshin/result';
            }else
                if(@$s[3]!=''){
                    static::$param['ap']=$s;
                    return 'podborshin/result';
                }elseif(@$s[2]!=''){
                    static::$param['ap']=$s;
                    return 'podborshin/modifs';
                }elseif(@$s[1]!=''){
                    static::$param['ap']=$s;
                    return 'podborshin/years';
                }elseif(@$s[0]!=''){
                    static::$param['ap']=$s;
                    return 'podborshin/models';
                }else{
                    return static::redir404();
            }
        }
        else
        {
            return 'podborshin/index';
        }
    }

    static function avtoPodborDiskov()
    {    
        static::$param['gr']=2;
        if(!empty(Url::$spath[2]))
        {
            $s=explode('--',@Url::$spath[2]);
            if(Request::$ajax)  
            {
                static::$param['ap'] = $s;
                // Костыль для отображения каталога через ajax
                if (@Url::$sq['ajax_tsort']){
                    if(@$s[3]!=''){
                        return 'podbordiskov/ax_result';
                    }elseif(@$s[2]!=''){
                        return 'podbordiskov/ax_modifs';
                    }elseif(@$s[1]!=''){
                        return 'podbordiskov/ax_years';
                    }
                }
                else {
                    return 'podbordiskov/axSearch';
                }
            }
            if(count($s)==4){
                static::$param['ap']=$s;
                return 'podbordiskov/result';
            }else
                if(@$s[3]!=''){
                    static::$param['ap']=$s;
                    return 'podbordiskov/result';
                }elseif(@$s[2]!=''){
                    static::$param['ap']=$s;
                    return 'podbordiskov/modifs';
                }elseif(@$s[1]!=''){
                    static::$param['ap']=$s;
                    return 'podbordiskov/years';
                }elseif(@$s[0]!=''){
                    static::$param['ap']=$s;
                    return 'podbordiskov/models';
                }else{
                    return static::redir404();
            }
        }
    }    

    static function avtoPodborDiskovIndex()
    {    
        static::$param['gr']=2;
        if(empty(Url::$spath[2]))
        {
            return 'podbordiskov/index';
        }
    }

    static function ajaxAvtoPodbor() 
    {
        if(Request::$ajax && !empty(Url::$spath[2]))
        {
            switch(Url::$spath[2]){
                case 'getModels': return 'ajaxpodbor/getModels';
                case 'getYears': return 'ajaxpodbor/getYears';
                case 'getModifs': return 'ajaxpodbor/getModifs';
                default: return static::redir404();

            }
        }
    }

    static function page()
    {
        if(Request::$ajax) return static::redir404();
        if(@Url::$spath[2]!='') return 'page/index';
    }

    static function news()
    {
        if(Request::$ajax) return static::redir404();
        if(@Url::$spath[2]!='') {
            return 'news/item';
        }else {
            return 'news/lenta';
        }
    }
    static function articles(){
        if(Request::$ajax) return static::redir404();
        if(@Url::$spath[2]!='')
            return 'articles/articlesItem';
        else return 'articles/articlesLenta';
    }

    /* Записи  */
    static function entrysection(){
        if(Request::$ajax) return static::redir404();
        if(@Url::$spath[2]!='')
            return 'entrysection/item';
        else return 'entrysection/lenta';
    }
    static function entry(){
        if(Request::$ajax) return static::redir404();
        if(@Url::$spath[2]!='')
            return 'entry/item';
    }

    static function faq(){
        if(@Url::$spath[2]!='')
            return static::redir404();
        else return 'faq/faqLenta';
    }

    static function novinkiSezona(){
        if(Request::$ajax) return static::redir404();
        if(@Url::$spath[2]!='')
            return 'articles/novinkiItem';
        else return 'articles/novinkiLenta';
    }

    static function cart()
    {
        if(Request::$ajax && empty(Url::$spath[2])) return static::redir404();
        switch(@Url::$spath[2]){
            case '': return 'cart/index';
            case 'add': return 'cart/add';
            case 'add2': return 'cart/add2';
            case 'del': return 'cart/del';
            case 'send': return 'cart/send';
            case 'changeAmount': return 'cart/changeAmount';
            case 'updateDopList': return 'cart/updateDopList';
            case 'getBrandAccessories': return 'cart/getBrandAccessories';
            case 'clear': return 'cart/clear';
            case 'cornerCart': return 'cart/cornerCart';
            default: return static::redir404();

        }
    }


    static function search()
    {
        if (@Url::$sq['ajax_tsort']) {
            return 'search/axView';
        } elseif(Request::$ajax) return static::redir404();
        return 'search/index';
    }

    static function yaSearch(){
        if(Request::$ajax) return static::redir404();
        return 'yaSearch/index';
    }

    static function compare()
    {
        switch(@Url::$spath[2]){
            case 'tyres':
                static::$param['gr']=1;
                return 'compare/tyres/index';
            case 'disks':
                static::$param['gr']=2;
                return 'compare/disks/index';
        }
    }

    static function tBySize()
    {
        if(Request::$ajax) return static::redir404();
        static::$param['gr']=1;
        return 'catalog/tyres/bySize/index';
    }

    static function dBySize()
    {
        if(Request::$ajax) return static::redir404();
        static::$param['gr']=2;
        return 'catalog/disks/bySize/index';
    }

    static function sitemap()
    {
        if(Request::$ajax) return static::redir404();
        switch(@Url::$spath[2]){
            case 'disks':
                return 'sitemap/disks';
        }
    }

    static function rss()
    {
        return 'rss/index';
    }



    static function yml()
    {
        return 'yml/index';
    }
}