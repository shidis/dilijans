<?
class App_AjaxPodbor_Controller extends App_Common_Controller
{
    private
    $valid_radiuses = array(),
    // главные параметры
    $P1=array(), // GET['p1'] || route['P1'] as float    ET
    $P2=array(), // GET['p2'] || route['P2'] as float   J
    $P3=array(), // GET['p3'] || route['P3'] as float    DIA
    $P4=array(), // GET['p4'] || route['P4'] as decimal   PCD
    $P5=array(), // GET['p5'] || route['P5'] as float    RADIUS
    $P6=array(), // GET['p6'] || route['P6'] as float   DCO
    $P46=array(), // GET['sv']  сверловка   SV
    $brands=array(), // GET['vendor']
    $replica, // _GET['replica']=1 || route['replica']=1
    // главные спарочные
    $sP1=array(), // GET['p1_']
    $sP2=array(), // GET['p2_']
    $sP3=array(), // GET['p3_']
    $sP4=array(), // GET['p4_']
    $sP5=array(), // GET['p5_']
    $sP6=array(), // GET['p6_']
    $sMode=false, // ==true - спарка-режим

    // уточняющие параметры
    $_P1=array(), // GET['_p1']
    $_P2=array(), // GET['_p2']
    $_P3=array(), // GET['_p3']
    $_P4=array(), // GET['_p4']
    $_P5=array(), // GET['_p5']
    $_P6=array(), // GET['_p6']
    $_P46=array(), // GET['_sv']
    $_brands=array(), // GET['_bids']
    // сумма параметров
    $P1_=array(),
    $P2_=array(),
    $P3_=array(),
    $P4_=array(),
    $P5_=array(),
    $P6_=array(),
    $P46_=array(),
    $brands_=array();

    private
    $apMode;  // _GET['ap']==1  включает delta_et  и delta_dia
    private function _initLists()
    {
        $this->mark = (@$_REQUEST['mark']);
        $this->model = (@$_REQUEST['model']);
        $this->year = (@$_REQUEST['year']);
        $this->modif = (@$_REQUEST['modif']);

        $this->ab = new CC_AB();

        $this->ab->getTree(array('svendor' => $this->mark, 'smodel' => $this->model, 'syear' => $this->year, 'smodif' => $this->modif));
    }

    public function getModels()
    {
        $this->_initLists();

        $this->r['s'] = '<option value="">' . (count(@$this->ab->tree['vendors']) ? 'выбрать модель' : 'нет моделей') . '</option>';
        if (count(@$this->ab->tree['models']))
            foreach ($this->ab->tree['models'] as $k => $v)
                $this->r['s'] .= "<option" . (@$this->abCookie['smodel'] == $v['sname'] ? ' selected' : '') . " value=\"{$v['sname']}\">" . Tools::html($v['name']) . '</option>';
    }

    public function getYears()
    {
        $this->_initLists();

        $this->r['s'] = '<option value="">' . (count(@$this->ab->tree['years']) ? 'выбрать год выпуска' : 'нет вариантов') . '</option>';
        if (count(@$this->ab->tree['years']))
            foreach ($this->ab->tree['years'] as $k => $v)
                $this->r['s'] .= "<option" . (@$this->abCookie['ab_year'] == $v['sname'] ? ' selected' : '') . " value=\"{$v['sname']}\">" . Tools::html($v['name']) . '</option>';
    }

    public function getModifs()
    {
        $this->_initLists();

        $this->r['s'] = '<option value="">' . (count(@$this->ab->tree['modifs']) ? 'выбрать двигатель' : 'нет вариантов') . '</option>';
        if (count(@$this->ab->tree['modifs']))
            foreach ($this->ab->tree['modifs'] as $k => $v)
                $this->r['s'] .= "<option" . (@$this->abCookie['ab_modif'] == $v['sname'] ? ' selected' : '') . " value=\"{$v['sname']}\">" . Tools::html($v['name']) . '</option>';
    }   
}