<?

class App_Orders extends Orders_Base
{
    use TextParser;

    public $adminCfg = [
        'purchase'            => [

            // подсказки о подходящем поставщике в списке заказов (зависит от reservation.DBF_suplrId)
            'suplrHinting'  => [
                'oStates'        => [0, 5], // для каких статусов делаем подсказки по поставщикам
                'minSuplrSC'     => 8, // минимальное кол-во на складе у поставщика
                'DBF_suplrPrice' => 'price1'  // поле cc_cat_sc.priceX для выборки цен
            ], // инфа о "загрузке" поставщиков на days вперед (зависит от конфига delivery.DBF_deliveryDate, reservation.DBF_suplrId)
            'futureSuplr'   => [
                'days' => 3, 'deliveringStateId' => 13,
            ], 'DBF_pprice' => 'pprice', 'suplrSelectEnabled' => true, 'DBF_suplrPrice' => 'price1', 'dopPPriceEnabled' => true, 'DBF_dop_pprice' => 'pprice',
        ], 'reservation'      => [
            'DBF_suplrId' => 'suplrId', 'DBF_reserveNum' => 'reserveNum', 'DBF_reserveDate' => 'reserveDate',
        ], 'drivers'          => [
            'DBF_driverId' => 'driverId',
        ], 'delivery'         => [
            'DBF_deliveryDate' => 'deliveryDate', 'DBF_TTN' => 'TTN',
        ], 'suplrPaymentDate' => [
            'DBF_suplrPaymentDate' => 'suplrPaymentDate', 'roleIds' => [7],
        ], 'billDate'         => [
            'DBF_billDate' => 'billDate',
        ], 'ordersListLimit'  => 70, // автоматическое изменение статуса заказов
        'phoenix'             => [
            'simple'           => [
                'fromStateId' => 2, 'toStateId' => 0, 'interval' => 2,
            ], 'delayedOrders' => [
                'fromStateId' => -3, 'toStateId' => 0,
            ],
        ], /*
         * Варианты причин отказов для отмены заказов (array). Если нет этого параметра, то вопрос о причине отмены не задается
         */
        'cancelReasons'       => [
            'NN' => 'Нет в наличии со слов поставщика', 'ND' => 'Отказ на складе поставщика', 'NA' => 'Клиент не вышел на связь', 'DD' => 'Дубль заказа',
            'OC' => 'Клиент отказался: высокая цена', 'OP' => 'Клиент отказался: без объяснения причины', 'OK' => 'Клиент отказался: уже купил',
        ], 'delayedOrders'    => true,
    ];

    public $orderStates = [
        0   => [
            'label'               => 'новый', 'allowFrom' => [], 'actLabel' => 'сделать новым', 'editable' => true, 'handler' => 'changeState_New',
            'isolatedChanges'     => false, 'keepCUser' => false, 'customPerms' => [
                1    => [
                    'keepCUser' => true, 'editable' => true,
                ], 2 => [
                    'allowFrom' => [0, 2, 5, 10, 11, 13, 14, 15, 17, 20, 35, 38, 40, 42, -1, -3, -5, -9, -20], 'editable' => true, 'isolatedChanges' => false,
                ],
            ], 'cmsDefaultChk'    => 1, 'bgStyle' => ['background-color' => '#FFFFFF'], 'textStyle' => ['font-weight' => 'bold'], 'next' => 5,
            'excludeFromDropList' => true, 'method' => [0, 1],
        ], // новый
        2   => [
            'label'            => 'нет связи', 'allowFrom' => [0, 2, 5, 10, 11, 13, 14, 15, 17, 20, 35, 38, 40, 42, -1, -3, -5, -9, -20],
            'actLabel'         => 'нет связи', 'editable' => false, 'handler' => 'changeState_NoConnect', 'isolatedChanges' => false, 'keepCUser' => false,
            'customPerms'      => [
                1    => [
                    'allowFrom' => [0, 2, 5, 10, 11, 13, 14, 15, 17, 20, 35, 38, 40, 42, -1, -3, -5, -9, -20], 'keepCUser' => true,
                ], 2 => [
                    'allowFrom' => [0, 2, 5, 10, 11, 13, 14, 15, 17, 20, 35, 38, 40, 42, -1, -3, -5, -9, -20], 'editable' => true, 'isolatedChanges' => false,
                ],
            ], 'cmsDefaultChk' => 1, 'bgStyle' => ['background-color' => '#FFC'], 'textStyle' => '', 'method' => [0, 1],
        ],  // нет связи
        5   => [
            'label'            => 'в обработке', 'allowFrom' => [0, 2, 5, 10, 11, 13, 14, 15, 17, 20, 35, 38, 40, 42, -1, -3, -5, -9, -20],
            'actLabel'         => 'в обработку', 'editable' => true, 'handler' => 'changeState_Processing', 'isolatedChanges' => false, 'keepCUser' => false,
            'customPerms'      => [
                1    => [
                    'allowFrom' => [0, 2, 5, 10, 11, 13, 14, 15, 17, 20, 35, 38, 40, 42, -1, -3, -5, -9, -20], 'keepCUser' => true,
                ], 2 => [
                    'allowFrom' => [0, 2, 5, 10, 11, 13, 14, 15, 17, 20, 35, 38, 40, 42, -1, -3, -5, -9, -20], 'editable' => true, 'isolatedChanges' => false,
                ],
            ], 'cmsDefaultChk' => 1, 'bgStyle' => ['background-color' => '#EEE'], 'textStyle' => '', 'method' => [0, 1],
        ],  // в обработке
        10  => [
            'label'            => 'выставлен счет', 'allowFrom' => [0, 2, 5, 10, 11, 13, 14, 15, 17, 20, 35, 38, 40, 42, -1, -3, -5, -9],
            'actLabel'         => 'выставлен счет', 'editable' => true, 'handler' => 'changeState_Invoice', 'isolatedChanges' => false, 'keepCUser' => true,
            'customPerms'      => [
                1    => [
                    'allowFrom' => [0, 2, 5, 10, 11, 13, 14, 15, 17, 20, 35, 38, 40, 42, -1, -3, -5, -9],
                ], 2 => [
                    'allowFrom' => [0, 2, 5, 10, 11, 13, 14, 15, 17, 20, 35, 38, 40, 42, -1, -3, -5, -9], 'editable' => true, 'isolatedChanges' => false,
                ],
            ], 'cmsDefaultChk' => 1, 'bgStyle' => ['background-color' => '#0CF'], 'textStyle' => '', 'method' => [1],
        ],  // выставлен счет
        35  => [
            'label'            => 'отправлен номер карты', 'allowFrom' => [0, 2, 5, 10, 11, 13, 14, 15, 17, 20, 35, 38, 40, 42, -1, -3, -5, -9],
            'actLabel'         => 'отправлен номер карты', 'editable' => true, 'handler' => 'changeState_CardNumSent', 'isolatedChanges' => false,
            'keepCUser'        => true, 'customPerms' => [
                1    => [
                    'allowFrom' => [0, 2, 5, 10, 11, 13, 14, 15, 17, 20, 35, 38, 40, 42, -1, -3, -5, -9],
                ], 2 => [
                    'allowFrom' => [0, 2, 5, 10, 11, 13, 14, 15, 17, 20, 35, 38, 40, 42, -1, -3, -5, -9], 'editable' => true, 'isolatedChanges' => false,
                ],
            ], 'cmsDefaultChk' => 0, 'bgStyle' => ['background-color' => '#F7DD6A'], 'textStyle' => '', 'method' => [0, 1],
        ],  // Клиенту отправлен номер карты
        11  => [
            'label'            => 'оплачен по безналу', 'allowFrom' => [], 'actLabel' => 'оплачен по безналу', 'editable' => true,
            'handler'          => 'changeState_Paid', 'isolatedChanges' => false, 'keepCUser' => true, 'customPerms' => [
                1    => [
                    'allowFrom' => [0, 2, 5, 10, 11, 13, 14, 15, 17, 20, 35, 38, 40, 42, -1, -3, -5, -9],
                ], 5 => [
                    'allowFrom' => [0, 2, 5, 10, 11, 13, 14, 15, 17, 20, 35, 38, 40, 42, -1, -3, -5, -9],
                ], 2 => [
                    'allowFrom' => [0, 2, 5, 10, 11, 13, 14, 15, 17, 20, 35, 38, 40, 42, -1, -3, -5, -9], 'editable' => true, 'isolatedChanges' => false,
                ],
            ], 'cmsDefaultChk' => 1, 'bgStyle' => ['background-color' => '#82E6FF'], 'textStyle' => '', 'method' => [1],
        ],  // оплачен по безналу
        38  => [
            'label'            => 'оплачен на карту', 'allowFrom' => [], 'actLabel' => 'оплачен на карту', 'editable' => true,
            'handler'          => 'changeState_PaidOnCard', 'isolatedChanges' => false, 'keepCUser' => true, 'customPerms' => [
                1    => [
                    'allowFrom' => [0, 2, 5, 10, 11, 13, 14, 15, 17, 20, 35, 38, 40, 42, -1, -3, -5, -9],
                ], 5 => [
                    'allowFrom' => [0, 2, 5, 10, 11, 13, 14, 15, 17, 20, 35, 38, 40, 42, -1, -3, -5, -9],
                ], 2 => [
                    'allowFrom' => [0, 2, 5, 10, 11, 13, 14, 15, 17, 20, 35, 38, 40, 42, -1, -3, -5, -9], 'editable' => true, 'isolatedChanges' => false,
                ],
            ], 'cmsDefaultChk' => 1, 'bgStyle' => ['background-color' => '#E2E22B'], 'textStyle' => '', 'method' => [0, 1],
        ],  // Оплачен на карту
        40  => [
            'label'            => 'оплачен со слов клиента', 'allowFrom' => [0, 2, 5, 10, 11, 13, 14, 15, 17, 20, 35, 38, 40, 42, -1, -3, -5, -9],
            'actLabel'         => 'оплачен со слов клиента', 'editable' => true, 'handler' => 'changeState_PaidParole', 'isolatedChanges' => false,
            'keepCUser'        => true, 'customPerms' => [
                1    => [
                    'allowFrom' => [0, 2, 5, 10, 11, 13, 14, 15, 17, 20, 35, 38, 40, 42, -1, -3, -5, -9],
                ], 2 => [
                    'allowFrom' => [0, 2, 5, 10, 11, 13, 14, 15, 17, 20, 35, 38, 40, 42, -1, -3, -5, -9], 'editable' => true, 'isolatedChanges' => false,
                ],
            ], 'cmsDefaultChk' => 1, 'bgStyle' => ['background-color' => '#F4F43D'], 'textStyle' => '', 'method' => [0, 1],
        ],  // Оплачен со слов клиента == выставлен счет по цвету
        42  => [
            'label'            => 'клиент отправил предоплату', 'allowFrom' => [0, 2, 5, 10, 11, 13, 14, 15, 17, 20, 35, 38, 40, 42, -1, -3, -5, -9],
            'actLabel'         => 'клиент отправил предоплату', 'editable' => true, 'handler' => 'changeState_PrepaimentSent', 'isolatedChanges' => false,
            'keepCUser'        => true, 'customPerms' => [
                1    => [
                    'allowFrom' => [0, 2, 5, 10, 11, 13, 14, 15, 17, 20, 35, 38, 40, 42, -1, -3, -5, -9],
                ], 2 => [
                    'allowFrom' => [0, 2, 5, 10, 11, 13, 14, 15, 17, 20, 35, 38, 40, 42, -1, -3, -5, -9], 'editable' => true, 'isolatedChanges' => false,
                ],
            ], 'cmsDefaultChk' => 1, 'bgStyle' => ['background-color' => '#8998ED'], 'textStyle' => '', 'method' => [0, 1],
        ],  // Клиент отправил предоплату
        13  => [
            'label'            => 'на доставке', 'allowFrom' => [0, 2, 5, 10, 11, 13, 14, 15, 17, 20, 35, 38, 40, 42, -1, -3, -5, -9],
            'actLabel'         => 'на доставку', 'editable' => true, 'handler' => 'changeState_Delivering', 'isolatedChanges' => false, 'keepCUser' => true,
            'customPerms'      => [
                1    => [
                    'allowFrom' => [0, 2, 5, 10, 11, 13, 14, 15, 17, 20, 35, 38, 40, 42, -1, -3, -5, -9],
                ], 2 => [
                    'allowFrom' => [0, 2, 5, 10, 11, 13, 14, 15, 17, 20, 35, 38, 40, 42, -1, -3, -5, -9], 'editable' => true, 'isolatedChanges' => false,
                ],
            ], 'cmsDefaultChk' => 1, 'bgStyle' => ['background-color' => '#66CC99'], 'textStyle' => '', 'method' => [0, 1],
        ],  // на доставке
        14  => [
            'label'            => 'на доставке (карта)', 'allowFrom' => [0, 2, 5, 10, 11, 13, 14, 15, 17, 20, 35, 38, 40, 42, -1, -3, -5, -9],
            'actLabel'         => 'на доставку (карта)', 'editable' => true, 'handler' => 'changeState_DeliveringCard', 'isolatedChanges' => false,
            'keepCUser'        => true, 'customPerms' => [
                1    => [
                    'allowFrom' => [0, 2, 5, 10, 11, 13, 14, 15, 17, 20, 35, 38, 40, 42, -1, -3, -5, -9],
                ], 2 => [
                    'allowFrom' => [0, 2, 5, 10, 11, 13, 14, 15, 17, 20, 35, 38, 40, 42, -1, -3, -5, -9], 'editable' => true, 'isolatedChanges' => false,
                ],
            ], 'cmsDefaultChk' => 1, 'bgStyle' => ['background-color' => '#74E8AE'], 'textStyle' => '', 'method' => [0, 1],
        ],  // на доставке
        15  => [
            'label'            => 'доставлен', 'allowFrom' => [0, 2, 5, 10, 11, 13, 14, 15, 17, 20, 35, 38, 40, 42, -1, -3, -5, -9], 'actLabel' => 'доставлен',
            'editable'         => true, 'handler' => 'changeState_Delivered', 'keepCUser' => true, 'isolatedChanges' => false, 'customPerms' => [
                1    => [
                    'allowFrom' => [0, 2, 5, 10, 11, 13, 14, 15, 17, 20, 35, 38, 40, 42, -1, -3, -5, -9],
                ], 2 => [
                    'allowFrom' => [0, 2, 5, 10, 11, 13, 14, 15, 17, 20, 35, 38, 40, 42, -1, -3, -5, -9], 'editable' => true, 'isolatedChanges' => false,
                ],
            ], 'cmsDefaultChk' => 0, 'bgStyle' => ['background-color' => '#0C9'], 'textStyle' => '', 'method' => [0, 1],
        ],  // доставлен
        17  => [
            'label'            => 'клиент забрал', 'allowFrom' => [0, 2, 5, 10, 11, 13, 14, 15, 17, 20, 35, 38, 40, 42, -1, -3, -5, -9],
            'actLabel'         => 'клиент забрал', 'editable' => true, 'handler' => 'changeState_PickedUp', 'keepCUser' => true, 'isolatedChanges' => false,
            'customPerms'      => [
                1    => [
                    'allowFrom' => [0, 2, 5, 10, 11, 13, 14, 15, 17, 20, 35, 38, 40, 42, -1, -3, -5, -9],
                ], 2 => [
                    'allowFrom' => [0, 2, 5, 10, 11, 13, 14, 15, 17, 20, 35, 38, 40, 42, -1, -3, -5, -9], 'editable' => true, 'isolatedChanges' => false,
                ],
            ], 'cmsDefaultChk' => 0, 'bgStyle' => ['background-color' => '#0C9'], 'textStyle' => '', 'method' => [0, 1],
        ],  // самовывезен
        20  => [
            'label'            => 'закрыт', 'allowFrom' => [0, 2, 5, 10, 11, 13, 14, 15, 17, 20, 35, 38, 40, 42, -1, -3, -5, -9], 'actLabel' => 'закрыть',
            'editable'         => true, 'handler' => 'changeState_Closed', 'keepCUser' => true, 'isolatedChanges' => false, 'customPerms' => [
                1    => [
                    'allowFrom' => [0, 2, 5, 10, 11, 13, 14, 15, 17, 20, 35, 38, 40, 42, -1, -3, -5, -9],
                ], 2 => [
                    'allowFrom' => [0, 2, 5, 10, 11, 13, 14, 15, 17, 20, 35, 38, 40, 42, -1, -3, -5, -9], 'editable' => true, 'isolatedChanges' => false,
                ],
            ], 'cmsDefaultChk' => 0, 'bgStyle' => ['background-color' => '#0C9'], 'textStyle' => '', 'method' => [0, 1],
        ],  // закрыт
        -1  => [
            'label'            => 'отменен', 'allowFrom' => [0, 2, 5, 10, 11, 13, 14, 15, 17, 20, 35, 38, 40, 42, -1, -3, -5, -9], 'actLabel' => 'отменить',
            'editable'         => false, 'handler' => 'changeState_Cancel', 'isolatedChanges' => true, 'keepCUser' => true, 'customPerms' => [
                1    => [
                    'allowFrom' => [0, 2, 5, 10, 11, 13, 14, 15, 17, 20, 35, 38, 40, 42, -1, -3, -5, -9], 'isolatedChanges' => false,
                ], 2 => [
                    'allowFrom' => [0, 2, 5, 10, 11, 13, 14, 15, 17, 20, 35, 38, 40, 42, -1, -3, -5, -9], 'editable' => true, 'isolatedChanges' => false,
                ],
            ], 'cmsDefaultChk' => 0, 'bgStyle' => ['background-color' => '#FF3300'], 'textStyle' => '', 'techMinLength' => 3, 'excludeFromDropList' => true,
            'method'           => [0, 1],
        ],  // отменен
        -3  => [
            'label'            => 'отложенный', 'allowFrom' => [0, 2, 5, 10, 11, 13, 14, 15, 17, 20, 35, 38, 40, 42, -1, -3, -5, -9], 'actLabel' => 'отложить',
            'editable'         => false, 'handler' => 'changeState_Delayed', 'isolatedChanges' => false, 'keepCUser' => false, 'customPerms' => [
                1    => [
                    'allowFrom' => [0, 2, 5, 10, 11, 13, 14, 15, 17, 20, 35, 38, 40, 42, -1, -3, -5, -9], 'keepCUser' => true,
                ], 2 => [
                    'allowFrom' => [0, 2, 5, 10, 11, 13, 14, 15, 17, 20, 35, 38, 40, 42, -1, -3, -5, -9], 'editable' => true, 'isolatedChanges' => false,
                ],
            ], 'cmsDefaultChk' => 0, 'bgStyle' => ['background-color' => '#D673CF'], 'textStyle' => '', 'excludeFromDropList' => true, 'method' => [0, 1],
        ],  // отложенный
        -5  => [
            'label'             => 'на возврат', 'allowFrom' => [5, 10, 11, 13, 15, 17, 20, 35, 38, 42, -1, -3, -5, -9], 'actLabel' => 'на возврат',
            'editable'          => true, 'dontChangeCUserId' => true, 'handler' => 'changeState_onReturn', 'isolatedChanges' => false, 'cmsDefaultChk' => 0,
            'keepCUser'         => true, 'customPerms' => [
                1    => [
                    'allowFrom' => [0, 2, 5, 10, 11, 13, 14, 15, 17, 20, 35, 38, 40, 42, -1, -3, -5, -9],
                ], 2 => [
                    'allowFrom' => [0, 2, 5, 10, 11, 13, 14, 15, 17, 20, 35, 38, 40, 42, -1, -3, -5, -9], 'editable' => true, 'isolatedChanges' => false,
                ],
            ], 'bgStyle'        => ['background-color' => '#FC6244'], 'textStyle' => '', 'method' => [0, 1],
        ],  // на возврат
        -9  => [
            'label'           => 'закрыт после возврата', 'allowFrom' => [0, 2, 5, 10, 11, 13, 14, 15, 17, 20, 35, 38, 40, 42, -1, -3, -5, -9],
            'actLabel'        => 'закрыть после возврата', 'editable' => false, 'dontChangeCUserId' => true, 'handler' => 'changeState_Returned',
            'isolatedChanges' => false, 'cmsDefaultChk' => 0, 'keepCUser' => true, 'customPerms' => [
                1    => [
                    'allowFrom' => [0, 2, 5, 10, 11, 13, 14, 15, 17, 20, 35, 38, 40, 42, -1, -3, -5, -9],
                ], 2 => [
                    'allowFrom' => [0, 2, 5, 10, 11, 13, 14, 15, 17, 20, 35, 38, 40, 42, -1, -3, -5, -9], 'editable' => true, 'isolatedChanges' => false,
                ],
            ], 'bgStyle'      => ['background-color' => '#CC3300'], 'textStyle' => '', 'method' => [0, 1],
        ],  // закрыт после возврата
        -20 => [
            'label'             => 'спам', 'allowFrom' => [0, 2, 5, 10, 11, 13, 14, 15, 17, 20, 35, 38, 40, 42, -1, -3, -5, -9], 'actLabel' => 'спам',
            'editable'          => false, 'dontChangeCUserId' => true, 'handler' => 'changeState_Common', 'isolatedChanges' => false, 'cmsDefaultChk' => 0,
            'keepCUser'         => false, 'customPerms' => [
                1    => [
                    'allowFrom' => [0, 2, 5, 10, 11, 13, 14, 15, 17, 20, 35, 38, 40, 42, -1, -3, -5, -9],
                ], 2 => [
                    'allowFrom' => [0, 2, 5, 10, 11, 13, 14, 15, 17, 20, 35, 38, 40, 42, -1, -3, -5, -9], 'editable' => true, 'isolatedChanges' => false,
                ],
            ], 'bgStyle'        => ['background-color' => '#CA46F2'], 'textStyle' => '', 'method' => [0, 1],
        ]  // спам
    ];

    // конфиг выгрузки документов
    public $docCfg = [

        'html'   => [
            'orderSpecFizClient'   => [
                'name'        => 'Спецификация клиенту (физ)', 'tn' => 'mail/ORDER_HTML_CLIENT.php', 'ptype' => [0], 'method' => [0, 1], 'useInCMS' => true,
                'internalUse' => false, 'mailSubject' => 'Заказ {$orderNum}',
            ], 'orderSpecUrClient' => [
                'name'        => 'Спецификация клиенту (юр)', 'tn' => 'mail/UORDER_HTML_CLIENT.php', 'ptype' => [1], 'method' => [0, 1], 'useInCMS' => true,
                'internalUse' => false, 'mailSubject' => 'Заказ {$orderNum}',
            ], 'orderSpecFizMgr'   => [
                'name'        => 'Спецификация менеджеру (физ)', 'tn' => 'mail/ORDER_HTML_BASE.php', 'ptype' => [0], 'method' => [0, 1], 'useInCMS' => true,
                'internalUse' => true, 'mailSubject' => 'Заказ {$orderNum}',
            ], 'orderSpecUrMgr'    => [
                'name'        => 'Спецификация менеджеру (юр)', 'tn' => 'mail/UORDER_HTML_BASE.php', 'ptype' => [1], 'method' => [0, 1], 'useInCMS' => true,
                'internalUse' => true, 'mailSubject' => 'Заказ {$orderNum}',
            ], 'orderDetail'       => [
                'name'        => 'Заказ-наряд', 'tn' => 'mail/ORDER_MGR_DETAIL.php', 'ptype' => [0, 1], 'method' => [0, 1], 'useInCMS' => true,
                'internalUse' => true, 'mailSubject' => 'Заказ-наряд {$orderNum}',
            ], 'orderCashMemo'     => [
                'name'        => 'Товарный чек', 'tn' => 'mail/cash-memo.php', 'ptype' => [0, 1], 'method' => [0, 1], 'useInCMS' => true, 'internalUse' => true,
                'mailSubject' => 'Товарный чек {$orderNum}',
            ],
        ], 'pdf' => [
            'orderDetail'            => [
                'name'        => 'Заказ-наряд', 'tn' => 'mail/ORDER_MGR_DETAIL.php', 'ptype' => [0, 1], 'method' => [0, 1], 'useInCMS' => true,
                'internalUse' => true, 'mailSubject' => 'Заказ-наряд {$orderNum}', 'fn' => 'Наряд {$orderNum}.pdf', 'mpdfInit' => [
                    'mode'      => 'ru_RU', 'format' => 'A4', 'defFontSize' => '10pt', 'defFont' => 'Arial', 'marginLeft' => 15, 'marginRight' => 5,
                    'marginTop' => 5, 'marginBottom' => 5, 'marginHeader' => 0, 'marginFooter' => 0, 'orient' => 'P',
                ],
            ], 'pd4'                 => [
                'name'        => 'Квитанция', 'tn' => 'pdf/PD-4.php', 'ptype' => [0], 'method' => [1], 'useInCMS' => false,
                'mailSubject' => 'Квитанция для заказа {$orderNum}', 'fn' => 'Квитанция {$orderNum}.pdf', 'mpdfInit' => [
                    'mode'      => 'ru_RU', 'format' => 'A4', 'defFontSize' => '8pt', 'defFont' => 'Arial', 'marginLeft' => 15, 'marginRight' => 10,
                    'marginTop' => 10, 'marginBottom' => 5, 'marginHeader' => 0, 'marginFooter' => 0, 'orient' => 'P',
                ],
            ], 'bill_ur'             => [
                'name'              => 'Счет', 'tn' => 'pdf/bill-ur.php', 'ptype' => [0, 1], 'method' => [1], 'useInCMS' => true,
                'availabilityJSFoo' => 'exportBillCheck', 'mailSubject' => 'Счет {$orderNum}', 'fn' => 'Счет {$orderNum}.pdf', 'mpdfInit' => [
                    'mode'      => 'ru_RU', 'format' => 'A4', 'defFontSize' => '9pt', 'defFont' => 'Arial', 'marginLeft' => 15, 'marginRight' => 10,
                    'marginTop' => 10, 'marginBottom' => 5, 'marginHeader' => 0, 'marginFooter' => 0, 'orient' => 'P',
                ],
            ], 'bill_ur_shtamp'      => [
                'name'              => 'Счет (с печатью)', 'tn' => 'pdf/bill-ur-shtamp.php', 'ptype' => [0, 1], 'method' => [1], 'useInCMS' => true,
                'availabilityJSFoo' => 'exportBillCheck', 'mailSubject' => 'Счет с печатью {$orderNum}', 'fn' => 'Счет с печатью {$orderNum}.pdf',
                'mpdfInit'          => [
                    'mode'      => 'ru_RU', 'format' => 'A4', 'defFontSize' => '9pt', 'defFont' => 'Arial', 'marginLeft' => 15, 'marginRight' => 10,
                    'marginTop' => 10, 'marginBottom' => 5, 'marginHeader' => 0, 'marginFooter' => 0, 'orient' => 'P',
                ],
            ], 'contract_fiz'        => [
                'name'              => 'Договор (физ)', 'tn' => 'pdf/dogovor-fiz.php', 'ptype' => [0], 'method' => [1], 'useInCMS' => true,
                'availabilityJSFoo' => 'exportContractCheck', 'mailSubject' => 'Договор {$orderNum}', 'fn' => 'Договор {$orderNum}.pdf', 'mpdfInit' => [
                    'mode'      => 'ru_RU', 'format' => 'A4', 'defFontSize' => '10pt', 'defFont' => 'Arial', 'marginLeft' => 20, 'marginRight' => 10,
                    'marginTop' => 15, 'marginBottom' => 15, 'marginHeader' => 0, 'marginFooter' => 0, 'orient' => 'P',
                ],
            ], 'contract_fiz_shtamp' => [
                'name'              => 'Договор (физ) (с печатью)', 'tn' => 'pdf/dogovor-fiz-shtamp.php', 'ptype' => [0], 'method' => [1], 'useInCMS' => true,
                'availabilityJSFoo' => 'exportContractCheck', 'mailSubject' => 'Договор с печатью {$orderNum}', 'fn' => 'Договор с печатью {$orderNum}.pdf',
                'mpdfInit'          => [
                    'mode'      => 'ru_RU', 'format' => 'A4', 'defFontSize' => '10pt', 'defFont' => 'Arial', 'marginLeft' => 20, 'marginRight' => 10,
                    'marginTop' => 15, 'marginBottom' => 15, 'marginHeader' => 0, 'marginFooter' => 0, 'orient' => 'P',
                ],
            ], 'contract_ur'         => [
                'name'              => 'Договор (юр)', 'tn' => 'pdf/dogovor-ur.php', 'ptype' => [1], 'method' => [1], 'useInCMS' => true,
                'availabilityJSFoo' => 'exportUrContractCheck', 'mailSubject' => 'Договор {$orderNum}', 'fn' => 'Договор {$orderNum}.pdf', 'mpdfInit' => [
                    'mode'      => 'ru_RU', 'format' => 'A4', 'defFontSize' => '10pt', 'defFont' => 'Arial', 'marginLeft' => 20, 'marginRight' => 10,
                    'marginTop' => 15, 'marginBottom' => 15, 'marginHeader' => 0, 'marginFooter' => 0, 'orient' => 'P',
                ],
            ], 'contract_ur_shtamp'  => [
                'name'              => 'Договор (юр) (с печатью)', 'tn' => 'pdf/dogovor-ur-shtamp.php', 'ptype' => [1], 'method' => [1], 'useInCMS' => true,
                'availabilityJSFoo' => 'exportUrContractCheck', 'mailSubject' => 'Договор с печатью {$orderNum}', 'fn' => 'Договор с печатью {$orderNum}.pdf',
                'mpdfInit'          => [
                    'mode'      => 'ru_RU', 'format' => 'A4', 'defFontSize' => '10pt', 'defFont' => 'Arial', 'marginLeft' => 20, 'marginRight' => 10,
                    'marginTop' => 15, 'marginBottom' => 15, 'marginHeader' => 0, 'marginFooter' => 0, 'orient' => 'P',
                ],
            ], 'orderSpecFizClient'  => [
                'name'        => 'Спецификация клиенту (физ)', 'tn' => 'mail/ORDER_HTML_CLIENT.php', 'ptype' => [0], 'method' => [0, 1], 'useInCMS' => true,
                'mailSubject' => 'Заказ {$orderNum}', 'fn' => 'Заказ {$orderNum}.pdf', 'mpdfInit' => [
                    'mode'      => 'ru_RU', 'format' => 'A4', 'defFontSize' => '10pt', 'defFont' => 'Arial', 'marginLeft' => 15, 'marginRight' => 10,
                    'marginTop' => 10, 'marginBottom' => 5, 'marginHeader' => 0, 'marginFooter' => 0, 'orient' => 'P',
                ],
            ], 'orderSpecUrClient'   => [
                'name'        => 'Спецификация клиенту (юр)', 'tn' => 'mail/UORDER_HTML_CLIENT.php', 'ptype' => [1], 'method' => [0, 1], 'useInCMS' => true,
                'mailSubject' => 'Заказ {$orderNum} на {$siteName}', 'fn' => 'Заказ {$orderNum}.pdf', 'mpdfInit' => [
                    'mode'      => 'ru_RU', 'format' => 'A4', 'defFontSize' => '10pt', 'defFont' => 'Arial', 'marginLeft' => 15, 'marginRight' => 10,
                    'marginTop' => 10, 'marginBottom' => 5, 'marginHeader' => 0, 'marginFooter' => 0, 'orient' => 'P',
                ],
            ], 'orderSpecFizMgr'     => [
                'name'        => 'Спецификация менеджеру (физ)', 'tn' => 'mail/ORDER_HTML_BASE.php', 'ptype' => [0], 'method' => [0, 1], 'useInCMS' => true,
                'internalUse' => true, 'mailSubject' => 'Заказ {$orderNum}', 'fn' => 'Заказ {$orderNum}.pdf', 'mpdfInit' => [
                    'mode'      => 'ru_RU', 'format' => 'A4', 'defFontSize' => '10pt', 'defFont' => 'Arial', 'marginLeft' => 15, 'marginRight' => 10,
                    'marginTop' => 10, 'marginBottom' => 5, 'marginHeader' => 0, 'marginFooter' => 0, 'orient' => 'P',
                ],
            ], 'orderSpecUrMgr'      => [
                'name'        => 'Спецификация менеджеру (юр)', 'tn' => 'mail/UORDER_HTML_BASE.php', 'ptype' => [1], 'method' => [0, 1], 'useInCMS' => true,
                'internalUse' => true, 'mailSubject' => 'Заказ {$orderNum}', 'fn' => 'Заказ {$orderNum}.pdf', 'mpdfInit' => [
                    'mode'      => 'ru_RU', 'format' => 'A4', 'defFontSize' => '10pt', 'defFont' => 'Arial', 'marginLeft' => 15, 'marginRight' => 10,
                    'marginTop' => 10, 'marginBottom' => 5, 'marginHeader' => 0, 'marginFooter' => 0, 'orient' => 'P',
                ],
            ], 'orderCashMemo1'      => [
                'name'        => 'Товарный чек', 'tn' => 'mail/cash-memo.php', 'ptype' => [0, 1], 'method' => [0, 1], 'useInCMS' => true, 'internalUse' => true,
                'mailSubject' => 'Товарный чек {$orderNum}', 'fn' => 'Товарный чек {$orderNum}.pdf', 'mpdfInit' => [
                    'mode'      => 'ru_RU', 'format' => 'A4', 'defFontSize' => '10pt', 'defFont' => 'Arial', 'marginLeft' => 15, 'marginRight' => 10,
                    'marginTop' => 10, 'marginBottom' => 5, 'marginHeader' => 0, 'marginFooter' => 0, 'orient' => 'P',
                ],
            ],
        ],

    ];

    var $cmsOrderTableSort = '(os_order.state_id DIV os_order.state_id) ASC, GREATEST(os_order.dt_add,os_order.dt_state) DESC';
    var $cmsOrderDatesField = 'os_order.dt_add';
    var $cmsInjectStatesWithCUser0 = [0, 2];

    /*
     * привести статусы:
     * SELECT * FROM os_order WHERE state_id=10 AND method=0
     * UPDATE os_order SET method=1 WHERE state_id=10 AND method=0
     */

    /*
     * только валидация данных в контроллере
     * метод работает с корзиной Cart
     * @r   данные форрмы
      */
    function add_order($r)
    {
        $orderNum = $this->newOrderNum();

        $qr = [ // массив полей для записи в БД
            'user_id'       => 0, 'order_num' => $orderNum, 'name' => '', 'city' => '', 'tel1' => '', 'tel2' => '', 'email' => '', 'addr' => '', 'info' => '',
            'state_id'      => 0, 'dt_add' => $dt = Tools::dt(), 'ip' => @$_SERVER['REMOTE_ADDR'], 'cost' => 0, 'bcost' => 0, 'discount' => 0, 'ptype' => 0,
            'method'        => 0, 'INN' => '', 'KPP' => '', 'BIK' => '', 'u_addr' => '', 'bank' => '', 'person' => '', 'rs' => '', 'ks' => '',
            'dt_state'      => $dt, 'delivery_cost' => 0, 'carrier_co' => '', 'avto_name' => '', 'subscribe' => 0, 'cUserId' => 0, 'createdBy' => 0, 'source' => 0
        ];

        if (!isset($r['ptype'])) $qr['ptype'] = 0;
        else $qr['ptype'] = (int)$r['ptype']; // 0- физик 1-юрик
        if ($r['ptype'] == 0)
        {
            if (isset($r['name_fiz'])) $qr['name'] = Tools::esc(Tools::stripTags($r['name_fiz']));
        }
        else
        {
            if (isset($r['name_ur'])) $qr['name'] = Tools::esc(Tools::stripTags($r['name_ur']));
        }
        if (isset($r['city'])) $qr['city'] = Tools::esc(Tools::stripTags($r['city']));
        if (isset($r['info'])) $qr['info'] = Tools::esc(Tools::stripTags($r['info']));
        if (isset($r['addr'])) $qr['addr'] = Tools::esc(Tools::stripTags($r['addr']));
        if (isset($r['INN_ur'])) $qr['INN'] = Tools::esc(Tools::stripTags($r['INN_ur']));
        if (isset($r['KPP_ur'])) $qr['KPP'] = Tools::esc(Tools::stripTags($r['KPP_ur']));
        if (isset($r['BIK_ur'])) $qr['BIK'] = Tools::esc(Tools::stripTags($r['BIK_ur']));
        if (isset($r['bank_ur'])) $qr['bank'] = Tools::esc(Tools::stripTags($r['bank_ur']));
        if (isset($r['person_ur'])) $qr['person'] = Tools::esc(Tools::stripTags($r['person_ur']));
        if (isset($r['rs_ur'])) $qr['rs'] = Tools::esc(Tools::stripTags($r['rs_ur']));
        if (isset($r['ks_ur'])) $qr['ks'] = Tools::esc(Tools::stripTags($r['ks_ur']));
        if (isset($r['u_addr_ur'])) $qr['u_addr'] = Tools::esc(Tools::stripTags($r['u_addr_ur']));

        if (isset($r['tel'])) $qr['tel1'] = Tools::esc(Tools::stripTags($r['tel']));
        if (isset($r['telPrefix'])) $qr['tel1'] = Tools::esc(Tools::stripTags($r['telPrefix'])) . ' ' . $qr['tel1'];
        if (isset($r['email'])) $qr['email'] = Tools::esc(Tools::stripTags($r['email']));

        $r['tel2'] = '';
        if (isset($r['carrier_co'])) $qr['carrier_co'] = Tools::esc(Tools::stripTags($r['carrier_co']));
        if (isset($r['avto_name'])) $qr['avto_name'] = Tools::esc(Tools::stripTags($r['avto_name']));

        if (!isset($r['subscribe'])) $qr['subscribe'] = 0;
        else $qr['subscribe'] = 1;

        if (isset($r['user_id'])) $qr['user_id'] = intval($r['user_id']);

        $qr['delivery_cost'] = $this->getDeliveryCost();
        $qr['discount'] = $this->discount->getDiscount();

        if (CU::isLogged())
        {
            $qr['createdBy'] = CU::$userId;
            if (empty($r['forceStateId0']))
            {
                $qr['cUserId'] = CU::$userId;
                $qr['state_id'] = 0;
            }
            else $qr['state_id'] = 0;
        }

        $qr['bcost'] = Cart::$b_sum; // сумма товаров без допов - выгружается в GA
        $qr['cost'] = ceil(Cart::$asum - Cart::$asum * $qr['discount'] / 100) + $qr['delivery_cost']; // итоговая сумма заказа со скидками, допами и доставкой

        // добавляем заказ
        $this->insert('os_order', $qr);
        $this->order_id = $this->lastId();

        // список товаров и допов
        $this->cc = new CC_Base();
        foreach (Cart::$b_list as $k => $v)
        {
            $this->cc->que('cat_by_id', $k);
            if ($this->cc->qnum())
            {
                $this->cc->next();

                if ($this->cc->qrow['gr'] == 1) $name = "Шина {$this->cc->qrow['bname']} {$this->cc->qrow['name']}" . ($this->cc->qrow['msuffix'] != '' ? (' ' . $this->cc->qrow['msuffix']) : '') . ($this->cc->qrow['csuffix'] != '' ? (' ' . $this->cc->qrow['csuffix']) : '') . ' ' . ($this->cc->qrow['P3'] != 0 ? $this->cc->qrow['P3'] : '-') . "/" . ($this->cc->qrow['P2'] != 0 ? $this->cc->qrow['P2'] : '-') . " R" . ($this->cc->qrow['P1'] != 0 ? $this->cc->qrow['P1'] : '-') . " " . $this->cc->qrow['P7'];
                else
                    $name = "Диск {$this->cc->qrow['bname']} {$this->cc->qrow['name']}" . ($this->cc->qrow['msuffix'] != '' ? (' ' . $this->cc->qrow['msuffix']) : '') . ($this->cc->qrow['csuffix'] != '' ? (' ' . $this->cc->qrow['csuffix']) : '') . ' ' . ($this->cc->qrow['P2'] != 0 ? $this->cc->qrow['P2'] : '-') . "Jx" . ($this->cc->qrow['P5'] != 0 ? $this->cc->qrow['P5'] : '-') . ' ' . ($this->cc->qrow['P4'] != 0 ? $this->cc->qrow['P4'] : '-') . "/" . ($this->cc->qrow['P6'] != 0 ? $this->cc->qrow['P6'] : '-') . " ET {$this->cc->qrow['P1']}" . ($this->cc->qrow['P3'] != 0 ? " DIA {$this->cc->qrow['P3']}" : '');

                $meta = [];
                if ($this->cc->qrow['scprice'] && $this->cc->qrow['scprice'] == Cart::$b_list[$k]['price']) $meta['spez'] = true;
                $meta = Tools::DB_serialize($meta);

                $this->insert('os_item', [
                    'cat_id' => $this->cc->qrow['cat_id'], 'order_id' => $this->order_id, 'gr' => $this->cc->qrow['gr'], 'name' => Tools::esc($name),
                    'amount' => (int)Cart::$b_list[$k]['amount'], 'price' => (float)Cart::$b_list[$k]['price'], 'meta' => $meta,
                ]);

            }
        }

        // добвляем некаталожные допы
        if (!empty(Cart::$dop_list))
        {
            foreach (Cart::$dop_list as $dop) {
                foreach ($dop as $v) {
                    $this->insert('os_dop', [
                        'order_id' => $this->order_id, 'name' => Tools::esc($v['name']), 'amount' => $v['amount'], 'price' => $v['price'],
                    ]);
                }
            }
        }

        // отправляем почту
        $r = $this->getOrderData($this->order_id);

        // робот - не предназначен для приема писем
        $mailRobot = trim(Data::get('mail_robot'));
        if ($mailRobot == '' || mb_strpos($mailRobot, '@') === false) $mailRobot = 'no-reply@' . str_replace('www.', '', $_SERVER['SERVER_NAME']);

        $mailOrder = Data::get('mail_order');

        $mailOrderName = Cfg::get('site_name');

        if (!preg_match('|([a-z0-9_\.\-]{1,20})@([a-z0-9\.\-]{1,20})\.([a-z]{2,4})|is', $r['email'])) $clientAddr = '';
        else $clientAddr = $r['email'];
        if (!preg_match("/[a-zабвгдеёжзиклмнопрстуфхцчшщъыьэюя_\-\.]+/iu", $r['name'])) $clientName = '';
        else $clientName = $r['name'];

        if ($r['ptype'] == 0) $tpl_office = $this->docCfg['html']['orderSpecFizMgr']['tn']; //Data::get('order_mail_tpl');
        elseif ($r['ptype'] == 1) $tpl_office = $this->docCfg['html']['orderSpecUrMgr']['tn']; //Data::get('uorder_mail_tpl');

        if ($r['ptype'] == 0) $tpl_client = $this->docCfg['html']['orderSpecFizClient']['tn']; //Data::get('order_client_mail_tpl');
        elseif ($r['ptype'] == 1) $tpl_client = $this->docCfg['html']['orderSpecUrClient']['tn']; //Data::get('uorder_client_mail_tpl');

        $charset = Data::get('order_mail_charset');
        $host = Data::get('mail_robot_host');
        $logpw = Data::get('mail_robot_logpw');
        $debug = 0;
        $secure = Data::get('mail_robot_smtp_secure');

        //в офис
        Mailer::sendmail([
            'fromAddr' => $mailRobot, 'fromName' => $clientName, 'toAddr' => $mailOrder, 'toName' => $r['siteName'], 'body' => $r,
            'subject'  => ($r['cUserId'] ? ("(" . CU::$shortName . ") ") : '') . 'Заказ №' . $r['order_num'] . ' на ' . $r['siteName'], 'tpl' => $tpl_office,
            'charset'  => $charset, 'host' => $host, 'logpw' => $logpw, 'SMTPSecure' => $secure, 'debug' => $debug,

        ]);

        // статус НОВЫЙ
        if (empty($r['cUserId']))
        {

            // клиенту
            if ($clientAddr != '')
            {
                Mailer::sendmail([
                    'fromAddr' => $mailRobot, 'fromName' => $mailOrderName, 'replyToAddr' => $mailOrder, 'replyToName' => $mailOrderName,
                    'toAddr'   => $clientAddr, 'toName' => $clientName, 'subject' => 'Заказ в магазине ' . $r['siteName'], 'tpl' => $tpl_client, 'body' => $r,
                    'charset'  => $charset, 'host' => $host, 'logpw' => $logpw, 'SMTPSecure' => $secure, 'debug' => $debug,
                ]);
            }


        }
        else
        {
            // статус В ОБРАБОТКЕ

            // клиенту
            if ($clientAddr != '')
            {
                Mailer::sendmail([
                    'fromAddr'    => $mailRobot, 'fromName' => $mailOrderName, 'replyToAddr' => !empty(CU::$email) ? CU::$email : $mailOrder,
                    'replyToName' => !empty(CU::$fullName) ? CU::$fullName : $mailOrderName, 'toAddr' => $clientAddr, 'toName' => $clientName,
                    'subject'     => 'Заказ в магазине ' . $r['siteName'], 'tpl' => $tpl_client, 'body' => $r, 'charset' => $charset, 'host' => $host,
                    'logpw'       => $logpw, 'SMTPSecure' => $secure, 'debug' => $debug,
                ]);
            }


            // уведомление манагеру кто разместил заказ

            // спецификация
            if (!empty(CU::$email))
            {
                Mailer::sendmail([
                    'fromAddr' => $mailRobot, 'fromName' => $clientName, 'replyToAddr' => $clientAddr, 'replyToName' => $clientName, 'toAddr' => CU::$email,
                    'toName'   => CU::$shortName, 'body' => $r, 'subject' => 'Заказ №' . $r['order_num'] . ' на ' . $r['siteName'], 'tpl' => $tpl_office,
                    'charset'  => $charset, 'host' => $host, 'logpw' => $logpw, 'SMTPSecure' => $secure, 'debug' => $debug,
                ]);

            }
        }

        // отправка СМС
        $smsSource = Data::get('SMS_defaultSource');
        if (Data::get('SMS_enabled') && !empty($smsSource))
        {
            $sms = SMS_Reactor::factory();
            if ($sms !== false)
            {
                $this->orderNum = $r['orderNum'];
                $ss = new Content();
                $msg = $this->parseText($ss->getDoc('new_order$19'));
                if (empty($msg))
                {
                    Log_Sys::put(SLOG_INFO, 'Orders.add_order', 'Шаблон для отправки СМС не сформирован');
                }
                else
                {
                    $smstel = $sms->checkTelNumber($r['tel1']);
                    if (!empty($smstel))
                    {
                        $rr = $sms->send($smsSource, $smstel, $msg);
                        $this->update('os_order', ['SMSTel' => Tools::esc($smstel)], "order_id='{$this->order_id}'");
                    }
                }
            }
            unset($sms, $this->orderNum, $ss);
        }

        $r['GA_customVarsSlot'] = 1;
        $r['GA_trans'] = GA::trans($r);
        if ($r['GA_trans'] === false) $r['GA_transErr'] = GA::$errorEvent;
        else $r['GA_transErr'] = '';

        unset($this->cc);

        return ($r);
    }

    function add_quick_order($r)
    {
        if (empty($r['cid']) || empty($r['am'])) return false;

        $orderNum = $this->newOrderNum();

        $qr = [ // массив полей для записи в БД
            'user_id'       => 0, 'order_num' => $orderNum, 'name' => '', 'city' => '', 'tel1' => '', 'tel2' => '', 'email' => '', 'addr' => '', 'info' => '',
            'state_id'      => 0, 'dt_add' => $dt = Tools::dt(), 'ip' => @$_SERVER['REMOTE_ADDR'], 'cost' => 0, 'bcost' => 0, 'discount' => 0, 'ptype' => 0,
            'method'        => 0, 'INN' => '', 'KPP' => '', 'BIK' => '', 'u_addr' => '', 'bank' => '', 'person' => '', 'rs' => '', 'ks' => '',
            'dt_state'      => $dt, 'delivery_cost' => 0, 'carrier_co' => '', 'avto_name' => '', 'subscribe' => 0, 'cUserId' => 0, 'createdBy' => 0, 'source' => 1
        ];

        if (isset($r['name'])) $qr['name'] = Tools::esc(Tools::stripTags($r['name']));
        if (isset($r['tel'])) $qr['tel1'] = Tools::esc(Tools::stripTags($r['tel']));
        if (isset($r['comment'])) $qr['info'] = Tools::esc(Tools::stripTags($r['comment']));

        $qr['bcost'] = $qr['cost'] = (float)$r['price'] * (int)$r['am']; // сумма товара

        // добавляем заказ
        $this->insert('os_order', $qr);
        $this->order_id = $this->lastId();

        // список товаров
        $this->cc = new CC_Base();
        $this->cc->que('cat_by_id', $r['cid'], 1);
        if ($this->cc->qnum())
        {
            $this->cc->next();

            if ($this->cc->qrow['gr'] == 1) $name = "Шина {$this->cc->qrow['bname']} {$this->cc->qrow['name']}" . ($this->cc->qrow['msuffix'] != '' ? (' ' . $this->cc->qrow['msuffix']) : '') . ($this->cc->qrow['csuffix'] != '' ? (' ' . $this->cc->qrow['csuffix']) : '') . ' ' . ($this->cc->qrow['P3'] != 0 ? $this->cc->qrow['P3'] : '-') . "/" . ($this->cc->qrow['P2'] != 0 ? $this->cc->qrow['P2'] : '-') . " R" . ($this->cc->qrow['P1'] != 0 ? $this->cc->qrow['P1'] : '-') . " " . $this->cc->qrow['P7'];
            else
                $name = "Диск {$this->cc->qrow['bname']} {$this->cc->qrow['name']}" . ($this->cc->qrow['msuffix'] != '' ? (' ' . $this->cc->qrow['msuffix']) : '') . ($this->cc->qrow['csuffix'] != '' ? (' ' . $this->cc->qrow['csuffix']) : '') . ' ' . ($this->cc->qrow['P2'] != 0 ? $this->cc->qrow['P2'] : '-') . "Jx" . ($this->cc->qrow['P5'] != 0 ? $this->cc->qrow['P5'] : '-') . ' ' . ($this->cc->qrow['P4'] != 0 ? $this->cc->qrow['P4'] : '-') . "/" . ($this->cc->qrow['P6'] != 0 ? $this->cc->qrow['P6'] : '-') . " ET {$this->cc->qrow['P1']}" . ($this->cc->qrow['P3'] != 0 ? " DIA {$this->cc->qrow['P3']}" : '');

            $meta = [];
            $meta = Tools::DB_serialize($meta);

            $this->insert('os_item', [
                'cat_id' => $this->cc->qrow['cat_id'], 'order_id' => $this->order_id, 'gr' => $this->cc->qrow['gr'], 'name' => Tools::esc($name),
                'amount' => (int)$r['am'], 'price' => (float)$this->cc->qrow['cprice'], 'meta' => $meta,
            ]);

        }
        // отправляем почту
        $r = $this->getOrderData($this->order_id);

        // робот - не предназначен для приема писем
        $mailRobot = trim(Data::get('mail_robot'));
        if ($mailRobot == '' || mb_strpos($mailRobot, '@') === false) $mailRobot = 'no-reply@' . str_replace('www.', '', $_SERVER['SERVER_NAME']);

        $mailOrder = Data::get('mail_order');

        $mailOrderName = Cfg::get('site_name');

        if (!preg_match('|([a-z0-9_\.\-]{1,20})@([a-z0-9\.\-]{1,20})\.([a-z]{2,4})|is', $r['email'])) $clientAddr = '';
        else $clientAddr = $r['email'];
        if (!preg_match("/[a-zабвгдеёжзиклмнопрстуфхцчшщъыьэюя_\-\.]+/iu", $r['name'])) $clientName = '';
        else $clientName = $r['name'];

        $tpl_office = $this->docCfg['html']['orderSpecFizMgr']['tn']; //Data::get('order_mail_tpl');

        $tpl_client = $this->docCfg['html']['orderSpecFizClient']['tn']; //Data::get('order_client_mail_tpl');

        $charset = Data::get('order_mail_charset');
        $host = Data::get('mail_robot_host');
        $logpw = Data::get('mail_robot_logpw');
        $debug = 0;
        $secure = Data::get('mail_robot_smtp_secure');

        //в офис
        Mailer::sendmail([
            'fromAddr' => $mailRobot, 'fromName' => $clientName, 'toAddr' => $mailOrder, 'toName' => $r['siteName'], 'body' => $r,
            'subject'  => 'Быстрый заказ №' . $r['order_num'] . ' на ' . $r['siteName'], 'tpl' => $tpl_office,
            'charset'  => $charset, 'host' => $host, 'logpw' => $logpw, 'SMTPSecure' => $secure, 'debug' => $debug,

        ]);

        // клиенту
        /*if ($clientAddr != '')
        {
            Mailer::sendmail([
                'fromAddr' => $mailRobot, 'fromName' => $mailOrderName, 'replyToAddr' => $mailOrder, 'replyToName' => $mailOrderName,
                'toAddr'   => $clientAddr, 'toName' => $clientName, 'subject' => 'Заказ в магазине ' . $r['siteName'], 'tpl' => $tpl_client, 'body' => $r,
                'charset'  => $charset, 'host' => $host, 'logpw' => $logpw, 'SMTPSecure' => $secure, 'debug' => $debug,
            ]);
        }*/

        // отправка СМС
        /*$smsSource = Data::get('SMS_defaultSource');
        if (Data::get('SMS_enabled') && !empty($smsSource))
        {
            $sms = SMS_Reactor::factory();
            if ($sms !== false)
            {
                $this->orderNum = $r['orderNum'];
                $ss = new Content();
                $msg = $this->parseText($ss->getDoc('new_order$19'));
                if (empty($msg))
                {
                    Log_Sys::put(SLOG_INFO, 'Orders.add_order', 'Шаблон для отправки СМС не сформирован');
                }
                else
                {
                    $smstel = $sms->checkTelNumber($r['tel1']);
                    if (!empty($smstel))
                    {
                        $rr = $sms->send($smsSource, $smstel, $msg);
                        $this->update('os_order', ['SMSTel' => Tools::esc($smstel)], "order_id='{$this->order_id}'");
                    }
                }
            }
            unset($sms, $this->orderNum, $ss);
        }*/
        unset($this->cc);
        return ($r);
    }

    function getDeliveryCost($callFromCart = true, $order_id = 0)
    {
        if ($callFromCart)
        {
            if (isset($_SESSION['deliveryCost'])) return $this->deliveryCost = $_SESSION['deliveryCost'];

            $this->deliveryCost = (int)Data::get('delivery_cost');

        }
        else
        {
            $this->deliveryCost = (int)Data::get('delivery_cost');
        }

        return $this->deliveryCost;
    }

    function getOrderData($order_id)
    {
        $order_id = (int)$order_id;

        $r = $this->getOne("SELECT * FROM os_order WHERE order_id=$order_id", MYSQLI_ASSOC);

        if ($r === 0) return false;

        foreach ($r as $k => $v)
        {
            $r[$k] = Tools::unesc($v);
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

        $r['deliveryDate'] = Tools::sdate($r['deliveryDate']);


        $r['rekvName'] = Data::get('rekv_name');
        $r['rekvLongName'] = Data::get('rekv_long_name');
        $r['rekvDirector_r'] = Data::get('rekv_director_r');
        $r['rekvDirector_im'] = Data::get('rekv_director_im');
        $r['rekvDirector_short'] = Data::get('rekv_director_short');
        $r['rekvAddrPost'] = Data::get('rekv_addr_post');
        $r['rekvAddrUr'] = Data::get('rekv_addr_ur');
        $r['rekvBIK'] = Data::get('rekv_BIK');
        $r['rekvTel'] = Data::get('rekv_tel');
        $r['rekvINN'] = Data::get('rekv_INN');
        $r['rekvKPP'] = Data::get('rekv_KPP');
        $r['rekvOGRN'] = Data::get('rekv_OGRN');
        $r['rekvRS'] = Data::get('rekv_rs');
        $r['rekvBank'] = Data::get('rekv_bank');
        $r['rekvKS'] = Data::get('rekv_ks');
        $r['vr'] = Data::get('vr');
        $r['tel'] = Data::get('tel');
        $r['tel2'] = Data::get('tel2');

        $r['siteName'] = Url::trimWWW(Cfg::get('site_name'));
        $r['site_name'] = Cfg::get('site_name');
        $r['emailInfo'] = Data::get('mail_info');
        $r['emailOrder'] = Data::get('mail_order');

        $r['orderNum'] = Cfg::get('orderPrefix') . ' ' . $r['order_num'];

        $r['TTN'] = Tools::cutDoubleSpaces(trim(str_ireplace('ТТН', '', Tools::unesc($r['TTN']))));
        $r['TK'] = Tools::cutDoubleSpaces(trim(Tools::unesc($r['carrier_co'])));

        if ($r['billDate'] == '0000-00-00') $r['billDate'] = $r['dt_add'];
        $r['billDate'] = Tools::sdate($r['billDate']);
        $r['billDateDots'] = Tools::sdate($r['billDate'], '.');
        $r['billDateRus'] = $this->russianDate($r['billDate']) . 'г.';

        $cusers = CU::usersList(['includeLD' => 1]);
        $drivers = CU::usersList(['driversOnly' => true]);

        $r['driverShortName'] = @$drivers[$r['driverId']]['shortName'];

        $cc = new CC_Base();
        $suplrs = $cc->suplrList([]);

        if ($r['createdBy'])
        {
            $r['mgr_CreatorFullName'] = $cusers[$r['createdBy']]['fullName'];
        }
        else
        {
            $r['mgr_CreatorFullName'] = '';
        }
        if ($r['cUserId'])
        {
            $r['mgr_FullName'] = $cusers[$r['cUserId']]['fullName'];
            $r['mgr_shortName'] = $cusers[$r['cUserId']]['shortName'];
            $r['mgr_email'] = $cusers[$r['cUserId']]['email'];
        }
        else
        {
            $r['mgr_FullName'] = '';
            $r['mgr_shortName'] = '';
            $r['mgr_email'] = '';
        }

        $imgs = [];
        $d = $this->que('item_list', $order_id);
        $r['list'] = [];
        $r['pItog'] = $r['summa'] = 0;
        foreach ($d as $v)
        {
            $meta = Tools::DB_unserialize($v['meta']);
            $r['list'][$v['item_id']] = [
                'cat_id'     => $v['cat_id'], 'gr' => $v['gr'], 'name' => $v['name'], 'amount' => $v['amount'], 'price' => Tools::nn($v['price']),
                '_price'     => $v['price'], 'sum' => Tools::nn($v['amount'] * $v['price']), '_sum' => $v['amount'] * $v['price'],
                'spez'       => @$meta['spez'] ? true : false, 'dop' => [],
                'turl'       => 'https://' . Cfg::get('site_url') . ($v['gr'] == 1 ? App_SUrl::tTipo($v['cat_id']) : App_SUrl::dTipo($v['cat_id'])) . '?from=mail',
                'pprice'     => Tools::nn($v['pprice']), '_pprice' => $v['pprice'], 'psum' => Tools::nn($v['pprice'] * $v['amount']),
                'reserveNum' => Tools::html($v['reserveNum']), 'suplrName' => @$suplrs[$v['suplrId']]['name'],
            ];
            $r['pItog'] += $v['amount'] * $v['pprice'];
            $r['summa'] += $v['amount'] * $v['price'];
            $imgs[] = $v['cat_id'];
        }

        $r['dops'] = $this->listDOP($order_id, 'all');
        foreach ($r['dops'] as $k => $v)
        {
            $r['summa'] += $v['amount'] * $v['price'];
            $r['pItog'] += $v['amount'] * $v['pprice'];
            $r['dops'][$k]['_sum'] = $v['price'] * $v['amount'];
            $r['dops'][$k]['_psum'] = $v['pprice'] * $v['amount'];
            $r['dops'][$k]['_price'] = $v['price'];
            $r['dops'][$k]['_pprice'] = $v['pprice'];
            $r['dops'][$k]['sum'] = Tools::nn($v['price'] * $v['amount']);
            $r['dops'][$k]['psum'] = Tools::nn($v['pprice'] * $v['amount']);
            $r['dops'][$k]['price'] = Tools::nn($v['price']);
            $r['dops'][$k]['pprice'] = Tools::nn($v['pprice']);
        }

        $r['_pItog'] = $r['pItog']; // сумма закупки товаров без допов без скидки
        $r['pItog'] = Tools::nn($r['pItog']); // сумма закупки товаров без допов без скидки
        $r['_summa'] = $r['summa']; // полная сумма с допами, но без скидки и доставки
        $r['summa'] = Tools::nn($r['summa']); // полная сумма с допами, но без скидки и доставки


        // скидка в рублях
        $r['_discountRUR'] = ceil($r['_summa'] * $r['discount'] / 100);
        $r['discountRUR'] = Tools::nn($r['_discountRUR']);
        // итог без скидки
        $r['_itogExDiscount'] = $r['_summa'] - $r['_discountRUR'];
        $r['_itogExDiscount'] = Tools::nn($r['_itogExDiscount']);

        // фотки в отчете
        if (@$r['ptype'] == 1) $r['imgsH'] = 100;
        else $r['imgsH'] = 200;

        $cc = new CC_Base();
        $r['imgs'] = [];
        if (!empty($imgs))
        {
            $d = $this->fetchAll("SELECT img2,img3, cc_model.name FROM cc_model JOIN cc_cat USING (model_id) WHERE cat_id IN(" . implode(',', $imgs) . ") AND img2!=''");
            foreach ($d as $v)
            {
                $ih = GD::get_h($cc->makeImgPath($v['img2'], true));
                $iw = GD::get_w($cc->makeImgPath($v['img2'], true));
                if ($ih)
                {
                    $k = $iw / $ih;
                    $ih = $r['imgsH'];
                    $iw = ceil($ih / $k);
                }
                else
                {
                    $ih = 0;
                    $iw = 0;
                }
                if ($ih)
                {
                    $r['imgs'][] = [
                        'img' => $cc->makeImgPath($v['img2']), 'name' => Tools::unesc($v['name']), 'h' => $ih, 'w' => $iw,
                    ];
                }
            }
        }

        return $r;

    }

    // обработчик $orderStates['handler']
    function changeState_Processing($order_id, $newStateId)
    {
        if (!$this->_changeState($order_id, $newStateId)) return false;

        //if ($this->CHS_prevState != 0) return true;

        $r = $this->getOrderData($order_id);

        if ($r === false) return false;

        // отправка СМС
        $smsSource = Data::get('SMS_defaultSource');
        if (Data::get('SMS_enabled') && !empty($smsSource))
        {
            $sms = SMS_Reactor::factory();
            if ($sms !== false)
            {
                $r = $this->getOrderData($order_id);
                $this->orderNum = $r['orderNum'];
                $this->mgr_FullName = $r['mgr_FullName'];
                $ss = new Content();
                $msg = $this->parseText($ss->getDoc('processing_order$19'));
                if (empty($msg))
                {
                    Log_Sys::put(SLOG_INFO, 'Orders.changeState_Processing', 'Шаблон для отправки СМС не сформирован');
                }
                else
                {
                    $smstel = $sms->checkTelNumber($r['tel1']);
                    if (!empty($smstel))
                    {
                        $rr = $sms->send($smsSource, $smstel, $msg);
                        $this->update('os_order', array('SMSTel' => Tools::esc($smstel)), "order_id='{$this->order_id}'");
                    }
                }
            }
            unset($sms, $this->orderNum, $ss);
        }

        return true;
    }

    // обработчик $orderStates['handler']
    function changeState_NoConnect($order_id, $newStateId)
    {
        if (!$this->_changeState($order_id, $newStateId)) return false;

        return true;
    }


    // обработчик $orderStates['handler']
    function changeState_Invoice($order_id, $newStateId)
    {
        if (!$this->_changeState($order_id, $newStateId)) return false;

        return true;
    }

    // обработчик $orderStates['handler']
    function changeState_Paid($order_id, $newStateId)
    {
        if (!$this->_changeState($order_id, $newStateId)) return false;

        // отправка СМС
        $smsSource = Data::get('SMS_defaultSource');
        if (Data::get('SMS_enabled') && !empty($smsSource))
        {
            $sms = SMS_Reactor::factory();
            if ($sms !== false)
            {
                $r = $this->getOrderData($order_id);
                $this->orderNum = $r['orderNum'];
                $ss = new Content();
                $msg = $this->parseText($ss->getDoc('paid_cashless_order$19'));
                if (empty($msg))
                {
                    Log_Sys::put(SLOG_INFO, 'Orders.changeState_Paid', 'Шаблон для отправки СМС не сформирован');
                }
                else
                {
                    $smstel = $sms->checkTelNumber($r['tel1']);
                    if (!empty($smstel))
                    {
                        $rr = $sms->send($smsSource, $smstel, $msg);
                        $this->update('os_order', array('SMSTel' => Tools::esc($smstel)), "order_id='{$this->order_id}'");
                    }
                }
            }
            unset($sms, $this->orderNum, $ss);
        }

        return true;
    }

    // обработчик $orderStates['handler']
    function changeState_PickedUp($order_id, $newStateId)
    {
        if (!$this->_changeState($order_id, $newStateId)) return false;

        return true;
    }

    // обработчик $orderStates['handler']
    function changeState_Delivering($order_id, $newStateId)
    {
        if (!$this->_changeState($order_id, $newStateId)) return false;

        return true;
    }

    // обработчик $orderStates['handler']
    function changeState_DeliveringCard($order_id, $newStateId)
    {
        if (!$this->_changeState($order_id, $newStateId)) return false;

        return true;
    }

    // обработчик $orderStates['handler']
    function changeState_CardNumSent($order_id, $newStateId)
    {
        if (!$this->_changeState($order_id, $newStateId)) return false;

        return true;
    }

    // обработчик $orderStates['handler']
    function changeState_PaidOnCard($order_id, $newStateId)
    {
        if (!$this->_changeState($order_id, $newStateId)) return false;

        return true;
    }

    // обработчик $orderStates['handler']
    function changeState_PaidParole($order_id, $newStateId)
    {
        if (!$this->_changeState($order_id, $newStateId)) return false;

        return true;
    }

    // обработчик $orderStates['handler']
    function changeState_PrepaimentSent($order_id, $newStateId)
    {
        if (!$this->_changeState($order_id, $newStateId)) return false;

        return true;
    }


    // обработчик $orderStates['handler']
    function changeState_onReturn($order_id, $newStateId)
    {
        if (!$this->_changeState($order_id, $newStateId)) return false;

        return true;
    }

    // обработчик $orderStates['handler']
    function changeState_Returned($order_id, $newStateId)
    {
        if (!$this->_changeState($order_id, $newStateId)) return false;

        return true;
    }

    // обработчик $orderStates['handler']
    function changeState_Closed($order_id, $newStateId)
    {
        if (!$this->_changeState($order_id, $newStateId)) return false;

        // отправка СМС
        $smsSource = Data::get('SMS_defaultSource');
        if (Data::get('SMS_enabled') && !empty($smsSource))
        {
            $sms = SMS_Reactor::factory();
            if ($sms !== false)
            {
                $r = $this->getOrderData($order_id);
                if (!empty($r['TTN']) && !empty($r['TK']))
                {
                    $this->orderNum = $r['orderNum'];
                    $this->TTN = 'ТТН ' . $r['TTN'];
                    $this->TK = $r['TK'];
                    $ss = new Content();
                    $msg = $this->parseText($ss->getDoc('order_closed$19'));
                    if (empty($msg))
                    {
                        Log_Sys::put(SLOG_INFO, 'Orders.add_order', 'Шаблон для отправки СМС не сформирован');
                    }
                    else
                    {
                        $smstel = $sms->checkTelNumber($r['tel1']);
                        if (!empty($smstel))
                        {
                            $rr = $sms->send($smsSource, $smstel, $msg);
                            $this->update('os_order', array('SMSTel' => Tools::esc($smstel)), "order_id='{$this->order_id}'");
                        }
                    }
                }
            }
            unset($sms, $this->orderNum, $ss, $this->TTN);
        }

        return true;
    }


    // обработчик $orderStates['handler']
    function changeState_Delayed($order_id, $newStateId, $data = [])
    {
        $order_id = (int)$order_id;

        if (false === ($d = Tools::fdate(@$data['date']))) return $this->putMsg(false, "[Orders::changeState_Delayed]: неверный параметр data[date]");

        $this->que('order_by_id', $order_id);
        if (!$this->qnum()) return $this->putMsg(false, "[Orders::changeState_Delayed]: Заказ ID=$order_id не найден.");
        $this->next();
        $od = Tools::sdate($this->qrow['dt_add'], '-', true) . ' ' . Tools::stime($this->qrow['dt_add'], ':', true);
        if (!$this->_changeState($order_id, $newStateId, [
            'slogMsg' => "Отложен до {$data['date']} 00:00",
        ])
        )
        {
            return false;
        }
        $this->query("UPDATE os_order SET " . App_TFields::$fields['os_order']['delayedOn']['as'] . " = '$d' WHERE order_id='$order_id'");

        return true;
    }

    function __construct()
    {
        parent::__construct();
        $this->discount = new App_Discount();
    }

}