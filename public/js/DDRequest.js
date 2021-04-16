function AjaxError(XMLHttpRequest){
    var msg = $.parseJSON(XMLHttpRequest.responseText);
    var errormsg = '';
    var msgtitle = '';
    if(msg.hasOwnProperty('message')){
        msgtitle = msg.message;
    }else{
        msgtitle = '错误信息';
    }
    if(msg.hasOwnProperty('errors')){
        $.each(msg.errors, function(name, text) {
            errormsg += text  + '<br/>';
        });
    }else{
        errormsg = '空';
    }
    
    return [msgtitle, errormsg];
}

function AjaxSubmit(url, params, success, error,method) {
    $.ajax({
        url: url,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: method,
        dataType: "json",
        timeout: 600000,
        data: params,
        success: function(msg) {
            success(msg);
        },
        error: function(XMLHttpRequest) {
            error(AjaxError(XMLHttpRequest))
        }
    });
}




