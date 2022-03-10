$(document).ready(function()
{
    var sb= $('.slog-box .toggle button')
        .css({height: '25px'})
        .button({
            icons: { primary: "ui-icon-arrowthickstop-1-s", secondary: null }
        })
        .click(function(e){
            var $el=$('#slog');
            if($el.hasClass('slog-active')) {
                $el.removeClass('slog-active');
                $(this).button('option', {icons: { primary: "ui-icon-arrowthickstop-1-s", secondary: null }});
                $('.edit_area').removeClass('edit_area-active');
                if(e.clientX !== undefined) SVarSet('_slogBoxActive',false);
            } else {
                $el.addClass('slog-active');
                $('.edit_area').addClass('edit_area-active');
                $(this).button('option', {icons: { primary: "ui-icon-arrowthickstop-1-n", secondary: null }});
                if(e.clientX !== undefined) SVarSet('_slogBoxActive',true);
            }
        });

    if(SDATA['_slogBoxActive']) sb.click();

    $('.slog-box #slogAddFiles')
        .button()
        .click(slogAddFiles);

    $('.slog-box #slogAddMsg')
        .on({
            focus: function()
            {
                $(this).css({height:'100px'}).attr('placeholder',"вводиим сообщение и Ctrl-Enter для сохранения");

            },
            keypress: function(e)
            {
                if ( e.which == 10 && e.ctrlKey ) {
                    $('.slog-box #slogAddLog').click();
                }
            }
        });

    $('.slog-box #slogAddLog')
        .button()
        .click(slogAddLog);

    updateSLogCounters();

    slogMakeList();

    $(document).on('click', '.slog-box .data .logEditable .del', slogDelLog);
    $(document).on('click', '.slog-box .data .fileItemEditable .del', slogDelFile);
    $(document).on('click', '.slog-box .data .row .file', function(){
        var hash=$(this).attr('hash');
        window.open('/cms/ext/getOrderFile.php?hash='+hash, '_self');
    });

});

function slogAddFiles(e)
{
    $('<div id="slogAddFilesDlg" title="Добавить файл(ы)"><div class="area"><div style="overflow: hidden">Перетащите файлы сюда<br>или <br>выберите файл на диске:<br><input class="file-input" type="file"></div></div></div>')
        .appendTo('body')
        .dialog({
            autoOpen: true,
            modal: true,
            resizable: false,
            closeOnEscape: true,
            height: 400,
            width: 500,
            position: { my: "right top", at: "right top", of: e.target},
            close: function ()
            {
                $(this).dialog('destroy').remove();
            },
            buttons: [
                {
                    text: 'Добавить',
                    click: function ()
                    {
                        if(this.uploading) note('Уже работаю...');
                        else if(!this.$input.duCount()) note('Нечего загружать');
                        else{
                            if ($.support.fileSending) {
                                loader.cloader('show');
                                this.uploading=true;
                                this.$input.duStart();
                            }else err('fileSending API не поддерживается браузером');
                        }
                    }
                },
                {
                    text: 'Отменить',
                    click: function ()
                    {
                        $(this).dialog('close');
                    }
                }
            ],
            open: function ()
            {
                var $area=$('#slogAddFilesDlg .area');
                this.$input=$('#slogAddFilesDlg .file-input');
                this.$input.damnUploader({
                    url: '../be/orderEdit.php?act=slogAddFile&order_id='+setup.order_id,
                    limit: 10,
                    multiple: true,
                    dropping: true,
                    dropBox: $area
                });

                $area.on({
                    dragover: function()
                    {
                        return false;
                    }
                });

                var self=this;

                this.$input.on({
                    'du.completed': function(){
                        self.uploading=false;
                        loader.cloader('hide');
                        $('#overlayDlg').dialog('close');
                        $('.slog-box #slogAddMsg').val('');
                        $(self).dialog('close');
                    },
                    'du.add': function(e)
                    {
                        var ui = e.uploadItem;
                        var filename = ui.file.name || "";
                        $area.append('<div class="file">'+filename+'</div> ');

                        ui.addPostData('msg', $('.slog-box #slogAddMsg').val());

                        ui.progressCallback = function(percent)
                        {
                            $('#overlayDlg').html('загружаю файл '+filename+' ... '+percent+'%').dialog('open');
                        }

                        ui.completeCallback = function(success, data, errorCode) {
                            logit('******');
                            logit('Загружено '+this.file.name+' size: '+this.file.size);
                            // this.file == $_FILES
                            if (success) {
                                var r=$.parseJSON(data);
                                if(r.fres){
                                    //note(this.file.name+' OK.');
                                    var html=$('#slogTypeFileEditable').render(r.data);
                                    $el=$(html).prependTo('.slog-box .data .list');
                                    fileEdit_bind($el);
                                    setup.slog.numFiles++;
                                    updateSLogCounters();
                                }else
                                    note(r.fres_msg,'error');
                            } else {
                                logit('uploading failed. Response code is:', errorCode);
                            }
                        }
                    },
                    'du.limit' : function() {
                        logit("Хватит файлов пока.");
                    }
                });

            }
        });
}

function slogDelLog(e)
{
    $(e.target).prop('disabled',true);
    var id=$(e.target).parent().parent().attr('slog_id');
    $.ajax({
        data: {
            act: 'slogDelLog',
            id: id
        },
        success: function(r)
        {
            if(r.fres){
                $('.slog-box .data .row[slog_id='+id+']').slideUp('fast',function(){$(this).remove()});
                setup.slog.numLogs--;
                updateSLogCounters();
            } else
                err(r.fres_msg);
        }
    })
}

function slogDelFile(e)
{
    $(e.target).prop('disabled',true);
    var id=$(e.target).parent().parent().attr('file_id');
    $.ajax({
        data: {
            act: 'slogDelFile',
            id: id
        },
        success: function(r)
        {
            if(r.fres){
                $('.slog-box .data .row[file_id='+id+']').slideUp('fast',function(){$(this).remove()});
                setup.slog.numFiles--;
                updateSLogCounters();
            } else
                err(r.fres_msg);
        }
    })
}

function slogAddLog(e)
{
    var $msg=$('.slog-box #slogAddMsg');
    if($msg.val()=='') note('Заполните поле сообщения');
    else{
        $('.slog-box #slogAddLog').button('disable');
        $.ajax({
            data: {
                'act': 'slogAddLog',
                order_id: setup.order_id,
                'msg': $msg.val()
            },
            success: function(r)
            {
                if(r.fres){
                    $msg.val('');
                    $el=$($('#slogTypeLogEditable').render(r.data)).prependTo('.slog-box .data .list');
                    slogEdit_bind($el);
                    setup.slog.numLogs++;
                    updateSLogCounters();
                } else
                    err(r.fres_msg);
            },
            complete: function()
            {
                $('.slog-box #slogAddLog').button('enable');
            }
        })
    }
}

function slogEdit_bind($el)
{
    $el.find('.msg').editable({
        url: '../be/orderEdit.php?act=slogMsgPost',
        name: 'msg',
        pk: $el.attr('slog_id'),
        title: 'Сообщение (записать - ctrl+enter)',
        inputclass: 'xe-items-name',
        type: 'textarea',
        placement: 'left',
        mode: 'popup',
        emptytext: '&nbsp;&nbsp;&nbsp;',
        validate: function (value)
        {
            if ($.trim(value) == '') {
                return 'Сообщение не может быть пустым';
            }
        }
    });
}

function fileEdit_bind($el)
{
    $el.find('.msg').editable({
        url: '../be/orderEdit.php?act=fileMsgPost',
        name: 'msg',
        pk: $el.attr('file_id'),
        title: 'Сообщение (записать - ctrl+enter)',
        inputclass: 'xe-items-name',
        type: 'textarea',
        placement: 'left',
        mode: 'popup',
        emptytext:'&nbsp;&nbsp;&nbsp;&nbsp;'
    });
}

function updateSLogCounters()
{
    $('.slog-box .toggle button').button('option','label', 'лог операций ('+setup.slog.numLogs+' / '+setup.slog.numFiles+')');
}

function slogMakeList()
{
    var $list=$('.slog-box .data .list');

    _.each(setup.slog.data, function(v,k){
        if(v.type=='log'){
            if(setup.loggedUserId == v.createdById && v.protected==0){
                $el=$($('#slogTypeLogEditable').render(v)).appendTo($list);
                slogEdit_bind($el);
            }
            else {
                $el=$($('#slogTypeLogProtected').render(v)).appendTo($list);
                if(setup.loggedUserId == v.createdById) slogEdit_bind($el);
            }
        }else if(v.type=='file'){
            if(setup.loggedUserId == v.createdById && v.protected==0){
                $el=$($('#slogTypeFileEditable').render(v)).appendTo($list);
                fileEdit_bind($el);
            }
            else {
                $list.append($('#slogTypeFileProtected').render(v));
            }
        }
    })
}
