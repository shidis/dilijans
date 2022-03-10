<?

final class Data
{
    /*
     * поле system_data.name  должно быть уникальным!
     */
    public static $current = array(); // все поля system_data из последнего обращения через get();
    private static $dataArr = array();
    private static $aulo_load_id = -1;  // system_data.aulo_load_id  || ==-1 - не будет автозагрузки

    /* Группы (system_data.group_id) :
        0 - не оттобжаемые данные в таблице настроек
        1 - заголовки страниц  (name начинается с title_)
        2 - емейлы (name начинается с mail_)
        3 - настройка скидок
        4 - параметры отображения каталога на сайте
        5 - параметры конвертации изображений в каталоге шин и дисков
        6 - настройка системы заказов и юзеров
    */


    public static function setAutoLoadId($aulo_load_id)
    {
        Data::$aulo_load_id = $aulo_load_id;
    }

    /*
     * TODO недодуманный и не протестирвоанный механизм загрузки автозагрузки
     */
    private static function loadNames($aulo_load_id)
    {  // номер группы должно быть >=0
        Data::$dataArr[$aulo_load_id] = array();
        $db = new DB();
        $r = $db->fetchAll("SELECT * FROM system_data WHERE auto_load_id='$aulo_load_id'");
        if ($db->qnum()) foreach ($r as $v) {
            Data::$dataArr[$aulo_load_id][$v['name']] = [
                'data_id' => $v['data_id'],
                'title' => Tools::unesc($v['title']),
                'V' => Tools::unesc($v['V']),
                'comment' => Tools::unesc($v['comment'])
            ];
        }
        unset($db);
    }

    public static function get($name)
    {
        if (Data::$aulo_load_id >= 0 && !isset(Data::$dataArr[Data::$aulo_load_id])) Data::loadNames(Data::$aulo_load_id);
        if (!isset(Data::$dataArr[Data::$aulo_load_id][$name]) && !isset(Data::$dataArr['all'][$name])) {
            $db = new DB;
            $name1=Tools::esc($name);
            $d = $db->getOne("SELECT * FROM system_data WHERE name = '$name1'");
            unset($db);
            if ($d !== 0) {
                Data::$current = [
                    'data_id' => $d['data_id'],
                    'title' => Tools::unesc($d['title']),
                    'name' => Tools::unesc($d['name']),
                    'V' => Tools::unesc($d['V']),
                    'comment' => Tools::unesc($d['comment'])
                ];
                if (Data::$aulo_load_id >= 0) {
                    Data::$dataArr[Data::$aulo_load_id][$name] = Data::$current;
                } else {
                    Data::$dataArr['all'][$name] = Data::$current;
                }
                return Data::$current['V'];
            } else {
                Data::$current = array();
                return '';
            }
        } elseif (Data::$aulo_load_id >= 0) {
            Data::$current=Data::$dataArr[Data::$aulo_load_id][$name];
            return Data::$current['V'];
        } else {
            Data::$current=Data::$dataArr['all'][$name];
            return Data::$current['V'];
        }
    }

    public static function set($name, $v, $group = -1)
    {
        $group = intval($group);
        $v1 = Tools::esc($v);
        $name1 = Tools::esc($name);
        $db = new DB();
        $db->query("SELECT * FROM system_data WHERE name = '$name1'");
        if ($db->qnum()) {
            $db->query("UPDATE system_data SET V='$v1'" . ($group >= 0 ? ", group_id='$group'" : '') . "  WHERE name='$name1'");
            if (Data::$aulo_load_id >= 0 && isset(Data::$dataArr[Data::$aulo_load_id][$name])) Data::$dataArr[Data::$aulo_load_id][$name]['V'] = $v;
            if (isset(Data::$dataArr['all'][$name])) Data::$dataArr['all'][$name]['V'] = $v;
        } else {
            if ($group == -1) $group = 0;
            $sql = "INSERT INTO system_data (name,V,group_id) VALUES('$name1','$v1','$group')";
            $db->query($sql);
        }
        unset($db);
    }

}
	