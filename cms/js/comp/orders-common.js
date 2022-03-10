function settingsClick(e)
{

    var $settingsDlg=$('<div id="settingsDlg" title="Персональные настройки"></div>')
        .appendTo('body')
        .dialog({
            autoOpen: true,
            modal: true,
            resizable: true,
            closeOnEscape: true,
            height: 'auto',
            width: 400,
            position: { my: "right top", at: "left bottom", of: e.target},
            close: function ()
            {
                $(this).dialog('destroy').remove();
            },
            buttons: [
                {
                    text: 'Записать изменения',
                    click: function ()
                    {
                        var $f=$('#settingsDlg');
                        var v;
                        v=$f.find('[name=oList_suplrSuggestOff]').prop('checked');
                        UVarSet('oList_suplrSuggestOff',v);
                        v=$f.find('[name=clientMailSign]').val();
                        UVarSet('clientMailSign',v);
                        v=$f.find('[name=oList_hideStates]').prop('checked');
                        UVarSet('oList_hideStates',v);
                        $(this).dialog('close');
                        logit(window.UDATA);
                    }
                },
                {
                    text: 'Отмена',
                    click: function ()
                    {
                        $(this).dialog('close');
                    }
                }
            ],
            open: function()
            {
                var $el=$('#settingsDlg');

                var html='<form><fieldset class="ui" style="margin-bottom: 10px; padding: 5px 10px;"><legend class="ui">Список заказов</legend>';

                if(window.setup.adminCfg.purchase != undefined && window.setup.adminCfg.purchase.suplrHinting != undefined)
                    html+='<input style="margin:0 10px 5px 0; vertical-align: middle" type="checkbox" name="oList_suplrSuggestOff" value="1" id="oList_suplrSuggestOff"><label for="oList_suplrSuggestOff">не показывать рекомендации по поставщикам</label><br>';

                html+=
                    '<input style="margin:0 10px 5px 0; vertical-align: middle" type="checkbox" name="oList_hideStates" value="1" id="oList_hideStates"><label for="oList_hideStates">не показывать выпадающий список со статусами</label>' +
                    '</fieldset>' +
                    '<fieldset class="ui" style="padding: 5px 10px">' +
                    '<legend class="ui">Подпись к E-Mail сообщениям</legend>' +
                    '<textarea name="clientMailSign" style="width: 97%; height: 100px"></textarea>' +
                    '<br><small>Вводите текст или в формате HTML с форматированием или без тегов. Во втором случае символы возврата строки будут преобразованы в &lsaquo;br&rsaquo; автматически.</small>' +
                    '</fieldset>' +
                    '</form>';

                $el.html(html);

                $el.find('[name=clientMailSign]').focus().val(window.UDATA.clientMailSign);
                $el.find('[name=oList_hideStates]').prop('checked', window.UDATA.oList_hideStates);

                if(typeof window.setup.adminCfg.purchase != 'undefined' && typeof window.setup.adminCfg.purchase.suplrHinting != 'undefined')
                   $el.find('[name=oList_suplrSuggestOff]').prop('checked', window.UDATA.oList_suplrSuggestOff);
            }
        });
    return false;
}


