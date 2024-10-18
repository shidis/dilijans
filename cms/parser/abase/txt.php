<? include('../../auth.php')?>
<? @define (true_enter,1);

require_once ($_SERVER['DOCUMENT_ROOT'].'/config/init.php');



if(!isset($_GET['restore'])) backup(); else restore();

if(isset($_GET['notexts'])) echo "только альты, без текстов<br>";
if(isset($_GET['saveold'])) echo "режим обновления<br>"; else echo "режим стереть/записать<br>";

/*
 * солхранение данных ПЕРЕД заменой базы подбора новой
 * и восстановление прежних текстовых данных ПОСЛЕ установки новой базы подбора
 */


function backup()
{
    $db=new DB();
    $dbtxt=new DB();

    $db->query("TRUNCATE ab_avto_txt");

    $db->query("SELECT alt,text1,text2,avto_id FROM ab_avto WHERE text1!='' OR text2!='' OR alt!=''");
    $i=0;
    while($db->next(MYSQLI_ASSOC)!==false){
        $qr=$db->qrow;
        $dbtxt->insert('ab_avto_txt',$qr);
        $i++;
    }
    echo "SAVED $i DONE.";
}

function restore()
{
    $db=new DB();
    $dbtxt=new DB();

    if(isset($_GET['saveold'])) {
        if (isset($_GET['notexts'])) $db->query("UPDATE ab_avto SET alt=''"); else
            $db->query("UPDATE ab_avto SET text1='', text2='', alt=''");
    }

    $dbtxt->query("SELECT * FROM ab_avto_txt");
    $i=0;
    while($dbtxt->next(MYSQLI_ASSOC)!==false){
        $qr=$dbtxt->qrow;
        if(isset($_GET['notexts']))
            $db->update('ab_avto',array(
                'alt'=>$qr['alt']
            ), "avto_id={$qr['avto_id']}");
        else
            $db->update('ab_avto',array(
                'text1'=>$qr['text1'],
                'text2'=>$qr['text2'],
                'alt'=>$qr['alt']
            ), "avto_id={$qr['avto_id']}");

        $u=$db->updatedNum();
        if($u) $i+=$u; else echo "avto_id={$qr['avto_id']} - не обновлен<br>";
    }
    echo "<br><br>UPDATED $i DONE.";

}

/*

CREATE TABLE IF NOT EXISTS ab_avto_txt (
  id int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  avto_id int(10) UNSIGNED NOT NULL,
  text1 text NOT NULL,
  text2 text NOT NULL,
  alt varchar(255) NOT NULL,
  PRIMARY KEY (id)
)
ENGINE = MYISAM
AUTO_INCREMENT = 1
CHARACTER SET utf8
COLLATE utf8_general_ci
ROW_FORMAT = fixed;

 */
