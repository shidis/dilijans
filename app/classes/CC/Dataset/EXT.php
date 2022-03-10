<?
if (!defined('true_enter')) die ("Direct access not allowed!");

class App_CC_Dataset_EXT extends CC_Dataset {
	
	public $dataFields=array(
		'b_text'=>				array('type'=>'string', 'require'=>false,'info'=>'Текстовое описание'),
		'b_imgs'=>				array('type'=>'string', 'require'=>false,'info'=>'Изображения'),
		'b_alt'=>				array('type'=>'string', 'require'=>false,'info'=>'Альтернативное название'),

		'm_text'=>				array('type'=>'string', 'require'=>false,'info'=>'Текстовое описание'),
		'm_imgs'=>				array('type'=>'string', 'require'=>false,'info'=>'Изображения без логотипа'),
		'm_alt'=>				array('type'=>'string', 'require'=>false,'info'=>'Альтернативное название'),

		't_cprice'=>			array('type'=>'string', 'require'=>false,'info'=>'Розничная цена'),
		't_bprice'=>			array('type'=>'string', 'require'=>false,'info'=>'Базовая цена/статус НеОбновлять/фиксЦена'),
	);
	

	function __construct()
	{
		parent::__construct();
	}


	public function export()
	{

		
	}

}	