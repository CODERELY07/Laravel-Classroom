import { initializeClassEllipsis } from './utils.js';

export function effectsFunctions(){
    initializeClassEllipsis();
    $(document).ready(function(){

        /* toggle hide and show dropdown */
        function toggle(elemID){
            const element = `#${elemID}`;
            $(document).on('click', element, function(e){
                e.stopPropagation();
                $('.toggle-content').not($(element).siblings('.toggle-content')).removeClass('active');
                $(element).siblings('.toggle-content').toggleClass('active');
            });
            $(document).on('click', function(){
                $('.toggle-content').removeClass('active');
            })
            $('.toggle-content').on('click', function(e){
                e.stopPropagation();
            })
        }
        toggle('add-class-btn');
        toggle('profile-class-btn');
        toggle('show-subject');
        toggle('bell-btn');
        toggle('create-classwork-btn');
    
        /* toggle hide and show dropdown */
    
        /* enrolled class dropdown  */
        $('#dropdown-class').on('click', function(){
            let classToggle = $(this).parent().toggleClass('active');
            if(classToggle.hasClass('active')){
                $(this).find('i').removeClass('fa-caret-right').addClass('fa-caret-down');
            }else{
                $(this).find('i').removeClass('fa-caret-down').addClass('fa-caret-right');
            }
            console.log($(this).parent().attr('class'));
        });
       
        $('#bar').on('click', function(){
           $('.nav').toggleClass('active');
        })
        /* show and hide nav links */
    
        // open modal and close modal
        $('.show-modal').on('click', function(){
            const targetModal = $(this).data('target');
            $(targetModal).modal('show');
        })
        $('.close-modal').on('click', function () {
            const targetModal = $(this).data('target');
            $(targetModal).modal('hide');
        });
    
         const savedTab = localStorage.getItem('activeTab');
         if (savedTab) {
             // Activate the saved tab
             $('.tab-button').removeClass('active');
             $('.tab-content').removeClass('active').css('opacity', 0); 
             $(`.tab-button[data-tab="${savedTab}"]`).addClass('active');
             $(`#${savedTab}`).addClass('active').css('opacity', 1); 
         }
     
         $('.tab-button').click(function () {
             const tabId = $(this).data('tab');
     
             // Only handle tab switches without showing the loading indicator
             $('.tab-button').removeClass('active');
             $('.tab-content').removeClass('active').css('opacity', 0); 
             $(this).addClass('active');
             $(`#${tabId}`).addClass('active').css('opacity', 1); 
     
             // Save the current active tab to localStorage
             localStorage.setItem('activeTab', tabId);
         });

        //  Unenroll students
        $('.unenroll-checkbox').on('change', function(){
            let checkboxes = $('.unenroll-checkbox:checked');

            if(checkboxes.length > 0){
                $('#unenroll-selected').removeClass('d-none').addClass('d-block');
            }else{
                $('#unenroll-selected').removeClass('d-block').addClass('d-none');
            }
        });

        $('#unenroll-selected').on('click', function (e) {
            e.preventDefault();
    
            let checkboxes = $('.unenroll-checkbox:checked');
    
            if (checkboxes.length === 0) {
                alert('Please select at least one student to remove.');
                return;
            }else{
                $(this).removeClass('d-none').addClass('d-block');
            }
    
            if (confirm('Are you sure you want to remove the selected students?')) {
                $('#unenroll-form').submit();
            }
        });

        // Materials Create toggles
        $(".toggle-classwork").on("click", function () {
            const target = $(this).data("target");
            $(".remove-toggle").addClass("d-none").removeClass("d-block");
            $(target).removeClass("d-none").addClass("d-block");
        });

        // Initialize an array to store added users' IDs
        let addedUserIds = [];

        $('#userSearchInput').on('input', function() {
            const data = $(this).val();  
            let url = $(this).parent().parent().attr('action');
           
            if (data.length > 0) {
                $.ajax({
                    url: url, 
                    method: "GET",
                    data: { data: data },
                    success: function(response) {
                        $('#searchResults').empty();
                        console.log(response.data);

                        if (response.data.length > 0) {
                            // Filter out the users that are already added
                            let filteredUsers = response.data.filter(function(user) {
                                return !addedUserIds.includes(user.id);
                            });

                            if (filteredUsers.length > 0) {
                                filteredUsers.forEach(function(user) {
                                    $('#searchResults').append(
                                        '<div class="d-flex justify-content-between align-items-center mb-2">' +
                                            '<p>' + user.email + '</p>' +
                                            '<p class="btn-sm add-user cursor-pointer" data-id="' + user.id + '" data-email="' + user.email + '">' +
                                                '<i class="fa-solid fa-user-plus"></i>' +
                                            '</p>' +
                                        '</div>'
                                    );
                                });
                            } else {
                                $('#searchResults').append('<p>No users found</p>');
                            }
                        } else {
                            $('#searchResults').append('<p>No users found</p>');
                        }
                    }
                });
            } else {
                $('#searchResults').empty();
            }
        });

        $('#searchResults').on('click', '.add-user', function() {
            var userId = $(this).data('id');
            var email = $(this).data('email');

            addedUserIds.push(userId);

            $('#addedUsersList').append(
                '<li class="my-2 d-flex justify-content-between align-items-center">' +
                    '<p>' + email + '</p>' +
                    '<p class="btn-sm remove-user cursor-pointer" data-id="' + userId + '">' +
                        '<i class="fa-solid fa-user-minus"></i>' +
                    '</p>' +
                '</li>'
            );

            $(this).parent().remove();
        });

        $('#addedUsersList').on('click', '.remove-user', function() {
            var userId = $(this).data('id');
            addedUserIds = addedUserIds.filter(function(id) {
                return id !== userId;
            });


            $(this).parent().remove();
        });


    // save users
    $('#userSearch').on('submit', function(e) {
        e.preventDefault(); 
    
        $('#userSearch input[name="added_users[]"]').remove();
    
        $('#addedUsersList li').each(function() {
            var userId = $(this).find('.remove-user').data('id');
            $('<input>', {
                type: 'hidden',
                name: 'added_users[]', 
                value: userId
            }).appendTo('#userSearch'); 
        });
    
        const url = $(this).attr('action');
        console.log(url); 
    
        var formData = new FormData(this);
    
        // Perform the AJAX request
        $.ajax({
            url: '/stream/save',  
            method: "POST",
            data: formData, 
            processData: false,  
            contentType: false,  
            success: function(response) {
                if(response.success){
                    window.location.href = response.redirectUrl;
                } 
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
                console.log(response);
            }
        });

    });

    // question generate inputs
        function generateInputs(count) {
            let inputContainer = $("#questionInputs");
            inputContainer.empty(); 
            for (let i = 1; i <= count; i++) {
                inputContainer.append(`
                    <div class="mt-2 d-flex gap-1 align-items-center">
                    <label for="question${i}" >${i}.</label>
                    <input type="text" class="form-control" name="question[]" id="question${i}" placeholder="Question ${i}">
                    </div>
                `);
            }
        }
        generateInputs(5);
        $("#question-items").on("input", function () {
            let selectedValue = $(this).val();
            generateInputs(selectedValue);
        });
    });    
}