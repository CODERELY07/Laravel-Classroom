// utils.js
export function initializeClassEllipsis() {
    const classEllipsis = document.querySelectorAll('.class-card .class-ellipsis');
    const toggleContents = document.querySelectorAll('.class-card .toggle-content');

    function removeActiveClasses() {
        toggleContents.forEach((content) => {
            content.classList.remove('active');
        });
    }

    classEllipsis.forEach((btn) => {
        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            console.log("hi");
            toggleContents.forEach((content) => {
                if (content !== btn.parentElement.querySelector('.toggle-content')) {
                    content.classList.remove('active');
                }
            });

            const toggleContent = btn.parentElement.querySelector('.toggle-content');
            if (toggleContent) {
                toggleContent.classList.toggle('active');
            }
        });
    });

    document.addEventListener('click', function () {
        removeActiveClasses();
    });

    toggleContents.forEach((content) => {
        content.addEventListener('click', function (e) {
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

// display the new profile picture before upload or submited
changeProfile()
function changeProfile(){
    $('#profile_picture').change(function(event) {
        const file = event.target.files[0]; // Get the selected file
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
}