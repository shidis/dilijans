<? 


require_once '../auth.php';
include('../struct.php');

$cp->frm['name']='pages';
$cp->frm['title']='Контекст';
$cp->checkPermissions();

cp_head();
cp_css();
cp_js();
?>

<?='
<style type="text/css">
#pages_edit{display:none}
</style>
'?>

<? cp_body()?>
<? cp_title()?>

<div id="pagered" style="text-align:center;"></div>

<div id="pages_edit" class="edit_area">
<fieldset class="ui" style="padding:0"><legend class="ui">Добавление / изменение страницы</legend>
<form method="post" id="page_edit_form" action="../be/pages.php?act=save">
<div style="padding:15px">
<button class="sbmt">Записать изменения</button>
<button class="f_cancel">Отменить</button>
<table width="100%" border="0" cellspacing="5" cellpadding="0" style="margin-top:10px">
  <tr>
    <td nowrap="nowrap">Адрес страницы (url)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
    <td width="100%"><input style="width:70%" type="text" name="ed_url" /> Параметры <input style="width:10%" type="text" name="ed_param" /> Строго <input type="checkbox" name="ed_strict" value="1" /></td>
  </tr>
  <tr>
    <td nowrap="nowrap">Title <input type="button" value="прочитать" id="get_title" /></td>
    <td><input style="width:100%" type="text" name="ed_title" /></td>
  </tr>
  <tr>
    <td nowrap="nowrap">Заголовок блока </td>
    <td><input style="width:100%" type="text" name="ed_header" /></td>
  </tr>  <tr>
    <td>Тип блока</td>
    <td><select name="ed_block_id"></select>&nbsp;&nbsp;&nbsp;&nbsp;Порядок <input type="text" style="width:30px; text-align:center" name="ed_pos" /></td>
  </tr>
</table>

<input type="hidden" name="ed_page_id" value="0" />
<input type="hidden" name="ed_text0" value="" />
<br />
<textarea name="ed_text" id="ed_text" style="height: 500px;"></textarea><br /><br />
<p>Keywords</p>
<textarea name="ed_keywords" style="width:100%; height:70px;"></textarea>
<p>Description</p>
<textarea name="ed_description" style="width:100%; height:70px;"></textarea>
<br /><br />
<button class="sbmt">Записать изменения</button>
<button class="f_cancel">Отменить</button>
</div>
</form>
</fieldset>
</div>

<div id="pages_list">
<p><input type="button" id="new_page" value="Добавить новую страницу" /></p>
<table id="grid" class="scroll" cellpadding="0" cellspacing="0"></table> 
</div>
<div style="margin:20px"> <a id="show_help" href="#" sty><em>Показать
    справку</em></a>
  <div id="help" style="<?='display:none; padding:20px; border:1px dashed green'?>">
    <p><img src="/cms/img/HelpIcon.png" width="50" height="50" hspace="15" align="left" />Внимание! В этом модуле можно менять содержимое блоков ТОЛЬКО на уже существующих страницах. Привязка осуществляется по урлу страницы.</p>
    <p>При
      активации
      параметра
      &quot;Строго&quot;
      текстовый
      блок
      будет
      показываться
      только
      на одной
      странице,
      url которой
      вы указали. </p>
    <p>Иначе
      текстовый
      блок
      будет
      отображаться
      на всех
      страницах адрес
      которых <strong>начинается</strong> с
      введенного
      вами
      значения
      url и
      имеющих
      параметры,
      указанные
      в соответствующем
      поле. Например,
      вы задали
      урл http://site.ru/catalague/ В
      этом
      случае
      текстовый
      блок
      отобразиться
      на страницах
      (если
      таковые
      имеются):</p>
    <p>http://site.ru/catalague/<br />
      http://site.ru/catalague/shini.html<br />
      http://site.ru/catalague/diski.html<br />
      http://site.ru/catalague/shini.html?page=2<br />
      и т.д. </p>
    <p>Параметры
      задаются
      в стандартном
      формате
      адреса
      страницы,
      например:
      page=2&amp;num=100.
      Т.е в
      формате
      [параметр=значение],
      разделенные
      знаком
      амперсанда
      (&amp;).
      Например,
      вы задали
      урл http://site.ru/catalague/
      и параметры
      num=5&amp;page=1.
      В этом
      случае
      текстовый
      блок
      отобразиться
      на страницах
      (если
      таковые
      имеются):</p>
    <p>http://site.ru/catalague/?num=5&amp;page=1<br />
      http://site.ru/catalague/shini.html?num=5&amp;page=1<br />
      http://site.ru/catalague/diski.html?num=5&amp;page=1<br />
      http://site.ru/catalague/shini.html?list=view&amp;num=5&amp;page=1<br />
      и т.д.</p>
    <p>При
      включенной
      опции &quot;Строго&quot;
      опция &quot;Параметр&quot;
      игнорируется.</p>
    <p>Если
      на сайте
      включена
      опция
      замены
      заголовка (тег &lt;title&gt;&lt;/title&gt;),
      то заголовок
      сайта
      будет
      заменяться
      значением,
      введенным
      в поле
      &quot;Title&quot;,
      если
      оно не
      пустое.
      Больший
      приоритет
      имеет
      заголовок
      из верхнего
      блока
      (при
      наличии
      нескольких
      блоков разного типа 
      на страницу). </p>
    <p>Поле &quot;Заголовок блока&quot; служит для замены/установки заголовка непосредственно блока. Для верхнего блока, как правило, это заголовок в тегах &lt;h1&gt;&lt;/h1&gt;. Для нижнего &lt;h2&gt;&lt;/h2&gt;. </p>
    <p>Параметр &quot;порядок&quot; служит для указания порядка вывода нескольких блоков одного типа на странице. Если заданы несколько блоков одного типа для страницы, то &quot;Заголовок блока&quot; или &quot;Title&quot; будет взят из первого для которого заголовок (Title) указан, остальные заголовки (тайтлы) будут проигнорированы.</p>
  </div>
</div>
<? cp_end()?>
