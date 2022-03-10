<?
trait App_Import_Common
{
    protected function defConfig()
    {
        $d = array();
        $d['maxFileList'] = '10';
        $d['DM_rowsLimitByIter'] = '2000';
        $d['suplrsMinPrice']=500;
        $d['diaMerge'] = array();
        $d['svMerge'] = array();
        return $d;
    }

    public function getConfig()
    {
        $d=$this->defConfig();
        $dd = Data::get('cii_config');
        if (mb_strpos($dd, ':') !== false) {
            $dd = unserialize($dd);
            $d = array_merge($d, $dd);
        } elseif ($dd != '') {
            $dd = Tools::DB_unserialize($dd);
            $d = array_merge($d, $dd);
        } else {
            Data::set('cii_config', Tools::DB_serialize($d));
        }
        return $d;
    }

    public function setConfig($data)
    {
        if(!is_array($data)) return false;
        $cfg=$this->getConfig();
        $cfg = array_merge($cfg, $data);
        if(isset($data['cc_runflat_suffix'])){
            Data::set('cc_runflat_suffix', $data['cc_runflat_suffix']);
        }
        unset($cfg['cc_runflat_suffix']);
        unset($cfg['notResetCBprice']);
        unset($cfg['delSuplrs']);
        unset($cfg['brands']);
        unset($cfg['resetOutOfStockGlobal']);
        unset($cfg['bExtras']);
        unset($cfg['replicaBrand']);
        unset($cfg['showTiposOnStock']);
        unset($cfg['ignoreZeroPrice']);
        Data::set('cii_config', Tools::DB_serialize($cfg));
    }


}