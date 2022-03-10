<h2 class="padd">FAQ</h2>
<div class="attention">
    <img src="/app/images/attention.png" alt="" class="fl-l">
    <p>Здесь вы можете найти ответы на часто задоваемые вопросы. </p>
</div>

<? if(!empty($lenta)){?>

<div class="faq">
    <ul>

        <? foreach($lenta as $v){?>

        <li>
            <a href="#"><?=$v['title']?></a>
            <div>

                <? if(!empty($v['img1'])){?>

                <img src="<?=$v['img1']?>" alt="" class="fl-l">

                <? }?>

               <div class="ctext"> <?=$v['text']?></div>

            </div>
        </li>

        <? }?>

    </ul>
</div>

<? }