
$(document).ready(function(){
    // auth Login and Signin
    function AuthAjax(formId){
        $(`#${formId}`).submit(function(e){
            e.preventDefault();
            const url = $(this).attr('action');
            const formData = new FormData(this);
            
            $.ajax({
                type: 'POST',
                url: url,
                data: formData,
                contentType: false,
                processData:false,
                success: (response) =>{
                    let MsgElement = $(`#${formId}`).siblings('.msg');
                    if (response.success) {
                        if(response.redirectUrl){
                            MsgElement.text(response.success).addClass('alert-success').removeClass('alert-danger').show(); 
                            window.location.href = response.redirectUrl;
                            setTimeout(function(){
                                MsgElement.removeClass('alert-success').hide(); 
                            }, 3000)
                        }
                        $(`#${formId}`)[0].reset();
                    } else {
                        MsgElement.text(response.error).addClass('alert-danger').removeClass('alert-success').show();
                    }
                    $(`#${formId}`).find('.text-danger').text('');
                },
                error: function(response){
                    $(`#${formId}`).find('.text-danger').text('');
                    $.each(response.responseJSON.errors, function(key, value){
                        let inputField = $(`#${formId}`).find(`[name = "${key}"]`);
                        inputField.after(`<span class="text-danger"> ${value[0]} </span>`);
                    });
                }
            })
        });
    }
    AuthAjax('signinForm');
    AuthAjax('signupForm');
    
    
})
