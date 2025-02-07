// utils.js
export function initializeClassEllipsis() {
    $(document).ready(function () {
        const $classEllipsis = $('.class-ellipsis');
        const $toggleContents = $('.toggle-content');
    
        function removeActiveClasses() {
            $toggleContents.removeClass('active');
        }
    
        $classEllipsis.on('click', function (e) {
            e.stopPropagation();
            
            $toggleContents.not($(this).siblings('.toggle-content')).removeClass('active');
            $(this).siblings('.toggle-content').toggleClass('active');
        });
    
        $(document).on('click', function () {
            removeActiveClasses();
        });
    
        $toggleContents.on('click', function (e) {
            e.stopPropagation();
        });
    });    
}

export function initializeShowModal(){
    // open modal and close modal
    $('.show-modal').on('click', function(){
        const targetModal = $(this).data('target');
        $(targetModal).modal('show');
    })
    
    $('.close-modal').on('click', function () {
        const targetModal = $(this).data('target');
        $(targetModal).modal('hide');
    });
}
export function editClass(){
    $('.edit-class').on('click', function(){
        const id = $(this).data('id');
        console.log(id)
        const classname = $(this).data('classname');
        const section = $(this).data('section');
        const subject = $(this).data('subject');

        $('#editClassname').val(classname);
        $('#editSection').val(section);
        $('#editSubject').val(subject);
        
        $('#editClassForm').attr('action', `/updateClass/${id}`); 
    })
}
export function confirmClass(classModalElem,classModalID,url){
    $(`.${classModalElem}`).on('click', function(){
        const id = $(this).data('id');
        console.log(id)
        $(`#${classModalID}`).attr('action', `/${url}/${id}`)
    })
}

// display the new profile picture before upload
changeProfile()
function changeProfile(){
    $('#profile_picture').change(function(event) {
        const file = event.target.files[0]; 
        if (file) {
            const reader = new FileReader();

            reader.onload = function(e) {
                
                $('#profile-change').css('background-image', `url(${e.target.result})`); 
                $('#profile-change').css('background-size', 'cover'); 
                $('#profile-change').css('background-position', 'center');
                $('#profile-change span').text('');
                $('#profile-change img').attr('src', e.target.result);
            }
      
            reader.readAsDataURL(file); 
            if($('#profileSaveButton').length === 0){
                $('#profileButtons').after(`<button id="profileSaveButton" type="submit" class="btn btn-primary w-100 mt-2">Save</button>`);
            }
            
        }
    });

    /* Toggle show and hide */
    $(document).on('click', '[data-toggle-btn]', function (e) {
        e.stopPropagation();
        const content = $(this).siblings('.toggle-content');
        $('.toggle-content').not(content).removeClass('active');
        content.toggleClass('active');
    });
    
    $(document).on('click', function () {
        $('.toggle-content').removeClass('active');
    });
    
    $('.toggle-content').on('click', function (e) {
        e.stopPropagation();
    });
    /* Toggle show and hide */
}