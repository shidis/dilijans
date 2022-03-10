<div class="box-grey feedback">
    <h5 class="h-form"><a href="#" onclick="$('form.feedback').get(0).reset(); return false;">очистить форму</a>Форма обратной связи</h5>
    <form action="#" class="form-style-02 feedback">
        <table>
            <tr>
                <td width="170px">Ваше имя <sup>*</sup></td>
                <td><div class="input" for="name"><input type="text" name="name"><img src="/app/images/uncorrect.png" alt=""><img src="/app/images/correct.png" alt=""></div></td>
                <td style="padding-left:44px; width:190px;" rowspan="4">
                    <p class="line"><b>Обратите внимание!</b>Поля, помеченные звездочкой *, обязательны для заполнения.</p>
                    <p class="line">Эту форму можно использовать для отправки письма администрации интернет-магазина.</p>
                    <p>Заполните все поля и нажмите кнопку «Отправить». Ответ будет выслан на указанный вами e-mail.</p>
                </td>
            </tr>
            <tr>
                <td>Контактный телефон</td>
                <td>
                    <table>
                        <tr>
                            <td width="71px"><div class="input2" for="tel"><input type="text" name="tel"></div></td>
                            <td width="4px">&nbsp;</td>
                            <td><div class="input"><input type="text"><img src="/app/images/uncorrect.png" alt=""><img src="/app/images/correct.png" alt=""></div></td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td>Адрес электронной почты <sup>*</sup></td>
                <td><div class="input" for="email"><input type="text" name="email"><img src="/app/images/uncorrect.png" alt=""><img src="/app/images/correct.png" alt=""></div></td>
            </tr>
            <tr>
                <td>Сообщение <sup>*</sup></td>
                <td>
                    <div class="input textarea" for="msg"><textarea name="msg" id="" ></textarea><img src="/app/images/uncorrect.png" alt=""><img src="/app/images/correct.png" alt=""></div>
                </td>
            </tr>
            <tr class="last">
                <td></td>
                <td style="padding-right:14px;"><input type="button" class="oform" onclick="$('form.feedback').submit(); return false;" value="Отправить"></td>
                <td></td>
            </tr>
        </table>
    </form>
</div>


