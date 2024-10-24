<?
if (!defined('true_enter')) die ("Direct access not allowed!");

class CC_Dataset extends DB 
{
	public $classes=array();
	public $c;
	public $selectedDataset; // хранится в $this
	public $dataset; // наследуется в управляющем объекте класса
	public $dataFields;
	
	
	
	public function datasetList($p=array()){
		
		$w=array();
		$w=join(' AND ',$w);
		return $this->fetchAll("SELECT *, (SELECT count(*) FROM cc_dataset_cat WHERE dataset_id=ds.dataset_id) AS catNum FROM cc_dataset ds".($w!=''?"WHERE $w":'')." ORDER BY dt_added DESC",MYSQLI_ASSOC);
	}
	
	public function getDataset($dataset_id){
		
		$d=$this->getOne("SELECT * FROM cc_dataset WHERE dataset_id='$dataset_id'",MYSQLI_ASSOC);
		if(!$d['dataset_id']) return false;
		else {
			$this->selectedDataset=$d;
			$this->selectedDataset['data']=Tools::DB_unserialize($this->selectedDataset['data']);
			$this->selectedDataset['name']=Tools::unesc($this->selectedDataset['name']);
			$this->selectedDataset['sname']=Tools::unesc($this->selectedDataset['sname']);
			return $this->selectedDataset;
		}
	}

	public function initDatasetBySname($sname){
		
		$sname=Tools::like($sname);
		$d=$this->getOne("SELECT * FROM cc_dataset WHERE sname LIKE '$sname'",MYSQLI_ASSOC);
		if(!$d['dataset_id']) return 100;
		
		if(false!==($instance=$this->classInstance($d['class']))){
			
			 $instance->dataset=$d;
			 $instance->dataset['data']=Tools::DB_unserialize($instance->dataset['data']);
			 $instance->dataset['name']=Tools::unesc($instance->dataset['name']);
			 $instance->dataset['sname']=Tools::unesc($instance->dataset['sname']);
			 return $instance;
			
		}else return 110;
			
	}
		

	function __construct($class='')	{

		parent::__construct();
		
		$this->initClasses();
		
		if(!empty($class)) 
			if(!$this->classInstance($class)) return false;
	
	}
	
	private function initClasses(){
		
		$this->classes=array();
		
		if(!empty(Cfg::$config['datasetClasses']))
			foreach(Cfg::$config['datasetClasses'] as $class=>$data){
				if(file_exists(Cfg::$config['root_path'].'/app/classes/CC/Dataset/'.$class.'.php') && class_exists('App_CC_Dataset_'.$class)){
					$this->classes[$class]=$data;
				}
			}
		
		return $this->classes;
	}

	public function classExists($class){
		
		if(empty($this->classes[$class])) 
			return false; 
		else 
			return true;
		
	}
	
	public function classInstance($class){
		
		if(empty($this->classes[$class])) return false;
		$cl='App_CC_Dataset_'.$class;
		return $this->c[$class]=new $cl;
	}

	public function prepareData(){

		foreach($this->dataFields as $k=>$v) {
			if(!empty($v['values'])) {
				if (is_array($this->dataset['data'][$k])) foreach ($this->dataset['data'][$k] as $kk => $vv) $this->dataset['data'][$k][$kk] = $v['values'][$vv];

				else
					$this->dataset['data'][$k] = $v['values'][$this->dataset['data'][$k]];
			}

			$this->dataset[$k]=($this->dataset['data'][$k]);
		}
		unset($this->dataset['data']);
	}

	public function sformat($s)
	{
		$s=Tools::unesc(trim($s));
		$ss=Tools::html($s,false);
		if($ss==$s)
			return $s;
		else
			return "<![CDATA[$s]]>";
	}

	public function datasetRealNum($dataset_id)
	{
		$this->getDataset($dataset_id);
		if(!isset($this->selectedDataset['data']['SCMin'])) return 'SCMin not defined';
		$cc=new CC_Base();
		$n=$cc->cat_view(array(
			'gr'=>'all',
			'count'=>1,
			'datasetTo'=>'cat',
			'dataset_id'=>$dataset_id,
			'add_query'=>"(cc_cat.cprice>0 OR cc_cat.scprice>0) AND cc_cat.sc>={$this->selectedDataset['data']['SCMin']}"
		));
		unset($cc);
		return $n;
	}
}