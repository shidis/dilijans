<?
if (!defined('true_enter')) die ("Direct access not allowed!");

class App_CC_Dataset_YM extends CC_Dataset {


	public $dataFields = array(
		'shopName' => array('type' => 'string', 'require' => true, 'info' => 'Название магазина (&lt;name&gt;)'),
		'company' => array('type' => 'string', 'require' => true, 'info' => 'Название фирмы (&lt;company&gt;)'),
		'shopUrl' => array('type' => 'string', 'require' => true, 'info' => 'Адрес сайта с http:// (&lt;url&gt;)'),
		'modelPrefix' => array(
			'type' => 'string',
			'require' => false,
			'info' => 'Префикс перед названием товара (не имеет отдельного тега в YML)'
		),
		'description' => array(
			'type' => 'string',
			'require' => false,
			'info' => 'Описание (&lt;description&gt;) не более 512 символов. <br>Если оставить пустым то описание будет сгененировано автоматически Яндексом. <br>Допустимые переменные: #bname# - название бренда, #balt# - альт. название бренда, #mname# - название модели, #malt# - альт. название модели.<br>Для диска: #replica#/#Replica# - слово Replica с большой и маленькой буквы, #replica_rus#/#Replica_rus# - слово Реплика с большой и маленькой буквы, J#, #xR#, #dirki#, #DCO#, #ET#, #DIA#,  #color# - цвет, #diskType#/#DiskType# - тип диска (со строчной и прописной буквы соответсвенно). <br>Для шины: #width#, #height#, #radius#, #inis# - ис/ин, #sezon#/#Sezon# - сезонность, #ship# - шипованная, #suffix# - суффиксы'
		),
		'typePrefix' => array(
			'type' => 'string',
			'require' => false,
			'info' => '&lt;typePrefix&gt; - может быть добавлено сервисом перед названием товара'
		),
		'delivery' => array(
			'type' => 'bool',
			'values' => ['0' => 'false', '1' => 'true'],
			'require' => true,
			'info' => 'Возможность доставить соответствующий товар &lt;delivery&gt;'
		),
		'local_delivery_cost' => array(
			'type' => 'float',
			'require' => false,
			'info' => 'Стоимость доставки данного товара в Своем регионе'
		),
		'country_of_origin' => array('type' => 'string', 'require' => false, 'info' => 'Страна производства товара.'),
		'sales_notes' => array(
			'type' => 'string',
			'require' => false,
			'info' => '&lt;sales_notes&gt;Элемент используется для отражения информации о минимальной сумме заказа, минимальной партии товара или необходимости предоплаты, а так же для описания акций магазина (кроме скидок). Допустимая длина текста в элементе — 50 символов. '
		),
		'categoryPrefix' => array(
			'type' => 'string',
			'require' => false,
			'info' => 'В качестве названия категорий товаров (&lt;category&gt;) используется бренд. Здесь можно задать префикс перед категорией (применяется к дискам НЕ реплики).'
		),
		'categoryReplicaPrefix' => array(
			'type' => 'string',
			'require' => false,
			'info' => 'В качестве названия категорий товаров (&lt;category&gt;) используется марка авто. Здесь можно задать свой префикс перед маркой. Этот же префикс будет использован в теге &lt;vendor&gt;, а перед названием модели будет прибавлена марка авто'
		),
		'SCMin' => array(
			'type' => 'integer',
			'require' => false,
			'info' => 'Выгружать только товары с наличием на складе больше указанного значения. По умолчанию выгружается все, включая отсутсвующие на складе'
		),
		'urlSuffix' => array(
			'type' => 'string',
			'require' => false,
			'info' => 'При переходе с маркета на сайт можно добавить к адресу страницы окончание (например, ?from=ym)'
		),
		'pickup' => array(
			'type' => 'bool',
			'values' => ['0' => 'false', '1' => 'true'],
			'require' => true,
			'info' => 'Возможность самовывоза (&lt;pickup&gt;)'
		),
		'manufacturer_warranty' => array(
			'type' => 'bool',
			'values' => ['0' => 'false', '1' => 'true'],
			'require' => true,
			'info' => 'Гарантия производителя (&lt;manufacturer_warranty&gt;)'
		)
	);

	function __construct()
	{
		parent::__construct();
	}

	public function export($debug=false)
	{
		//Tools::prn($this->dataset['data']);
		$this->prepareData();
		if($debug) {
			Tools::prn($this->dataset);
			error_reporting(E_ALL);
			ini_set('error_reporting', E_ALL);
		}
		extract($this->dataset);

		if($debug) echo '<textarea style="width:100%; height:1000px">';

		echo '<?xml version="1.0" encoding="utf-8"?>'; echo "\n";
		echo '<!DOCTYPE yml_catalog SYSTEM "shops.dtd">'; echo "\n";
		echo "<yml_catalog date=\"".date("Y-m-d H:i")."\">"; echo "\n\n";
		echo '<shop>'; echo "\n\n";
		echo "<name>".$this->sformat($shopName)."</name>"; echo "\n";
		echo "<company>".$this->sformat($company)."</company>"; echo "\n";
		echo "<url>".$this->sformat($shopUrl)."</url>"; echo "\n\n";
		echo "<currencies>"; echo "\n";
		echo "<currency id=\"RUR\" rate=\"1\" />"; echo "\n";
		echo "</currencies>	"; echo "\n\n";

		$SCMin=intval($SCMin);

		$cc=new CC_Base();
		$n=$cc->cat_view(array(
			'gr'=>'all',
			'nolimits'=>1,
			'datasetTo'=>'cat',
			'dataset_id'=>$dataset_id,
			'ex'=>1,
			'exFields'=>array('brand'=>array()),
			'add_query'=>"(cc_cat.cprice>0 OR cc_cat.scprice>0) AND cc_cat.sc>={$SCMin}",
			'order'=>'cc_cat.gr, cc_brand.name, cc_model.name'
		));
		if(!$n) {
			echo "</shop>\n</yml_catalog>"; echo "\n";
			return;
		}

		echo "<categories>"; echo "\n";


		// соотвествие категории нашей маркетовской
		$cats=array();

		echo "<category id=\"1\">Автомобильные шины</category>\n";
		echo "<category id=\"2\">Автомобильные диски</category>\n";
		echo "<category id=\"3\">Диски Replica</category>\n";
		$catsi=3; // последний индекс категории маркета

		if(!empty($cc->ex_arr['brand']['replica'])){
			foreach($cc->ex_arr['brand']['replica'] as $k=>$v){
				$cats[$k]=++$catsi;
				echo "<category id=\"$catsi\" parentId=\"3\">".$this->sformat($categoryReplicaPrefix.' '.$v['name'])."</category>"; echo "\n";
			}
		}
		if(!empty($cc->ex_arr['brand'][0])){
			foreach($cc->ex_arr['brand'][0] as $k=>$v){
				$cats[$k]=++$catsi;
				echo "<category id=\"$catsi\" parentId=\"".($v['gr'])."\">".$this->sformat($categoryPrefix[$v['gr']].' '.$v['name'])."</category>"; echo "\n";
			}
		}
		echo "</categories>"; echo "\n\n";

		echo "<offers>"; echo "\n\n";

		$cc->first();
		while($cc->next()!==false){
			$gr=$cc->qrow['gr'];
			if($gr==1 && $cc->qrow['P1']>0  && $cc->qrow['P2']>0 && $cc->qrow['P3']>0 || $gr==2 && $cc->qrow['P2']>0 && $cc->qrow['P5'] && $cc->qrow['P4'] && $cc->qrow['P6']) {
				echo "<offer id=\"{$cc->qrow['cat_id']}\" type=\"vendor.model\" available=\"".($cc->qrow['sc']>0?'true':'false')."\">"; echo "\n";
				if($gr==1)
					echo "<url>".$this->sformat("https://".Cfg::get('site_url').App_SUrl::tTipo(0,$cc->qrow)."{$urlSuffix[$gr]}")."</url>";
				else
					echo "<url>".$this->sformat("https://".Cfg::get('site_url').App_SUrl::dTipo(0,$cc->qrow)."{$urlSuffix[$gr]}")."</url>";
				echo "\n";
				$price=$cc->qrow['cprice'];
				if($cc->qrow['scprice']>0) $price=$cc->qrow['scprice'];
				echo "<price>{$price}</price>"; echo "\n";
				echo "<currencyId>RUR</currencyId>"; echo "\n";
				echo "<categoryId>".$cats[$cc->qrow['brand_id']]."</categoryId>"; echo "\n";
				if($cc->qrow['img1']!=''){
					echo "<picture>https:".$this->sformat($cc->make_img_path(1)).'</picture>'; echo "\n";
				}
				if($cc->qrow['img2']!=''){
					echo "<picture>https:".$this->sformat($cc->make_img_path(2)).'</picture>'; echo "\n";
				}
				echo "<store>false</store>"; echo "\n";
				echo "<pickup>{$pickup[$gr]}</pickup>"; echo "\n";
				echo "<delivery>{$delivery[$gr]}</delivery>"; echo "\n";
				echo "<local_delivery_cost>{$local_delivery_cost[$gr]}</local_delivery_cost>"; echo "\n";
				echo "<typePrefix>{$this->sformat($typePrefix[$gr])}</typePrefix>"; echo "\n";
				$bname=Tools::unesc($cc->qrow['bname']);
				if($cc->qrow['replica'] && !empty($categoryReplicaPrefix))
					echo "<vendor>$categoryReplicaPrefix</vendor>";
				else
					echo "<vendor>".$this->sformat($bname)."</vendor>";
				echo "\n";
				$mname=Tools::html($cc->qrow['mname']);
				if($gr==1) {
					$p1=Tools::n($cc->qrow['P1']);
					$p2=Tools::n($cc->qrow['P2']);
					$p3=Tools::n($cc->qrow['P3']);
					$s=explode(' ',$cc->qrow['csuffix']);
					if(array_search('C',$s)) {
						$r="R{$p1}C";
						$s=array_diff($s,array('C'));
					}else $r="R$p1";
					$s=implode(' ',$s);
					if($cc->qrow['P6']) $r='Z'.$r;
					$model=trim("$bname $mname $p3/$p2 $r {$cc->qrow['P7']} $s");
				}elseif($gr==2){
					// Antera 345 8,5x18 6x139,7 ET 35 Dia 67,1
					//             P2 P5 P4 P6      P1     P3
					// ET и DIA может быть 0, dia в этом случае не отображается
					$p1=($cc->qrow['P1']);
					$p2=($cc->qrow['P2']);
					$p3=($cc->qrow['P3']);
					$p4=$cc->qrow['P4'];
					$p5=($cc->qrow['P5']);
					$p6=($cc->qrow['P6']);
					if($cc->qrow['replica'])
						if(!empty($categoryReplicaPrefix))
							$model="$bname $mname {$p2}x{$p5}/{$p4}x{$p6}"." ET{$p1}".($p3>0?" D$p3":'').' '.$cc->qrow['csuffix'];
						else
							$model="$mname {$p2}x{$p5}/{$p4}x{$p6}"." ET{$p1}".($p3>0?" D$p3":'').' '.$cc->qrow['csuffix'];
					else
						$model="$bname $mname {$p2}x{$p5}/{$p4}x{$p6}"." ET{$p1}".($p3>0?" D$p3":'').' '.$cc->qrow['csuffix'];
				}
				echo "<model>".$this->sformat(($modelPrefix[$gr]!=''?"{$modelPrefix[$gr]} ":'').$model)."</model>"; echo "\n";
				$d=$this->parseDescription($cc->qrow,$description[$gr]);
				echo "<description>".$this->sformat($d)."</description>"; echo "\n";
				echo "<sales_notes>".$this->sformat($sales_notes[$gr])."</sales_notes>"; echo "\n";
				if($manufacturer_warranty[$gr]) echo '<manufacturer_warranty>true</manufacturer_warranty>'; echo "\n";
				if(trim($country_of_origin[$gr])!='') echo "<country_of_origin>".$this->sformat($country_of_origin[$gr])."</country_of_origin>"; echo "\n";
				echo "</offer>"; echo "\n";
				echo "\n";
			}
		}
		echo "\n\n";
		echo "</offers>"; echo "\n\n";

		echo "</shop>\n</yml_catalog>";
		if($debug) echo '</textarea>';

	}

	private function parseDescription($qrow,$descr){
		if($qrow['gr']==2){
			switch($qrow['MP1']){
				case 1: $diskType='кованые'; $DiskType='Кованые'; break;
				case 2: $diskType='литые'; $DiskType='Литые'; break;
				case 3: $diskType='штампованные'; $DiskType='Штампованные'; break;
				default: $diskType=''; $DiskType='';
			}
			$descr=str_replace('#diskType#',$diskType,$descr);
			$descr=str_replace('#DiskType#',$DiskType,$descr);
			// , алюминиевый сплав, ширина обода 6.5", диаметр обода 15", крепежных отверстий 5, PCD 114.3 мм, центральное отверстие 60.1 мм, вылет ET 43 мм, цвет.
			$descr=str_replace('#J#',$qrow['P2'],$descr);
			$descr=str_replace('#xR#',$qrow['P5'],$descr);
			$descr=str_replace('#dirki#',$qrow['P4'],$descr);
			$descr=str_replace('#DCO#',$qrow['P6'],$descr);
			$descr=str_replace('#ET#',$qrow['P1'],$descr);
			$descr=str_replace('#DIA#',$qrow['P3'],$descr);
			$descr=str_replace('#color#',Tools::unesc($qrow['csuffix']),$descr);
			$descr=str_replace('#Replica#',$qrow['replica']?'Replica':'',$descr);
			$descr=str_replace('#replica#',$qrow['replica']?'replica':'',$descr);
			$descr=str_replace('#replica_rus#',$qrow['replica']?'реплика':'',$descr);
			$descr=str_replace('#Replica_rus#',$qrow['replica']?'Реплика':'',$descr);
		}else{
			switch($qrow['MP1']){
				case 1: $sezon='летние'; $Sezon='Летние'; break;
				case 2: $sezon='зимние'; $Sezon='Зимние'; break;
				case 3: $sezon='всесезонные'; $Sezon='Всесезонные'; break;
				default: $sezon=''; $Sezon='';
			}
			$descr=str_replace('#sezon#',$sezon,$descr);
			$descr=str_replace('#Sezon#',$Sezon,$descr);
			if($qrow['MP3']) $ship='шипованные'; else $ship='';
			$descr=str_replace('#ship#',$ship,$descr);
			$descr=str_replace('#width#',$qrow['P3'],$descr);
			$descr=str_replace('#height#',$qrow['P2'],$descr);
			$descr=str_replace('#radius#',$qrow['P1'],$descr);
			$descr=str_replace('#inis#',$qrow['P7'],$descr);
			$descr=str_replace('#suffix#',Tools::unesc($qrow['csuffix']),$descr);

		}
		$mname=Tools::unesc($qrow['mname']);
		$malt=Tools::unesc($qrow['malt']);
		$malt=$malt==''?$mname:$malt;

		$descr=str_replace('#mname#',$mname,$descr);
		$descr=str_replace('#malt#',$malt,$descr);

		$bname=Tools::unesc($qrow['bname']);
		$balt=Tools::unesc($qrow['balt']);
		$balt=$balt==''?$bname:$balt;

		$descr=str_replace('#bname#',$bname,$descr);
		$descr=str_replace('#balt#',$balt,$descr);

		$descr=preg_replace("~#[^#]*?#~u",'',$descr);

		return trim($descr);
	}


}	