
/* ==== focused class for form controls ==== */
$(document).ready(function() {
    initFloatingLabel();
});
function initFloatingLabel(){
    $('.form-group input[type="text"], .form-group input[type="email"], .form-group input[type="password"], .form-group input[type="url"], .form-group select, .form-group textarea').each(function() {
        // Apply 'focused' class when the input is focused
        $(this).on('focus', function() {
            $(this).addClass('focused');
            console.log("class added")
        });

        // Remove 'focused' class if the field is empty and loses focus
        $(this).on('blur', function() {
            if ($(this).val() === '') {
                $(this).removeClass('focused');
            console.log("class removed")

            }
        });

        $(this).on('change', function() {
            if ($(this).val() === '') {
                $(this).removeClass('focused');
            console.log("class 1")

            }else{
                $(this).addClass('focused');
            console.log("class 2")

            }
        });

        // On page load, check if the field has a value
        if ($(this).val() !== '') {
            $(this).addClass('focused');  // Apply 'focused' class if field is not empty
            $(this).focus();               // Focus on the input field if it has a value
        }
    });
}
$(document).ready(function () {
    const dobInput = document.getElementById("dob");
    const datePicker = document.getElementById("datePicker");
    const monthYear = document.getElementById("monthYear");
    const dateGrid = document.getElementById("dateGrid");
    const prevMonthBtn = document.getElementById("prevMonth");
    const nextMonthBtn = document.getElementById("nextMonth");

    let selectedDate = null;
    let currentDate = new Date();

    // Toggle the date picker display
    $(dobInput).on("click", () => {
        $(datePicker).toggleClass("hidden");
        renderCalendar(currentDate);
    });

    // Close date picker if clicked outside
    $(document).on("click", (event) => {
        if (!$(datePicker).has(event.target).length && event.target !== dobInput) {
            $(datePicker).addClass("hidden");
        }
    });

    // Function to render calendar
    function renderCalendar(date) {
        $(dateGrid).empty(); // Clear previous grid
        $(monthYear).text(`${date.toLocaleString("default", { month: "long" })} ${date.getFullYear()}`);

        const year = date.getFullYear();
        const month = date.getMonth();

        // Get the first day of the month and the number of days in the month
        const firstDay = new Date(year, month, 1).getDay();
        const daysInMonth = new Date(year, month + 1, 0).getDate();

        // Fill in empty cells for the days before the first day of the month
        for (let i = 0; i < firstDay; i++) {
            const emptyCell = $("<div></div>");
            $(dateGrid).append(emptyCell);
        }

        // Add day cells
        for (let day = 1; day <= daysInMonth; day++) {
            const dayCell = $("<div></div>").addClass("date").text(day);

            if (selectedDate && day === selectedDate.getDate() && month === selectedDate.getMonth() && year === selectedDate.getFullYear()) {
                dayCell.addClass("selected");
            }

            dayCell.on("click", () => {
                selectedDate = new Date(year, month, day);
                $(dobInput).val(selectedDate.toLocaleDateString("en-CA"));
                $(datePicker).addClass("hidden");
            });

            $(dateGrid).append(dayCell);
        }
    }

    // Month navigation
    $(prevMonthBtn).on("click", () => {
        currentDate.setMonth(currentDate.getMonth() - 1);
        renderCalendar(currentDate);
    });

    $(nextMonthBtn).on("click", () => {
        currentDate.setMonth(currentDate.getMonth() + 1);
        renderCalendar(currentDate);
    });
});
    
    

// function initFloatingLabel(){
//     // $('.form-group input[type="text"], .form-group input[type="email"], .form-group input[type="password"], .form-group input[type="url"], .form-group select, .form-group textarea').each(function() {
//         $('.form-group input[type="text"], .form-group input[type="email"], .form-group input[type="password"], .form-group input[type="url"], .form-group select, .form-group textarea').each(function() {

//         // Apply 'focused' class when the input is focused
//         $(this).on('focus', function() {
//             $(this).addClass('focused');
//         });

//         // Remove 'focused' class if the field is empty and loses focus
//         $(this).on('blur', function() {
//             if ($(this).val() === '') {
//                 $(this).removeClass('focused');
//             }
//         });

//         $(this).on('change', function() {
//             if ($(this).val() === '') {
//                 $(this).removeClass('focused');
//             }else{
//                 $(this).addClass('focused');
//             }
//         });

//         // On page load, check if the field has a value
//         if ($(this).val() !== '') {
//             $(this).addClass('focused');  // Apply 'focused' class if field is not empty
//             $(this).focus();               // Focus on the input field if it has a value
//         }
//     });
// }
