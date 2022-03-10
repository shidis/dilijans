<?
if (!defined('true_enter')) die ("Direct access not allowed!");

class App_SUrl {
	
	private static $cc;
	
	static function tTipo($cat_id=0, $qrow=array())
	{
        if(!empty($qrow)){
            $qr=$qrow;
        }else{
            if(empty(self::$cc)) self::$cc=new CC_Ctrl();

            self::$cc->que('cat_by_id',intval($cat_id));
            if(self::$cc->qnum()==0) return false;
            self::$cc->next();
            $qr=self::$cc->qrow;
        }


		return '/'.App_Route::$actions['tTipo']['url']."/".(isset($qr['cat_sname'])?$qr['cat_sname']:$qr['sname']).".html";
	}
	
		
	static function dTipo($cat_id=0, $qrow=array())
	{
        if(!empty($qrow)){
            $qr=$qrow;
        }else{
            if(empty(self::$cc)) self::$cc=new CC_Ctrl();
		
            self::$cc->que('cat_by_id',intval($cat_id));
            if(self::$cc->qnum()==0) return false;
            self::$cc->next();
            $qr=self::$cc->qrow;
        }


        return '/'.App_Route::$actions['dTipo']['url']."/".(isset($qr['cat_sname'])?$qr['cat_sname']:$qr['sname']).".html";
	}

    static function dModel($model_id=0, $qrow=array())
    {
        if(!empty($qrow)){
            $qr=$qrow;
        }else{
            if(empty(self::$cc)) self::$cc=new CC_Ctrl();

            self::$cc->que('model_by_id',intval($model_id));
            if(self::$cc->qnum()==0) return false;
            self::$cc->next();
            $qr=self::$cc->qrow;
        }

        $dRoute='/'.App_Route::_getUrl('dModel').'/';


        return $dRoute.(isset($qr['model_sname'])?$qr['model_sname']:$qr['sname']).'.html';
    }

    static function tModel($model_id=0, $qrow=array())
    {
        if(!empty($qrow)){
            $qr=$qrow;
        }else{
            if(empty(self::$cc)) self::$cc=new CC_Ctrl();

            self::$cc->que('model_by_id',intval($model_id));
            if(self::$cc->qnum()==0) return false;
            self::$cc->next();
            $qr=self::$cc->qrow;
        }

        $tRoute='/'.App_Route::_getUrl('tModel').'/';

        return $tRoute.(isset($qr['model_sname'])?$qr['model_sname']:$qr['sname']).'.html';
    }


}
		
		
