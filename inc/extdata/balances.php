<?
function array_cp1251($a)
{
	foreach($a as &$v) $v=@Tools::cp1251($v);
	return $a;
}
define('MAX_TIME', 720);
set_time_limit(MAX_TIME);
ini_set('max_execution_time', MAX_TIME);
ini_set('mysql.connect_timeout', MAX_TIME);
ini_set('default_socket_timeout', MAX_TIME);

$fname=@$_REQUEST['__q'];

//* у файла должно быть расширение. sname получаем, отбрасывая его
$fname=explode('.',$fname);
$ext=array_pop($fname);
$gr_arr=explode('_',$fname[0]);
$fname=join('.',$fname);

if($gr_arr[1] == 'shiny') $gr = 1; else $gr = 2;

if($fname=='') exit;

$where = Array();
if (strstr($fname, 'all-')){
	$where = array('cc_cat.sc>=4');
}else{
	$where = array('is_balances=1', 'cc_cat.sc>=4');
}

@define (true_enter,1);
require_once ($_SERVER['DOCUMENT_ROOT'].'/config/init.php');

BotLog::detect();
if (empty($_GET['debug'])) {
	header('Content-Disposition: attachment; filename=' . $fname . '.' . $ext);
	header("Pragma: no-cache");
	header("Expires: 0");
}
$cc=new CC_Base();
$n=$cc->cat_view(array(
	'gr'=>$gr,
	'nolimits'=>1,
	'ex'=>1,
	'exFields'=>array('brand'=>array(), 'MP1'=>array()),
	'where' => $where,
	'order'=>'cc_cat.gr, cc_brand.name, cc_model.name'
));
switch($ext){
	case 'xml':
			header('Content-Type:application/xml; charset=utf-8');
			//
			echo '<?xml version="1.0" encoding="utf-8"?>'; echo "\n";
			echo "<offers>"; echo "\n\n";
			while($cc->next()!==false){
				$r = Array();
				if(($gr==1 && $cc->qrow['P1']>0  && $cc->qrow['P2']>0 && $cc->qrow['P3']>0) || $gr==2 && ($cc->qrow['P2']>0 && $cc->qrow['P5'] && $cc->qrow['P4'] && $cc->qrow['P6'])) {
					echo "<offer>"; echo "\n\n";
					$r[] = "<article>".Tools::xml_sformat($cc->qrow['cat_id'])."</article>";
					$r[] = "<brand>".Tools::xml_sformat($cc->qrow['bname'])."</brand>";
					$r[] = "<model>".Tools::xml_sformat($cc->qrow['mname'])."</model>";
					if ($gr==2)	$r[] = "<name>".Tools::xml_sformat(Tools::n($cc->qrow['bname'].' '.$cc->qrow['mname']." {$cc->qrow['P2']}Jx{$cc->qrow['P5']}"." {$cc->qrow['P4']}/{$cc->qrow['P6']}"." ET{$cc->qrow['P1']}".($cc->qrow['P3']!=0?" DIA{$cc->qrow['P3']}":'').($cc->qrow['csuffix']!=''?" {$cc->qrow['csuffix']}":'')))."</name>";
					else $r[] = "<name>".Tools::xml_sformat(Tools::n($cc->qrow['bname'].' '.$cc->qrow['mname']." {$cc->qrow['P3']}/{$cc->qrow['P2']} R{$cc->qrow['P1']}".($cc->qrow['csuffix']!=''?" {$cc->qrow['csuffix']}":'')))."</name>";

					$r[] = "<picture>".$cc->make_img_path(2)."</picture>";
					// Размеры
					if ($gr == 1) {
						$r[] = "<P1>".Tools::n($cc->qrow['P1'])."</P1>";
						$r[] = "<P2>".Tools::n($cc->qrow['P2'])."</P2>";
						$r[] = "<P3>".Tools::n($cc->qrow['P3'])."</P3>";
					} elseif ($gr == 2) {
						$r[] = "<P5>".Tools::n($cc->qrow['P5'])."</P5>";
						$r[] = "<P2>".Tools::n($cc->qrow['P2'])."</P2>";
						$r[] = "<P4>".Tools::n($cc->qrow['P4'])."</P4>";
						$r[] = "<P6>".Tools::n($cc->qrow['P6'])."</P6>";
						$r[] = "<P1>".Tools::n($cc->qrow['P1'])."</P1>";
						$r[] = "<P3>".Tools::n($cc->qrow['P3'])."</P3>";
					}
					//$r[] = "<applicability>".Tools::unesc("Тут будет примеяемость")."</applicability>";

					if ($gr == 2){
						switch ($cc->qrow['MP1']){
							case 1:
								$r[] = "<type>".'Кованый'."</type>";
								break;
							case 2:
								$r[] = "<type>".'Литой'."</type>";
								break;
							case 3:
								$r[] = "<type>".'Штампованный'."</type>";
								break;
							default:
								$r[] = "<type></type>";
								break;
						}
					}
					else{
						switch ($cc->qrow['MP1']){
							case 1:
								$r[] = "<type>".'Летняя'."</type>";
								break;
							case 2:
								$r[] = "<type>".'Зимняя'."</type>";
								break;
							case 3:
								$r[] = "<type>".'Всесезонная'."</type>";
								break;
							default:
								$r[] = "<type></type>";
								break;
						}
					}
					if ($gr == 2){
						$r[] = "<color>".Tools::xml_sformat($cc->qrow['csuffix'])."</color>";
					}
					else{
						$s=explode(' ',$cc->qrow['csuffix']);
						if(array_search('C',$s)) {
							$s=array_diff($s,array('C'));
						}
						$s=implode(' ',$s);
						$r[] = "<isin>".Tools::xml_sformat($cc->qrow['P7'])."</isin>";
						$r[] = "<params>".Tools::xml_sformat($s)."</params>";
					}

					$r[] = "<availability>".Tools::n($cc->qrow['sc'])."</availability>";

					$price = $cc->qrow['cprice'];
					if ($cc->qrow['scprice'] > 0) $price = $cc->qrow['scprice'];
					$r[] = "<price>".Tools::n($price)."</price>";
					echo implode("\n", $r);
					echo "</offer>"; echo "\n\n";
				}
			}
			echo "</offers>";
		break;
	case 'csv':
		header("Content-type: text/csv");

		$output = fopen("php://output", "w");
		$r[] = 'Артикул';
		$r[] = 'Бренд';
		$r[] = 'Модель';
		$r[] = 'Полное название товара';
		if ($gr == 2) {
			$r[] = 'Ширина';
			$r[] = 'Диаметр';
			$r[] = 'Отв.';
			$r[] = 'PCD';
			$r[] = 'ET';
			$r[] = 'DIA';
		}
		else{
			$r[] = 'Сезон';
			$r[] = 'Ширина';
			$r[] = 'Профиль';
			$r[] = 'Диаметр';
			$r[] = 'ИН/ИС';
		}
		$r[] = ($gr == 2) ? 'Цвет' : 'Параметры';
		if ($gr == 2) {
			$r[] = 'Тип диска';
		}
		$r[] = 'Остаток';
		$r[] = 'Цена';
		$r[] = (($gr == 2) ?  'Дизайн диска' : 'Фото шины');

		fputcsv($output, array_cp1251($r), ';');
		while($cc->next()!==false) {
			$r = Array();
			if (($gr == 1 && $cc->qrow['P1'] > 0 && $cc->qrow['P2'] > 0 && $cc->qrow['P3'] > 0) || $gr == 2 && ($cc->qrow['P2'] > 0 && $cc->qrow['P5'] && $cc->qrow['P4'] && $cc->qrow['P6'])) {
				$r[] = Tools::unesc($cc->qrow['cat_id']);
				$r[] = Tools::unesc($cc->qrow['bname']);
				$r[] = Tools::unesc($cc->qrow['mname']);
				if ($gr==2)	$r[] = Tools::unesc(Tools::n($cc->qrow['bname'].' '.$cc->qrow['mname']." {$cc->qrow['P2']}Jx{$cc->qrow['P5']}"." {$cc->qrow['P4']}/{$cc->qrow['P6']}"." ET{$cc->qrow['P1']}".($cc->qrow['P3']!=0?" DIA{$cc->qrow['P3']}":'').($cc->qrow['csuffix']!=''?" {$cc->qrow['csuffix']}":'')));
				else $r[] = Tools::unesc(Tools::n($cc->qrow['bname'].' '.$cc->qrow['mname']." {$cc->qrow['P3']}/{$cc->qrow['P2']} R{$cc->qrow['P1']}".($cc->qrow['csuffix']!=''?" {$cc->qrow['csuffix']}":'')));
				// Размеры
				if ($gr == 1) {
					switch ($cc->qrow['MP1']){
						case 1:
							$r[] = 'Летняя';
							break;
						case 2:
							$r[] = 'Зимняя';
							break;
						case 3:
							$r[] = 'Всесезонная';
							break;
						default:
							$r[] = '';
							break;
					}
					$r[] = Tools::n($cc->qrow['P3']);
					$r[] = Tools::n($cc->qrow['P2']);
					$r[] = Tools::n($cc->qrow['P1']);
					$r[] = $cc->qrow['P7'];
					$s=explode(' ',$cc->qrow['csuffix']);
					if(array_search('C',$s)) {
						$s=array_diff($s,array('C'));
					}
					$s=implode(' ',$s);
					$r[] = $s;
				} elseif ($gr == 2) {
					$r[] = Tools::n($cc->qrow['P2']);
					$r[] = Tools::n($cc->qrow['P5']);
					$r[] = Tools::n($cc->qrow['P4']);
					$r[] = Tools::n($cc->qrow['P6']);
					$r[] = Tools::n($cc->qrow['P1']);
					$r[] = Tools::n($cc->qrow['P3']);
				}
				if ($gr == 2){
					$r[] = $cc->qrow['csuffix'];
				}
				if ($gr == 2){
					switch ($cc->qrow['MP1']){
						case 1:
							$r[] = 'Кованый';
							break;
						case 2:
							$r[] = 'Литой';
							break;
						case 3:
							$r[] = 'Штампованный';
							break;
						default:
							$r[] = '';
							break;
					}
				}
				$r[] = $cc->qrow['sc'];

				$price = $cc->qrow['cprice'];
				if ($cc->qrow['scprice'] > 0) $price = $cc->qrow['scprice'];
				$r[] = $price;
				$r[] = $cc->make_img_path(2);

				fputcsv($output, array_cp1251($r), ';');
			}
		}
		break;
	case 'xls':
			include Cfg::_get('root_path').'/inc/PHPExcel/PHPExcel.php';
			if(!class_exists('PHPExcel')) die('CLASS PHPExcel не существует. Необходимо установить');
			$borders = array(
				'alignment' => array(
					'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
					'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
				),
				'borders' => array(
					'outline' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN,
						'color' => array('rgb' => '000000'),
					),
				)
			);
			$borders_bold = array(
				'alignment' => array(
					'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
					'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
				),
				'borders' => array(
					'outline' => array(
						'style' => PHPExcel_Style_Border::BORDER_MEDIUM,
						'color' => array('rgb' => '000000'),
					),
				)
			);
			$headers = Array();
			// Подготовка данных
			$data = Array();
			while($cc->next()!==false) {
				$data[$cc->qrow['brand_id']]['name'] = $cc->qrow['bname'];
				if (true) { // TODO: какие-нибудь проверки вывода
					$data[$cc->qrow['brand_id']]['models'][$cc->qrow['model_id']][] = $cc->qrow;
				}
			}
			//**************************************************************************************
			$headers[] = 'Артикул';
			if ($gr == 1) {
				$headers[] = 'Бренд';
			}
			if ($gr == 2) {
				$headers[] = 'Дизайн диска';
			}
			$headers[] = 'Название модели';
			if ($gr == 2) {
				$headers[] = 'Диаметр';
				$headers[] = 'Ширина';
				$headers[] = 'Сверловка';
				$headers[] = 'Вылет (ET)';
				$headers[] = 'DIA';
			}
			else{
				$headers[] = 'Ширина';
				$headers[] = 'Профиль';
				$headers[] = 'Диаметр';
				$headers[] = 'Ин/Ис';
				$headers[] = 'Доп.';
				$headers[] = 'Шипы';
				$headers[] = 'Сезон';
			}
			$headers[] = 'Применяемость';
			if ($gr == 2) {
				$headers[] = 'Тип диска';
				$headers[] = 'Цвет';
			}
			$headers[] = 'Наличие';
			$headers[] = 'Цена';
			// Создаем объект класса PHPExcel
			$xls = new PHPExcel();
			// Устанавливаем индекс активного листа
			$xls->setActiveSheetIndex(0);

			// Получаем активный лист
			$sheet = $xls->getActiveSheet();
			// Подписываем лист
			$sheet->setTitle($gr == 1 ? 'Шины '.date('d.m.Y') : 'Диски '.date('d.m.Y'));
			$sheet->getColumnDimension('A')->setAutoSize(true);
			// Вставляем Логотип в ячейку B1
			$objDrawing = new PHPExcel_Worksheet_Drawing();
			$objDrawing->setWorksheet($sheet);
			$objDrawing->setName("dilijans.org");
			$objDrawing->setDescription("dilijans.org");
			$objDrawing->setPath(Cfg::_get('root_path').'/app/images/logo.png');
			$objDrawing->setCoordinates('B1');
			$objDrawing->setWidth(250);
			$objDrawing->setOffsetX(25);
			$objDrawing->setOffsetY(15);
			// Размер ячейки A1
			$sheet->getRowDimension(1)->setRowHeight(70);
			$sheet->getColumnDimension('B')->setWidth(20);
			$sheet->getColumnDimension('C')->setWidth(30);
			$sheet->mergeCells("B1:C1");
			// Телефоны
			$sheet->mergeCells("D1:F1");

			$objRichText = new PHPExcel_RichText();
			$objRichText->createTextRun('8')->getFont()->setColor(new PHPExcel_Style_Color( PHPExcel_Style_Color::COLOR_BLACK))->setBold(true)->setSize(17);
			$objRichText->createTextRun(' (495) ')->getFont()->setColor(new PHPExcel_Style_Color( PHPExcel_Style_Color::COLOR_RED))->setBold(true)->setSize(17);
			$objRichText->createTextRun('662-58-82'.PHP_EOL.'8')->getFont()->setColor(new PHPExcel_Style_Color( PHPExcel_Style_Color::COLOR_BLACK))->setBold(true)->setSize(17);
			$objRichText->createTextRun(' (800) ')->getFont()->setColor(new PHPExcel_Style_Color( PHPExcel_Style_Color::COLOR_RED))->setBold(true)->setSize(17);
			$objRichText->createTextRun('555-11-08')->getFont()->setColor(new PHPExcel_Style_Color( PHPExcel_Style_Color::COLOR_BLACK))->setBold(true)->setSize(17);
			$sheet->setCellValue("D1", $objRichText);
			$sheet->getStyle('D1')->getAlignment()->setWrapText(true)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
			// Надпись
			$sheet->mergeCells("G1:K1");
			$sheet->setCellValue('G1', $gr == 1 ? 'Большой ассортимент шин. Редкие тюнинг размеры.' : 'Оригинальные дизайны дисков по доступным ценам');
			$sheet->getStyle('G1')->getFont()->setSize(14)->setBold(true);
			$sheet->getStyle('G1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
			// Ссылка
			$sheet->mergeCells("L1:M1");
			$sheet->getColumnDimension('L')->setWidth(10);
			$sheet->getColumnDimension('M')->setWidth(10);
			$sheet->setCellValue('L1', 'www.dilijans.org');
			$sheet->getCell('L1')->getHyperlink()->setUrl('https://www.dilijans.org');
			$sheet->getStyle('L1')->getFont()->setSize(14)->getColor()->applyFromArray(array('rgb' => '0000FF'));
			$sheet->getStyle('L1')->getFont()->setUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);
			$sheet->getStyle('L1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
			// Устанавливаем заголовки
			$k=0;
			foreach ($headers as $h)
			{
				$sheet->setCellValueByColumnAndRow(
					$k,
					2,
					$h);
				$sheet->getStyleByColumnAndRow(
					$k,
					2)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
				$sheet->getStyleByColumnAndRow(
					$k,
					2)->getFill()->getStartColor()->setRGB('16365C');
				$sheet->getStyleByColumnAndRow(
					$k,
					2)->getFont()->setBold(true)->getColor()->applyFromArray(array('rgb' => 'FFFFFF'));
				$sheet->getStyleByColumnAndRow(
					$k,
					2)->applyFromArray($borders_bold);
				$k++;
			}
			$i=3; // вертикальное начальное занчение
			if (!empty($data)){
				foreach ($data as $brand) {
					//Заголовок бренда
					$sheet->mergeCells("A$i:M$i");
					$sheet->setCellValueByColumnAndRow(
						0,
						$i,
						Tools::unesc($brand['name']));
					$sheet->getStyleByColumnAndRow(
						0,
						$i)->getFont()->setSize(14)->setBold(true)->getColor()->applyFromArray(array('rgb' => 'FFFFFF'));
					$sheet->getStyleByColumnAndRow(
						0,
						$i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
					$sheet->getStyleByColumnAndRow(
						0,
						$i)->getFill()->getStartColor()->setRGB('366092');
					$sheet->getStyle("A$i:".($gr==1?'K':'M')."$i")->applyFromArray($borders_bold);
					$i++;
					foreach ($brand['models'] as $model) {
						if ($gr == 2) {
							// Картинка
							$sheet->mergeCells("B$i:B" . ($i + count($model) - 1));
							if (is_file($cc->make_img_path($model[0]['img2'], true))) {
								$objDrawing = new PHPExcel_Worksheet_Drawing();
								$objDrawing->setWorksheet($sheet);
								$objDrawing->setName("dilijans.org");
								$objDrawing->setDescription("dilijans.org");
								$objDrawing->setPath($cc->make_img_path($model[0]['img2'], true));
								$objDrawing->setCoordinates('B' . $i);
								$objDrawing->setWidth(100);
								$objDrawing->setOffsetX(20);
								$objDrawing->setOffsetY($gr == 1 ? 10 : 25);
							}
							$sheet->getStyle("B$i:B" . ($i + count($model) - 1))->applyFromArray($borders_bold);
						}
						//
						foreach ($model as $row) {
							if (true) { // TODO: какие-нибудь проверки вывода
								if (count($model) < 6 && $gr == 2) {
									$sheet->getRowDimension($i)->setRowHeight(round(120 / count($model)));
								}
								// Артикул
								$burl = 'https://'.Cfg::$config['site_url'].'/'.App_Route::_getUrl($gr == 1 ? 'tTipo' : 'dTipo').'/';
								$sheet->getColumnDimensionByColumn(0)->setAutoSize(true);
								$sheet->setCellValueByColumnAndRow(
									0,
									$i,
									(Tools::unesc($row['cat_id'])));
								$sheet->getStyleByColumnAndRow(
									0,
									$i)->applyFromArray($gr == 1 ? $borders : $borders_bold);
								$sheet->getCellByColumnAndRow(
									0,
									$i)->getHyperlink()->setUrl($burl.$row['cat_sname'].'.html');
								$sheet->getStyleByColumnAndRow(
									0,
									$i)->getFont()->getColor()->applyFromArray(array('rgb' => '0000FF'));
								$sheet->getStyleByColumnAndRow(
									0,
									$i)->getFont()->setUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);
								//
								if ($gr == 1){
									// Бренд
									$sheet->getColumnDimensionByColumn(0)->setAutoSize(true);
									$sheet->setCellValueByColumnAndRow(
										1,
										$i,
										(Tools::unesc($row['bname'])));
									$sheet->getStyleByColumnAndRow(
										1,
										$i)->applyFromArray($borders);
									//
								}
								// Модель
								$sheet->getColumnDimensionByColumn(2)->setAutoSize(true);
								$sheet->setCellValueByColumnAndRow(
									2,
									$i,
									(Tools::unesc($row['mname'])));
								$sheet->getStyleByColumnAndRow(
									2,
									$i)->applyFromArray($borders);
								//
								// Размеры
								$j = 0; // Дальше только с переменной (разное кол-во столбцов)
								if ($gr == 1) {
									$j = 10;
									$sheet->getColumnDimensionByColumn(3)->setAutoSize(true);
									$sheet->setCellValueByColumnAndRow(
										3,
										$i,
										(Tools::n($row['P3'])));
									$sheet->getStyleByColumnAndRow(
										3,
										$i)->applyFromArray($borders);
									//
									$sheet->getColumnDimensionByColumn(4)->setAutoSize(true);
									$sheet->setCellValueByColumnAndRow(
										4,
										$i,
										(Tools::n($row['P2'])));
									$sheet->getStyleByColumnAndRow(
										4,
										$i)->applyFromArray($borders);
									//
									$sheet->getColumnDimensionByColumn(5)->setAutoSize(true);
									$sheet->setCellValueByColumnAndRow(
										5,
										$i,
										(Tools::n($row['P1'])));
									$sheet->getStyleByColumnAndRow(
										5,
										$i)->applyFromArray($borders);
									//
									$sheet->getColumnDimensionByColumn(6)->setAutoSize(true);
									$sheet->setCellValueByColumnAndRow(
										6,
										$i,
										(Tools::unesc($row['P7'])));
									$sheet->getStyleByColumnAndRow(
										6,
										$i)->applyFromArray($borders);
									//
									$s = explode(' ', $row['csuffix']);
									if (array_search('C', $s)) {
										$s = array_diff($s, array('C'));
									}
									$s = implode(' ', $s);
									$sheet->getColumnDimensionByColumn(7)->setAutoSize(true);
									$sheet->setCellValueByColumnAndRow(
										7,
										$i,
										(Tools::unesc($s)));
									$sheet->getStyleByColumnAndRow(
										7,
										$i)->applyFromArray($borders);
									//
									$sheet->getColumnDimensionByColumn(8)->setAutoSize(true);
									$sheet->setCellValueByColumnAndRow(
										8,
										$i,
										(!empty($row['MP3']) ? 'шип' : ''));
									$sheet->getStyleByColumnAndRow(
										8,
										$i)->applyFromArray($borders);
									//
									switch ($row['MP1']) {
										case 1:
											$type = 'Летняя';
											break;
										case 2:
											$type = 'Зимняя';
											break;
										case 3:
											$type = 'Всесезонная';
											break;
										default:
											$type = '';
											break;
									}
									$sheet->getColumnDimensionByColumn(9)->setAutoSize(true);
									$sheet->setCellValueByColumnAndRow(
										9,
										$i,
										$type);
									$sheet->getStyleByColumnAndRow(
										9,
										$i)->applyFromArray($borders);
								} elseif ($gr == 2) {
									$j = 8;
									$sheet->getColumnDimensionByColumn(3)->setAutoSize(true);
									$sheet->setCellValueByColumnAndRow(
										3,
										$i,
										(Tools::n($row['P5'])));
									$sheet->getStyleByColumnAndRow(
										3,
										$i)->applyFromArray($borders);
									//
									$sheet->getColumnDimensionByColumn(4)->setAutoSize(true);
									$sheet->setCellValueByColumnAndRow(
										4,
										$i,
										(Tools::n($row['P2'])));
									$sheet->getStyleByColumnAndRow(
										4,
										$i)->applyFromArray($borders);
									//
									$sheet->getColumnDimensionByColumn(5)->setAutoSize(true);
									$sheet->setCellValueByColumnAndRow(
										5,
										$i,
										(Tools::n($row['P4']) . 'x' . Tools::n($row['P6'])));
									$sheet->getStyleByColumnAndRow(
										5,
										$i)->applyFromArray($borders);
									//
									$sheet->getColumnDimensionByColumn(6)->setAutoSize(true);
									$sheet->setCellValueByColumnAndRow(
										6,
										$i,
										(Tools::n($row['P1'])));
									$sheet->getStyleByColumnAndRow(
										6,
										$i)->applyFromArray($borders);
									//
									$sheet->getColumnDimensionByColumn(7)->setAutoSize(true);
									$sheet->setCellValueByColumnAndRow(
										7,
										$i,
										(Tools::n($row['P3'])));
									$sheet->getStyleByColumnAndRow(
										7,
										$i)->applyFromArray($borders);
								}
								// ************* Применяемость *************
								$app_selected = unserialize($row['app']);
								if (!empty($app_selected))
								{
									$app = $app_selected;
								}
								else {
									$AB = new CC_AB();
									$app = Array();
									if ($gr == 2) {
										$_deltaDia = -0.1;
										$_deltaET = -5;
										$deltaET_ = 3;
										if ($gr == 2){
											$suitable = $AB->getAvtoArrayByTipo(Array(
												'P1' => array('_from'=>$_deltaET, '_to'=> $deltaET_, 'ex' => $row['P1']),
												'P2' => $row['P2'],
												'P3' => array('from'=>0, 'to'=> $row['P3'] + abs($_deltaDia)),
												'P4' => $row['P4'],
												'P5' => $row['P5'],
												'P6' => $row['P6']
											), $gr);
										}
									} else {
										$suitable = $AB->getAvtoArrayByTipo(Array(
											'P1' => $row['P1'],
											'P2' => $row['P2'],
											'P3' => $row['P3'],
										), 1);
										// *****************************************
									}
									if (!empty($suitable)) {
										foreach ($suitable as $brand => $models) {
											foreach ($models as $model => $modifs) {
												foreach ($modifs as $modif => $years) // Можно дальше пройтись по годам
												{
													$app[] = $brand . ' ' . $model . ' ' . $modif;
												}
											}
										}
									}
								}
								$sheet->getColumnDimensionByColumn($j)->setWidth(35);
								$sheet->setCellValueByColumnAndRow(
									$j,
									$i,
									implode("; ", $app));
								$sheet->getStyleByColumnAndRow(
									$j,
									$i)->applyFromArray($borders);
								$sheet->getStyleByColumnAndRow(
									$j,
									$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
								$j++;
								// Тип
								if ($gr == 2) {
									switch ($row['MP1']) {
										case 1:
											$type = 'Кованый';
											break;
										case 2:
											$type = 'Литой';
											break;
										case 3:
											$type = 'Штампованный';
											break;
										default:
											$type = '';
											break;
									}
									$sheet->getColumnDimensionByColumn($j)->setAutoSize(true);
									$sheet->setCellValueByColumnAndRow(
										$j,
										$i,
										($type));
									$sheet->getStyleByColumnAndRow(
										$j,
										$i)->applyFromArray($borders);
									$j++;
									// Цвет
									$cp = str_replace('nocolor', '', $row['csuffix']);
									$sheet->getColumnDimensionByColumn($j)->setAutoSize(true);
									$sheet->setCellValueByColumnAndRow(
										$j,
										$i,
										($cp));
									$sheet->getStyleByColumnAndRow(
										$j,
										$i)->applyFromArray($borders);
									$j++;
								}
								// Количество
								$color = '';
								$sc = Tools::n($row['sc']);
								if ($row['sc'] == 1){
									$color = 'FF0000';
								}elseif($row['sc'] < 4){
									$color = 'FFFF00';
								}elseif($row['sc'] >= 4 && $row['sc'] < 20){
									$color = '92D050';
								}elseif($row['sc'] >= 20){
									$color = '00B050';
									$sc = '>20';
								}
								$sheet->setCellValueByColumnAndRow(
									$j,
									$i,
									$sc);
								$sheet->getStyleByColumnAndRow(
									$j,
									$i)->applyFromArray($borders);
								$sheet->getStyleByColumnAndRow(
									$j,
									$i)->getFont()->setBold(true);
								$sheet->getStyleByColumnAndRow(
									$j,
									$i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
								$sheet->getStyleByColumnAndRow(
									$j,
									$i)->getFill()->getStartColor()->setRGB($color);
								$j++;
								// Цена
								$price = $row['cprice'];
								if ($row['scprice'] > 0) $price = $row['scprice'];
								if ($price == 0) $price = 'уточняйте';
								$sheet->setCellValueByColumnAndRow(
									$j,
									$i,
									(Tools::n($price)));
								$sheet->getStyleByColumnAndRow(
									$j,
									$i)->applyFromArray($borders);
								$sheet->getStyleByColumnAndRow(
									$j,
									$i)->getFont()->setBold(true);
							}
							$i++;
						}
					}
				}
				// Перетираем обводку страницы (фикс для толстых бордеров)
				$sheet->getStyle("A".($i - 1).":M".($i - 1))->applyFromArray(
				Array(
					'alignment' => array(
						'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
						'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
					),
					'borders' => array(
						'left' => array(
							'style' => PHPExcel_Style_Border::BORDER_THIN,
						),
						'right' => array(
							'style' => PHPExcel_Style_Border::BORDER_THIN,
						),
						'bottom' => array(
							'style' => PHPExcel_Style_Border::BORDER_MEDIUM,
						),
						'top' => array(
							'style' => PHPExcel_Style_Border::BORDER_THIN,
						),
					)
				)
				);
				$sheet->getStyle("M2:M".($i - 1))->applyFromArray(
					Array(
						'alignment' => array(
							'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
							'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
						),
						'borders' => array(
							'left' => array(
								'style' => PHPExcel_Style_Border::BORDER_THIN,
							),
							'right' => array(
								'style' => PHPExcel_Style_Border::BORDER_MEDIUM,
							),
							'bottom' => array(
								'style' => PHPExcel_Style_Border::BORDER_THIN,
							),
							'top' => array(
								'style' => PHPExcel_Style_Border::BORDER_THIN,
							),
						)
					)
				);
				$sheet->getStyle("M".($i - 1))->applyFromArray(
					Array(
						'alignment' => array(
							'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
							'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
						),
						'borders' => array(
							'left' => array(
								'style' => PHPExcel_Style_Border::BORDER_THIN,
							),
							'right' => array(
								'style' => PHPExcel_Style_Border::BORDER_MEDIUM,
							),
							'bottom' => array(
								'style' => PHPExcel_Style_Border::BORDER_MEDIUM,
							),
							'top' => array(
								'style' => PHPExcel_Style_Border::BORDER_THIN,
							),
						)
					)
				);
			}
			//
			$objWriter2007 = PHPExcel_IOFactory::createWriter($xls, 'Excel2007');
			$objWriter2007->save('php://output');
		break;
}

