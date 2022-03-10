<?
include_once('ajx_loader.php');

$cp->setFN('accessories');
$cp->checkPermissions();

$r->fres = true;
$r->fres_msg = '';

$act = $_REQUEST['act'];
$gr = @$_REQUEST['gr'];

$db = new DB();
$cc = new CC_Ctrl();
$ac = new Accessories();

function getBrands($groupID, $getAccessoryCount = true)
{
    global $cc;

    $cc->que('brands', $groupID, '', '', 'brand_id');
    $arBrands = $cc->fetchAll();

    if ($getAccessoryCount) {
        $cc->query("SELECT * FROM cc_accessories_bind WHERE gr='$groupID'");
        $arBrandsAccessories = $cc->fetchAll();

        foreach ($arBrands as $id => $brand) {
            foreach ($arBrandsAccessories as $key => $accessory) {
                if ($brand['brand_id'] == $accessory['brand_id']) {
                    $arBrands[$id]['accessoriesCount'] = count(unserialize($accessory['accessories']));
                    unset($arBrandsAccessories[$key]);
                }
            }
        }
    }

    return $arBrands;
}

function saveAccessories($brandID, $groupID, $accessories)
{
    global $cc;

    if ($brandID && $groupID) {
        $resAccessories = '';

        if (!empty($accessories)) {
            foreach ($accessories as $id => $data) {
                $resAccessories[$id]['price'] = (float)$data['price'];
            }
            $accessories = serialize($resAccessories);


            $accessoryObj = $cc->fetchAll("SELECT id FROM cc_accessories_bind WHERE brand_id='$brandID' AND gr='$groupID'");

            if (!empty($accessoryObj)) {
                if (!$cc->query("UPDATE cc_accessories_bind SET accessories='$accessories' WHERE brand_id='$brandID'")) {
                    return false;
                }
            } else {
                if (!$cc->query("INSERT INTO cc_accessories_bind (brand_id,gr,accessories) VALUES ('$brandID','$groupID','$accessories')")) {
                    return false;
                }
            }
        } else {
            if (!$cc->query("DELETE FROM cc_accessories_bind WHERE brand_id='$brandID'")) {
                return false;
            }
        }

        return true;
    }

    return false;
}

switch ($act) {
    case 'getBrandsByGroup':
        $group = (int)$_REQUEST['group'];

        if (empty($group)) {
            $r->fres = false;
            $r->fres_msg = 'Нет ID группы';
        }

        $arBrands = getBrands($group);
        $arResult = [];

        foreach ($arBrands as $brand) {
            $arResult[] = [
                'id' => $brand['brand_id'],
                'name' => $brand['name'],
                'count' => $brand['accessoriesCount'] ? $brand['accessoriesCount'] : "Нет"
            ];
        }

        $r->data = $arResult;

        break;
    case 'getAccessoriesByGroup':
        $group = (int)$_REQUEST['group'];

        if (empty($group)) {
            $r->fres = false;
            $r->fres_msg = 'Нет ID группы';
        }

        $arAccessories = $ac->getAccessories($group);

        $arResult = [];

        foreach ($arAccessories as $accessory) {
            $arResult[] = [
                'id' => $accessory['acc_id'],
                'name' => $accessory['name'],
                'price' => $accessory['aprice'],
            ];
        }

        $r->data = $arResult;

        break;
    case 'getBrandAccessories':
        $brandsID = $_REQUEST['brandsID'];
        $group = (int)$_REQUEST['group'];

        if (empty($group)) {
            $r->fres = false;
            $r->fres_msg = 'Нет ID группы';
        }

        if (empty($brandID)) {
            $r->fres = false;
            $r->fres_msg = 'Нет ID бренда';
        }


        foreach ($brandsID as $brandID) {
            $arBrandsAccessories[] = $ac->getBrandAccessories($brandID, $group);
        }

        $arResult = [];

        if (!empty($arBrandsAccessories)) {
            $arAccessories = $arBrandsAccessories[0];

            for ($i = 1; $i < count($arBrandsAccessories); $i++) {
                $arAccessories = array_intersect_key($arAccessories, $arBrandsAccessories[$i]);
            }

            foreach ($arAccessories as $accessory) {
                $arResult[] = [
                    'id' => $accessory['acc_id'],
                    'name' => $accessory['name'],
                    'price' => $arAccessories[$accessory['acc_id']]['price'],
                ];
            }
        } else {
            $r->fres = false;
        }

        $r->data = $arResult;

        break;
    case 'saveAccessories':
        $brandsID = $_REQUEST['brandsID'];

        if (empty($_POST['formData'])) {
            $r->fres = false;
            $r->fres_msg = 'Нет ID группы';
        }

        if (empty($brandID)) {
            $r->fres = false;
            $r->fres_msg = 'Нет ID бренда';
        }

        parse_str($_POST['formData'], $formData);

        foreach ($brandsID as $brandID) {
            $saveResult = saveAccessories($brandID, $formData['group'], $formData['accessories']);
        }

        $r->data = $saveResult ? 'Сохранено' : 'Ошибка сохранения';

        break;
    default:
        $r->fres = false;
        $r->fres_msg = 'BAD ACT_CASE ' . $act;
}

ajxEnd();