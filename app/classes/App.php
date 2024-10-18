<?
class App_App extends App {

    function __construct(){
        if(server_loc=='remote' && false) $this->clearCode=true;
        parent::__construct();
    }

    public function makeId($v)
    {
        return preg_replace("~[^a-z0-9_-]~iu",'_',str_replace('*','x',$v));
    }

}

