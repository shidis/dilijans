$('document').ready(function(){

    $.ajaxSetup({
        type:'POST',
        cache:false,
        dataType: 'json',
        url: '../be/oshed.php',
        error: Err
    });

});
