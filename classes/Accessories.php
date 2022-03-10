<?php


class Accessories
{
    public $cc;
    private $boxTitle;
    private $markPointer = '-';
    private $classMain = '';

    public function __construct()
    {
        $this->cc = new CC_Ctrl();
    }

    public function getAccessories($groupID)
    {
        if ($groupID) {
            return $this->cc->fetchAll("SELECT acc_id, name, aprice FROM `cc_accessories` WHERE `gr` = '{$groupID}' AND NOT `LD` ORDER BY 'pos';");
        } else return false;
    }

    public function getBrandAccessories($brandID, $groupID)
    {
        if ($brandID && $groupID) {
            $arAccessories = $this->getAccessories($groupID);

            $this->cc->que('accessories_bind', $brandID, $groupID);
            $this->cc->next();

            $arBrandAccessoriesInfo = @unserialize($this->cc->qrow['accessories']);
            $arBrandAccessory = [];

            if (is_array($arBrandAccessoriesInfo)) {
                foreach ($arBrandAccessoriesInfo as $id => $brandAccessoryInfo) {
                    foreach ($arAccessories as $key => $accessory) {
                        if ($accessory['acc_id'] == $id) {
                            $arBrandAccessory[$id] = $accessory;
                            $arBrandAccessory[$id]['price'] = $brandAccessoryInfo['price'];
                        }
                    }
                }
            }

            return $arBrandAccessory;
        } else return false;
    }

    public function getBrandIDByCatID($catID)
    {
        // Забираем ID бренда
        $resObj = $this->cc->fetchAll("SELECT cc_cat.model_id, cc_model.brand_id AS brandID  FROM cc_cat LEFT JOIN cc_model ON cc_cat.model_id = cc_model.model_id WHERE cat_id='$catID'");
        return $resObj[0]['brandID'];
    }

    public function setTitle($title)
    {
        $this->boxTitle = $title;
    }

    /**
     * Устанавливает символ перед названием. Доступные варианты: <br/>
     * <b>dash</b> - символ тире <br/>
     * <b>plus</b> - символ плюс
     * @param string $markPointer
     */
    public function setMarkPointer($markPointer = 'dash')
    {
        switch ($markPointer) {
            case 'dash':
                $this->markPointer = '-';
                break;
            case 'plus':
                $this->markPointer = '+';
                break;
            default:
                $this->markPointer = '-';
        }
    }

    /**
     * Устанавливает класс <b>accessories-box-main</b> блоку списка допов <br/>
     * для индификации блока выбора допов при добавлении товара в карзину
     */
    public function setClassMain()
    {
        $this->classMain = 'accessories-box-main';
    }

    public function getAccessoriesCheckboxes($brandID = '', $groupID = '', $catID)
    {
        if (!$brandID) {
            $brandID = $this->getBrandIDByCatID($catID);
        }

        $arAccessories = $this->getBrandAccessories($brandID, $groupID);

        if (empty($arAccessories)) {
            return '';
        }

        $output = '<div class="accessories-box '. $this->classMain .'" data-cat_id="' . $catID . '"">';

        if ($this->boxTitle) {
            $output .= '<h4>' . $this->boxTitle . '</h4>';
        }

        $output .= '<div class="accessories-box__items-container">';

        foreach ($arAccessories as $i => $accessory) {
            $checked = '';

            if ($catID && !empty($_SESSION['dop_list'][$catID])) {
                $selectedAccessories = $_SESSION['dop_list'][$catID];

                foreach ($selectedAccessories as $key => $selectedAccessory) {
                    if ($accessory['acc_id'] == $selectedAccessory['acc_id']) {
                        $checked = 'checked';
                        unset($selectedAccessories[$key]);
                    }
                }
            }
            $id = substr(md5(rand(1,100000) + time()), 0,5);
            $output .= <<< EOT
            <div class="accessories-box__item">
                <div class="accessories-box__item-field">
                    <input title="" type="checkbox" {$checked} class="accessories-box__item-checkbox" value="{$accessory['price']}" id="accessories-box__item_{$id}" data-id="{$accessory['acc_id']}" data-name="{$accessory['name']}">
                </div>
                <label for="accessories-box__item_{$id}">
                <div class="accessories-box__item-field">{$this->markPointer} {$accessory['name']} -</div>
                <div class="accessories-box__item-field" style="color: red">{$accessory['price']} р.</div>
                </label>
            </div>
EOT;
        }
        $output .= '</div></div>';

        return $output;
    }

}