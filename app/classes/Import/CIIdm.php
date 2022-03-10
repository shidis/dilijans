<?
if (!defined('true_enter')) die ("Direct access not allowed!");

// Ver 4.0 модуля  сдемоном для выполнения задачи. Без привязки к кодам ТИ, сохранение полного списка поставщиков
// модуль импорта App_CC_CII (CAT_IMPORT_MODE==2)


class CurrentRow
{
    public
        $cat_id=0,
        $model_id=0,
        $brand_id=0,
        $replica=0,
        $suplr_id=0,
        $suplrName='',
        $sup_id=0,
        $bname='',
        $mname='',
        $suffix='',
        $iSuffixes,
        $P6,
        $IN, $IS,
        $transform=[],
        $tSuffixesBrandKey,
        $price1,$price,$price3,
        $code = '',
        $cstatus=0,
        $mstatus=0,
        $bstatus=0;

    public function clear()
    {
        $this->transform=[];
        $this->suplrName=$this->bname=$this->mname=$this->suffix=$this->P6=$this->IN=$this->IS=$this->iSuffixes=$this->code='';
        $this->cat_id=$this->model_id=$this->brand_id=$this->replica=$this->suplr_id=
        $this->sup_id=$this->cstatus=$this->mstatus=$this->bstatus=$this->tSuffixesBrandKey=
        $this->price1=$this->price2=$this->price3=0;
    }
}

class App_Import_CIIdm extends DB
{
    use App_Import_Common;

    protected
        $gr,
        $param,
        $file_id,
        $opt, // == getConfig()
        $exSuffixes, // словарь суффиксов шин: массив пар brandName=>dictName
        $rfSuffixes, // простой массив ранфлет суффиксов
        $suplrsFromFile, // все поставщики о которых были упоминания в файле
        $brandsFromFile, // все бренды о которых были упоминания в файле
        $ignoreDiskSuffixes,
        $emptyDiskSuffix,
        $YSTSuplrId,
        $minExtras,
        $sMatrix,
        $models, $brands, $suplrs, // кеш массивы
        $sups,
        $insertedBrandsCounter, $insertedModelsCounter, $insertedTiposCounter, // счетчик доавленных
        $insertedSCCounter,
        $updatedSCCounter,
        $deletedSCCounter,
        $updatedCatsCounter,
        $updatedModelsCounter,
        $CIIR,
        $cr,
        $cachedBrandName,
   //     $replicaBrands,
        $cc;


    /* cii_file.status ={0 - не импортирован 1- импортирован}*/

    function recognize($task)
    {
        if(1) {
            $this->sql_connect();

            $daemon = $this->dm_updateInfo();
            $this->dm_modTask(['state' => 'exec', 'ts_run' => $tsRun = time(), 'pid' => $daemon['pid']]);

            $this->opt = $task['opt']; // тут getConfig + данные формы парсинга
            $this->file_id = @(int)$task['opt']['file_id'];
            $this->dm_taskLog("[parser]: Запуск процедуры парсинга file_id={$this->file_id}");

            $file = $this->getOne("SELECT SQL_NO_CACHE gr,CM,param,status FROM cii_file WHERE file_id='{$this->file_id}'");

            if ($file === 0) return $this->dm_taskError('Не найден файл id=' . $this->file_id);
            if (@$file['gr'] != 1 && @$file['gr'] != 2) return $this->dm_taskError('Не правильная группа для файла id=' . $this->file_id);
            if (@$file['status'] == 1 && false) return $this->dm_taskError('Файл уже импортирован id=' . $this->file_id);

            $this->param = Tools::DB_unserialize($file['param']);
            if (!isset($file['CM'])) return $this->dm_taskError('Неизвестная структура файла.'); else $this->CM = $file['CM'];

            $this->gr = $file['gr'];
            $this->suplrsFromFile = [];
            $this->brandsFromFile = [];
            $this->insertedBrandsCounter = 0;
            $this->insertedModelsCounter = 0;
            $this->insertedTiposCounter = 0;
            $this->cachedBrandName='';
            $this->insertedSCCounter = 0;
            $this->updatedSCCounter = 0;
            $this->deletedSCCounter = 0;
            $this->updatedCatsCounter = 0;
            $this->updatedModelsCounter = 0;
            $this->brands = $this->models = $this->tipos = [];

            $this->cr=new CurrentRow(); // для работы со строкой. данные текущей строки

            if (empty($this->opt['mode'])) return $this->dm_taskError('Не задан режим работы парсера!');
            if ($this->opt['mode'] == 1) $this->dm_taskLog('[parser]: Тестовый импорт (без обновления товарной базы сайта)'); elseif ($this->opt['mode'] ==2) $this->dm_taskLog('[parser]: Частичный импорт');
            elseif ($this->opt['mode'] == 3) $this->dm_taskLog('[parser]: Полный импорт');

            unset($task);

            if(empty($this->opt['brands'])) $this->opt['brands']=[];
            if(!isset($this->opt['delSuplrs'])) $this->opt['delSuplrs']=[];
            if(!isset($this->opt['suplrsMinPrice'])) $this->opt['suplrsMinPrice']=0; else $this->opt['suplrsMinPrice']=abs((int)$this->opt['suplrsMinPrice']);

            // загружаем словари
            // шины
            if ($this->gr == 1) {

                //загружаем словарь суффиксов шин
                $d = $this->cc->fetchAll("SELECT SQL_NO_CACHE cc_dict.name AS dname,  IFNULL( cc_brand.name, 0 ) AS bname FROM cc_dict LEFT JOIN cc_brand USING ( brand_id ) WHERE cc_dict.gr=1 ORDER BY LENGTH( cc_dict.name ) DESC", MYSQL_ASSOC);
                $this->exSuffixes = array();
                if (!empty($d)) {
                    foreach ($d as $v) {
                        $this->exSuffixes[trim(Tools::unesc($v['bname']))][] = trim(Tools::cutDoubleSpaces(Tools::unesc($v['dname'])));
                    }
                }

                // загружаем аналоги ранфлет
                $this->rfSuffixes = array();
                $d = Data::get('cc_runflat_suffix');
                if (!empty($d)) {
                    $d = explode(',', $d);
                    foreach ($d as $v) {
                        $v = trim(Tools::cutDoubleSpaces($v));
                        if (!empty($v)) $this->rfSuffixes[] = $v;
                    }
                }

                // диски
            }
            else {

                if (empty($this->opt['diaMerge'])) $this->opt['diaMerge'] = [];
                if (empty($this->opt['svMerge'])) $this->opt['svMerge'] = [];

                /*
                 * Яршинторг
                 * YST = {
                 *      suplrName: string
                 *      bExtras: [
                 *       {brand_id=>extra%}
                 *      ]
                 * }
                 */
                if (empty($this->opt['YST'])) $this->opt['YST'] = [];
                $suplr = Tools::esc(@$this->opt['YST']['suplrName']);
                $d = $this->cc->getOne("SELECT SQL_NO_CACHE suplr_id FROM cc_suplr WHERE name != '' AND name LIKE '$suplr'");
                if ($d !== 0) {
                    $this->YSTSuplrId = $d[0];
                    $this->cc->load_extra();
                    $this->minExtras = $this->cc->min_extra_arr;
                }

                // загружаем матрицу цветов дисков
                $this->cc->loadSMatrix(2);
                $this->sMatrix = $this->cc->sMatrix;
                unset($this->cc->sMatrix);

                $this->emptyDiskSuffix = trim($this->opt['emptyDiskSuffix']);
                $s = trim($this->opt['ignoreDiskSuffixes']);
                $s = explode(',', $s);
                $this->ignoreDiskSuffixes = array();
                foreach ($s as $v) {
                    $v = trim($v);
                    if ($v != '') $this->ignoreDiskSuffixes[] = $v;
                }

                // бренды реплики
                //$d=$this->cc->fetchAll("SELECT cc_sup.name, cc_sup.sup_id FROM cc_sup JOIN cc_brand USING (sup_id) WHERE cc_brand.gr = '2' AND cc_brand.LD=0", MYSQL_ASSOC);
                $d=$this->cc->fetchAll("SELECT cc_sup.name, cc_sup.sup_id FROM cc_sup", MYSQL_ASSOC);
                $this->sups = [];
                foreach($d as $v) $this->sups[$v['sup_id']] = trim(Tools::cutDoubleSpaces(Tools::unesc($v['name'])));
/*
                $this->replicaBrands=[];
                if(trim($this->opt['replicaBrand'])!='') {
                    $ex = preg_split("/[;,]/u", $this->opt['replicaBrand']);
                    foreach ($ex as $rb) {
                        $rb = trim(Tools::cutDoubleSpaces($rb));
                        if ($rb != '') $this->replicaBrands[] = $rb;
                    }
                }
*/
            }

            $t=microtime(true);
            $this->query("UPDATE cii_item SET cstatus=0, mstatus=0, bstatus=0 WHERE file_id='{$this->file_id}'");
            $t=$this->timerEnd($t);
            $this->dm_taskLog("[parser]: cii_item обнулена ($t sec)");
        }

        // кешируем список поставщиков из cc_suplrs
        $this->loadSuplrsCache();
        // кешируем бренды
        $this->loadBrandsCache();

        $this->dm_taskLog('[parser]: Справочники загружены');
        $this->dm_updateInfo();

        $this->S8_prepareReplica();

        $this->dm_modTask([
            'pg_label' => 'Основная последовательность'
        ]);

        $qri=0; // счетчик обработанных строк в исходном файле

        do {

            $t=microtime(true);

            $cfg=Tools::DB_unserialize(Data::get('cii_config',true));
            $limit = (int)$cfg['DM_rowsLimitByIter'];
            if(empty($limit)) $this->opt['DM_rowsLimitByIter']=$limit=2000;
            unset($cfg);

            if ($this->gr == 1) // USE INDEX (file_id_cstatus)
                $ciirs=$this->fetchAll("SELECT item_id, bstatus, mstatus, cstatus, brand, model, full_name, company, P1+'0' AS P1, P2+'0' AS P2, P3+'0' AS P3, P7, P7_1, MP1, MP2, MP3, suffix, sklad, price1,price2,price3, cat_id, transform, suplr_id, brand_id, model_id  FROM cii_item WHERE file_id='{$this->file_id}' AND cstatus=0 ORDER BY brand, model, P3, P2, P1, P7, P7_1, suffix  LIMIT 0, $limit", MYSQL_ASSOC);
            else
                // USE INDEX (file_id_cstatus)
                $ciirs=$this->fetchAll("SELECT item_id, bstatus, mstatus, cstatus, brand, model, full_name, company, P1+'0' AS P1, P2+'0' AS P2, P3+'0' AS P3, P4+'0' AS P4, P5+'0' AS P5, P6+'0' AS P6, P7, P7_1, MP1, suffix, sklad, price1,price2,price3, cat_id, brand_id, model_id, replica, transform, suplr_id, sup_id FROM cii_item WHERE file_id='{$this->file_id}' AND cstatus=0 ORDER BY brand, model, P5, P2, P4, P6, P1, P3, suffix  LIMIT 0, $limit", MYSQL_ASSOC);

            $this->sqlFree();

            $t=$this->timerEnd($t);
            $this->dm_taskLog('[parser]: SELECT cii_item LIMIT '.$limit." ($t sec)");

            foreach($ciirs as $this->CIIR)
            {
                $this->cr->clear();

                // получаем $this->cr->suplr_id, добавляем в базу, если нет
                $this->S16_getSuplrId();

                if(!$this->S12_checkIgnore())
                {
                    $this->CIIR['full_name']=Tools::unesc($this->CIIR['full_name']);
                    $this->CIIR['sklad'] = $this->CIIR['sklad']*1;
                    $this->cr->price1 = $this->CIIR['price1']*1;
                    $this->cr->price2 = $this->CIIR['price2']*1;
                    $this->cr->price3 = $this->CIIR['price3']*1;

                    // получаем $this->cr->bname,mname
                    $this->S13_prepareBrandAndModel(Tools::unesc($this->CIIR['brand']), Tools::unesc($this->CIIR['model']));
                    // получаем $this->cr->brand_id / добавляем новый, получаем bstatus
                    $this->S18_getBrandId();

                    if (Tools::mb_strcasecmp($this->cachedBrandName, $this->cr->bname)!==0) {
                        // если бренд поменялся, то сбрасываем кеш в БД, обновляем склад и остатки
                        $this->S24_flushBrandCache();
                        // получаем кеш моделей, ращмеров, остатков для текущего бренда
                        $this->S28_loadModelsCache();
                    }

                    // получаем $this->cr->cat_id, model_id, mstatus, cstatus, вызов методов добавления/обновления кеш-моделей-размеров и остатков
                    $this->S30_parseModelAndTipo();
                    // обновляем склад
                    $this->S35_updatePriceCache();
                    // обновление cII-item
                    $this->S40_updateItemRow();

                }


                $qri++;
                //срабатывает на каждой 10той строке
                if(($qri / 50) == ceil($qri/50) || !$this->qnum){
                    $this->dm_updateInfo();
                    // посылаем данные в МС для отображения в статусном окне
                    $this->dm_modTask([
                        'qri'=>$qri,
                        'pg_index'=>ceil($qri*100/$this->param['numRows']),
                        'pg_label'=> !empty($this->cr->bname) ? "Обработка бренда {$this->cr->bname}" : ''
                    ]);
                    if(!$this->dm_cmd()) {
                        // если полукчена команда task_break - остановка задачи
                        $this->dm_modTask(['state' => 'interrupted', 'ts_finished' => time()]);
                        $this->dm_taskLog('[parser]: задача прервана оператором.');
                        return true;
                    }
                }
            }


        } while(!empty($ciirs));

        $this->S24_flushBrandCache();

        $this->dm_taskLog("[parser]: Все строки ($qri) файла обработаны.");
        $this->dm_updateInfo();

        if ($this->opt['mode']!=1) {

            $bkeys=implode(',', array_keys($this->brandsFromFile));
            $supkeys=implode(',', array_keys($this->suplrsFromFile));

            // полный импорт
            if($this->opt['mode']==3){
                $this->dm_modTask([
                    'pg_label'=>'Финальная последовательность...'
                ]);

                // удалеям всех поставщиков в незатронутых брендах, пишем нулевой склад
                if(!empty($bkeys)) {
                    //  AND cc_cat.ignoreUpdate=0
                    $t = microtime(true);
                    $this->query("SELECT cc_cat.cat_id FROM cc_cat INNER JOIN cc_model ON cc_cat.model_id = cc_model.model_id WHERE NOT cc_cat.LD AND NOT cc_model.LD AND cc_cat.gr='{$this->gr}' AND cc_model.brand_id NOT IN($bkeys)");
                    if ($this->qnum()) {
                        $ids = [];
                        while ($this->next(MYSQL_NUM) !== false) $ids[] = $this->qrow[0];
                        $this->sqlFree();
                        $ids = implode(',', $ids);
                        $this->query("DELETE FROM cc_cat_sc WHERE cat_id IN($ids)");
                        $u1 = $this->updatedNum();
                        $this->query("UPDATE cc_cat SET sc='0' WHERE cat_id IN ($ids)");
                        $u2 = $this->updatedNum();
                        $t = $this->timerEnd($t);
                        $this->dm_taskLog("[parser.final]: Очищены поставщики ($u1 записей) и обнулен склад ($u2 размеров) у незатронутых брендов ($t sec)");
                    }
                }
            }
            // частичный импорт
            elseif($this->opt['mode']==2){
                if(!empty($bkeys)) {
                    // вытаскиваем бренды за пределами обновления
                    $d = $this->fetchAll("SELECT cb.brand_id, cb.name FROM cc_brand cb WHERE NOT cb.LD AND cb.gr='{$this->gr}' AND cb.brand_id NOT IN($bkeys)", MYSQL_ASSOC);
                    if ($d) {
                        $bids = [];
                        foreach ($d as $v) {
                            $bids[$v['brand_id']] = $v['name'];
                        }
                        $this->dm_taskLog("[parser.final]: Подчистка брендов...");
                        foreach (array_keys($bids) as $bid) {
                            $this->dm_updateInfo();
                            $this->dm_taskLog("[parser.final]: Очистка бренда {$bids[$bid]}");
                            $this->dm_modTask([
                                'pg_label' => "Очистка бренда {$bids[$bid]}"
                            ]);
                            $this->cr->clear();
                            $this->cr->brand_id = $bid;
                            $this->cr->bname = $bids[$bid];
                            $this->S28_loadModelsCache(true);
                            $this->S24_flushBrandCache();
                        }
                    }
                }
            }

            $status = 1;
            $this->cc->addCacheTask('brands pricesNoIntPrice sizes modAll waitList', $this->gr);

        } else {
            $status = 0;
        }

        if(1) {

            $this->param['opt'] = $this->opt;
            if ($this->gr == 1) {
                $this->param['result']['exSuffixes'] = $this->exSuffixes;
                $this->param['result']['runflatSuffixes'] = $this->rfSuffixes;
            } else {
                $this->param['result']['sups'] = $this->sups;
            }
            $this->param['result']['brandsFromFile'] = array_keys($this->brandsFromFile);
            $this->param['result']['suplrsFromFile'] = array_keys($this->suplrsFromFile);
            $this->param['result']['insertedTiposCounter'] = $this->insertedTiposCounter;
            $this->param['result']['insertedModelsCounter'] = $this->insertedModelsCounter;
            $this->param['result']['insertedBrandsCounter'] = $this->insertedBrandsCounter;
            $this->param['result']['updatedCatsCounter'] = $this->updatedCatsCounter;
            $this->param['result']['updatedModelsCounter'] = $this->updatedModelsCounter;
            $this->param['result']['insertedSCCounter'] = $this->insertedSCCounter;
            $this->param['result']['updatedSCCounter'] = $this->updatedSCCounter;
            $this->param['result']['deletedSCCounter'] = $this->deletedSCCounter;

            unset($this->brands, $this->models, $this->suplrs, $this->sups);

            $this->sqlFree();
            $this->cc->sqlFree();

            $tsFinished = time();
            $this->param['result']['elapsed'] = $tsFinished - $tsRun;

            $param = Tools::DB_serialize($this->param);
            $this->query("UPDATE cii_file SET status=$status, param='$param' WHERE file_id='{$this->file_id}'");


            $this->cc->sql_close();
            $this->sql_close();

            $this->brands=$this->suplrs=$this->models=[];

            $this->dm_taskLog("[parser.final]: Обработка завершена ({$this->param['result']['elapsed']} sec).");

            $this->dm_updateInfo();
            $this->dm_modTask([
                'ts_finished' => time(),
                'ts' => time(),
                'state' => 'finished',
                'pg_index' => 100,
                'pg_label' => 'Обработка прайс листа успешно завершена'
            ]);

        }

        return true;
    }

    /*
     * загружаем все марки авто-реплики
     * выбираем все строки cii содержащие в колонке бренда поставщика реплики из списка cc_sup
     * разбираем модель: в начале должно встретиться название марки реплики(авто), все что правее - название модели
     * если модель окружена скобками - убираем их
     * если марка реплики (авто) не опознано - пишем ИГНОР
     */
    private function S8_prepareReplica()
    {
        if($this->gr==2 && !empty($this->sups)){

            $this->dm_taskLog('[parser]: Начинаю преобразование имен реплики');
            $this->dm_modTask([
                'pg_label' => 'Преобразование имен реплики'
            ]);

            $t=microtime(true);

            // читаем все марки авто
            $this->query("SELECT name FROM cc_brand WHERE NOT LD AND replica=1 AND sup_id=0");
            $ab=[];
            while($this->next(MYSQL_NUM)!==false)
            {
                $ab[]=Tools::unesc($this->qrow[0]);
            }

            // читаем всех поставщиков реплики
            $rb=[];
            foreach($this->sups as $v){
                $rb[]="brand LIKE '".Tools::like($v)."'";
            }
            $rb=implode(' OR ',$rb);
            $this->query($sql="SELECT item_id, TRIM(brand) AS brand, model FROM cii_item WHERE file_id='{$this->file_id}' AND ($rb)");
            $this->dm_taskLog("[parser]: Выбрано ".$this->qnum().' строк с репликой для преобразования');
            //echo $sql."\n";
           // print_r( $this->sups);
            //print_r($ab);
            $u=0;
            $ign=0;
            $db=new DB();
            if($this->qnum())
            {
                $updates=[];
                while ($this->next() !== false)
                {
                    if (preg_match("/([^\(]+)\((.+)\)/u", $this->qrow['model'], $m))
                    {
                        $brand = trim(Tools::cutDoubleSpaces($this->qrow['brand']));
                        $newBrand = trim(Tools::cutDoubleSpaces($m[1]));
                        $newModel = trim(Tools::cutDoubleSpaces($m[2]));
                        $sup_id = (int)Tools::mb_array_search($brand, $this->sups);
                        $item_id = $this->qrow['item_id'];

                        // replica, sup_id, brand, model
                        $updates[] = "('$item_id','$sup_id','$newBrand','$newModel')";
                    }
                }
                if($updates)
                {
                    if (count($updates) <= 10000) {
                        $this->query("INSERT INTO cii_item (item_id, sup_id, brand, model) VALUES " . implode(',', $updates) . " ON DUPLICATE KEY UPDATE replica='1', sup_id=VALUES(sup_id), brand=VALUES(brand), model=VALUES(model)");
                        $u += $this->updatedNum() / 2;
                    }
                    else {
                        while (!empty($updates)) {
                            $slice = [];
                            // выбираем по 10000 строк
                            $i=0;
                            do {
                                $slice[] = array_shift($updates);
                                $i++;
                            } while ($i<10000 && count($updates));
                            $this->query("INSERT INTO cii_item (item_id, sup_id, brand, model) VALUES " . implode(',', $slice) . " ON DUPLICATE KEY UPDATE replica='1', sup_id=VALUES(sup_id), brand=VALUES(brand), model=VALUES(model)");
                            $u += $this->updatedNum() / 2;
                        }
                    }
                    unset($updates);
                }
            }
            unset($db);
            $t=$this->timerEnd($t);
            $this->dm_taskLog("[parser]: Конец преобразования имен реплики, обновлено $u строк cii, кроме того $ign с неверным форматом (см. статус игнор) ($t sec)");
        }
    }

    private function S12_checkIgnore()
    {
        $ignore = false;
        $trans = '';

        if($this->CIIR['sklad'] <=0) {
            $ignore = true;
            $trans = ':IGN нулевой склад:';
        }

        // проверяем валидность параметров
        elseif($this->gr == 1){
            if($this->CIIR['P1'] <= 0 || $this->CIIR['P3'] <= 0) {
                $ignore = true;
                $trans = ':IGN ошибка в размере:';
            }
        } else {
            if($this->CIIR['P4'] <= 0 || $this->CIIR['P6'] <= 0  || $this->CIIR['P5'] <= 0 ||  $this->CIIR['P2'] <= 0) {
                $ignore = true;
                $trans = ':IGN ошибка в размере:';
            }
        }
        if (!$ignore) // проверем валидность цены
            if ($this->CIIR['price1'] < 0 || $this->CIIR['price2'] < 0 || $this->CIIR['price3'] < 0)
            {
                $ignore = true;
                $trans = ':IGN отрицательное значение цены:';
            }
            elseif ($this->opt['suplrsMinPrice'] && ($this->CIIR['price1'] < $this->opt['suplrsMinPrice'] && $this->CIIR['price2'] < $this->opt['suplrsMinPrice'] && $this->CIIR['price3'] < $this->opt['suplrsMinPrice']))
            {
                $ignore = true;
                $trans = ':IGN непроходная цена:';
            }

        if($ignore){
            $status=$this->opt['mode']==1 ? 23 : 3;
            $this->update('cii_item', [
                'cstatus'=>$status,
                'mstatus'=>$status,
                'bstatus'=>$status,
                'transform'=>$trans
            ], "item_id='{$this->CIIR['item_id']}'");

            return true;
        }
        else
        {
            return false;
        }
    }

    private function S13_prepareBrandAndModel($brand, $model)
    {
        $brand = trim(Tools::cutDoubleSpaces($brand));
        $model = trim(Tools::cutDoubleSpaces($model));
        if ($this->gr == 2)
        {
            if ($this->CIIR['replica'])
            {
                $this->cr->replica = 1;
                $this->cr->sup_id = $this->CIIR['sup_id'];
            }
            else
            {
                // хак для КИК
                // для дополнительного параметра. В базе сайта должно быть поле cc_cat.code
                if (mb_strpos($model, '##') !== false)
                {
                    $m = explode('##', $model);
                    $model = trim($m[0]);
                    $this->cr->code = trim(@$m[1]);
                }
                elseif (preg_match("/([^\(]+)\([КСKC]{2}([0-9]+)\)(.*)/iu", $model, $m))
                {
                    $model = trim($m[1] . ' ' . $m[3]);
                    $this->cr->code = trim('КС' . $m[2]);
                }
            }
        }
        $this->cr->bname = $brand;
        $this->cr->mname = $model;
    }

    private function S16_getSuplrId()
    {
        $this->cr->suplrName = Tools::unesc($this->CIIR['company']);
        $this->cr->suplr_id=Tools::mb_array_search($this->cr->suplrName, $this->suplrs);
        if($this->opt['mode']!=1 && $this->cr->suplr_id === false){
            $dt = Tools::dt();
            $this->query("INSERT INTO cc_suplr (name,dt_added) VALUES('{$this->CIIR['company']}','$dt')");
            $this->suplrs[$this->cr->suplr_id = $this->lastId()] = $this->cr->suplrName;
            $this->dm_taskLog("[getSuplrId]: добавлен поставщик {$this->cr->suplrName} ({$this->cr->suplr_id})");
        }
        if($this->cr->suplr_id) $this->suplrsFromFile[$this->cr->suplr_id]=$this->cr->suplrName;
    }

    private function S18_getBrandId()
    {
        if($this->gr==2){
            if(!$this->cr->replica){
                $this->cr->brand_id=(int)Tools::mb_array_search($this->cr->bname, $this->brands[0]);
                if(!$this->cr->brand_id) {
                    $this->cr->brand_id = (int)Tools::mb_array_search($this->cr->bname, $this->brands[1]);
                    if($this->cr->brand_id) $this->cr->replica=1;
                }
            }else{
                $this->cr->brand_id=(int)Tools::mb_array_search($this->cr->bname, $this->brands[1]);
                if(!$this->cr->brand_id) {
                    $this->cr->brand_id = (int)Tools::mb_array_search($this->cr->bname, $this->brands[0]);
                    if ($this->cr->brand_id) $this->cr->replica = 0;
                }
            }
        }else{
            $this->cr->brand_id=(int)Tools::mb_array_search($this->cr->bname, $this->brands[0]);
        }

        if ($this->cr->brand_id) {
            if ($this->opt['mode']==1) $this->cr->bstatus = 21; else $this->cr->bstatus = 1;
        } else {
            if ($this->opt['mode']!=1) {
                $dt = date("Y-m-d H:i:s");
                $b = Tools::esc($this->cr->bname);
                $this->query("INSERT INTO cc_brand (gr,name,replica,dt_added,H) VALUES('{$this->gr}','{$b}',{$this->cr->replica},'$dt',1)");
                $this->cr->brand_id = $this->lastId();
                if (!$this->cr->brand_id) return $this->dm_taskError("[getBrandId]: Добавлен бренд {$this->cr->bname} ({$this->cr->brand_id})");
                $this->insertedBrandsCounter++;
                $this->cc->sname_brand($this->cr->brand_id);
                $this->cr->bstatus = 2;
                $this->brands[$this->cr->replica][$this->cr->brand_id] = $this->cr->bname;
            } else {
                $this->cr->bstatus = 22;
            }
        }

        if ($this->cr->brand_id) $this->brandsFromFile[$this->cr->brand_id]=$this->cr->bname;


    }


    private function loadSuplrsCache()
    {
        $t=microtime(true);
        $this->query("SELECT SQL_NO_CACHE * FROM cc_suplr ORDER BY name");
        $this->suplrs = [];
        if ($this->qnum()) while ($this->next() !== false) {
            $this->suplrs[$this->qrow['suplr_id']] = Tools::unesc($this->qrow['name']);
        }
        $this->sqlFree();
        $t=$this->timerEnd($t);
        $this->dm_taskLog("[loadSuplrsCache]: кеш поставщиков загружен ($t sec)");
    }

    private function loadBrandsCache()
    {
        // кешируем бренды
        $t=microtime(true);
        $this->cc->que('brands', $this->gr, 0, 'AND sup_id=0');
        $this->brands = array(0 => array(), 1 => array());
        if ($this->cc->qnum()) while ($this->cc->next() !== false) {
            $this->brands[$this->cc->qrow['replica'] == 0 ? 0 : 1][$this->cc->qrow['brand_id']] = trim(Tools::cutDoubleSpaces(Tools::unesc($this->cc->qrow['name'])));
        }
        $this->cc->sqlFree();
        $t=$this->timerEnd($t);
        $this->dm_taskLog("[loadBrandsCache]: кеш брендов загружен ($t sec)");
    }

    private function S28_loadModelsCache($force=false)
    {
        $this->cachedBrandName = $this->cr->bname;
        $this->models = [];

        if(!$force) {
            if (empty($this->cr->brand_id) || $this->cr->bstatus == 2 || $this->cr->bstatus == 22) return; // пустой кеш если бренд только что добавлен или в режиме теста с ненайденныим брендом работаем
        }

        $t=microtime(true);
        // модели
        $this->query("SELECT SQL_NO_CACHE mm.sup_id,  mm.name,  mm.model_id,  mm.suffix AS msuffix FROM cc_model mm WHERE mm.LD = '0' AND mm.brand_id = '{$this->cr->brand_id}'");

        if ($this->qnum()) {
            while ($this->next(MYSQL_ASSOC) !== false)
            {

                if (!isset($this->models[$this->qrow['model_id']]))
                {
                    $this->models[$this->qrow['model_id']] = [
                        'm' => trim(Tools::cutDoubleSpaces(Tools::unesc($this->qrow['name']))),
                        // 's' => trim(Tools::cutDoubleSpaces(Tools::unesc($this->qrow['msuffix']))),
                        'T' => [], // типоразмеры
                        'a' => ''   //-- действие со строкой (a,e) - добавлена, есть упоминание
                    ];
                    if ($this->gr == 2)
                    {
                        $this->models[$this->qrow['model_id']]['sup'] = $this->qrow['sup_id'];
                    }
                }
            }

            // размеры и склад
            $this->query("SELECT SQL_NO_CACHE cc.cat_id,  cc.sc AS catSC,  mm.sup_id,  mm.name,  mm.model_id,  mm.suffix AS msuffix,  cc.suffix AS csuffix, cc.P1 + '0' AS P1,  cc.P2 + '0' AS P2,  cc.P3 + '0' AS P3,  cc.P4 + '0' AS P4,  cc.P5 + '0' AS P5,  cc.P6 + '0' AS P6,  cc.P7,  cc.bprice,  cc.cprice,  ss.price1,  ss.price2,  ss.price3,  ss.sc, ss.cat_sc_id, ss.suplr_id FROM cc_cat cc JOIN cc_model mm USING (model_id) LEFT JOIN cc_cat_sc ss USING(cat_id) WHERE cc.LD = '0' AND mm.LD = '0' AND mm.brand_id = '{$this->cr->brand_id}' AND cc.ignoreUpdate = 0");


            if ($this->qnum())
            {
                while ($this->next(MYSQL_ASSOC) !== false)
                {

                    $csuffix = trim(Tools::cutDoubleSpaces(Tools::unesc($this->qrow['csuffix'])));

                    if ($this->gr == 1)
                    {
                        // ШИНЫ
                        $s = $this->splitTSuffix($csuffix);
                        $s = array_diff($s, array(
                            'ZR',
                            'zr',
                            'Z'
                        )); // убираем Zr чтобы корректно работало сравнение суффиксов шин

                        if (!isset($this->models[$this->qrow['model_id']]['T'][$this->qrow['cat_id']]))
                        {
                            $this->models[$this->qrow['model_id']]['T'][$this->qrow['cat_id']] = array(
                                'P1' => $this->qrow['P1'], // R
                                'P2' => $this->qrow['P2'], // height
                                'P3' => $this->qrow['P3'], // width
                                //'P4' => $this->qrow['P4'], // C
                                'P6' => $this->qrow['P6'], // ZR
                                'P7' => $this->qrow['P7'], // inis
                                's' => $s, // суффиксы
                                'sc' => $this->qrow['catSC'] * 1,
                                'bp' => $this->qrow['bprice'] * 1,
                                'cp' => $this->qrow['cprice'] * 1,
                                'T' => [], // здесь будут остатки
                                'a' => ''   //-- действие со строкой (a,u,e) - добавлен, обновить, есть упоминание
                            );
                        }

                    }
                    else
                    {
                        // ДИСКИ
                        if (!isset($this->models[$this->qrow['model_id']]['T'][$this->qrow['cat_id']]))
                        {
                            $this->models[$this->qrow['model_id']]['T'][$this->qrow['cat_id']] = array(
                                'P1' => $this->qrow['P1'], //ET
                                'P2' => $this->qrow['P2'], //J
                                'P3' => $this->qrow['P3'], // DIA
                                'P4' => $this->qrow['P4'], // дырки
                                'P5' => $this->qrow['P5'], // R
                                'P6' => $this->qrow['P6'], // DCO
                                's' => $csuffix,
                                'sc' => $this->qrow['catSC'] * 1,
                                'bp' => $this->qrow['bprice'] * 1,
                                'cp' => $this->qrow['cprice'] * 1,
                                'T' => [], // здесь будут остатки
                                'a' => ''  //-- действие со строкой (a,u,e) - добавлен, обновить, есть упоминание
                            );
                        }
                    }

                    if ($this->qrow['cat_sc_id'])
                    {
                        $this->models[$this->qrow['model_id']]['T'][$this->qrow['cat_id']]['T'][$this->qrow['suplr_id']] = [
                            'id' => $this->qrow['cat_sc_id'],
                            'sc' => $this->qrow['sc'] * 1,
                            'price1' => $this->qrow['price1'] * 1,
                            'price2' => $this->qrow['price2'] * 1,
                            'price3' => $this->qrow['price3'] * 1,
                            'a' => ''  //-- действие со строкой (a|d,u,e) - добавлен, удалить, обновить, есть упоминание
                        ];
                    }
                }
            }
        }
        $this->sqlFree();
        $t=$this->timerEnd($t);
        $this->dm_taskLog("[loadModelsCache]: кеш моделей {$this->cachedBrandName} загружен ($t sec)");
    }

    private function S30_parseModelAndTipo()
    {
        $model_ids = array();

        if ($this->gr == 1) {
            //ШИНЫ
            $this->cr->IN = trim($this->CIIR['P7']);
            $this->cr->IS = trim($this->CIIR['P7_1']);

            // делаем суффиксы в массив из поля suffix
            $this->cr->suffix = $this->splitTSuffix(trim(Tools::cutDoubleSpaces($this->CIIR['suffix'])));

            // добавляем также суффиксы из full_name используя exSuffixes
            $this->getBrandInSuffixArr(); // ищем код бренда в exSuffixes и заносим его в tSuffixesBrandKey
            $s3 = $s = trim(Tools::cutDoubleSpaces($this->CIIR['full_name'])) . ' ';
            //ищем суффикс сначала для бренда
            if (!empty($this->exSuffixes[$this->cr->tSuffixesBrandKey]))
                foreach ($this->exSuffixes[$this->cr->tSuffixesBrandKey] as $v) {
                    $s2 = str_ireplace(" $v ", ' ', $s);
                    if (Tools::mb_strcasecmp($s, $s2) != 0) $this->cr->suffix[] = $v;
                    $s = $s2;
                }
            //потом глобальный
            if (!empty($this->exSuffixes[0]))
                foreach ($this->exSuffixes[0] as $v) {
                    $s2 = str_ireplace(" $v ", ' ', $s);
                    if (Tools::mb_strcasecmp($s, $s2) != 0) $this->cr->suffix[] = $v;
                    $s = $s2;
                }

            $this->cr->suffix = array_unique($this->cr->suffix); // убираем дубли

            // выделяем статус ZR
            if (count(array_intersect($this->cr->suffix, array('ZR', 'Z')))) {
                $this->cr->P6 = 1;
                // убираем из суффикса ZR
                $this->cr->suffix = array_diff($this->cr->suffix, array('ZR', 'Z'));
            } else $this->cr->P6 = 0;

            //убираем пустые на всякий случай
            foreach ($this->cr->suffix as $k => $v) {
                if (trim($v) == '') unset($this->cr->suffix[$k]);
            }

            // выделяем ZR из полного названия
            // if (preg_match("/ ZR[0-9]+ /u", trim($s3))) $this->cr->P6 = 1;


            // если есть модель, то проверяем есть ли в ней размер, если нет размера, то добавляем, если нет модели, то добавляем модель и в нее добавляем размер
            // параметры Не учитывающиесмя при сравнении: шипы,сезон,тип ТС

            // ищем модель
            foreach ($this->models as $km => $vm) {
                if (Tools::mb_strcasecmp($vm['m'], $this->cr->mname) == 0) {
                    $model_ids[] = $km; // ищем все подходящие модели
                }
            }


            // ищем размер
            if (!empty($model_ids)) { // есть модель(и)

                if ($this->opt['mode']==1) $this->cr->mstatus = 21; else $this->cr->mstatus = 1;

                /* Первый проход по размерам: дополнение ИН
                    если XL то ставим максимальный ИС
                */
                if (empty($this->cr->IN) && !empty($this->cr->IS)) {
                    $_XL = Tools::mb_array_search('XL', $this->cr->suffix); // XL есть
                    $_RF = false; // один из суффиксов ранфлет есть
                    $INs = array();
                    foreach ($this->rfSuffixes as $v)
                        if (Tools::mb_array_search($v, $this->cr->suffix) !== false) {
                            $_RF = true;
                            break 1;
                        }
                    foreach ($model_ids as $mid) {
                        foreach ($this->models[$mid]['T'] as $kt => $vt) {
                            $XL = Tools::mb_array_search('XL', $vt['s']);
                            $RF = false;
                            foreach ($this->rfSuffixes as $v)
                                if (Tools::mb_array_search($v, $vt['s']) !== false) {
                                    $RF = true;
                                    break 1;
                                }
                            // смотрим только полные ИНИС vt['P7']
                            if (preg_match("~^([0-9\/]+)([a-z]{1})$~iu", trim($vt['P7']), $m)) {
                                $in = $m[1];
                                $is = $m[2];
                                $eq = 1;
                                if ($vt['P1'] != $this->CIIR['P1']) $eq = 0;
                                elseif ($vt['P2'] != $this->CIIR['P2']) $eq = 0;
                                elseif ($vt['P3'] != $this->CIIR['P3']) $eq = 0;
                                elseif ($is != $this->cr->IS) $eq = 0;
                                // проверяем обязательные суффиксы
                                elseif ($_XL !== false && $XL === false || $_XL === false && $XL !== false) $eq = 0;
                                elseif ($_RF !== false && $RF === false || $_RF === false && $RF !== false) $eq = 0;


                                if ($eq) {
                                    $model_ids = array($mid); // оставляем в списке только текущую модель
                                    $this->cr->IN = $in;
                                    $this->cr->transform[':ИН ' . $this->cr->IN . ':'] = $this->cr->IN;
                                    $INs[] = $in;
                                    //break 2;  если не включать, то будут отображены все найденные подстановки ИН.
                                }
                            }
                        }
                    }
                    // максимальное значение станет ИН
                    if (!empty($INs)) {
                        if ($_XL !== false) {
                            sort($INs, SORT_NUMERIC);
                            asort($this->cr->transform, SORT_NUMERIC);
                        } else {
                            arsort($INs, SORT_NUMERIC);
                            arsort($this->cr->transform, SORT_NUMERIC);
                        }
                        $this->cr->IN = array_pop($INs);
                    }
                }

                // второй проход: поиск размера
                foreach ($model_ids as $mid) {

                    foreach ($this->models[$mid]['T'] as $kt => $vt) {
                        $eq = 1;
                        /*
                        $eqs=[];
                        if (!$this->arrayCompare($this->cr->suffix, $vt['s'])) {$eq = 0; $eqs[]="SUF";}
                        elseif ($vt['P1'] != $this->CIIR['P1']) {$eq = 0; $eqs[]="P1";}
                        elseif ($vt['P2'] != $this->CIIR['P2']) {$eq = 0; $eqs[]="P2";}
                        elseif ($vt['P3'] != $this->CIIR['P3']) {$eq = 0; $eqs[]="P3";}
                        elseif ($vt['P6'] != $this->cr->P6) {$eq = 0; $eqs[]="ZR";}// ZR
                        elseif ($vt['P7'] != ($this->cr->IN . $this->cr->IS)) {$eq = 0; $eqs[]="INIS";}// ИНИС
*/
                        if (!$this->arrayCompare($this->cr->suffix, $vt['s'])) $eq = 0;
                        elseif ($vt['P1'] != $this->CIIR['P1']) $eq = 0;
                        elseif ($vt['P2'] != $this->CIIR['P2']) $eq = 0;
                        elseif ($vt['P3'] != $this->CIIR['P3']) $eq = 0;
                        elseif ($vt['P6'] != $this->cr->P6) $eq = 0; // ZR
                        elseif ($vt['P7'] != ($this->cr->IN . $this->cr->IS))$eq = 0;

                        if ($eq) { // размеры равны
                            $this->cr->model_id = $mid;
                            $this->cr->cat_id=$kt;
                            if ($this->opt['mode']==1) $this->cr->cstatus = 21; else $this->cr->cstatus = 1;
                            break 2;
                        }
                        //$this->cr->transform[$kt.':'.implode(' ',$eqs)]='';

                    }
                }

                if ($this->cr->model_id == 0) { // нет размера
                    $this->cr->model_id = array_shift($model_ids); // берем первую подходящую модель
                    // добавляем размер
                    $this->insertTipoCache();
                    if ($this->opt['mode']!=1) $this->cr->cstatus = 2; else  $this->cr->cstatus = 22;
                }
            } else { // нет модели
                $this->insertModelCache();
                $this->insertTipoCache();
                if ($this->opt['mode']!=1) {
                    $this->cr->mstatus = 2;
                    $this->cr->cstatus = 2;
                } else {
                    $this->cr->mstatus = 22;
                    $this->cr->cstatus = 22;
                }
            }

            // ДИСКИ
            // если есть модель, то проверяем есть ли в ней размер, если нет размера, то добавляем, если нет модели, то добавляем модель и в нее добавляем размер
            // для сравнения DIA в размерах используется таблица$this->opt['diaMerge'] для поиска аналогов
            // для сравнения ступиц в размерах используется таблица$this->opt['svMerge'] для поиска аналогов
            // параметры Не учитывающиесмя при сравнении: тип диска
        }
        else {
            $this->cr->suffix = trim(Tools::cutDoubleSpaces($this->CIIR['suffix']));

            // находим аналоги цветов $this->iSuffixes по матрице
            $this->cr->iSuffixes = array($this->cr->suffix);
            if ($this->cr->suffix != '') {
                $bs = $nbs = array();
                if (!empty($this->sMatrix[2])) foreach ($this->sMatrix[2] as $sk => $sv) {
                    if (!empty($sv[$this->cr->brand_id]['iSuffixes']) && Tools::mb_array_search($this->cr->suffix, $sv[$this->cr->brand_id]['iSuffixes']) !== false) $bs[] = $sk;
                    if (!empty($sv[0]['iSuffixes']) && Tools::mb_array_search($this->cr->suffix, $sv[0]['iSuffixes']) !== false) $nbs[] = $sk;
                }
                // приоритет для суффиксов с указанным брендом. В идеале iSuffixes должен содержать одно значение
                $nbs = array_diff($nbs, $bs);
                $this->cr->iSuffixes = array_merge($this->cr->iSuffixes, $bs, $nbs);
            }

            // ищем модель
            foreach ($this->models as $km => $vm) {
                if (Tools::mb_strcasecmp($vm['m'], $this->cr->mname) == 0) { // && ($vm['sup'] == $this->cr->sup_id)
                    $model_ids[] = $km; // ищем все подходящие модели
                }
            }

            // ищем размер
            if (!empty($model_ids)) { // есть модель(и)

                if ($this->opt['mode']==1) $this->cr->mstatus = 21; else $this->cr->mstatus = 1;

                foreach ($model_ids as $mid) {

                    foreach ($this->models[$mid]['T'] as $kt => $vt) {
                        $eq = 1;
                        if ($vt['P1'] != $this->CIIR['P1']) $eq = 0; // ET
                        elseif ($vt['P2'] != $this->CIIR['P2']) $eq = 0; // J
                        elseif ($vt['P5'] != $this->CIIR['P5']) $eq = 0; // диаметр
                        if ($eq) {
                            $eq1 = 0;
                            // поиск по матрице цветов включая ситуацию когда vs['s']=='' && $this->suffix==''
                            if (($im = Tools::mb_array_search($vt['s'], $this->cr->iSuffixes)) !== false) {
                                $eq1 = 1;
                                if ($im > 0) $this->cr->transform[$this->cr->iSuffixes[$im]] = 1; else $this->cr->transform[':DIRECTHIT:'] = 1;
                            }
                            // поиск с игнорированием  суффикса
                            if (!$eq1 && !empty($this->ignoreDiskSuffixes) && $vt['s'] == '' && Tools::mb_array_search($this->cr->suffix, $this->ignoreDiskSuffixes) !== false) {
                                $eq1 = 1;
                                $this->cr->transform[':IDS:'] = 1;
                            }
                            // поиск c пустым суффиксом
                            if (!$eq1 && $this->emptyDiskSuffix != '' && $this->cr->suffix == '' && Tools::mb_strcasecmp($vt['s'], $this->emptyDiskSuffix) == 0) {
                                $eq1 = 1;
                                $this->cr->transform[':EDS:'] = 1;
                            }
                            if (!$eq1) $eq = 0;
                        }

                        if ($eq)
                        { // DIA трансформ
                            if (!empty($this->opt['diaMerge']) && isset($this->opt['diaMerge'][(string)$this->CIIR['P3']]))
                            {
                                $newDia = $this->opt['diaMerge'][(string)$this->CIIR['P3']];
                                $this->cr->transform[":Dia {$newDia}:"] = 1;
                                if ($newDia != $vt['P3'])
                                {
                                    $eq = 0;
                                }
                            }
                            elseif ($vt['P3'] != $this->CIIR['P3']) $eq = 0;
                        }

                        if($eq) {
                            $sv_vt = $vt['P4'] . '*' . $vt['P6'];
                            $sv = $this->CIIR['P4'] . '*' . $this->CIIR['P6'];
                            if (!empty($this->opt['svMerge'])) { // LZxPCD трансформ
                                $eqsv = 0;
                                $inSVT = false;
                                foreach ($this->opt['svMerge'] as $k => $v) {
                                    if ($k == $sv) $inSVT = true;
                                    if (($v[0] . '*' . $v[1]) == $sv_vt && $k == $sv) {
                                        $eqsv = 1;
                                        $this->cr->transform[":SV {$sv_vt}:"] = 1;
                                        break;
                                    }
                                }
                                // Будет добавлен или обновлен только типоразмер с преобразованной свеврловкой
                                if (!$eqsv && $inSVT && $sv_vt == $sv) $eq = 0;
                                elseif (!$eqsv) if ($sv_vt != $sv) $eq = 0;
                            }
                            elseif ($sv_vt != $sv) $eq = 0;
                        }

                        if ($eq) { // размеры равны
                            $this->cr->model_id = $mid;
                            $this->cr->cat_id = $kt;
                            if ($this->opt['mode']==1) $this->cr->cstatus = 21; else $this->cr->cstatus = 1;
                            break 2;
                        }
                    }
                }
                if ($this->cr->model_id == 0) { // нет размера
                    $this->cr->model_id = array_shift($model_ids); // берем первую подходящую модель
                    // добавляем размер
                    $this->insertTipoCache();
                    if ($this->opt['mode']!=1) $this->cr->cstatus = 2; else  $this->cr->cstatus = 22;
                }
            } else { // нет модели
                $this->insertModelCache();
                $this->insertTipoCache();
                if ($this->opt['mode']!=1) {
                    $this->cr->mstatus = 2;
                    $this->cr->cstatus = 2;
                } else {
                    $this->cr->mstatus = 22;
                    $this->cr->cstatus = 22;
                }
            }

        }
    }

    private function S40_updateItemRow()
    {
        // обновляем cii_item
        $aq = [];
        if($this->cr->bname != $this->CIIR['brand']) $aq['brand'] = Tools::esc($this->cr->bname);
        if ($this->cr->code != '')
        {
            $aq['model'] = $this->cr->mname . '##' . $this->cr->code;
        }
        if($this->cr->mname != $this->CIIR['model']) $aq['model'] = Tools::esc($this->cr->mname);

        $tr = Tools::esc(implode(' ', array_keys($this->cr->transform))); // трансформации
        if($tr != $this->CIIR['transform']) $aq['transform'] = $tr;

        if ($this->gr == 1) {
            if ($this->cr->P6 == 1) $this->cr->suffix[] = 'ZR'; // показываем статус ZR в поле суффикса
            if($this->cr->suffix != $this->CIIR['suffix']) $aq['suffix'] = implode(' ', $this->cr->suffix);
        } else {
            if($this->cr->sup_id != $this->CIIR['sup_id']) $aq['sup_id'] = $this->cr->sup_id;
            if($this->cr->replica != $this->CIIR['replica']) $aq['replica'] = $this->cr->replica;
        }

        if($this->cr->brand_id != $this->CIIR['brand_id']) $aq['brand_id'] = $this->cr->brand_id;
        if($this->cr->model_id != $this->CIIR['model_id']) $aq['model_id'] = $this->cr->model_id;
        if($this->cr->cat_id != $this->CIIR['cat_id']) $aq['cat_id'] = $this->cr->cat_id;

        if($this->cr->suplr_id != $this->CIIR['suplr_id']) $aq['suplr_id']=$this->cr->suplr_id;

        if($this->cr->cstatus != $this->CIIR['cstatus']) $aq['cstatus'] = $this->cr->cstatus;
        if($this->cr->bstatus != $this->CIIR['bstatus']) $aq['bstatus'] = $this->cr->bstatus;
        if($this->cr->mstatus != $this->CIIR['mstatus']) $aq['mstatus'] = $this->cr->mstatus;

        if($this->cr->price1 != $this->CIIR['price1'])  $aq['price1'] = $this->cr->price1;
        if($this->cr->price2 != $this->CIIR['price2'])  $aq['price2'] = $this->cr->price2;
        if($this->cr->price3 != $this->CIIR['price3'])  $aq['price3'] = $this->cr->price3;

        if (count($aq)) $this->update('cii_item', $aq, "item_id='{$this->CIIR['item_id']}'");
    }

    private function insertModelCache()
    {
        /*
         * добавляет модель и в кеш и БД
         */
        if ($this->opt['mode'] != 1) {
            if ($this->gr == 1) {
                // ШИНЫ
                $this->insert('cc_model', array(
                    'brand_id' => $this->cr->brand_id,
                    'gr'       => 1,
                    'name'     => Tools::esc($this->cr->mname),
                    'P1'       => $this->CIIR['MP1'],
                    'P2'       => $this->CIIR['MP2'],
                    'P3'       => $this->CIIR['MP3'],
                    'dt_added' => Tools::dt()
                ));
            } else {
                // ДИСКИ
                $this->insert('cc_model', array(
                    'brand_id' => $this->cr->brand_id,
                    'gr'       => 2,
                    'sup_id'   => $this->cr->sup_id,
                    'name'     => Tools::esc($this->cr->mname),
                    'P1'       => $this->CIIR['MP1'],
                    'dt_added' => Tools::dt()
                ));
            }
            $this->cr->model_id = $this->lastId();
            $this->cc->sname_model($this->cr->model_id);
            $this->models[$this->cr->model_id] = [
                'm' => $this->cr->mname,
                'T' => [],
                'a' => 'a'
                //,'s' => ''
            ];
            if ($this->gr == 2) {
                $this->models[$this->cr->model_id]['sup'] = $this->cr->sup_id;
            }
            $this->insertedModelsCounter++;
            $this->dm_taskLog("[insertModelCache]: добавлена модель {$this->cr->mname}");
        }
    }

    private function insertTipoCache()
    {
        /*
         * вызов метода только после insertModelCache
         * записываем в базу новый размер и добавляем его в кеш
         */

        if($this->gr==2) {
            $dia = $this->getDiam($this->CIIR['P3']);
            list($lz, $pcd) = $this->getSV($this->CIIR['P4'], $this->CIIR['P6']);
            $suf = $this->makeDiskSuffix();
        }

        if ($this->opt['mode'] != 1){
            if (empty($this->cr->model_id)) {
                $this->dm_taskError("[insertTipoCache]: ERROR: Не передан model_id /item_id={$this->CIIR['item_id']}/");
                exit(1);
            }
            $this->insertedTiposCounter++;
            // ставим метку модели - упоминание
            $this->models[$this->cr->model_id]['a'] = 'e';

            // ШИНЫ
            if ($this->gr == 1) {
                $suf = trim(Tools::esc(implode(' ', $this->cr->suffix)));
                $inis = $this->cr->IN . $this->cr->IS;
                $this->insert('cc_cat', array(
                    'model_id' => $this->cr->model_id,
                    'gr'       => 1,
                    'P1'       => $this->CIIR['P1'],
                    'P2'       => $this->CIIR['P2'],
                    'P3'       => $this->CIIR['P3'],
                    'P4'       => $this->cc->isCinSuffix($suf),
                    'P6'       => $this->cr->P6,
                    'P7'       => Tools::esc($inis),
                    'suffix'   => $suf,
                    'cur_id'   => 1,
                    'dt_added' => Tools::dt()
                ));
                $this->cr->cat_id = $this->lastId();
                $this->cc->sname_cat($this->cr->cat_id);
                $this->models[$this->cr->model_id]['T'][$this->cr->cat_id] = array(
                    'P1' => $this->CIIR['P1'],
                    'P2' => $this->CIIR['P2'],
                    'P3' => $this->CIIR['P3'],
                    'P6' => $this->cr->P6,
                    'P7' => $inis,
                    's'  => $this->cr->suffix,
                    'sc' => 0,
                    'bp' => 0,
                    'cp' => 0,
                    'a'  => 'a',
                    'T'  => []
                );

                // ДИСКИ
            } elseif($this->gr==2) {

                $this->insert('cc_cat', array(
                    'model_id' => $this->cr->model_id,
                    'gr'       => 2,
                    'P1'       => $this->CIIR['P1'],
                    'P2'       => $this->CIIR['P2'],
                    'P3'       => $dia,
                    'P4'       => $lz,
                    'P5'       => $this->CIIR['P5'],
                    'P6'       => $pcd,
                    'suffix'   => Tools::esc($suf),
                    'cur_id'   => 1,
                    'dt_added' => Tools::dt()
                ));
                $this->cr->cat_id = $this->lastId();
                $this->cc->sname_cat($this->cr->cat_id);
                $this->models[$this->cr->model_id]['T'][$this->cr->cat_id] = array(
                    'P1' => $this->CIIR['P1'],
                    'P2' => $this->CIIR['P2'],
                    'P3' => $dia,
                    'P4' => $lz,
                    'P5' => $this->CIIR['P5'],
                    'P6' => $pcd,
                    's'  => $suf,
                    'sc' => 0,
                    'bp' => 0,
                    'cp' => 0,
                    'a'  => 'a',
                    'T'  => []
                );
            }

            echo "[insertTipoCache]: размер добавлен cat_id={$this->cr->cat_id}\n";
        }

    }

    private function S35_updatePriceCache()
    {
        /*
         * $this->cr->model_id,cat_id должен быть >0
         * обновляем метку models['a'] = 'u'
         * добавлем строку склада только в кеш
         */

        if ($this->gr == 2 && $this->cr->suplr_id == $this->YSTSuplrId && !empty($this->opt['YST']['bExtras'][$this->cr->brand_id])) {
            // Яршинторг. В price3 записываем новую розницу с учетом всех наценок
            $this->cr->price3 = $this->cr->price1 + ceil($this->cr->price1 * @$this->opt['YST']['bExtras'][$this->cr->brand_id] / 100);
            /*$min = $this->minExtras[$this->gr . ''][$this->gr == 2 ? '5' : '1'][(($this->gr == 2 ? $this->CIIR['P5'] : $this->CIIR['P1']) * 1) . ''];
            if (($this->cr->price3 - $this->cr->price1) < $min) {
                $this->cr->price3 = $this->cr->price1 + $min;
            }*/
        }


        if ($this->opt['mode'] != 1) {

            if (empty($this->cr->model_id)) {
                $this->dm_taskError("[updateTipoCache]: ERROR: Не передан model_id /item_id={$this->CIIR['item_id']}/");
                exit(1);
            }
            if (empty($this->cr->cat_id)) {
                $this->dm_taskError("[updateTipoCache]: Не передан cat_id /item_id={$this->CIIR['item_id']}/");
                exit(1);
            }

            // текущий остаток
            $newr=[
                'sc' => $this->CIIR['sklad'],
                'price1' => $this->cr->price1,
                'price2' => $this->cr->price2,
                'price3' => $this->cr->price3
            ];


            if (isset($this->models[$this->cr->model_id]['T'][$this->cr->cat_id]['T'][$this->cr->suplr_id])) { // если есть в кеше запись с постащиком

                // если уже есть запись с этим поставщиком с маркером добавить в кеш - это значит дубль поставщика в файле для одного cat_id
                if ($this->models[$this->cr->model_id]['T'][$this->cr->cat_id]['T'][$this->cr->suplr_id]['a'] != 'a') {

                    // проверяем нет ли заявки на удаление поставщика
                    if (!in_array($this->cr->suplr_id, $this->opt['delSuplrs'])) {

                        if ($this->cr->price1 >= $this->opt['suplrsMinPrice'] || $this->cr->price2 >= $this->opt['suplrsMinPrice'] || $this->cr->price3 >= $this->opt['suplrsMinPrice']) {

                            $r = $this->models[$this->cr->model_id]['T'][$this->cr->cat_id]['T'][$this->cr->suplr_id];
                            unset($r['a'], $r['id']);
                            if (count(array_diff_assoc($r, $newr))) { // сравниваем цены и остаток с кешем, если не равно

                                // ставим метку модели - упоминание
                                $this->models[$this->cr->model_id]['a'] = 'u';
                                // ставим метку размеру - упоминание
                                $this->models[$this->cr->model_id]['T'][$this->cr->cat_id]['a'] = 'u';
                                // ставим метку остатку - обновлен
                                $newr['a'] = 'u';
                                $newr['id'] = $this->models[$this->cr->model_id]['T'][$this->cr->cat_id]['T'][$this->cr->suplr_id]['id'];
                                // помещаем в кеш
                                $this->models[$this->cr->model_id]['T'][$this->cr->cat_id]['T'][$this->cr->suplr_id] = $newr;
                            }
                            else { // сравниваем цены и остаток с кешем, если равно
                                // ставим метку модели - упоминание
                                if($this->models[$this->cr->model_id]['a'] != 'u') $this->models[$this->cr->model_id]['a'] = 'e';
                                // ставим метку модели - упоминание
                                if($this->models[$this->cr->model_id]['T'][$this->cr->cat_id]['a'] != 'u') $this->models[$this->cr->model_id]['T'][$this->cr->cat_id]['a'] = 'e';
                                // ставим метку остатку - упоминание
                                $this->models[$this->cr->model_id]['T'][$this->cr->cat_id]['T'][$this->cr->suplr_id]['a'] = 'e';
                            }
                        }
                        else {  // есть, но не проходит по цене
                            // ставим метку модели - обновлен
                            $this->models[$this->cr->model_id]['a'] = 'u';
                            // ставим метку модели - обновлен
                            $this->models[$this->cr->model_id]['T'][$this->cr->cat_id]['a'] = 'u';
                            // ставим метку остатку - удалить
                            $this->models[$this->cr->model_id]['T'][$this->cr->cat_id]['T'][$this->cr->suplr_id]['a'] = 'd';
                        }
                    }
                    else { // есть заявка на удаление поставщика
                        // ничего не делаем
                    }
                }
                else{
                    // дубль поставщика для этого cat_id
                    $this->cr->cstatus=$this->cr->mstatus=$this->cr->bstatus=3; // игнор
                    // TODO возможно стоит просумировать склад?
                }
            }
            else{ // если нет в кеше записи с поставщиком, добавляем в кеш
                if(!in_array($this->cr->suplr_id, $this->opt['delSuplrs'])) {
                    if($this->cr->price1 >= $this->opt['suplrsMinPrice'] || $this->cr->price2 >= $this->opt['suplrsMinPrice'] || $this->cr->price3 >= $this->opt['suplrsMinPrice']) {
                        // ставим метку модели - обновлен
                        $this->models[$this->cr->model_id]['a'] = 'u';
                        // ставим метку размеру - обновлен
                        $this->models[$this->cr->model_id]['T'][$this->cr->cat_id]['a'] = 'u';
                        // ставим метку остатку - добавлен
                        $newr['a'] = 'a';
                        $this->models[$this->cr->model_id]['T'][$this->cr->cat_id]['T'][$this->cr->suplr_id] = $newr;
                    }
                }
            }

        }
    }

    protected function S24_flushBrandCache()
    {
        /*
         * сбрасываем $this->models в базу
         *
         *  возможные метки 'a':
         *      у модели a,e  - БЫЛА ДОБАВЛЕНА, ВНУТРИ ЕСТЬ ОБНРОВЛЕННЫЕ РАЗМЕРЫ ИЛИ СКЛАД
         *      у размера a,e  - БЫЛ ДОБАВЛЕН, ВНУТРИ ЕСТЬ ОБНОВЛЕННЫЙ СКЛАД
         *      у склада a,d,u - ДОБАВИТЬ СТРОКУ, УДАЛИТЬ, ОБНОВИТЬ
         *      * Если у склада a==a, то sc_cat_id еще не присвоен, в кеше id стоит виртуальный v<число>    (catSCvid++)
         *
         *  TODO здесь же можно сдедать проверку на проходную цену для всех постащиков всех товаров
         */

        if($this->opt['mode']==1) return;
        $t=microtime(true);
        $cc=new CC_Ctrl;
        $scDeletes=[]; // список id для удаления из cc_cat_sc
        $scInserts=[]; // массив строк для добавления cc_cat_sc (cat_id,suplr_id,sc,dt_added, price1,price2,price3) VALUES implode(',', $scInserts)
        $scUpdates=[]; // массив для обновления cc_cat_sc
        $scUpdatesDT=[]; //  массив для обновления cc_cat_sc только дата
        $catUpdatesFull=[]; // массив для обновления цен и склада cc_cat
        $catUpdatesDT=[]; // массив для обновления только метки времени cc_cat
        $modelsUpdates=[]; // массив для обновления меток времени cc_model
        $dt=Tools::dt();

        // обходим дерево models
        if(!empty($this->models)) {

            foreach ($this->models as $mid => $mr) {

                foreach ($mr['T'] as $cid => $cr) {

                    $scSum = 0; // сумма склад. остатков по всем поставщикам
                    $price1 = $price2 = $price3 = $price2_all = array();  // массив цен всех поставщиков (с остатком >=4)
                    $exPrices = [];// массив цен с остатком из второй и третьей колонки >=1
                    // Выясняем, фиксированная ли цена
                    $tp_info_res = $cc->que('cat_by_id', $cid);
                    $tp_info = $cc->next();
                    // ***
                    if ($tp_info['gr'] == 1) {
                        $exc_suplr = explode(';', Data::get('cc_suplrs_exclude_s')); // Исключенные из имопрта поставщики (шины)
                        $p2_spec = (bool)Data::get('price2_spec_s'); // Ищем подходящую собственную розницу у поставщика, чтобы не влететь с минимальной розничной ценой
                    }else{
                        $exc_suplr = explode(';', Data::get('cc_suplrs_exclude_d')); // Исключенные из имопрта поставщики (диски)
                        $p2_spec = (bool)Data::get('price2_spec_d'); // Ищем подходящую собственную розницу у поставщика, чтобы не влететь с минимальной розничной ценой
                    }
                    foreach ($cr['T'] as $suplr_id => $scr) {
                        $scr['ignored'] = false;
                        $splr_info = $cc->getOne("SELECT `name` FROM `cc_suplr` WHERE suplr_id = '$suplr_id'", MYSQL_ASSOC);
                        // проверяем нет ли заявки на удаление поставщика, виртуальных строк здесь не может быть
                        if (in_array($suplr_id, $this->opt['delSuplrs'])) {
                            $scr['a'] = 'd';
                            $cr['a'] = 'u';
                            $mr['a'] = 'u';
                        }
                        // строка с остатком не упоминалась в файле ...
                        elseif ($scr['a'] == '') {
                            // поставщик есть в файле
                            if (isset($this->suplrsFromFile[$suplr_id]) || !$suplr_id) { // !$suplr_id - на всякий случай, если есть в базе строки suplr_id==0
                                $scr['a'] = 'd';
                                $cr['a'] = 'u';
                                $mr['a'] = 'u';
                            }
                            // поставщика нет в файле
                            else {
                                // полное обновление
                                if ($this->opt['mode'] == 3) {
                                   $scr['a'] = 'd';
                                    $cr['a'] = 'u';
                                    $mr['a'] = 'u';
                                }
                            }
                        }

                        // ценообразование
                        if($scr['a'] != 'd')
                        {
                            $scr['ignored'] = 0; // TODO: исправить обновление игнорирования поставщиков ($scr['a'] == 'e') для вывода в админке во всплывающей таблице с ценами
                            // только если какой то поставщик размера был обновлен/добавлен
                            $scSum += $scr['sc'];
                            if ($scr['sc'] >= 4)
                            {
                                $margins = $cc->getOne("SELECT * FROM cc_min_extra WHERE gr='{$tp_info['gr']}' AND pVal='".($tp_info['gr'] == 2 ? $tp_info['P5'] : $tp_info['P1'])."'", MYSQL_ASSOC);
                                if (!in_array($splr_info['name'], $exc_suplr)) {
                                    if ($scr['price1'] > $this->opt['suplrsMinPrice']) $price1[] = $scr['price1'];
                                    if ($scr['price2'] > $this->opt['suplrsMinPrice']) {
                                        $price2_all[] = $scr['price2'];
                                    }
                                    if (!empty($tp_info['extra_b'])){
                                        if ($scr['price2'] > $this->opt['suplrsMinPrice'] && ($scr['price1'] > $this->opt['suplrsMinPrice'] && $scr['price2'] >= ($scr['price1'] + ($scr['price1'] * ($tp_info['extra_b'] / 100))))) $price2[] = $scr['price2'];
                                    }
                                    else if ($scr['price2'] > $this->opt['suplrsMinPrice'] && ($scr['price1'] > $this->opt['suplrsMinPrice'] && $scr['price2'] >= ($scr['price1'] + $margins['extra']))) $price2[] = $scr['price2'];

                                    if ($scr['price3'] > $this->opt['suplrsMinPrice']) $price3[] = $scr['price3'];
                                }else {
                                    $cc->query("UPDATE cc_cat_sc SET ignored=1 WHERE suplr_id = '$suplr_id';");
                                    $scr['ignored'] = 1;
                                    $scr['a'] = 'u';
                                }
                            }

                            /*if($scr['sc'] > 0)
                            {
                                if($scr['price2'] > $this->opt['suplrsMinPrice']) $exPrices[]=$scr['price2'];
                                if($scr['price3'] > $this->opt['suplrsMinPrice']) $exPrices[]=$scr['price3'];
                            }*/
                        }

                        // формирование массивов для изменения БД
                        if ($scr['a'] == 'd') { // удалить строку
                            $scDeletes[] = $scr['id'];
                        }elseif ($scr['a'] == 'a' || empty($scr['id'])) { // добавленная строка
                            $scInserts[] = "('{$cid}','{$suplr_id}','{$scr['sc']}','{$dt}','{$scr['price1']}','{$scr['price2']}','{$scr['price3']}','{$scr['ignored']}')";
                        }elseif ($scr['a'] == 'u') { // обновленная строка
                            $scUpdates[] = "('{$scr['id']}','{$scr['sc']}','{$dt}','{$scr['price1']}','{$scr['price2']}','{$scr['price3']}','{$scr['ignored']}')";
                        }elseif ($scr['a'] == 'e') { // обновленная строка
                            $scUpdatesDT[] = $scr['id'];
                        }

                    } // конец цикла по поставщикам

                    if (true)
                    { // только если какой то поставщик размера был обновлен
                        if(!empty($exPrices))
                        {
                            $cprice = min($exPrices);
                            $bprice = 0;
                            //$fixPrice = 1;
                        }
                        else
                        {
                            // опт
                            if (!empty($price1))
                            {
                                $mp1 = (float)@min($price1);
                            }
                            else
                            {
                                $mp1 = 0;
                            }

                            // рек розница
                            if (!empty($price2))
                            {
                                $mp2 = (float)@min($price2);
                            }
                            elseif($p2_spec && empty($price2) && !empty($price2_all) && min($price2_all) > 0 && $mp1 > 0){ // Когда price2 не прошла по наценке, но может оказаться дороже, чем price1 с наценкой
                                if ($tp_info['gr'] == 1)
                                    $cp = $cc->extra_price($mp1, $tp_info['cur_id'], $tp_info['P1'], $suplr_id, $tp_info['brand_id'], $tp_info['extra_b'], 1, $tp_info['MP1']);
                                else
                                    $cp = $cc->extra_price($mp1, $tp_info['cur_id'], $tp_info['P5'], $suplr_id, $tp_info['brand_id'], $tp_info['extra_b'], 2);
                                if ($cp < min($price2_all)){
                                    $mp2 = (float)@min($price2_all);
                                }elseif(!empty($cp)){
                                    $mp2 = $cp;
                                }
                            }
                            else
                            {
                                $mp2 = 0;
                            }

                            // собств розница, здесь только Яршинторг может быть
                            if (!empty($price3))
                            {
                                $mp3 = (float)@min($price3);
                            }
                            else
                            {
                                $mp3 = 0;
                            }
                            // Финальная цена
                            if ($mp3 != 0)
                            {
                                $bprice = $mp1;
                                $cprice = $mp3;
                                $recomend = 1;
                            }
                            elseif ($mp2 != 0)
                            {
                                $bprice = $mp1;
                                $cprice = $mp2;
                                $recomend = 1;
                            }
                            else
                            {
                                $bprice = $mp1;
                                if ($bprice > 0) {
                                    if ($tp_info['gr'] == 1)
                                        $cprice = $cc->extra_price($bprice, $tp_info['cur_id'], $tp_info['P1'], $suplr_id, $tp_info['brand_id'], $tp_info['extra_b'], 1, $tp_info['MP1']);
                                    else
                                        $cprice = $cc->extra_price($bprice, $tp_info['cur_id'], $tp_info['P5'], $suplr_id, $tp_info['brand_id'], $tp_info['extra_b'], 2);
                                }else $cprice = 0;
                                //$cprice = $cr['cp'];
                                $recomend = 0;
                            }
                        }
                        // формируем строку для обновления типоразмера TODO: условия для обновления фикс цены и кол-ва
                        if ($tp_info['fixPrice'] == 1 && $tp_info['fixSc'] == 1){
                            $catUpdatesFull[] = "('{$cid}','{$dt}','{$bprice}','{$tp_info['cprice']}','1','1','$recomend','{$tp_info['sc']}')";
                        }
                        elseif ($tp_info['fixSc'] == 1 && $tp_info['fixPrice'] != 1){
                            $catUpdatesFull[] = "('{$cid}','{$dt}','{$bprice}','{$cprice}','0','1','$recomend','{$tp_info['sc']}')";
                        }
                        elseif ($tp_info['fixSc'] != 1 && $tp_info['fixPrice'] == 1){
                            $catUpdatesFull[] = "('{$cid}','{$dt}','{$bprice}','{$tp_info['cprice']}','1','0','$recomend','{$scSum}')";
                        }
                        else $catUpdatesFull[] = "('{$cid}','{$dt}','{$bprice}','{$cprice}','0','0','$recomend','$scSum')";
                    } // если было упоминание по складу, но изменнеий небыло
                    elseif ($cr['a'] == 'e') {
                        // формируем строку для обновления только времени обновления типоразмера
                        //$catUpdatesDT[] = $cid;
                    }
                    unset($DB);
                }

                if ($mr['a'] == 'e' || $mr['a'] == 'u') {
                    // формируем строку для обновления только времени обновления модели
                    $modelsUpdates[] = "('$mid','$dt')";
                }
            }

            /*
             * INSERT INTO table (id,Col1,Col2) VALUES (1,1,1),(2,2,3),(3,9,3),(4,10,12) ON DUPLICATE KEY UPDATE Col1=VALUES(Col1),Col2=VALUES(Col2);
             */

            // сбрасываем добавленные и удаленные строки в БД

            // удаление строк cc_cat_sc
            $t1 = microtime(true);
            $t1u = 0;
            if (!empty($scDeletes)) {
                // удаляем все за раз
                $this->query("DELETE FROM cc_cat_sc WHERE cat_sc_id IN(" . implode(',', $scDeletes) . ")");
                $this->deletedSCCounter += $t1u = $this->updatedNum();
            }
            $t1 = $this->timerEnd($t1);

            // добавление строк cc_cat_sc
            $t2 = microtime(true);
            $t2u = 0;
            if (!empty($scInserts)) {
                if (count($scInserts) <= 5000) {
                    $this->query("INSERT INTO cc_cat_sc (cat_id,suplr_id, sc, dt_added, price1, price2, price3, ignored) VALUES " . implode(',', $scInserts));
                    $this->insertedSCCounter += $t2u = $this->updatedNum();
                }
                else {
                    while (!empty($scInserts)) {
                        $slice = [];
                        // выбираем по 5000 строк
                        $i=0;
                        do {
                            $slice[] = array_shift($scInserts);
                        } while (++$i<5000 && count($scInserts));
                        $this->query("INSERT INTO cc_cat_sc (cat_id, suplr_id, sc, dt_added, price1, price2, price3, ignored) VALUES " . implode(',', $slice));
                        $t2u = $u = $this->updatedNum();
                        $this->insertedSCCounter += $u;
                    }
                }
            }
            $t2 = $this->timerEnd($t2);

            // обновление строк cc_cat_sc
            $t3 = microtime(true);
            $t3u = 0;
            if (!empty($scUpdates)) {
                if (count($scUpdates) <= 5000) {
                    $this->query("INSERT INTO cc_cat_sc (cat_sc_id, sc, dt_upd, price1, price2, price3, ignored) VALUES " . implode(',', $scUpdates) . " ON DUPLICATE KEY UPDATE sc=VALUES(sc), dt_upd=VALUES(dt_upd), price1=VALUES(price1), price2=VALUES(price2), price3=VALUES(price3), ignored=VALUES(ignored)");
                    $this->updatedSCCounter += $t3u = $this->updatedNum() /2 ;
                }
                else {
                    while (!empty($scUpdates)) {
                        $slice = [];
                        // выбираем по 5000 строк
                        $i=0;
                        do {
                            $slice[] = array_shift($scUpdates);
                        } while (++$i<5000 && count($scUpdates));
                        $this->query("INSERT INTO cc_cat_sc (cat_sc_id, sc, dt_upd, price1, price2, price3, ignored) VALUES " . implode(',', $slice) . " ON DUPLICATE KEY UPDATE sc=VALUES(sc), dt_upd=VALUES(dt_upd), price1=VALUES(price1), price2=VALUES(price2), price3=VALUES(price3), ignored=VALUES(ignored)");
                        $t3u = $u = $this->updatedNum() / 2;
                        $this->updatedSCCounter += $u;
                    }
                }
            }
            if (!empty($scUpdatesDT)) {
                if (count($scUpdatesDT) <= 5000) {
                    $ids=implode(',',$scUpdatesDT);
                    $this->query("UPDATE cc_cat_sc SET dt_upd='$dt' WHERE cat_sc_id IN ($ids)");
                    $this->updatedSCCounter += $t3u = $this->updatedNum();
                }
                else {
                    while (!empty($scUpdatesDT)) {
                        $slice = [];
                        // выбираем по 5000 строк
                        $i=0;
                        do {
                            $slice[] = array_shift($scUpdatesDT);
                        } while (++$i<5000 && count($scUpdatesDT));
                        $ids=implode(',', $slice);
                        $this->query("UPDATE cc_cat_sc SET dt_upd='$dt' WHERE cat_sc_id IN ($ids)");
                        $t3u += $u = $this->updatedNum();
                        $this->updatedSCCounter += $u;
                    }
                }
            }
            $t3 = $this->timerEnd($t3);

            // полное обновление строк cc_cat
            $t4 = microtime(true);
            $t4u = 0;
            if (!empty($catUpdatesFull)) {
                if (count($catUpdatesFull) <= 5000) {
                    $this->query("INSERT INTO cc_cat (cat_id, dt_upd, bprice, cprice, fixPrice, fixSc, recomend, sc) VALUES " . implode(',', $catUpdatesFull) . " ON DUPLICATE KEY UPDATE dt_upd=VALUES(dt_upd), bprice=VALUES(bprice), cprice=VALUES(cprice), fixPrice=VALUES(fixPrice), fixSc=VALUES(fixSc), recomend=VALUES(recomend), sc=VALUES(sc)");
                    $this->updatedCatsCounter += $t4u = $this->updatedNum() / 2;
                }
                else {
                    while (!empty($catUpdatesFull)) {
                        $slice = [];
                        // выбираем по 5000 строк
                        $i=0;
                        do {
                            $slice[] = array_shift($catUpdatesFull);
                        } while (++$i<5000 && count($catUpdatesFull));
                        $this->query("INSERT INTO cc_cat (cat_id, dt_upd, bprice, cprice, fixPrice, fixSc, recomend, sc) VALUES " . implode(',', $slice) . " ON DUPLICATE KEY UPDATE dt_upd=VALUES(dt_upd), bprice=VALUES(bprice), cprice=VALUES(cprice), fixPrice=VALUES(fixPrice), fixSc=VALUES(fixSc), recomend=VALUES(recomend), sc=VALUES(sc)");
                        $t4u += $u = $this->updatedNum() / 2;
                        $this->updatedCatsCounter += $u;
                    }
                }
            }

            // обновление только времени cc_cat
            if (!empty($catUpdatesDT)) {
                if (count($catUpdatesDT) <= 5000) {
                    $ids=implode(',',$scUpdatesDT);
                    $this->query("UPDATE cc_cat SET dt_upd='$dt' WHERE cat_id IN ($ids)");
                    $t4u += $u = $this->updatedNum();
                    $this->updatedCatsCounter += $u;
                }
                else {
                    while (!empty($catUpdatesDT)) {
                        $slice = [];
                        // выбираем по 5000 строк
                        $i=0;
                        do {
                            $slice[] = array_shift($catUpdatesDT);
                        } while (++$i<5000 && count($catUpdatesDT));
                        $ids=implode(',', $slice);
                        $this->query("UPDATE cc_cat SET dt_upd='$dt' WHERE cat_id IN ($ids)");
                        $t4u += $u = $this->updatedNum();
                        $this->updatedCatsCounter += $u;
                    }
                }
            }
            $t4 = $this->timerEnd($t4);

            // обновление только времени cc_model
            $t5 = microtime(true);
            $t5u = 0;
            if (!empty($modelsUpdates)) {
                if (count($modelsUpdates) <= 5000) {
                    $this->query("INSERT INTO cc_model (model_id,dt_upd) VALUES " . implode(',', $modelsUpdates) . " ON DUPLICATE KEY UPDATE dt_upd=VALUES(dt_upd)");
                    $this->updatedModelsCounter += $t5u = $this->updatedNum() / 2;
                }
                else {
                    while (!empty($modelsUpdates)) {
                        $slice = [];
                        // выбираем по 5000 строк
                        $i=0;
                        do {
                            $slice[] = array_shift($modelsUpdates);
                        } while (++$i<5000 && count($modelsUpdates));
                        $this->query("INSERT INTO cc_model (model_id, dt_upd) VALUES " . implode(',', $slice) . " ON DUPLICATE KEY UPDATE dt_upd=VALUES(dt_upd)");
                        $t5u += $u = $this->updatedNum() / 2;
                        $this->updatedModelsCounter += $u;
                    }
                }
            }
            $t5 = $this->timerEnd($t5);

            $t = $this->timerEnd($t);
            $this->dm_taskLog("[flushBrandCache]: {$this->cachedBrandName } обновлен ($t sec)");
            echo "[flushBrandCache]: DB n/secs: cc_model(u) $t5u/$t5, cc_cat(u) $t4u/$t4, cat_sc(u) $t3u/$t3, cat_sc(ins) $t2u/$t2, cat_sc(del) $t1u/$t1\n";
        }

        $this->models=[];

    }

    private function getBrandInSuffixArr()
    {
        // ищем код бренда в exSuffixes и заносим его в tSuffixesBrandKey
        $this->cr->tSuffixesBrandKey = 0;
        foreach ($this->exSuffixes as $k => $v) {
            if (Tools::mb_strcasecmp($k, $this->cr->bname) === 0) {
                $this->cr->tSuffixesBrandKey = $k;
                break;
            }
        }
    }

    private function splitTSuffix($s)
    {
        $suffixArr = array();
        $s = ' ' . $s . ' ';
        if (!empty($this->exSuffixes[$this->cr->tSuffixesBrandKey])) do {
            $s0 = $s;
            //ищем суффикс сначала для бренда
            foreach ($this->exSuffixes[$this->cr->tSuffixesBrandKey] as $v) {
                $s2 = str_ireplace(" $v ", ' ', $s);
                if (Tools::mb_strcasecmp($s, $s2) != 0) $suffixArr[] = $v;
                $s = $s2;
            }
        } while ($s != $s0);
        if (!empty($this->exSuffixes[0])) do {
            $s0 = $s;
            //потом глобальный
            foreach ($this->exSuffixes[0] as $v) {
                $s2 = str_ireplace(" $v ", ' ', $s);
                if (Tools::mb_strcasecmp($s, $s2) != 0) $suffixArr[] = $v;
                $s = $s2;
            }
        } while ($s != $s0);
        $s = trim($s);
        if ($s != '') $suffixArr = array_unique(array_merge($suffixArr, explode(' ', trim($s))));

        return $suffixArr;
    }


    private function getDiam($p3)
    {
        // запишем в размер DIA не из ТИ а из матрицы Dia
        if (isset($this->opt['diaMerge'][$p3])) {
            $dia = $this->opt['diaMerge'][$p3];
            $this->cr->transform[":Dia {$dia}:"] = 1;
        } else {
            $dia = $p3;
        }

        return $dia;
    }

    private function getSV($p4, $p6)
    {
        // запишем в размер сверловку не из ТИ а из матрицы сверловок
        if (isset($this->opt['svMerge'][$p4 . '*' . $p6])) {
            list($lz, $pcd) = $this->opt['svMerge'][$p4 . '*' . $p6];
            $this->cr->transform[":SV {$lz}*{$pcd}:"] = 1;
        } else {
            list($lz, $pcd) = array($p4, $p6);
        }

        return array($lz, $pcd);
    }

    /*
     * формирование цвета диска для дабавления нового размера в БДС
     */
    private function makeDiskSuffix()
    {

        if ($this->cr->suffix != '') {
            // добавляем размер с суффиксом со вторым  значением из матрицы цветов
            if (!empty($this->cr->iSuffixes[1])) {
                $suf = $this->cr->iSuffixes[1];
                $this->cr->transform[$this->cr->iSuffixes[1]] = 1;
            } // или с пустым значением, если суффикс в игнорируемых
            elseif (!empty($this->ignoreDiskSuffixes) && Tools::mb_array_search($this->cr->suffix, $this->ignoreDiskSuffixes) !== false) {
                $suf = '';
                $this->cr->transform[':IDS:'] = 1;
            } else {
                $suf = $this->cr->suffix;
                // проверяем на вхождение суффикса в базовых суффиксах, но без значения в матрице
                //if(Tools::mb_array_search($this->suffix,$this->baseSuffixes)!==false) $this->transform[':INBASE:']=1; else $this->transform[':NOBASE']=1;
            }
        } else $suf = '';

        return $suf;
    }

    // сравнение массивов по значениям - регисрозависимо, сравнение ключей не производится(важно-здесь был баг)
    // возвращает 1 - если равны
    function arrayCompare($a1, $a2)
    {
        if (count($a1) != count($a2)) return 0;
        foreach ($a1 as $v) {
            if (($kk = Tools::mb_array_search($v, $a2)) !== false) unset($a2[$kk]);
        }
        if (count($a2)) return 0; else return 1;
    }




    /* daemon control   */

    function dm_setState($state)
    {
        $d = ['state' => $state];
        return $this->dm_updateInfo($d);
    }

    function dm_pause($pause)
    {
        return $this->dm_updateInfo(['paused' => $pause]);
    }

    function dm_updateInfo($info = [], $newInstance=false)
    {
        $_daemon = [
            'pid'      => getmypid(),
            'mem'      => memory_get_usage(true),
            'mem_peak' => memory_get_peak_usage(true),
            //'memc'     => Tools::memoryGetUsage(),
            'ts'=>time()
        ];

        $d = MC::sget('ciidm.daemon');
        if ($d === false || !is_array($d) || $newInstance) {
            $d = array_merge($_daemon, [
                'state'      => 'waiting',
                'paused'     => false,
                'ts_started' => time(),
                'ts'=>time()
            ], $info);
        } else
            $d = array_merge($d, $_daemon, $info);

        MC::sset('ciidm.daemon', $d);

        return $d;
    }

    function dm_newTask($opt = [])
    {
        $_config = $this->getConfig();
        $opt = array_merge($_config, $opt);

        $task = [
            'state' => 'new',
            'pid' => getmypid(),
            'ts_added' => time(),
            'cUserId'  => CU::$userId,
            'opt'   => $opt
        ];

        MC::sset('ciidm.task', $task);

        $this->dm_log('Новая задача установлена');

        return $task;
    }

    function dm_modTask($d = [])
    {
        $task = MC::sget('ciidm.task');
        if (false === $task) return false;
        if (!empty($d)) {
            $task = array_merge($task, $d, ['ts'=>time()]);
            MC::sset('ciidm.task', $task);
        }

        return $task;
    }

    function dm_taskError($msg)
    {
        $this->dm_modTask(['state' => 'error', 'ts_finished' => time()]);
        $this->dm_taskLog($msg);

        return false;
    }

    function dm_clearLog()
    {
        MC::sdel('ciidm.log');
    }

    function dm_clearTaskLog()
    {
        MC::sdel('ciidm.taskLog');
    }

    /*
     * лог демона
     */
    function dm_log($msg)
    {
        $d = MC::sget('ciidm.log');
        if (false === $d || !is_array($d)) $d = [];

        $d[$ts=microtime(true).''] = $msg;
        MC::sset('ciidm.log', $d);
        echo 'dm_log: '.Tools::dt($ts).': '.$msg."\n";
    }

    /*
     * лог задачи
     */
    function dm_taskLog($msg)
    {
        $d = MC::sget('ciidm.taskLog');
        if (false === $d || !is_array($d)) $d = [];

        $d[$ts=microtime(true).''] = $msg;
        MC::sset('ciidm.taskLog', $d);
        echo 'dm_taskLog: '.Tools::dt($ts).': '.$msg."\n";
    }

    /*
     * очередь команд в MC[ciidm.cmd]
     * выполнение команд демона и функция паузы выполнения демона. Пауза зацикливается в этом методе
     * возврает false если задача должна быть остановлена
    */
    function dm_cmd()
    {
        $paused = false;
        $res=true;
        do {
            $cmd = MC::sget('ciidm.cmd');
            if (!empty($cmd) && is_array($cmd)) {
                foreach ($cmd as $cmdi) {
                    switch ($cmdi) {
                        case 'stop':
                            $this->dm_log('Command received: daemon stop');
                            $this->dm_setState('stopped');
                            MC::sdel('ciidm.cmd');
                            exit(101);
                        case 'pause':
                            // в паузе updateInfo не происходит
                            $this->dm_log('Command received: daemon pause');
                            $this->dm_pause(true);
                            $paused = true;
                            break;
                        case 'resume':
                            $this->dm_log('Command received: daemon resume');
                            $this->dm_pause(false);
                            $paused = false;
                            break;
                        case 'task_break':
                            $this->dm_log('Command received: task_break');
                            $paused=false;
                            $this->dm_pause(false);
                            $res=false;
                    }
                }
            }

            MC::sdel('ciidm.cmd');
            if ($paused) sleep(1);

        } while ($paused);
        return $res;
    }

    function DM_pushCmd($cmd)
    {
        if(empty($cmd)) return false;
        $cmds = MC::sget('ciidm.cmd');
        $cmds[]=$cmd;
        MC::sset('ciidm.cmd',$cmds);
        return $cmds;
    }


    function DM_currentTask()
    {
        return MC::sget('ciidm.task');
    }


    /*
     * тестирование
     */
    function test()
    {
        $daemon = $this->dm_updateInfo();
        $this->dm_modTask(['state' => 'exec', 'ts_run' => time(), 'pid' => $daemon['pid']]);

        $this->dm_taskLog("[test]: Запуск ".mt_rand());

        $qri=0;
        $qmax=3000;

        do {
            $qri++;
            //срабатывает на каждой 10той строке
            if (($qri / 50) == ceil($qri / 50)) {
                $this->dm_updateInfo();
                $this->dm_taskLog("[test]: обработано $qri");
                // посылаем данные в МС для отображения в статусном окне
                $this->dm_modTask([
                    'qri' => $qri,
                    'pg_index' => ceil($qri * 100 / $qmax),
                    'pg_label' => 'Обработка qri=' . $qri
                ]);
                if (!$this->dm_cmd()) {
                    // если полукчена команда task_break - остановка задачи
                    $this->dm_modTask(['state' => 'interrupted', 'ts_finish' => time()]);
                    $this->dm_taskLog('[test]: task.state=interrupted - задача прервана оператором.');
                    return true;
                }
            }

            usleep(5000);

        }while($qri<$qmax);

        $this->dm_taskLog("[test.final]: Обработка завершена.");

        $this->dm_updateInfo();
        $this->dm_modTask([
            'ts_finish'=>time(),
            'ts'=>time(),
            'state'=>'finished',
            'pg_label'=>'Обработка завершена'
        ]);
    }


    public function dm_getState($lastTS)
    {
        $r=(object)[];
        $r->daemon=MC::sget('ciidm.daemon');
        $r->daemon['dt_started']=date("d.m.Y H:i:s", @$r->daemon['ts_started']);
        $r->daemon['mem_']=Tools::memSizeConvert(@$r->daemon['mem']);
        if(isset($r->daemon['memc'])) $r->daemon['memc_']=Tools::memSizeConvert(@$r->daemon['memc']);
        $r->daemon['tdiff']=(time()-@$r->daemon['ts']).'s';
        $r->daemon['mem_peak_']=Tools::memSizeConvert(@$r->daemon['mem_peak']);

        $r->task=MC::sget('ciidm.task');
        if($r->task){
            if(!empty($r->task['ts_run'])) {
                $r->task['dt_run']=date("d.m.Y H:i:s", $r->task['ts_run']);
                $r->task['elapsed']=$r->task['ts']-$r->task['ts_run'];
                $r->task['elapsed_']=Tools::formatSeconds($r->task['elapsed']);
            }
        }

        // сводный лог: сумма двух логов
        $dlog=MC::sget('ciidm.log');
        $tlog=MC::sget('ciidm.taskLog');
        $r->log=$log=[];
        if(is_array($tlog)) $log=$tlog;
        if(is_array($dlog)) foreach($dlog as $k=>$v) $log[$k]=$v;
        ksort($log, SORT_NUMERIC);

        $r->TS=microtime(true);

       if(empty($lastTS)) $lastTS=0;
       foreach($log as $k=>$v) if($k>$lastTS) $r->log[]=[$k,$v];

        return $r;
    }


    protected function timerEnd($timerStarted)
    {
        return ceil((microtime(true)-$timerStarted)*1000)/1000;
    }


    function __construct()
    {
        parent::__construct();
        $this->cc = new CC_Ctrl();
    }


}