<? if(!empty($rvws)){?>

    <div class="reviews">

        <? //print_r($_SESSION)?>

    <? if(!empty($rvws['list'])){?>

        <div class="items">

        <? foreach($rvws['list'] as $ritem){ ?>

            <? $this->incView('catalog/tyres/reviews/item', false, $ritem);?>

        <? }?>

        </div>


        <div class="divider"></div>

        <? if(!empty($rvws['canAdd'])){?>

            <div class="add-new"><a href="#">Добавить свой отзыв</a> </div>

            <div class="form-add-c"></div>

        <? }

    } else{?>

        <div class="items"></div>


        <? if(!empty($rvws['canAdd'])){?>

            <div class="add-new"><a href="#">Добавить свой отзыв</a> </div>

            <div class="form-add-c"></div>

        <? }?>

    <? }?>


    </div>

<? }?>