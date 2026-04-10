/* -- dropdown sidebar -- */
// function toggleDropdown() {
//     var dropdownContent = document.getElementById("dropdownContent");
//     const arrow = document.querySelector(".arrow");    
//     // Toggle the "show" class on the dropdown content
//     dropdownContent.classList.toggle("show");
//     // Toggle the "rotate" class on the arrow
//     arrow.classList.toggle("rotate");
// }

$(document).ready(function () {
    $(".navbar-toggler").on("click", function () {
        $("#sidebar").toggleClass("open");
    });

    $(".close-btn").on("click", function () {
        $("#sidebar").removeClass("open");
    });
});
$(document).ready(function() {
    initSelect();
    $('.cust-select').select2();    
});



// dropdown //
$(document).ready(function(){
    $('.dropdown-toggle').on('click', function(e) {
        e.preventDefault();
        var dropdownMenu = $(this).siblings('.dropdown-menu');
        
        // Close any other open dropdowns
        $('.dropdown-menu').not(dropdownMenu).slideUp();

        // Toggle the current dropdown
        dropdownMenu.slideToggle();
    });

    // Close the dropdown if clicking outside of it
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.dropdown').length) {
            $('.dropdown-menu').slideUp();
        }
    });
});