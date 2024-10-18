<?

class Content extends SSCommon
{
    use TextParser;

    public
        $typesTree,
        $cnt_type_id,
        $cnt_id,
        $cnt_text,
        $img1,$img2,
        $cnt_intro,
        $link,
        $published,
        $cnt_title,
        $docsNum,
        $meta;



    function __construct()
    {
        parent::__construct();
    }

    private function _getTree($parent_id,&$parent)
    {
        static $level=0;

        $sql="SELECT *, (SELECT count(*) FROM ss_cnt_type WHERE parent_id=t1.cnt_type_id) AS ch FROM ss_cnt_type t1 WHERE parent_id=$parent_id ORDER BY pos, name";
        $this->query($sql);
        $d=$this->fetchAll('',MYSQLI_ASSOC);
        foreach($d as $v){
            $vv=array(
                'childrens'=>array(),
                'level'=>$level,
                'name'=>Tools::unesc($v['name']),
                'pos'=>$v['pos'],
                'type'=>$v['type'],
                'description'=>Tools::unesc($v['description']),
                'noDoubleIn'=>explode(',',$v['noDoubleIn'])
            );
            $parent[$v['cnt_type_id']]=$vv;

            if($v['ch']){
                $level++;
                $this->_getTree($v['cnt_type_id'], $parent[$v['cnt_type_id']]['childrens']);
                $level--;
            }
        }
    }

    public function getTree()
    {
        $this->typesTree=array();
        $this->_getTree(0,$this->typesTree);
        return $this->typesTree;
    }

    public function cntList($r=array())
    {
        /*
         * параметры:
         * published {0|1|null} - 1 - возвращать только опубликованные, 0 - не опубликованные, null - все, по умолчанию =1
         * rIds - {int|array) - ids рубрик
         * order - {string|array} - условия для сортировки. По умолчанию ORDER BY pos DESC, publishedDate DESC, dt_added DESC, title ASC. Значения не экранируются.
         * where -  {string|array} - условие для отбора WHERE. Значения не экранируются.
         * start,limit - {int} - LIMIT - если указан limit то будет сделано два запроса и вычислено $this->docsNum
         * exDocIds - {int|array} - исключить доки с указанными ID
         * fields  -array() - дополнительные поля, например array('intro','text','description')
         * parse - {0|1} - парсить переменные в документах/ по умолчанию = 0 потеряло актуальность: переменных в контексте этого объекта как правило не бывает
         */

        if(!isset($r['parse'])) $parse=0; else $parse=$r['parse'];

        if(!empty($r['where']))
            if(is_array($r['where'])) $where = $r['where'];
            else $where[] = $r['where'];
        else $where=array();

        if(!empty($r['order']))
            if(is_array($r['order'])) $order = $r['order'];
            else $order[] = $r['order'];
        else $order=array();

        $rids=array();
        if(!empty($r['rIds']))
            if(is_array($r['rIds'])) {
                foreach($r['rIds'] as $v) if(!empty($v)) $rids[]=(int)$v;
            } else $rids[]=(int)$r['rIds'];

        if(!empty($rids)) $where[]="cnt_type_id IN (".implode(',',$rids).")";

        if(!isset($r['published'])) $r['published']=1;

        if(!is_null($r['published']))
            if(@$r['published']) $where[]="published=1";
            elseif(!@$r['published']) $where[]="published=0";

        if(!empty($r['exDocIds']))
            if(is_array($r['exDocIds'])) {
                foreach($r['exDocIds'] as $v) if(!empty($v)) $where[]="cnt_id!=".(int)$v;
            } else $where[]="cnt_id!=".(int)$r['exDocIds'];

        if(!empty($r['limit'])) $limits="LIMIT ".(int)abs(@$r['start']).", ".(int)abs($r['limit']); else $limits='';

        if(empty($r['fields'])) $fields=array(); else $fields=$r['fields'];

        $where=implode(" AND ",$where);
        if(!empty($where)) $where=" AND $where";
        $order=implode(", ",$order);
        if(!empty($order)) $order=" ORDER BY $order"; elseif(!isset($r['order'])) $order="ORDER BY pos DESC, publishedDate DESC, dt_added DESC, title ASC"; else $order='';

        if(!empty($limits)){
            $this->getOne("SELECT count(*) FROM ss_cnt WHERE NOT LD $where");
            $this->docsNum=$this->qrow[0];
        }
        $d=$this->fetchAll($sql="SELECT * FROM ss_cnt WHERE NOT LD $where $order $limits", MYSQLI_ASSOC);
        $res=array();
        if(!empty($d))
            foreach($d as $v){
                $vi=array(
                    'cnt_id'=>$v['cnt_id'],
                    'cnt_type_id'=>$v['cnt_type_id'],
                    'title'=>Tools::html($v['title']),
                    'sname'=>Tools::unesc($v['sname']),
                    'dt_added'=>Tools::sDateTime($v['dt_added']),
                    'publishedDate'=>Tools::sdate($v['publishedDate']),
                    'pos'=>$v['pos'],
                    'img1'=>$this->makeImgPath($v['img1']),
                    'img2'=>$this->makeImgPath($v['img2']),
                    'link'=>Tools::unesc($v['link']),
                    'published'=>$v['published']
                );
                if(Cfg::get('php_eval_enabled') && $parse){
                    if(in_array('text',$fields)) $vi['text']=$this->parse(Tools::unesc($v['text']));
                    if(in_array('intro',$fields)) $vi['intro']=$this->parse(Tools::unesc($v['intro']));
                    if(in_array('keywords',$fields)) $vi['keywords']=$this->parse(Tools::html($v['keywords']));
                    if(in_array('description',$fields)) $vi['description']=$this->parse(Tools::html($v['description']));
                }else{
                    if(in_array('text',$fields)) $vi['text']=Tools::unesc($v['text']);
                    if(in_array('intro',$fields)) $vi['intro']=Tools::unesc($v['intro']);
                    if(in_array('keywords',$fields)) $vi['keywords']=Tools::html($v['keywords']);
                    if(in_array('description',$fields)) $vi['description']=Tools::html($v['description']);
                }
                $res[]=$vi;
            }
        return $res;
    }

    /*
     * получение документа по псевдониму. Переменные отрабатываеются
     * если документ не опубликован или не существует - возвращает пустую строку
     */
    function getParsed($sname,$onlyPublished=true)
    {
        $doc=$this->getParseCnt($sname);
        if($onlyPublished && $this->published || !$onlyPublished) return $doc;
        else return '';
    }

    /*
     * получение документа по псевдониму. Переменные НЕ отрабатываеются
     * если документ не опубликован или не существует - возвращает пустую строку
     */
    function getDoc($sname,$onlyPublished=true)
    {
        $doc=$this->getParseCnt($sname, false);
        if($onlyPublished && $this->published || !$onlyPublished) return $doc;
        else return '';
    }

    /*
     * возвращает документ по псевдониму
     * обрезает по словам до нужной длины $len
     * парсит переменные и сниппеты в полях text & intro если noparse==false и php_eval_enabled==true
     * sname переадется как sname$rId  или sname$rId1,$rId2... или просто как sname
     */
    function getParseCnt($sname='', $parse=true)
    {
        $this->cnt_title=$s=$this->cnt_text='';
        $sname=explode('$',$sname);  // $ - разделитель рубрики
        $ss=new Content();
        $this->cnt_id=0;
        if($sname[0]!=''){
            $ss->que('cnt_by_sname',$sname[0],@$sname[1]);
            if($ss->qnum()){
                $this->cnt_title=Tools::html($ss->qrow['title']);
                $this->cnt_intro=Tools::unesc($ss->qrow['intro']);
                $s=Tools::unesc($ss->qrow['text']);
                $this->cnt_id=$ss->qrow['cnt_id'];
                $this->cnt_type_id=$ss->qrow['cnt_type_id'];
                $this->img1=$ss->makeImgPath(1);
                $this->img2=$ss->makeImgPath(2);
                $this->link=Tools::unesc($ss->qrow['link']);
                $this->published=$ss->qrow['published'];
                $this->meta=array('description'=>Tools::html($ss->qrow['description']),'keywords'=>Tools::html($ss->qrow['keywords']));
            }else $this->cnt_id=0;
        }else {
            $s=Tools::unesc(@$this->qrow['text']);
            $this->cnt_title=Tools::html(@$this->qrow['title']);
            $this->cnt_intro=Tools::unesc(@$this->qrow['intro']);
            $this->cnt_id=@$this->qrow['cnt_id'];
            $this->cnt_type_id=@$this->qrow['cnt_type_id'];
            $this->link=Tools::unesc(@$this->qrow['link']);
            $this->published=@$this->qrow['published'];
            $this->meta=array('description'=>Tools::html(@$this->qrow['description']),'keywords'=>Tools::html(@$this->qrow['keywords']));
        }
        if(Cfg::get('php_eval_enabled') && $parse){
            $s=$this->parse($s);
            if(!empty($this->cnt_intro)) $this->cnt_intro=$this->parse($this->cnt_intro);
            if(!empty($this->meta['keywords'])) $this->meta['keywords']=$this->parse($this->meta['keywords']);
            if(!empty($this->meta['description'])) $this->meta['description']=$this->parse($this->meta['description']);
        }
        unset($ss);
        $this->cnt_text=$s;
        return($s);
    }


    function que($qname,$cond1='',$cond2='',$cond3='',$cond4='')
    {
        $res=false;
        switch ($qname)
        {
            case 'cnt_type_list':
                if($cond1!=='') $cond1=(int)$cond1; // parent_id
                if($cond1!=='') $res=$this->query("SELECT * FROM ss_cnt_type WHERE parent_id=$cond1 ORDER BY pos, name");
                else $res=$this->query("SELECT * FROM ss_cnt_type ORDER BY pos, name");
                break;
            case 'cnt_type_by_id':
                $cond1=(int)$cond1;
                $res=$this->query("SELECT * FROM ss_cnt_type WHERE cnt_type_id=$cond1");
                $this->next();
                break;
            case 'cnt_list':
                $cond1=$cond1;  // cnt_type_id, integer или перечисление в массиве
                $cond2=Tools::esc($cond2); // start,limit
                $cond3=Tools::esc($cond3);  //  order
                $cond4=$cond4; // aditional query
                if(is_array($cond1)) $cond1=" AND cnt_type_id IN (".implode(',',$cond1).")"; elseif(!empty($cond1)) {
                    $cond1=(int)$cond1;
                    $cond1=" AND cnt_type_id='$cond1'";
                }
                $res=$this->query("SELECT * FROM ss_cnt WHERE (NOT LD) $cond1 ".($cond4!=''?" AND ($cond4)":'').($cond3==''?'ORDER BY pos DESC, publishedDate DESC, dt_added DESC, title ASC':"ORDER BY $cond3").($cond2!=''?" LIMIT $cond2":''));
                break;
            case 'cnt_by_id':
                $cond1=intval($cond1);
                $cond2=intval($cond2);
                $res=$this->query("SELECT * FROM ss_cnt WHERE NOT LD AND cnt_id='$cond1' ".($cond2?"AND cnt_type_id='$cond2'":''));
                $this->next();
                break;
            case 'cnt_by_sname':
                $cond1=Tools::esc($cond1); //sname
                if($cond2!=''){
                    $cond2=explode(',',$cond2);   // cnt_type_id = integer || array
                    foreach($cond2 as $k=>&$v) $v="'".intval($v)."'";
                    $cond2=implode(',',$cond2);
                }else $cond2=intval($cond2);
                $res=$this->query("SELECT * FROM ss_cnt WHERE NOT LD AND sname='$cond1'".($cond2!=''?" AND cnt_type_id IN($cond2)":''));
                $this->next();
                break;
        }
        return $res;
    }


}