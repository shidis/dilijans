<?
if (!defined('true_enter')) die ("Hacker attempt.  Stoped!");

class App_Sitemap extends DB {
	
	public static 
		$e="\r\n",
		$h='',
		$us='.html', // суффикс урла
		$dapSuffix='',
		$tapSuffix='';
		
	
	public static function index(){
		?>
		<p>Файлы sitemap:</p>
		<ul>
            <li>https://<?=Cfg::get('site_url')?>/sitemap/dsizes.xml</li>
            <li>https://<?=Cfg::get('site_url')?>/sitemap/dbm.xml</li>
		</ul>
		<?
	}

	public static function header(){
		
		echo '<?xml version="1.0" encoding="UTF-8"?>'.self::$e;
		echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"'.self::$e;
		echo 'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"'.self::$e;
		echo 'xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9'.self::$e;
		echo 'http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">'.self::$e;
	
		self::$h='https://'.Cfg::get('site_url').'/';

	}

    public static function dbm()
    {

        $u=App_Route::_getUrl('dCat');
        echo "<url><loc>".self::$h.$u.self::$us."</loc></url>".self::$e;
        $u=App_Route::_getUrl('replicaCat');
        echo "<url><loc>".self::$h.$u.self::$us."</loc></url>".self::$e;

        $cc=new CC_Base();

        // БРЕНДЫ ДИСКОВ
        $cc->que('brands',2,1,'AND NOT replica');
        while($cc->next()!==false){
            $u=App_Route::_getUrl('dCat').'/'.Tools::unesc($cc->qrow['sname']);
            echo "<url><loc>".self::$h.$u.self::$us."</loc></url>".self::$e;
        }
        // БРЕНДЫ РЕПЛИКИ
        $cc->que('brands',2,1,'AND replica');
        while($cc->next()!==false){
            $u=App_Route::_getUrl('dCat').'/'.Tools::unesc($cc->qrow['sname']);
            echo "<url><loc>".self::$h.$u.self::$us."</loc></url>".self::$e;
        }

        // МОДЕЛИ ДИСКОВ и РЕПЛИКИ

        $n=$cc->model_view(array(
            'gr'=>2,
            'nolimits'=>true
        ));
        if($n)
            while($cc->next()!==false){
                $u=App_Route::_getUrl('dModel').'/'.$cc->qrow['sname'];
                echo "<url><loc>".self::$h.$u.self::$us."</loc></url>".self::$e;
            }

    }

    public static function dsizes(){

        $cc=new CC_Base();

        $n=$cc->cat_view(array(
            'gr'=>2,
            'nolimits'=>true,
            'where'=>'cc_cat.sc>=4',
            'order'=>'cc_brand.replica, cc_cat.P5 DESC'
        ));

        $dcat=App_Route::_getUrl('dSearch').self::$us.'?';

        if($n){
            $r=$rsv=$sv=array();
            $br=$bsv=$rbsv=array();
            while($cc->next()!==false){
                // сверловка
                if(!isset($sv[$cc->qrow['P4'].'-'.$cc->qrow['P6']])){
                    $sv[$cc->qrow['P4'].'-'.$cc->qrow['P6']]=1;
                    $u=$dcat."sv={$cc->qrow['P4']}x{$cc->qrow['P6']}";
                    echo "<url><loc>".self::$h.$u."</loc></url>".self::$e;
                }
                // радиус
                if(!isset($r[$cc->qrow['P5']])){
                    $r[$cc->qrow['P5']]=1;
                    $u=$dcat."p5={$cc->qrow['P5']}";
                    echo "<url><loc>".self::$h.$u."</loc></url>".self::$e;
                }
                // сверловка + радиус
                if(!isset($rsv[$cc->qrow['P5'].'-'.$cc->qrow['P4'].'-'.$cc->qrow['P6']])){
                    $rsv[$cc->qrow['P5'].'-'.$cc->qrow['P4'].'-'.$cc->qrow['P6']]=1;
                    $u=$dcat."p5={$cc->qrow['P5']}&amp;sv={$cc->qrow['P4']}x{$cc->qrow['P6']}";
                    echo "<url><loc>".self::$h.$u."</loc></url>".self::$e;
                }
                // бренд + радиус
                if(!isset($br[$cc->qrow['brand_sname'].'-'.$cc->qrow['P5']])){
                    $br[$cc->qrow['brand_sname'].'-'.$cc->qrow['P5']]=1;
                    $u=$dcat."vendor={$cc->qrow['brand_sname']}&amp;p5={$cc->qrow['P5']}";
                    echo "<url><loc>".self::$h.$u."</loc></url>".self::$e;
                }
                // бренд + сверловка
                if(!isset($bsv[$cc->qrow['P4'].'-'.$cc->qrow['P6']])){
                    $bsv[$cc->qrow['P4'].'-'.$cc->qrow['P6']]=1;
                    $u=$dcat."vendor={$cc->qrow['brand_sname']}&amp;sv={$cc->qrow['P4']}x{$cc->qrow['P6']}";
                    echo "<url><loc>".self::$h.$u."</loc></url>".self::$e;
                }
                // бренд + сверловка + радиус
                if(!isset($rbsv[$cc->qrow['P5'].'-'.$cc->qrow['P4'].'-'.$cc->qrow['P6']])){
                    $rbsv[$cc->qrow['P5'].'-'.$cc->qrow['P4'].'-'.$cc->qrow['P6']]=1;
                    $u=$dcat."vendor={$cc->qrow['brand_sname']}&amp;p5={$cc->qrow['P5']}&amp;sv={$cc->qrow['P4']}x{$cc->qrow['P6']}";
                    echo "<url><loc>".self::$h.$u."</loc></url>".self::$e;
                }
            }
        }
    }




	public static function close(){
		
		echo '</urlset>';
	}
		
	
	
	
}
