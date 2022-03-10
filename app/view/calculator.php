<div class="box-grey-01">
<h2 class="pad">Пересчет типоразмера шины</h2>
<div class="box-ov">
    <div class="right-box">
        <img src="/app/images/img-text-02.png" alt="визуальный шинный калькулятор">
    </div>
    <div class="box-ov calc-ov-v">
        <div class="box-padding ctext">
           <?=$tyreText?>
        </div>
        <div class="box-filter-grey">
            <form action="" class="form-style-01">
                <table class="calc-ov-v__table">
                    <tr>
                        <td width="140px" class="va" for="oldWidth">Старый типоразмер:</td>
                        <td>
                            <table>
                                <tr>
                                    <td>
                                        <div class="select-01 old">
                                            <span></span>
                                            <select name="select"  id="oldWidth">
                                                <option value="145">145</option>
                                                <option value="155">155</option>
                                                <option value="165">165</option>
                                                <option value="175" selected="selected">175</option>
                                                <option value="185">185</option>
                                                <option value="195">195</option>
                                                <option value="205">205</option>
                                                <option value="215">215</option>
                                                <option value="225">225</option>
                                                <option value="235">235</option>
                                                <option value="245">245</option>
                                                <option value="255">255</option>
                                                <option value="265">265</option>
                                                <option value="275">275</option>
                                                <option value="285">285</option>
                                                <option value="295">295</option>
                                                <option value="305">305</option>
                                                <option value="315">315</option>
                                                <option value="325">325&nbsp;</option>
                                            </select>
                                            <i></i>
                                        </div>
                                    </td>
                                    <td width="20px" style="text-align:center;">/</td>
                                    <td>
                                        <div class="select-01">
                                            <span></span>
                                            <select name="select2"  id="oldProfile">
                                                <option value="30">30</option>
                                                <option value="35">35</option>
                                                <option value="40">40</option>
                                                <option value="45">45</option>
                                                <option value="50">50</option>
                                                <option value="55">55</option>
                                                <option value="60">60</option>
                                                <option value="65">65</option>
                                                <option value="70" selected="selected">70</option>
                                                <option value="75">75</option>
                                                <option value="80">80</option>
                                                <option value="85">85</option>
                                            </select>
                                            <i></i>
                                        </div>
                                    </td>
                                    <td width="20px" style="text-align:center;">R</td>
                                    <td>
                                        <div class="select-01">
                                            <span></span>
                                            <select name="select2" id="oldDiameter">
                                                <option value="12">12</option>
                                                <option value="13" selected="selected">13</option>
                                                <option value="14">14</option>
                                                <option value="15">15</option>
                                                <option value="16">16</option>
                                                <option value="17">17</option>
                                                <option value="18">18</option>
                                                <option value="19">19</option>
                                                <option value="20">20</option>
                                                <option value="21">21</option>
                                                <option value="22">22</option>
                                                <option value="24">24</option>
                                                <option value="26">26</option>
                                                <option value="28">28</option>
                                                <option value="32">32</option>
                                            </select>
                                            <i></i>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="5" id="t1"></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr class="last">
                        <td width="140px" class="va" for="newWidth">Новый типоразмер:</td>
                        <td>
                            <table>
                                <tr>
                                    <td>
                                        <div class="select-01">
                                            <span></span>
                                            <select name="select2" id="newWidth">
                                                <option value="145">145</option>
                                                <option value="155">155</option>
                                                <option value="165">165</option>
                                                <option value="175" selected="selected">175</option>
                                                <option value="185">185</option>
                                                <option value="195">195</option>
                                                <option value="205">205</option>
                                                <option value="215">215</option>
                                                <option value="225">225</option>
                                                <option value="235">235</option>
                                                <option value="245">245</option>
                                                <option value="255">255</option>
                                                <option value="265">265</option>
                                                <option value="275">275</option>
                                                <option value="285">285</option>
                                                <option value="295">295</option>
                                                <option value="305">305</option>
                                                <option value="315">315</option>
                                                <option value="325">325</option>
                                            </select>
                                            <i></i>
                                        </div>
                                    </td>
                                    <td width="20px" style="text-align:center;">/</td>
                                    <td>
                                        <div class="select-01">
                                            <span></span>
                                            <select name="" id="newProfile">
                                                <option value="30">30</option>
                                                <option value="35">35</option>
                                                <option value="40">40</option>
                                                <option value="45">45</option>
                                                <option value="50">50</option>
                                                <option value="55">55</option>
                                                <option value="60">60</option>
                                                <option value="65">65</option>
                                                <option value="70" selected="selected">70</option>
                                                <option value="75">75</option>
                                                <option value="80">80</option>
                                                <option value="85">85</option>
                                            </select>
                                            <i></i>
                                        </div>
                                    </td>
                                    <td width="20px" style="text-align:center;">R</td>
                                    <td>
                                        <div class="select-01">
                                            <span></span>
                                            <select name="" id="newDiameter">
                                                <option value="12">12</option>
                                                <option value="13" selected="selected">13</option>
                                                <option value="14">14</option>
                                                <option value="15">15</option>
                                                <option value="16">16</option>
                                                <option value="17">17</option>
                                                <option value="18">18</option>
                                                <option value="19">19</option>
                                                <option value="20">20</option>
                                                <option value="21">21</option>
                                                <option value="22">22</option>
                                                <option value="24">24</option>
                                                <option value="26">26</option>
                                                <option value="28">28</option>
                                                <option value="32">32</option>
                                            </select>
                                            <i></i>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="5" id="t2"></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </form>
        </div>
        <div class="box-padding">
            <h5 class="h-form">Таблица совместимости</h5>
        </div>
        <div class="table-style-01 resultsTable">
            <div></div>
            <table>
                <thead>
                <tr>
                    <td>Размеры</td>
                    <td>Старый</td>
                    <td>Новый</td>
                    <td>Разница</td>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td id="labelShemeL">Ширина шины (L ) мм</td>
                    <td id="oldL"></td>
                    <td><b id="newL"></b></td>
                    <td><i id="deltaL">10</i></td>
                </tr>
                <tr>
                    <td id="labelShemeH">Высота профиля (H) мм</td>
                    <td id="oldH"></td>
                    <td><b id="newH"></b></td>
                    <td><i id="deltaH"></i></td>
                </tr>
                <tr>
                    <td id="labelShemeD">Внутренний диаметр (d) мм</td>
                    <td id="oldD"></td>
                    <td><b id="newD"></b></td>
                    <td><i id="deltaD"></i></td>
                </tr>
                <tr>
                    <td id="labelShemeDD">Внешний диаметр (D) мм</td>
                    <td id="oldDD"></td>
                    <td><b id="newDD"></b></td>
                    <td><i id="deltaDD"></i></td>
                </tr>
                <tr>
                    <td>Показания спидометра, км/ч</td>
                    <td>60</td>
                    <td><b>60</b></td>
                    <td><i>-</i></td>
                </tr>
                <tr>
                    <td>Реальная скорость, км/ч</td>
                    <td>60</td>
                    <td><b id="newRS"></b></td>
                    <td><i id="deltaRS"></i></td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<h2 class="pad">Расчет ширины диска</h2>
<div class="box-ov">
    <div class="right-box">
        <img src="/app/images/img-text-03.png" alt="калькулятор дисков">
    </div>

    <div class="box-ov">
        <div class="box-padding ctext">
            <?=$diskText?>
        </div>
        <div class="box-blue old">
            <form action="#" class="form-style-01">
                <table class="calc-ov-v__table">
                    <tr class="last">
                        <td width="140px" for="tireWidth">Типоразмер шины:</td>
                        <td>
                            <div class="select-01">
                                <span></span>
                                <select name="" id="tireWidth">
                                    <option value="145">145</option>
                                    <option value="155">155</option>
                                    <option value="165">165</option>
                                    <option value="175" selected="selected">175</option>
                                    <option value="185">185</option>
                                    <option value="195">195</option>
                                    <option value="205">205</option>
                                    <option value="215">215</option>
                                    <option value="225">225</option>
                                    <option value="235">235</option>
                                    <option value="245">245</option>
                                    <option value="255">255</option>
                                    <option value="265">265</option>
                                    <option value="275">275</option>
                                    <option value="285">285</option>
                                    <option value="295">295</option>
                                    <option value="305">305</option>
                                    <option value="315">315</option>
                                    <option value="325">325</option>
                                </select>
                                <i></i>
                            </div>
                        </td>
                        <td width="20px" style="text-align:center;">/</td>
                        <td>
                            <div class="select-01">
                                <span></span>
                                <select name="" id="tireProfile">
                                    <option value="30">30</option>
                                    <option value="35">35</option>
                                    <option value="40">40</option>
                                    <option value="45">45</option>
                                    <option value="50">50</option>
                                    <option value="55">55</option>
                                    <option value="60">60</option>
                                    <option value="65">65</option>
                                    <option value="70" selected="selected">70</option>
                                    <option value="75">75</option>
                                    <option value="80">80</option>
                                    <option value="85">85</option>
                                </select>
                                <i></i>
                            </div>
                        </td>
                        <td width="20px" style="text-align:center;">R</td>
                        <td>
                            <div class="select-01">
                                <span></span>
                                <select name="" id="tireDiameter">
                                    <option value="12">12</option>
                                    <option value="13" selected="selected">13</option>
                                    <option value="14">14</option>
                                    <option value="15">15</option>
                                    <option value="16">16</option>
                                    <option value="17">17</option>
                                    <option value="18">18</option>
                                    <option value="19">19</option>
                                    <option value="20">20</option>
                                    <option value="21">21</option>
                                    <option value="22">22</option>
                                    <option value="24">24</option>
                                    <option value="26">26</option>
                                    <option value="28">28</option>
                                    <option value="32">32</option>
                                </select>
                                <i></i>
                            </div>
                        </td>
                    </tr>
                </table>
            </form>
        </div>
        <div class="box-padding">
            <h5 class="h-form">Параметры диска (в дюймах)</h5>
            <ul class="des">
                <li><p><i>Диаметр</i><b id="discDiameter"></b></p></li>
                <li><p><i>Ширина (мin)</i><b id="discWidthMin"></b></p></li>
                <li><p><i>Ширина (маx)</i><b id="discWidthMax"></b></p></li>
            </ul>
        </div>
    </div>
</div>

</div>

<script type="text/javascript">

    var turl='/<?=App_Route::_getUrl('tSearch')?>.html?';

</script>
