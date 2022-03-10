Tags={
    axUrl: '/cms/be/tags.php',
    pageMustReload: false
};

Tags.init=function()
{
    if(this.allTagsButton.length){

        var self=this;

        $('body').append(
            '<div id="tagsEditorDlg" style="padding-top:20px">'+
                '<div id="_te0">'+
                    '<div id="_teGroups">'+
                        '<fieldset><legend>Группы тегов</legend>'+
                        '<select id="_teGroupId"><option value="">все группы</option></select>'+
                        '<button id="_teAddGroup" title="добавить группу" class="tooltip"><span class="ui-icon ui-icon-plusthick"></span>добавить</button>'+
                        '<button id="_teEditGroup" title="изменить группу"><span class="ui-icon ui-icon-pencil"></span>изменить</button>'+
                        '<button id="_teDelGroup" title="удалить группу"><span class="ui-icon ui-icon-minusthick"></span>удалить</button>'+
                    '</fieldset>'+
                    '</div>'+
                    '<fieldset><legend>Теги</legend>'+
                        '<div id="_teTags">'+
                            '<div id="_teTagsNewWrap"><input type="text" id="_teNewTagName" style="width:400px" value=""><button id="_teNewTagAdd"><span class="ui-icon ui-icon-plusthick"></span>добавить тег</button></div>'+
                            '<div id="_teTagsList"></div>'+
                        '</div>'+
                    '</fieldset>'+
                '</div>'+
                '<div id="_teGroup">'+
                    '<div id="_teGroupButs">'+
                        '<button id="_teGroupAddSave" title="добавить группу"><span class="ui-icon ui-icon-circle-check"></span>записать</button>'+
                        '<button id="_teGroupEditSave" title="записать"><span class="ui-icon ui-icon-circle-check"></span>сохранить изменения</button>'+
                        '<button id="_teBack0" title="вернуться без записи"><span class="ui-icon ui-icon-arrowreturnthick-1-w"></span>назад без записи</button>'+
                    '</div>'+
                    '<form>'+
                        '<fieldset><legend id="_teGroupLegend" class="ui"></legend>'+
                            '<div class="row"><label for="_teGroupName">Название группы (обязательно)</label><input style="width:99%" type="text" id="_teGroupName" name="name"></div>'+
                            '<div class="row"><label for="_teGroupPos">Приоритет в списке</label><input style="width:50px" type="text" id="_teGroupPos" name="pos"></div>'+
                            '<div class="row"><label for="_teGroupSname">Псевдоним (если пусто - автогенерация)</label><input style="width: 99%" type="text" id="_teGroupSname" name="sname"></div>'+
                            '<div class="row"><label for="_teGroupInfo">Описание</label><textarea style="width: 99%; height:80px" id="_teGroupInfo" name="info"></textarea></div>'+
                        '</fieldset>'+
                    '</form>'+
                '</div>'+
             '</div>'
        );
        $('#_teGroups').css({
            margin:'0 0 10px'
        });
        $('#tagsEditorDlg fieldset legend').css({
            'color':'#789DD7'
        });
        $('#_teGroups').buttonset();
        $('#_teGroups button:first').css({
            'margin-left':'15px'
        });
        $('#_teGroup .row').css({
            'overflow':'hidden',
            'margin':'10px 0'
        });
        $('#_teGroup .row label').css({
            'display':'block'
        });

        this.newTagFieldBind();
        this.tagsList();

        // выбор группы из списка
        $('#_teGroupId').css({width:'200px'}).chosen().change(function(){
            if($('#_teGroupId').val()=='') {
                $('#_teEditGroup, #_teDelGroup').button('disable');
                $("#_teTagsNewWrap").slideUp(100);
            } else {
                $('#_teEditGroup, #_teDelGroup').button('enable');
                $("#_teTagsNewWrap").slideDown(100);
            }
            self.tagsList();
        });
        // кнопка НАЗАД
        $('#_teBack0').button().click(function(){
            $('#_teGroup').hide();
            $('#_te0').show();
            $("#_teNewTagName").focus();
        });
        // новая группа
        $('#_teAddGroup').click(function(){
            $('#_te0').hide();
            $('#_teGroup').show();
            $('#_teGroupLegend').html('Добавить новую теговую группу');
            $('#_teGroupAddSave').show();
            $('#_teGroupEditSave').hide();
            $('#_teGroup form input:first').focus();
            $('#_teGroup form input').keydown(function(e){
                if (e.which == 13) $("#_teGroupAddSave").trigger('click');
            });
        });
        // записать новую группу
        $("#_teGroupAddSave").button().click(function(){
            $.ajax({
                url: self.axUrl,
                data: {
                    act: 'addGroup',
                    gr: self.gr,
                    frm: $('#_teGroup form').serialize()
                },
                success: function(r){
                    if(r.fres){
                        self.loadTagGroups();
                        $('#_teGroup').hide();
                        $('#_te0').show();
                        $('#_teGroup form').get(0).reset();
                        $('#_teGroup form input').unbind('keydown');
                    }else err(r.fres_msg);
                }
            });
        });

        // изменить группу
        $('#_teEditGroup').click(function(){
            $.ajax({
                url: self.axUrl,
                data: {
                    act: 'loadGroup',
                    gr: self.gr,
                    id: $('#_teGroupId').val()
                },
                success: function (r){
                    if(r.fres){
                        populate('#_teGroup form', r.frm);
                        $('#_te0').hide();
                        $('#_teGroup').show();
                        $('#_teGroupLegend').html('Изменить теговую группу');
                        $('#_teGroupAddSave').hide();
                        $('#_teGroupEditSave').show();
                        $('#_teGroup form input:first').focus();
                        $('#_teGroup form input').keydown(function(e){
                            if (e.which == 13) $("#_teGroupEditSave").trigger('click');
                        });
                    }else err(r.fres_msg);
                }
            });

        });
        // записать изменения в группе
        $("#_teGroupEditSave").button().click(function(){
            $.ajax({
                url: self.axUrl,
                data: {
                    act: 'commitChangesGroup',
                    id: $('#_teGroupId').val(),
                    frm: $('#_teGroup form').serialize()
                },
                success: function(r){
                    if(r.fres){
                        self.loadTagGroups();
                        $('#_teGroup').hide();
                        $('#_te0').show();
                        $('#_teGroup form').get(0).reset();
                        $('#_teGroup form input').unbind('keydown');
                        self.pageMustReload=true;
                    }else err(r.fres_msg);
                }
            });
        });
        // удалить группу
        $('#_teDelGroup').click(function(){
            if(!confirm('Будут удалены все теги в группе и упоминания этих тегов в моделях. Удалять?')) return;
            $.ajax({
                url: self.axUrl,
                data: {
                    act: 'removeGroup',
                    id: $('#_teGroupId').val()
                },
                success: function(r){
                    if(r.fres){
                        self.loadTagGroups();
                        self.pageMustReload=true;
                    }else err(r.fres_msg);
                }
            });
        });

        // добавить новый тег
        $("#_teNewTagAdd").click(function(){
            var gid=$('#_teGroupId').val();
            var tag=$('#_teNewTagName').val();
            if(tag!='' && gid>0){
                $.ajax({
                    url: self.axUrl,
                    data: {
                        act: 'addTag',
                        gid: $('#_teGroupId').val(),
                        name: tag
                    },
                    success: function(r){
                        if(r.fres){
                            self.tagsList();
                            self.pageMustReload=true;
                            $("#_teNewTagName").val('');
                        }else err(r.fres_msg);
                    }

                });
            }
        });

        $('button span').css({'float':'left'});
        $('#_teEditGroup, #_teDelGroup').button( "disable" );
        $("#_teTagsNewWrap").hide();

        $('#tagsEditorDlg').dialog({
            title: "Все доступные теги",
            autoOpen:false,
            modal:true,
            minWidth:580,
            minHeight:400,
            buttons:{
                'Закрыть':function(){
                    $( this ).dialog( "close" );
                }
            },
            open: function(e,ui){
                $('#_te0').show();
                $('#_teGroup').hide();
                $('#_teGroupAddSave, #_teGroupEditSave').hide();
                self.loadTagGroups();
            },
            close: function(){
                if(self.pageMustReload) location.href=location.href;
            }
        });
    }
    $(this.allTagsButton).click(function() {
        $('#tagsEditorDlg').dialog('open');
        return false;
    })
}

// загрузка списка тегов (с группами или без)
Tags.tagsList = function ()
{
    $('#_teTagsList').html('<img src="/assets/images/ax/siteheart.gif">');
    $("#_teNewTagName").focus();
    var gid=$('#_teGroupId').val();
    var self=this;
    $.ajax({
        url: this.axUrl,
        data: {
            act: 'tagsList',
            gr: this.gr,
            gid: gid
        },
        success: function(r){
            if(r.fres){
                var s='';
                if(!gid) s+=('<div style="border:1px solid #ccc; border-radius:5px; padding: 5px; margin-bottom:15px"><span style="float:left; margin-right:5px" class="ui-icon ui-icon-info"></span> Чтобы добавить тег, выберите сначала группу для него</div>');
                s+=('<ul>');
                if(gid){
                    for(var k in r.tags){
                        s+=('<li id="'+ r.tags[k]['tag_id']+'">'+ r.tags[k]['name']+'</li>');
                    }
                }else{
                    for(var group_id in r.tags){
                        s+=('<li id=""><b>Группа: &quot;'+r.groups[group_id]['name']+'&quot;:</b>');
                        s+=('<ul gid="'+group_id+'">');
                        for(var k in r.tags[group_id]){
                            s+=('<li id="'+ r.tags[group_id][k]['tag_id']+'">'+ r.tags[group_id][k]['name']+'</li>')
                        }
                        s+=('</ul></li>');
                    }
                }
                s+='</ul>';
                $('#_teTagsList').html(s);
                $('#_teTagsList ul').css({
                    'padding':0,
                    'margin':'5px 0 5px 0'
                });
                $('#_teTagsList li').css({
                    'list-style':'none',
                    'padding':'0 0 0 30px',
                    'margin':'3px 0 3px 5px'
                });
                // инлайн редактирование тега
                $('#_teTagsList li[id!=""]')
                    .css({
                        'color':'#2E83CE',
                        'font-weight':'bold'
                    })
                    .editable(self.axUrl+"?act=inlineEditTag", {
                        indicator : "<img src='/cms/img/loader.white.gif'>",
                        tooltip   : "Клик для изменения",
                        onblur     : 'cancel',
                        cancel    : 'отменить',
                        submit    : 'записать',
                        callback: function(){
                            self.pageMustReload=true;
                        }
                    })
                    .mouseenter(function(){
                        $('._teTagDel').remove();
                        var o=$(this).offset();
                        $('#_teTagsList').append('<span tag_id="'+$(this).attr('id')+'"  class="_teTagDel ui-icon ui-icon-circle-close" style="position:absolute; cursor: pointer; "></span>');
                        $('._teTagDel')
                            .offset({left: o.left+10, top: o.top})
                            .click(function(){
                                var tag_id=$(this).attr('tag_id');
                                $.ajax({
                                    url: self.axUrl,
                                    data: {
                                        act: 'removeTag',
                                        tag_id: tag_id
                                    },
                                    success: function(r){
                                        if(r.fres){
                                            self.tagsList();
                                            self.pageMustReload=true;
                                        }else err(r.fres_msg);
                                    }
                                });
                            });
                    });


            }else err(r.fres_msg);
        }
    });

}

Tags.newTagFieldBind=function()
{
    $("#_teNewTagName").keydown(function(e){
        if (e.which == 13) $("#_teNewTagAdd").trigger('click');
    });
}
Tags.newTagFieldUnbind = function()
{
    $("#_teNewTagName").unbind('click');
}

Tags.loadTagGroups=function ()
{
    $.ajax({
        url: this.axUrl,
        data: {
            act: 'groupList',
            gr: this.gr
        },
        success: function(r){
            if(r.fres){
                var id=$('#_teGroupId').val();
                $('#_teGroupId option[value!=""]').remove();
                for(var k in r.groups){
                    $('#_teGroupId').append('<option value="'+ k+'">'+ r.groups[k]['name']+'</option>');
                }
                $('#_teGroupId').val(id).trigger("chosen:updated");
                if(id!=$('#_teGroupId').val()) $('#_teGroupId').trigger('change');
                $("#_teNewTagName").focus();
            }else err(r.fres_msg);

        }
    });

}

Tags.model_bot_controller=function()
{
    var self=this;
    $('.tags')
        .each(function(){
            var mt=$(this).attr('v');
            $(this)
                .css({'width':'350px'})
                .attr('data-placeholder','без тегов')
                .attr('multiple','')
                .html(function(){
                    var s='<option value=""></option>';
                    for(var k in Tags.groups){
                        if(isDefined(Tags.tags[k])){
                            s+='<optgroup label="'+Tags.groups[k]['name']+'">';
                            for(var i in Tags.tags[k]){
                                s+='<option ';
                                if(mt.indexOf('.'+Tags.tags[k][i]['tag_id']+'.')!=-1) s+='selected ';
                                s+='value="'+Tags.tags[k][i]['tag_id']+'">'+Tags.tags[k][i]['name']+'</option>';
                            }
                            s+='</optgroup>';
                        }
                    }
                    return s;
                }())
                .chosen({
                    no_results_text:'не найдено'
                });
        })
        .change(function(){
            $.ajax({
                url: self.axUrl,
                data: {
                    act: 'modelTagsChange',
                    tagIds: $(this).val(),
                    model_id: $(this).attr('model_id')
                },
                success: function(r){
                    if(!r.fres) err(r.fres_msg);
                }
            });
        });
}

$(document).ready(function(){
    if(Tags.enabled) {
        Tags.init();
        if(Tags.from=='model_bot') Tags.model_bot_controller();
    }
});