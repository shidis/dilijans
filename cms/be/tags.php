<?
include_once ('ajx_loader.php');

//sleep(1);

$cp->setFN('tags');
$cp->checkPermissions();

$r->fres=true;
$r->fres_msg='';

$act=Tools::esc(@$_REQUEST['act']);
$gr=@$_REQUEST['gr'];

$db=new DB();
$tags=new CC_Tags();

switch ($act){
    case 'groupList':
        $gr=@(int)$_REQUEST['gr'];
        if(!$gr) {$r->fres=false; $r->fres_msg='[tags.ax.groupList]: Нет gr';}
        $r->groups=$tags->groupsList(array('gr'=>$gr));
        $r->fres=$tags->fres;
        if(empty($tags->fres_msg) && !$r->fres) $r->fres_msg='[tags.ax.groupList]: Ошибка'; else $r->fres_msg=$tags->fres_msg;
        break;

    case 'addGroup':
        $gr=@(int)$_REQUEST['gr'];
        if(!$gr) {$r->fres=false; $r->fres_msg='[tags.ax.groupList]: Нет gr';}
        $r->fres=$tags->addTagGroup(array_merge(array('gr'=>$gr),Tools::parseStr(@$_REQUEST['frm'])));
        if(!$r->fres) if(empty($tags->fres_msg)) $r->fres_msg='[tags.ax.addGroup]: Ошибка добавления'; else $r->fres_msg=$tags->fres_msg;
        break;

    case 'loadGroup':
        $id=@(int)$_REQUEST['id'];
        $r->fres=$r->frm=$tags->getGroup($id);
        if(!$r->fres) if(empty($tags->fres_msg)) $r->fres_msg='[tags.ax.loadGroup]: Ошибка'; else $r->fres_msg=$tags->fres_msg;
        break;

    case 'commitChangesGroup':
        $id=@(int)$_REQUEST['id'];
        $r->fres=$tags->changeTagGroup(array_merge(array('id'=>$id),Tools::parseStr(@$_REQUEST['frm'])));
        if(!$r->fres) if(empty($tags->fres_msg)) $r->fres_msg='[tags.ax.commitChangesGroup]: Ошибка'; else $r->fres_msg=$tags->fres_msg;
        break;

    case 'removeGroup':
        $id=@(int)$_REQUEST['id'];
        $r->fres=$tags->removeGroup($id);
        if(!$r->fres) if(empty($tags->fres_msg)) $r->fres_msg='[tags.ax.removeGroup]: Ошибка'; else $r->fres_msg=$tags->fres_msg;
        $r->tagIds=@$tags->tagIds;
        $r->updatedModels=@$tags->updatedModels;
        $r->deletedTags=@$tags->deletedTags;
        break;

    case 'tagsList':
        $gr=@(int)$_REQUEST['gr'];
        $tg=@(int)$_REQUEST['gid'];
        $r->tags=$tags->tagsList(array('gr'=>$gr,'tag_group_id'=>$tg,'shortData'=>1));
        $r->fres=$tags->fres;
        if(empty($tags->fres_msg) && !$r->fres) $r->fres_msg='[tags.ax.tagsList]: Ошибка загрузки тегов'; else $r->fres_msg=$tags->fres_msg;
        $r->groups=$tags->groupsList(array('gr'=>$gr));
        $r->fres=$tags->fres;
        if(empty($tags->fres_msg) && !$r->fres) $r->fres_msg='[tags.ax.tagsList]: Ошибка загрузки групп'; else $r->fres_msg=$tags->fres_msg;
        break;

    case 'addTag':
        $gid=@(int)$_REQUEST['gid'];
        $tagName=@$_REQUEST['name'];
        $r->fres=$tags->addTag(array('tag_group_id'=>$gid,'name'=>$tagName));
        if(!$r->fres) if(empty($tags->fres_msg)) $r->fres_msg='[tags.ax.addTag]: Ошибка добавления тега'; else $r->fres_msg=$tags->fres_msg;
        break;

    case 'removeTag':
        $id=@(int)$_REQUEST['tag_id'];
        $r->fres=$tags->removeTag($id);
        if(!$r->fres) if(empty($tags->fres_msg)) $r->fres_msg='[tags.ax.removeTag]: Ошибка'; else $r->fres_msg=$tags->fres_msg;
        $r->updatedModels=@$tags->updatedModels;
        break;

    case 'inlineEditTag':
        $r->textOutput=true;
        $r->prependFresMsg=true;
        $res=$tags->editTag(array('tag_id'=>@$_REQUEST['id'],'name'=>@$_REQUEST['value']));
        if($res===false) echo $tags->oldTagName; else echo $res;
        break;

    case 'modelTagsChange':
        $r->fres=$tags->modelTagsChange(array('tagIds'=>@$_REQUEST['tagIds'],'model_id'=>@$_REQUEST['model_id']));
        if(!$r->fres) if(empty($tags->fres_msg)) $r->fres_msg='[tags.ax.modelTagsChange]: Ошибка'; else $r->fres_msg=$tags->fres_msg;
        break;


    default: $r->fres=false; $r->fres_msg='BAD ACT_CASE '.$act;
}

ajxEnd();
