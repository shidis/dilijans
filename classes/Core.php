<?

class Core{
	
	public static $config=array();


/* 
	Наследуемые классы контроллеров должны иметь окончание _Controller и каждый фрагмент пути через _ должен иметь заглавную букву
	Название файла контроллера и папки должна начинаться с маленькой буквы и должен включать в себя путь до файла. Фрагменты пути также с заглавной буквы.
	
	Например, путь к контроллеру app/catalog/models/index подключит файл app/catalog/models.php и запустит метод index().
	Название класса при это м должно быть App_Catalog_Models_Controller
	
	
	Путь к файлу модели должен быть в названии класса с разделителем _. Поиск модели делается сначала по всему пути в названии модели ,затем в /classes/+название модели
	Название файла модели и папки должна начинаться с заглавной буквы и должен включать в себя путь до файла от корня.
	Имя файла модели повоторяет в точности имя класса модели.
*/

	public static function autoload($class)
    {
        try {
            $path=explode('_',$class);

            if(count($path)>1){
			
                if($path[count($path)-1]=='Controller'){
                    $isCtrl=true;
                    array_pop($path); // убираем слово Controller с конца
                    foreach($path as $k=>&$v) $v=mb_strtolower(mb_substr($v,0,1)).mb_substr($v,1);
                    $paths='/'.array_shift($path).'/controllers/'.implode('/',$path).'.php';
                }else{
                    $paths='yes';
                }

            }else $paths='';

            $rootPath=Cfg::_get('root_path');

            // если контроллер
            if(@$isCtrl) {

                if(is_file($rootPath.$paths))
                    require_once($rootPath.$paths);
                else
                    throw new CoreException('[Core::autoLoad]: ['.$class.'] controller class file not exists in path ['.$paths.']', 701);

            } else {

                // если модель

                if($paths==''){ // класс без единого _

                    if(is_file($rootPath.'/classes/'.$class.'.php'))

                        include_once($rootPath.'/classes/'.$class.'.php');

                    else throw new CoreException('[Core::autoLoad]: for ['.$class.'] class -  file not exists in path [/classes/'.$class.'.php]', 702);

                } else {
                    // класс хотя бы с одним _

                    // первый поиск в папка приложения:

                    // уменьшаем первую букву первой части пути и формируем путь
                    $_path=$path;
                    $paths='/'.mb_strtolower(mb_substr($s=array_shift($path),0,1)).mb_substr($s,1).'/classes/'.implode('/',$path).'.php';

                    if(is_file($rootPath.$paths)) {

                        include_once($rootPath.$paths);

                    } else {
                        $path=$_path;
                        $paths1=$paths;

                        // второй поиск в папке /classes/
                        $paths='/classes/'.implode('/',$path).'.php';

                        if(is_file($rootPath.$paths)) {

                            include_once($rootPath.$paths);

                        } else throw new CoreException('[Core::autoLoad]: for ['.$class.'] class - file not exists in path ['.$paths1.'] and in path ['.$paths.'] also.', 703);
                    }
                }
            }
        } catch (CoreException $e){
            $e->getError();
        }
    }

        public static function exception_text(Exception $e)
    {
        return sprintf('%s [ %s ]: %s ~ %s [ %d ]',
            get_class($e), $e->getCode(), Tools::stripTags($e->getMessage()), realpath($e->getFile()), $e->getLine());
	}
	
}


class CommonException extends Exception
{
	
	public function __construct($message, $code=0)
	{

		// Pass the message to the parent
		parent::__construct($message, $code);
	}

	public function __toString()
	{
		return Core::exception_text($this);
	}

    function getError($noHalt = false)
    {
        @header('HTTP/1.1 503 Service Temporarily Unavailable');
        @header('Status: 503 Service Temporarily Unavailable');
        @header('Retry-After: 300');//300 seconds
        $buf = "ERROR (" . $this->getCode() . "): " . $this->getMessage() . "\n" . 'Error at ' . $this->getFile() . " (" . $this->getLine() . ")\n" . "Stack:\n" . $this->getExceptionTraceAsString() . "******end stack *******\n\n";

        $dt = Tools::dt();
        @file_put_contents(Tools::getLogPath() . 'exceptions.log', "\n" . $dt . ' - ' . @$_SERVER['REMOTE_ADDR'] . ' - ' . @$_SERVER['HTTP_HOST'] . @$_SERVER['REQUEST_URI'] . ' - ' . $buf, FILE_APPEND);

        if (CONSOLE)
        {
            echo "ERROR (" . $this->getCode() . "): " . $this->getMessage() . "\n" . 'Error at ' . $this->getFile() . " (" . $this->getLine() . ")\n****** Stack: *******\n" . $this->getExceptionTraceAsString() . "****** end stack *******\n\n";
        }
        elseif (CU::isLogged())
        {
            echo nl2br("<b>ERROR (" . $this->getCode() . "): " . $this->getMessage() . "</b>\n\n" . '<b>Error at</b> ' . $this->getFile() . " (" . $this->getLine() . ")\n\n <b>Stack:</b>\n" . $this->getExceptionTraceAsString() . "******end stack *******<br>\n\n");
        }
        else
        {
            if (!$noHalt)
            {
                // TODO сделать вывод для клиента в случае ошибки (с учетом noHalt)
                echo "Извините произошла ошибка при обработке запроса. Мы уже вкурсе об этом, скоро все заработает.";
            }
            //echo "<b>ERROR (".$this->getCode()."): ".$this->getMessage()."</b>\n\n";
        }
        if (!$noHalt) die();
    }

	function getExceptionTraceAsString() {
		$rtn = "";
		$count = 0;
		foreach ($this->getTrace() as $frame) {
			$args = "";
			if (isset($frame['args'])) {
				$args = array();
				foreach ($frame['args'] as $arg) {
					if (is_string($arg)) {
						$args[] = "'" . $arg . "'";
					} elseif (is_array($arg)) {
						$args[] = "Array";
					} elseif (is_null($arg)) {
						$args[] = 'NULL';
					} elseif (is_bool($arg)) {
						$args[] = ($arg) ? "true" : "false";
					} elseif (is_object($arg)) {
						$args[] = get_class($arg);
					} elseif (is_resource($arg)) {
						$args[] = get_resource_type($arg);
					} else {
						$args[] = $arg;
					}   
				}   
				$args = join(", ", $args);
			}
			$rtn .= sprintf( "#%s %s(%s): %s(%s)\n",
									 $count,
									 @$frame['file'],
									 @$frame['line'],
									 @$frame['function'],
									 $args );
			$count++;
		}
		return $rtn;
	}

}
class CoreException extends CommonException
{
}

