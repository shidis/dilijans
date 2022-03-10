<?
if (!defined('true_enter')) die ("Direct access not allowed!");

class DB extends Common
{

    var $sql_query; // sql-запрос
    var $qres; // указатель на sql-запрос
    var $qrow; //текущая запись
    var $qdata; // весь запрос в массиве
    var $qnum; // количество записей в запросе
    var $sql_host, $sql_user, $sql_pass, $sql_db;
    var $last_id; //mysql_insert_id()
    var $charset = 'utf8';

    function __construct()
    {
        //	parent::__construct();
        $this->sql_host = Cfg::_get('sql_host');
        $this->sql_user = Cfg::_get('sql_user');
        $this->sql_pass = Cfg::_get('sql_pass');
        $this->sql_db = Cfg::_get('sql_db');
    }

    function set_db($sql_host, $sql_db, $sql_user, $sql_pass)
    {
        $this->sql_host = $sql_host;
        $this->sql_db = $sql_db;
        $this->sql_user = $sql_user;
        $this->sql_pass = $sql_pass;
    }
    
    function getConnId()
    {
        return @Stat::$dbConnIds[$this->sql_host.$this->sql_user.$this->sql_db];
    }


    function sql_connect()
    {
        $i = 0;
        do {
            $i++;

            $conn = Stat::$dbConnIds[$this->sql_host.$this->sql_user.$this->sql_db] = @mysql_connect($this->sql_host, $this->sql_user, $this->sql_pass, true);

            if ($conn === false) usleep(500000);

        } while ($conn === false && $i < 15);

        if ($conn === false)
            throw new DBException("Error in " . __FILE__ . ", line " . __LINE__ . " :: mysql_connect($i) Error: " . mysql_error(), mysql_errno());

        if (false === ($res = @mysql_select_db($this->sql_db, $conn))) {
            throw new DBException("Error in " . __FILE__ . ", line " . __LINE__ . " :: mysql_select_db() Error: " . mysql_error($conn), mysql_errno($conn));
        }

        $this->qres = mysql_query("SET NAMES '{$this->charset}'", $conn);
        //$this->qres=mysql_query("SET CHARACTER SET utf8", $this->getConnId());
        //$this->qres=mysql_query("SET CHARACTER_SET_CONNECTION=utf8", $this->getConnId());
        //$this->qres=mysql_query("SET SQL_MODE = ''", $this->getConnId());

        return $res;
    }

    function sql_close()
    {
        $res=@mysql_close($this->getConnId());
        unset(Stat::$dbConnIds[$this->sql_host.$this->sql_user.$this->sql_db]);
        return $res;
    }

    function sql_execute($noHalt=false)
    {

        $conn = $this->getConnId();
        if (!is_resource($conn)) {
            $this->sql_connect();
            Stat::$dbConnNum++;
        }

        if ($this->qres === false) {
            if($noHalt) return false;
            throw new DBException("Error in " . __FILE__ . ", line " . __LINE__ . " with errno = " . mysql_errno($this->getConnId()) . ': ' . mysql_error($this->getConnId()));
        }

        $this->qres = @mysql_query($this->sql_query, $this->getConnId());


        if ($this->qres === false) {
            if($noHalt) return false;
            throw new DBException("Error in " . __FILE__ . ", line " . __LINE__ . " with errno = " . mysql_errno($this->getConnId()) . ': ' . mysql_error($this->getConnId()) . ".\n\nQueryFullString: /{$this->sql_query}/");
        }

        Stat::incDB();

        return $this->qres;

    }

    function query($query, $noHalt=false)
    {
        $this->sql_query = $query;
        return $this->sql_execute($noHalt);
    }

    function fetchAll($query = '', $type = MYSQL_BOTH, $noHalt=false)
    {
        $this->qdata = array();
        $res = array();
        if ($query != '' && ($res = $this->query($query, $noHalt)) || $query == '') {
            if ($query == '') while ($this->next($type) !== false) $this->qdata[] = $this->qrow;
            else while ($this->next($type) !== false) $this->qdata[] = $this->qrow;
        } else return $res;
        return $this->qdata;
    }

    function setCharset($ch)
    {
        $this->charset = $ch;
    }

    function updatedNum()
    {
        return mysql_affected_rows($this->getConnId());
    }

    function count($query = '', $noHalt=false)
    {
        if ($query != '')
            if ($res = $this->query($query, $noHalt)) {
                if (mb_stripos($query, 'count') !== false) {
                    $this->next(MYSQL_NUM);
                    return $this->qrow[0];
                } else return $this->qnum();
            } else return $res;
    }

    function getOne($query = '', $type = MYSQL_BOTH, $noHalt=false)
    {
        if ($query != '')
            if ($res = $this->query($query, $noHalt))
                if ($this->qnum()) {
                    $this->next($type);
                    return $this->qrow;
                } else return 0;
            else return $res;
        elseif ($this->qnum()) {
            $this->next($type);
            return $this->qrow;
        } else return 0;
    }

    function foundRows($noHalt=false)
    {
        if (!is_resource($this->qres)) return false;
        if ($res = $this->query("SELECT FOUND_ROWS ()", $noHalt))
            return mysql_result($this->qres, 0);
        else
            return $res;

    }

    function next($type = MYSQL_BOTH)
    { // MYSQL_ASSOC, MYSQL_NUM, MYSQL_BOTH
        if (!is_resource($this->qres)) return false;
        $res = $this->qrow = mysql_fetch_array($this->qres, $type);
        return ($res);
    }

    function first()
    {
        if (!is_resource($this->qres)) return false;
        $res = $this->qrow = mysql_data_seek($this->qres, 0);
        return ($res);
    }

    function qnum()
    {
        if (!is_resource($this->qres)) return false;
        return $this->qnum = mysql_num_rows($this->qres);
    }

    function unum()
    {
        return mysql_affected_rows($this->getConnId());
    }

    function lastId()
    {
        return $this->last_id=mysql_insert_id($this->getConnId());
    }

    function seek($num)
    {
        if (!is_resource($this->qres)) return false;
        return mysql_data_seek($this->qres, $num);
    }

    function sqlFree()
    {
        return mysql_free_result($this->qres);
    }

    function ld($table, $id, $value)
    {
        $c = new DB;
        $err = $c->query("UPDATE $table SET LD='1' WHERE $id='$value'");
        unset($c);
        return ($err);
    }

    function del($table, $id, $value, $noHalt=false)
    {
        $res = $this->query("DELETE FROM {$table} WHERE $id='{$value}'", $noHalt);
        if (!$res) return false;
        return mysql_affected_rows($this->getConnId());
    }

    function insert($table, $fieldsValues, $noHalt=false)
    {
        // fieldsValues=array(field=>value,.....)
        // если value is array то формат value=array(значение, параметр форматирования)
        // параметр форматирвоания может быть noquot
        // ToDo
        // Разобратся со вставкой пустого ''  значения и null в поле int
        // вставлет 0, а должен null
        //
        $val = $ar = array();
        foreach ($fieldsValues as $key => &$v) {
            $ar[] = $key;
            if (is_array($v)) {
                switch ($v[1]) {
                    case 'noquot':
                        $val[] = $v[0];
                        break;
                    default:
                        $val[] = "'$v'";
                }
            } else $val[] = "'$v'";
        }
        $sql = "INSERT INTO $table (";
        $sql .= join(', ', $ar);
        $sql .= ') VALUES (';
        $sql .= join(',', $val);
        $sql .= ')';
        return $this->query($sql, $noHalt);
    }

    function update($table, $fieldsValues, $where = '', $limit = '', $noHalt=false)
    { // fieldsValues=array(field=>value,.....)   where=  срока условия
        $ar = array();
        foreach ($fieldsValues as $key => &$v) {
            if (is_array($v)) {
                switch ($v[1]) {
                    case 'noquot':
                        $ar[] = "$key={$v[0]}";
                        break;
                }
            } else $ar[] = "$key='$v'";
        }
        $sql = "UPDATE $table SET " . join(', ', $ar) . ($where != '' ? " WHERE $where" : '') . ($limit != '' ? " LIMIT $limit" : '');
        return $this->query($sql, $noHalt);
    }

    function getUUID()
    {
        $d = $this->getOne("SELECT UUID();");
        return $d[0];
    }

    function sqlInfo()
    {
        return mysql_get_server_info();
    }

    function getColumns($tbl, $types=false, $noHalt=false)
    {
        $d=$this->fetchAll("SHOW COLUMNS FROM `$tbl`", MYSQL_ASSOC, $noHalt);
        $r=array();
        if($d!==false){
            foreach($d as $v){
                if($types)
                    $r[$v['Field']]=$v['Type'];
                else
                    $r[]=$v['Field'];
            }
        return $r;
        }
        return false;
    }


}

class DBException extends CommonException
{

}

