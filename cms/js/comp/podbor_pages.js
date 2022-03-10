$('document').ready(function () {
    $.ajaxSetup({
        type: 'POST',
        global: true,
        cache: false,
        dataType: 'json',
        //		timeout:1000,
        url: '../be/podbor_pages.php',
        error: Err
    });

    var modif_id;
    vendorLoad();
    $('#vendor').change(vendorChange);
    // ***
    $('.add_new_pp').click(function(){
        $('#form_pp #action').val('addNew');
        $('#form_pp').submit();
    });
});
////////////////////////////////////////////////////////////////////////////////////////////////////
function vendorLoad() {
    $('.choice:gt(0)').empty();
    $('#tables').hide('fast');
    $('#_vendors').html($('#loading1').html());
    $.ajax({
        data: {act: 'vendors'},
        success: function (res) {
            $('#_vendors').html(res.data);
            $('#vendor').change(vendorChange);
            cinit();
        }
    });
};

function vendorChange() {
    $('.choice:gt(0)').empty();
    if ($(this).val() == 0) return;
    $('#tables').hide('fast');
    $('#_models').html($('#loading1').html());
    $.ajax({
        data: {act: 'models', vendor_id: $(this).val()},
        success: function (res) {
            $('#_models').html(res.data);
            $('#model').change(modelChange);
            cinit();
        }
    });
};

function modelChange() {
    $('.choice:gt(1)').empty();
    if ($(this).val() == 0) return;
    $('#tables').hide('fast');
    $('#_years').html($('#loading1').html());
    $.ajax({
        data: {act: 'years', model_id: $(this).val()},
        success: function (res) {
            $('#_years').html(res.data);
            $('#year').change(yearChange);
            cinit();
        }
    });
};

function yearChange() {
    $('.choice:gt(2)').empty();
    if ($(this).val() == 0) return;
    $('#tables').hide('fast');
    $('#_modifs').html($('#loading1').html());
    $.ajax({
        data: {act: 'modifs', year_id: $(this).val()},
        success: function (res) {
            $('#_modifs').html(res.data);
            $('#modif').change(cinit);
            cinit();
        }
    });
};

function cinit() {
    var $wrapper = $('.content_wrap');
    $.ajax({
        data: {
            gr: $("#gr").val(),
            act: 'getData',
            vendor_id: $("#vendor").val(),
            model_id: $("#model").val(),
            year_id: $("#year").val(),
            modif_id: $("#modif").val()
        },
        success: function (res) {
            setTimeout(function(){
                $('#tables').css('display', 'block');
                $wrapper.html(res.data);}
            , 500);
        },
        beforeSend: function () {
            $wrapper.html('');
        }
    });
}

function del_cascade() {
    if(confirm('Уверены?'))
    {
        $('.content_wrap table tbody tr').each(function(){
            if($(this).find('.cc').is(':checked'))
            {
                del($(this).find('.delete')[0]);
            }
        });
    }
    return false;
}

function del(obj)
{
    var page_id = $(obj).parents('tr').attr('id');
    $.ajax({
        data: {
            act: 'del',
            page_id: page_id
        },
        success: function (r) {
            if(r.fres){
                $('#tables tr#'+page_id).remove();
            }else {
                note(r.fres_msg,'error');
            }
        },
    });
}
function edit(obj)
{
    var page_id = $(obj).parents('tr').attr('id');
    $('#form_pp #action').val('edit');
    $('#form_pp #page_id').val(page_id);
    $('#form_pp').submit();
}

$(document).on('click','a.h-sw',function(e){
    e.preventDefault();
    var td=$(this).parent();
    var id=$(this).parent().parent('tr').attr('id');
    var s=td.html();
    if(id>0){
        td.html(loading2);
        $.ajax({
            data: {act:'hSwitch', 'page_id':id},
            success:function(r){
                if(r.fres){
                    td.html(r.v);
                }else {
                    td.html(s);
                    note(r.fres_msg,'error');
                }
            }
        });
    } else note('нет ИД','note');
});

function SelectAll(mark, f) {
    $('#' + f + ' .cc').each(function(){
        this.checked = mark;
    });
}

