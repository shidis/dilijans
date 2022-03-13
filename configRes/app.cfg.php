<?
if (!defined('true_enter')) die ("No direct access allowed.");

class Cfg extends CfgGlobal
{

    public static function configOverrides()
    {
        static::$config = array_merge(static::$config,
            array(

// версия база подбора по авто, 2- база с nakolesah.ru
                'avto_bd_ver' => '2',

                //разрядов для номера карты
                'digit_n1_num' => '4',
                'digit_n2_num' => '4',

//тип авторизации скидки на сайте
                'dc_auth_type' => 'n1_n2',

// включен учет/регистрация покупателей на сайте
                'os_users' => 0,

//автоматическое добавление в корзину доп.комплектации
                'auto_dop_including' => '0',
                'dop_not_calc_amount' => '1',

                'apb_enabled' => '0',

// устаревшие параметры - решается запросами в реалтайм легче
// список ИН и список ИС
                'INIS_S1S2' => 0, // должны быть поля cc_model.S1,S2
                'INIS_S1S2_includeHide' => false, // включать в подсчет скрытые размеры
//список радиусов дисков
                'RDisk_S1' => 0, // должны быть поля cc_model.S1
                'RDisk_S1_includeHide' => false, // включать в подсчет скрытые размеры
// список радиусов шин
                'RTyre_R1' => 0, // должны быть поля cc_model.R1
                'RTyre_R1_includeHide' => false, // включать в подсчет скрытые размеры
//расчет мин и макс цены для модели
                'int_price_F1F2' => 0, // должны быть поля cc_model.F1,F2
                'int_price_F1F2_includeHide' => false, // включать в подсчет скрытые размеры
// суммарный складской остаток
                'model_SC' => 0, // должны быть поля cc_model.sc
                'model_SC_includeHide' => false, // считать склад у скрытых размеров
// -----

                'cc_filters_sc_not_zero' => 1, // в файловый кеш фильтров попадают только cc_cat.sc>0

//работа с красными ценами/скидками на сайте переменные t_discount/d_discount / discount_price()
                'td_discount' => '0',


                'url_suffix'=>'.html',

                // URL TRANSFORMATION
                'SNAME_CNT_REG'=>'lower',// upper, lower, ''
                'SNAME_CNT_LEN'=>255,//максимальная длина псевдонима

                // шаблон псевдонима бренда
                'SNAME_BRAND_TPL'=>'#B',//#B-бренд (транслит)   ... по умолчанию #B
                'SNAME_BRAND_DAREA'=>1, // 1-в пределах gr,  0-не проверять
                'SNAME_BRAND_REG'=>'',// upper, lower, ''
                'SNAME_BRAND_LEN'=>255,//максимальная длина псевдонима

                // шаблон псевдонима модели
                'SNAME_MODEL_TPL'=>'#B-#M',//#B-бренд (транслит)  #M-модель (транслит) ... по умолчанию #M
                //область проверки дубликатов псевдонима модели
                'SNAME_MODEL_DAREA'=>1,// 1-в пределах gr, 2- в пределах brand_id, 0-не проверять
                'SNAME_MODEL_SUFFIX'=>1,//добавлять ли суффикс в конце модели  через знак - если есть
                'SNAME_MODEL_LEN'=>255,//максимальная длина псевдонима
                'SNAME_MODEL_REG'=>'',// upper, lower, ''
                // шаблон псевдонима типоразмеров
                //#B-бренд (транслит),  #M-модель (транслит) #P1-#P7 - значения полей P, #C - sprintf("%u\n",crc32(P1P2P3P4P5P6P7suffix)) по умолчанию #C
                // #Z -> Z если скоростная		''=>'',
                'SNAME_CAT_TPL2'=>'#B-#M-#P2Jx#P5-#P4x#P6-ET#P1-D#P3',// gr=2
                'SNAME_CAT_TPL1'=>'#B-#M-#P3x#P2#ZR#P1-#P7', //gr=1
                //область проверки дубликатов псевдонима модели
                'SNAME_CAT_DAREA'=>1,// 1-в пределах gr, 2- в пределах model_id, 0-не проверять
                'SNAME_CAT_SUFFIX'=>1,//добавлять ли суффикс в конце   через знак - если есть
                'SNAME_CAT_LEN'=>255, //максимальная длина псевдонима
                'SNAME_CAT_REG'=>'',// upper, lower, ''
                // шаблон для картинок cc.
                'SNAME_BRAND_IMG_TPL'=>'#B-i#ID',// #ID - идентификатор числовой записи/ Допустимы #B #ID
                'SNAME_BRAND_IMG_REG'=>'',
                'SNAME_BRAND_IMG_LEN'=>255,
                'SNAME_MODEL_IMG_TPL'=>'#B-#M-i#ID',// допустим #B #M #ID
                'SNAME_MODEL_IMG_REG'=>'',
                'SNAME_MODEL_IMG_LEN'=>255,
                'SNAME_AVTO_IMG_TPL'=>'#V-#MD-#Y-#MI',// vendor, model, year, modif
                'SNAME_AVTO_IMG_REG'=>'',
                'SNAME_AVTO_IMG_LEN'=>255,

                'CAT_IMPORT_MODE' => 2, // 1 - используется модуль импорта с привязкой по кодам ТИ (версии модуля 1,2,3), 2- ver 4 модуля (класс CC_CII), 3- версия 4.2 (класс CC_CII2 - dilijans)

                'datasetDir' => 'extdata',

                'datasetClasses'=>array	(
                    'YM'=>array(
                        'pos'=>1,
                        'name'=>'Яндекс маркет',
                        'ext'=>'xml'
                    ),

                    'EXT'=>array(
                        'pos'=>2,
                        'name'=>'Внутренний формат',
                        'ext'=>''
                    ),

                    'YML'=>array(
                        'pos'=>3,
                        'name'=>'Yandex Market Language',
                        'ext'=>'xml'
                    )
                ),

                'showTSklad' => 1,
                'showDSklad' => 1,

                'cmsShowHitQuant' => 0,
                'waitList' => 0,

                'ccTags' => array(
                    'enabled' => false,
                    'sname_reg' => 'lower',
                    'sname_len' => 255
                ),

                'GA'=>array(
                    'account'=>'UA-10715452-1',
                    'domainName'=>'dilijans.org'
                ),
                'YAM'=>array(
                    'counterId'=>'188708'
                ),

                'orderFilesDir'=>'assets/order_files',

                // отзывы
                'reviews' => array(
                    '1'=>array(
                        'enabled' => 1, // включены ли отзывы
                        'ratingScale' => 5, // максимальная оценка
                        'voting' => false, // для голосования нужны три поля в reviews: votes, votesPlus, votesMinus
                        // пункты оценки
                        'ratingItems' => array(
                            '10' => 'Низкий уровень шума',
                            '20' => 'Акустический комфорт',
                            '30' => 'Сцепление с дорогой',
                            '40' => 'Управляемость'
                        ),
                        // пары роль => статус при добавлении отзыва
                        // 0- новый/не отмодерирован (1)- одобрен (-1) - отменен (2) - на модерацию от наемного публикатора
                        'roles' => array(
                            1=>1,
                            2=>1,
                            6=>1,
                            5=>1,
                            3=>1,
                            60=>2
                        ),
                        'onlyOneByProd'=>true, // посетители могут осьтавлять только один отзыв для одного товара
                        // статус при добавлении отзыва посетителем сайта / ==false если добавление неавторизованым запрещено
                        'defaultState' => 0
                    ),
                    'MCVoteLifeTime'=>172800,  // время жизни голоса за отзыв в МС в секундах
                ),

                'rating'=>[
                    'models'=>[
                        // шины
                        '1'=>[
                            'reviews'=>[
                                // reviews.state отзывов участвующих в расчете рейтинга модели
                                'states'=>[1]
                            ]
                        ]
                    ]
                ]


            ));


        require_once dirname(__FILE__).'/CC_GD.php';

        if (!isset(Cfg::$config['cc_upload_path'])) Cfg::$config['cc_upload_path'] = Cfg::$config['root_path'] . '/' . Cfg::$config['cc_upload_dir'];

        if (!isset(Cfg::$config['cc_cache_images_path'])) Cfg::$config['cc_cache_images_path'] = Cfg::$config['root_path'] . '/' . Cfg::$config['cc_cache_images_dir'];

    }

}
