import { initializeClassEllipsis, initializeShowModal, editClass, confirmClass } from './utils.js';

export function authFunctions() {
    $(document).ready(function () {
        // Auth Signin, Signup, Forgot Password, createclass,editClass
        handleRequest('signinForm', 'Signing in...');
        handleRequest('signupForm', 'Signing up...');
        handleRequest('forgetPassword', 'Sending Please Wait...');
        handleRequest('forgetPasswordLink', 'Loading Please Wait...', 2000);
        handleRequest('createClass', 'Creating Please Wait...', 1000, false);
        handleRequest('editClassForm', 'Editing Please Wait...', 1000, false);
        handleRequest('joinClassForm', 'Joining Please Wait...', 1000, false);
        handleRequest('changeName', 'Changing Please Wait...', 1000, false);
        handleRequest('changePassword', 'Changing Please Wait...', 1000, false);
        handleRequest('createquestionForm', 'Creating Please Wait...', 2000, false);

        // Fetch classes once for both teacher and archive
        fetchAllClasses();

        // Archive, delete, unarchive
        confirmClassAction('archiveClassModalID');
        confirmClassAction('unarchiveClassModalID');
        confirmClassAction('deleteClassModalID');
        confirmClassAction('unenrollClassModalID');
        confirmClassAction('changeProfile');
        confirmClassAction('unenroll-form');
        


        // Handle form submissions with AJAX
        function handleRequest(formId, beforeText, timeout = 1000, isDisable = true) {
            $(`#${formId}`).submit(function (e) {
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
                    processData: false,
                    beforeSend: function () {
                        if (isDisable) {
                            button.prop('disabled', true).text(beforeText);
                        }
                    },
                    success: (response) => {
                        let MsgElement = $(`#${formId}`).siblings('.msg');
                        // console.log(response.error);
                        if (response.success) {
                            button.text('Done');
                            MsgElement.text(response.success).addClass('alert-success').removeClass('alert-danger').show();
                            setTimeout(function () {
                                MsgElement.removeClass('alert-success').hide();
                            }, 3000);
                            // console.log(response.redirectUrl);
                            if (response.redirectUrl) {
                              
                                $(button).css({ backgroundColor: 'transparent', color: '#000000b3' });
                                $(button).text(beforeText);
                                setTimeout(() => {
                                    window.location.href = response.redirectUrl;
                                    button.css({ backgroundColor: originalStyles.backgroundColor, color: originalStyles.color });
                                    button.text(originalStyles.text);
                                }, timeout);
                            }
                            fetchAllClasses(); 
                        } else {
                            MsgElement.text(response.error).addClass('alert-danger').removeClass('alert-success').show();
                            button.prop('disabled', false).text(originalStyles.text);
                            setTimeout(function () {
                                MsgElement.removeClass('alert-danger').hide();
                            }, 3000);
                            console.log(response.error);
                        }
                        $(`#${formId}`)[0].reset();
                        $(`#${formId}`).find('.text-danger').text('');
                    },
                    error: function (response) {
                        $(`#${formId}`).find('.text-danger').text('');
                        $.each(response.responseJSON.errors, function (key, value) {
                            let inputField = $(`#${formId}`).find(`[name="${key}"]`);
                            inputField.after(`<span class="text-danger"> ${value[0]} </span>`);
                        });
                        button.prop('disabled', false).text(originalStyles.text);
                        console.log(response.responseJSON);
                    }
                });
            });
        }

        // Fetch classes for both teacher and archive in a single function
        function fetchAllClasses() {
            const requests = [
                { url: teacherClassesUrl, elementID: 'classessParent' },
                { url: achiveClassesUrl, elementID: 'archiveParent' }
            ];

            requests.forEach(request => {
                $.ajax({
                    url: request.url,
                    method: 'GET',
                    success: function (data) {
                        if (data.classess.length === 0) {
                            $(`#${request.elementID}`).removeClass('sub-continer d-flex flex-wrap gap-4');
                        } else {
                            $(`#${request.elementID}`).addClass('sub-continer d-flex flex-wrap gap-4');
                        }
                        $(`#${request.elementID}`).html(data.html);
                        if (request.elementID === 'archiveParent') {
                            initializeClassEllipsis();
                        }
                        initializeShowModal();
                        editClass();
                        confirmClass('archive-class','archiveClassModalID','archiveClass');
                        confirmClass('unarchive-class','unarchiveClassModalID','unarchiveClass');
                        confirmClass('delete-class','deleteClassModalID','destroyClass');
                        confirmClass('unenroll-class','unenrollClassModalID','unenrollClass');
                    },
                    error: function (response) {
                        console.log(response.responseJSON);
                    }
                });
            });
        }
        

        // Confirm actions for class archive
        function confirmClassAction(classModalID) {
            $(`#${classModalID}`).submit(function (e) {
                e.preventDefault();
                const url = $(this).attr('action');
                const formData = new FormData(this);

                $.ajax({
                    url: url,
                    method: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function (response) {
                        const msgElement = $('.main-alert-msg');
                        if (response.success) {
                            // reinistialize
                            fetchAllClasses(); 
                            initializeClassEllipsis();
                            alert(response.success);
                            if(response.redirectUrl){
                                window.location.href = response.redirectUrl;
                            }
                        }else if(response.error){
                            alert(response.error);
                        } else {
                            alert('Something went wrong. Please try again!');
                        }
                        setTimeout(function () {
                            msgElement.removeClass('alert-success').hide();
                        }, 3000);

                        $('.close-modal').on('click', function () {
                            const targetModal = $(this).data('target');
                            $(targetModal).modal('hide');
                        });
                    },
                    error: function (response) {
                        console.log(response.responseJSON);
                    }
                });
            });
        }
    });
}
