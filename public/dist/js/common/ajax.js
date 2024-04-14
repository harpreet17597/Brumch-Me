/*Ajax Request*/
function dynamicAjax(fileName, reqType, dataObj,beforeCall,afterCall) {
   
    return new Promise((resolve, reject) => {
        $.ajax({
            url: fileName,
            type: reqType,
            async: true,
            data: dataObj,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            processData: false,
            contentType: false,
            beforeSend: function() {
                if(typeof beforeCall == 'function') {
                    beforeCall();
                }
                // callLoader();
            },
            complete: function() {
                // endLoader();
            },
            dataType: 'json',
            success: function(response) {
                resolve(response);
            },
            error: function(response) {
                reject(response);
            }
        });

    });
}