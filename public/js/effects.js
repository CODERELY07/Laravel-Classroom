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

    // open modal
    // $('#create').on('click', function(){
    //     $('#createClassModal').modal('show');
    // })
    // $('#join').on('click', function(){
    //     $('#joinClassModal').modal('show');
    // })
    $('.show-modal').on('click', function(){
        const targetModal = $(this).data('target');
        $(targetModal).modal('show');
    })
    $('.close-modal').on('click', function () {
        const targetModal = $(this).data('target');
        $(targetModal).modal('hide');
    });

});
