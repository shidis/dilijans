<?php
class MySitemapGenerator
{
    var
        $data=Array(),
        $returntype='json', //json or array
        $error,
        $debug=false;
    
    function __construct($apikey)
    {
        $this->data['api_key']=$apikey;
    }
    
    function __destruct()
    {
        if($this->debug)
        {
            if(!empty($this->error))
                echo($this->error);
            else
                echo 'Запрос выполнен успешно.';
        }
    }
    
    function call($method,$params=Null)
    {
        if(is_array($params) & sizeof($params))
            $this->data=array_merge($this->data,$params);
            
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, True);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, True);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $this->data);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_URL, str_replace('{{method}}',$method,'http://www.mysitemapgenerator.com/api/{{method}}?format=json'));
        
        $result=json_decode(curl_exec($ch));
        
        if(null===$result)
            $this->error='Invalid JSON';
            
        if(isset($result->result) & $result->result!=='success')
        {
            $this->error='Ошибка запроса';
            if(!isset($result->notice))
                $this->error=': '.$result->notice;
        }

                
        switch($this->returntype)
        {
            default:
            case 'json':
                return $result;
            break;
            case 'array':
                return (array)$result;
            break;
        }
    }
}
?>