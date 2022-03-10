<?
if (!defined('true_enter')) die ("Direct access not allowed!");

class Pages extends DB{

	var $page=array();
	var $tpl="<div class=\"#class#\">#body#</div>";
	var $tplClass=array(1=>'tbp_top',2=>'tbp_bot');
	var $blockChainMode=false;
	var $chainBlockClass='part';

function __construct($tplClass='',$tpl='')
{
	parent::__construct();
	if($tplClass!='') $this->tplClass=$tplClass;
	if($tpl!='') $this->tpl=$tpl;
}

function que($qname,$cond1='',$cond2='',$cond3='',$cond4='',$cond5='')
{
	switch ($qname)  
	{
		case 'pages_list':
			$cond1=Tools::like($cond1);  // url
			$cond2=intval($cond2); //start
			$cond3=intval($cond3); //limit
			$cond4=Tools::esc($cond4); // поля сортировки
			if($cond4=='') $cond4="url,block_name";
			$cond5=Tools::esc($cond5); // asc/desc
			$limit='';
			if(!$cond3 && $cond2) $limit="LIMIT $cond3";
				elseif($cond3) $limit="LIMIT $cond2,$cond3";
			$res=$this->query("SELECT ss_pages.*, ss_pages_blocks.name AS block_name FROM ss_pages LEFT JOIN ss_pages_blocks ON ss_pages.block_id=ss_pages_blocks.block_id WHERE (NOT LD) ".($cond1!=''?"AND (url LIKE '$cond1')":'')." ORDER BY $cond4 $cond5 $limit");
			break;
		case '_pages_list':
			$cond1=Tools::like($cond1);  // url
			$res=$this->query("SELECT count(page_id) FROM ss_pages WHERE (NOT LD) ".($cond1!=''?"AND (url LIKE '$cond1')":''));
			$this->next();
			$res=$this->qrow[0];
			break;
		case 'page_by_id':
			$cond1=intval($cond1);
			$res=$this->query("SELECT *, ss_pages_blocks.name AS block_name FROM ss_pages LEFT JOIN ss_pages_blocks ON ss_pages.block_id=ss_pages_blocks.block_id  WHERE page_id='$cond1'");
			$this->next();
			break;
		case 'block_list':
			$res=$this->query("SELECT * FROM ss_pages_blocks ORDER BY name");
			break;
	}
	return($res);
}	
	
function load($block_id)
{	
	if(!count($this->page)){
		$this->page=array();
		$d=$this->fetchAll("SELECT * FROM ss_pages_blocks ORDER BY block_id DESC");   // сначала нижний, затем верхний
		if($this->qnum()) 
			foreach($d as $r) 
				if($this->blockChainMode) 
					$this->page[$r['block_id']][0]=array('text'=>'','title'=>'','header'=>'','keywords'=>'','description'=>''); 
				else $this->page[$r['block_id']]=array('text'=>'','title'=>'','header'=>'','keywords'=>'','description'=>''); 
		else return '';
		$url=Tools::esc($_SERVER['REQUEST_URI']);
		$u=@parse_url($url);
		$_url=Tools::like($u['path']);
		if(@$u['query']!='') parse_str($u['query'],$u); else $u='';
		$d=$this->fetchAll("SELECT * FROM ss_pages WHERE (NOT LD) AND ( (url='$url' AND strict) OR (LOCATE(url, '$url')!=0 AND NOT strict AND param='') OR (url LIKE '$_url%' AND NOT strict AND param!='') ) ORDER BY pos ASC,page_id ASC");
		if($this->qnum()){
			$part=0;
			foreach($d as $r) {
				$r['param']=Tools::unesc(trim($r['param']));
				if(!$r['strict'] && $r['param']!=''){
					parse_str($r['param'],$param);
					if(!is_array($u) || count(array_diff_assoc($param,$u))) continue;
				}
				if($this->blockChainMode)
					$this->page[$r['block_id']][$part]=array(
						'text'=>trim(Tools::unesc($r['text'])),
						'title'=>trim(Tools::unesc($r['title'])),
						'header'=>trim(Tools::unesc($r['header'])),
						'keywords'=>trim(Tools::unesc($r['keywords'])),
						'description'=>trim(Tools::unesc($r['description'])),
						'pos'=>$r['pos']
					);
				else
					$this->page[$r['block_id']]=array(
						'text'=>trim(Tools::unesc($r['text'])),
						'title'=>trim(Tools::unesc($r['title'])),
						'header'=>trim(Tools::unesc($r['header'])),
						'keywords'=>trim(Tools::unesc($r['keywords'])),
						'description'=>trim(Tools::unesc($r['description'])),
						'pos'=>$r['pos']
					);
				$part++;
			}
		}
//	print_r($this->page);
	}
	return @$this->page[$block_id];
}

function block($block_id)
{
	$r=$this->load($block_id);
	if($this->blockChainMode){
		if(is_array($r)){
			$text='';
			foreach($r as $part){
				$text.="<div class=\"{$this->chainBlockClass}\">{$part['text']}</div>";
			}
			if($this->tpl=='') $s=$text;
			else{
				$s=str_replace('#class#',$this->tplClass[$block_id],$this->tpl);
				$s=str_replace('#body#',$text,$s);
			}
			return $s;
		}
	}else{
		if(is_array($r) && Tools::stripTags($r['text'])!=''){
			if($this->tpl=='') $s=$r['text'];
			else{
				$s=str_replace('#class#',$this->tplClass[$block_id],$this->tpl);
				$s=str_replace('#body#',$r['text'],$s);
			}
			return $s;
		}
	}
	return '';
}

// тайттл блока
function title($block_id)
{
	$r=$this->load($block_id);
	if($this->blockChainMode) {
		foreach($r as $part)
			if(trim($part['title'])!='') return $part['title']; 
	}else return $r['title'];
	return '';
}

function kd($blocksOrder=array(2,1))
{
	// просматриваем KD по списку blocksOrder. Возвращаем первые попавшиеся KD

	$res=array('keywords'=>'','description'=>'');
	foreach($blocksOrder as $block_id){
		$r=$this->load($block_id);
		if($this->blockChainMode) {
			if($res['keywords']=='') foreach($r as $part)
				if(trim($part['keywords'])!='') {
					$res['keywords']=$part['keywords']; 
					break 1;
				}
			if($res['description']=='') foreach($r as $part)
				if(trim($part['description'])!='') {
					$res['description']=$part['description']; 
					break 1;
				}
		}else {
			if($res['description']=='' && trim($r['description'])!='') $res['description']=$r['description'];
			if($res['keywords']=='' && trim($r['keywords'])) $res['keywords']=$r['keywords'];
		}
	}
	return $res;
}

// заголовок блока. 
function header($block_id)
{
	$r=$this->load($block_id);
	if($this->blockChainMode) {
		foreach($r as $part)
			if(trim($part['header'])!='') return $part['header']; 
	}else return $r['header'];
	return '';
}

function setChainMode($mode,$chainBlockClass='')
{
	if(!empty($chainBlockClass)) $this->chainBlockClass=$chainBlockClass;
	$this->blockChainMode=$mode;
	
}

}?>