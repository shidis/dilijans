<?
if (!defined('true_enter')) die ("Direct access not allowed!");

class CC_tags extends DB
{
    public $groups=array();
    public $tags=array();


    public function groupsList($p=array()){
        // gr - обязательный парметр
        $gr=!empty($p['gr'])?(int)$p['gr']:0;
        if(!$gr) {
            return $this->putMsg(false,'[CC_Tags.groupList]: Не задана товарная группа');
        }
        $d=$this->fetchAll("SELECT * FROM cc_tag_group WHERE gr=$gr ORDER BY pos DESC");
        $this->groups=array();
        foreach($d as $v){
            $this->groups[(string)$v['tag_group_id']]=array(
                'name'=>Tools::html($v['name']),
                'sname'=>Tools::html($v['sname']),
                'pos'=>$v['pos'],
                'param'=>Tools::DB_unserialize($v['param']),
                'info'=>Tools::taria($v['info'])
            );
        }
        if(empty($p['json'])) return $this->groups; else return json_encode($this->groups);
    }

    public function tagsList($p=array())
    {
        // gr - обязательный парметр если не передан tagIds = array(id1, id2,...)
        // теги должны быть в одной из теговых групп, но если tag_group_id ==0 то будет возвращено дерево тегов по всем группам
        // $p['tagIds'] - список ИД тегов для дополнительно отсева результата
        // shortData == 1 - сокращенный результат для формирования списка тегов
        // json == 1 - возврат в виде массива json

        $tg=!empty($p['tag_group_id'])?(int)$p['tag_group_id']:0;
        $gr=!empty($p['gr'])?(int)$p['gr']:0;
        if(!empty($p['tagIds'])){
            $tagIds=$p['tagIds'];
            $_tagIds=implode(',',$tagIds);
        }else $tagIds=false;

        if(!$gr && !$tg && empty($tagIds)) {
            return $this->putMsg(false,'[CC_Tags.tagsList]: Не задана товарная и теговая группа');
        }
        if($tagIds!==false && empty($tagIds)) return $this->tags=array();


        $d=$this->fetchAll("SELECT cc_tag.*, cc_tag_group.name AS group_name, cc_tag_group.sname AS group_sname FROM cc_tag JOIN cc_tag_group USING (tag_group_id) WHERE 1=1".(!empty($tg)?" AND cc_tag.tag_group_id=$tg":'').(!empty($gr)?" AND cc_tag.gr=$gr":'').(!empty($tagIds)?" AND cc_tag.tag_id IN ($_tagIds)":'')." ORDER BY cc_tag_group.pos DESC, cc_tag_group.name ASC, cc_tag.pos DESC, cc_tag.name ASC",MYSQL_ASSOC);

        //echo $this->sql_query;
        if($tg) $this->tags[$tg]=array(); else $this->tags=array();
        foreach($d as $v){
            if(!empty($p['shortData']))
                $row=array(
                    'tag_id'=>$v['tag_id'],
                    'name'=>Tools::html($v['name']),
                    'sname'=>Tools::html($v['sname']),
                    'alt'=>Tools::html($v['alt']),
                    'group_name'=>Tools::html($v['group_name']),
                    'group_sname'=>Tools::html($v['group_sname'])
                );
            else
                $row=array(
                    'tag_id'=>$v['tag_id'],
                    'name'=>Tools::html($v['name']),
                    'sname'=>Tools::html($v['sname']),
                    'group_name'=>Tools::html($v['group_name']),
                    'group_sname'=>Tools::html($v['group_sname']),
                    'alt'=>Tools::html($v['alt']),
                    'text'=>Tools::taria($v['text']),
                    'keywords'=>Tools::taria($v['keywords']),
                    'description'=>Tools::taria($v['description']),
                    'param'=>Tools::DB_unserialize($v['param'])
                );
           $this->tags[(string)$v['tag_group_id']][]=$row;
        }
        if(!empty($p['json']))
            if($tg) return json_encode($this->tags[$tg]); else return json_encode($this->tags);
        else
            if($tg) return $this->tags[$tg]; else return $this->tags;
    }

    public function addTagGroup($p=array())
    {
        $gr=!empty($p['gr'])?(int)$p['gr']:0;
        if(!$gr) {
            return $this->putMsg(false,'[CC_Tags.addTagGroup]: Не задана товарная группа');
        }
        $name=Tools::esc(trim(@$p['name']));
        if(empty($name)) {
            return $this->putMsg(false,'[CC_Tags.addTagGroup]: Не введено имя группы');
        }
        $pos=@(int)$p['pos'];
        if(empty($p['sname'])){
            $sname=Tools::str2iso($name,@Cfg::$config['ccTags']['sname_len'],@Cfg::$config['ccTags']['sname_reg']);
        }else
            $sname=Tools::str2iso($p['sname'],@Cfg::$config['ccTags']['sname_len'],@Cfg::$config['ccTags']['sname_reg']);

        $d=$this->getOne("SELECT count(*) FROM cc_tag_group WHERE (sname LIKE '$sname' OR name LIKE '$name') AND gr=$gr");
        if($d[0]){
            return $this->putMsg(false,'[CC_Tags.addTagGroup]: Дубликат - не добавлено');
        }
        $info=Tools::esc(trim(@$p['info']));
        $res=$this->insert('cc_tag_group',array('gr'=>$gr,'name'=>$name,'info'=>$info,'pos'=>$pos,'sname'=>$sname));
        return $res;
    }

    public function changeTagGroup($p=array())
    {
        $id=!empty($p['id'])?(int)$p['id']:0;
        if(!$id) {
            return $this->putMsg(false,'[CC_Tags.changeTagGroup]: Не задан ID группы');
        }
        $name=Tools::untaria(trim(@$p['name']));
        if(empty($name)) {
            return $this->putMsg(false,'[CC_Tags.changeTagGroup]: Не введено имя группы');
        }
        $pos=@(int)$p['pos'];
        $d=$this->getOne("SELECT * FROM cc_tag_group WHERE tag_group_id=$id");
        if($d===0){
            return $this->putMsg(false,'[CC_Tags.changeTagGroup]: Не найдена группа ИД='.$id);
        }
        $gr=$d['gr'];
        if(empty($p['sname'])){
            $sname=Tools::str2iso($name,@Cfg::$config['ccTags']['sname_len'],@Cfg::$config['ccTags']['sname_reg']);
        }else
            $sname=Tools::str2iso($p['sname'],@Cfg::$config['ccTags']['sname_len'],@Cfg::$config['ccTags']['sname_reg']);

        $d=$this->getOne("SELECT count(*) FROM cc_tag_group WHERE (sname LIKE '$sname' OR name LIKE '$name') AND tag_group_id!=$id AND gr=$gr");
        if($d[0]){
            return $this->putMsg(false,'[CC_Tags.changeTagGroup]: Дубликат - изменения не внесены');
        }
        $info=Tools::untaria(trim(@$p['info']));
        $res=$this->update('cc_tag_group',array('name'=>$name,'info'=>$info,'pos'=>$pos,'sname'=>$sname),"tag_group_id=$id");
        return $res;
    }

    public function getGroup($id)
    {
        $id=(int)$id;
        if(!$id) {
            return $this->putMsg(false,'[CC_Tags.getGroup]: Не задана теговая группа');
        }
        $d=$this->getOne("SELECT * FROM cc_tag_group WHERE tag_group_id=$id");
        if($d===0) return $this->putMsg(false,'[CC_Tags.getGroup]: Не найдена теговая группа id='.$id);
        return array(
            'name'=>Tools::html($d['name']),
            'sname'=>Tools::html($d['sname']),
            'info'=>Tools::taria($d['info']),
            'pos'=>$d['pos'],
            'param'=>Tools::DB_unserialize($d['param'])
        );
    }

    public function removeGroup($id)
    {
        $id=(int)$id;
        if(!$id) {
            return $this->putMsg(false,'[CC_Tags.removeGroup]: Не задан ID группы');
        }
        $d=$this->getOne("SELECT * FROM cc_tag_group WHERE tag_group_id=$id");
        if($d===0){
            return $this->putMsg(false,'[CC_Tags.removeGroup]: Не найдена группа ИД='.$id);
        }
        $gr=$d['gr'];

        // находим все теги с группой
        $d=$this->fetchAll("SELECT tag_id FROM cc_tag WHERE tag_group_id=$id",MYSQL_NUM);
        $tagIds=array();
        foreach($d as $v) $tagIds[]=$v[0];

        // выпиливаем ИД тегов из моделей
        $this->tagIds=$tagIds;
        $this->updatedModels=0;
        foreach($tagIds as $tag_id){
            $this->query("UPDATE cc_model SET tags=REPLACE(tags,'.{$tag_id}.','.') WHERE gr=$gr AND tags LIKE '.%.{$tag_id}.%' OR tags LIKE '.{$tag_id}.%'");
            $this->updatedModels+=$this->unum();
        }

        // удаляем все теги группы
        $this->query("DELETE FROM cc_tag WHERE tag_group_id=$id");
        $this->deletedTags=$this->unum();

        // удаляем саму группу
        $res=$this->query("DELETE FROM cc_tag_group WHERE tag_group_id=$id");
        return $res;
    }

    public function addTag($p=array())
    {
        $gid=(int)$p['tag_group_id'];
        if(!$gid) {
            return $this->putMsg(false,'[CC_Tags.addTag]: Не задана теговая группа');
        }
        $d=$this->getOne("SELECT * FROM cc_tag_group WHERE tag_group_id=$gid");
        if($d===0) return $this->putMsg(false,'[CC_Tags.addTag]: Не найдена теговая группа gid='.$gid);
        $gr=$d['gr'];

        $name=Tools::esc(trim(@$p['name']));
        if(empty($name))
            return $this->putMsg(false,'[CC_Tags.addTag]: Пустое название тега');

        $alt=Tools::esc(trim(@$p['alt']));
        $text=Tools::esc(trim(@$p['text']));
        $keywords=Tools::esc(trim(@$p['keywords']));
        $description=Tools::esc(trim(@$p['description']));

        if(empty($p['sname']))
            $sname=Tools::str2iso($name,@Cfg::$config['ccTags']['sname_len'],@Cfg::$config['ccTags']['sname_reg']);
        else
            $sname=Tools::str2iso($p['sname'],@Cfg::$config['ccTags']['sname_len'],@Cfg::$config['ccTags']['sname_reg']);

        $d=$this->getOne("SELECT count(*) FROM cc_tag WHERE (sname LIKE '$sname' OR name LIKE '$name') AND gr=$gr");
        if($d[0])
            return $this->putMsg(false,'[CC_Tags.addTag]: Дубликат имени тега или псевдонима - не добавлено');

        $res=$this->insert('cc_tag',array('gr'=>$gr, 'tag_group_id'=>$gid, 'name'=>$name, 'sname'=>$sname, 'alt'=>$alt, 'text'=>$text, 'keywords'=>$keywords, 'description'=>$description));
        return $res;
    }

    public function removeTag($id)
    {
        $id=(int)$id;
        if(!$id) {
            return $this->putMsg(false,'[CC_Tags.removeTag]: Не задан ID тега');
        }
        $d=$this->getOne("SELECT cc_tag.gr FROM cc_tag WHERE tag_id=$id");
        if($d===0){
            return $this->putMsg(false,'[CC_Tags.removeTag]: Не найден тег ИД='.$id);
        }
        $gr=$d['gr'];

        // выпиливаем ИД тега из моделей
        $this->query("UPDATE cc_model SET tags=REPLACE(tags,'.{$id}.','.') WHERE gr=$gr AND tags LIKE '.%.{$id}.%' OR tags LIKE '.{$id}.%'");
        $this->updatedModels=$this->unum();

        // удаляем все теги группы
        $res=$this->query("DELETE FROM cc_tag WHERE tag_id=$id");
        return $res;
    }

    public function editTag($p=array())
    {
        $id=!empty($p['tag_id'])?(int)$p['tag_id']:0;
        if(!$id) {
            return $this->putMsg(false,'[CC_Tags.editTag]: Не задан ID тега');
        }
        $name=Tools::esc(trim(@$p['name']));
        if(empty($name)) {
            return $this->putMsg(false,'[CC_Tags.editTag]: Не введено название тега');
        }

        $d=$this->getOne("SELECT gr,name FROM cc_tag WHERE tag_id=$id");
        if($d===0){
            return $this->putMsg(false,'[CC_Tags.editTag]: Не найден тег ИД='.$id);
        }
        $gr=$d['gr'];
        $this->oldTagName=Tools::taria($d['name']);

        if(empty($p['sname'])){
            $sname=Tools::str2iso($name,@Cfg::$config['ccTags']['sname_len'],@Cfg::$config['ccTags']['sname_reg']);
        }else
            $sname=Tools::str2iso($p['sname'],@Cfg::$config['ccTags']['sname_len'],@Cfg::$config['ccTags']['sname_reg']);

        $d=$this->getOne("SELECT count(*) FROM cc_tag WHERE (sname LIKE '$sname' OR name LIKE '$name') AND tag_id!=$id AND gr=$gr");
        if($d[0]){
            return $this->putMsg(false,'[CC_Tags.editTag]: Дубликат - изменения не внесены!');
        }
        $q=array('name'=>$name,'sname'=>$sname);
        if(isset($p['alt']))  $q['alt']=Tools::untaria(trim(@$p['alt']));
        if(isset($p['text']))  $q['text']=Tools::untaria(trim(@$p['text']));
        if(isset($p['keywords']))  $q['keywords']=Tools::untaria(trim(@$p['keywords']));
        if(isset($p['description']))  $q['description']=Tools::untaria(trim(@$p['description']));

        $res=$this->update('cc_tag',$q,"tag_id=$id");
        if(!$res) return $res; else return Tools::taria($name);
    }

    public function modelTagsChange($p=array())
    {
        $mid=!empty($p['model_id'])?(int)$p['model_id']:0;
        if(!$mid) {
            return $this->putMsg(false,'[CC_Tags.modelTagsChange]: Не задан ID модели');
        }
        $d=$this->getOne("SELECT gr FROM cc_model WHERE model_id=$mid");
        if($d===0){
            return $this->putMsg(false,'[CC_Tags.modelTagsChange]: Не найдена модель ИД='.$mid);
        }
        $gr=$d[0];

        if(!is_array(@$p['tagIds']) && @$p['tagIds']!=''){
            return $this->putMsg(false,'[CC_Tags.modelTagsChange]: Некорректный формат списка тегов');
        }
        $tags='';
        if(is_array($p['tagIds'])) {
            foreach($p['tagIds'] as $k=>&$v){
                $v=(int)$v;
                $tags.=".$v";
            }
            $tags.='.';
            $tagIds=implode(',',$p['tagIds']);

            $d=$this->getOne("SELECT count(*) FROM cc_tag WHERE tag_id IN({$tagIds}) AND gr=$gr");
            if($d===0){
                return $this->putMsg(false,'[CC_Tags.modelTagsChange]: Не все ИД тегов реально существуют');
            }
        }

        $res=$this->query("UPDATE cc_model SET tags='{$tags}' WHERE model_id=$mid");

        return $res;
    }

}