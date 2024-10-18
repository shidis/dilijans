<? //
if (!defined('true_enter')) die ("No direct access allowed.");

abstract class App
{

    public $controllerInstance = NULL, $template, $error, $controller, // например tyres/catalog/index
        $controllerPath, // путь к файлу классу контроллера например tyres/catalog
        $controllerFName, // путь к файлу класса контроллера app/controllers/tyres/catalog/shini.php
        $controllerMethod, // метод контроллера
        $controllerClassName, // название класса контроллера
        $gzipEnabled = true, $clearCode = false, $namespace; // path[0]


    public function __construct()
    {
        //		parent::__construct();
        $this->namespace = Route::$ns;
        $this->setController(Route::$controller);
        if (!$this->initController())
        {
            echo $this->getError();
            die();
        }
        else
        {
            if (is_callable([
                $this->controllerInstance,
                'construct',
            ])) call_user_func([$this->controllerInstance, 'construct']);

            return true;
        }
    }

    public function execute($method = '')
    {
        $res = call_user_func([$this->controllerInstance, $method == '' ? $this->controllerMethod : $method]);

        // возвратить может путь к контроллеру для передачи управления
        while ($res != '')
        {
            if (!$this->initController($res))
            {
                echo $this->getError();
                die();
            }
            else
            {
                if (is_callable([
                    $this->controllerInstance,
                    'construct',
                ])) call_user_func([$this->controllerInstance, 'construct']);
                $res = call_user_func([$this->controllerInstance, $this->controllerMethod]);
            }
        }

        return true;
    }

    private function initController($controller = '')
    {
        // сохпяняем данные выполненного контроллера
        if ($this->controllerInstance != NULL)
        {
            $data = $this->controllerInstance->_data;
        }
        else $data = [];

        $this->controllerInstance = NULL;
        if ($controller == '') $controller = $this->getController();
        else $this->setController($controller);
        // $controller = home/index например

        $s = explode('/', $controller);

        // имя контроллера не может состоять только из имени класса, нужен метож вызова
        if (count($s) == 1 || $controller == '')
        {
            if (Request::$ajax)
            {
                $this->error['err_msg'] = '[App::initController().Ajax]: Ошибка в пути к контроллеру';
                $this->error['fres'] = false;
            }
            else $this->error = '[App::initController()]: Ошибка в пути к контроллеру';

            return false;
        }

        $this->controllerMethod = array_pop($s); // метод контроллера
        $cn = array_pop($s); // последняя часть названия класса
        $this->controllerPath = implode('/', $s); //все осталось в $s - путь к классу
        if (!empty($this->controllerPath)) $this->controllerPath = '/' . $this->controllerPath;

        // формируем путь к файлу класса контроллера
        $this->controllerFName = $this->namespace . '/controllers' . $this->controllerPath . '/' . $cn . '.php';

        foreach ($s as $k => &$v) if ($v != '') $v = ucfirst($v);
        array_push($s, ucfirst($cn), 'Controller');
        array_unshift($s, ucfirst($this->namespace));
        $this->controllerClassName = implode('_', $s);

        if (!is_file($this->controllerFName))
        {
            if (Request::$ajax)
            {
                $this->error['err_msg'] = '[App::initController().Ajax]: Файл контроллера не существует';
                $this->error['fres'] = false;
            }
            else $this->error = '[App::initController()]: Файла контроллера ' . "{$this->controllerFName}" . ' не существует';

            return false;
        }

        include_once $this->controllerFName;
        if (!class_exists($this->controllerClassName))
        {
            if (Request::$ajax)
            {
                $this->error['err_msg'] = '[App::initController().Ajax]: Название класса в файле контроллера некорректное';
                $this->error['fres'] = false;
            }
            else $this->error = '[App::initController()]: Название класса в файле контроллера некорректное';

            return false;
        }
        $this->controllerInstance = new $this->controllerClassName();

        // передаем данные из предыдущего контроллера если был такой
        $this->controllerInstance->_data = array_merge($this->controllerInstance->_data, $data);
        $this->controllerInstance->_data['currentController'] = $controller;
        unset($data);

        if (Request::$ajax)
        {
            if (!isset($this->controllerInstance->r)) $this->controllerInstance->r = [
                'err_msg'  => '',
                'fres_msg' => '',
                'fres'     => true,
            ];
        }

        return true;
    }


    public function setController($controllerPath)
    {
        // обрезаем слеши вначале и в конце  и удаляем повоторные слеши
        $controllerPath = trim($controllerPath);
        $s = explode('/', $controllerPath);
        $s1 = [];
        for ($i = 0; $i < count($s); $i++) if (trim($s[$i]) != '') $s1[] = $s[$i];
        $controllerPath = implode('/', $s1);
        $this->controller = $controllerPath;
    }

    public function getController()
    {
        return $this->controller;
    }

    public function obStart()
    {
        if ($this->gzipEnabled) ob_start("ob_gzhandler");
        elseif ($this->clearCode) ob_start();
    }

    public function obEnd()
    {
        if (Request::$ajax)
        {
            if ($this->gzipEnabled) ob_end_flush();
        }
        elseif ($this->clearCode)
        {
            $buf = ob_get_contents();
            ob_get_clean();
            echo preg_replace("~[\t\n\r]~", '', Tools::cutDoubleSpaces($buf));
        }
        elseif ($this->gzipEnabled) ob_end_flush();
    }

    public function getError()
    {
        if (is_array($this->error))
        {
            return json_encode($this->error);
        }
        else throw new AppException ($this->error);
    }

    /*
     * view должен быть в том же неймспейс, что и контроллер
     */
    public function output()
    {
        if (Request::$ajax && Request::$ajaxMethod == 'json')
        {

            $this->obStart();

            echo json_encode($this->controllerInstance->r);
            $this->obEnd();

        }
        else
        {
            if (@$this->controllerInstance->_template != '') $this->template($this->controllerInstance->_template);

            // если не задан шаблон, то ничего не подключаем view
            if ($this->template == '') return;

            if (is_file($this->namespace . '/view/' . $this->template() . '.php'))
            {
                extract((array)$this->controllerInstance, EXTR_OVERWRITE);
                extract($this->controllerInstance->_data, EXTR_OVERWRITE);
                $this->obStart();
                include $this->namespace . '/view/' . $this->template() . '.php';
                $this->obEnd();
            }
            else
                throw new AppException ('[App::output()]: ' . $this->namespace . '/view/' . $this->template() . ' open fault.');
        }
    }

    public function incView($file, $errOutput = false, $extractData = [])
    {  // $file без расширения .php
        extract((array)$this->controllerInstance, EXTR_OVERWRITE);
        extract($this->controllerInstance->_data, EXTR_OVERWRITE);

        // полезно при использовании incView внутри incView или template
        if (!empty($extractData)) extract((array)$extractData, EXTR_OVERWRITE);

        if (is_file($this->namespace . '/view/' . $file . '.php')) include $this->namespace . '/view/' . $file . '.php';
        elseif ($errOutput) throw new AppException ('[App::output()]: ' . $this->namespace . '/view/' . $file . ' not found.');
    }

    /*
     * установка нового/получение текущего шаблона
     */
    public function template($template = '')
    {
        if ($template == '') return $this->template;
        else $this->template = $template;
    }


}

class AppException extends CommonException
{

}
