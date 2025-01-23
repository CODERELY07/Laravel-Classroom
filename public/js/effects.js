$(document).ready(function(){

    /* toggle hide and show dropdown */
    function toggle(elemID){
        const element = `#${elemID}`;
        console.log(element);
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
    /* enrolled class dropdown  */
    
    /* show and hide nav links */
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

    /* Class Elipsis */
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
     /* Class Elipsis */

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
});
