    <table border="1">
                <? 
				$mmm=new Morphy();
				foreach($cities as $v){
					$r=$mmm->byPadej($v['name'],'ед','оп');
					
                    ?><tr><td>http://www.dilijans.org/<?=UPART_DCITY?>/<?=$v['sname']?>.html</td><td><?=$v['name']?></td><td><?=mb_substr($r,0,1).mb_strtolower(mb_substr($r,1))?></td></tr><?
                }?>
	</table>   
