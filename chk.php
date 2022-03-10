<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 09.12.2015
 * Time: 22:23
 */


@define(true_enter, 1);

define('ROOT_PATH', realpath(dirname(__FILE__)));
require_once(ROOT_PATH . '/config/init.php');

$db = new DB();

$t = $db->fetchAll("show tables", MYSQL_NUM);
$chk = true;
foreach ($t as $v)
{
    $v = $v[0];
    $r=$db->getOne("CHECK TABLE $v", MYSQL_ASSOC);
    if ($r['Msg_text'] !== 'OK')
    {
        $chk = false;
        echo "$v !!!{$r['Msg_text']}!!!\n";
    }
}

if ($chk) echo "*good*";
else echo "*bad*";