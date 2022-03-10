<? 
if (!defined('true_enter')) die ("Direct access not allowed!");

// Ver 4.2.

class App_CC_CII2t extends App_CC_CII2Base {
	
function recognize($file_id,$iter,$limit,$opt,$ciSID){
	
	$this->r->finish=false;
	$this->r->fres=true;
	$this->r->fres_msg='';
	$this->is->ciSID=$ciSID;
	$this->is->file_id=$file_id;
	$this->cc=new CC_Ctrl();
	$this->r->logs=array(); // массив для лог-данных
	$this->db=new DB;
	if($iter==0) {

		if(!$this->iter1($file_id,$ciSID,$opt)) return $this->r;
		
		if($this->is->gr!=1){
			$this->r->fres=$this->putMsg(false,'[CC_CII2t]: Не правильная группа для файла id='.$file_id);
			$this->r->fres_msg=$this->fres_msg;
			return $this->r;
		}

		//загружаем словарь суффиксов шин
		$d=$this->cc->fetchAll("SELECT cc_dict.name AS dname,  IFNULL( cc_brand.name, 0 ) AS bname FROM cc_dict LEFT JOIN cc_brand USING ( brand_id ) WHERE cc_dict.gr=1 ORDER BY LENGTH( cc_dict.name ) DESC",MYSQL_ASSOC);
		$this->is->exSuffixes=array();
		if(!empty($d)){
			foreach($d as $v){
				$this->is->exSuffixes[Tools::unesc($v['bname'])][]=Tools::unesc($v['dname']);
			}
		}
		
		
		$this->is->blist=array(); // список брендов в файле

		$this->query("UPDATE cii_item SET cstatus=0, mstatus=0, cstatus=0 WHERE file_id='$file_id'");
		$this->query("UPDATE cii_file SET SID='{$this->is->ciSID}' WHERE file_id='$file_id'");
		if(!$this->is->opt['test']){
			$this->query("UPDATE cc_cat SET upd_id=0 WHERE gr='{$this->is->gr}'");
			// cc_cat.upd_id для cat_id будет равен CSID если размер подвергся обработке и cc_cat_sc обновился
		}

		// поставщики
		$this->query("SELECT * FROM cc_suplr ORDER BY name");
		$this->is->suplrs=array();
		if($this->qnum()) while($this->next()!==false){
			$this->is->suplrs[$this->qrow['suplr_id']]=Tools::unesc($this->qrow['name']);
		}
				
		$this->is->cat_id=0;
		
	}else{
		if(!$this->loadSession()) return $this->r;
	}


	$this->bm=array();

	$this->r->brandName='';

    $limit = (int)$limit;

    // USE INDEX (file_id_cstatus)
    $this->query("SELECT item_id, bstatus, mstatus, cstatus, brand, model, full_name, company, P1+'0' AS P1, P2+'0' AS P2, P3+'0' AS P3, P7, P7_1, MP1, MP2, MP3, suffix, sklad, price1,price2,price3, cat_id, replica, sup_id  FROM cii_item WHERE file_id='$file_id' AND cstatus=0 ORDER BY brand, model, P3, P2, P1, P7, P7_1, suffix  LIMIT 0, $limit");
	
	if($this->qnum()){
		
		while($this->next()!==false){
			
			$this->cstatus=$this->mstatus=$this->bstatus=$this->brand_id=$this->model_id=0;
			$this->tipoInserted=false;
			
			$brand=Tools::unesc($this->qrow['brand']);
			$model=Tools::unesc($this->qrow['model']);
			$this->sup_id=$this->qrow['sup_id'];
			$this->prepareModel(trim($brand),trim($model));
			
			$this->suplr=Tools::unesc($this->qrow['company']);
			
			// $this->suffix - массив если шины и строка если диски
			// делаем суффиксы в массив из поля suffix
			$this->getBrandInSuffixArr();
			$this->suffix=$this->splitTSuffix(trim(Tools::cutDoubleSpaces($this->qrow['suffix'])));
			// добавляем также суффиксы из full_name используя exSuffixes
			$s3=$s=trim(Tools::cutDoubleSpaces($this->qrow['full_name'])).' ';
			//ищем суффикс сначала для бренда
			foreach($this->is->exSuffixes[$this->tSuffixBrand] as $v){
				$s2=str_ireplace(" $v ",' ',$s);
				if(Tools::mb_strcasecmp($s,$s2)!=0) $this->suffix[]=$v;
				$s=$s2;
			}
			//потом глобальный
			foreach($this->is->exSuffixes[0] as $v){
				$s2=str_ireplace(" $v ",' ',$s);
				if(Tools::mb_strcasecmp($s,$s2)!=0) $this->suffix[]=$v;
				$s=$s2;
			}
			$this->suffix=array_unique($this->suffix); // убираем дубли
			
			// выделяем статус ZR
			if(count(array_intersect($this->suffix,array('ZR','Z','zr')))) $this->P6=1; else $this->P6=0;
			
			$this->suffix=array_diff($this->suffix,array('ZR','Z','zr'));
			
			//убираем пустые на всякий случай
			foreach($this->suffix as $k=>$v){
				if(trim($v)=='') unset($this->suffix[$k]);
			}
			
			// выделяем ZR из полного названия
			if(preg_match("/ ZR[0-9]+ /u",trim($s3))) $this->P6=1;
				
				
			if(!$this->brandExists()) return $this->r;

            if ($this->brand_id && !@$this->is->brandsCfg[$this->brand_id]['priceNoUpd']) {
                $this->cstatus=23;
                $this->mstatus=23;
                $this->bstatus=23;
            }else{
                if(!$this->modelExists()) return $this->r;
            }

			$this->is->blist[$this->brand_id]=1;
			
			
			// обновляем cii_item
			$aq=array();
			$aq['brand']=Tools::esc($this->bm['brand']);
			$aq['model']=Tools::esc($this->bm['model']);
			$aq['sup_id']=$this->sup_id;
				
			if($this->P6==1) $this->suffix[]='ZR';  // показываем статус ZR в поле суффикса
			$aq['suffix']=implode(' ',$this->suffix);
			
			if(!$this->is->opt['test']){
				$aq['brand_id']=$this->brand_id;
				$aq['model_id']=$this->model_id;
				$aq['cat_id']=$this->is->cat_id;
			}
				
			$aq['cstatus']=$this->cstatus;
			$aq['bstatus']=$this->bstatus;
			$aq['mstatus']=$this->mstatus;
			
			if(count($aq)) $this->db->update('cii_item',$aq,"item_id='{$this->qrow['item_id']}'");
			
		}
	}else $this->r->finish=true;

	if(!$this->saveSession()) return $this->r;
		
	if($this->r->finish){
		
		$this->changeCatId(0); // сбрасываем в базу данные по последнему типоразмеру
		
		unset($this->is->models,$this->is->tipos);
		
		if(!$this->is->opt['test']){
			
			$this->finishCat();			
			$this->status=1;
			$this->cc->addCacheTask('brands pricesNoIntPrice sizes modAll',$this->is->gr);
			
		} else {
			$this->status=0;
		}
		
		$this->finish();
	}


	return $this->r;
}

private function getBrandInSuffixArr()
{
	// ищем бренд exSuffixes
	$this->tSuffixBrand=0;
	foreach($this->is->exSuffixes as $k=>$v){
		if(Tools::mb_strcasecmp($k,$this->bm['brand'])===0) {
			$this->tSuffixBrand=$k;
			break;
		}
	}
}

private function splitTSuffix($s)
{
	$suffixArr=array();
	$s=' '.$s.' ';
	if(!empty($this->is->exSuffixes[$this->tSuffixBrand]))
		do{
			$s0=$s;
			//ищем суффикс сначала для бренда
			foreach($this->is->exSuffixes[$this->tSuffixBrand] as $v){
				$s2=str_ireplace(" $v ",' ',$s);
				if(Tools::mb_strcasecmp($s,$s2)!=0) $suffixArr[]=$v;
				$s=$s2;
			}
		}while($s!=$s0);
	if(!empty($this->is->exSuffixes[0]))
		do{
			$s0=$s;
			//потом глобальный
			foreach($this->is->exSuffixes[0] as $v){
				$s2=str_ireplace(" $v ",' ',$s);
				if(Tools::mb_strcasecmp($s,$s2)!=0) $suffixArr[]=$v;
				$s=$s2;
			}
		}while($s!=$s0);
	$s=trim($s);
	if($s!='') $suffixArr=array_unique(array_merge($suffixArr,explode(' ',trim($s))));
	return $suffixArr;
}


private function brandExists()
{
	if(empty($this->is->brands)){
		// кешируем бренды
		$this->cc->que('brands',$this->is->gr);
		$this->is->brands=array();
		if($this->cc->qnum()) while($this->cc->next()!==false){
			$this->is->brands[trim(Tools::unesc($this->cc->qrow['name']))]=$this->cc->qrow['brand_id'];
		}
	}
	
	foreach($this->is->brands as $kb=>&$vb){
		if(Tools::mb_strcasecmp($kb,$this->bm['brand'])==0) {
			$this->brand_id=$vb;
			break;
		}
	}
	$dt=date("Y-m-d H:i:s");
	if($this->brand_id) {
		if($this->is->opt['test']) $this->bstatus=21; else $this->bstatus=1;
	}else{
		if(!$this->is->opt['test']){
			$b=Tools::esc($this->bm['brand']);
			$this->db->query("INSERT INTO cc_brand (gr,name,dt_added) VALUES('{$this->is->gr}','{$b}','$dt')");
			$this->brand_id=$this->db->lastId();
			if(!$this->brand_id) {
				$this->r->fres=$this->putMsg(false,'[brandExists()]: Бренд не записан в БД');
				$this->r->fres_msg=$this->fres_msg;
				return false;
			}
			$this->cc->sname_brand($this->brand_id);
			$this->bstatus=2;
			return $this->brand_id=$this->is->brands[$this->bm['brand']]=$this->brand_id;
		}else {
			$this->bstatus=22;
		}
	}
	$this->r->brandName=$this->bm['brand'];
	return true;
}

private function modelExists(){

	if(empty($this->is->models) || $this->is->modelsBrand!=$this->bm['brand']){
		// кешируем модели и размеры внутри выбранного бренда
		$this->is->modelsBrand=$this->bm['brand'];
		$sql="SELECT  cc_model.name, cc_model.model_id, cc_model.suffix AS msuffix FROM cc_model WHERE cc_model.gr='{$this->is->gr}' AND cc_model.brand_id='{$this->brand_id}' AND NOT cc_model.LD";
		$this->db->query($sql);
		$this->is->models=array();
		if($this->db->qnum()) while($this->db->next()!==false){
			$m=trim(Tools::unesc($this->db->qrow['name']));
			$msuffix=Tools::cutDoubleSpaces(trim(Tools::unesc($this->db->qrow['msuffix'])));
			$this->is->models[$this->db->qrow['model_id']]=array('m'=>$m,'s'=>$msuffix);
		}
		// размеры
		$sql="SELECT  cc_cat.ignoreUpdate, cc_cat.cat_id, cc_cat.upd_id, cc_cat.sc, cc_model.sup_id, cc_model.name, cc_model.P1 AS M1, cc_model.P2 AS M2, cc_model.P3 AS M3, cc_model.model_id, cc_model.suffix AS msuffix, cc_cat.suffix AS csuffix, cc_cat.cat_id, cc_cat.P1, cc_cat.P2, cc_cat.P3, cc_cat.P4, cc_cat.P5, cc_cat.P6, cc_cat.P7, cc_cat.bprice, cc_cat.cur_id FROM cc_cat INNER JOIN cc_model ON cc_cat.model_id = cc_model.model_id WHERE cc_cat.gr='{$this->is->gr}' AND cc_model.brand_id='{$this->brand_id}' AND NOT cc_cat.LD AND NOT cc_model.LD";
		$this->db->query($sql);
		$this->is->tipos=array();
		if($this->db->qnum()) while($this->db->next()!==false){
			$csuffix=Tools::cutDoubleSpaces(trim(Tools::unesc($this->db->qrow['csuffix'])));
			$s=$this->splitTSuffix($csuffix);
			$s=array_diff($s,array('ZR','zr','Z')); // убираем Zr чтобы корректно работало сравнение суффиксов шин
			$this->is->tipos[$this->db->qrow['model_id']][$this->db->qrow['cat_id']]=array(
				'P1'=>$this->db->qrow['P1'], // R
				'P2'=>$this->db->qrow['P2'],  // height
				'P3'=>$this->db->qrow['P3'],  // width
				'P6'=>$this->db->qrow['P6'], // ZR
				'P7'=>$this->db->qrow['P7'], // inis
				's'=>$s
			);
			// уже обработанный размер
			if($this->db->qrow['upd_id']==$this->is->ciSID) $this->is->tipos[$this->db->qrow['model_id']][$this->db->qrow['cat_id']]['u']=true;
			if($this->db->qrow['ignoreUpdate']) $this->is->tipos[$this->db->qrow['model_id']][$this->db->qrow['cat_id']]['iu']=true;
		}
	}
	
	$model_ids=array();
	
	$dt=date("Y-m-d H:i:s");

	// если есть модель, то проверяем есть ли в ней размер, если нет размера, то добавляем, если нет модели, то добавляем модель и в нее добавляем размер
	// параметры Не учитывающиесмя при сравнении: шипы,сезон,тип ТС
	
	// ищем модель
	foreach($this->is->models as $km=>&$vm){
		if(Tools::mb_strcasecmp($vm['m'],$this->bm['model'])==0) {
			$model_ids[]=$km; // ищем все подходящие модели
		}
	}
	
	// ищем размер
	if(!empty($model_ids)){ // есть модель(и)
		
		if($this->is->opt['test']) $this->mstatus=21; else $this->mstatus=1;
		
		foreach($model_ids as $mid){
			
			if(!empty($this->is->tipos[$mid])) foreach($this->is->tipos[$mid] as $kt=>&$vt){
				$eq=1;
				if(!$this->arrayCompare($this->suffix,$vt['s'])) $eq=0;
				if($vt['P1']!=$this->qrow['P1']) $eq=0;
				if($vt['P2']!=$this->qrow['P2']) $eq=0;
				if($vt['P3']!=$this->qrow['P3']) $eq=0;
				if($vt['P6']!=$this->P6) $eq=0; // ZR
				if($vt['P7']!=($this->qrow['P7'].$this->qrow['P7_1'])) $eq=0;
//				if($vt['P4']!==$this->qrow['MP1']) $eq=0; //сезон
//				if($vt['P5']!==$this->qrow['MP3']) $eq=0; //шипы
				if($eq){ // размеры равны
					$this->changeCatId($kt);
					$this->model_id=$mid;
					$this->pushSCT();
					if(empty($vt['iu'])){
						if($this->is->opt['test']) $this->cstatus=21; else $this->cstatus=1;
					}else {
						if($this->is->opt['test']) $this->cstatus=23; else $this->cstatus=3;
					}
					break 2;
				}
			}
		}
		if($this->model_id==0){  // нет размера
			$this->model_id=array_shift($model_ids); // берем первую подходящую модель
			if(!$this->is->opt['test']){
				// добавляем размер
				$cid=$this->tipoInsert();
				if($cid===false) return false;
				$this->changeCatId($cid);
				$this->cstatus=2;
				$this->pushSCT();
			}else{  
				$this->cstatus=22;
			}
		} 
	} else{ // нет модели
		if(!$this->is->opt['test']) {
			$this->mstatus=2; 
			$this->cstatus=2;
			// добавляем модель
			$this->db->insert('cc_model',array(
				'brand_id'=>$this->brand_id,
				'gr'=>1,
				'name'=>Tools::esc($this->bm['model']),
				'P1'=>$this->qrow['MP1'],
				'P2'=>$this->qrow['MP2'],
				'P3'=>$this->qrow['MP3'],
				'dt_added'=>$dt
			));
			$this->model_id=$this->db->lastId();
			if(!$this->model_id) return false;
			$this->cc->sname_model($this->model_id);
			$this->is->models[$this->model_id]=array('m'=>$this->bm['model'],'s'=>'');
			// добавляем размер
			$cid=$this->tipoInsert();
			if($cid===false) return false;
			$this->changeCatId($cid);
			$this->pushSCT();
		} else {
			$this->mstatus=22;
			$this->cstatus=22;
		}
	}
	return true;
			
}

private function tipoInsert(){
	
	$dt=date("Y-m-d H:i:s");
	
	$suf=trim(Tools::esc(implode(' ',$this->suffix)));
	$inis=Tools::esc($this->qrow['P7'].$this->qrow['P7_1']);
	$this->db->insert('cc_cat',array(
		'model_id'=>$this->model_id,
		'gr'=>1,
		'P1'=>$this->qrow['P1'],
		'P2'=>$this->qrow['P2'],
		'P3'=>$this->qrow['P3'],
		'P4'=>$this->cc->isCinSuffix($suf),
		'P6'=>$this->P6,
		'P7'=>$inis,
		'suffix'=>$suf,
		'upd_id'=>$this->is->ciSID,
		'dt_added'=>$dt
	));
	$cat_id=$this->db->lastId();
	$this->tipoInserted=true;
	if(!$cat_id) {
		$this->r->fres=$this->putMsg(false,'[tipoInsert()]: Размер не записан в БД');
		$this->r->fres_msg=$this->fres_msg;
		return false;
	}
	$this->cc->sname_cat($cat_id);
	$this->is->tipos[$this->model_id][$cat_id]=array(
		'P1'=>$this->qrow['P1'],
		'P2'=>$this->qrow['P2'],
		'P3'=>$this->qrow['P3'],
		'P6'=>$this->qrow['P6'],
		'P7'=>$this->qrow['P7'].$this->qrow['P7_1'],
		's'=>$this->suffix,
		'u'=>true
	);
	
	return $cat_id;
}

private function prepareModel($brand,$model,$replicaBrand='Replica'){
	
	return $this->bm=array('brand'=>$brand,'model'=>$model);
}
	
function __construct()
{
	parent::__construct();
}

	
	
}