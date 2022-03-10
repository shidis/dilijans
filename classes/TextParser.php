<?
trait TextParser
{

    public function parse($s)
    {
        return $this->parseText($s);
    }

    /*
 * парсит переменные и сниппеты в строке
 * сниппеты распознаются по шаблону {{snippet_name}}  Файл сниппета должен лежать в snippets_dir/snippet_name.php
 * в сниппете можно подучить доступ к переменным контроллера как $this->linkObj->variable
 * переменные #var#
 * тег <noparse /> в строке  выключает парсинг переменных и сниппетов, строка возвращается в неизменном виде
 */

    public function parseText($s)
    {
        if(preg_match("/<noparse[^>]*>/i",$s)) {
            $s=preg_replace("/<noparse[^>]*>/i",'',$s);
            return $s;
        }
        if(Cfg::get('php_eval_enabled')){
            preg_match_all("/\{\{([^}]+)\}\}/i",$s,$m);
            for($i=0;$i<count($m[0]);$i++){
                // http://php.yar.ru/manual/en/public function.eval.php
                ob_start();
                @eval("include('".Cfg::_get('root_path').'/'.Cfg::get('snippets_dir').'/'.$m[1][$i].".php');");
                $e = ob_get_contents();
                ob_end_clean();
                $s=str_replace("{{".$m[1][$i]."}}",$e,$s);
            }
            preg_match_all("/#([a-zA-Z0-9_\'\[\]\-]+)#/i",$s,$m);
            for($i=0;$i<count($m[0]);$i++){
                //			echo '|_'.$m[1][$i].'_|';
                ob_start();
                @eval('echo $this->'.$m[1][$i].';');
                $e = ob_get_contents();
                ob_end_clean();
                $s=str_replace("#".$m[1][$i]."#",$e,$s);
            }
        }

        return $s;
    }

    /*
     * разбивает строку по брейкерам или возвращет по фрагменту длиной len
     * возвращает @string если не разбилось
     * или array(1=>@string, 2=>@string, text=>@string)
     */
    public function split_text($text,$len=0,$break=true)
    {
        $s=$text;
        if($break){
            $ex=Cfg::get('page_break_code');
            if(is_array($ex)){
                $i=0;
                do{
                    $t=preg_split($ex[$i],$text);
                    $i++;
                }while(count($t)==1 && $i<count($ex));
            }else{
                $t=preg_split($ex,$text);
            }

            if(count($t)>1) {
                $s=$this->array_fill_keys(range(1,count($t),1),$t);
                $s['text']=$text;
                //$s=array('1'=>$t[0],'2'=>$t[1],'text'=>$text);
            }
        }
        if(!is_array($s) && $len){
            $t=explode(".",$text);
            $s1=$s2='';
            foreach($t as $k=>$v)
                if(mb_strlen(Tools::stripTags($s1))<$len) $s1.=$v.($v!='' && Tools::stripTags($v)!=''?'.':'');
                else $s2.=$v.($v!='' && Tools::stripTags($v)!=''?'.':'');

            if($s1==$text) $s=$text; else $s=array('1'=>$s1,'2'=>$s2,'text'=>$text);
        }
        return $s;
    }

    public function parse_para_text($t)
    {
        $ex=explode("\r\n",$t);
        $s='';
        foreach($ex as $k=>$v) $s.="<p>".Tools::unesc($v).'</p>';
        return $s;
    }

    /*
     * разбивает строку по брейкерам
     * возвращает array(фрагменты)
     * frgamentsNum - кол-во возвращаемых фрагментов.
     */
    public static function splitText($text,$frgamentsNum=2)
    {
        $ex=Cfg::get('page_break_code');
        if(is_array($ex)){
            $i=0;
            do{
                $t=preg_split($ex[$i],$text);
                $i++;
            }while(count($t)==1 && $i<count($ex));
        }else{
            $t=preg_split($ex,$text);
        }
        if(count($t)>$frgamentsNum){
            $t[$frgamentsNum-1]=implode('',array_slice($t,$frgamentsNum-1));
            array_splice($t,$frgamentsNum);
        }
        return $t;
    }


    private function array_fill_keys($keyArray, $valueArray)
    {
        if(is_array($keyArray)) {
            foreach($keyArray as $key => $value) {
                $filledArray[$value] = $valueArray[$key];
            }
        }
        return $filledArray;
    }

}