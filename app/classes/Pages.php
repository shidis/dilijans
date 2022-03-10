<?
if (!defined('true_enter')) die ("Direct access not allowed!");

class App_Pages extends Pages
{
    use TextParser;

    var $tpl = "<div class=\"promo-text clearfix ctext #class#\">#body#</div>";
    var $tplClass = array(1 => 'tbp_top', 2 => 'tbp_bot');

    function __construct()
    {
        parent::__construct();
    }


    function block($block_id)
    {
        $r = $this->load($block_id);
        if ($this->blockChainMode) {
            if (is_array($r)) {
                $text = '';
                foreach ($r as $part) {
                    $s = $this->splitText($part['text']);
                    if (count($s) == 1) $text .= "<div class=\"{$this->chainBlockClass}\">{$part['text']}</div>"; else
                        $text .= "<div class=\"{$this->chainBlockClass}\"><div class=\"fl-l col-5 clearfix\">{$s[0]}</div><div class=\"fl-r col-5 clearfix\">{$s[1]}</div></div>";
                }
                if ($this->tpl == '') $s = $text; else {
                    $s = str_replace('#class#', $this->tplClass[$block_id], $this->tpl);
                    $s = str_replace('#body#', $text, $s);
                }
                return html_entity_decode($s);
            }
        } else {
            if (is_array($r) && Tools::stripTags($r['text']) != '') {

                $s = $this->splitText($r['text']);
                if (count($s) == 1) $text = "<div class=\"{$this->chainBlockClass}\">{$r['text']}</div>"; else
                    $text = "<div class=\"{$this->chainBlockClass}\"><div class=\"fl-l col-5 clearfix\">{$s[0]}</div><div class=\"fl-r col-5 clearfix\">{$s[1]}</div></div>";

                if ($this->tpl == '') $s = $text; else {
                    $s = str_replace('#class#', $this->tplClass[$block_id], $this->tpl);
                    $s = str_replace('#body#', $text, $s);
                }
                return html_entity_decode($s);
            }
        }
        return '';
    }


}