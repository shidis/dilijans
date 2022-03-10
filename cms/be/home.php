<?
include_once('ajx_loader.php');

//sleep(1);


$cp->setFN('home');
$cp->checkPermissions();

$r->fres = true;
$r->fres_msg = '';

$page = @$_REQUEST['page']; // get the requested page
$limit = @$_REQUEST['rows']; // get how many rows we want to have into the grid
$sidx = @$_REQUEST['sidx']; // get index row - i.e. user click to sort
$sord = @$_REQUEST['sord']; // get the direction
if (!$sidx) $sidx = 1;
$act = Tools::esc(@$_REQUEST['act']);
if (@$_REQUEST['oper'] == 'add') $act = 'add';
if (@$_REQUEST['oper'] == 'edit') $act = 'edit';
if (@$_REQUEST['oper'] == 'del') $act = 'del';
if ($act == '') $act = 'list';

$db = new DB();

switch ($act) {

    case 'botLog_init':
        $d = $db->fetchAll("SELECT se FROM bot_log GROUP BY se ORDER BY se");
        $r->se = array('' => 'все');
        foreach ($d as $v) {
            $r->se[$v[0]] = BotLog::$UA[$v[0]]['label'];
        }
        $d = $db->fetchAll("SELECT botName,se FROM bot_log GROUP BY botName ORDER BY se,botName");
        $r->botNames = array('' => 'все');
        foreach ($d as $v) {
            $r->botNames[$v['botName'] != '' ? $v['botName'] : 'blank'] = $v['botName'] != '' ? $v['botName'] : 'не известно';
        }
        $d = $db->getOne("SELECT count(*) FROM bot_log WHERE se=1 AND dt_visited>=CURDATE()");
        $r->todayYA = @$d[0];
        $d = $db->getOne("SELECT count(*) FROM bot_log WHERE se=1 AND dt_visited >= (CURDATE()-1) AND dt_visited < CURDATE()");
        $r->yestedayYA = @$d[0];
        $d = $db->getOne("SELECT count(*) FROM bot_log WHERE se=1 AND dt_visited >= DATE_SUB(CURRENT_DATE, INTERVAL 7 DAY)");
        $r->weekYA = @$d[0];
        $d = $db->getOne("SELECT count(*) FROM bot_log WHERE se=1 AND dt_visited >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)");
        $r->monthYA = @$d[0];
        $d = $db->getOne("SELECT SUM(hits) FROM bot_history WHERE se=1 AND `date` >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)");
        $r->monthYA += @$d[0];

        $d = $db->getOne("SELECT SUM(hits) FROM bot_history WHERE se=1");
        $d1 = $db->getOne("SELECT count(*) FROM bot_log WHERE se=1");
        $r->totalYA = @$d[0] + @$d1[0];

        $d = $db->getOne("SELECT count(*) FROM bot_log WHERE se=2 AND dt_visited>=CURDATE()");
        $r->todayG = @$d[0];
        $d = $db->getOne("SELECT count(*) FROM bot_log WHERE se=2 AND dt_visited >= (CURDATE()-1) AND dt_visited < CURDATE()");
        $r->yestedayG = @$d[0];
        $d = $db->getOne("SELECT count(*) FROM bot_log WHERE se=2 AND dt_visited >= DATE_SUB(CURRENT_DATE, INTERVAL 7 DAY)");
        $r->weekG = @$d[0];
        $d = $db->getOne("SELECT count(*) FROM bot_log WHERE se=2 AND dt_visited >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)");
        $r->monthG = @$d[0];
        $d = $db->getOne("SELECT SUM(hits) FROM bot_history WHERE se=2 AND `date` >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)");
        $r->monthG += @$d[0];

        $d = $db->getOne("SELECT SUM(hits) FROM bot_history WHERE se=2");
        $d1 = $db->getOne("SELECT count(*) FROM bot_log WHERE se=2");
        $r->totalG = @$d[0] + @$d1[0];
        break;


    case 'botLog_list':
        $sq = array();
        if (@$_REQUEST['_search'] == 'true') {
            $filters = json_decode(@$_GET['filters'], true);
            if (count($filters['rules'])) {
                foreach ($filters['rules'] as $v) {
                    if (in_array(@$v['field'], array('se', 'botName', 'dt_visited', 'userAgent', 'url', 'botIP'))) {
                        $data = Tools::like(trim($v['data']));
                        if ($data == -1) $data = '';
                        if ($data != '') {
                            switch ($v['field']) {
                                case 'botIP':
                                    $sq[] = " INET_NTOA({$v['field']}) LIKE '%{$data}%'";
                                    break;
                                case 'se':
                                case 'botName':
                                    if ($data == 'blank') $sq[] = " {$v['field']} = ''"; else $sq[] = " {$v['field']} = '{$data}'";
                                    break;
                                default:
                                    $sq[] = " {$v['field']} LIKE '%{$data}%'";

                            }
                        }
                    }
                }
            }
        }
        if (count($sq)) $where = ' WHERE ' . implode(' AND ', $sq); else $where = '';
        $r->where = $where;
        $d = $db->getOne("SELECT count(se) FROM bot_log JOIN bot_userAgent USING (userAgentId) $where");
        $r->records = $db->qrow[0];
        if ($r->records) $total_pages = ceil($r->records / $limit); else $total_pages = 0;
        if ($page > $total_pages) $page = $total_pages;
        $start = $limit * $page - $limit; // do not put $limit*($page - 1)
        if ($start < 0) $start = 0;
        $r->page = $page;
        $r->total = $total_pages;
        if (!$db->query("SELECT se,botName,dt_visited,userAgent,url, INET_NTOA(botIP) AS botIP FROM bot_log JOIN bot_userAgent USING (userAgentId) $where ORDER BY $sidx $sord LIMIT $start,$limit")) {
            $r->fres = false;
            break;
        }
        $i = $start + 1;
        $ii = 0;

        while ($db->next() !== false) {
            $r->rows[$ii]['id'] = $i;
            $r->rows[$ii]['cell'] = array(BotLog::$UA[$db->qrow['se']]['label'], $db->qrow['botName'], $db->qrow['dt_visited'], Tools::unesc($db->qrow['userAgent']), Tools::unesc($db->qrow['url']), $db->qrow['botIP']);
            $i++;
            $ii++;
        }
        break;


    case 'exLibDebugGet':
        if (ExLib::isDebug()) $r->debug = 1; else $r->debug = false;
        break;

    case 'exLibDebugSW':
        if (ExLib::isDebug()) {
            $r->fres = App_ExLib::debug(0);
            $r->debug = 0;
        } else {
            $r->fres = App_ExLib::debug(1);
            $r->debug = 1;
        }
        break;

    case 'exLibConcatJS':
        $r->fres = App_ExLib::concatJS();
        ExLib::vJS();
        break;

    case 'exLibConcatCSS':
        $r->fres = App_ExLib::concatCSS();
        ExLib::vCSS();
        break;

    case 'exLibConcatImages':
        $r->fres = ExLib::vImages();
        break;

    case 'SMSBalance':
        $sms=SMS_Reactor::factory();
        $r->data=$sms->balance();
        if($r->data===false){
            $r->fres=false;
            $r->fres_msg=$sms->strMsg();
        }
        break;

    case 'callLog_GraphData':

        $suc=$mis=$out=$dates=[];
        $r->data=[];

        // пропущенные
        $db->query("SELECT  DATE(dt) AS date1,  COUNT(*) AS calls FROM os_log_calls olc WHERE DATE(dt) >= DATE_SUB(DATE(NOW()), INTERVAL 2 YEAR) AND (olc.type = 1 AND olc.co = olc.dest OR olc.type = 11) GROUP BY date1 ORDER BY date1 ASC");


        //date_default_timezone_set('UTC');
        //echo date_default_timezone_get ();

        if($db->qnum()) {
            $db->next(MYSQL_NUM);
            $db->qrow[1]=(int)$db->qrow[1];
            $firstTS=$curd=explode('-',$first=$db->qrow[0]); // [Y,m,d]
            $mis[]=$db->qrow[1];
            $dates[]=$first;
            while (false !== $db->next()){
                $dd=$db->qrow[0];
                $db->qrow[1]=(int)$db->qrow[1];
                $curd[2]++;
                $curd=explode('-', $cdd=date("Y-m-d", mktime(0,0,0, date($curd[1]), date($curd[2]), date($curd[0]))));
                while($cdd!=$dd) {
                    $mis[]=0;
                    $dates[]=$cdd;
                    $curd[2]++;
                    $curd=explode('-', $cdd=date("Y-m-d", mktime(0,0,0, date($curd[1]), date($curd[2]), date($curd[0]))));
                }
                $mis[]=$db->qrow[1];
                $dates[]=$cdd;
            }

            // принятые
            $db->query("SELECT (DATE(dt)) AS date1, COUNT(*) AS calls FROM os_log_calls olc WHERE dt >= '$first 00:00:00' AND (olc.type=1 AND olc.co!=olc.dest) GROUP BY date1 ORDER BY date1 ASC");

            if($db->qnum()){
                $curd=explode('-',$first);
                while (false !== $db->next()){
                    $dd=$db->qrow[0];
                    $db->qrow[1]=(int)$db->qrow[1];
                    $curd=explode('-', $cdd=date("Y-m-d", mktime(0,0,0, date($curd[1]), date($curd[2]), date($curd[0]))));
                    while($cdd!=$dd) {
                        $suc[]=0;
                        $curd[2]++;
                        if(in_array($cdd,$dates)) $dates[]=$cdd;
                        $curd=explode('-', $cdd=date("Y-m-d", mktime(0,0,0, date($curd[1]), date($curd[2]), date($curd[0]))));
                    }
                    $suc[]=$db->qrow[1];
                    if(in_array($cdd,$dates)) $dates[]=$cdd;
                    $curd[2]++;
                }
            }

            // исходящие
            $db->query("SELECT (DATE(dt)) AS date1, COUNT(*) AS calls FROM os_log_calls olc WHERE dt >= '$first 00:00:00' AND olc.type=2 GROUP BY date1 ORDER BY date1 ASC");

            if($db->qnum()){
                $curd=explode('-',$first);
                while (false !== $db->next()){
                    $dd=$db->qrow[0];
                    $db->qrow[1]=(int)$db->qrow[1];
                    $curd=explode('-', $cdd=date("Y-m-d", mktime(0,0,0, date($curd[1]), date($curd[2]), date($curd[0]))));
                    while($cdd!=$dd) {
                        $out[]=0;
                        $curd[2]++;
                        if(in_array($cdd,$dates)) $dates[]=$cdd;
                        $curd=explode('-', $cdd=date("Y-m-d", mktime(0,0,0, date($curd[1]), date($curd[2]), date($curd[0]))));
                    }
                    $out[]=$db->qrow[1];
                    if(in_array($cdd,$dates)) $dates[]=$cdd;
                    $curd[2]++;
                }
            }

        }
        $r->firstDateTSM=mktime(0,0,0, date($firstTS[1]), date($firstTS[2]), date($firstTS[0])) * 1000;
        $r->data=[
            'dates'=>$dates,
            'successed'=>$suc,
            'missed'=>$mis,
            'out'=>$out
        ];

        break;

    case "djsMissesReset":
        $r->fres=false;
        if(MC::chk()) {
            $pid = @Cfg::$config['DJS']['pid'];
            if (!empty($pid)) {
                MC::del("DJS:$pid:js_misses");
                MC::del("DJS:$pid:js_reloadMisses");
                MC::del("DJS:$pid:js_afterReload");
                $r->fres=true;
            }
        }
        break;

    default:
        $r->fres = false;
        $r->fres_msg = 'BAD ACT ID ' . $act;
}

ajxEnd();