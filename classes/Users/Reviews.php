<?
if (!defined('true_enter')) die ("Direct access not allowed!");

class Users_Reviews extends DB
{

    public $cfg;

    public function isEnabled($gr)
    {
        $this->cfg = Cfg::get('reviews');

        if (@$this->cfg[$gr]['enabled']) {
            $this->cfg = $this->cfg[$gr];
            return true;
        }
        return false;
    }

    /*
     * prodId!=0 - проверяет есть ли у пользователя отыв для заданного товара (по метке в сессии) и возвращает запись из БД. Для админов вернет 0
     * ИЛИ
     * reviewId!=0 - проверяет и возвращает отзыв по ИД
     * подрузамевается что поссетитель оставляет только один отзыв для товара
     * если не передан state то он будет установлен по конфигам сайта
     *
     * возвращает {false|0|qrow с отзывом}
     */
    public function getReview($r = array(), $gr = 1)
    {
        CU::isLogged();
        Session::check();
        $prodId = (int)@$r['prodId'];
        $reviewId = (int)@$r['reviewId'];
        if (!empty(CU::$userId) && !empty($prodId)) return 0;
        if (empty($prodId) && empty($reviewId)) $this->putMsg(false, "[Reviews.getReview]: prodId или reviewId не задан");

        if (!empty($prodId)) {
            $dd = $this->fetchAll("SELECT * FROM reviews WHERE prodId='$prodId'", MYSQL_ASSOC);
            foreach ($dd as $v)
                if (isset($_SESSION['rvwsPosted'][$v['id']])) {
                    $d = $v;
                    break;
                }
            unset($dd);
        } else {
            $d = $this->getOne("SELECT * FROM reviews WHERE id='$reviewId'", MYSQL_ASSOC);
        }

        $cUsers = CU::usersList(array('includeLD' => 1));

        if (!empty($d))
            if ($prodId && isset($_SESSION['rvwsPosted'][$d['id']]) || $reviewId) {
                if (!$this->isEnabled($d['gr'])) return $this->putMsg(false, "[Reviews.getReview]: Группа отзывов ({$d['gr']}) не инициализирована");
                $af = App_TFields::get('reviews', 'all', $d['gr']);
                $d['dateAdd'] = Tools::sdate($d['dt_add']);
                $d['email'] = Tools::unesc($d['email']);
                $d['userName'] = Tools::unesc($d['userName']);
                $d['comment'] = Tools::unesc($d['comment']);
                $d['advants'] = Tools::unesc($d['advants']);
                $d['defects'] = Tools::unesc($d['defects']);
                if (!empty($d['postedByAdmin'])) {
                    $d['postedBy_fullName'] = @$cUsers[$d['postedByAdmin']]['fullName'];
                    $d['postedBy_shortName'] = @$cUsers[$d['postedByAdmin']]['shortName'];
                }
                if (!empty($d['cUserId'])) {
                    $d['cUser_fullName'] = @$cUsers[$d['cUserId']]['fullName'];
                    $d['cUser_shortName'] = @$cUsers[$d['cUserId']]['shortName'];
                    $d['dt_state'] = Tools::sDateTime($d['dt_state']);
                }
                foreach ($af as $fv) {
                    if (!empty($fv['serializeVal'])) $d[$fv['as']] = Tools::DB_unserialize($d[$fv['as']]);
                    elseif (!empty($fv['implodeVals'])) {
                        $vals = Tools::unesc($d[$fv['as']]);
                        $d[$fv['as']] = array();
                        $vals = trim($vals, '_');
                        if (!empty($vals)) {
                            $vals = explode('_', $vals);
                            foreach ($vals as $vv) {
                                $d[$fv['as']][] = Tools::unesc($vv);
                            }
                            $d[$fv['as']] = array_unique($d[$fv['as']]);
                        }
                    } elseif (mb_stripos($fv['dbType'], 'varchar') !== false) $d[$fv['as']] = Tools::unesc($d[$fv['as']]);
                    elseif (@$fv['toFloat']) $d[$fv['as']] = 1 * ($d[$fv['as']]);
                }
                $vals = $d['vals'];
                $d['vals'] = array();
                if (!empty($vals)) {
                    $vals = trim($vals, '_');
                    $vals = explode('_', $vals);
                    foreach ($vals as $vv) {
                        $vv = explode(':', $vv);
                        if (isset($this->cfg['ratingItems'][$vv[0]]) && @$vv[1] <= $this->cfg['ratingScale']) $d['vals'][$vv[0]] = @$vv[1];
                    }
                    ksort($d['vals'], SORT_NUMERIC);
                }

                // редактировать могут или обычные юзеры с меткой в сессии или админы, содавшие отзыв, но только если статус не был изменен или отмодерирован
                if (empty(CU::$userId) && @$_SESSION['rvwsPosted'][$d['id']] == $d['state'] && empty($d['cUserId'])) $d['editable'] = true;
                elseif (!empty(CU::$userId) && $d['postedByAdmin'] == CU::$userId && ($d['state'] == $this->cfg['roles'][CU::$roleId] && (empty($d['cUserId']) || $d['cUserId']==CU::$userId))) $d['editable'] = true;

                return $d;
            }

        if (!$this->isEnabled($gr)) return $this->putMsg(false, "[Reviews.getReview]: Группа отзывов ({$gr}) не инициализирована");
        return 0;
    }

    /*
     * добавление отзыва
     * * @gr  char    req  def=1
     * * @prodId    int req
     * dt_state и cUserId не устанавливается
     * для посетителей сайта создается переменная в сессии rvwsPosted[<reviewId>]=state
     * exclusiveVisitor  {1|0}   посетитель может добалвять только один олтзыв. Срабатывает вместе с prodId   def=Cfg.onlyOneByProd
     */
    public function add($r = array())
    {
        if (empty($r['gr'])) $r['gr'] = 1;
        if (!$this->isEnabled($r['gr'])) return $this->putMsg(false, "[Reviews.add]: Группа отзывов ({$r['gr']}) не инициализирована");
        CU::isLogged();
        Session::check();
        $q = array();
        if (empty(CU::$userId)) {
            if ($this->cfg['defaultState'] === false)
                return $this->putMsg(false, "[Reviews.add]: Запрещено добавление новых отзывов для группы товаров ({$r['gr']})");
            if (isset($r['state']))
                $q['state'] = (int)$r['state'];
            else
                $q['state'] = $this->cfg['defaultState'];
        } else {
            $q['postedByAdmin'] = CU::$userId;
            if (!isset($r['state']))
                if (isset($this->cfg['roles'][CU::$roleId])) {
                    $q['state'] = $this->cfg['roles'][CU::$roleId];
                } else
                    return $this->putMsg(false, "[Reviews.add]: Вам запрещено добавление новых отзывов");
            else
                $q['state'] = (int)$r['state'];
        }

        $q['prodId'] = (int)@$r['prodId'];
        if(empty($q['prodId']))
            return $this->putMsg(false, "[Reviews.add]: ID продукта не задан");

        if (!isset($r['exclusiveVisitor'])) $r['exclusiveVisitor'] = $this->cfg['onlyOneByProd'];
        if (empty(CU::$userId) && !empty($r['exclusiveVisitor']) && !empty($r['prodId']) && is_array(@$_SESSION['rvwsPosted'])) {
            $d = $this->fetchAll("SELECT id FROM reviews WHERE prodId='{$r['prodId']}'", MYSQL_ASSOC);
            foreach ($d as $v)
                if (isset($_SESSION['rvwsPosted'][$v['id']]))
                    return $this->putMsg(false, "[Reviews.add]: Вы уже добавили отзыв для этого товара");
        }

        $q['gr'] = Tools::esc($r['gr']);
        $q['dt_add'] = Tools::dt();
        $q['userIP'] = array("INET_ATON('" . @$_SERVER['REMOTE_ADDR'] . "')", 'noquot');
        if (isset($r['email'])) $q['email'] = Tools::esc($r['email']);
        if (isset($r['userName'])) $q['userName'] = $this->userText($r['userName']);
        $vi = 0;
        if (is_array(@$r['vals'])) {
            /*
             * оценки должны передаваться как массив пар ('id'=>'оценка')
             */
            $vals = array();
            $ii = array_keys($this->cfg['ratingItems']);
            $rating = 0;
            foreach ($r['vals'] as $k => $v) {
                $v = (int)$v;
                if (in_array($k, $ii) && !empty($v)) {
                    $vi++;
                    $vals[] = (int)$k . ':' . $v;
                    $rating += $v;
                }
            }
            if (!empty($vals)) $q['vals'] = "_" . implode('_', $vals) . '_';
        }
        if (empty($vals) && isset($r['rating'])) {
            $q['rating'] = (int)$r['rating'];
            $q['vals'] = '';
        } elseif (!empty($vals) && $vi) {
            $q['rating'] = ($rating / count($vals));
            $q['rating'] = ceil($q['rating'] * 10) / 10;
        }

        if (isset($r['comment'])) $q['comment'] = $this->userText($r['comment']);
        if (isset($r['advants'])) $q['advants'] = $this->userText($r['advants']);
        if (isset($r['defects'])) $q['defects'] = $this->userText($r['defects']);

        $af = App_TFields::get('reviews', 'all', $r['gr']);

        foreach ($af as $v) {
            if (isset($r[$v['as']])) {
                if (mb_stripos($v['dbType'], 'decimal') !== false || mb_stripos($v['dbType'], 'float') !== false) $q[$v['as']] = Tools::toFloat($r[$v['as']]);
                elseif (mb_stripos($v['dbType'], 'int') !== false) $q[$v['as']] = (int)$r[$v['as']];
                elseif (!empty($v['serialize'])) $q[$v['as']] = Tools::DB_serialize($r[$v['as']]);
                elseif (!empty($v['implodeVals'])) {
                    if (is_array($r[$v['as']])) {
                        $q[$v['as']] = '_' . implode('_', array_unique($r[$v['as']])) . '_';
                    } else {
                        $r[$v['as']] = str_replace('_', '', $r[$v['as']]);
                        $q[$v['as']] = '_' . $r[$v['as']] . '_';
                    }
                    $q[$v['as']] = Tools::esc($q[$v['as']]);

                } else $q[$v['as']] = $this->userText($r[$v['as']]);
            }
        }

        $res = $this->insert('reviews', $q);
        $reviewId=$this->lastId();
        if ($res) {
            if(false===App_CC_Rating::recalcModel($q['prodId'])) $this->putMsg(false, App_CC_Rating::strMsg());
            if (empty(CU::$userId)) {
                Session::start();
                $_SESSION['rvwsPosted'][$reviewId] = $q['state'];
            }
            return $reviewId;
        }

        return false;
    }

    /*
     * проверяет на валидность текстовые значения (check==true) или фильтрует и возвращает безопасный текст
     */
    public function userText($s, $check = false)
    {
        if (!$check) {
            return Tools::html($s, false);
        }
        return true;
    }


    /*
     * редактирование отзыва
     * dt_state, cUserId не обновляются
     * редактировать могут или обычные юзеры с меткой в сессии или админы, содавшие отзыв, но только если статус не был изменен или отмодерирован
     * если переда state то он будет установлен иначе state у отзыва будет перезаписан с учетом настроек конфига сайта
     *
     * @gr  char    req  def=1
     * @reviewId    int req
     */
    public function mod($r = array())
    {
        $reviewId = (int)@$r['reviewId'];
        if (empty($reviewId)) return $this->putMsg(false, "[Reviews.mod]: reviewId не задан");
        $d = $this->getOne("SELECT * FROM reviews WHERE id='$reviewId'", MYSQL_ASSOC);
        if ($d === 0) return $this->putMsg(false, "[Reviews.mod]: Отзыв не найден id=$reviewId");
        if (!$this->isEnabled($d['gr'])) return $this->putMsg(false, "[Reviews.mod]: Группа отзывов ({$d['gr']}) не инициализирована");
        CU::isLogged();
        Session::check();

        if (empty(CU::$userId) && (!isset($_SESSION['rvwsPosted'][$reviewId]) || @$_SESSION['rvwsPosted'][$reviewId] != $d['state'] || !empty($d['cUserId'])))
            return $this->putMsg(false, "[Reviews.mod]: Вам запрещено редактировать этот отзыв");

        elseif (!empty(CU::$userId) && ($d['postedByAdmin'] != CU::$userId || $d['state'] != $this->cfg['roles'][CU::$roleId] || !empty($v['cUserId']) && $v['cUserId']!=CU::$userId))
            return $this->putMsg(false, "[Reviews.mod]: Вам запрещено редактировать этот отзыв");

        $q = array();

        if (isset($r['state'])) $q['state'] = (int)$r['state'];
        if (isset($r['prodId'])) $q['prodId'] = (int)$r['prodId'];
        if (isset($r['email'])) $q['email'] = Tools::esc($r['email']);
        if (isset($r['userName'])) $q['userName'] = $this->userText($r['userName']);
        $vi = 0;
        if (is_array(@$r['vals'])) {
            /*
             * оценки должны передаваться как массив пар ('id'=>'оценка')
             */
            $vals = array();
            $ii = array_keys($this->cfg['ratingItems']);
            $rating = 0;
            foreach ($r['vals'] as $k => $v) {
                $v = (int)$v;
                if (in_array($k, $ii) && !empty($v)) {
                    $vi++;
                    $vals[] = (int)$k . ':' . $v;
                    $rating += $v;
                }
            }
            if (!empty($vals)) $q['vals'] = "_" . implode('_', $vals) . '_';
        }

        if (empty($vals) && isset($r['rating'])) {
            $q['rating'] = (int)$r['rating'];
            $q['vals'] = 'aa';
        } elseif (!empty($vals) && $vi) {
            $q['rating'] = ($rating / count($vals));
            $q['rating'] = ceil($q['rating'] * 10) / 10;
        }

        if (isset($r['comment'])) $q['comment'] = $this->userText($r['comment']);
        if (isset($r['advants'])) $q['advants'] = $this->userText($r['advants']);
        if (isset($r['defects'])) $q['defects'] = $this->userText($r['defects']);

        if (empty(CU::$userId)) {
            if (isset($r['state']))
                $q['state'] = (int)$r['state'];
            else
                $q['state'] = $this->cfg['defaultState'];
        } else {
            if (!isset($r['state']))
                if (isset($this->cfg['roles'][CU::$roleId])) {
                    $q['state'] = $this->cfg['roles'][CU::$roleId];
                } else
                    $q['state'] = (int)$r['state'];
        }

        $af = App_TFields::get('reviews', 'all', $d['gr']);

        foreach ($af as $v) {
            if (isset($r[$v['as']])) {
                if (mb_stripos($v['dbType'], 'decimal') !== false || mb_stripos($v['dbType'], 'float') !== false) $q[$v['as']] = Tools::toFloat($r[$v['as']]);
                elseif (mb_stripos($v['dbType'], 'int') !== false) $q[$v['as']] = (int)$r[$v['as']];
                elseif (!empty($v['serialize'])) $q[$v['as']] = Tools::DB_serialize($r[$v['as']]);
                elseif (!empty($v['implodeVals'])) {
                    if (is_array($r[$v['as']])) {
                        $q[$v['as']] = '_' . implode('_', array_unique($r[$v['as']])) . '_';
                    } else {
                        $r[$v['as']] = str_replace('_', '', $r[$v['as']]);
                        $q[$v['as']] = '_' . $r[$v['as']] . '_';
                    }
                    $q[$v['as']] = Tools::esc($q[$v['as']]);

                } else $q[$v['as']] = $this->userText($r[$v['as']]);
            }
        }

        if ($this->update('reviews', $q, "id='$reviewId'")) {
            if(false===App_CC_Rating::recalcModel($q['prodId'])) $this->putMsg(false, App_CC_Rating::strMsg());
            if (empty(CU::$userId) && isset($q['state'])) {
                Session::start();
                $_SESSION['rvwsPosted'][$reviewId] = $q['state'];
            }
            return true;
        }
        return false;
    }


    /*
     * удаление отзыва
     * удалять могут или пользователи с меткой в сессии или админы, создавшие его, но только если статус не был изменен или отмодерирован
     */
    public function delReview($reviewId)
    {
        $reviewId = (int)$reviewId;
        if (empty($reviewId)) return $this->putMsg(false, "[Reviews.del]: reviewId не задан");
        $d = $this->getOne("SELECT * FROM reviews WHERE id='$reviewId'", MYSQL_ASSOC);
        if ($d === 0) return $this->putMsg(false, "[Reviews.del]: Отзыв не найден id=$reviewId");
        if (!$this->isEnabled($d['gr'])) return $this->putMsg(false, "[Reviews.del]: Группа отзывов ({$d['gr']}) не инициализирована");
        CU::isLogged();
        Session::check();

        if (empty(CU::$userId) && (!isset($_SESSION['rvwsPosted'][$reviewId]) || @$_SESSION['rvwsPosted'][$reviewId] != $d['state'] || !empty($d['cUserId'])))
            return $this->putMsg(false, "[Reviews.del]: Вам запрещено удалять этот отзыв");

        elseif (!empty(CU::$userId) && ($d['postedByAdmin'] != CU::$userId || $d['state'] != $this->cfg['roles'][CU::$roleId] || !empty($d['cUserId']) && $d['cUserId']!=CU::$userId))
            return $this->putMsg(false, "[Reviews.del]: Вам запрещено удалять этот отзыв");

        $this->query("DELETE FROM reviews WHERE id='$reviewId'");
        if(false===App_CC_Rating::recalcModel($d['prodId'])) $this->putMsg(false, App_CC_Rating::strMsg());
        unset($_SESSION['rvwsPosted'][$reviewId]);
        return true;
    }

    /*
     * метод меняет статус отзыва (модерация)
     * признак отмодерированности отзыва: cUserId>0 && state==1
     * модераторы задаются  CP::isAllow()
     * проверка на ужеОтмодерированность не производится
     */
    public function moderate($reviewId, $state)
    {
        $reviewId = (int)$reviewId;
        if (empty($reviewId)) return $this->putMsg(false, "[Reviews.moderate]: reviewId не задан");
        $d = $this->getOne("SELECT * FROM reviews WHERE id='$reviewId'", MYSQL_ASSOC);
        if ($d === 0) return $this->putMsg(false, "[Reviews.moderate]: Отзыв не найден id=$reviewId");
        if (!$this->isEnabled($d['gr'])) return $this->putMsg(false, "[Reviews.moderate]: Группа отзывов ({$d['gr']}) не инициализирована");
        CU::isLogged();
        $cp = new CP();
        if (!$cp->isAllow('reviews.moderate')) return $this->putMsg(false, "[Reviews.moderate]: нет прав на модерацию отзывов");
        unset($cp);
        $q = array();
        $q['dt_state'] = Tools::dt();
        $q['cUserId'] = CU::$userId;
        $q['state'] = $state;
        $this->update('reviews', $q, "id='$reviewId'");
        if(false===App_CC_Rating::recalcModel($d['prodId'])) $this->putMsg(false, App_CC_Rating::strMsg());
        return true;
    }

    /*
     * проголосовать за отзыв
     * информация о голосе хранится в MC rvws.[reviewId].[ip2long]=true
     * @vote    {-1,1}
     */
    public function vote($reviewId, $vote)
    {
        $reviewId = (int)$reviewId;
        if (!$this->canVote($reviewId)) return false;

        if ($vote == 1)
            $f = 'votesPlus';
        elseif ($vote == -1)
            $f = 'votesMinus';
        else
            return $this->putMsg(false, "[Reviews.vote]: Параметр vote=$vote не допустим");

        $long = ip2long($_SERVER['REMOTE_ADDR']);
        $this->query("UPDATE reviews SET $f=`$f`+1 WHERE id='$reviewId'");
        if ($this->updatedNum()) {
            $this->query("UPDATE reviews SET votes=(votesPlus-votesMinus) WHERE id='$reviewId'");
            MC::set('rvwVote.' . (int)$reviewId . '.' . $long . MC::uid(), true, Cfg::$config['reviews']['MCVoteLifeTime']);
        }
        return true;
    }


    /*
     * Возможность юзером голосовать за отзыв
     * информация о голосе хранится в MC rvws.[reviewId].[ip2long]=true
     * создавшие отзыв посетители не могут голосовать за свой отзыв
     */
    public function canVote($reviewId)
    {
        if(!$this->cfg['voting']) return false;
        if (!MC::chk()) return $this->putMsg(false, "[Reviews.canVote]: MC не инициализирована");
        $long = ip2long($_SERVER['REMOTE_ADDR']);
        if ($long == -1 || $long === FALSE) return $this->putMsg(false, "[Reviews.canVote]: IP не сконвертирован");
        if (isset($_SESSION['rvwsPosted'][$reviewId])) return $this->putMsg(false, "[Reviews.canVote]: Нельзя голосовать за свой отзыв");
        if (MC::get('rvwVote.' . (int)$reviewId . '.' . $long . MC::uid()) === false) return true;
        return $this->putMsg(false, "[Reviews.canVote]: Уже есть голос");
    }

    /*
     * входные параметры:
     * states   {array,int}       0- новый/не отмодерирован (1)- одобрен (-1) - отменен (2) - на модерацию от наемного публикатора (3) - только оценка без отзыва
     * listStates   {array,int}     Какие состояния включать в выходной массив. Этот массив является подмножеством states
     * includeOwnPosts  {0|1}  В независимости от state будет подмешаны отзывы посетителя с меткой в сессии или модера создавшего отзыв. Сработает вместе с prodId>0. Записи с state=3 не попадают в выходной массив
     * prodId  int
     * brand_id     {array,int}
     * forceCatJoin {0|1}  заджойнить таблицу с брендами и моделями
     * gr  char    req  def=1
     * sort    string      def "dt_add DESC"
     * start
     * limit
     * dtAddStart
     * dtAddEnd
     * cUserId  {array,int,условие}
     * postedByAdmin    {array,int,условие}
     *
     * @return  {array,false}
     * array=(
     *      data,   array   массив с отзывами   c listStates
     *      total,  int     всего отзывов с states
     *      gtotal  int     всего отзывов с states без limitby
     *      avgRating   float   средняя оценка с states
     * )
     */
    public function olist($r = array())
    {
        if (!isset($r['states'])) $states = array(); elseif (!is_array(@$r['states'])) $states = array($r['states']);
        else $states = $r['states'];
        if (!isset($r['listStates'])) $listStates = array(); elseif (!is_array($r['listStates'])) $listStates = array($r['listStates']);
        else $listStates = $r['listStates'];

        Session::check();
        CU::isLogged();

        $w = array();
        if (isset($r['gr'])) $w[] = "reviews.gr='" . Tools::esc($r['gr']) . "'";
        else {
            $r['gr'] = '1';
            $w[] = "reviews.gr='1'";
        }

        if (!$this->isEnabled($r['gr'])) return $this->putMsg(false, "[Reviews.olist]: Группа отзывов ({$r['gr']}) не инициализирована");

        if(!empty($r['brand_id']))
            if(is_array($r['brand_id'])){
                $a=array();
                foreach($r['brand_id'] as $v) if($v!=0) $a[]=(int)$v;
                $w[]="cc_brand.brand_id IN(".implode(',',$a).")";
            }else $w[]="cc_brand.brand_id = '{$r['brand_id']}'";

        if(isset($r['cUserId']))
            if(is_array($r['cUserId'])){
                $a=array();
                foreach($r['cUserId'] as $v) if($v!=0) $a[]=(int)$v;
                $w[]="reviews.cUserId IN(".implode(',',$a).")";
            }elseif(Tools::typeOf($r['cUserId'])=='string') $w[]="reviews.cUserId {$r['cUserId']}";
            elseif(Tools::typeOf($r['cUserId'])=='integer') $w[]="reviews.cUserId='".(int)$r['cUserId']."'";

        if(isset($r['postedByAdmin']))
            if(is_array($r['postedByAdmin'])){
                $a=array();
                foreach($r['postedByAdmin'] as $v) if($v!=0) $a[]=(int)$v;
                $w[]="reviews.postedByAdmin IN(".implode(',',$a).")";
            }elseif(Tools::typeOf($r['postedByAdmin'])=='string') $w[]="reviews.postedByAdmin {$r['postedByAdmin']}";
            elseif(Tools::typeOf($r['postedByAdmin'])=='integer') $w[]="reviews.postedByAdmin='".(int)$r['postedByAdmin']."'";

        if (!empty($r['prodId'])) $w[] = "reviews.prodId=" . (int)$r['prodId'];
        if (!empty($r['includeOwnPosts']) && (is_array(@$_SESSION['rvwsPosted']) && !empty($_SESSION['rvwsPosted']) || !empty(CU::$userId))) {
            if (!empty($states)) {
                if (empty(CU::$userId))
                    $w[] = "(reviews.state IN (" . implode(',', $states) . ') OR id IN (' . implode(',', array_keys($_SESSION['rvwsPosted'])) . '))';
                else
                    $w[] = "(reviews.state IN (" . implode(',', $states) . ") OR postedByAdmin='" . CU::$userId . "')";
            } else {
                if (empty(CU::$userId))
                    $w[] = "id IN (" . implode(',', array_keys($_SESSION['rvwsPosted'])) . ')';
                else
                    $w[] = "postedByAdmin='" . CU::$userId . "'";
            }
        } else {
            if (!empty($states)) $w[] = "reviews.state IN (" . implode(',', $states) . ')';
        }

        if(!empty($r['dtAddStart'])) $w[]="reviews.dt_add >= '".Tools::esc($r['dtAddStart'])."'";
        if(!empty($r['dtAddEnd'])) {
            if(preg_match("~^[0-9]{4}-[0-9]{2}-[0-9]{2}$~u", $r['dtAddEnd'])) $r['dtAddEnd'].=' 23:59:59';
            $w[]="reviews.dt_add <= '".Tools::esc($r['dtAddEnd'])."'";
        }

        $_w = implode(' AND ', $w);

        if (!empty($r['sort'])) $sort = $r['sort']; else $sort = "reviews.dt_add DESC";

        $af = App_TFields::DBselect('reviews', $r['gr']);
        if($this->cfg['voting']) $af.=', reviews.votesPlus, reviews.votesMinus, reviews.votes';

        $cUsers = CU::usersList(array('includeLD' => 1));

        if(isset($r['start'])) {
            $limitBy=" LIMIT ".abs(intval($r['start']));
            if(!empty($r['limit'])) $limitBy.=", ".abs(intval($r['limit']));
        }else $limitBy='';

        if (empty($r['prodId']) || !empty($r['brand_id']) || !empty($r['forceCatJoin'])){
            if(!empty($limitBy)){
                $d=$this->getOne("SELECT count(*) FROM reviews JOIN cc_model ON reviews.prodId=cc_model.model_id JOIN cc_brand USING (brand_id) WHERE $_w");
                $gtotal=$d[0];
            }

            $d = $this->fetchAll($s="SELECT INET_NTOA(reviews.userIP) AS userIP, reviews.id, reviews.prodId, reviews.state, reviews.cUserId, reviews.dt_state, reviews.dt_add, reviews.postedByAdmin, reviews.userId, reviews.email, reviews.userName, reviews.rating, reviews.vals, reviews.comment, reviews.advants, reviews.defects, cc_model.name AS mname, cc_brand.name AS bname, cc_model.model_id, cc_brand.brand_id {$af} FROM reviews JOIN cc_model ON reviews.prodId=cc_model.model_id JOIN cc_brand USING (brand_id) WHERE $_w ORDER BY $sort $limitBy", MYSQL_ASSOC);
        }else{
            if(!empty($limitBy)){
                $d=$this->getOne("SELECT count(*) FROM reviews WHERE $_w ORDER BY $sort");
                $gtotal=$d[0];
            }
            $d = $this->fetchAll($s="SELECT INET_NTOA(reviews.userIP) AS userIP, reviews.id, reviews.prodId, reviews.state, reviews.cUserId, reviews.dt_state, reviews.dt_add, reviews.postedByAdmin, reviews.userId, reviews.email, reviews.userName, reviews.rating, reviews.vals, reviews.comment, reviews.advants, reviews.defects {$af} FROM reviews WHERE $_w ORDER BY $sort $limitBy", MYSQL_ASSOC);
        }


        $af = App_TFields::get('reviews', 'all', $r['gr']);
        $res = array();
        $total = count($d);
        if(!isset($gtotal)) $gtotal=$total;
        $avgRating = 0;

        foreach ($d as $v) {
            if (in_array($v['state'], $listStates) || empty($listStates) || $v['state']!=3 && !empty($r['includeOwnPosts']) && isset($_SESSION['rvwsPosted'][$v['id']])) {
                $res[$v['id']] = $v;
                if (empty($r['prodId'])) {
                    $res[$v['id']]['mname'] = Tools::unesc($v['mname']);
                    $res[$v['id']]['bname'] = Tools::unesc($v['bname']);
                }
                if (!empty($v['postedByAdmin'])) {
                    $res[$v['id']]['postedBy_fullName'] = @$cUsers[$v['postedByAdmin']]['fullName'];
                    $res[$v['id']]['postedBy_shortName'] = @$cUsers[$v['postedByAdmin']]['shortName'];
                }
                if (!empty($v['cUserId'])) {
                    $res[$v['id']]['cUser_fullName'] = @$cUsers[$v['cUserId']]['fullName'];
                    $res[$v['id']]['cUser_shortName'] = @$cUsers[$v['cUserId']]['shortName'];
                    $res[$v['id']]['dt_state'] = Tools::sDateTime($v['dt_state']);
                }
                $res[$v['id']]['dateAdd'] = Tools::sdate($v['dt_add']);
                $res[$v['id']]['email'] = Tools::unesc($v['email']);
                $res[$v['id']]['userName'] = Tools::unesc($v['userName']);
                $res[$v['id']]['comment'] = Tools::unesc($v['comment']);
                $res[$v['id']]['advants'] = Tools::unesc($v['advants']);
                $res[$v['id']]['defects'] = Tools::unesc($v['defects']);
                foreach ($af as $fv) {
                    if (!empty($fv['serialize'])) $res[$v['id']][$fv['as']] = Tools::DB_unserialize($v[$fv['as']]);
                    elseif (!empty($fv['implodeVals'])) {
                        $vals = Tools::unesc($v[$fv['as']]);
                        $res[$v['id']][$fv['as']] = array();
                        $vals = trim($vals, '_');
                        if (!empty($vals)) {
                            $vals = explode('_', $vals);
                            foreach ($vals as $vv) {
                                $res[$v['id']][$fv['as']][] = Tools::unesc($vv);
                            }
                            $res[$v['id']][$fv['as']] = array_unique($res[$v['id']][$fv['as']]);
                        }
                    } elseif (mb_stripos($fv['dbType'], 'varchar') !== false) $res[$v['id']][$fv['as']] = Tools::unesc($v[$fv['as']]);
                    elseif (@$fv['toFloat']) $res[$v['id']][$fv['as']] = 1 * $res[$v['id']][$fv['as']];
                }
                $res[$v['id']]['rating'] = $v['rating'];
                $res[$v['id']]['vals'] = array();
                $vals = $v['vals'];
                if (!empty($vals)) {
                    $vals = trim($vals, '_');
                    $vals = explode('_', $vals);
                    foreach ($vals as $vv) {
                        $vv = explode(':', $vv);
                        if (isset($this->cfg['ratingItems'][$vv[0]]) && @$vv[1] <= $this->cfg['ratingScale']) $res[$v['id']]['vals'][$vv[0]] = @$vv[1];
                    }
                    ksort($res[$v['id']]['vals'], SORT_NUMERIC);
                }
                // редактировать могут или обычные юзеры с меткой в сессии или админы, содавшие отзыв, но только если статус не был изменен или отмодерирован
                if (
                    empty(CU::$userId)
                    &&
                    @$_SESSION['rvwsPosted'][$v['id']] == $v['state']
                    &&
                    empty($v['cUserId'])
                ) $res[$v['id']]['editable'] = true;
                elseif (
                    !empty(CU::$userId)
                    &&
                    $v['postedByAdmin'] == CU::$userId
                    &&
                    (
                        $v['state'] == @$this->cfg['roles'][CU::$roleId]
                        && (empty($v['cUserId']) || $v['cUserId']==CU::$userId)
                    )
                ) $res[$v['id']]['editable'] = true;
            }
            $avgRating += $v['rating'];
        }
        if ($total) {
            $avgRating = ($avgRating / $total);
            $avgRating = ceil($avgRating * 10) / 10;
        }

        return array('data' => $res, 'total' => $total, 'avgRating' => $avgRating, 'gtotal'=>@$gtotal);
    }

    public function canModerate()
    {
        CU::isLogged();
        $cp = new CP();
        $canModerate = $cp->isAllow('reviews.moderate');
        unset($cp);
        return $canModerate;
    }

    public function allowToEdit($reviewId)
    {
        $reviewId = (int)$reviewId;
        if (empty($reviewId)) return $this->putMsg(false, "[Reviews.allowToEdit]: reviewId не задан");
        $d = $this->getOne("SELECT * FROM reviews WHERE id='$reviewId'", MYSQL_ASSOC);
        if ($d === 0) return $this->putMsg(false, "[Reviews.allowToEdit]: Отзыв не найден id=$reviewId");
        if (!$this->isEnabled($d['gr'])) return $this->putMsg(false, "[Reviews.allowToEdit]: Группа отзывов ({$d['gr']}) не инициализирована");

        CU::isLogged();
        Session::check();

        if (empty(CU::$userId) && (!isset($_SESSION['rvwsPosted'][$reviewId]) || @$_SESSION['rvwsPosted'][$reviewId] != $d['state'] || !empty($d['cUserId'])))
            return $this->putMsg(false, "[Reviews.allowToEdit]: Вам запрещено редактировать этот отзыв");

        elseif (!empty(CU::$userId) && ($d['postedByAdmin'] != CU::$userId || $d['state'] != $this->cfg['roles'][CU::$roleId] || !empty($v['cUserId']) && $v['cUserId']!=CU::$userId))
            return $this->putMsg(false, "[Reviews.allowToEdit]: Вам запрещено редактировать этот отзыв");

        return true;
    }

    public function allowToAdd($gr = 1, $prodId=0)
    {
        if (!$this->isEnabled($gr)) return $this->putMsg(false, "[Reviews.allowToAdd]: Группа отзывов ({$gr}) не инициализирована");
        CU::isLogged();

        if (empty(CU::$userId)) {
            if ($this->cfg['defaultState'] === false)
                return $this->putMsg(false, "[Reviews.allowToAdd]: Запрещено добавление новых отзывов");

            if(!empty($prodId) && !empty($this->cfg['onlyOneByProd']) && !empty($_SESSION['rvwsPosted'])){
                $prodId=(int)$prodId;
                $d=$this->fetchAll("SELECT id FROM reviews WHERE prodId=$prodId", MYSQL_NUM);
                if(empty($d)) return true;
                foreach($d as $v){
                    if(isset($_SESSION['rvwsPosted'][$v[0]])) return $this->putMsg(false, "[Reviews.allowToAdd]: Для этого товара уже был вами добавлен отзыв");
                }
            }

        } else {
            if (!isset($this->cfg['roles'][CU::$roleId]))
                return $this->putMsg(false, "[Reviews.allowToAdd]: Вам запрещено добавление новых отзывов");
        }

        return true;
    }



}