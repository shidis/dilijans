<?
include_once ('ajx_loader.php');

$cp->setFN('pages');
$cp->checkPermissions();

//sleep(1);


$ss=new Pages();

$r->fres=true;
$r->fres_msg='';

$page = @$_REQUEST['page']; // get the requested page
$limit = @$_REQUEST['rows']; // get how many rows we want to have into the grid
$sidx = @$_REQUEST['sidx']; // get index row - i.e. user click to sort
$sord = @$_REQUEST['sord']; // get the direction
if(!$sidx) $sidx =1;
$act=Tools::esc(@$_REQUEST['act']);

switch ($act){
	
	case 'init':
		$ss->que('block_list');
		$r->blocks=array('0'=>'Все');
		while($ss->next()!==false) $r->blocks[$ss->qrow['block_id']]=Tools::html($ss->qrow['name']);
        $r->blocks=(object)$r->blocks;
		break;
		
	case 'list':
		$sq=array();
		if(@$_REQUEST['_search']=='true'){
			$filters=json_decode(@$_GET['filters'],true);
			if(count($filters['rules'])){
				foreach($filters['rules'] as $v){
					if(in_array(@$v['field'],array('block_id','url','param','strict','title','header','pos'))){
						if($v['field']=='url') $v['data']=str_replace('http://'.$_SERVER['HTTP_HOST'],'',$v['data']);
						if($v['field']=='strict' && $v['data']=='-1') continue;
						if($v['field']=='block_id' && $v['data']=='0') continue;
					}
					$q=explode(' ',trim($v['data']));
					foreach($q as $v1) {
						$v1=Tools::like(trim($v1));
						if($v1!='') $sq[]=" ss_pages.{$v['field']} LIKE '%{$v1}%' ";
					}
				}
			}
		}
		if(count($sq)) $where=' AND '.implode('AND',$sq); else $where='';
        $r->where=$where;
		$r->records=$ss->query("SELECT count(page_id) FROM ss_pages WHERE (NOT LD)  $where");
		$ss->next();
		$r->records=$ss->qrow[0];
		if( $r->records ) $total_pages = ceil($r->records/$limit); else $total_pages = 0;
		if ($page > $total_pages) $page=$total_pages;
		$start = $limit*$page - $limit; // do not put $limit*($page - 1)
		if($start<0) $start=0;
		$r->page = $page;
		$r->total = $total_pages;
		if(!$ss->query("SELECT ss_pages.*, ss_pages_blocks.name AS block_name FROM ss_pages LEFT JOIN ss_pages_blocks ON ss_pages.block_id=ss_pages_blocks.block_id WHERE (NOT LD) $where ORDER BY $sidx $sord LIMIT $start,$limit")){
			$r->fres=false;
			break;
		}
		$i=0;
	//	$r->sql=$ss->sql_query;
		$r->qnum=$ss->qnum();
		while($ss->next()!==false){
			$row=$ss->qrow;
			$r->rows[$i]['id']=$row['page_id'];
			$kd=array();
			if($row['keywords']!='') $kd[]='K';
			if($row['description']!='') $kd[]='D';
			$kd=implode(' ',$kd);
			$r->rows[$i]['cell']=array($row['page_id'].(mb_strlen(trim(Tools::stripTags($row['text'])))==0?' (nt)':''),Tools::html(strval($row['block_name'])),Tools::html($row['url']),$row['param']!=''?Tools::html($row['param']):'',($row['strict'])==1?1:0,$row['title']!=''?Tools::html($row['title']):'',$kd,$row['header']!=''?Tools::html($row['header']):'',$row['pos'],'');
			$i++;
		}
	break;
	
	case 'del': $r->fres=$ss->ld('ss_pages','page_id',@intval($_REQUEST['id'])); break;
	
	case 'get_page':
		$ss->que('page_by_id',@intval($_REQUEST['id'])); 
		if(!$ss->qnum()) {$r->fres=false; $r->fres_msg='Не райдена страница с ID='.@intval($_REQUEST['id']);}
		else{
			$r->text=Tools::taria($ss->qrow['text']);
			$r->title=Tools::unesc($ss->qrow['title']);
			$r->keywords=Tools::unesc($ss->qrow['keywords']);
			$r->description=Tools::unesc($ss->qrow['description']);
			$r->header=Tools::unesc($ss->qrow['header']);
			$r->param=Tools::unesc($ss->qrow['param']);
			$r->url='http://'.$_SERVER['HTTP_HOST'].Tools::unesc($ss->qrow['url']);
			$r->block_id=$ss->qrow['block_id'];
			$r->page_id=$ss->qrow['page_id'];
			$r->pos=$ss->qrow['pos'];
			$r->strict=(int)$ss->qrow['strict'];
			$ss->que('block_list');
			$r->block_list='';
			while($ss->next()!==false) $r->block_list.="<option value='{$ss->qrow['block_id']}'".($ss->qrow['block_id']==$r->block_id?'selected':'').">{$ss->qrow['name']}</option>";
		}
		break;
		
	case 'get_block_list':
		$ss->que('block_list');
		$r->block_list='';
		while($ss->next()!==false) $r->block_list.="<option value='{$ss->qrow['block_id']}'>{$ss->qrow['name']}</option>";
		break;
		
	case 'save':
		$f=strarr(($_REQUEST['f']));
		$r->DATA=$f;
		$text=Tools::untaria($f['ed_text0']);
		$title=Tools::esc($f['ed_title']);
		$keywords=Tools::esc($f['ed_keywords'],1);
		$description=Tools::esc($f['ed_description'],1);
		$header=Tools::esc($f['ed_header']);
		$param=Tools::esc($f['ed_param']);
		$strict=(int)@$f['ed_strict'];
		$block_id=intval($f['ed_block_id']);
		$pos=intval($f['ed_pos']);
		$page_id=@intval($f['ed_page_id']);
		$url=parse_url($f['ed_url']);
		if(empty($url['path'])) $url['path']='/';
		$r->PARSE=$url;
		$url=Tools::esc(@$url['path'].(@$url['query']!=''?"?{$url['query']}":'').(@$url['fragment']!=''?"#{$url['fragment']}":''));
		if($url=='') {$r->fres=false; $r->fres_msg='Неверный формат URL';}
		elseif($page_id)
			if(!$ss->query("UPDATE ss_pages SET block_id='$block_id', url='$url', title='$title', keywords='$keywords', description='$description', header='$header', text='$text', strict='$strict', param='$param', pos='$pos' WHERE page_id='$page_id'")){
				$r->fres=false; $r->fres_msg='Ошибка записи БД';
			} else;
		elseif(!$ss->query("INSERT INTO ss_pages (block_id,url,title,keywords,description,header,text,strict,param,pos) VALUES('$block_id','$url','$title','$keywords','$description','$header','$text','$strict','$param','$pos')")) {
			$r->fres=false; $r->fres_msg='Ошибка записи БД';
		}
		break;
		
	case 'query':
		if(@$_REQUEST['strict']==1 || @$_REQUEST['strict']=='true') $strict=1; else $strict=false;
		$param=@$_REQUEST['param'];
		$url=@$_REQUEST['url'];
		if($param!='' && !$strict) $url.=mb_strpos($url,'?')===false?"?$param":"&$param";
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
		$html=curl_exec($ch);
		if(curl_errno($ch)) {
			$r->fres=false; $r->fres_msg='Ошибка чтения адреса: '.curl_errno($ch).' ('.curl_error($ch).')';
			curl_close($ch);
			break;
		}
		curl_close($ch);
		$html=preg_replace("/[\r\n]/",'',$html);
		preg_match("/<title>(.+?)<\/title>/",$html,$m);
		$r->title=@$m[1];
		$r->u=$url;
		break;
		
	default: echo 'BAD ACT ID '.$act;
}

ajxEnd();