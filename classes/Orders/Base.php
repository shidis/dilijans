<?
if (!defined('true_enter')) die ("Direct access not allowed!");


// COST - сумма заказа со скидкой и со стоимостью доставки и всеми допами
/* 	Класс управления заказами   
	
	Базовые поля в таблице orders:
		order_num,
		name, - имя покупателя
		email,
		city,
		addr, - адрес доставки
		info, - комментарий покупателя
		tech, комментарий менеджера
		state_id, - статус заказа
		dt_add, - дата добавления заказа
		dt_state, - дата изменения статуса последнего
		ip,
		cost, - стоимость заказа без скидок и доставки
		bcost, обычно итоговая стоимость заказа со всеми скидками и доставкой
		discount, - процент скидки
		delivery_cost, - стоимость доставки
		meta - резервное поле под всякую служебную инфу
        cUserId - ИД менеджера работающего с заказом
        createdBy - ИД менеджера оформившего заказ или 0 если клиент это сделал

		остальные через TFields

        os_order.LD = { 0 - нормальное состояние   1 - заказ удален     2 - заказ создан но не подтвержден, может быть удален создавшим его  }
*/

abstract class Orders_Base extends DB
{
    var $order_id;
    var $min_d,$min_m,$min_y,$max_d,$max_m,$max_y; //min_od()   max_od()
    var $deliveryCost;


    // массив orderStates пересчитанный для текущего юзера с применением customPerms. Сам массив customPerms в нем будет удален
    // если не авторизован, то этот массив будет равен orderStates с удаленным  customPerms
    var $_orderStates;

    /*
     * allowFrom - возможны смены сотояния на это допускается только из перечисленных состояний
     * editable - возможность редактирования полей заказа в этом состоянии
     * isolatedChanges - менять ЭТО сотояние может только тот сотрудник (cUserId) кто в момент изменения является обслуживающим заказ
     * handler - метод-обработчик в классе Orders для изменения этого состояния
     * cmsDefaultChk - стоит галочка в фильтре заказов по умолчанию
     * bgcolor - цвет фона в таблице с заказами
     * excludeFromDropList - (true) - не показывать в выпадающем списке  в списке заказов
     * method - статус доступен только в бывранных методах оплаты @array
     * next - id логически следующего статуса.
     * keepCUser - @bool  - при смене НА это состояние не изменять менеджера, иначе при смене состояния будет подставляться авторизованный юзер. Исключение: если cUserId==0, то все таки будет изменение.
     * customPerms- переопределяет глобальные права смены статуса и редактирования для выбранных ролей. @array().
     *      Ключ масива - roleId
     *
     * Во избежании ошибок выполнения все поля, кроме next & customPerms должны быть инициализированы
     * Не изменяемые ключи стутусов
     * с индексом 0 - вегда статус нового заказа
     * с индексом 5 - всегда первый статус после "новый"  (в обработке)
     * с индексом -1 - вегда статус отмененного заказа
     * с индексом -3 - вегда статус отложенного заказа
     */
    public $orderStates=array(
        0=>array(
            'label' => 'новый',
            'allowFrom' => array(-1,5,15),
            'actLabel' => 'сделать новым',
            'editable' => true,
            'handler' => 'changeState_New',
            'isolatedChanges' => false,
            'customPerms'=>array(),
            'cmsDefaultChk' => 1,
            'keepCUser'=>false,
            'bgStyle' => array('background-color' => '#FFFFFF'),
            'textStyle' => array('font-weight' => 'bold'),
            'next' => 5,
            'method' => array(0, 1)
        ),
        5 => array(
            'label' => 'в обработке',
            'allowFrom' => array(0, 2, 10, -1),
            'actLabel' => 'в обработку',
            'editable' => true,
            'handler' => 'changeState_Processing',
            'isolatedChanges' => false,
            'customPerms'=>array(),
            'cmsDefaultChk' => 1,
            'keepCUser'=>false,
            'bgStyle' => array('background-color' => '#EEE'),
            'textStyle' => '',
            'method' => array(0, 1)
        ),
        15=>array(
            'label'=>'доставлен',
            'allowFrom'=>array(0,5,-1),
            'actLabel'=>'доставлен',
            'editable'=>false,
            'handler'=>'changeState_Delivered',
            'isolatedChanges'=>false,
            'customPerms'=>array(),
            'cmsDefaultChk'=>0,
            'keepCUser'=>false,
            'bgStyle' => array('background-color' => '#0C9'),
            'textStyle' => '',
            'method'=>array(0,1)
        ),
        -1=>array(
            'label'=>'отменен',
            'allowFrom'=>array(0,5,15),
            'actLabel'=>'отменить',
            'editable'=>false,
            'handler'=>'changeState_Cancel',
            'isolatedChanges'=>false,
            'customPerms'=>array(),
            'cmsDefaultChk'=>0,
            'keepCUser'=>false,
            'bgStyle' => array('background-color' => '#F66'),
            'textStyle' => '',
            'method'=>array(0,1)
        )
    );

    /*
     * конфиг выгрузки документов
     * tn - путь к файлу шаблона начиная от /app/templates/
     * useInCMS - шаблон отображается в списках в админке
     * fn - имя файла по умолчанию при выгрузке (для HTML - не актуально)
     * mailSubject - тема для одиночной выгрузке документа с отправкой на почту
     * internalUse - запрет на отправку на внешние емейлы
     *
     * Ключ документа не должен содержать в названии точки
     */
    public $docCfg=array(

        'html'=>array(
            'orderSpecFizClient'=>array(
                'name'=>'Спецификация клиенту (физ)',
                'tn'=>'mail/ORDER_HTML_CLIENT.php',
                'ptype'=>array(0),
                'method'=>array(0,1),
                'useInCMS'=>false
            ),
            'orderSpecUrClient'=>array(
                'name'=>'Спецификация клиенту (юр)',
                'tn'=>'mail/UORDER_HTML_CLIENT.php',
                'ptype'=>array(1),
                'method'=>array(0,1),
                'useInCMS'=>false
            ),
            'orderSpecFizMgr'=>array(
                'name'=>'Спецификация менеджеру (физ)',
                'tn'=>'mail/ORDER_HTML_BASE.php',
                'ptype'=>array(0),
                'method'=>array(0,1),
                'useInCMS'=>true,
                'internalUse'=>true,
                'mailSubject'=>'Заказ N{$order_num} на {$siteName}'
            ),
            'orderSpecUrMgr'=>array(
                'name'=>'Спецификация менеджеру (юр)',
                'tn'=>'mail/UORDER_HTML_BASE.php',
                'ptype'=>array(1),
                'method'=>array(0,1),
                'useInCMS'=>true,
                'internalUse'=>true,
                'mailSubject'=>'Заказ N{$order_num} на {$siteName}'
            )
        ),
        'pdf'=>array(
            'orderSpecFizClient' => array(
                'name' => 'Спецификация клиенту (физ)',
                'tn' => 'mail/ORDER_HTML_CLIENT.php',
                'ptype' => array(0),
                'method' => array(0, 1),
                'useInCMS' => false,
                'mailSubject' => 'Заказ {$orderNum}',
                'fn' => 'Заказ {$orderNum}.pdf',
                'mpdfInit' => array(
                    'mode' => 'ru_RU',
                    'format' => 'A4',
                    'defFontSize' => '10pt',
                    'defFont' => 'Arial',
                    'marginLeft' => 15,
                    'marginRight' => 10,
                    'marginTop' => 10,
                    'marginBottom' => 5,
                    'marginHeader' => 0,
                    'marginFooter' => 0,
                    'orient' => 'P'
                )
            ),
            'orderSpecUrClient' => array(
                'name' => 'Спецификация клиенту (юр)',
                'tn' => 'mail/UORDER_HTML_CLIENT.php',
                'ptype' => array(1),
                'method' => array(0, 1),
                'useInCMS' => false,
                'mailSubject' => 'Заказ {$orderNum} на {$siteName}',
                'fn' => 'Заказ {$orderNum}.pdf',
                'mpdfInit' => array(
                    'mode' => 'ru_RU',
                    'format' => 'A4',
                    'defFontSize' => '10pt',
                    'defFont' => 'Arial',
                    'marginLeft' => 15,
                    'marginRight' => 10,
                    'marginTop' => 10,
                    'marginBottom' => 5,
                    'marginHeader' => 0,
                    'marginFooter' => 0,
                    'orient' => 'P'
                )
            ),
            'orderSpecFizMgr' => array(
                'name' => 'Спецификация менеджеру (физ)',
                'tn' => 'mail/ORDER_HTML_BASE.php',
                'ptype' => array(0),
                'method' => array(0, 1),
                'useInCMS' => true,
                'internalUse' => true,
                'mailSubject' => 'Заказ {$orderNum}',
                'fn' => 'Заказ {$orderNum}.pdf',
                'mpdfInit' => array(
                    'mode' => 'ru_RU',
                    'format' => 'A4',
                    'defFontSize' => '10pt',
                    'defFont' => 'Arial',
                    'marginLeft' => 15,
                    'marginRight' => 10,
                    'marginTop' => 10,
                    'marginBottom' => 5,
                    'marginHeader' => 0,
                    'marginFooter' => 0,
                    'orient' => 'P'
                )
            ),
            'orderSpecUrMgr' => array(
                'name' => 'Спецификация менеджеру (юр)',
                'tn' => 'mail/UORDER_HTML_BASE.php',
                'ptype' => array(1),
                'method' => array(0, 1),
                'useInCMS' => true,
                'internalUse' => true,
                'mailSubject' => 'Заказ {$orderNum}',
                'fn' => 'Заказ {$orderNum}.pdf',
                'mpdfInit' => array(
                    'mode' => 'ru_RU',
                    'format' => 'A4',
                    'defFontSize' => '10pt',
                    'defFont' => 'Arial',
                    'marginLeft' => 15,
                    'marginRight' => 10,
                    'marginTop' => 10,
                    'marginBottom' => 5,
                    'marginHeader' => 0,
                    'marginFooter' => 0,
                    'orient' => 'P'
                )
            )
        )
    );

    // конфиг функционала админки
    /*
     * кроме того:
     * App_TFields[os_order][method] - наличие поля автоматически включает функционал работы с типами оплаты
     * App_TFields[os_order][ptype] - наличие поля автоматически включает функционал работы с юр/физ лицами
     *
     * параметры DBF_* - названия неорбходимых полей в базе данных
     */
    public $adminCfg=array(
        'ordersListLimit'=>100
    );

    // настройки вывода таблицы с заказами в админке (Orders.ordersList), по умолчанию
    var $cmsOrderTableSort='GREATEST(os_order.dt_add,os_order.dt_state)';
    var $cmsOrderDatesField='GREATEST(os_order.dt_add,os_order.dt_state)';
    // для ordersList(): если выбран отбор по менеджеру, то будет также показаны заказы с этими state_id
    var $cmsInjectStatesWithCUser0=array(0);


    function __construct()
    {
        parent::__construct();

    }

    public function initOrderStatesByUser()
    {
        $this->_orderStates=$this->orderStates;

        foreach($this->_orderStates as $k=>$v){
            unset($this->_orderStates[$k]['customPerms']);
            if(isset($v['customPerms'][CU::$roleId]) && is_array($v['customPerms'][CU::$roleId]) && !empty($v['customPerms'][CU::$roleId]))
                $this->_orderStates[$k]=array_merge($this->_orderStates[$k],$v['customPerms'][CU::$roleId]);
        }

    }

    /*
     * поштучное изменение полей заказа
     * юзер должен быть авторизован иначе проверка на соттв. юзеру будет пропущено
     * на вход
     * $r[order_id]
     * $r[field] - доп поля должны иметь вид af[имя_поля]
     * $r['newVal'] - новое значение
     *
     * на выходе (array) {
     *      fres - false|true
     *      prevVal - старое значение поля
     *      newVal - новое значение записанное в базу если $this->fres==true или prevVal иначе
     *      любые другие переменные
     * }
     * и статус в $this->fres, $this->fres_msg
     * if $this->fres то в базу изменения не записаны
     * prevVal & newVal возвращаются готовыми к вставке в поля, т.е. применется taria || html
     *
     * для любого поля можно сделать свой обработчик, назвав его orderDataEdit_название__редактируемого_поля($order_id,$newVal)  пример orderDataEdit_ptype()
     * результат обработчика array( fres|| любые данные которые мерджатся с основным результатом). newVal возвращать не надо отсюда
     */
    function orderDataEdit($r)
    {
        $res=array('prevVal'=>'','newVal'=>'','fres'=>true);

        if(!isset($r['newVal'])){
            $res['fres']=$this->putMsg(false,'Не передано значение поля');
            return $res;
        }
        if(empty($r['field'])){
            $res['fres']=$this->putMsg(false,'Не передано название поля');
            return $res;
        }

        $order_id=(int)@$r['order_id'];
        if(empty($r['order_id'])){
            $res['fres']=$this->putMsg(false,'Не передан ID заказа');
            return $res;
        }

        if(preg_match("~af\[([a-z0-9_\-]+)\]~iu",@$r['field'],$m) && isset(App_TFields::$fields['os_order'][$m[1]])) $field=$m[1];
        else {
            $field=Tools::esc($r['field']);
            if(!in_array($field, array('order_num','name','email','city','addr','info','tech','delivery_cost','discount'))){
                $res['fres']=$this->putMsg(false,'Не корректное название поля');
                return $res;
            }
        }
        $d=$this->getOne("SELECT $field,state_id,cUserId,order_num FROM os_order WHERE LD!=1 AND order_id=$order_id");
        if($d!==0){
            $res['newVal']=$res['prevVal']=$this->ordersFieldForOutput($field,$d[$field]);
        }else{
            $res['fres']=$this->putMsg(false,"Заказ $order_id не найден");
            return $res;
        }

        $this->initOrderStatesByUser();

        if(CU::$userId){
            if($d['cUserId']!=0 && @$this->_orderStates[$d['state_id']]['isolatedChanges'] && CU::$userId != $d['cUserId']){
                $res['fres']=$this->putMsg(false, "Смена состояния заказа {$d['order_num']} может производить только обслуживающий его сотрудник");
                return $res;
            }
        }

        if(!@$this->_orderStates[$d['state_id']]['editable'] && $field!='tech'){
            $res['fres']=$this->putMsg(false,"В текущем статусе данное изменение не предусмотрено");
            return $res;
        }

        if(method_exists($this, "orderDataEdit_$field")){
            $rr=call_user_func(array($this, "orderDataEdit_$field"), $order_id, $r['newVal']);
            $res=array_merge($res,$rr);
            if(!$rr['fres']){
                return $res;
            }
        }
        $newVal=$this->ordersFieldForDB($field,$r['newVal']);
        $this->query("UPDATE os_order SET $field='$newVal' WHERE order_id=$order_id");

        $d=$this->getOne("SELECT $field FROM os_order WHERE order_id=$order_id");
        $res['newVal']=$this->ordersFieldForOutput($field,$d[$field]);

        if(in_array($r['field'],array('discount','delivery_cost'))) {
            $rr=$this->orderUpdateItog($order_id);
            if($rr!==false)
                $res=array_merge($res,$rr);
            else{
                $res['fres']=false;
                return $res;
            }
        }

        return $res;

    }

    /*
     * проверка возможности смены method и вернем доступные для этого метода состояния в виде массива (state_id=>actLabel)
     */
    function orderDataEdit_method($order_id,$newVal)
    {
        $res=array('fres'=>true);

        $d=$this->getOne("SELECT state_id FROM os_order WHERE LD!=1 AND order_id=$order_id");
        if($d===0){
            $res['fres']=$this->putMsg(false,"[orderClientDataEdit_method]:: Заказ $order_id не найден");
            return $res;
        }

        if(!in_array((int)$newVal,$this->_orderStates[$d['state_id']]['method'])){
            $res['fres']=$this->putMsg(false,"В текущем статусе изменение метода оплаты заказа не возможно.");
            return $res;
        }

        $res['oStates']=array();
        foreach($this->_orderStates as $k=>$v)
            if(in_array((int)$newVal,$v['method'])) $res['oStates'][$k]=$v['actLabel'];

        return $res;
    }

    /*
     * на вход экранированные значения
     * применяет taria, html к $val в зависимости от $field
     * только для полей os_order
     */
    function ordersFieldForOutput($field,$val)
    {
        switch($field){
            case 'order_num':
                $res=$val;
                break;
            case 'name':
            case 'email':
            case 'city':
                $res=Tools::html($val);
                break;
            case 'addr':
            case 'info':
            case 'tech':
                $res=Tools::taria($val);
                break;
            case 'delivery_cost':
            case 'discount':
                $res=(float)$val;
                break;
            default:
                $type=@App_TFields::$fields['os_order'][$field]['widget'];
                switch ($type){
                    default:
                    case 'input':
                        $res=Tools::html($val);
                        break;
                    case 'textarea':
                        $res=Tools::taria($val);
                        break;
                }
        }

        return $res;
    }

    /*
     * на вход неэкранированные значения
     * применяет untaria, esc к $val в зависимости от $field. На выходе - готовое к записи в БД значение
     * только для полей os_order
     */
    function ordersFieldForDB($field,$val)
    {
        switch($field){
            case 'order_num':
                $res=(int)$val;
                break;
            case 'name':
            case 'email':
            case 'city':
                $res=Tools::esc($val);
                break;
            case 'addr':
            case 'info':
            case 'tech':
                $res=Tools::untaria($val);
                break;
            default:
                $type=@App_TFields::$fields['os_order'][$field]['widget'];
                switch ($type){
                    default:
                    case 'input':
                        $res=Tools::esc($val);
                        break;
                    case 'textarea':
                        $res=Tools::untaria($val);
                        break;
                }
        }

        return $res;
    }


    /*
    редактирование спецификации  заказа
    на вход массив=>
    order_id:int
    mode: {edit|del|add}
    Для режима edit:
        item_id:int или dop_id:int
        field: изменяемое поле (доп поля должны иметь вид af[имя_поля])
        newVal: новое значение
    Для режима добавления:
        item_id:1 или dop_id:1 - для выбора таблицы куда добавляем
        row:array(с полями)
    Для режима del:
        item_id:int или dop_id:int

    на выходе false|array(
        fres - false|true
        cost:decimal
        Для режима edit:
            prevVal: предыдущее значение
            newVal: новое значение записанное в базу если $this->fres==true или prevVal иначе
        Для режима add:
            newRow: массив с добавленными значениями
            item_id:int или dop_id:int
    )
     для любого поля можно сделать свой обработчик для режима редактирвоания, назвав его orderSpecEdit(Item|Dop)_название__редактируемого_поля(($item_id|$dop_id),$newVal)  пример orderSpecEditItem_pprice()
     результат обработчика array( fres|| любые данные которые мерджатся с основным результатом). newVal возвращать не надо отсюда
    ошибка в $this->fres_msg
     */
    function orderSpec($r)
    {
        $res=array('prevVal'=>'','newVal'=>'','fres'=>true);

        if(!in_array(@$r['mode'], array('edit','add','del'))){
            $res['fres']=$this->putMsg(false,'Не задан режим обработки данных');
            return $res;
        }

        $order_id=(int)@$r['order_id'];
        if(empty($r['order_id'])){
            $res['fres']=$this->putMsg(false,'Не передан ID заказа');
            return $res;
        }

        $d=$this->getOne("SELECT state_id,cUserId,order_num FROM os_order WHERE LD!=1 AND order_id=$order_id");
        if($d===0){
            $res['fres']=$this->putMsg(false,"Заказ $order_id не найден");
            return $res;
        }

        $this->initOrderStatesByUser();

        if(CU::$userId){
            if($d['cUserId']!=0 && @$this->_orderStates[$d['state_id']]['isolatedChanges'] && CU::$userId != $d['cUserId']){
                return $this->putMsg(false, "Смена состояния заказа {$d['order_num']} может производить только обслуживающий его сотрудник");
            }
        }

        if(!@$this->_orderStates[$d['state_id']]['editable']){
            return $this->putMsg(false,"В текущем статусе данное изменение не предусмотрено");
        }

        if($r['mode']=='edit'){

            if(!isset($r['newVal'])){
                $res['fres']=$this->putMsg(false,'Не передано значение поля');
                return $res;
            }
            if(empty($r['field'])){
                $res['fres']=$this->putMsg(false,'Не передано название поля');
                return $res;
            }

            if(!empty($r['dop_id'])){

                if(preg_match("~af\[([a-z0-9_\-]+)\]~iu",@$r['field'],$m) && isset(App_TFields::$fields['os_dop'][$m[1]])) $field=$m[1];
                else {
                    $field=Tools::esc($r['field']);
                    if(!in_array($field, array('name','amount','price'))){
                        $res['fres']=$this->putMsg(false,'Не корректное название поля');
                        return $res;
                    }
                }

                if($field=='name' && trim($r['newVal'])==''){
                    $res['fres']=$this->putMsg(false,'Название не может быть пустой строкой');
                    return $res;
                }
                if($field=='amount' && $r['newVal']<=0){
                    $res['fres']=$this->putMsg(false,'Неверно указаное количество');
                    return $res;
                }

                $dop_id=(int)$r['dop_id'];
                $d=$this->getOne("SELECT $field FROM os_dop WHERE NOT LD AND dop_id=$dop_id");
                if($d!==0){
                    $res['newVal']=$res['prevVal']=Tools::unesc($d[0]);
                }else{
                    $res['fres']=$this->putMsg(false,'Не найден доп dop_id='.$dop_id);
                    return $res;
                }

                if(method_exists($this, "orderSpecEditDop_$field")){
                    $rr=call_user_func(array($this, "orderSpecEditDop_$field"), $dop_id, $r['newVal']);
                    $res=array_merge($res,$rr);
                    if(!$rr['fres']){
                        return $res;
                    }
                }
                $newVal=Tools::esc($r['newVal']);
                $this->query("UPDATE os_dop SET $field='$newVal' WHERE dop_id=$dop_id");

                $d=$this->getOne("SELECT $field FROM os_dop WHERE NOT LD AND dop_id=$dop_id");
                $res['newVal']=Tools::html($d[$field]);

            }elseif(!empty($r['item_id'])){

                if(preg_match("~af\[([a-z0-9_\-]+)\]~iu",@$r['field'],$m) && isset(App_TFields::$fields['os_item'][$m[1]])) $field=$m[1];
                else {
                    $field=Tools::esc($r['field']);
                    if(!in_array($field, array('name','amount','price'))){
                        $res['fres']=$this->putMsg(false,'Не корректное название поля');
                        return $res;
                    }
                }

                if($field=='name' && trim($r['newVal'])==''){
                    $res['fres']=$this->putMsg(false,'Название не может быть пустой строкой');
                    return $res;
                }
                if($field=='amount' && $r['newVal']<=0){
                    $res['fres']=$this->putMsg(false,'Неверно указаное количество');
                    return $res;
                }

                $item_id=(int)$r['item_id'];
                $d=$this->getOne("SELECT $field FROM os_item WHERE NOT LD AND item_id=$item_id");
                if($d!==0){
                    $res['newVal']=$res['prevVal']=Tools::unesc($d[0]);
                }else{
                    $res['fres']=$this->putMsg(false,'Не найдена запись item_id='.$item_id);
                    return $res;
                }

                if(method_exists($this, "orderSpecEditItem_$field")){
                    $rr=call_user_func(array($this, "orderSpecEditItem_$field"), $item_id, $r['newVal']);
                    $res=array_merge($res,$rr);
                    if(!$rr['fres']){
                        return $res;
                    }
                }
                $newVal=Tools::esc($r['newVal']);
                $this->query("UPDATE os_item SET $field='$newVal' WHERE item_id=$item_id");

                $d=$this->getOne("SELECT $field FROM os_item WHERE NOT LD AND item_id=$item_id");
                $res['newVal']=Tools::html($d[$field]);
            }

        }elseif($r['mode']=='del'){

            if(!empty($r['dop_id'])){
                $dop_id=(int)$r['dop_id'];
                $d=$this->getOne("SELECT dop_id FROM os_dop WHERE NOT LD AND dop_id=$dop_id");
                if($d!==0){
                    $this->ld('os_dop','dop_id',$dop_id);
                }else{
                    $res['fres']=$this->putMsg(false,'Не найден доп dop_id='.$dop_id);
                    return $res;
                }

            }elseif(!empty($r['item_id'])){
                $item_id=(int)$r['item_id'];
                $d=$this->getOne("SELECT item_id FROM os_item WHERE NOT LD AND item_id=$item_id");
                if($d!==0){
                    $this->ld('os_item','item_id',$item_id);
                }else{
                    $res['fres']=$this->putMsg(false,'Не найдена запись item_id='.$item_id);
                    return $res;
                }
            }

        }elseif($r['mode']=='add'){

            if(!empty($r['item_id'])){

                if(empty($r['row'])){
                    $res['fres']=$this->putMsg(false,'Нет данных для добавления');
                    return $res;
                }

                foreach($r['row'] as $k=>&$v) {
                    if(!in_array($k, array('order_id','name','amount','price')) && isset(App_TFields::$fields['os_item'][$k])){
                        $res['fres']=$this->putMsg(false,'Не корректное название поля os_item.'.$k);
                        return $res;
                    }
                    $v=Tools::esc($v);
                }

                if(trim(@$r['row']['name'])==''){
                    $res['fres']=$this->putMsg(false,'Название не может быть пустой строкой');
                    return $res;
                }
                if(@$r['row']['amount']<=0){
                    $res['fres']=$this->putMsg(false,'Неверно указаное количество');
                    return $res;
                }

                $r['row']['order_id']=$order_id;

                $this->insert('os_item',$r['row']);
                $res['item_id']=$this->lastId();
                $d=$this->getOne("SELECT * FROM os_item WHERE item_id='{$res['item_id']}'");
                $res['newRow']=array();
                foreach($d as $k=>$v) {
                    $res['newRow'][$k]=Tools::html($v);
                }

            }elseif(!empty($r['dop_id'])){

                if(empty($r['row'])){
                    $res['fres']=$this->putMsg(false,'Нет данных для добавления');
                    return $res;
                }

                foreach($r['row'] as $k=>&$v) {
                    if(!in_array($k, array('order_id','item_id','name','amount','price')) && isset(App_TFields::$fields['os_dop'][$k])){
                        $res['fres']=$this->putMsg(false,'Не корректное название поля os_dop.'.$k);
                        return $res;
                    }
                    $v=Tools::esc($v);
                }

                if(trim(@$r['row']['name'])==''){
                    $res['fres']=$this->putMsg(false,'Название не может быть пустой строкой');
                    return $res;
                }
                if(@$r['row']['amount']<=0){
                    $res['fres']=$this->putMsg(false,'Неверно указаное количество');
                    return $res;
                }

                $r['row']['order_id']=$order_id;

                $this->insert('os_dop',$r['row']);
                $res['dop_id']=$this->lastId();
                $d=$this->getOne("SELECT * FROM os_dop WHERE dop_id='{$res['dop_id']}'");
                $res['newRow']=array();
                foreach($d as $k=>$v) {
                    $res['newRow'][$k]=Tools::html($v);
                }
            }

        }

        if($r['mode']=='edit' && (!empty($dop_id) && in_array($field,array('price','amount','pprice')) || !empty($item_id) && in_array($field, array('price','amount','pprice'))) || $r['mode']!='edit'){
            $rr=$this->orderUpdateItog($order_id);
            if($rr!==false)
                $res=array_merge($res,$rr);
            else{
                $res['fres']=false;
                return $res;
            }
        }
        return $res;
    }

    function getDeliveryCost($r=[])
    {
        $this->deliveryCost=(int)Data::get('delivery_cost');
        return $this->deliveryCost;
    }



    /*
     * пересчет bcost, cost для заказа с учетом discount. Стоимость доствки не пересчитывается а берется из заказа
     * bcost - сумма товаров без допов и доставкии скидки
     * @return false|array(cost:cost, bcost:bcost, pcost/если включен функционал purchase)
     */
    function orderUpdateItog($order_id)
    {
        $order_id=(int)$order_id;
        $od=$this->getOne("SELECT order_id, discount, delivery_cost FROM os_order WHERE LD!=1 AND order_id=$order_id");
        if($od===0){
            return $this->putMsg(false,"[orderUpdateItog()]:: Заказ id=$order_id не найден",true);
        }

        $is=$_is=0;
        $ds=$_ds=0;

        if(!empty($this->adminCfg['purchase'])){
            $dbf=$this->adminCfg['purchase']['DBF_pprice'];
            $d=$this->fetchAll("SELECT amount, price, $dbf FROM os_item WHERE NOT LD AND order_id=$order_id");
            if(!empty($d))
                foreach($d as $v){
                    $is+=$v['amount']*$v['price'];
                    $_is+=$v['amount']*$v[$dbf];
                }

            if(!empty($this->adminCfg['purchase']['DBF_dop_pprice'])){
                $dbf=$this->adminCfg['purchase']['DBF_dop_pprice'];
                $d=$this->fetchAll("SELECT amount, price, $dbf FROM os_dop WHERE NOT LD AND order_id=$order_id");
                if(!empty($d))
                    foreach($d as $v){
                        $ds+=$v['amount']*$v['price'];
                        $_ds+=$v['amount']*$v[$dbf];
                    }
            }else {
                $d=$this->fetchAll("SELECT amount, price FROM os_dop WHERE NOT LD AND order_id=$order_id");
                if(!empty($d))
                    foreach($d as $v){
                        $ds+=$v['amount']*$v['price'];
                    }
            }
        }else{
            $d=$this->fetchAll("SELECT amount, price FROM os_item WHERE NOT LD AND order_id=$order_id");
            if(!empty($d))
                foreach($d as $v){
                    $is+=$v['amount']*$v['price'];
                }
            $d=$this->fetchAll("SELECT amount, price FROM os_dop WHERE NOT LD AND order_id=$order_id");
            if(!empty($d))
                foreach($d as $v){
                    $ds+=$v['amount']*$v['price'];
                }
        }
        $cost=ceil(($is+$ds)-($is+$ds)*$od['discount']/100)+$od['delivery_cost'];
        $bcost=ceil($is+$ds);

        $this->query("UPDATE os_order SET cost='$cost', bcost='$bcost' WHERE order_id=$order_id");

        return array('cost'=>$cost*1,'bcost'=>$bcost*1,'is'=>$is,'ds'=>$ds,'discount'=>$od['discount'],'pcost'=>($_is+$_ds)*1);
    }



    /*
     * общая часть для каждого обработчика ссмена состояний заказа
     * возможна смена состония без авторихации юзера, тогда cUserId останется не изменным и не будет выполнена проверка режима isolatedChanges
     */
    function _changeState($order_id, $newStateId, $param=array())
    {
        $order_id=(int)$order_id;
        $newStateId=(int)$newStateId;

        $d=$this->getOne("SELECT * FROM os_order WHERE LD!=1 AND order_id=$order_id");
        if($d===0){
            return $this->putMsg(false,"[_changeState()]:: Заказ id=$order_id не найден");
        }

        $this->CHS_prevState=$d['state_id'];

        $this->initOrderStatesByUser();

        if(CU::$userId){
            if($d['cUserId']!=0 && @$this->_orderStates[$d['state_id']]['isolatedChanges'] && CU::$userId != $d['cUserId']){
                return $this->putMsg(false, "Смена состояния заказа {$d['order_num']} может производить только обслуживающий его сотрудник");
            }
            $cUserId=CU::$userId;
        } else{
            $cUserId=$d['cUserId'];
        }

        if(isset(App_TFields::$fields['os_order']['method'])){
            if(!in_array($d['method'],$this->_orderStates[$newStateId]['method'])){
                return $this->putMsg(false,"Метод оплаты в этом заказе не соотвествует выбранному состоянию.");
            }
        }

        if(@is_array($this->_orderStates[$newStateId]['allowFrom']) && in_array($d['state_id'], $this->_orderStates[$newStateId]['allowFrom']) || !isset($this->_orderStates[$d['state_id']])){
            $r= array(
                'dt_state'=>$dt=Tools::dt(),
                'state_id'=>$newStateId
            );
            if(!@$this->_orderStates[$newStateId]['keepCUser'] || $d['cUserId']==0) $r['cUserId']=$cUserId;
            $this->update('os_order', $r, "order_id=$order_id");

            $this->insert('os_slog', array(
                'order_id'=>$order_id,
                'orderCUserId'=>$d['cUserId'],
                'createdById'=>CU::$userId,
                'old_state_id'=>$d['state_id'],
                'new_state_id'=>$newStateId,
                'dt_added'=>$dt,
                'msg'=>!empty($param['slogMsg'])?Tools::esc($param['slogMsg']):'',
                'protected'=>1
            ));

            $this->lastAddedSLogId=$this->lastId();

        }else{
            return $this->putMsg(false, "Такой вариант смены состояния для заказа {$d['order_num']} не предусмотрен.");
        }

        $this->CHS_cUserId=$cUserId;
        return true;

    }

    // обработчик $orderStates['handler']
    function changeState_New($order_id, $newStateId)
    {
        if(!$this->_changeState($order_id, $newStateId)) return false;

        return true;
    }

    // обработчик $orderStates['handler']
    function changeState_Processing($order_id, $newStateId)
    {
        if(!$this->_changeState($order_id, $newStateId)) return false;

        return true;
    }

    // обработчик $orderStates['handler']
    function changeState_Delivered($order_id, $newStateId)
    {
        if(!$this->_changeState($order_id, $newStateId)) return false;

        return true;
    }

    // обработчик $orderStates['handler']
    function changeState_Cancel($order_id, $newStateId, $data=array())
    {
        if(isset($this->adminCfg['cancelReasons'])){
            $order_id=(int)$order_id;
            if(!empty($data['reason'])) {
                $reason="[{$data['reason']}]";
                $_reason=$this->adminCfg['cancelReasons'][$data['reason']];
            }
            elseif(mb_strlen(trim(@$data['reason_str']))>5) $_reason=$reason=trim($data['reason_str']);
            else{
                return $this->putMsg(false, 'Укажите причину отмены заказа');
            }
            if(!$this->_changeState($order_id, $newStateId, array('slogMsg'=>$_reason))) return false;
            $this->update('os_order', array(App_TFields::$fields['os_order']['cancelReason']['as']=>Tools::esc($reason)), "order_id='$order_id'");
        }else
            if(!$this->_changeState($order_id, $newStateId)) return false;

        return true;
    }



    /*
     * getPurchaseForPeriod (0|1) - рассчитать прибыль, выручку  за указанный период
     * search - поиск по данным клиента
     * order_num - поиск по номеру заказа
     * order_id - поиск по id заказа
     * searchItems - поиск по товару в заказе
     * state_id
     * from,to -диапазон дат
     * rangeBy - поле по которому будет применено from, to
     * cUserId
     * sort - сортировка (def ==cmsOrderTableSort)
     * start, limit - кол-вл строк
     *
     * mode - {'','detail'}     выборка включая товары в заказах
     *
     * beforeProceedTime - включить в запрос подзапрос с вычисленным временем с момента размещения заказа до взятия в обработку менеджром. В качестве ключа - (int)  state_id статуса обработки
     *
     * @return
     * если getPurchaseForPeriod  то array('newOrders'=>int, 'proceeds'=>float, 'total'=>in, ['profit'=>float, 'expense'=>float])
     * иначе array('total'=>integer, 'newOrders'=>int)
     *
     */
    function ordersList($r)
    {
        $q=array();

        $joinItems='';

        if (@$r['search']!='') {
            $s=trim(Tools::cutDoubleSpaces(Tools::esc($r['search'])));
            $s=explode(' ',$s);
            $search=array();
            foreach($s as $v){
                $search[]="os_order.name LIKE '%$v%' OR os_order.email LIKE '%$v%' OR os_order.addr LIKE '%$v%'";
            }
            $q[]='('.implode(' OR ',$search).')';

        }
        elseif (@$r['order_num']!='') $q[]="os_order.order_num='".Tools::esc(trim(str_replace(Cfg::get('orderPrefix'),'',$r['order_num'])))."'";
        elseif (@$r['order_id']) $q[]="os_order.order_id='".(int)$r['order_id']."'";
        else{
            if (@$r['searchItems']!=''){
                $s=trim(Tools::cutDoubleSpaces(Tools::esc($r['searchItems'])));
                $s=explode(' ',$s);
                $search=array();
                foreach($s as $v){
                    $search[]="os_item.name LIKE '%$v%' OR os_item.cat_id = '$v'";
                }
                $q[]='('.implode(' OR ',$search).')';
                $joinItems="JOIN os_item USING (order_id)";
                $q[]="NOT os_item.LD";
            }

            if (@$r['user_id']) $q[]="os_user.user_id ='".(int)$r['user_id']."'";

            if(empty($r['rangeBy'])){
                if (@$r['from']!='') $q[]="{$this->cmsOrderDatesField} >= '".Tools::esc($r['from'])."'";
                if (@$r['to']!='') $q[]="{$this->cmsOrderDatesField} <= '".Tools::esc($r['to'])."'";
            }else{
                if (@$r['from']!='') $q[]="{$r['rangeBy']} >= '".Tools::esc($r['from'])."'";
                if (@$r['to']!='') $q[]="{$r['rangeBy']} <= '".Tools::esc($r['to'])."'";
            }
            if (!empty($r['state_id'])) {
                $states=array();
                foreach($r['state_id'] as $v) $states[]=(int)$v;
                $q[]="os_order.state_id IN (".implode(',',$states).")";
            }

            if(@$r['cUserId']){
                // если указан r[cUserId] то подливаются заказы state_id=0 (state_id=cmsInjectStatesWithCUser0)
                if(!empty($this->cmsInjectStatesWithCUser0)){
                    $in=implode(',',$this->cmsInjectStatesWithCUser0);
                    $q[]="(os_order.cUserId=".(int)$r['cUserId']." OR os_order.state_id IN($in))";
                }else{
                    $q[]="os_order.cUserId=".(int)$r['cUserId'];
                }
            }
        }


        $result=array('total'=>0);

        if(!empty($r['getPurchaseForPeriod']) && !empty($this->adminCfg['purchase']['DBF_pprice'])){

            $qq=implode(' AND ',$q);
            if(!empty($qq)) $qq=' AND '.$qq; else $qq='';

            $psum=$sum=0;

            $pf=$this->adminCfg['purchase']['DBF_pprice'];
            $this->query("SELECT os_item.$pf, os_item.price, os_item.amount  FROM os_order JOIN os_item USING (order_id) WHERE os_order.LD=0 AND NOT os_item.LD $qq ");
            while($this->next()!==false){
                $qr=$this->qrow;
                if($qr[$pf]==0) $psum+=$qr['price']*$qr['amount']; else $psum+=$qr[$pf]*$qr['amount'];
                $sum+=$qr['price']*$qr['amount'];
            }

            if(!empty($this->adminCfg['purchase']['dopPPriceEnabled']) && !empty($this->adminCfg['purchase']['DBF_dop_pprice']) && empty($joinItems)){
                $pf=$this->adminCfg['purchase']['DBF_dop_pprice'];
                $this->query("SELECT os_dop.$pf, os_dop.price, os_dop.amount  FROM os_order JOIN os_dop USING (order_id) WHERE os_order.LD=0 AND NOT os_dop.LD $qq ");
                while($this->next()!==false){
                    $qr=$this->qrow;
                    if($qr[$pf]==0) $psum+=$qr['price']*$qr['amount']; else $psum+=$qr[$pf]*$qr['amount'];
                    $sum+=$qr['price']*$qr['amount'];
                }
            }

            $result+=array('proceeds'=>$sum, 'profit'=>ceil($sum-$psum),'expense'=>$psum);
        }

        if (@$r['sort']=='') $sort=$this->cmsOrderTableSort; else $sort=Tools::esc($r['sort']);

        if(@$r['mode']=='detail'){

            $result=array_merge(
                $this->_ordersListDetail(array(
                    'sort'=>$sort,
                    'q'=>$q
                )),
                $result
            );

        }else{

            $limit='';
            if(isset($r['start'])) {
                $start=abs(intval($r['start']));
                $limit="LIMIT $start";
            }
            if(!empty($r['limit'])){
                $lim=(abs(intval($r['limit'])));
                if(!empty($limit)) $limit.=", $lim"; else $limit="LIMIT $lim";
            }

            $w='';
            if(!empty($r['beforeProceedTime'])){
                $sid=(int)$r['beforeProceedTime'];
                $w=", (SELECT ((TO_SECONDS(os_slog.dt_added)-TO_SECONDS(os_order.dt_add)) DIV 60) FROM os_slog WHERE os_slog.order_id=os_order.order_id AND os_slog.new_state_id=$sid AND os_slog.old_state_id=0 LIMIT 1) AS proceedMinutes";
            }

            $qq=implode(' AND ',$q);
            if(!empty($qq)) $qq=' AND '.$qq; else $qq='';

            if(!empty($limit)){
                $num=$this->query("SELECT os_order.state_id, os_order.cost, os_order.bcost, os_order.delivery_cost{$w} FROM os_order $joinItems WHERE os_order.LD=0 $qq GROUP BY os_order.order_id");
                $result['total']=$this->qnum();
                $result=array_merge($this->_ordersList(),$result);
            }

            $this->query("SELECT os_order.*, (SELECT count(*) FROM os_slog WHERE os_slog.order_id=os_order.order_id AND msg!='') AS slogNum{$w} FROM os_order $joinItems WHERE os_order.LD=0 $qq GROUP BY os_order.order_id ORDER BY $sort $limit");

            if(!isset($num)) {
                $result['total']=$this->qnum();
                $result=array_merge($this->_ordersList(),$result);
            }
        }

        return $result;
    }

    /*
     * получение списка заказов с товарными позициями
     *
     * $r{
     *      @sort    string     строка ORDER BY __      require
     *      @q      string      строка WHERE __
     */
    function _ordersListDetail($r=array())
    {
        $qq=implode(' AND ',$r['q']);
        if(!empty($qq)) $qq=' AND '.$qq; else $qq='';

        $result=array('orders'=>array(),'items'=>array(),'dops'=>array());

        $result['orders']=$this->fetchAll("SELECT os_order.* FROM os_order WHERE os_order.LD=0 $qq ORDER BY {$r['sort']}", MYSQLI_ASSOC);
        $result['total']=count($result['orders']);

        $this->query("SELECT os_item.* FROM os_order JOIN os_item USING (order_id) WHERE os_order.LD=0 AND NOT os_item.LD $qq ORDER BY {$r['sort']}");
        while($this->next(MYSQLI_ASSOC)!==false){
            $oid=$this->qrow['order_id'];
            unset($this->qrow['order_id'],$this->qrow['LD']);
            $result['items'][$oid][]=$this->qrow;
        }

        $this->query("SELECT os_dop.* FROM os_order JOIN os_dop USING (order_id) WHERE os_order.LD=0 AND NOT os_dop.LD $qq ORDER BY {$r['sort']}");
        while($this->next(MYSQLI_ASSOC)!==false){
            $oid=$this->qrow['order_id'];
            unset($this->qrow['order_id'],$this->qrow['LD']);
            $result['dops'][$oid][]=$this->qrow;
        }


        return $result;
    }

    /*
     * @return array(NewOrders,Proceeds)
     */
    private function _ordersList()
    {
        $result=array('newOrders'=>0);

        $result['proceeds']=0;

        if($this->qnum()) {
            while($this->next()!=false){
                if($this->qrow['state_id']==0) {
                    $result['newOrders']++;
                }
                $result['proceeds']+=$this->qrow['cost']-$this->qrow['delivery_cost'];
            }
            $this->first();
        }
        return $result;
    }


    function min_od()
    {
        $this->query("SELECT min(dt_add) FROM os_order WHERE LD=0");
        $this->next(MYSQL_NUM);
        if($this->qrow[0]!=0){
            preg_match("/([0-9]{4})-([0-9]{2})-([0-9]{2})/",$this->qrow[0],$m);
            $this->min_d=$m[3];
            $this->min_m=$m[2];
            $this->min_y=$m[1];
            return($this->qrow[0]);
        }else return(date("now"));
    }

    function max_od()
    {
        $this->query("SELECT max(dt_add) FROM os_order WHERE LD=0");
        $this->next(MYSQL_NUM);
        if($this->qrow[0]!=0){
            preg_match("/([0-9]{4})-([0-9]{2})-([0-9]{2})/",$this->qrow[0],$m);
            $this->max_d=$m[3];
            $this->max_m=$m[2];
            $this->max_y=$m[1];
            return($this->qrow[0]);
        }else return(date("now"));
    }

    /*
     * получаем список допов
     */
    function listDOP($order_id,$item_id=0)
    {
        $q=array();
        $q[]='NOT LD';
        if(!empty($order_id)) $q[]="order_id=".(int)$order_id;
        if($item_id>0) $q[]="item_id=".(int)$item_id; elseif($item_id!=='all') $q[]="item_id=0";
        $q=implode(' AND ',$q);

        $d=$this->fetchAll("SELECT * FROM os_dop WHERE $q ORDER BY name");
        foreach($d as &$v){
            $v['name']=Tools::unesc($v['name']);
        }
        //echo $this->sql_query;
        return $d;
    }

    function que($qname,$cond1='',$cond2='',$cond3='',$cond4='')
    {
        switch ($qname)
        {
            case 'order_by_id':
                $cond1=(int)$cond1;
                $res=$this->query("SELECT os_order.* FROM os_order LEFT JOIN os_user ON os_order.user_id = os_user.user_id WHERE os_order.LD!=1 AND os_order.order_id='$cond1'");
                break;
            case 'item_list':
                $cond1=(int)$cond1;
                $res=$this->query("SELECT * FROM os_item WHERE  order_id='$cond1' AND NOT LD ORDER BY gr,name");
                if($this->qnum()) {
                    $res=$this->fetchAll('',MYSQLI_ASSOC);
                    $this->first();
                } else $res=array();
                break;
            case 'new_order_now':
                $dt1=date("Y-m-d").' 00:00:00';
                $dt2=date("Y-m-d").' 23:59:59';
                $res=$this->query("SELECT count(order_id) FROM os_order WHERE LD=0 AND dt_add>'$dt1' AND dt_add<'$dt2'");
                $this->next();
                $res=$this->qrow[0];
                break;

            default: $res=false;
        }
        return $res;
    }

    /*
     * подготовка переменных для заполенения шаблонов
     */
    function getOrderData($order_id)
    {
        $order_id = (int)$order_id;

        $r = $this->getOne("SELECT * FROM os_order WHERE LD!=1 AND order_id=$order_id", MYSQLI_ASSOC);

        if ($r === 0) return false;

        foreach ($r as &$v) {
            $v = Tools::unesc($v);
        }

        $r['order_date'] = Tools::sdate($r['dt_add']);
        $r['orderDateDots'] = Tools::sdate($r['dt_add'], '.');
        $r['orderDateRus'] = $this->russianDate($r['dt_add']) . 'г.';
        $r['order_dt'] = Tools::sDateTime($r['dt_add']);

        $r['_itog'] = $r['cost'];
        $r['itog'] = Tools::nn($r['cost']);
        $r['_delivery_cost'] = $r['delivery_cost'];
        $r['delivery_cost'] = Tools::nn($r['delivery_cost']);
        $r['discount'] = $r['discount'] * 1;
        $r['itogPropis'] = $this->num2str($r['cost']);

        $r['siteName'] = Url::trimWWW(Cfg::get('site_name'));
        $r['site_name'] = Cfg::get('site_name');
        $r['emailInfo'] = Data::get('mail_info');
        $r['emailOrder'] = Data::get('mail_order');

        $r['orderNum'] = Cfg::get('orderPrefix') . ' ' . $r['order_num'];

        $cusers=CU::usersList(array('includeLD'=>1));

        if ($r['createdBy']) {
            $r['mgr_CreatorFullName'] = $cusers[$r['createdBy']]['fullName'];
        } else {
            $r['mgr_CreatorFullName'] = '';
        }
        if ($r['cUserId']) {
            $r['mgr_FullName'] = $cusers[$r['cUserId']]['fullName'];
            $r['mgr_shortName'] = $cusers[$r['cUserId']]['shortName'];
            $r['mgr_email'] = $cusers[$r['cUserId']]['email'];
        } else {
            $r['mgr_FullName'] = '';
            $r['mgr_shortName'] = '';
            $r['mgr_email'] = '';
        }

        $imgs = array();
        $d = $this->que('item_list', $order_id);
        $r['list'] = array();
        $r['summa'] = 0;
        foreach ($d as $v) {
            $meta = Tools::DB_unserialize($v['meta']);
            $r['list'][$v['item_id']] = array(
                'cat_id' => $v['cat_id'],
                'gr' => $v['gr'],
                'name' => $v['name'],
                'amount' => $v['amount'],
                'price' => Tools::nn($v['price']),
                '_price' => $v['price'],
                'sum' => Tools::nn($v['amount'] * $v['price']),
                '_sum' => $v['amount'] * $v['price'],
                'spez' => @$meta['spez'] ? true : false,
                'dop' => array(),
                'turl' => 'http://' . Cfg::get('site_url') . ($v['gr'] == 1 ? App_SUrl::tTipo($v['cat_id']) : App_SUrl::dTipo($v['cat_id'])).'?from=mail'
            );
            $r['summa'] += $v['amount'] * $v['price'];
            $imgs[] = $v['cat_id'];
        }

        $r['dops'] = $this->listDOP($order_id, 'all');
        foreach ($r['dops'] as $k => $v) {
            $r['summa'] += $v['amount'] * $v['price'];
            $r['dops'][$k]['_price'] = $v['price'];
            $r['dops'][$k]['_sum'] = $v['price'] * $v['amount'];
            $r['dops'][$k]['sum'] = Tools::nn($v['price'] * $v['amount']);
            $r['dops'][$k]['price'] = Tools::nn($v['price']);
        }

        $r['_summa'] = $r['summa']; // полная сумма с допами, но без скидки и доставки
        $r['summa'] = Tools::nn($r['summa']); // полная сумма с допами, но без скидки и доставки

        // скидка в рублях
        $r['_discountRUR']=ceil($r['_summa']*$r['discount']/100);
        $r['discountRUR']=Tools::nn($r['_discountRUR']);
        // итог без скидки
        $r['_itogExDiscount']=$r['_summa']-$r['_discountRUR'];
        $r['_itogExDiscount']=Tools::nn($r['_itogExDiscount']);

        return $r;

    }

    function exportPDF ($order_id, $__doc, $__output)
    {
        if(!isset($this->docCfg['pdf'][$__doc])) die("[".get_called_class()."::exportPDF]:: doc not exist [pdf][$__doc].");

        if(empty($order_id) || ($odata=$this->getOrderData($order_id))===false) die("[".get_called_class()."::exportPDF]:: order $order_id not found.");

        if(!is_file(Cfg::_get('root_path')."/app/templates/{$this->docCfg['pdf'][$__doc]['tn']}")) die("[".get_called_class()."::exportPDF]:: template {$this->docCfg['pdf'][$__doc]['tn']} not found.");

        extract($odata);

        ob_start();
        include Cfg::_get('root_path')."/app/templates/{$this->docCfg['pdf'][$__doc]['tn']}";
        $html=ob_get_clean();

        include_once Cfg::_get('root_path').'/inc/mpdf/mpdf.php';

        $doc=$this->docCfg['pdf'][$__doc]['mpdfInit'];
        $mpdf=new mPDF($doc['mode'],$doc['format'],$doc['defFontSize'],$doc['defFont'],$doc['marginLeft'],$doc['marginRight'],$doc['marginTop'],$doc['marginBottom'],$doc['marginHeader'],$doc['marginFooter']);
        $mpdf->addPage($doc['orient']);
        $mpdf->WriteHTML($html);

        if($__output=='file'){
            $fn='output.pdf';
            eval("\$fn=\"".$this->docCfg['pdf'][$__doc]['fn']."\";");
            $mpdf->Output($fn,'D');
        }elseif(preg_match("~^[0-9\sa-zа-я\-_\(\),\.]+\.pdf$~iu",$__output)){
            $mpdf->Output(Cfg::_get('root_path').'/tmp/'.Tools::cp1251($__output));
        }else{
            $mpdf->Output();
        }

        unset($mpdf);
    }

    /*
     * отправка документа в теле письма
     * отправитель - авторизованный юзер или robot@
     */
    function exportEmailInBody ($order_id, $__doc, $__emailTo, $__subject='', $sign='')
    {
        $__x=explode('.', $__doc);

        if(!isset($this->docCfg[$__x[0]][@$__x[1]])) return $this->putMsg(false, "[".get_called_class()."::exportEmailInBody]:: документ не найден [$__doc]");

        if(empty($order_id) || ($r=$this->getOrderData($order_id))===false) return $this->putMsg(false, "[".get_called_class()."::exportEmailInBody]:: заказ $order_id не найден");

        if(!is_file(Cfg::_get('root_path')."/app/templates/{$this->docCfg[$__x[0]][@$__x[1]]['tn']}")) return $this->putMsg(false, "[".get_called_class()."::exportEmailInBody]:: файл шаблона {$this->docCfg[$__x[0]][@$__x[1]]['tn']} не найден");

        if(empty($__subject)){
            extract($r);
            $subj=$this->docCfg[$__x[0]][@$__x[1]]['mailSubject'];
            eval("\$subj=\"".$this->docCfg[$__x[0]][@$__x[1]]['mailSubject']."\";");
        }else $subj=$__subject;

        $host=Data::get('mail_robot_host');

        if(empty(CU::$email)){
            $emailFrom=trim(Data::get('mail_robot'));
            if($emailFrom=='' || mb_strpos($emailFrom,'@')===false) $emailFrom='no-reply@'.str_replace('www.','',$_SERVER['SERVER_NAME']);
            $fromName=Cfg::get('site_name');
        }else{
            if($host!=''){
                $emailFrom=Data::get('mail_robot');
            }else{
                $emailFrom=CU::$email;
            }
            $fromName=CU::$fullName;
        }

        if(!empty($sign)) {
            if(!preg_match("~[<>]~u",$sign)) $sign=nl2br($sign);
            $r['sign']=$sign;
        }


        $res=Mailer::sendmail(array(
            'fromAddr'=>$emailFrom,
            'fromName'=>$fromName,
            'toAddr'=>$__emailTo,
            'body'=>$r,
            'subject'=>$subj,
            'tpl'=>$this->docCfg[$__x[0]][@$__x[1]]['tn'],
            'charset'=>Data::get('order_mail_charset'),
            'host'=>$host,
            'logpw'=>Data::get('mail_robot_logpw'),
            'SMTPSecure'=>Data::get('mail_robot_smtp_secure')
        ));

        if(!$res) return $this->putMsg(false, "[".get_called_class()."::exportEmailInBody.Mailer]:: ".Mailer::$errors);

        return true;

    }

    /*
     * отправка пачки документов как вложение с телом письма $__body
     * docs - array -список документов в виде pdf.orderDetail (т.е. путь разделенный точкой)
     * отправитель - авторизованный юзер или robot@
     *
     * метод работает пока только с PDF
     */
    function exportEmailMulti ($order_id, $__docs, $__emailTo, $__subject, $__body, $__bodyIsHtml=false)
    {
        if(empty($order_id) || ($r=$this->getOrderData($order_id))===false) return $this->putMsg(false, "[".get_called_class()."::exportEmailMulti]:: заказ $order_id не найден");

        extract($r);

        include_once Cfg::_get('root_path').'/inc/mpdf/mpdf.php';

        $__attach=array();

        foreach($__docs as $v){
            $__x=explode('.', $v);

            if($__x[0]=='html') return $this->putMsg(false, "[".get_called_class()."::exportEmailMulti]:: неверный тип документа $v");

            if(!isset($this->docCfg[$__x[0]][@$__x[1]])) return $this->putMsg(false, "[".get_called_class()."::exportEmailMulti]:: документ $v не найден");

            if(!is_file(Cfg::_get('root_path')."/app/templates/{$this->docCfg[$__x[0]][$__x[1]]['tn']}")) return $this->putMsg(false, "[".get_called_class()."::exportEmailMulti]:: файл шаблона {$this->docCfg[$__x[0]][$__x[1]]['tn']} не найден");

            ob_start();
            include Cfg::_get('root_path')."/app/templates/{$this->docCfg[$__x[0]][$__x[1]]['tn']}";
            $html=ob_get_clean();

            $doc=$this->docCfg[$__x[0]][$__x[1]]['mpdfInit'];
            $mpdf=new mPDF($doc['mode'],$doc['format'],$doc['defFontSize'],$doc['defFont'],$doc['marginLeft'],$doc['marginRight'],$doc['marginTop'],$doc['marginBottom'],$doc['marginHeader'],$doc['marginFooter']);
            $mpdf->addPage($doc['orient']);
            $mpdf->WriteHTML($html);

            $tempFN=Cfg::_get('root_path').'/tmp/'.Tools::randString(10);
            $mpdf->Output($tempFN);
            unset($mpdf);

            $fn=$this->docCfg[$__x[0]][$__x[1]]['fn'];
            eval("\$fn=\"".$this->docCfg[$__x[0]][@$__x[1]]['fn']."\";");
            $__attach[]=array($tempFN, $fn);
        }

        $host=Data::get('mail_robot_host');
        if(empty(CU::$email)){
            $emailFrom=trim(Data::get('mail_robot'));
            if($emailFrom=='' || mb_strpos($emailFrom,'@')===false) $emailFrom='no-reply@'.str_replace('www.','',$_SERVER['SERVER_NAME']);
            $fromName=Cfg::get('site_name');
        }else{
            if($host!=''){
                $emailFrom=Data::get('mail_robot');
            }else{
                $emailFrom=CU::$email;
            }
            $fromName=CU::$fullName;
        }

        if(!$__bodyIsHtml) $__body=nl2br($__body);
        if(empty($__body)) $__body=' ';

        $res=Mailer::sendmail(array(
            'fromAddr'=>$emailFrom,
            'fromName'=>$fromName,
            'toAddr'=>$__emailTo,
            'subject'=>$__subject,
            'body'=>$__body,
            'attachments'=>$__attach,
            'charset'=>Data::get('order_mail_charset'),
            'host'=>Data::get('mail_robot_host'),
            'logpw'=>Data::get('mail_robot_logpw'),
            'SMTPSecure'=>Data::get('mail_robot_smtp_secure'),
            'debug'=>0
        ));

        foreach($__attach as $v){
            @unlink($v[0]);
        }
        if(!$res) return $this->putMsg(false, "[".get_called_class()."::exportEmailMulti.Mailer]:: ".Mailer::$errors);


        return true;

    }

    /*
     * на вход дата "2012-12-03" или "03-12-2012"
     * на выходе "03 декабря 2012"
     */
    function russianDate($date)
    {
        $date = explode("-", Tools::sdate($date));
        switch ($date[1]) {
            case 1: $m = 'января';
                break;
            case 2: $m = 'февраля';
                break;
            case 3: $m = 'марта';
                break;
            case 4: $m = 'апреля';
                break;
            case 5: $m = 'мая';
                break;
            case 6: $m = 'июня';
                break;
            case 7: $m = 'июля';
                break;
            case 8: $m = 'августа';
                break;
            case 9: $m = 'сентября';
                break;
            case 10: $m = 'октября';
                break;
            case 11: $m = 'ноября';
                break;
            case 12: $m = 'декабря';
                break;
        }
        return $date[0] . ' ' . $m . ' ' . $date[2];
    }

    /**
     * Склоняем словоформу
     * @ author runcore
     */
    private function _morf($n, $f1, $f2, $f5) {
        $n = abs(intval($n)) % 100;
        if ($n>10 && $n<20) return $f5;
        $n = $n % 10;
        if ($n>1 && $n<5) return $f2;
        if ($n==1) return $f1;
        return $f5;
    }


    /**
     * Возвращает сумму прописью
     * @author runcore
     * @uses morph(...)
     */
    function num2str($num) {
        $nul='ноль';
        $ten=array(
            array('','один','два','три','четыре','пять','шесть','семь', 'восемь','девять'),
            array('','одна','две','три','четыре','пять','шесть','семь', 'восемь','девять'),
        );
        $a20=array('десять','одиннадцать','двенадцать','тринадцать','четырнадцать' ,'пятнадцать','шестнадцать','семнадцать','восемнадцать','девятнадцать');
        $tens=array(2=>'двадцать','тридцать','сорок','пятьдесят','шестьдесят','семьдесят' ,'восемьдесят','девяносто');
        $hundred=array('','сто','двести','триста','четыреста','пятьсот','шестьсот', 'семьсот','восемьсот','девятьсот');
        $unit=array( // Units
            array('копейка' ,'копейки' ,'копеек',	 1),
            array('рубль'   ,'рубля'   ,'рублей'    ,0),
            array('тысяча'  ,'тысячи'  ,'тысяч'     ,1),
            array('миллион' ,'миллиона','миллионов' ,0),
            array('миллиард','милиарда','миллиардов',0),
        );
        //
        list($rub,$kop) = explode('.',sprintf("%015.2f", floatval($num)));
        $out = array();
        if (intval($rub)>0) {
            foreach(str_split($rub,3) as $uk=>$v) { // by 3 symbols
                if (!intval($v)) continue;
                $uk = sizeof($unit)-$uk-1; // unit key
                $gender = $unit[$uk][3];
                list($i1,$i2,$i3) = array_map('intval',str_split($v,1));
                // mega-logic
                $out[] = $hundred[$i1]; # 1xx-9xx
                if ($i2>1) $out[]= $tens[$i2].' '.$ten[$gender][$i3]; # 20-99
                else $out[]= $i2>0 ? $a20[$i3] : $ten[$gender][$i3]; # 10-19 | 1-9
                // units without rub & kop
                if ($uk>1) $out[]= $this->_morf($v,$unit[$uk][0],$unit[$uk][1],$unit[$uk][2]);
            } //foreach
        }
        else $out[] = $nul;
        $out[] = $this->_morf(intval($rub), $unit[1][0],$unit[1][1],$unit[1][2]); // rub
        $out[] = $kop.' '.$this->_morf($kop,$unit[0][0],$unit[0][1],$unit[0][2]); // kop
        return trim(preg_replace('/ {2,}/', ' ', join(' ',$out)));
    }

    /*
     * получить лог и файлы для заказа
     * параметры
     * order_id
     * mode: array(
     *  'logs',  # только логи
     *  'files',  # только файлы
     *  'log&files' #default  скомбинировать логи и файлы с сортировкой по dt_added
     * )
     *
     * order  - (asc|desc) - всегда по dt_added
     * whereSLog - доп условие отбора
     * whereFiles - доп условие отбора
     *
     */
    public function getSLogs($r = array())
    {
        $order_id=(int)@$r['order_id'];
        if (empty($order_id)) {
            return $this->putMsg(false, '[Orders.getSLog()]:: Не передан ID заказа');
        }
        $d = $this->getOne("SELECT cUserId FROM os_order WHERE LD!=1 AND order_id=$order_id");
        if ($d === 0) {
            return  $this->putMsg(false, "[Orders.getSLog()]:: Заказ $order_id не найден");
        }

        $users = CU::usersList(array('includeLD' => true));
        if (empty($this->_orderStates)) $this->initOrderStatesByUser();

        if (empty($r['order'])) $order = "desc"; else $order = $r['order'];

        if(!empty($r['whereSLog'])) $qSLog=" AND {$r['whereSLog']}"; else $qSLog='';
        if(!empty($r['whereFiles'])) $qFiles=" AND {$r['whereFiles']}"; else $qFiles='';

        if(@$r['mode']=='logs'){
            $d = $this->fetchAll("SELECT * FROM os_slog WHERE order_id=$order_id $qSLog ORDER BY dt_added $order", MYSQLI_ASSOC);
            if (empty($d)) {
                return ['data'=>[],'numFiles'=>0, 'numLogs'=>0];
            }

            foreach ($d as &$v) {
                $v['msg'] = nl2br(Tools::html($v['msg']));
                $v['protectedMsg'] = Tools::html($v['protectedMsg']);
                $v['dt_added'] = Tools::sDateTime($v['dt_added']);
                $v['dt_upd'] = Tools::sDateTime($v['dt_upd']);
                $v['old_state'] = @$this->_orderStates[$v['old_state_id']]['label'];
                $v['new_state'] = @$this->_orderStates[$v['new_state_id']]['label'];
                $v['createdBy_shortName'] = @$users[$v['createdById']]['shortName'];
                $v['createdBy_fullName'] = @$users[$v['createdById']]['fullName'];
            }
            $numLogs=count($d);

        } elseif(@$r['mode']=='files'){
            $d = $this->fetchAll("SELECT * FROM os_files WHERE order_id=$order_id $qFiles ORDER BY dt_added $order", MYSQLI_ASSOC);
            if (empty($d)) {
                return ['data'=>[],'numFiles'=>0, 'numLogs'=>0];
            }

            foreach ($d as &$v) {
                $v['msg'] = nl2br(Tools::html($v['msg']));
                $v['dt_added'] = Tools::sDateTime($v['dt_added']);
                $v['dt_upd'] = Tools::sDateTime($v['dt_upd']);
                $v['state'] = @$this->_orderStates[$v['state_id']]['label'];
                $v['createdBy_shortName'] = @$users[$v['createdById']]['shortName'];
                $v['createdBy_fullName'] = @$users[$v['createdById']]['fullName'];
            }

            $numFiles=count($d);

        } else {
            $d1 = $this->fetchAll("SELECT *, UNIX_TIMESTAMP(dt_added) AS dt_added_ts FROM os_slog WHERE order_id=$order_id $qSLog ORDER BY dt_added $order", MYSQLI_ASSOC);
            $d2 = $this->fetchAll("SELECT *, UNIX_TIMESTAMP(dt_added) AS dt_added_ts FROM os_files WHERE order_id=$order_id $qFiles ORDER BY dt_added $order", MYSQLI_ASSOC);
            $d=array();
            $i=0;
            foreach ($d1 as $v) {
                $d[$v['dt_added_ts'].'_'.++$i]=array_merge($v,array(
                    'type'=>'log',
                    'msg' => nl2br(Tools::html($v['msg'])),
                    'protectedMsg' => Tools::html($v['protectedMsg']),
                    'dt_added' => Tools::sDateTime($v['dt_added']),
                    'dt_upd' => Tools::sDateTime($v['dt_upd']),
                    'old_state' => @$this->_orderStates[$v['old_state_id']]['label'],
                    'new_state' => @$this->_orderStates[$v['new_state_id']]['label'],
                    'createdBy_shortName' => @$users[$v['createdById']]['shortName'],
                    'createdBy_fullName' => @$users[$v['createdById']]['fullName']
                ));
            }
            foreach ($d2 as $v) {
                $d[$v['dt_added_ts'].'_'.++$i]=array_merge($v,array(
                    'type'=>'file',
                    'msg' => nl2br(Tools::html($v['msg'])),
                    'dt_added' => Tools::sDateTime($v['dt_added']),
                    'dt_upd' => Tools::sDateTime($v['dt_upd']),
                    'state' => @$this->_orderStates[$v['state_id']]['label'],
                    'createdBy_shortName' => @$users[$v['createdById']]['shortName'],
                    'createdBy_fullName' => @$users[$v['createdById']]['fullName']
                ));
            }
            $numFiles=count($d2);
            $numLogs=count($d1);
            unset($d1,$d2);
            if($order=='asc') ksort($d,SORT_NUMERIC); else krsort($d,SORT_NUMERIC);
        }

        unset($users);

        return array('data'=>$d,'numFiles'=>@$numFiles, 'numLogs'=>@$numLogs);
    }

    /*
     * добавляет новую запись с файлом для заказа
     * вход:
     * order_id
     * uploadedFileFieldName - $_FILES[uploadedFileFieldName]=array(name,tmp_name,error)
     * title
     * msg
     * protected
     *
     *
     * формат файла на сервере: /assets/Cfg[orderFilesDir]/[год]/[месяц]/[order_id].[ext]
     *
     * возвращает array() с созданной записью (кроме поля fname)
     */
    public function addOrderFile($r=array())
    {
        $order_id=(int)@$r['order_id'];
        if (empty($order_id)) {
            return $this->putMsg(false, '[Orders.addOrderFile()]:: Не передан ID заказа');
        }
        if(empty($r['uploadedFileFieldName'])) $r['uploadedFileFieldName']='file';

        $sfile=@$_FILES[$r['uploadedFileFieldName']]['tmp_name'];
        $sname=@$_FILES[$r['uploadedFileFieldName']]['name'];

        if(!is_uploaded_file($sfile)){
            return $this->putMsg(false, '[Orders.addOrderFile()]:: Загруженный файл не найден на сервере');
        }

        $finfo=pathinfo($sname);
        $finfo['extension']=(@$finfo['extension']);
        $finfo['filename']=($finfo['filename']);

        if(Tools::mb_strcasecmp($finfo['extension'],'php')===0){
            @unlink($r['source']);
            return $this->putMsg(false, '[Orders.addOrderFile()]:: Недопустимое расширение файла '.basename($sname));
        }
        if(!empty($r['title']))
            $title=htmlentities(Tools::stripTags($r['title']), ENT_QUOTES);
        else
            $title=$finfo['filename'].(!empty($finfo['extension'])?".{$finfo['extension']}":'');

        if(!empty($finfo['extension'])) $ext='.'.mb_strtolower($finfo['extension']); else $ext='';

        $d = $this->getOne("SELECT UNIX_TIMESTAMP(dt_add) AS dt, state_id FROM os_order WHERE LD!=1 AND order_id=$order_id");
        if ($d === 0) {
            return $this->putMsg(false, "[Orders.addOrderFile()]:: Заказ $order_id не найден");
        }

        $fy=date('Y', $d['dt']);
        $fm=date('m', $d['dt']);

        $this->insert('os_files',array(
            'order_id'=>$order_id,
            'createdById'=>CU::$userId,
            'dt_added'=>Tools::dt(),
            'state_id'=>$d['state_id'],
            'msg'=>Tools::esc(@$r['msg']),
            'title'=>Tools::esc($title),
            'protected'=>(int)@$r['protected'],
            'hash'=>Tools::randString(32)
        ));
        $id=$this->lastId();

        $dir=Cfg::$config['root_path'] . '/' . Cfg::$config['orderFilesDir']. '/'. $fy. '/'. $fm . '/';
        Tools::tree_mkdir($dir);

        if(!@move_uploaded_file($sfile, $dest=$dir."$id$ext")){
            @unlink($r['source']);
            return $this->putMsg(false, '[Orders.addOrderFile()]::не могу скопировать загруженный файл в папку назначения');
        }

        $this->update('os_files', array(
            'fname'=>'/'.Cfg::$config['orderFilesDir']."/$fy/$fm/$id$ext"
        ),"id=$id");

        @unlink($r['source']);

        return $this->getOrderFileById($id);
    }

    public function getOrderFileById($id)
    {
        $id=(int)$id;
        $d=$this->getOne("SELECT * FROM os_files WHERE id=$id", MYSQLI_ASSOC);
        if(empty($d)) {
            return $this->putMsg(false, "[Orders.getOrderFileById()]:: Не найдена запись файла $id");
        }

        $users = CU::usersList(array('includeLD' => true));
        if (empty($this->_orderStates)) $this->initOrderStatesByUser();

        $d['dt_added']=Tools::sDateTime($d['dt_added']);
        $d['dt_upd']=Tools::sDateTime($d['dt_upd']);
        $d['msg'] = nl2br(Tools::html($d['msg']));
        $d['state'] = @$this->_orderStates[$d['state_id']]['label'];
        $d['createdBy_shortName'] = @$users[$d['createdById']]['shortName'];
        $d['createdBy_fullName'] = @$users[$d['createdById']]['fullName'];
        unset($d['fname']);

        return $d;
    }

    public function openOrderFileByHash($hash)
    {
        $hash=Tools::esc($hash);
        if(empty($hash)){
            return $this->putMsg(false, "[Orders.openOrderFileByHash()]:: Не корректный параметр");
        }
        $d=$this->getOne("SELECT * FROM os_files WHERE hash='$hash'", MYSQLI_ASSOC);
        if(empty($d)) {
            return $this->putMsg(false, "[Orders.openOrderFileByHash()]:: Не найдена запись файла $hash");
        }

        $file=Cfg::$config['root_path'].$d['fname'];
        if(!is_file($file)){
            return $this->putMsg(false, "[Orders.openOrderFileByHash()]:: Файл не найден");
        }

        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'.($d['title']!=''?$d['title']:basename($file)).'"');
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file));
        readfile($file);
        exit;
    }

    /*
    * на вход:
    * mode: (edit,del)
    * msg
    * id
    *
    * права проверяются здесь.
    * создавать записи могут все без ограничения но
    * Изменять может только создавший запись
     * редактируем только msg
    *
    * возвращает array() с измененной записи (кроме поля fname)  или true|false для mode=del
    */
    public function modOrderFile($r=array())
    {
        $mode=@$r['mode'];
        if (!in_array($mode,array('edit','del'))) {
            return $this->putMsg(false, '[Orders.modOrderFile()]:: Не ясен режим обработки '.$mode);
        }

        $id=(int)@$r['id'];
        if (empty($id)) {
            return $this->putMsg(false, '[Orders.modOrderFile()]:: Не передан ID записи');
        }
        $d=$this->getOne("SELECT * FROM os_files WHERE id=$id", MYSQLI_ASSOC);
        if(empty($d)){
            return $this->putMsg(false, "[Orders.modOrderFile()]:: Не найдена запись $id");
        }

        if(CU::$userId != $d['createdById']) {
            return $this->putMsg(false, "[Orders.modOrderFile()]:: У вас нет прав на эту операцию");
        }

        $msg=trim(@$r['msg']);

        if($mode=='edit'){
            $this->update('os_files',array(
                'dt_upd'=>Tools::dt(),
                'msg'=>$d['msg']=Tools::esc($msg)
            ),"id=$id");

            return $this->getOrderFileById($id);

        }elseif($mode=='del'){
            if(is_file(Cfg::$config['root_path'].$d['fname']) && !@unlink(Cfg::$config['root_path'].$d['fname'])){
                return $this->putMsg(false, "[Orders.modOrderFile()]:: Файл не удаляется");
            }else{
                return $this->del('os_files','id',$id);
            }
        }

        return false;
    }


    /*
     * на вход:
     * order_id  если mode=add
     * mode: (add,edit,del)
     * msg
     * id - если mode==(del,edit)
     * protected    {0|1}
     * cUserId  int подмена юзера
     *
     * права проверяются здесь.
     * создавать записи могут все без ограничения но
     * Изменять может только создавший запись
     * если protected==true, то 1. msg может быть пустое   2. msg может менять создавший пользователь   3. удалять не может
     *
    * возвращает array() с созданной/измененной записью или true|false для mode=del
     */
    public function modSLog($r=array())
    {
        $mode=@$r['mode'];
        if (!in_array($mode,array('add','edit','del'))) {
            return $this->putMsg(false, '[Orders.modSLog()]:: Не ясен режим обработки '.$mode);
        }

        if(isset($r['cUserId'])) $cUserId=(int)$r['cUserId']; else $cUserId=CU::$userId;


        if($mode!='add'){
            $id=(int)@$r['id'];
            if (empty($id)) {
                return $this->putMsg(false, '[Orders.modSLog()]:: Не передан ID записи');
            }
            $d=$this->getOne("SELECT * FROM os_slog WHERE id=$id", MYSQLI_ASSOC);
            if(empty($d)){
                return $this->putMsg(false, "[Orders.modSLog()]:: Не найдена запись $id");
            }
        }else{
            $order_id=(int)@$r['order_id'];
            if (empty($r['order_id'])) {
                return $this->putMsg(false, '[Orders.modSLog()]:: Не передан ID заказа');
            }
            $od = $this->getOne("SELECT cUserId,state_id FROM os_order WHERE LD!=1 AND order_id=$order_id");
            if ($od === 0) {
                return  $this->putMsg(false, "[Orders.modSLog()]:: Заказ $order_id не найден");
            }
        }

        if($mode!='add' && $cUserId != $d['createdById']) {
            return $this->putMsg(false, "[Orders.modSLog()]:: У вас нет прав на эту операцию");
        }

        $msg=trim(@$r['msg']);

        if($mode=='edit' && !$d['protected'] && empty($msg) || $mode=='add' && empty($msg)){
            return $this->putMsg(false, '[Orders.modSLog()]:: Пустое сообщение');
        }

        if($mode=='add'){
            $this->insert('os_slog',array(
                'order_id'=>$order_id,
                'orderCUserId'=>$od['cUserId'],
                'createdById'=>$cUserId,
                'old_state_id'=>$od['state_id'],
                'new_state_id'=>$od['state_id'],
                'dt_added'=>Tools::dt(),
                'protected'=>(int)@$r['protected'],
                'msg'=>Tools::esc($msg)
            ));
            $this->lastAddedSLogId=$id=$this->lastId();
            return $this->getSLogById($id);

        }elseif($mode=='edit'){
            $this->update('os_slog',array(
                'dt_upd'=>Tools::dt(),
                'msg'=>Tools::esc($msg)
            ),"id=$id");
            return $this->getSLogById($id);

        }elseif($mode=='del'){
            if($d['protected']) {
                return $this->putMsg(false, "[Orders.modSLog()]:: У вас нет прав на эту операцию");
            }

            return $this->del('os_slog','id',$id);
        }

        return false;

    }

    public function getSLogById($id)
    {
        $id=(int)$id;
        $d=$this->getOne("SELECT * FROM os_slog WHERE id=$id", MYSQLI_ASSOC);
        if(empty($d)) {
            return $this->putMsg(false, "[Orders.getSLogById()]:: Не найдена запись $id");
        }

        $users = CU::usersList(array('includeLD' => true));
        if (empty($this->_orderStates)) $this->initOrderStatesByUser();

        $d['dt_added']=Tools::sDateTime($d['dt_added']);
        $d['dt_upd']=Tools::sDateTime($d['dt_upd']);
        $d['msg'] = nl2br(Tools::html($d['msg']));
        $d['protectedMsg'] = Tools::html($d['protectedMsg']);
        $d['old_state'] = @$this->_orderStates[$d['old_state_id']]['label'];
        $d['new_state'] = @$this->_orderStates[$d['new_state_id']]['label'];
        $d['createdBy_shortName'] = @$users[$d['createdById']]['shortName'];
        $d['createdBy_fullName'] = @$users[$d['createdById']]['fullName'];

        return $d;
    }

    public function newOrderNum()
    {
        $orderNum = $this->getOne("SELECT max(order_num) FROM os_order");
        if (!$orderNum[0]) $orderNum = 1001; else $orderNum = $orderNum[0] + 1;
        return $orderNum;
    }

    public function createNewOrder()
    {
        $this->initOrderStatesByUser();
        if(empty($this->_orderStates[0])){
            return $this->putMsg(false, "[Orders.createNewOrder()]:: Не настроена опция NEXT. Заказ не создан.");
        }
        $orderNum=$this->newOrderNum();
        if($this->insert('os_order', array(
            'order_num'=>$orderNum,
            'createdBy'=>CU::$userId,
            'cUserId'=>CU::$userId,
            'ip'=>$_SERVER['REMOTE_ADDR'],
            'dt_add'=>Tools::dt(),
            'LD'=>2,
            'state_id'=>$this->_orderStates[0]['next']
        ))){
            return $this->lastId();
        } else {
            return false;
        }
    }

    public function confirmNewOrder($order_id)
    {
        $order_id=(int)$order_id;
        $d=$this->getOne("SELECT LD,cUserId FROM os_order WHERE order_id=$order_id");
        if(@$d['LD']!=2) {
            return $this->putMsg(false, "[Orders.confirmNewOrder()]:: Не найден неподтвержденный заказ ID=$order_id");
        }

        if($d['cUserId']!=CU::$userId){
            return $this->putMsg(false, "[Orders.confirmNewOrder()]:: Нет прав на подтверждение заказа ID=$order_id");
        }

        if($this->update('os_order', array('LD'=>0), "order_id=$order_id") && $this->updatedNum()){
            return true;
        } else {
            return false;
        }
    }

    public function cancelNewOrder($order_id)
    {
        $order_id=(int)$order_id;

        $d=$this->getOne("SELECT LD,cUserId FROM os_order WHERE order_id=$order_id");
        if(@$d['LD']!=2) {
            return $this->putMsg(false, "[Orders.cancelNewOrder()]:: Заказ ID=$order_id не найден или уже подтвержден");
        }

        if($d['cUserId']!=CU::$userId){
            return $this->putMsg(false, "[Orders.confirmNewOrder()]:: Нет прав на подтверждение заказа ID=$order_id");
        }

        $d=$this->fetchAll("SELECT id FROM os_files WHERE order_id=$order_id");
        foreach($d as $v)
            if(!$this->modOrderFile(array('mode'=>'del', 'id'=>$v['id']))) return false;

        $this->del('os_item','order_id',$order_id);
        $this->del('os_dop','order_id',$order_id);
        $this->del('os_slog','order_id',$order_id);
        $this->del('os_order','order_id',$order_id);
        return true;
    }

    /*
    * вызов из frm/orders_c.php
    */
    function ordersExportCSV($r)
    {
        $this->initOrderStatesByUser();
        if(isset(App_TFields::$fields['os_order']['ptype'])) $ptypeOn=1; else $ptypeOn=0;
        if(isset(App_TFields::$fields['os_order']['method'])) $methodOn=1; else $methodOn=0;
        $cUsers=CU::usersList(array('includeLD'=>1));
        $drivers=CU::usersList(array('driversOnly'=>true));

        $this->ordersList($r);

        header("Content-type: text/csv");
        header("Content-Disposition: attachment; filename=orders-".Cfg::get('site_name').".csv");
        header("Pragma: no-cache");
        header("Expires: 0");

        $output = fopen("php://output", "w");

        $r=array(
            '№ заказа',
            'Дата заказа',
            'Время заказа'
        );
        if($ptypeOn) $r[]='Метод оплаты';
        if($methodOn) $r[]='Тип';
        $r[]='Клиент';
        $r[]='Город';
        $r[]='Адрес';
        $r[]='E-Mail';
        $r[]='Сумма заказа, р.';
        $r[]='Доставка, р.';
        $r[]='Скидка,%';
        $r[]='Состояние';
        $r[]='Менеджер';
        $r[]='Оформил';
        $r[]='Коммент менеджера';
        if(!empty($this->adminCfg['drivers']['DBF_driverId'])) $r[]='Водитель';
        if(!empty($this->adminCfg['delivery']['DBF_deliveryDate'])) $r[]='Дата доставки';
        if(isset(App_TFields::$fields['os_order']['carrier_co'])) $r[]='ТК';
        if(!empty($this->adminCfg['delivery']['DBF_TTN'])) $r[]='ТТН';
        $r[]='Минут до обработки';
        $r[]='Марка авто';
        fputcsv($output, array_cp1251($r), ';');

        while($this->next(MYSQLI_ASSOC)!==false){
            $r=array(
                Tools::unesc($this->qrow['order_num']),
                Tools::sdate($this->qrow['dt_add']),
                Tools::stime($this->qrow['dt_add'])
            );
            if($ptypeOn)
                $r[]=isset(App_TFields::$fields['os_order']['ptype']['varList2'][$this->qrow['ptype']]) ? App_TFields::$fields['os_order']['ptype']['varList2'][$this->qrow['ptype']] : "!error!{$this->qrow['ptype']}!";

            if($methodOn)
                $r[]=isset(App_TFields::$fields['os_order']['method']['varList2'][$this->qrow['method']]) ? App_TFields::$fields['os_order']['method']['varList2'][$this->qrow['method']] : "!error!{$this->qrow['method']}!";

            $r[]=Tools::unesc($this->qrow['name']);
            $r[]=Tools::unesc($this->qrow['city']);
            $r[]=Tools::unesc(trim($this->qrow['tel1'].' '.$this->qrow['tel2'].' '.$this->qrow['addr']));
            $r[]=Tools::unesc($this->qrow['email']);
            $r[]=Tools::n($this->qrow['cost']);
            $r[]=Tools::n($this->qrow['delivery_cost']);
            $r[]=Tools::n($this->qrow['discount']);

            $r[]=isset($this->_orderStates[$this->qrow['state_id']]) ? $this->_orderStates[$this->qrow['state_id']]['label'] : "!error!{$this->qrow['state_id']}!";

            $r[]=isset($cUsers[$this->qrow['cUserId']]) && $this->qrow['cUserId'] ? $cUsers[$this->qrow['cUserId']]['fullName'] : ($this->qrow['cUserId']!=0 ? "!error!{$this->qrow['cUserId']}!" : '');

            $r[]=isset($cUsers[$this->qrow['createdBy']]) && $this->qrow['createdBy'] ? $cUsers[$this->qrow['createdBy']]['fullName']:($this->qrow['createdBy']==0?'клиент':"!error!{$this->qrow['createdBy']}!");

            $r[]=Tools::unesc($this->qrow['tech']);

            if(!empty($this->adminCfg['drivers']['DBF_driverId']))
                $r[]=isset($drivers[$this->qrow[$this->adminCfg['drivers']['DBF_driverId']]]) && $this->qrow[$this->adminCfg['drivers']['DBF_driverId']] ? $drivers[$this->qrow[$this->adminCfg['drivers']['DBF_driverId']]]['fullName'] : ($this->qrow[$this->adminCfg['drivers']['DBF_driverId']]!=0 ? "!error!{$this->qrow[$this->adminCfg['drivers']['DBF_driverId']]}!":'');

            if(!empty($this->adminCfg['delivery']['DBF_deliveryDate']))
                $r[]=Tools::sdate($this->qrow[$this->adminCfg['delivery']['DBF_deliveryDate']]);

            if(isset(App_TFields::$fields['os_order']['carrier_co']))
                $r[]=Tools::unesc($this->qrow['carrier_co']);

            if(!empty($this->adminCfg['delivery']['DBF_TTN']))
                $r[]=Tools::unesc($this->qrow[$this->adminCfg['delivery']['DBF_TTN']]);

            $r[]=$this->qrow['proceedMinutes'];

            $r[]=Tools::unesc($this->qrow['avto_name']);

            fputcsv($output, array_cp1251($r), ';');
        }

        fclose($output);

    }

    /*
    * вызов из frm/orders_c.php
    */
    function ordersExportCSVDetail($r)
    {
        $this->initOrderStatesByUser();
        if(isset(App_TFields::$fields['os_order']['ptype'])) $ptypeOn=1; else $ptypeOn=0;
        if(isset(App_TFields::$fields['os_order']['method'])) $methodOn=1; else $methodOn=0;
        $cUsers=CU::usersList(array('includeLD'=>1));
        $drivers=CU::usersList(array('driversOnly'=>true));
        $cc = new CC_Base();
        $suplrs = $cc->suplrList(array());

        $d=$this->ordersList($r);

        header("Content-type: text/csv");
        header("Content-Disposition: attachment; filename=ordersDetail-".Cfg::get('site_name').".csv");
        header("Pragma: no-cache");
        header("Expires: 0");

        $output = fopen("php://output", "w");

        $r=array(
            '№ заказа',
            'Дата заказа',
            'Время заказа'
        );
        if($ptypeOn) $r[]='Метод оплаты';
        if($methodOn) $r[]='Тип';
        $r[]='Клиент';
        $r[]='Город';
        $r[]='Адрес';
        $r[]='E-Mail';
        $r[]='Сумма заказа, р.';
        $r[]='Доставка, р.';
        $r[]='Скидка,%';
        $r[]='Марка авто';
        $r[]='Состояние';
        $r[]='Менеджер';
        $r[]='Оформил';
        if(!empty($this->adminCfg['drivers']['DBF_driverId'])) $r[]='Водитель';
        if(!empty($this->adminCfg['delivery']['DBF_deliveryDate'])) $r[]='Дата доставки';
        if(!empty($this->adminCfg['delivery']['DBF_TTN'])) $r[]='ТТН';
        $r[]='Код товара';
        $r[]='Наименование';
        $r[]='Кол-во';
        $r[]='Цена за шт.';
        $r[]='Сумма, р';
        if(!empty($this->adminCfg['purchase']['DBF_pprice'])) {
            $r[]='Закуп за шт.';
            $r[]='Сумма закупа';
        }
        if(!empty($this->adminCfg['reservation']['DBF_suplrId'])) {
            $r[]='Поставщик';
            if(!empty($this->adminCfg['reservation']['DBF_reserveNum'])) $r[]='Номер резерва';
            if(!empty($this->adminCfg['reservation']['DBF_reserveDate'])) $r[]='Дата резерва';
        }
        fputcsv($output, array_cp1251($r), ';');

        foreach($d['orders'] as $o){
            $r=array(
                Tools::unesc($o['order_num']),
                Tools::sdate($o['dt_add']),
                Tools::stime($o['dt_add'])
            );
            if($ptypeOn)
                $r[]=isset(App_TFields::$fields['os_order']['ptype']['varList2'][$o['ptype']]) ? App_TFields::$fields['os_order']['ptype']['varList2'][$o['ptype']] : "!error!{$o['ptype']}!";

            if($methodOn)
                $r[]=isset(App_TFields::$fields['os_order']['method']['varList2'][$o['method']]) ? App_TFields::$fields['os_order']['method']['varList2'][$o['method']] : "!error!{$o['method']}!";

            $r[]=Tools::unesc($o['name']);
            $r[]=Tools::unesc($o['city']);
            $r[]=Tools::unesc(trim($o['tel1'].' '.$o['tel2'].' '.$o['addr']));
            $r[]=Tools::unesc($o['email']);
            $r[]=Tools::n($o['cost']);
            $r[]=Tools::n($o['delivery_cost']);
            $r[]=Tools::n($o['discount']);
            $r[]=Tools::unesc($o['avto_name']);

            $r[]=isset($this->_orderStates[$o['state_id']]) ? $this->_orderStates[$o['state_id']]['label'] : "!error!{$o['state_id']}!";

            $r[]=isset($cUsers[$o['cUserId']]) && $o['cUserId'] ? $cUsers[$o['cUserId']]['fullName'] : ($o['cUserId']!=0 ? "!error!{$o['cUserId']}!" : '');

            $r[]=isset($cUsers[$o['createdBy']]) && $o['createdBy'] ? $cUsers[$o['createdBy']]['fullName']:($o['createdBy']==0?'клиент':"!error!{$o['createdBy']}!");

            if(!empty($this->adminCfg['drivers']['DBF_driverId']))
                $r[]=isset($drivers[$o[$this->adminCfg['drivers']['DBF_driverId']]]) && $o[$this->adminCfg['drivers']['DBF_driverId']] ? $drivers[$o[$this->adminCfg['drivers']['DBF_driverId']]]['fullName'] : ($o[$this->adminCfg['drivers']['DBF_driverId']]!=0 ? "!error!{$o[$this->adminCfg['drivers']['DBF_driverId']]}!":'');

            if(!empty($this->adminCfg['delivery']['DBF_deliveryDate']))
                $r[]=Tools::sdate($o[$this->adminCfg['delivery']['DBF_deliveryDate']]);
            if(!empty($this->adminCfg['delivery']['DBF_TTN']))
                $r[]=Tools::unesc($o[$this->adminCfg['delivery']['DBF_TTN']]);

            if(!empty($d['items'][$o['order_id']])) foreach($d['items'][$o['order_id']] as $oi){
                $ri=array();
                $ri[]=$oi['cat_id'];
                $ri[]=Tools::unesc($oi['name']);
                $ri[]=$oi['amount'];
                $ri[]=Tools::n($oi['price']);
                $ri[]=Tools::n($oi['price']*$oi['amount']);
                if(!empty($this->adminCfg['purchase']['DBF_pprice'])) {
                    $ri[]=Tools::n($oi[$this->adminCfg['purchase']['DBF_pprice']]);
                    $ri[]=Tools::n($oi[$this->adminCfg['purchase']['DBF_pprice']]*$oi['amount']);
                }
                if(!empty($this->adminCfg['reservation']['DBF_suplrId'])) {
                    $ri[]=@$suplrs[$oi[$this->adminCfg['reservation']['DBF_suplrId']]]['name'];
                    if(!empty($this->adminCfg['reservation']['DBF_reserveNum'])) $ri[]=Tools::unesc($oi[$this->adminCfg['reservation']['DBF_reserveNum']]);
                    if(!empty($this->adminCfg['reservation']['DBF_reserveDate'])) $ri[]=Tools::sdate($oi[$this->adminCfg['reservation']['DBF_reserveDate']]);
                }

                fputcsv($output, array_cp1251(array_merge($r,$ri)), ';');
            }

            if(!empty($d['dops'][$o['order_id']])) foreach($d['dops'][$o['order_id']] as $oi){
                $ri=array();
                $ri[]='';
                $ri[]=Tools::unesc($oi['name']);
                $ri[]=$oi['amount'];
                $ri[]=Tools::n($oi['price']);
                $ri[]=Tools::n($oi['price']*$oi['amount']);
                if(!empty($this->adminCfg['purchase']['DBF_pprice'])) {
                    $ri[]=Tools::n($oi[$this->adminCfg['purchase']['DBF_pprice']]);
                    $ri[]=Tools::n($oi[$this->adminCfg['purchase']['DBF_pprice']]*$oi['amount']);
                }
                if(!empty($this->adminCfg['reservation']['DBF_suplrId'])) {
                    $ri[]='';
                    if(!empty($this->adminCfg['reservation']['DBF_reserveNum'])) $ri[]='';
                    if(!empty($this->adminCfg['reservation']['DBF_reserveDate'])) $ri[]='';
                }

                fputcsv($output, array_cp1251(array_merge($r,$ri)), ';');
            }

        }

        fclose($output);

    }

    /*
     * автоматическая смена статуса заказов
     * adminCfg['phoenix'] {
            simple: {
               'fromStateId'=>2,
                'toStateId'=>0,
                'interval'=>30
            }

            для этого типа в App_TFields['os_order'] должно быть описание поля 'delayedOn'
            delayedOrders: {
                'fromStateId'=>2,
                'toStateId'=>0
            }
        }
     */
    public function phoenix()
    {
        if(!isset($this->adminCfg['phoenix'])) return true;

        $orders=array();

        if(!empty($this->adminCfg['phoenix']['simple']))
        {
            $orders['simple']=array();

            $from=@(int)$this->adminCfg['phoenix']['simple']['fromStateId'];
            $to=@(int)$this->adminCfg['phoenix']['simple']['toStateId'];
            $int=@(int)$this->adminCfg['phoenix']['simple']['interval'];  // в минутах

            $d=$this->fetchAll("SELECT order_id, order_num FROM os_order WHERE NOT LD AND state_id='$from' AND dt_state < DATE_SUB(NOW(), INTERVAL $int MINUTE)", MYSQLI_ASSOC);
            foreach($d as $v)
            {
                $orders['simple'][]=$v['order_num'];
                $this->update('os_order',array('state_id'=>$to,'dt_state'=>Tools::dt()),"order_id='{$v['order_id']}'");

                $this->modSLog(array(
                    'mode'=>'add',
                    'protected'=>1,
                    'msg'=>'автовозврат из *'.$this->orderStates[$from]['label'].'*',
                    'order_id'=>$v['order_id'],
                    'cUserId'=>0
                ));
            }
        }

        if(!empty($this->adminCfg['phoenix']['delayedOrders']))
        {
            $orders['delayedOrders']=array();
            $from=@(int)$this->adminCfg['phoenix']['delayedOrders']['fromStateId'];
            $to=@(int)$this->adminCfg['phoenix']['delayedOrders']['toStateId'];
            $fld=App_TFields::$fields['os_order']['delayedOn']['as'];

            $d=$this->fetchAll("SELECT order_id, order_num, dt_add, dt_state FROM os_order WHERE NOT LD AND state_id='$from' AND $fld <= NOW() ", MYSQLI_ASSOC);
            foreach($d as $v)
            {
                $orders['delayedOrders'][]=$v['order_num'];

                $dtState=Tools::sdate($v['dt_state'],'-',true).' '.Tools::stime($v['dt_state'],':',true);

                $this->query("UPDATE os_order SET state_id = '$to', dt_state= NOW(), tech = CONCAT ('[ ЗАКАЗ БЫЛ ОТЛОЖЕН $dtState ]\n\n', tech) WHERE order_id='{$v['order_id']}'");

                $this->modSLog(array(
                    'mode'=>'add',
                    'protected'=>1,
                    'msg'=>'автовозврат из *'.$this->orderStates[$from]['label'].'*',
                    'order_id'=>$v['order_id'],
                    'cUserId'=>0
                ));
            }
        }

        return $orders;
    }

    /*
     * подсказка дается только если поставщик в заказе еще не выбран
     * критерий рекомендации:
     *  кол-во на складе >= suplrHinting.minSuplrSC AND >= кол-во из заказа
     *  минимальная закупка у поставщика
     * возвращает уникальные пары {gr,suplrName}
     */
    public function suplrSuggest($order_id)
    {
        if(empty($this->adminCfg['purchase']['suplrHinting']) || empty($this->adminCfg['reservation']['DBF_suplrId'])) return false;

        if(!($minSC=(int)@$this->adminCfg['purchase']['suplrHinting']['minSuplrSC'])) $minSC=8;
        if(!($priceField=(int)@$this->adminCfg['purchase']['suplrHinting']['DBF_suplrPrice'])) $priceField='price1';
        $db=new DB();
        $spf=$this->adminCfg['reservation']['DBF_suplrId'];
        $order_id=(int)$order_id;
        $d=$db->fetchAll("SELECT DISTINCT os_item.gr, (SELECT cc_suplr.name FROM cc_cat_sc JOIN cc_suplr USING (suplr_id) WHERE cc_cat_sc.cat_id=os_item.cat_id AND cc_cat_sc.sc>=$minSC AND cc_cat_sc.sc>=os_item.amount ORDER BY cc_cat_sc.sc DESC, cc_cat_sc.$priceField ASC LIMIT 1) suplrName FROM os_item WHERE NOT os_item.LD AND os_item.{$spf}=0 AND os_item.order_id=$order_id HAVING suplrName!='' ORDER BY gr", MYSQLI_ASSOC);

        return $d;
    }

    /*
     * возвращает кол-во единиц товара вывозимых у поставщиков сегодня и на days дней вперед
     * анализируются заказы только со статусом "На доставке" (deliveringStateid)
     * Параметры:
     *  days - на сколько дней вперед смотреть, если 0 то не лимитируем
     *  stateId - статус "На доставке"
     *  suplrIds - ограничить поставщиком(ми)    int|array
     *  suplrNames - возвращать название поставщика, default=false
     *  today - включать сегодня
     *
     * на выходе строки
     * deliveryDate, dayNo (дней от сегодня), suplrId, [suplrName], itemsNum
     * сгруппированные по дате доставки, отсортированные по даате доставки и поставщику
     */
    public function futureSuplr($r=[])
    {

        if(empty($this->adminCfg['reservation']['DBF_suplrId']) || empty($this->adminCfg['delivery']['DBF_deliveryDate'])) return false;
        $stateId = (int)@$r['stateId'];
        if (empty($stateId)) return false;
        $days = (int)@$r['days'];
        if(!empty($r['suplrIds'])) {
            if (!is_array($r['suplrIds'])) $suplrIds = [(int)@$r['suplrIds']]; else $suplrIds = $r['suplrIds'];
            $suplrIds=implode(',',$suplrIds);
        }

        $db = new DB();

        $spf=$this->adminCfg['reservation']['DBF_suplrId'];
        $dlf=$this->adminCfg['delivery']['DBF_deliveryDate'];

        if (!empty($r['suplrNames'])) {

            $sql="SELECT os_order.$dlf AS deliveryDate, DATEDIFF(os_order.$dlf,NOW()) AS dayNo, os_item.$spf AS suplrId, cc_suplr.name AS suplrName, SUM(os_item.amount) AS itemsNum FROM os_item JOIN os_order USING (order_id) JOIN cc_suplr ON cc_suplr.suplr_id=os_item.$spf WHERE NOT os_order.LD " . (!empty($suplrIds)?" AND os_item.suplrId IN ($suplrIds) ":'') . (!empty($stateId) ? " AND os_order.state_id=$stateId" : '') . " AND os_order.deliveryDate " . (!empty($r['today']) ? '>=' : '>') . " DATE_ADD(current_date, INTERVAL 0 DAY) " . (!empty($days) ? "AND os_order.deliveryDate <= DATE_ADD(current_date, INTERVAL $days DAY)" : '') . " GROUP BY os_item.$spf, os_order.$dlf  ORDER BY os_order.$dlf, cc_suplr.name";

            $d = $db->fetchAll($sql, MYSQLI_ASSOC);

        } else {

            $sql="SELECT os_order.$dlf AS deliveryDate, DATEDIFF(os_order.$dlf,NOW()) AS dayNo, os_item.$spf AS suplrId, SUM(os_item.amount) AS itemsNum FROM os_item JOIN os_order USING (order_id) WHERE NOT os_order.LD " . (!empty($suplrIds)?" AND os_item.$spf IN ($suplrIds)":'') . (!empty($stateId) ? " AND os_order.state_id=$stateId" : '') . " AND os_order.deliveryDate " . (!empty($r['today']) ? '>=' : '>') . " DATE_ADD(current_date, INTERVAL 0 DAY) " . (!empty($days) ? "AND os_order.deliveryDate <= DATE_ADD(current_date, INTERVAL $days DAY)" : '') . " GROUP BY os_item.$spf, os_order.$dlf  ORDER BY os_order.$dlf, os_item.$spf";

            $d = $db->fetchAll($sql, MYSQLI_ASSOC);

        }

        unset($db);

        return $d;
    }
}
