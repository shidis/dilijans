<?
include('../auth.php');
@define (true_enter,1);
require_once ($_SERVER['DOCUMENT_ROOT'].'/config/init.php');
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <title>CMS: <?=$title=strtoupper(str_replace('www.','',Cfg::get('site_url')))?> - Заказы</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">

    <?
    if(@$_GET['markup']=='top') {
        $framesModel=array(
            'def'=>array(
                'cols'=>"*",
                'rows'=>"105,*"
            ),
            'noSidebar'=>array(
                'cols'=>"*",
                'rows'=>"0,*"
            )
        );
    }
    elseif(@$_GET['markup']=='left') {
        $framesModel=array(
            'def'=>array(
                'cols'=>"260,*",
                'rows'=>"*"
            ),
            'noSidebar'=>array(
                'cols'=>"0,*",
                'rows'=>"*"
            )
        );
    }
    else {
        $framesModel=array(
            'def'=>array(
                'cols'=>"*,260",
                'rows'=>"*"
            ),
            'noSidebar'=>array(
                'cols'=>"*,0",
                'rows'=>"*"
            )
        );
    }
    ?>

    <script>
        var framesModel=<?=json_encode($framesModel)?>;
        var framesView='def';
    </script>

</head>

<? if(@$_GET['markup']=='top'){?>
    <frameset cols="<?=$framesModel['def']['cols']?>" rows="<?=$framesModel['def']['rows']?>">
        <frame name="sidebar" src="orders_top.php?markup=<?=$_GET['markup']?>" scrolling="no" frameborder="no">
        <frame name="center" src="orders_c.php?markup=<?=$_GET['markup']?>" frameborder="no">
    </frameset>
<? }
elseif(@$_GET['markup']=='left'){?>
    <frameset cols="<?=$framesModel['def']['cols']?>" rows="<?=$framesModel['def']['rows']?>">
        <frame name="sidebar" src="orders_sidebar.php?markup=<?=$_GET['markup']?>" frameborder="no">
        <frame name="center" src="orders_c.php?markup=<?=$_GET['markup']?>" frameborder="no">
    </frameset>
<? }else{?>
    <frameset cols="<?=$framesModel['def']['cols']?>" rows="<?=$framesModel['def']['rows']?>" name="ordersMainFrame">
        <frame name="center" src="orders_c.php?markup=right" frameborder="no">
        <frame name="sidebar" src="orders_sidebar.php?markup=right" frameborder="no">
    </frameset>
<? }?>

</html>
