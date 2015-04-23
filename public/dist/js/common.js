$( document ).ready(function() {
    $('.plus-holder').click(function() {
       var email = $('.email').clone();
       email.find('.plus-email').removeClass('glyphicon-plus');
       email.find('.plus-email').addClass('glyphicon-minus');
       email.removeClass('email');
       email.addClass('added-email');
       email.find('.holder').removeClass('plus-holder');
       email.find('.holder').addClass('min-holder');
       $('.email-box').append(email); 
    });
    
    $(document).on("click", ".min-holder", function () {
   
     $(this).parent().remove();
    });
});