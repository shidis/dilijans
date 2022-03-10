<?
abstract class TFields
{
    // Tfields::array() ->
    // gr,caption,as,type,widget,dbType - обязательные поля
    // widget:  {editor,input,checkbox,select,textarea}, если путо, то автоматически поле не генерируется в форме
    // для widget[editor] опционально задается Height и ToolbarSet - для визуального редактора  (TODO разобраться надо ли)
    // style: string - стили для элемента в формате css
    // поддерживаются c_cat, cc_model, cc_brand, os_user, os_order

    static $fields=array();

    static public function formEl($table,$widget='all',$gr='all',$def=array(),$esc=true)
    {
        $r=array();
        if(!isset(static::$fields[$table])) return array();
        foreach(static::$fields[$table] as $k=>$v){
            if((@$v['widget']==$widget || $widget=='all') && ($gr=='all' || @$v['gr']==$gr || @$v['gr']=='12')) {
                $style=@$v['style'];
                switch ($v['widget']){
                    case 'input': $r[$k]=array($v['caption'],"<input style=\"{$style}\" type=\"text\" name=\"af[{$k}]\" value=\"".(@$def[$k]!=''?Tools::html($def[$k],$esc):'')."\">");
                        break;
                    case 'textarea': $r[$k]=array($v['caption'],"<textarea style=\"{$style}\" name=\"af[{$k}]\">".(@$def[$k]!=''?Tools::taria($def[$k],$esc):'')."</textarea>");
                        break;
                    case 'checkbox': $r[$k]=array($v['caption'],"<input style=\"{$style}\" type=\"checkbox\"".(@$def[$k]?'checked':'')." name=\"af[{$k}]\" value=\"{$v['value']}\">");
                        break;
                    case 'select':
                        $s="<select style=\"{$style}\" name=\"af[{$k}]\">";
                        foreach($v['varList'] as $k1=>$v1){
                            $s.="<option value=\"{$k1}\"".(@$def[$k]==$k1?' selected':'').">{$v1}</option>";
                        }
                        $s.='</select>';
                        $r[$k]=array($v['caption'],$s);
                        break;
                }
            }
        }
        return $r;
    }

    static public function get($table,$widget='all',$gr='all')
    {
        $r=array();
        if(!isset(static::$fields[$table])) return $r;
        foreach(static::$fields[$table] as $k=>$v){
            if((@$v['widget']==$widget || $widget=='all') && ($gr=='all' || @$v['gr']==$gr || @$v['gr']=='12')) $r[$k]=$v;
        }
        return $r;
    }

    static public function DBselect($table,$gr='all',$prefix=',')
    {
        if(!isset(static::$fields[$table])) return '';
        $r=array();
        foreach(static::$fields[$table] as $k=>$v){
            if($gr=='12' || $gr==@$v['gr'] || @$v['gr']=='12' || $gr=='all') $r[]="$table.$k AS {$v['as']}";
        }
        $r=implode(",",$r);
        return $r!=''?($prefix.$r):'';
    }

    // учитывается префикс tmh_ как хак для тинимсе
    static public function DBinsert($table,$vals,$gr='all',$esc=true,$prefix=',')
    {
        if(!isset(static::$fields[$table]) || !is_array($vals)) return array('','');
        $values=array();
        $val=array();
        foreach(static::$fields[$table] as $k=>$v){
            if(($gr=='12' || $gr==@$v['gr'] || @$v['gr']=='12' || $gr=='all') && isset($vals[$k])) {
                if(@$v['widget']=='editor'){
                    if(isset($vals['tmh_'.$k]) && $vals['tmh_'.$k]!=''){
                        $values[]=$k;
                        $vals[$k]=Tools::untaria($vals['tmh_'.$k],$esc);
                        $val[]="'{$vals[$k]}'";
                    }else{
                        $values[]=$k;
                        $vals[$k]=Tools::untaria($vals[$k],$esc);
                        $val[]="'{$vals[$k]}'";
                    }
                }else{
                    $values[]=$k;
                    if($esc) $vals[$k]=Tools::esc($vals[$k]);
                    $val[]="'{$vals[$k]}'";
                }
            }
        }
        $s1=implode(",",$values);
        $s2=implode(",",$val);
        return array($s1!=''?($prefix.$s1):'',$s2!=''?($prefix.$s2):'');
    }

    // учитывается префикс tmh_ как хак для тинимсе
    // force = true - если в $vals нет поля из static::$fields[$table] то значение в базе для него сотрется
    static public function DBupdate($table,$vals,$gr='all',$esc=true,$prefix=',',$force=true)
    {
        if(!isset(static::$fields[$table]) || !is_array($vals)) return '';
        $r=array();
        foreach(static::$fields[$table] as $k=>$v){
            if(($gr=='12' || mb_strpos(@$v['gr'],$gr)!==false || @$v['gr']=='12' || $gr=='all')) {
                if(@$v['widget']=='editor'){
                    if(isset($vals['tmh_'.$k]) && $vals['tmh_'.$k]!=''){
                        $vals[$k]=Tools::untaria($vals['tmh_'.$k],$esc);
                        $r[]="$k='{$vals[$k]}'";
                    }else{
                        if(isset($vals[$k])) {
                            $vals[$k]=Tools::untaria($vals[$k],$esc);
                            $r[]="$k='{$vals[$k]}'";
                        }elseif($force) $r[]="$k=''";
                    }
                }else{
                    if(isset($vals[$k])) {
                        if($esc) $vals[$k]=Tools::esc($vals[$k]);
                        $r[]="$k='{$vals[$k]}'";
                    }
                    elseif($force) $r[]="$k=''";
                }
            }
        }
        $r=implode(",",$r);
        return $r!=''?($prefix.$r):'';
    }

}