<?

class App_Geo extends DB
{
    public  $quiet;
    public $mainCityId=-77;
    public $mainCityName='Москва';

    function __construct($quiet = false)
    {
        $this->quiet=$quiet;
        parent::__construct();
    }

    function resolveWithMapsTable($ip)
    {
        if (!preg_match("/^([0-9]+)\.([0-9]+)\.([0-9]+)\.([0-9]+)$/u", $ip)) {
            return false;
        }

        $r = array(
            'sx_country_code'=>'',
            'sx_region'=>'',
            'sx_city'=>'',
            'cityId' => 0,
            'cityName'=> ''
        );

        /* пробиваем по SxGEO  */
        include_once Cfg::get('root_path') . "/inc/geoIP/SxGeo.php";

        $SxGeo=@new SxGeo(Cfg::get('root_path') . "/inc/geoIP/data/SxGeoCity.dat", SXGEO_FILE);
        $d=false;
        $d=@$SxGeo->getCityFull($ip); // возвращает полную информацию о городе и регионе
        if($d===false) {
            $err="[App_Geo.SxGeo.getCityFull]: ошибка";
            if (!$this->quiet) echo $err; else return Msg::put(false, $err);
        }
        //print_r($d);
        //$city = $SxGeo->get($ip); // выполняет getCountry либо getCity в зависимости от типа базы
        //print_r($city);
        $r['sx_country_code']=@$d['country']['iso'];
        $r['sx_region']=@$d['region']['name_ru'];
        $r['sx_city']=@$d['city']['name_ru'];
        //$r['sxData']=$d;
        // находим город транспортной компании
        if(!empty($r['sx_city'])){
            if(Tools::tolow($r['sx_city']) == Tools::tolow($this->mainCityName)) {
                $r['cityId'] = $this->mainCityId;
                $r['cityName'] = $this->mainCityName;
            }else{
                $city=Tools::esc($r['sx_city']);
                $d = $this->getOne("SELECT id FROM dostavka WHERE city LIKE '{$city}' LIMIT 1");
                if ($d !== 0) {
                    $r['cityId'] = $d[0];
                    $r['cityName'] = $city;
                }
            }
        }
        unset($SxGeo);

        return $r;

    }

}