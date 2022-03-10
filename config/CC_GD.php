<?

/*
 * DELETE FROM system_data WHERE group_id=5
 */


Cfg::$config=array_merge(Cfg::$config, array(

    // upload dir
    'cc_upload_dir' => 'assets/ui',

// gallery upload dir
    'cc_gal_upload_dir' => 'assets/ig',

// Certificates upload dir
    'cc_cert_upload_dir' => 'assets/ic',

// virtual path for cc class images
    'cc_images_dir' => 'cimg',
    'cc_gal_images_dir' => 'cgimg',
    'cc_cert_images_dir' => 'ccimg',

// папки на диске,для формирования пути к файлу. (путь записываемый в БД
    'cc_tyres_subdir' => 'shiny',
    'cc_wheels_subdir' => 'diski',
    'cc_brand_subdir' => 'b',
    'cc_model_subdir' => 'm',
    'cc_avto_subdir' => 'avto',

// путь до файла на фронте: http://site.ru/CC_IMAGES_PATH/CC_TYRES[WHEELS]_SUBDIR/CC_MODEL[BRAND}_SUBDIR/{1,2,3}/fname
    'cc_cache_images_dir' => 'assets/uic',
    'cc_gal_cache_images_dir' => 'assets/igc',
    'cc_cert_cache_images_dir' => 'assets/icc',

    'wmark_param' => array(
        'cc_model' => array(
            'left_x'=>0,
            'top_y'=>70,
            'width'=>100,
            'left_x1'=>5,
            'top_y1'=>25,
            'width1'=>100,
            'yx'=>100.3,
            'fill_color' => 0xFFFFFF,
            'quality'=>90,
            'fn'=>'wmark.png'
        ),
        'cc_gal' => array(
            'left_x'=> 0.01,
            'top_y'=> 10,
            'width'=> 33,
            'left_x1'=> 50,
            'top_y1'=> 15,
            'width1'=>70,
            'yx'=>1.5,
            'fill_color' => 0xFFFFFF,
            'quality'=>90,
            'fn'=>'gallery.wmark.new.png'
        ),
        'cc_cert' => array(
            'left_x'=> 0.01,
            'top_y'=> 10,
            'width'=> 33,
            'left_x1'=> 50,
            'top_y1'=> 15,
            'width1'=>70,
            'yx'=>1.5,
            'fill_color' => 0xFFFFFF,
            'quality'=>90,
            'fn'=>'gallery.wmark.new.png'
        )
    ),

    // ictrl transformations
    // оригиналы без логотипа лучше сохранять в качестве 100
    'cc_cache_transform'=>array(
        'wmark'=>array(
            'cc_model'=>array(
                '1'=>0,
                '2'=>1,
                '3'=>0
            ),
            'cc_gal'=>array(
                '1'=>0,
                '2'=>1,
                '3'=>1
            ),
            'cc_cert'=>array(
                '1'=>0,
                '2'=>1,
                '3'=>1
            )
        )
    ),

    // cc_brand_img[gr][img_num]['transform'][transMode][param]=value
    // операции из transform выполняются последжовательно

    'cc_brand_img'=>array(
        // tyres
        1=>array(
            1=>array(
                'size'=>array(
                    'w'=>140,
                    'h'=>85
                ),
                'transform'=>array(
                    array(
                        'action'=>'resize',
                        'outputFormat'=>'image/jpeg',
                        'quality'=>100,
                        'method'=>'SO',
                        'w'=>140,
                        'h'=>85
                    )
                )

            ),
            2=>array(
                'size'=>array(
                    'w'=>170,
                    'h'=>26
                ),
                'transform'=>array(
                    array(
                        'action'=>'resize',
                        'outputFormat'=>'image/png',
                        'quality'=>9,
                        'method'=>'NO',
                        'w'=>170,
                        'h'=>26
                    )
                )
            )
        ),
        //disks
        2=>array(
            1=>array(
                'size'=>array(
                    'w'=>140,
                    'h'=>85
                ),
                'transform'=>array(
                    array(
                        'action'=>'resize',
                        'outputFormat'=>'image/jpeg',
                        'quality'=>100,
                        'method'=>'SO',
                        'w'=>140,
                        'h'=>85
                    )
                )
            ),
            2=>array(
                'size'=>array(
                    'w'=>170,
                    'h'=>26
                ),
                'transform'=>array(
                    array(
                        'action'=>'resize',
                        'outputFormat'=>'image/png',
                        'quality'=>9,
                        'method'=>'SO',
                        'w'=>170,
                        'h'=>26
                    )
                )
            ),
            3=>array(
                'size'=>array(
                    'w'=>1024,
                    'h'=>1024
                ),
                'transform'=>array(
                    array(
                        'action'=>'resize',
                        'outputFormat'=>'image/jpeg',
                        'quality'=>100,
                        'method'=>'SO',
                        'w'=>1024,
                        'h'=>1024
                    )
                )
            )
        )
    ),

    'ab_avto_img'=>array(
        // tyres
        1=>array(
            1=>array(
                'size'=>array(
                    'w'=>290,
                    'h'=>130
                ),
                'transform'=>array(
                    array(
                        'action'=>'resize',
                        'outputFormat'=>'image/png',
                        'quality'=>9,
                        'method'=>'SO',
                        'w'=>290,
                        'h'=>130
                    )
                )

            )
        ),
        //disks
        2=>array(
            1=>array(
                'size'=>array(
                    'w'=>290,
                    'h'=>130
                ),
                'transform'=>array(
                    array(
                        'action'=>'resize',
                        'outputFormat'=>'image/png',
                        'quality'=>9,
                        'method'=>'SO',
                        'w'=>290,
                        'h'=>130
                    )
                )
            )
        )
    ),

    'cc_model_img'=>array(
        //tyres
        1=>array(
            1=>array(
                'size'=>array(
                    'w'=>185,
                    'h'=>370
                ),
                'transform'=>array(
                    array(
                        'action'=>'resize',
                        'outputFormat'=>'image/jpeg',
                        'quality'=>90,
                        'method'=>'SO',
                        'w'=>185,
                        'h'=>370
                    )
                )
            ),
            2=>array(
                'size'=>array(
                    'w'=>1200,
                    'h'=>1200
                ),
                'transform'=>array(
                    array(
                        'action'=>'resize',
                        'outputFormat'=>'image/jpeg',
                        'quality'=>100,
                        'method'=>'SO',
                        'w'=>1200,
                        'h'=>1200
                    )
                )
            ),
            3=>array(
                'size'=>array(
                    'w'=>70,
                    'h'=>70
                ),
                'transform'=>array(
                    array(
                        'action'=>'resize',
                        'outputFormat'=>'image/jpeg',
                        'quality'=>90,
                        'method'=>'SO',
                        'w'=>70,
                        'h'=>70
                    )
                )
            )
        ),
        //disks
        2=>array(
            1=>array(
                'size'=>array(
                    'w'=>185,
                    'h'=>185
                ),
                'transform'=>array(
                    array(
                        'action'=>'resize',
                        'outputFormat'=>'image/jpeg',
                        'quality'=>90,
                        'method'=>'SO',
                        'w'=>185,
                        'h'=>185
                    )
                )
            ),
            2=>array(
                'size'=>array(
                    'w'=>1200,
                    'h'=>1200
                ),
                'transform'=>array(
                    array(
                        'action'=>'resize',
                        'outputFormat'=>'image/jpeg',
                        'quality'=>100,
                        'method'=>'SO',
                        'w'=>1200,
                        'h'=>1200
                    )
                )
            ),
            3=>array(
                'size'=>array(
                    'w'=>70,
                    'h'=>70
                ),
                'transform'=>array(
                    array(
                        'action'=>'resize',
                        'outputFormat'=>'image/jpeg',
                        'quality'=>90,
                        'method'=>'SO',
                        'w'=>70,
                        'h'=>70
                    )
                )
            )
        )

    )






));