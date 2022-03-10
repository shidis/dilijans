<?
error_reporting(E_ALL | E_STRICT);
class App_Entry_Controller extends App_Common_Controller
{


    public function item() {

        $this->template('entryPage');


        $this->entry=new Entry();

        if(@Url::$spath[2]=='') return App_Route::redir404();
        $this->entry->que('entry_by_sname',Url::$spath[2], "published=1 AND (dt_published = '0000-00-00' OR dt_published <= '".date('Y-m-d')."')");
        if(!$this->entry->qrow['entry_id']) {
            return App_Route::redir404();
        }
        $this->_title=Tools::unesc($this->entry->qrow['title']);
        $this->title=Tools::unesc($this->entry->qrow['seo_title']);
        $this->content=$this->ss->parseText(Tools::unesc($this->entry->qrow['text']));
        $this->img1 = !empty($this->entry->qrow['img1']) ? $this->ss->makeImgPath($this->entry->qrow['img1']) : '';
        $this->description=Tools::html($this->entry->qrow['description']);
        $this->keywords=Tools::html($this->entry->qrow['keywords']);
        $this->dateSys=($this->entry->qrow['dt']);
        $d=Tools::sdate($this->entry->qrow['dt'],'-');
        $this->date=$d;

        $this->allItemsUrl='/'.App_Route::_getUrl('entrysection').'.html';

        $this->breadcrumbs['Новости']=$this->allItemsUrl;

        if(!empty($this->entry->qrow['entry_section_id'])) {
            $this->entrysection = new Entrysection();
            $this->entryAlltList = new Entry();
            $this->entrysection->que('entry_section_by_id',$this->entry->qrow['entry_section_id'],"published=1",1);

            if(!empty($this->entrysection->qrow['title'])) {
                $this->breadcrumbs[$this->entrysection->qrow['title']] = '/'.App_Route::_getUrl('entrysection').'/'.$this->entrysection->qrow['sname'].'.html';
            }

            // Другие публикации
            $this->entryAlltList->que('entry_list_by_section_id', "entry_section_id=" . $this->entry->qrow['entry_section_id'], "entry_id != ". $this->entry->qrow['entry_id']." AND published=1 AND (dt_published = '0000-00-00' OR dt_published <= '".date('Y-m-d')."')" , 4);
            $den=$this->entryAlltList->fetchAll();
            $this->entryList=array();
            $this->entryAlltListCount=count($den);
            foreach($den as $v){
                $this->entryList[]=array(
                    'url'=>'/'.App_Route::_getUrl('entry').'/'.$v['sname'].'.html',
                    'title'=>Tools::unesc($v['title']),
                    'intro'=>Tools::unesc($v['intro']),
                    'text'=>$v['text'],
                    'img1'=> !empty($v['img1']) ? $this->ss->makeImgPath($v['img1']) : '',
                    'published'=> !empty($v['published']) ? $v['published'] : ''

                );
            }
        }

        $this->breadcrumbs[]=$this->title;

        // Карточки моделей
        $this->modelsForToken = array();
        $this->modelsForToken = Tools::goodsTokensToArray($this->ss->parseText(Tools::unesc($this->entry->qrow['text'])));

        if(!empty($this->modelsForToken)) {
            $arTokest = $this->modelsForToken;

            foreach ($arTokest as $id => $arToken) {

                if(!empty($arToken['value'])) {
                    $this->modelLoop($id, $arToken);
                }
            }


        }


        $this->view('entry/item');
    }

    private function modelLoop($id, $arToken) {
        $cc = new CC_Base();

        foreach ($arToken['value'] as $vid => $arItem) {
            $item_id = $arItem['value'];
            $item_type = $arItem['type'];
            $arFields = array();
            $arFields['type'] = $item_type;			
            if($item_type == 'm') {
                // Модели
                $cc->que('model_by_id', (int)$item_id);
                $cc->next();
                //$this->modelsForToken[$id]['value'][$vid] = $cc->qrow;

				
                $arFields['gr'] = $cc->qrow['gr'];

                if ($cc->qrow['gr'] == 1) {
                    $burl = '/' . App_Route::_getUrl('tModel') . '/';
                    // Поля для шин
                    $arFields['bname'] = Tools::unesc($cc->qrow['name']);
                    $arFields['brand_id'] = $cc->qrow['brand_id'];

                    $arFields['url'] = $burl . $cc->qrow['sname'] . '.html';
                    $arFields['model_fields'] = array();

                    $arFields['model_fields'] = $this->_tireModel($cc->qrow['model_id'], $arFields);

                    $arFields['img'] = "";
                    $arFields['name'] = "";
                    $arFields['anc'] = "";
                    $arFields['alt'] = "";


                    if (!empty($arFields['model_fields'])) {
                        if (!empty($arFields['model_fields']['img1'])) {
                            $arFields['img'] = $cc->makeImgPath($arFields['model_fields']['img1']);
                        }
                        if (!empty($arFields['model_fields']['name'])) {
                            $mname = Tools::unesc($arFields['model_fields']['name']);
                        }
                        if (!empty($arFields['model_fields']['bname']) && !empty($mname)) {
                            $arFields['anc'] = "{$arFields['model_fields']['bname']} {$mname}";
                            if (!empty($arFields['model_fields']['suffix'])) {
                                $arFields['anc'] .= ' ' . $arFields['model_fields']['suffix'];
                            }
                        }
                        if (!empty($arFields['anc'])) {
                            $malt = Tools::unesc($arFields['model_fields']['alt'] != '' ? $arFields['model_fields']['alt'] : $arFields['anc']);
                            $malt1 = $this->firstS($malt);
                            $arFields['alt'] = "резина {$this->balt} {$malt1}";
                        }

                    }

                } elseif ($cc->qrow['gr'] == 2) {
                    // Поля для дисков
                    $burl = '/' . App_Route::_getUrl('dModel') . '/';

                    $arFields['brand_id'] = $cc->qrow['brand_id'];
                    $arFields['model_fields'] = $this->_diskModel($cc->qrow['model_id'], $arFields);
                    $arFields['bname'] = Tools::unesc($cc->qrow['name']);
                    $arFields['brand_sname'] = $cc->qrow['sname'];
                    $arFields['balt'] = Tools::unesc($arFields['model_fields']['alt'] != '' ? $arFields['model_fields']['alt'] : $arFields['model_fields']['name']);

                    $malt = Tools::unesc($arFields['model_fields']['alt'] != '' ? $arFields['model_fields']['alt'] : $arFields['model_fields']['name']);
                    $malt1 = $this->firstS($malt);
                    $arFields['alt'] = "диски {$this->balt} {$malt1}";

                    $arFields['url'] = $burl . $cc->qrow['sname'] . '.html';

                }

            } elseif($item_type == 't') {
                // Типоразмеры
                $cc->que('cat_by_id', (int)$item_id);
                $cc->next();
                $arFields['gr']=!empty($cc->qrow['gr']) ? $cc->qrow['gr'] : '';;


                if ($cc->qrow['gr'] == 1) {
                    // Поля для типоразмеров шин
                    $arFields['bname']=Tools::unesc($cc->qrow['bname']);
                    $arFields['brand_sname']=$cc->qrow['brand_sname'];
                    $arFields['model_sname']=$cc->qrow['model_sname'];
                    $arFields['brand_id']=$cc->qrow['brand_id'];
                    $arFields['mname']=Tools::unesc($cc->qrow['name']);
                    $arFields['suffix']=Tools::unesc($cc->qrow['suffix']);
                    $arFields['balt']=$cc->qrow['balt']!=''?Tools::unesc($cc->qrow['balt']):$this->bname;
                    $arFields['malt']=$cc->qrow['alt']!=''?Tools::unesc($cc->qrow['alt']):$this->mname;
                    $arFields['balt1']=$this->firstS($this->balt);
                    $arFields['baltOther']=$this->otherS($this->balt);
                    $arFields['malt1']=$this->firstS($this->malt);
                    $arFields['maltOther']=$this->otherS($this->malt);
                    $arFields['model_id']=$cc->qrow['model_id'];
                    $arFields['burl']='/'.App_Route::_getUrl('tCat').'/'. $arFields['brand_sname'].'.html';
                    $arFields['self_url']='/'.App_Route::_getUrl('tTipo').'/'.$cc->qrow['sname'].'.html';
                    $arFields['cprice']=Tools::nn($cc->qrow['cprice']);
                    $arFields['width']=$cc->qrow['P3'];
                    $arFields['height']=$cc->qrow['P2'];
                    $arFields['radius']=$cc->qrow['P1'];
                    $arFields['svr']=$cc->qrow['P6'];
                    $arFields['size']="{$cc->qrow['P3']}/{$cc->qrow['P2']} ".($cc->qrow['P6']?'Z':'')."R{$cc->qrow['P1']}";
                    $arFields['video_link']=$cc->qrow['video_link'];
                    $arFields['INIS']=$cc->qrow['P7'];
                    $arFields['fullSize']= $arFields['size'].' '. $this->inis.' '. $arFields['suffix'];
                    $arFields['inisUrl']= $arFields['INIS']!=''?('<a href="#" rel="/ax/explain/inis?v='.$arFields['INIS'].'" title="Что означает '. $arFields['INIS'].'?" class="atip gr">'. $arFields['INIS'].'</a>'): $arFields['INIS'];

                    if($cc->qrow['cprice']) $arFields['priceText']=Tools::nn($cc->qrow['cprice']).'&nbsp;руб.';
                    else  $arFields['priceText']='уточняйте по тел.';

                    $arFields['qty']=(($cc->qrow['P1'] >= $this->minQtyRadius && $cc->qrow['sc'] >= 2) || ($cc->qrow['P1'] < $this->minQtyRadius  && $cc->qrow['sc'] >= 4) ? (int)$cc->qrow['sc'] : 0);
                    if($arFields['qty']>0) $arFields['scDiv'] = true;
                    $arFields['scText']=$arFields['qty']==0?"<noindex>нет в наличии</noindex>":"есть на складе";
                    $arFields['qtyText']=$arFields['qty']>12?"&gt;&nbsp;12&nbsp;шт":(!$arFields['qty']?'отсутствует':$arFields['qty']."&nbsp;шт");
                    $arFields['defQty']=$arFields['qty']>4 ||  $arFields['qty']==0?4:$cc->qrow['sc'];
                    $arFields['maxQty']=$cc->qrow['sc'];
                    $arFields['minQty']=$this->minQty($this->radius,1);

                    $arFields['cat_id']=$cc->qrow['cat_id'];
                    $arFields['img1']=$cc->make_img_path($cc->qrow['img1']);
                    $arFields['img2']=$cc->make_img_path($cc->qrow['img2']);
                    $arFields['imgAlt']='Фото шины '.Tools::cutDoubleSpaces($this->bname.' '.$this->mname.' '.$this->size.' '.$this->inis.' '.$this->suffix);
                    $arFields['imgTitle']='Шина '.Tools::cutDoubleSpaces($this->bname.' '.$this->mname.' '.$this->size.' '.$this->inis.' '.$this->suffix);

                    $arFields['bimg1']=$cc->make_img_path($cc->qrow['brand_img1']);

                    $arFields['brand_alt']=$this->brand.($this->balt!=''?" ({$this->balt})":'');

                    $arFields['sezonId']=$cc->qrow['MP1'];
                    $arFields['shipId']=$cc->qrow['MP3'];
                    $arFields['atId']=$cc->qrow['MP2'];

                    $arFields['mtext']=$this->ss->parseText(Tools::unesc($cc->qrow['text']));


                    if(trim(Tools::stripTags($this->mtext))!=''){
                        $arFields['mtext']=$this->ss->splitText($this->mtext,2);
                    }else{
                        $arFields['mtext']=$this->ss->splitText($this->parse($this->ss->getDoc('tpl_tmodel$10')),2);
                    }

                    if($cc->qrow['mspez_id']==1) $this->new=true; else $this->new=false;

                    $arFields['title']='Шины '.Tools::cutDoubleSpaces($arFields['bname'].' '.$arFields['mname'].' '.$arFields['size'].' '.$this->inis.' '.$arFields['suffix']).' - купить, цена, наличие';
                    $arFields['_title']=Tools::cutDoubleSpaces($arFields['bname'].' '.$arFields['mname'].' '.$arFields['size'].' '.$this->inis.' '.$arFields['suffix']);

                } else {
                    // Поля для типоразмеров дисков

                    $arFields['bname']=Tools::unesc($cc->qrow['bname']);
                    $arFields['brand_sname']=$cc->qrow['brand_sname'];
                    $arFields['model_sname']=$cc->qrow['model_sname'];
                    $arFields['brand_id']=$cc->qrow['brand_id'];
                    $arFields['mname']=Tools::unesc($cc->qrow['name']);
                    $arFields['color']=Tools::unesc($cc->qrow['suffix']);
                    $arFields['balt']=$cc->qrow['balt']!=''?Tools::unesc($cc->qrow['balt']):$arFields['bname'];
                    $arFields['malt']=$cc->qrow['alt']!=''?Tools::unesc($cc->qrow['alt']):$arFields['mname'];
                    $arFields['model_id']=$cc->qrow['model_id'];
                    $arFields['replica']=$cc->qrow['replica'];
                    $arFields['burl']='/'.App_Route::_getUrl('dCat').'/'.$arFields['brand_sname'].'.html';
                    $arFields['self_url']='/'.App_Route::_getUrl('dTipo').'/'.$cc->qrow['sname'].'.html';
                    $arFields['cprice']=Tools::nn($cc->qrow['cprice']);
                    $arFields['width']=$cc->qrow['P2'];
                    $arFields['radius']=$cc->qrow['P5'];
                    $arFields['dia_val']=$cc->qrow['P3'];
                    $arFields['pcd']=$cc->qrow['P6'];
                    $arFields['dirok']=$cc->qrow['P4'];
                    $arFields['size']="{$cc->qrow['P2']}x{$cc->qrow['P5']}";
                    $arFields['size1']="{$cc->qrow['P2']} x {$cc->qrow['P5']}";
                    $arFields['et']=$cc->qrow['P1'];
                    $arFields['d_type'] = $cc->qrow['MP1']; // 2-литые
                    if($cc->qrow['P4'] && $cc->qrow['P6']) $arFields['sverlovka']="{$cc->qrow['P4']}/{$cc->qrow['P6']}"; else $arFields['sverlovka']='';
                    if($cc->qrow['P4'] && $cc->qrow['P6']) $arFields['sverlovka1']="{$cc->qrow['P4']} / {$cc->qrow['P6']}"; else $arFields['sverlovka1']='';
                    if($cc->qrow['P3']) $arFields['dia']=$cc->qrow['P3']; else $arFields['dia']='';
                    $arFields['fullSize']=trim( $arFields['size'].' '. $arFields['sverlovka'].( $arFields['et']!=''?" ET{$arFields['et']}":'').' d'.$arFields['dia']);
                    $arFields['fullSize1']=trim( $arFields['size1'].' '. $arFields['sverlovka1'].($arFields['et']!=''?" ET {$arFields['et']}":'').' '.$arFields['dia']);

                    $arFields['img1']=$cc->make_img_path($cc->qrow['img1']);
                    $arFields['img2']=$cc->make_img_path($cc->qrow['img2']);

                    if($arFields['img1'] == '') $arFields['img1'] = $this->noimg2;
                    if($arFields['img2'] == '') $arFields['img2'] = $this->noimg2;


                    if($cc->qrow['cprice'])  $arFields['priceText']=Tools::nn($cc->qrow['cprice']).'&nbsp;р.';
                    else $arFields['priceText']='уточняйте по тел.';

                    $arFields['qty']=(($cc->qrow['P5'] >= $this->minQtyRadius && $cc->qrow['sc'] >= 2) || ($cc->qrow['P5'] < $this->minQtyRadius && $cc->qrow['sc'] >= 4) ? (int)$cc->qrow['sc'] : 0);
                    $arFields['scText']=$arFields['qty']==0?"<noindex>нет в наличии</noindex>":"есть на складе";
                    $arFields['qtyText']=$arFields['qty']>12?"&gt;&nbsp;12&nbsp;шт":(!$arFields['qty']?'отсутствует':$arFields['qty']."&nbsp;шт");
                    $arFields['defQty']=$arFields['qty']>4 || $arFields['qty']==0?4:$cc->qrow['sc'];
                    $arFields['maxQty']=$cc->qrow['sc'];
                    $arFields['minQty']=$this->minQty($arFields['radius'],2);

                    if($arFields['qty']>0) $arFields['scDiv'] = true;

                    $arFields['title']='Диски '.Tools::cutDoubleSpaces($arFields['bname'].' '.$arFields['mname'].' '.$arFields['fullSize1'].' '.$arFields['color']).' - купить, цена, наличие';
                    $arFields['_title']=Tools::cutDoubleSpaces($arFields['bname'].' '.$arFields['mname'].' '.$arFields['fullSize'].' '.$arFields['color']);

                    $arFields['color_url']= $this->dict_url($cc->dict_search_key($arFields['color'], $cc->qrow['gr'], $arFields['brand_id']));
                    //$arFields['fields'] = $cc->qrow;

                    // Стикеры
                    $arFields['m_sticker'] = array();
                    $arFields['sticker_id'] = $cc->qrow['sticker_id'];
                    if (!empty($cc->qrow['sticker_id'])) {
                        $CC_Ctrl = new CC_Ctrl();
                        $stickers_list = $CC_Ctrl::getStickersList();
                        $m_sticker = $CC_Ctrl->getModelSticker($cc->qrow['model_id']);
                        if (!empty($m_sticker)) {
                            $arFields['m_sticker'] = array_merge($m_sticker, $stickers_list[$m_sticker['sticker_type']]);
                        }
                        unset($CC_Ctrl);
                    }
                }

            }

            if(!empty($cc->qrow['gr'])) {
                $this->modelsForToken[$id]['value'][$vid] = $arFields;
            }

        }

    }


    private function _tireModel($model_id, $arFields)
    {
        if($model_id == 27989) return array();
        if(empty($model_id)) return false;
        $this->doubleDimension=false;

        $this->mLimit=(int)abs(Data::get('t_models_per_page'));
        $page=(int)abs(@Url::$sq['page']);
        if(!$page) $page=1;

        $r=array(
            'gr'=>1,
            'model_id'=>$model_id,
            'brand_id'=>$arFields['brand_id'],
            'qSelect'=>array(
                'scDiv'=>array()
            ),
            'order'=>"scDiv DESC, cc_model.name ASC"
        );
        if(!empty($this->radius)) $r['whereCat'][]="cc_cat.P1 = '{$this->radius}'";
        if(!empty($this->sezonId)) $r['P1']=$this->sezonId;
        if($this->shipId!=='') $r['P3']=$this->shipId;
        if(!empty($this->atId)) {
            if($this->atId==1) $r['P2']=array('list'=>array(1,4));
            else if($this->atId==2) $r['P2']=array('list'=>array(2,4));
            else $r['P2']=3;
        }

        if(empty($this->sezonId)){
            $sezOrder=Data::get('ccSezonOrder');
            if($sezOrder==1){
                // сначала зиму
                //sezOrd= {1,3,5}
                $r['select']="IF(cc_model.P1=1, 5, IF(cc_model.P1=3, 3, 1)) AS sezOrd";
            }elseif($sezOrder==2){
                // сначала лето
                //sezOrd= {1,3,5}
                $r['select']="IF(cc_model.P1=2, 5, IF(cc_model.P1=3, 3, 1)) AS sezOrd";
            }else $sezOrder=0;
            if(!empty($sezOrder))
                $r['order']='scDiv DESC, sezOrd, cc_model.name ASC';
        }

        $this->num=$this->cc->models($r);

        if(!$this->num) {
            $this->bottomTextTitle=$this->topText=$this->bottomText='';
            $this->noResults=$this->parse($this->ss->getDoc('t_models_nr_sub$6'));
            return false;
        }

        $d=$this->cc->fetchAll('', MYSQL_ASSOC);


        $this->models=array();

        $burl='/'.App_Route::_getUrl('tModel').'/';

        $v = array();

        foreach($d as $v){
            // *****************************************************************
            $v['catalog_items'] = array('gt0'=>array(), 0=>array());
            $do = $this->cc->cat_view(array(
                'model_id' => $v['model_id'],
                'gr'=>1,
                'scDiv'=>'NOT NULL',
                'nolimits'=>true,
                'order'=>'scDiv DESC, cc_cat.P1, cc_cat.P3,cc_cat.P2'
            ));

            $desc=$this->cc->fetchAll();
            $v['item_rads']=array();
            $prices = Array();
            foreach($desc as $item)
            {
                if(empty($this->diametr) || $this->diametr==$item['P5'])
                {
                    if($item['sc'] && $item['cprice'] > 0)
                    {
                        $prices[] = $item['cprice'];
                    }
                }
                if($item['sc'])
                {
                    $v['item_rads'][$item['P1']] = ($this->diametr == $item['P1']) ? true : false;
                }

            }
            $v['prices'] = $prices;
            $v['radiuses'] = $v['item_rads'];
            $v['spezId'] = $v['mspez_id']==1?true:false;
            $v['sez']=$v['P1'];
            $v['ship']=$v['P3'];
        }

        return $v;
    }

    private function _diskModel($model_id, $arFields)
    {
        $this->mLimit=(int)abs(Data::get('d_models_per_page'));
        $page=(int)abs(@Url::$sq['page']);
        if(!$page) $page=1;

        $r=array(
            'gr'=>2,
            'model_id'=>$model_id,
            'brand_id'=>$arFields['brand_id'],
            'qSelect'=>array(
                'scDiv'=>array()
            ),
            'order'=>"scDiv DESC, m_pos ASC, cc_model.name ASC"
        );

        $this->num=$this->cc->models($r);


        if(!$this->num) {
            $this->bottomTextTitle=$this->topText=$this->bottomText='';
            $this->noResults=$this->parse($this->ss->getDoc('t_models_nr_sub$6'));
            return false;
        }

        $d=$this->cc->fetchAll('', MYSQL_ASSOC);

        $this->models=array();

        $burl='/'.App_Route::_getUrl('dModel').'/';
        $CC_Ctrl = new CC_Ctrl();
        $stickers_list = $CC_Ctrl::getStickersList();
        foreach($d as $v)
        {
            // *****************************************************************
            $v['catalog_items'] = array('gt0'=>array(), 0=>array());
            $this->cc->cat_view(array(
                'model_id' => $v['model_id'],
                'gr'=>2,
                'scDiv'=>1,
                'nolimits'=>true,
                'order'=>'scDiv DESC, cc_cat.P5, cc_cat.P4,cc_cat.P6, cc_cat.P1, cc_cat.P2, cc_cat.P3'
            ));
            $desc=$this->cc->fetchAll();
            $v['item_rads']=array();
            $prices = Array();
            $colors_url = Array();
            foreach($desc as $item)
            {
                if(empty($this->diametr) || $this->diametr==$item['P5'])
                {
                    if($item['sc']) // Убрать, если нужны будут и те, которых нет на складе
                    {
                        $colors_url[] = $this->dict_url($this->cc->dict_search_key($item['csuffix'],$item['gr'],$this->brand_id));
                        if ($item['cprice'] > 0)
                        {
                            $prices[] = $item['cprice'];
                        }
                    }
                }
                if($item['sc'])
                {
                    $v['item_rads'][$item['P5']] = ($this->diametr==$item['P5']) ? true : false;
                }
            }
            // *****************************************************************
            $malt=Tools::unesc($v['alt']!=''?$v['alt']:$v['name']);
            $malt1=$this->firstS($malt);
            $mname=Tools::unesc($v['name']);
            $bname=Tools::unesc($v['bname']);

            $v['suffix']=Tools::unesc($v['suffix']);
            // Стикеры
            if (!empty($v['sticker_id'])) {
                $m_sticker = $CC_Ctrl->getModelSticker($v['model_id']);
                if (!empty($m_sticker)) {
                    @$m_sticker = array_merge($m_sticker, $stickers_list[$m_sticker['sticker_type']]);
                }
            }
            else $m_sticker = array();
            //
            $vi=array(
                'anc'=>"{$this->bname} {$mname} {$v['suffix']}",
                'anc'=>$bname.' '.$mname.' '.Tools::unesc($v['suffix']),
                'alt'=>"диски {$this->balt} {$malt1}",
                'url'=>$burl.$v['sname'].'.html',
                'img'=>$this->cc->makeImgPath($v['img1']),
                'scDiv'=>$v['scDiv'],
                'spezId'=>$v['mspez_id']==2?true:false,
                'model_sticker' => $m_sticker,
                'video_link' => $v['video_link'],

            );
            if($vi['img']=='') $vi['img']=$this->noimg2;
            $vi['colors'] = array_unique($colors_url, SORT_STRING);
            $vi['prices'] = $prices;
            $vi['radiuses'] = $v['item_rads'];
            $this->models[]= $vi;
        }
        return $vi;
    }

    public function dict_url($r)
    {
        $s='';
        foreach($r as $k=>$v) if($v)
            $s.=($s!=''?'&nbsp;&nbsp;':'')."<a href=\"#\" rel=\"/ax/explain/color?v=$v\" title=\"Что значит $k?\" class=\"atip gr\">$k</a>";
        else $s.=($s!=''?'&nbsp;&nbsp;':'').$k;
        return trim($s);
    }
}