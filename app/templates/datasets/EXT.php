<? $ym=new App_CC_Dataset_EXT();?>
<fieldset class="ui">
<table cellpadding="10">
  <tr>
    <td colspan="4"><strong>Для брендов выгружать информацию:</strong></td>
  </tr>
  <tr>
    <td width="25%"><label><?=$ym->dataFields['b_imgs']['info']?></label><select name="b_imgs"><option value="0">нет</option><option value="1">да</option></select></td>
    <td width="25%"><label><?=$ym->dataFields['b_text']['info']?></label><select name="b_imgs"><option value="0">нет</option><option value="1">да</option></select></td>
    <td width="25%"><label><?=$ym->dataFields['b_alt']['info']?></label><select name="b_imgs"><option value="0">нет</option><option value="1">да</option></select></td>
    <td width="25%"></td>
  </tr>
  <tr>
    <td colspan="4"><strong>Для моделей выгружать информацию:</strong></td>
  </tr>
  <tr>
    <td width="25%"><label><?=$ym->dataFields['m_imgs']['info']?></label><select name="b_imgs"><option value="0">нет</option><option value="1">да</option></select></td>
    <td width="25%"><label><?=$ym->dataFields['m_text']['info']?></label><select name="b_imgs"><option value="0">нет</option><option value="1">да</option></select></td>
    <td width="25%"><label><?=$ym->dataFields['m_alt']['info']?></label><select name="b_imgs"><option value="0">нет</option><option value="1">да</option></select></td>
    <td width="25%"></td>
  </tr>
  <tr>
    <td colspan="4"><strong>Для типоразмеров выгружать информацию:</strong></td>
  </tr>
  <tr>
    <td width="25%"><label><?=$ym->dataFields['t_cprice']['info']?></label><select name="t_cprice"><option value="0">нет</option><option value="1">да</option></select></td>
    <td width="25%">&nbsp;</td>
    <td width="25%"></td>
    <td width="25%"></td>
  </tr>
</table>
</fieldset>
<fieldset class="ui" style="padding:15px"><legend>Особенности</legend>
<p>Этот режим выгрузки предназначен для выгрузки данных на другие сайты, работающих на нашем шинном движке и поддерживающих функцию импорта из наборов.</p>
<p><em><strong>Безопасность.</strong></em> Авторизация или какие либо коды для доступа к файлу набора не нужны, для доступа достаточно имени файла набора.</p>
<p>Данные в наборе всегда актуальны, так как формируются &quot;на лету&quot;, а не сохраняются &quot;по кнопке&quot; где-то в файле на сервере.</p>
<p><strong>Внимание.</strong> Не стоит всю базу включать в один набор. Т.к. при импорте и экспорте набора в память загружается целиком структура данных, возможно возникновение ошибки не хватки памяти. Гораздо безопаснее разнести данные по разным наборам.</p>
<p>Набор данных состоит из:</p>
<ul>
  <li>списка брендов</li>
  <li>списка моделей</li>
  <li>списка размеров</li>
</ul>
<p>Можно добавить в набор список брендов. Тогда будет выгружены все модели и типоразмеры этих брендов.</p>
<p>Можно добавить в набор список моделей. Тогда будет выгружены все типоразмеры этих моделей.</p>
<p>Можно добавить в набор список типоразмеров. Тогда будет выгружены только эти типоразмеры.</p>
<p>Файл xml состоит из трех основных секций: </p>
<ol>
  <li>секция брендов.</li>
  <li>секция моделей</li>
  <li>секция типоразмеров</li>
</ol>
<p>Состав выгружаемой информации, такой как: описание, урл изображений, идентифаторы, цена, складской остаток и т.д, задается для каждого набора индивидуально. Помимо выранных настроек выгрузки, по умолчанию будет выгружена информация: базовое название модели/бренда, суффикс модели, полный типоразмер, статус скрытости, внутренний код. Можно выгружать избыточную информацию о товарах, т.к. принимающая сторона имеет свои настройки для обновлений данных сайта.</p>
<p><strong>Типовые примеры. </strong></p>
<p>Исходные данные - пустой набор.</p>
<p>1. Если в набор добавлена модель M и типоразмер Т, не входящий в модель М. Будет выгружены: бренд к которой относится модель М и бренд типоразмера Т, модель М, модель типоразмера Т, все типоразмеры модели М, типоразмер Т. </p>
<p>2. Если в набор добавлен бренд Б, модель М входящая в бренд Б. Будет выгружено: все модели бренда Б, все типоразмеры моделей бренда Б, бренд Б.</p>
<p>3. Если в набор добавлено несколько типоразмеров из разных или одной модели/брендов. Будет выгружено: все выбраные типразмеры, модели и бренды выбранных типоразмеров.</p>
<p>Т.е., как видно из примеров, при выгрузке используется метод перекрывания по иерархии включений данных в набор.</p>
</fieldset>