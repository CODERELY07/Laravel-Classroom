
$(document).ready(function(){
    // auth Login and Signin
    function AuthAjax(formId,beforeText){
        $(`#${formId}`).submit(function(e){
            e.preventDefault();
            const url = $(this).attr('action');
            const formData = new FormData(this);
            
            const button = $(this).find('.btn');
            const originalStyles = {
                backgroundColor: button.css('backgroundColor'),                                
                color: button.css('color'),
                text: button.text()
            };  

            $.ajax({
                type: 'POST',
                url: url,
                data: formData,
                contentType: false,
                processData:false,
                beforeSend: function() {
                    button.prop('disabled', true).text(beforeText);
                },
                success: (response) =>{
                    button.text('Done');
                    let MsgElement = $(`#${formId}`).siblings('.msg');
                    if (response.success) {
                        MsgElement.text(response.success).addClass('alert-success').removeClass('alert-danger').show(); 
                        setTimeout(function(){
                            MsgElement.removeClass('alert-success').hide(); 
                        }, 5000)
                        if(response.redirectUrl){
                            $(button).css({backgroundColor:'transparent', color:'#000000b3'});
                            $(button).text(beforeText)
                            setTimeout(()=>{
                                window.location.href = response.redirectUrl;
                                button.css({backgroundColor: originalStyles.backgroundColor, color: originalStyles.color});
                                button.text(originalStyles.text);
                            }, 5000);
                        }
                        $(`#${formId}`)[0].reset();
                    }else {
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
                    button.prop('disabled', false).text(originalStyles.text);
                }
            })
        });
    }
    AuthAjax('signinForm', 'Signing in...');
    AuthAjax('signupForm', 'Signing up...');
    AuthAjax('forgetPassword', 'Sending Please Wait...');
    AuthAjax('forgetPasswordLink', 'Loading Please Wait...');
    
    
})
