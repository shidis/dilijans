<?
class App_TFields extends TFields 
{

	public static $fields=array
    (
        'cc_model'=>[
            'rating'=>[
                'caption'=>'Рейтинг модели',
                'as'=>'mrating',
                'gr'=>1,
                'widget'=>'input',  // если =='' отображаться в форме не будет
                'dbType'=>'DECIMAL(5,2)'
            ]
        ],
        'cc_cat'=>array(
            'app'=>array(
                'caption'=>'Применяемость',
                'as'=>'app',
                'gr'=>2,
                'widget'=>'',  // если =='' отображаться в форме не будет
                'dbType'=>'VARCHAR(255)'
            ),
            'analogi'=>array(
                'caption'=>'Аналоги',
                'as'=>'analogi',
                'gr'=>2,
                'widget'=>'input',  // если =='' отображаться в форме не будет
                'dbType'=>'VARCHAR(255)'
            )
        )
        ,'cc_user'=>array(
            'I'=>array(
                'caption'=>'Имя',
                'as'=>'I',
                'gr'=>12,
                'widget'=>'',
                'dbType'=>'VARCHAR(255)'
            ),
            'O'=>array(
                'caption'=>'Отчество',
                'as'=>'F',
                'gr'=>12,
                'widget'=>'',
                'dbType'=>'VARCHAR(255)'
            ),
            'tel1'=>array(
                'caption'=>'Телефон',
                'as'=>'tel1',
                'gr'=>12,
                'widget'=>'input',
                'dbType'=>'VARCHAR(255)'
            ),
            'tel2'=>array(
                'caption'=>'Телефон дополнительный',
                'as'=>'tel2',
                'gr'=>12,
                'widget'=>'input',
                'dbType'=>'VARCHAR(255)'
            ),
            'avto_name'=>array(
                'caption'=>'Выбранный автомобиль',
                'as'=>'tel1',
                'gr'=>12,
                'widget'=>'input',
                'dbType'=>'TEXT'
            )
        ),
        'os_order'=>array(
            'ptype'=>array(
                'caption'=>'Тип покупателя (физ/юр)',
                'as'=>'ptype',
                'gr'=>12,
                'widget'=>'select',
                'dbType'=>'TINYINT(1)',
                'varList'=>array(
                    0=>'физическое лицо',
                    1=>'юридическое лицо'
                ),
                'varList2'=>array(
                    0=>'ФИЗ',
                    1=>'ЮР'
                )
            ),
            'method'=>array(
                'caption'=>'Способ оплаты',
                'as'=>'method',
                'gr'=>12,
                'widget'=>'select',
                'dbType'=>'TINYINT(1)',
                'varList'=>array(
                    0=>'наличными при получении',
                    1=>'безналичный расчет'
                ),
                'varList2'=>array(
                    0=>'НАЛ',
                    1=>'Б/Н'
                )
            ),
            'dc_id'=>array(
                'caption'=>'Идентификатор записи в таблице дисконтных карт',
                'as'=>'dc_id',
                'gr'=>12,
                'widget'=>'',
                'dbType'=>'INT(10)'
            ),
            'driverId'=>array(
                'caption'=>'Водитель',
                'as'=>'driverId',
                'gr'=>12,
                'widget'=>'',
                'dbType'=>'INT(10)'
            ),
            'deliveryDate'=>array(
                'caption'=>'Дата доставки',
                'as'=>'deliveryDate',
                'gr'=>12,
                'widget'=>'',
                'dbType'=>'DATE'
            ),
            'tel1'=>array(
                'caption'=>'Телефон',
                'as'=>'tel1',
                'gr'=>12,
                'widget'=>'input',
                'dbType'=>'VARCHAR(255)'
            ),
            'tel2'=>array(
                'caption'=>'Телефон дополнительный',
                'as'=>'tel2',
                'gr'=>12,
                'widget'=>'input',
                'dbType'=>'VARCHAR(255)'
            ),
            'person'=>array(
                'caption'=>'Контактное лицо',
                'as'=>'tel1',
                'ptype'=>1,
                'gr'=>12,
                'widget'=>'input',
                'dbType'=>'TEXT'
            ),
            'u_addr'=>array(
                'caption'=>'Юридический адрес',
                'as'=>'u_addr',
                'gr'=>12,
                'ptype'=>1,
                'widget'=>'textarea',
                'dbType'=>'TEXT'
            ),
            'INN'=>array(
                'caption'=>'ИНН',
                'as'=>'INN',
                'gr'=>12,
                'ptype'=>1,
                'widget'=>'input',
                'dbType'=>'TEXT'
            ),
            'KPP'=>array(
                'caption'=>'КПП',
                'as'=>'KPP',
                'gr'=>12,
                'ptype'=>1,
                'widget'=>'input',
                'dbType'=>'TEXT'
            ),
            'bank'=>array(
                'caption'=>'Банк',
                'as'=>'bank',
                'gr'=>12,
                'ptype'=>1,
                'widget'=>'input',
                'dbType'=>'TEXT'
            ),
            'BIK'=>array(
                'caption'=>'БИК',
                'as'=>'bank',
                'ptype'=>1,
                'gr'=>12,
                'widget'=>'input',
                'dbType'=>'TEXT'
            ),
            'rs'=>array(
                'caption'=>'Рассчетный счет',
                'as'=>'rs',
                'gr'=>12,
                'ptype'=>1,
                'widget'=>'input',
                'dbType'=>'TEXT'
            ),
            'ks'=>array(
                'caption'=>'Корр счет',
                'as'=>'ks',
                'ptype'=>1,
                'gr'=>12,
                'widget'=>'input',
                'dbType'=>'TEXT'
            ),
            'avto_name'=>array(
                'caption'=>'Выбранный автомобиль',
                'as'=>'avto_name',
                'gr'=>12,
                'widget'=>'input',
                'dbType'=>'TEXT'
            ),
            'subscribe'=>array(
                'caption'=>'Подписка на рассылку',
                'as'=>'subscribe',
                'gr'=>12,
                'value'=>1,
                'widget'=>'checkbox',
                'dbType'=>'TINYINT(1)'
            ),
            'director'=>array(
                'caption'=>'ФИО Генерального директора в именительном падеже',
                'as'=>'director',
                'ptype'=>1,
                'gr'=>12,
                'widget'=>'input',
                'dbType'=>'VARCHAR(255)'
            ),
            'directorGenitive'=>array(
                'caption'=>'ФИО Генерального директора в родительном падеже',
                'as'=>'directorGenitive',
                'ptype'=>1,
                'gr'=>12,
                'widget'=>'input',
                'dbType'=>'VARCHAR(255)'
            ),
            'carrier_co'=>array(
                'caption'=>'Транспортная компания',
                'as'=>'carrier_co',
                'gr'=>12,
                'style'=>'background-color:#E1CCCC',
                'widget'=>'input',
                'dbType'=>'VARCHAR(255)'
            ),
            'passport'=>array(
                'caption'=>'Паспортные данные',
                'as'=>'passport',
                'gr'=>12,
                'style'=>'height:40px; background-color:#E1CCCC',
                'widget'=>'textarea',
                'dbType'=>'TEXT'
            ),
            'deliveryTime'=>array(
                'caption'=>'Время доставки',
                'as'=>'deliveryTime',
                'gr'=>12,
                'style'=>'background-color:#E1CCCC',
                'widget'=>'input',
                'dbType'=>'TEXT'
            ),
            'TTN'=>array(
                'caption'=>'ТТН',
                'as'=>'TTN',
                'gr'=>12,
                'widget'=>'',
                'dbType'=>'VARCHAR(255)'
            ),
            'suplrPaymentDate'=>array(
                'caption'=>'Время доставки',
                'as'=>'suplrPaymentDate',
                'gr'=>12,
                'widget'=>'',
                'dbType'=>'DATE'
            ),
            'billDate'=>array(
                'caption'=>'Дата счета',
                'as'=>'billDate',
                'gr'=>12,
                'widget'=>'',
                'dbType'=>'DATE'
            ),
            'SMSTel'=>array(
                'caption'=>'Номер телефона для СМС уведомлений',
                'as'=>'SMSTel',
                'gr'=>12,
                'widget'=>'',
                'dbType'=>'VARCHAR(255)'
            ),
            'delayedOn'=>array(
                'caption'=>'Вернуть заказ в состояние новый этого числа',
                'as'=>'delayedOn',
                'gr'=>12,
                'widget'=>'',
                'dbType'=>'DATETIME'
            ),
            'cancelReason'=>array(
                'caption'=>'Причина отказа',
                'as'=>'cancelReason',
                'gr'=>12,
                'widget'=>'',
                'dbType'=>'VARCHAR(255)'
            )
        ),
        'os_item'=>array(
            'pprice'=>array(
                'caption'=>'Закупка',
                'as'=>'pprice',
                'gr'=>12,
                'widget'=>'input',
                'dbType'=>'DECIMAL(10,2)'
            ),
            'suplrId'=>array(
                'caption'=>'Поставщик',
                'as'=>'suplrId',
                'gr'=>12,
                'widget'=>'select',
                'dbType'=>'INT(10)'
            ),
            'reserveNum'=>array(
                'caption'=>'Номер резерва',
                'as'=>'reserveNum',
                'gr'=>12,
                'widget'=>'input',
                'dbType'=>'VARCHAR(255)'
            ),
            'reserveDate'=>array(
                'caption'=>'Дата резерва',
                'as'=>'reserveDate',
                'gr'=>12,
                'widget'=>'input',
                'dbType'=>'DATE'
            )
        ),
        'os_dop'=>array(
            'pprice'=>array(
                'caption'=>'Закупка',
                'as'=>'pprice',
                'gr'=>12,
                'widget'=>'input',
                'dbType'=>'DECIMAL(10,2)'
            )
        ),
        'reviews'=>array(
            'avtoName'=>array(
                'caption'=>'Автомобиль',
                'as'=>'avtoName',
                'gr'=>1,
                'dbType'=>'varchar(255)'
            )
        )

    );

}


