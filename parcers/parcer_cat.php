<html>
<head>
    <meta charset="UTF-8">
</head>
<body>
<?
define('MAX_TIME', 2400);
set_time_limit(MAX_TIME);
ini_set('max_execution_time', MAX_TIME);
ini_set('mysql.connect_timeout', MAX_TIME);
ini_set('default_socket_timeout', MAX_TIME);
$counter = 0;
$s_counter = 0;
$translit = array(

    'а' => 'a',   'б' => 'b',   'в' => 'v',

    'г' => 'g',   'д' => 'd',   'е' => 'e',

    'ё' => 'yo',   'ж' => 'zh',  'з' => 'z',

    'и' => 'i',   'й' => 'j',   'к' => 'k',

    'л' => 'l',   'м' => 'm',   'н' => 'n',

    'о' => 'o',   'п' => 'p',   'р' => 'r',

    'с' => 's',   'т' => 't',   'у' => 'u',

    'ф' => 'f',   'х' => 'x',   'ку' => 'q',

    'ч' => 'ch',  'ш' => 'sh',  'щ' => 'shh',

    'ь' => '\'',  'уай' => 'y',   'ъ' => '\'\'',

    'э' => 'e\'',   'ю' => 'yu',  'я' => 'ya', 'тз' => 'th',


    'А' => 'A',   'Б' => 'B',   'В' => 'V',

    'Г' => 'G',   'Д' => 'D',   'Е' => 'E',

    'Ё' => 'YO',   'Ж' => 'Zh',  'З' => 'Z',

    'И' => 'I',   'Й' => 'J',   'К' => 'K',

    'Л' => 'L',   'М' => 'M',   'Н' => 'N',

    'О' => 'O',   'П' => 'P',   'Р' => 'R',

    'С' => 'S',   'Т' => 'T',   'У' => 'U',

    'Ф' => 'F',   'Х' => 'X',   'Ку' => 'Q',

    'Ч' => 'CH',  'Ш' => 'SH',  'Щ' => 'SHH',

    'Ь' => '\'',  'Уай' => 'Y',   'Ъ' => '\'\'',

    'Э' => 'E\'',   'Ю' => 'YU',  'Я' => 'YA', 'ТЗ' => 'TH',

);
// ******************************************************************************************************
/* Подключение к серверу MySQL и создание экземпляра класса БД */
if (!empty($_POST['processing']) && !empty($_POST['content'])) {
    @define(true_enter, 1);
    // Where
    $where = Array();
    $where[] = " (cc_cat.gr = '1') "; // Шины
    $where[] = " (cc_model.P1 = '" . ((int)$_POST['t_type']) . "') "; // Сезонность
    //
    file_put_contents('cat_parcer_' . date('d_m_Y') . '.log', "[" . date('H:i:s') . "]\t Начало импорта типоразмеров\n--------------------------------------------------------------------------\n", FILE_APPEND);
    require_once($_SERVER['DOCUMENT_ROOT'] . '/config/init.php');
    $DB = new DB();
    $models = $DB->fetchAll("SELECT cc_cat.cat_id, cc_cat.gr, cc_cat.sname,
                                cc_model.model_id, cc_model.name, cc_brand.name as bname, cc_brand.alt,
                                cc_cat.P1+'0' AS P1,
                                cc_cat.P2+'0' AS P2,
                                cc_cat.P3+'0' AS P3
                                FROM cc_cat
                                INNER JOIN (cc_model JOIN cc_brand ON cc_model.brand_id = cc_brand.brand_id) ON cc_cat.model_id = cc_model.model_id
                                WHERE NOT cc_cat.LD AND NOT cc_model.LD AND NOT cc_brand.LD AND " . implode(' AND ', $where), MYSQLI_ASSOC);
    foreach ($models as $model) {
        //Ищем запись в "страницах"
        $page = $DB->getOne("SELECT page_id, url FROM ss_pages WHERE url = '".'/'.App_Route::_getUrl('tTipo').'/'.$model['sname'].'.html'."';", MYSQLI_ASSOC);
        if ((empty($page) && empty($_POST['has_page'])) || (!empty($page) && !empty($_POST['has_page']))) {
        $model_name = str_replace(Array('w', 'W', 'c', 'C', 'h', 'H'), Array('v', 'V', 'k', 'K', 'x', 'X'), preg_replace('%[^A-Za-zА-Яа-я0-9\s]%', '', $model['name']));
        $brand_name = (!empty($model['alt']) ? $model['alt'] : strtr($model['bname'], array_flip($translit)));
        $content = Tools::esc(trim($_POST['content']));
        $content = str_replace(Array('{model}', '{model_alt}', '{val_1}', '{val_2}', '{radius}'), Array($model['bname'].' '.$model['name'], $brand_name.' '.strtr($model_name, array_flip($translit)), $model['P3'], $model['P2'], $model['P1']), $content);
        // ***
        $update_query = "UPDATE cc_cat SET {$_POST['object_to_modify']} = '{$content}' WHERE  cat_id = '{$model['cat_id']}'";
        $res = $DB->query($update_query, true);
        if ($res) {
            $s_counter++;
            file_put_contents('cat_parcer_' . date('d_m_Y') . '.log', "[" . date('H:i:s') . "]\tМодель [{$model['model_id']}][{$model['cat_id']}]: " . $_POST['object_to_modify'] . " - изменено! Новое значение: $content\n", FILE_APPEND);
        } else {
            file_put_contents('cat_parcer_' . date('d_m_Y') . '.log', "[" . date('H:i:s') . "]\tЗапрос: " . $update_query . " - ошибка!\n", FILE_APPEND);
        }
        }
        else{
            file_put_contents('cat_parcer_' . date('d_m_Y') . '.log', "[" . date('H:i:s') . "]\tМодель [{$model['model_id']}][{$model['cat_id']}]: найдена страница {$page['url']} - пропущено!\n", FILE_APPEND);
        }
        $counter++;
    }
    echo "
            <hr><div>Изменено: $s_counter / $counter</div>
            <div><b>Поле:</b> {$_POST['object_to_modify']}</div>
         ";
        file_put_contents('cat_parcer_' . date('d_m_Y') . '.log', "[" . date('H:i:s') . "]\tИмпорт завершен - изменено: $s_counter / $counter\n", FILE_APPEND);
} else {
    ?>
    <center><h1>Пакетная обработка мета-тегов типоразмеров</h1></center>
    <form action="" method="post">
        <table border="1" width="500px" style="margin: 0px auto;">
            <tr>
                <td>
                    <div><b>Тип шин:</b></div>
                    <select name="t_type" id="t_type">
                        <option value="2">Зимние</option>
                        <option value="1">Летние</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>
                    <b>Объект для изменения: </b>
                    <select name="object_to_modify" id="object_to_modify">
                        <option value="seo_title">Title</option>
                        <option value="seo_description">Description</option>
                        <option value="seo_keywords">Keywords</option>
                        <option value="adv_text">Text</option>
                    </select>
                    <div><b>Страница:</b> <input type="checkbox" value="1" name="has_page"></div>
                </td>
            </tr>
            <tr>
                <td>
                    <b>Контент: </b>
                    <textarea name="content" id="" cols="70" rows="10"></textarea>
                    <b>Легенда:</b>
                    <table border="0" width="100%">
                        <tr>
                            <td>МОДЕЛЬ ШИНЫ</td>
                            <td>{model}</td>
                        </tr>
                        <tr>
                            <td>МОДЕЛЬ ШИНЫ ПО РУССКИ</td>
                            <td>{model_alt}</td>
                        </tr>
                        <tr>
                            <td>Размер 1</td>
                            <td>{val_1}</td>
                        </tr>
                        <tr>
                            <td>Размер 2</td>
                            <td>{val_2}</td>
                        </tr>
                        <tr>
                            <td>Радиус</td>
                            <td>{radius}</td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td>
                    <center><input name="processing" type="submit" value="Обработка"></center>
                </td>
            </tr>
        </table>
    </form>
<? } ?>
</body>
</html>