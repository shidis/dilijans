<?
if (!defined('true_enter')) die ("Direct access not allowed!");

class CC_Rating extends CommonStatic
{
    /*
     * полный пересчет рейтинга модели
     * зависомсти: отзывы
     */
    public static function recalcModel($model_id)
    {
        if(empty(App_TFields::$fields['cc_model']['rating'])) return static::putMsg(false, "[CC_Rating.recalcModel]: колонка cc_model.rating не включена");
        $model_id=(int)$model_id;
        $db=new DB();
        $md=$db->getOne("SELECT gr,rating FROM cc_model WHERE model_id=$model_id");
        if($md===0) return static::putMsg(false, "[CC_Rating.recalcModel]: модель $model_id не найдена");

        if($md['gr']==1){
            if(!empty(Cfg::$config['reviews']['1']['enabled']) && !empty(Cfg::$config['rating']['models']['1']['reviews']['states'])){
                // отзывы для шины
                $states=(array)Cfg::$config['rating']['models']['1']['reviews']['states'];
                if(empty($states)) $s=''; else $s=" AND state IN (".implode(',',$states).")";
                $rd=$db->fetchAll($sql="SELECT rating FROM reviews WHERE gr='{$md['gr']}' AND rating>0 AND prodId=$model_id $s", MYSQL_NUM);
                $revAvg=0;
                if(!empty($rd)) {
                    $revSum = 0;
                    foreach ($rd as $vr) {
                        $revSum += $vr[0];
                    }
                    $revAvg = ceil(($revSum / count($rd)) * 10) / 10;
                }
            }
        }

        if(isset($revAvg)){
            $db->update('cc_model', ['rating'=>$revAvg], "model_id=$model_id");
            return $db->unum();
        }
        return 0;
    }
}