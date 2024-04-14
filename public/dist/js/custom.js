/*
    *common functions Start
*/
/*===============AJAX=================================*/
function dynamicAjax(fileName , reqType , dataObj)
{
    return new Promise((resolve,reject) => {

        $.ajax({
            url     : fileName,
            type    : reqType,
            async   : true,
            data    : dataObj,
            beforeSend: function() {
                // callLoader();
            },
            complete: function() {
                // endLoader();
            },
            dataType:'json',
            success : function (response)  { resolve(response); },
            error   : function (response)  { reject(response);  }
        });

    });
}
/*=====================================================================*/
