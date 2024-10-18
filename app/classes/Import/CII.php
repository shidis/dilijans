<?
class App_Import_CII extends DB
{
    use App_Import_Common;

    public $CMT = array( // шаблон сопоставления системного параметра с названием колонки файла

                         1 => array(
                             //'Код TI размера'=>'Системный код','Код бренда'=>'IDBRAND','Код модели'=>'IDMODEL',
                             'Бренд' => 'Бренд', 'Модель' => 'Модель', 'Полный размер' => 'Название', 'Компания' => 'Компания', 'Ширина' => 'Ш', 'Высота' => 'П', 'Диаметр' => 'Д', 'Индекс нагрузки' => 'ИН', 'Индекс скорости' => 'ИС', 'Суффиксы' => 'Усил.', 'Шипы' => 'Шип', 'Сезонность' => 'Сезон', 'Тип ТС' => 'Тип ТС', 'Склад' => 'Склад', 'Опт' => 'Опт', 'Розница 1' => 'Розница', 'Розница 2' => 'Собственная розница'
                         ),

                         2 => array(
                             //'Код TyreIndex'=>'Системный код','Код бренда'=>'IDBRAND','Код модели'=>'IDMODEL',
                             'Бренд' => 'Бренд', 'Модель' => 'Модель', 'Полный размер' => 'Название', 'Компания' => 'Компания', 'Ширина' => 'Ш', 'Диаметр' => 'Д', 'Крепеж' => 'Крепеж', 'PCD' => 'PCD', 'ET' => 'ET', 'DIA' => 'Dia', 'Цвет' => 'Цвет', 'Тип диска' => 'Тип диска', 'Склад' => 'Склад', 'Опт' => 'Опт', 'Розница 1' => 'Розница', 'Розница 2' => 'Собственная розница'
                         )

    );

    public $PCD2ColName = 'PCD(двойной)';


    public $CMI=array(  // параметры для обработки системных параметров
                        1=>array(
                            /*		'Код TyreIndex'=>array('item_field'=>'sys_code','type'=>'integer','list'=>array())
                                    ,'Код бренда'=>array('item_field'=>'IDBRAND','type'=>'integer','list'=>array())
                                    ,'Код модели'=>array('item_field'=>'IDMODEL','type'=>'integer','list'=>array()),*/
                            'Бренд'=>array(				'item_field'=>'brand',		'type'=>'string','list'=>array(),'fieldWidth'=>30)
                            ,'Модель'=>array(			'item_field'=>'model',		'type'=>'string','list'=>array(),'fieldWidth'=>70)
                            ,'Полный размер'=>array(	'item_field'=>'full_name',	'type'=>'string','list'=>array(),'fieldWidth'=>90)
                            ,'Ширина'=>array(			'item_field'=>'P3',			'type'=>'float','list'=>array(),'fieldWidth'=>20)
                            ,'Высота'=>array(			'item_field'=>'P2',			'type'=>'float','list'=>array(),'fieldWidth'=>20)
                            ,'Диаметр'=>array(			'item_field'=>'P1',			'type'=>'float','list'=>array(),'fieldWidth'=>20)
                            ,'Индекс нагрузки'=>array(	'item_field'=>'P7',			'type'=>'string','list'=>array(),'fieldWidth'=>30)
                            ,'Индекс скорости'=>array(	'item_field'=>'P7_1',		'type'=>'string','list'=>array(),'fieldWidth'=>30)
                            ,'Суффиксы'=>array(			'item_field'=>'suffix',		'type'=>'string','list'=>array(),'fieldWidth'=>20)
                            ,'Шипы'=>array(				'item_field'=>'MP3',		'type'=>'id','list'=>array('шип'=>1),'fieldWidth'=>20)
                            ,'Сезонность'=>array(		'item_field'=>'MP1',		'type'=>'id','list'=>array('летняя'=>1,'зимняя'=>2,'всесезонная'=>3),'fieldWidth'=>30)
                            ,'Тип ТС'=>array(			'item_field'=>'MP2',		'type'=>'id','list'=>array('легковой'=>1,'внедорожник'=>2,'микроавтобус'=>3),'fieldWidth'=>30)
                            ,'Трансформации'=>array(	'item_field'=>'transform',	'type'=>'string','list'=>array(),'fieldWidth'=>70)
                            //		,'Поставщик'=>array(		'item_field'=>'sup_id',		'type'=>'id','list'=>array())
                            ,'Компания'=>array(			'item_field'=>'company',	'type'=>'string','list'=>array(),'fieldWidth'=>40)
                            ,'Склад'=>array(			'item_field'=>'sklad',		'type'=>'integer','list'=>array(),'fieldWidth'=>30)
                            ,'Опт'=>array(				'item_field'=>'price1',		'type'=>'price','list'=>array(),'cur_id'=>'1','fieldWidth'=>30)
                            ,'Розница 1'=>array(		'item_field'=>'price2',		'type'=>'price','list'=>array(),'cur_id'=>'1','fieldWidth'=>30)
                            ,'Розница 2'=>array(		'item_field'=>'price3',		'type'=>'price','list'=>array(),'cur_id'=>'1','fieldWidth'=>30)
                        ),
                        2=>array(
                            /*		'Код TyreIndex'=>array('item_field'=>'sys_code','type'=>'integer','list'=>array())
                                    ,'Код бренда'=>array('item_field'=>'IDBRAND','type'=>'integer','list'=>array())
                                    ,'Код модели'=>array('item_field'=>'IDMODEL','type'=>'integer','list'=>array()),*/
                            'Бренд'=>array(				'item_field'=>'brand',		'type'=>'string','list'=>array(),'fieldWidth'=>30)
                            ,'Модель'=>array(			'item_field'=>'model',		'type'=>'string','list'=>array(),'fieldWidth'=>70)
                            ,'Поставщик'=>array(		'item_field'=>'sup_id',		'type'=>'id','list'=>array(),'fieldWidth'=>30)
                            ,'Полный размер'=>array(	'item_field'=>'full_name',	'type'=>'string','list'=>array(),'fieldWidth'=>90)
                            ,'Ширина'=>array(			'item_field'=>'P2',			'type'=>'float','list'=>array(),'fieldWidth'=>20)
                            ,'Диаметр'=>array(			'item_field'=>'P5',			'type'=>'float','list'=>array(),'fieldWidth'=>20)
                            ,'Крепеж'=>array(			'item_field'=>'P4',			'type'=>'float','list'=>array(),'fieldWidth'=>20)
                            ,'PCD'=>array(				'item_field'=>'P6',			'type'=>'float','list'=>array(),'fieldWidth'=>20)
                            //		,'PCD (двойной)'=>array(	'item_field'=>'P4_1',		'type'=>'float','list'=>array(),'fieldWidth'=>20)
                            ,'ET'=>array(				'item_field'=>'P1',			'type'=>'float','list'=>array(),'fieldWidth'=>20)
                            ,'DIA'=>array(				'item_field'=>'P3',			'type'=>'float','list'=>array(),'fieldWidth'=>20)
                            ,'Цвет'=>array(				'item_field'=>'suffix',		'type'=>'string','list'=>array(),'fieldWidth'=>40)
                            ,'Трансформации'=>array(	'item_field'=>'transform',	'type'=>'string','list'=>array(),'fieldWidth'=>70)
                            ,'Тип диска'=>array(		'item_field'=>'MP1',		'type'=>'id','list'=>array('кованый'=>1,'кованный'=>1,'литой'=>2,'стальной'=>3),'fieldWidth'=>30)
                            ,'Компания'=>array(			'item_field'=>'company',	'type'=>'string','list'=>array(),'fieldWidth'=>40)
                            ,'Склад'=>array(			'item_field'=>'sklad',		'type'=>'integer','list'=>array(),'fieldWidth'=>30)
                            ,'Опт'=>array(				'item_field'=>'price1',		'type'=>'price','list'=>array(),'cur_id'=>'1','fieldWidth'=>30)
                            ,'Розница 1'=>array(		'item_field'=>'price2',		'type'=>'price','list'=>array(),'cur_id'=>'1','fieldWidth'=>30)
                            ,'Розница 2'=>array(		'item_field'=>'price3',		'type'=>'price','list'=>array(),'cur_id'=>'1','fieldWidth'=>30)
                        )
    );

    var $status = array( // применяется к cstatus/mstatus/bstatus
                         // игнор ставится для размеров с ignoreUpdate
                         0 => 'не обработан', 1 => 'Обновлен', 2 => 'Добавлен', 3 => 'игнор', 21 => 'Обновлю', 22 => 'Добавлю', 23 => 'игнор'
    );

    var $fres = true, $fres_msg = '',
        $sheets, $fname, $ftype, $deletedFiles, $param = array(), // param в таблице cii_file
        $colModel = array(), // в таблице cii_file
        $CM = array(), // colModel подготовленная  для вывода таблицы
        $gr, $name, // имя файла в таблице cii_file
        $dt_added, // дата файла в таблице cii_file
        $file_id, $r;


    function view($file_id)
    {
        $d = $this->getOne("SELECT gr,CM,param,status FROM cii_file WHERE file_id='$file_id'");
        if ($d === 0) {
            return $this->putMsg(false, 'Не найден файл id=' . $file_id);
        }
        if (@$d['gr'] == 0) {
            return $this->putMsg(false, 'Не присвоена группа для файла id=' . $file_id);
        }
        $param = Tools::DB_unserialize($d['param']);
        $this->CM = Tools::DB_unserialize($d['CM']);
        $r = (object)array();
        $r->status = $d['status'];

        if ($d['gr'] == 2) {
            $cc = new CC_Ctrl();
            $cc->load_sup(0);
            $this->CMI[$d['gr']]['Поставщик']['list'] = array_flip($cc->sup_arr);
        }

        $s = array();
        foreach ($this->CMI[$d['gr']] as $v) {
            if (isset($_GET[$v['item_field']])) {
                switch ($v['type']) {
                    case 'id':
                        /*					if(count($v['list'])) {
                                            $a=array_flip($v['list']);
                                            $s[]="{$v['item_field']}='".@$a[intval($_GET[$v['item_field']])]."'";
                                            break;
                                        }*/
                    case 'integer':
                    $s[] = "{$v['item_field']} = '" . intval($_GET[$v['item_field']]) . "'";
                    break;
                    case 'float':
                        $s[] = "{$v['item_field']} = '" . floatval($_GET[$v['item_field']]) . "'";
                        break;
                    case 'price':
                        $s[] = "{$v['item_field']} = '" . floatval($_GET[$v['item_field']]) . "'";
                        break;
                    default:
                        $s[] = "{$v['item_field']} LIKE '%" . Tools::esc($_GET[$v['item_field']]) . "%'";
                        break;
                }
            }
        }
        if (isset($_GET['item_id'])) $s[] = "item_id='" . intval($_GET['item_id']) . "'";
        if (isset($_GET['cstatus'])) $s[] = "(cstatus='" . intval($_GET['cstatus']) . "' OR cstatus='" . intval($_GET['cstatus'] + 20) . "')";
        if (isset($_GET['mstatus'])) $s[] = "(mstatus='" . intval($_GET['mstatus']) . "' OR mstatus='" . intval($_GET['mstatus'] + 20) . "')";
        if (isset($_GET['bstatus'])) $s[] = "(bstatus='" . intval($_GET['bstatus']) . "' OR bstatus='" . intval($_GET['bstatus'] + 20) . "')";
        $s = implode(' AND ', $s);
        if ($s != '') $s = ' AND ' . $s;

        $page = (int)@$_REQUEST['page']; // get the requested page
        $limit = (int)@$_REQUEST['rows']; // get how many rows we want to have into the grid
        $sidx = Tools::esc(@$_REQUEST['sidx']); // get index row - i.e. user click to sort
        $sord = Tools::esc(@$_REQUEST['sord']); // get the direction
        if (!$sidx) $sidx = 1;
        $dd = $this->getOne("SELECT count(item_id) FROM cii_item WHERE (file_id='$file_id') $s");
        $count = $dd[0];
        $total_pages = ceil($count / $limit);
        if ($page > $total_pages) $page = $total_pages;
        $start = $limit * $page - $limit;
        if ($start < 0) $start = 0;
        if ($sidx == '') $sidx = 'item_id';
        $r->page = $page;
        $r->total = $total_pages;
        $r->records = $count;

        $this->query("SELECT * FROM cii_item WHERE (file_id='$file_id') $s ORDER BY $sidx $sord LIMIT $start,$limit", MYSQLI_ASSOC);
        $i = 0;
        if ($this->qnum()) while ($this->next() !== false) {
            $r->rows[$i]['id'] = $this->qrow['item_id'];
            //		$r->rows[$i]['cell'][]='';
            // $r->rows[$i]['cell'][]=$this->qrow['item_id'];
            if (isset($this->status[$this->qrow['cstatus']])) $r->rows[$i]['cell'][] = $this->status[$this->qrow['cstatus']]; else $r->rows[$i]['cell'][] = '???';
            if (isset($this->status[$this->qrow['mstatus']])) $r->rows[$i]['cell'][] = $this->status[$this->qrow['mstatus']]; else $r->rows[$i]['cell'][] = '???';
            if (isset($this->status[$this->qrow['bstatus']])) $r->rows[$i]['cell'][] = $this->status[$this->qrow['bstatus']]; else $r->rows[$i]['cell'][] = '???';
            foreach ($this->CMI[$d['gr']] as $v) {
                if (count($v['list'])) {
                    $a = array_flip($v['list']);
                    $r->rows[$i]['cell'][] = @$a[$this->qrow[$v['item_field']]];
                } else {
                    if (in_array($v['item_field'], array(
                        'P1',
                        'P2',
                        'P3',
                        'P4',
                        'P5',
                        'P6'
                    ))) $this->qrow[$v['item_field']] = $this->qrow[$v['item_field']] * 1;
                    $r->rows[$i]['cell'][] = $this->qrow[$v['item_field']];
                }
            }
            $i++;
        }

        return $r;
    }


function __construct()
{
    parent::__construct();
    include_once Cfg::_get('root_path') . '/inc/excel/reader.php';
}


function readCSV($fname, $sheetNo = 1)
{
    //	error_reporting(0);
    $this->fname = $fname;
    if (!is_file($fname)) {
        return $this->putMsg(false, "Файл $fname не найден");
    }
    $f = fopen($fname, 'r');
    $row = $cols = 0;
    $this->sheets = array();
    while (($data = fgetcsv($f, 0, ";")) !== FALSE) {
        $row++;
        $cols = $cols < count($data) ? count($data) : $cols;
        foreach ($data as $k => &$v) $v = iconv('cp1251', 'UTF-8//IGNORE', $v);
        $this->sheets[$sheetNo - 1]['cells'][] = $data;
    }
    fclose($f);
    if (!$row || !$cols) {
        return $this->putMsg(false, 'Нет строк в файле');
    }
    $this->sheets[$sheetNo - 1]['numRows'] = $row;
    $this->sheets[$sheetNo - 1]['numCols'] = $cols;

    return $this->putMsg(true);
}

    function readExcel($fname, $sheetNo = 'all')
    {
        $this->fname = $fname;
        if (!is_file($fname)) {
            return $this->putMsg(false, "Файл $fname не найден");
        }
        try {
            $excel = new Spreadsheet_Excel_Reader();
            $excel->setOutputEncoding("UTF-8");
            if (($res = $excel->read($fname) !== true)) {
                $this->putMsg(false, "Ошибка во время обработки EXCEL файла");
                unset($excel);

                return false;
            }
            /*	for ($x=0; $x<sizeof ($excel->sheets); $x++) {
                echo "Number of rows in sheet " . ($x+1) . ": " . $excel->sheets[$x]["numRows"] . "\n";
                echo "Number of columns in sheet " . ($x+1) . ": " . $excel->sheets[$x]["numCols"] . "\n";
                $this->echoSheet($excel->sheets[$x]);
            }*/
            if (!count($excel->sheets)) {
                $this->putMsg(false, "Нет доступных листов после обработки EXCEL файла");
                unset($excel);

                return false;
            }
            if ($sheetNo == 'all') $this->sheets = $excel->sheets; else {
                if (isset($excel->sheets[$sheetNo - 1])) $this->sheets[$sheetNo - 1] = $excel->sheets[$sheetNo - 1]; else {
                    $this->putMsg(false, "Нет доступных данных на листе " . $sheetNo);
                    unset($excel);

                    return false;
                }
            }
            unset($excel);
        } catch (Exception $e) {
            return $this->putMsg(false, $e->getMessage());
        }

        return $this->putMsg(true);
    }

    function parse($fname)
    {
        $config = $this->getConfig();
        $pi = pathinfo($this->fname = $fname);
        $this->param = array();
        $this->param['sheetNo'] = 1; // нумерация с 1 при отправке в парвер и для записи в таблицу param файла. Но Парсер нумерует с нуля
        $this->ftype = mb_strtolower(@$pi['extension']);
        if ($this->ftype == 'xls') $this->readExcel($fname, $this->param['sheetNo']); elseif ($this->ftype == 'csv') $this->readCSV($fname, $this->param['sheetNo']);
        else $this->putMsg(false, 'Неверный формат файла ' . $this->ftype);
        @unlink($fname);
        if (!$this->fres) {
            return false;
        }
        $this->param['extension'] = $this->ftype;
        $this->param['fname'] = $pi['basename'];
        $this->param['numRows'] = $this->sheets[$this->param['sheetNo'] - 1]['numRows'];
        $this->param['numCols'] = $this->sheets[$this->param['sheetNo'] - 1]['numCols'];
        $this->param['status'] = 0;
        $this->gr = 0;
        $this->name = date("d/m/Y H:i") . ', ' . $pi['basename'];
        $this->dt_added = date("Y-m-d H:i:s");
        $this->colModel = $this->CMT; // у нас одна пока один шаблон модели колонок . ColModel!=CM

        $line = 0; // номер строки, пустые не включаются в счет
        if (count(@$this->sheets[$this->param['sheetNo'] - 1]['cells'])) {

            $insBuf = array();

            foreach (@$this->sheets[$this->param['sheetNo'] - 1]['cells'] as $cell) {
                $t = false;
                for ($i = 1; $i <= $this->param['numCols']; $i++) if (trim(@$cell[$i]) != '') {
                    $t = true;
                    break;
                } // пропускаем пустые колонки

                if ($t) {
                    $line++;
                    if ($line == 1) {
                        // первая непустая строка - хедер
                        // распознаем модель по первой строчке в файле
                        for ($gr = 1; $gr <= 2; $gr++) {
                            $this->CM = array(); // CM - модель колонок которая будет записана в файл. Состоит из пар: название системного параметра => номер столбца в файле
                            $c = array();
                            foreach ($this->colModel[$gr] as $k => &$v) $c[$k] = mb_strtolower($v);

                            foreach ($cell as $k => &$v) {
                                if (($cn = Tools::mb_array_search(mb_strtolower($v), $c)) !== false) $this->CM[$cn] = $k;
                            }
                            if (count($this->CM) == count($this->colModel[$gr])) {
                                $this->gr = $gr;
                                break;
                            }
                        }
                        if ($this->gr == 0) return $this->putMsg(false, 'Структура файла не распознана. Первая строка файла содержит не все необходимые параметры: <br>ДЛЯ ШИН => ' . implode(',', array_values($this->colModel[1])) . '<br>ДЛЯ ДИСКОВ =>' . implode(',', array_values($this->colModel[2])), true);

                        if ($this->gr == 2) {
                            // ищем PCD2ColName
                            $pcd2Col = -1;
                            foreach ($cell as $k => &$v) {
                                if ($v == $this->PCD2ColName) {
                                    $pcd2Col = $k;
                                    break 1;
                                }
                            }
                            if ($pcd2Col == -1) return $this->putMsg(false, 'Структура файла не распознана. Не найдена колонка ' . $this->PCD2ColName, true);
                        }

                        $this->query("INSERT INTO cii_file (name,gr,dt_added,param,CM) VALUES('{$this->name}','{$this->gr}','{$this->dt_added}','" . Tools::DB_serialize($this->param) . "','" . Tools::DB_serialize($this->CM) . "')");

                        $this->file_id = $this->lastId();

                        if (@$config['maxFileList'] > 0) $this->deletedFiles = $this->clearFileList($config['maxFileList']); else $this->deletedFiles = array();

                        $ff = array();
                        foreach ($this->CM as $k => $v) {
                            $ff[] = $this->CMI[$this->gr][$k]['item_field'];
                        }
                        $ff = implode(',', $ff);

                    } else {
                        // распознаем данные из файла и раскидываем их по столбцам в БД. Первая строка файла не записывается.
                        $vv = array();
                        foreach ($this->CM as $k => $v) {
                            switch ($this->CMI[$this->gr][$k]['type']) {
                                case 'id':
                                    if (count($this->CMI[$this->gr][$k]['list'])) {
//                                        $vv[$this->CMI[$this->gr][$k]['item_field']] = "'" . @$this->CMI[$this->gr][$k]['list'][@$cell[$v]] . "'";
                                        $vv[$this->CMI[$this->gr][$k]['item_field']] = "'" . intval(@$this->CMI[$this->gr][$k]['list'][@$cell[$v]]). "'" ;
                                        break;
                                    }
                                case 'integer':
                                    $vv[$this->CMI[$this->gr][$k]['item_field']] = "'" . intval(@$cell[$v]) . "'";
                                    break;
                                case 'float':
                                case 'price':
                                $vv[$this->CMI[$this->gr][$k]['item_field']] = "'" . Tools::toFloat(@$cell[$v]) . "'";
                                break;
                                case 'string':
                                default:
                                $vv[$this->CMI[$this->gr][$k]['item_field']] = "'" . Tools::esc(@$cell[$v]) . "'";
                                break;
                            }
                        }
                        $vv1 = implode(',', $vv);
                        if (count($insBuf) > 10000) {
                            $this->query("INSERT INTO cii_item (file_id,{$ff}) VALUES " . implode(',', $insBuf));
                            $insBuf = array();
                        }

                        $insBuf[] = "('{$this->file_id}',{$vv1})";

                        if ($this->gr == 2) {
                            // PCD двойной
                            $pcd2 = Tools::toFloat($cell[$pcd2Col]);
                            if (!empty($pcd2)) {
                                $vv[$this->CMI[2]['PCD']['item_field']] = "'" . $pcd2 . "'";
                                $vv1 = implode(',', $vv);
                                $insBuf[] = "('{$this->file_id}',{$vv1})";
                            }

                        }
                    }
                }
            }
            if (!empty($insBuf)) $this->query("INSERT INTO cii_item (file_id,{$ff}) VALUES " . implode(',', $insBuf));
        }
        if ($line == 0) return $this->putMsg(false, 'Файл пустой', true);

        return $this->fres;
    }

    function clearFileList($maxFiles)
    {
        $mf = (int)$maxFiles;
        $r = array();
        if (empty($mf)) $mf = 10;
        $d = $this->fetchAll("SELECT file_id FROM cii_file WHERE NOT LD ORDER BY dt_added DESC");
        for ($i = $mf; $i < count($d); $i++) {
            $r[] = $d[$i]['file_id'];
            $this->del('cii_item', 'file_id', $d[$i]['file_id']);
            $this->ld('cii_file', "file_id", $d[$i]['file_id']);
        }

        return $r;
    }

    function echoSheet($sheet)
    {
        ?>
        <table><?
        $x = 1;
        while ($x <= $sheet['numRows']) {
            echo "<tr><td>$x</td>";
            $y = 1;
            while ($y <= $sheet['numCols']) {
                $cell = isset($sheet['cells'][$x][$y]) ? $sheet['cells'][$x][$y] : '';
                echo "<td>$cell</td>";
                $y++;
            }
            echo "</tr>";
            $x++;
        }
        ?></table><?
    }

}