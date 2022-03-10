<?

$tc=new TC();

$cities=$tc->fetchAll("SELECT tc_city.name,tc_city.sname FROM (tc_company INNER JOIN tc_city_rel ON tc_city_rel.company_id=tc_company.company_id) RIGHT JOIN tc_city ON tc_city.city_id=tc_city_rel.city_id WHERE NOT tc_company.disabled GROUP BY tc_city.name ORDER BY tc_city.name");

$curr_url = $_SERVER['REQUEST_URI'];

$alpha=array();
$js='var tkc=[];';
foreach($cities as $v){
	if(!isset($alpha[mb_substr($v['name'],0,1)][$v['name']]))  {
		$a=mb_substr($v['name'],0,1);
		if(!isset($alpha[$a])) $js.="tkc['{$a}']=[];";
		$js.="tkc['{$a}']['{$v['name']}']='{$v['sname']}';";
		$alpha[$a][$v['name']]=$v['sname'];
	}
}


if(count($alpha)){
	
    ?><div class="box-alphabet"><?
        ?><ul><?
        ?><li><a href="#" class="active">Все</a></li><?
        foreach($alpha as $k=>$v){
            ?><li><a href="#"><?=$k?></a></li><?
        }
        ?></ul><?
    ?></div><?
    $u='/'.App_Route::_getUrl('byCity').'/';
    ?><ul class="list-02" id="tkc0"><?
        foreach($cities as $v){
            $city_page = $u.$v['sname'].".html";
            ?><li>
            <?php
                if (strcmp($city_page, $curr_url) == 0)
                    echo $v['name'];
                else {
            ?>
                    <a href="<?=$city_page?>"><?=$v['name']?></a>
            <?php
                }
            ?>
            </li><?
        }
    ?></ul><?
    ?><ul class="list-02" id="tkc" style="display:none">1</ul><?
}

?>
<script>

<?=$js?>

$(document).ready(function(){
	
	$('.box-alphabet a').click(function(e){
		e.preventDefault();
		if(!$(this).hasClass('active')){
			$('.box-alphabet *').removeClass('active');
			$(this).addClass('active').parent().addClass('active');
			if($(this).text()=='Все'){
				$('#tkc0').show();
				$('#tkc').hide();
			} else {
				$('#tkc').show().html('');
				$('#tkc0').hide();
				var v=$(this).text();
				for(var k in tkc[v]) 
					$('#tkc').append('<li><a href="<?=$u?>'+tkc[v][k]+'.html">'+k+'</a></li>');
			}
		}
	});
	
});
</script>