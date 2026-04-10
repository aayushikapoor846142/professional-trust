/*!
 * Start Bootstrap - SB Admin v7.0.7 (https://startbootstrap.com/template/sb-admin)
 * Copyright 2013-2023 Start Bootstrap
 * Licensed under MIT (https://github.com/StartBootstrap/startbootstrap-sb-admin/blob/master/LICENSE)
 */
//
// Scripts
//

window.addEventListener("DOMContentLoaded", (event) => {
    initFrontPhoneNo();
    // Toggle the side navigation
    const sidebarToggle = document.body.querySelector("#sidebarToggle");
    if (sidebarToggle) {
        // Uncomment Below to persist sidebar toggle between refreshes
        // if (localStorage.getItem('sb|sidebar-toggle') === 'true') {
        //     document.body.classList.toggle('sb-sidenav-toggled');
        // }
        sidebarToggle.addEventListener("click", (event) => {
            event.preventDefault();
            document.body.classList.toggle("sb-sidenav-toggled");
            localStorage.setItem(
                "sb|sidebar-toggle",
                document.body.classList.contains("sb-sidenav-toggled")
            );
        });
    }
});

function isHTML(content) {
    // Regular expression to check for HTML tags
    var htmlPattern = /<\/?[a-z][\s\S]*>/i;

    return htmlPattern.test(content);
}

/* tool tip function call */
$(function () {
    $('[data-toggle="tooltip"]').tooltip();
});
/* # tool tip function call */

function contentReadingTime(content) {
    var text;
    if (isHTML(content)) {
        text = jQuery(content).text();
    } else {
        text = content;
    }
    var readingSpeed = 200; // Average reading speed (words per minute)

    const words = text.trim().split(/\s+/).length; // Count words in the text
    const minutes = Math.ceil(words / readingSpeed); // Calculate minutes (round up)
    return `${minutes} minute${minutes > 1 ? "s" : ""} read`;
}
// function showPopupNew(url, method = "get", paramters = {},showFull = false) {
//     $.ajax({
//         url: url + "?_token=" + csrf_token,
//         dataType: "json",
//         type: method,
//         data: paramters,
//         beforeSend: function () {
//             showLoader();
//             // var html = "<div class='popup-loader'><i class='fa fa-spin fa-spinner fa-2x'></i></div>"
//             // $("#popupModal").html(html);
//         },
//         success: function (result) {
//             hideLoader();
//             if (result.status == true) {
//                 $("#popupModal").html(result.contents);
//                 const modal = document.getElementById("popupModal");
//                 const modalContent = document.querySelector(".modal-content");
//                 const openModalBtn = document.getElementById("openModal");
//                 const closeModalBtns = document.querySelectorAll(".cdsTYDashboard-modal-close-btn, .cdsTYDashboard-modal-cancel-btn, .btn-close");
//                 const collapseBtn = document.querySelector(".cdsTYDashboard-modal-collapse-btn");

//                 // Open Modal (Fullscreen mode by default)
//                 if(showFull){
//                     modal.classList.remove("cdsTYDashboard-standard-side-panel", "hide");
//                 }else{
//                     modal.classList.remove("hide");
//                     modal.classList.add("cdsTYDashboard-standard-side-panel");
//                 }
                
//                 modal.classList.add("show");
//                 modal.style.display = "flex";  // Ensure display is set

//                 // Close Modal with smooth fade-out
//                 function closeModal() {
//                     modal.classList.remove("show");
//                     modal.classList.add("hide");

//                     // Wait for animation to complete before hiding
//                     setTimeout(() => {
//                         modal.style.display = "none";
//                     }, 300);
//                 }

//                 // Close Modal (Click on Close Button or Outside)
//                 closeModalBtns.forEach(btn => {
//                     btn.addEventListener("click", closeModal);
//                 });

//                 // window.addEventListener("click", (event) => {
//                 //     if (event.target === modal) {
//                 //         closeModal();
//                 //     }
//                 // });

//                 // Toggle Side Panel Mode
//                 collapseBtn.addEventListener("click", () => {
//                     modal.classList.toggle("cdsTYDashboard-standard-side-panel");
//                 });
//                 initFloatingLabel();
//             } else {
//                 if (result.message != undefined) {
//                     errorMessage(result.message);
//                 } else {
//                     errorMessage("No Modal Data found");
//                 }
//             }
//         },
//         complete: function () {
//             hideLoader();
//         },
//         error: function () {
//             hideLoader();
//             internalError();
//         },
//     });
// }
function showPopup(url, method = "get", paramters = {}) {
    $.ajax({
        url: url ,
        dataType: "json",
        type: method,
        data: paramters,
        beforeSend: function () {
            showLoader();
            // var html = "<div class='popup-loader'><i class='fa fa-spin fa-spinner fa-2x'></i></div>"
            // $("#popupModal").html(html);
        },
        success: function (result) {
            hideLoader();
            if (result.status == true) {
                $("#popupModal").html(result.contents);
               
                initFloatingLabel();
                $("#popupModal").modal("show");
                setTimeout(() => {
                    initSelect();
                }, 1100);
            } else {
                if (result.message != undefined) {
                    errorMessage(result.message);
                } else {
                    errorMessage("No Modal Data found");
                }
            }
        },
        complete: function () {
            hideLoader();
        },
        error: function () {
            hideLoader();
            internalError();
        },
    });
}

function closeModal() {
    $("#popupModal").html("");
    $("#popupModal").modal("hide");
}

function internalError() {
    hideLoader();
    warningMessage("Something went wrong. Try again!");
}
function validateDigit(input) {
    // Remove any non-digit character
    input.value = input.value.replace(/[^0-9]/g, "");

    // Limit input length to 8 characters
    if (input.value.length > 8) {
        input.value = input.value.substring(0, 8);
    }
}
function validateNumber(input) {
    // Remove any non-digit character
    input.value = input.value.replace(/[^0-9]/g, "");
}
function initPhoneNo(e) {
    var iti = intlTelInput(e, {
        autoHideDialCode: false,
        autoPlaceholder: "aggressive",
        initialCountry: "ca",
        strictMode: true,
        separateDialCode: true,
        preferredCountries: ["ru", "th"],
        customPlaceholder: function (
            selectedCountryPlaceholder,
            selectedCountryData
        ) {
            return "" + selectedCountryPlaceholder.replace(/[0-9]/g, "X");
        },
        geoIpLookup: function (callback) {
            $.get("https://ipinfo.io", function () {}, "jsonp").always(
                function (resp) {
                    var countryCode = resp && resp.country ? resp.country : "";
                    callback(countryCode);
                }
            );
        },
        utilsScript:
            "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/16.0.0/js/utils.js", // just for
    });
}

function validation(errors) {
    $(".invalid-feedback").remove();
    $(".form-control").removeClass("is-invalid");
    $(".custom-select").removeClass("is-invalid");

    $.each(errors, function (index, value) {
        let element = $("*[name=" + index + "]");
        if (element.length === 0) {
            // Skip to the next iteration if the element is not found
            return true;
        }

        element.parents(".js-form-message").find(".invalid-feedback").remove();
        element
            .parents(".js-form-message")
            .find(".form-control")
            .removeClass("is-invalid");
        var html =
            '<div id="' +
            index +
            '-error" class="invalid-feedback required-error">' +
            value +
            "</div>";

        // Find the form-group div and append the error message after it
        var formGroup = element.parents(".form-group").find(".input-group");
        console.log(formGroup);

        if (formGroup.length) {
            var html =
                '<div class="invalid-feedback d-block">' + value + "</div>";
            formGroup.after(html); // Insert the error message after the form-group div
        } else {
            console.log("Form group not found for:", index);
        }

        if (
            element.get(0).tagName === "SELECT" ||
            element.hasClass("select2-hidden-accessible")
        ) {
            // Adjust for select2 elements
            if (element.hasClass("select2-hidden-accessible")) {
                // For select2, append error after the select2 container
                element.parents(".js-form-message").append(html);
                element
                    .next(".select2")
                    .find(".select2-selection")
                    .addClass("is-invalid");
            } else {
                element.parents(".js-form-message").append(html);
                element.addClass("is-invalid");
            }
        } else {
            if (element.hasClass("editor")) {
                element.parents(".js-form-message").append(html);
            } else if (formGroup.length == 0) {
                element.parents(".js-form-message").append(html);
            }
        }
        element
            .parents(".js-form-message")
            .find(".form-control")
            .addClass("is-invalid");
    });
}

function validateURLInput(input) {
    // Define a regular expression to match URLs
    var urlPattern =
        /^(https?:\/\/)?([\da-z.-]+)\.([a-z.]{2,6})([/\w .-]*)*\/?$/;
    // Get the error message element
    var errorMsg = document.getElementById("urlError");
    // Check if the input matches the URL pattern
    if (input.value) {
        if (!urlPattern.test(input.value)) {
            // If it doesn't match, highlight the input and show an error message
            input.style.borderColor = "red"; // Change border color to red for invalid input
            errorMsg.style.display = "block"; // Show the error message
            errorMsg.textContent = "Please enter a valid URL."; // Set the error message
        } else {
            // If it's valid, reset the input and hide the error message
            input.style.borderColor = ""; // Reset border color for valid input
            errorMsg.style.display = "none"; // Hide the error message
        }
    } else {
        // If the input is empty, reset the border and hide the error message
        input.style.borderColor = "";
        errorMsg.style.display = "none"; // Hide the error message if empty
    }
}

function validateEmail(email) {
    // Simple email regex pattern
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    // Test if the input is a valid email
    return emailRegex.test(email);
}

function validateNoHtmlOrSpecialChars(input) {
    // Regex to block HTML tags and special characters
    // var regex = /<[^>]*>|[\'^£$%&*()}{@#~?><>|=+¬]/;
    var regex = /<[^>]*>/;
    // validate if the input contains any unwanted characters
    return !regex.test(input);
}
function validateStringandNumber(input) {
    // Allow letters, numbers, and spaces only
    input.value = input.value.replace(/[^a-zA-Z0-9\s]/g, "");
}

function validateStringWithDot(input) {
    // Allow letters, spaces, and dots only
    input.value = input.value.replace(/[^a-zA-Z.\s]/g, "");
}

function removeSpaces(input) {
    // Remove spaces from the input value
    input.value = input.value.replace(/\s/g, "");
}

function showLoader() {
    var confettiAnimation = lottie.loadAnimation({
        container: document.getElementById('lottie-loader'),
        renderer: 'svg',
        loop: false,
        autoplay: false,
        path: SITEURL + '/assets/plugins/animation/main-loader.json' // Make sure this path is correct
    });
    confettiAnimation.goToAndPlay(0, true);
    setTimeout(() => {
        confettiAnimation.goToAndPlay(0, true);
        hideLoader();
    }, 1500);
    $(".loader").show();
}

function validatePhoneInput(input) {
    // Replace any character that is not a digit with an empty string
    input.value = input.value.replace(/[^0-9]/g, "");

    // Check if the length of the input exceeds 15 digits
    if (input.value.length > 15) {
        input.value = input.value.substring(0, 15); // Limit the input to max 15 digits
    }
}

function hideLoader() {
    $(".loader").hide();
}

function redirect(url) {
    window.location.href = url;
}

function onlyNumberKey(evt) {
    // Only ASCII character in that range allowed
    var ASCIICode = evt.which ? evt.which : evt.keyCode;
    if (ASCIICode > 31 && (ASCIICode < 48 || ASCIICode > 57)) return false;
    return true;
}

function confirmAction(e) {
    var url = $(e).attr("data-href");
    Swal.fire({
        title: "Are you sure to delete?",
        text: "You won't be able to revert this!",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes",
        confirmButtonClass: "btn btn-primary",
        cancelButtonClass: "btn btn-danger ml-1",
        buttonsStyling: false,
    }).then(function (result) {
        if (result.value) {
            redirect(url);
        }
    });
}
function confirmAnyAction(e) {
    var url = $(e).attr("data-href");
    var action = $(e).attr("data-action");

    Swal.fire({
        title: "Are you sure to " + action + "?",
        // text: "You won't be able to revert this!",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#198754",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes",
        confirmButtonClass: "btn btn-success",
        cancelButtonClass: "btn btn-danger ml-1",
        buttonsStyling: false,
    }).then(function (result) {
        if (result.value) {
            redirect(url);
        }
    });
}

function confirmPaymentMethod(e) {
    var url = $(e).attr("data-href");
    Swal.fire({
        title: "Are you sure to make this Default Payment Method?",
        text: "You won't be able to revert this!",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes",
        confirmButtonClass: "btn btn-primary",
        cancelButtonClass: "btn btn-danger ml-1",
        buttonsStyling: false,
    }).then(function (result) {
        if (result.value) {
            redirect(url);
        }
    });
}

function removePaymentMethod(e) {
    var url = $(e).attr("data-href");
    Swal.fire({
        title: "Are you sure to Remove this Payment Method?",
        text: "You won't be able to revert this!",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes",
        confirmButtonClass: "btn btn-primary",
        cancelButtonClass: "btn btn-danger ml-1",
        buttonsStyling: false,
    }).then(function (result) {
        if (result.value) {
            redirect(url);
        }
    });
}

function errorMessage(message) {
    toastr.error(message, "Error");
}

function successMessage(message) {
    toastr.success(message, "Success");
}

function warningMessage(message) {
    toastr.warning(message, "Warning");
}

function deleteMultiple(e) {
    var url = $(e).attr("data-href");
    if ($(".row-checkbox:checked").length <= 0) {
        warningMessage("No records selected to delete");
        return false;
    }
    Swal.fire({
        title: "Are you sure to delete?",
        text: "You won't be able to revert this!",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes",
        confirmButtonClass: "btn btn-primary m-2",
        cancelButtonClass: "btn btn-danger",
        buttonsStyling: false,
    }).then(function (result) {
        if (result.value) {
            if ($(".row-checkbox:checked").length <= 0) {
                warningMessage("No records selected to delete");
                return false;
            }
            var row_ids = [];
            $(".row-checkbox:checked").each(function () {
                row_ids.push($(this).val());
            });
            var ids = row_ids.join(",");
            $.ajax({
                type: "POST",
                url: url,
                data: {
                    _token: csrf_token,
                    ids: ids,
                },
                dataType: "json",
                beforeSend: function () {},
                success: function (response) {
                    if (response.status == true) {
                        location.reload();
                    } else {
                        errorMessage(response.message);
                    }
                },
                error: function () {
                    internalError();
                },
            });
        }
    });
}

$("#togglePassword").on("click", function () {
    const passwordField = $("#password");
    const icon = $("#iconPassword");
    if (passwordField.attr("type") === "password") {
        passwordField.attr("type", "text");
        icon.removeClass("fa-eye-slash").addClass("fa-eye");
    } else {
        passwordField.attr("type", "password");
        icon.removeClass("fa-eye").addClass("fa-eye-slash");
    }
});

$("#togglePasswordConfirm").on("click", function () {
    const passwordConfirmField = $("#password_confirmation");
    const icon = $("#iconPasswordConfirm");
    if (passwordConfirmField.attr("type") === "password") {
        passwordConfirmField.attr("type", "text");
        icon.removeClass("fa-eye-slash").addClass("fa-eye");
    } else {
        passwordConfirmField.attr("type", "password");
        icon.removeClass("fa-eye").addClass("fa-eye-slash");
    }
});
initSelect();
function initSelect2(){

    // Single Select initialize

    document.querySelectorAll('.CDSComponents-SingleSelect').forEach(select => {
        if (select.dataset.enhanced || select.multiple) return; // Skip already enhanced or multiple select
        select.dataset.enhanced = true;

        const wrapper = document.createElement('div');
        wrapper.className = 'CDSComponents-SingleSelect-wrapper';

        const display = document.createElement('div');
        display.className = 'CDSComponents-SingleSelect-display';
        display.setAttribute('tabindex', '0');
        display.textContent = select.options[select.selectedIndex]?.text || '-- Select an option --';

        const dropdown = document.createElement('div');
        dropdown.className = 'CDSComponents-SingleSelect-dropdown';

        const search = document.createElement('input');
        search.className = 'CDSComponents-SingleSelect-search';
        search.placeholder = 'Search...';

        dropdown.appendChild(search);
        wrapper.appendChild(display);
        wrapper.appendChild(dropdown);
        select.parentNode.insertBefore(wrapper, select);
        wrapper.appendChild(select);
        select.classList.add('CDSComponents-hidden');

        let optionsElements = [];
        let activeIndex = -1;

        function renderOptions() {
            dropdown.querySelectorAll('.CDSComponents-SingleSelect-option').forEach(el => el.remove());

            optionsElements = Array.from(select.options)
                .filter(opt => opt.value !== '')
                .map(option => {
                    const div = document.createElement('div');
                    div.className = 'CDSComponents-SingleSelect-option';
                    div.textContent = option.text;
                    div.dataset.value = option.value;

                    div.addEventListener('click', () => {
                        select.value = option.value;
                        display.textContent = option.text;
                        dropdown.style.display = 'none';
                        activeIndex = -1;
                    });

                    dropdown.appendChild(div);
                    return div;
                });
        }

        function highlightOption(index) {
            optionsElements.forEach((el, i) => {
                el.classList.toggle('active', i === index);
            });
            if (optionsElements[index]) {
                optionsElements[index].scrollIntoView({
                    block: 'nearest'
                });
            }
        }

        display.addEventListener('click', () => {
            dropdown.style.display = 'block';
            search.focus();
        });

        document.addEventListener('click', (e) => {
            if (!wrapper.contains(e.target)) {
                dropdown.style.display = 'none';
                activeIndex = -1;
            }
        });

        search.addEventListener('input', () => {
            const term = search.value.toLowerCase();
            optionsElements.forEach(opt => {
                opt.style.display = opt.textContent.toLowerCase().includes(term) ? '' : 'none';
            });
            activeIndex = -1;
        });

        display.addEventListener('keydown', e => {
            const visible = optionsElements.filter(opt => opt.style.display !== 'none');

            if (['ArrowDown', 'ArrowUp', 'Enter'].includes(e.key)) {
                if (dropdown.style.display !== 'block') {
                    dropdown.style.display = 'block';
                    search.focus();
                    return;
                }
            }

            if (e.key === 'ArrowDown') {
                e.preventDefault();
                activeIndex = (activeIndex + 1) % visible.length;
                highlightOption(visible.indexOf(visible[activeIndex]));
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                activeIndex = (activeIndex - 1 + visible.length) % visible.length;
                highlightOption(visible.indexOf(visible[activeIndex]));
            } else if (e.key === 'Enter') {
                e.preventDefault();
                if (visible[activeIndex]) visible[activeIndex].click();
            } else if (e.key === 'Escape') {
                dropdown.style.display = 'none';
            }
        });

        renderOptions();
    });

    // Multiple Select Initialize

    const selects = document.querySelectorAll('.CDSComponents-MultiSelect');
    
    selects.forEach(select => {
        select.classList.add('CDSComponents-Select-box-hidden');

        const wrapper = document.createElement('div');
        wrapper.className = 'CDSComponents-Select-box-wrapper';
        wrapper.setAttribute('role', 'combobox');
        wrapper.setAttribute('aria-haspopup', 'listbox');
        wrapper.setAttribute('aria-expanded', 'false');

        const inputBox = document.createElement('div');
        inputBox.className = 'CDSComponents-Select-box-input';
        inputBox.setAttribute('tabindex', '0');

        const dropdown = document.createElement('div');
        dropdown.className = 'CDSComponents-Select-box-dropdown';
        dropdown.setAttribute('role', 'listbox');

        const search = document.createElement('input');
        search.className = 'CDSComponents-Select-box-search';
        search.placeholder = 'Search...';
        dropdown.appendChild(search);

        select.parentNode.insertBefore(wrapper, select);
        wrapper.appendChild(select);
        wrapper.appendChild(inputBox);
        wrapper.appendChild(dropdown);

        let selected = [];
        const max = +select.dataset.limit || Infinity;
        let activeIndex = -1;

        function renderTags() {
        inputBox.innerHTML = '';
        selected.forEach((value, idx) => {
            const tag = document.createElement('div');
            tag.className = 'CDSComponents-Select-box-tag';

            const span = document.createElement('span');
            span.textContent = value;
            tag.appendChild(span);

            const remove = document.createElement('span');
            remove.className = 'CDSComponents-Select-box-tag-remove';
            remove.textContent = '×';
            remove.onclick = () => {
            selected.splice(idx, 1);
            renderTags();
            renderDropdown();
            syncSelect();
            };
            tag.appendChild(remove);
            inputBox.appendChild(tag);
        });
        }

        function renderDropdown() {
        dropdown.querySelectorAll('.CDSComponents-Select-box-option, .CDSComponents-Select-box-optgroup').forEach(e => e.remove());
        Array.from(select.children).forEach(child => {
            if (child.tagName === 'OPTGROUP') {
            const groupLabel = document.createElement('div');
            groupLabel.className = 'CDSComponents-Select-box-optgroup';
            groupLabel.textContent = child.label;
            dropdown.appendChild(groupLabel);

            Array.from(child.children).forEach(option => {
                if (selected.includes(option.value)) return;
                const div = document.createElement('div');
                div.className = 'CDSComponents-Select-box-option';
                div.setAttribute('role', 'option');
                div.textContent = option.text;
                div.dataset.value = option.value;
                div.addEventListener('click', () => {
                if (selected.length < max) {
                    selected.push(option.value);
                    renderTags();
                    renderDropdown();
                    syncSelect();
                }
                });
                dropdown.appendChild(div);
            });
            }
        });
        }

        function syncSelect() {
        Array.from(select.options).forEach(opt => {
            opt.selected = selected.includes(opt.value);
        });
        }

        inputBox.addEventListener('click', () => {
        dropdown.style.display = 'block';
        wrapper.setAttribute('aria-expanded', 'true');
        });

        document.addEventListener('click', e => {
        if (!wrapper.contains(e.target)) {
            dropdown.style.display = 'none';
            wrapper.setAttribute('aria-expanded', 'false');
        }
        });

        search.addEventListener('input', () => {
        const term = search.value.toLowerCase();
        dropdown.querySelectorAll('.CDSComponents-Select-box-option').forEach(opt => {
            opt.style.display = opt.textContent.toLowerCase().includes(term) ? '' : 'none';
        });
        });

        inputBox.addEventListener('keydown', e => {
        const visibleOptions = Array.from(dropdown.querySelectorAll('.CDSComponents-Select-box-option')).filter(opt => opt.style.display !== 'none');
        if (e.key === 'ArrowDown') {
            activeIndex = (activeIndex + 1) % visibleOptions.length;
        } else if (e.key === 'ArrowUp') {
            activeIndex = (activeIndex - 1 + visibleOptions.length) % visibleOptions.length;
        } else if (e.key === 'Enter') {
            e.preventDefault();
            if (visibleOptions[activeIndex]) {
            visibleOptions[activeIndex].click();
            }
        }
        visibleOptions.forEach((el, i) => el.classList.toggle('active', i === activeIndex));
        });

        renderDropdown();
        renderTags();
    });
}

function initSelect() {
    if ($("select").length > 0) {
        $("select:not(.flatpickr-monthDropdown-months):not(.CdsTicket-filter-dropdown):not(.CDSDatepicker-Select):not(.no-select2)").each(function () {
            let $this = $(this);
            let $modal = $this.closest(".modal");

            $this.select2({
                dropdownParent: $modal.length ? $modal : $(document.body),
                width: "100%",
            });
        });
    }
}
$(document).on('shown.bs.modal', function () {
    initSelect();
});

function formValidation(formId) {
    var is_valid = true;

    $("#" + formId)
        .find(".js-form-message .form-control")
        .removeClass("is-invalid");
    $("#" + formId)
        .find(".required-error")
        .remove();
    $("#" + formId)
        .find(".required:not(.noval)")
        .each(function () {
            // console.log($("input[name='gender']:checked").val());
            if ($(this).val() == "") {
                is_valid = false;
                var errmmsg =
                    '<div class="required-error text-danger">This field is required</div>';
                $(this).parents(".js-form-message").append(errmmsg);
                $(this)
                    .parents(".js-form-message")
                    .find(".form-control")
                    .addClass("is-invalid");
            } else {
                var type = $(this).attr("type");
                if (type == "email") {
                    if ($(this).val() !== "" && !validateEmail($(this).val())) {
                        is_valid = false;
                        var errmmsg =
                            '<div class="required-error text-danger">Email is not valid.</div>';
                        $(this).parents(".js-form-message").append(errmmsg);
                        $(this)
                            .parents(".js-form-message")
                            .find(".form-control")
                            .addClass("is-invalid");
                    }
                } else {
                    if (
                        !$(this).hasClass("html-editor") &&
                        !validateNoHtmlOrSpecialChars($(this).val())
                    ) {
                        is_valid = false;
                        var errmmsg =
                            '<div class="required-error text-danger">This field should not have any tags or special characters.</div>';
                        $(this).parents(".js-form-message").append(errmmsg);
                        $(this)
                            .parents(".js-form-message")
                            .find(".form-control")
                            .addClass("is-invalid");
                    }
                }
            }
        });

    $("#" + formId)
        .find(
            "input[type=text]:not(.required),input[type=text]:not(.noval),input[type=url]:not(.required):not(.noval)"
        )
        .each(function () {
            var type = $(this).attr("type");
            var is_email = false;
            if (type == "email") {
                if (email !== "" && !validateEmail($(this).val())) {
                    is_valid = false;
                    var errmmsg =
                        '<div class="required-error text-danger">Email is not valid.</div>';
                    $(this).parents(".js-form-message").append(errmmsg);
                    $(this)
                        .parents(".js-form-message")
                        .find(".form-control")
                        .addClass("is-invalid");
                }
            } else {
                if (
                    $(this).val() != "" &&
                    !validateNoHtmlOrSpecialChars($(this).val())
                ) {
                    is_valid = false;
                    var errmmsg =
                        '<div class="required-error text-danger">This field should not have any tags or special characters.</div>';
                    $(this).parents(".js-form-message").append(errmmsg);
                    $(this)
                        .parents(".js-form-message")
                        .find(".form-control")
                        .addClass("is-invalid");
                }
            }
        });
    $("#" + formId)
        .find("textarea.addvalidation")
        .each(function () {
            if (
                $(this).val() != "" &&
                !validateNoHtmlOrSpecialChars($(this).val())
            ) {
                is_valid = false;
                var errmmsg =
                    '<div class="required-error text-danger">This field should not have any tags or special characters.</div>';
                $(this).parents(".js-form-message").append(errmmsg);
                $(this)
                    .parents(".js-form-message")
                    .find(".form-control")
                    .addClass("is-invalid");
            }
        });
    return is_valid;
}

function datepicker(id) {
    $("#" + id).datepicker({
        inline: true,
    });
}

function reminderDatePicker(id) {
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);

    $("#" + id).flatpickr({
        inline: false,
        minDate: tomorrow, // Prevents selection before tomorrow
    });

}
function initTimePicker(id) {
  

     const tp = new TimePicker("#" + id, {
        format: '12hr',
        stepMinutes: 15,
        minTime: '08:00',
      //  maxTime: '18:00',
   //     defaultTime: '09:30',
        onSelect: (time) => {
            console.log('Selected:', time);
        }
    });
}

       
/* flatpickr */
function initDatePicker(id) {
        CustomCalendarWidget.initialize(id, {
        dateFormat: "Y-m-d",
    });
}

/* invoice date */
function invoiceDatePicker(id) {
    CustomCalendarWidget.initialize(id,{
        dateFormat: "Y-m-d",
        minDate: new Date()
    });
}


function getFormatted18YearsAgoDate() {
            const date = new Date();
            date.setFullYear(date.getFullYear() - 18);
            
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`; 
        }
    
/* flatpickr for birthday */
function dobDatePicker(id) {
      CustomCalendarWidget.initialize(id, {
            maxDate: getFormatted18YearsAgoDate(),
            defaultDate: getFormatted18YearsAgoDate(),
            dateFormat: "Y-m-d"
        });
}

/* flatpickr for employment date */

function initDatePicker(id) {
        CustomCalendarWidget.initialize(id, {
        dateFormat: "Y-m-d",
    });
}

function initPastDatePicker(id) {
    CustomCalendarWidget.initialize(id, {
        dateFormat: "Y-m-d",
        maxDate: new Date()
    });
}

function initFutureDatePicker(id) {
    CustomCalendarWidget.initialize(id, {
        dateFormat: "Y-m-d",
        minDate: new Date()
    });
}



function feedDatePicker(id) {
    CustomCalendarWidget.initialize(id, {
        dateFormat: "Y-m-d",
        minDate: new Date() // Only allow today and future dates
    });
}

function allowAlphanumeric(input) {
    input.value = input.value.replace(/[^a-zA-Z0-9 ]/g, "");
}

function validateString(input) {
    // Allow letters and spaces only
    input.value = input.value.replace(/[^a-zA-Z\s]/g, "");
}

function validatePhoneNumber(input) {
    // Allow number only
    input.value = input.value.replace(/[^0-9]/g, "");
}

function validateName(input) {
    // Allow letters and spaces only
    input.value = input.value.replace(/[^a-zA-Z\s]/g, "");
}

function validateZipCode(input) {
    // Remove any character that is not a number
 input.value = input.value.replace(/[^a-zA-Z0-9\s]/g, "");
    
 // Limit the input to a maximum of 10 digits
 if (input.value.length > 10) {
     input.value = input.value.slice(0, 10);
 }
}

function validateSupportAmount(input) {
    // Allow letters and spaces only
    input.value = input.value.replace(/[^0-9 ]/g, "");
}

function allowAlphabetsHyphenDot(input) {
    input.value = input.value.replace(/[^a-zA-Z-.]/g, ""); // Allow only letters, hyphens, and dots
}

function replaceSpacewithDash(input) {
    input.value = input.value
        .replace(/[^a-zA-Z0-9\s-]/g, "") // Allow letters, numbers, spaces, and hyphens
        .replace(/\s+/g, "-") // Replace spaces with hyphens
        .toLowerCase() // Convert the entire string to lowercase
        .replace(/^-/, ""); // Remove leading hyphen if it exists
}

function validateExclamatory(input) {
    input.value = input.value.replace(/[!@]/g, "");
}

function validateAddress(input) {
    // Allow letters and spaces only
    input.value = input.value.replace(/[^a-zA-Z0-9,. \/\s-]/g, "");
}

// /^[a-zA-Z0-9 \/]+$/
// $(document).ready(function () {
//     /* --- Navs Tab --- */
//     $(".tabs-section #tabs-nav li:first-child").addClass("active");// Show the first tab and hide the rest
//     $(".tabs-section .tab-content").hide();
//     $(".tabs-section .tab-content:first").show();
//     // Click function
//     $(".tabs-section #tabs-nav li").click(function () {
//         $(".tabs-section #tabs-nav li").removeClass("active");
//         $(this).addClass("active");
//         $(".tabs-section .tab-content").hide();
//         var activeTab = $(this).find("a").attr("href");
//         $(activeTab).fadeIn();
//         return false;
//     });/* --- # Navs Tab --- */
// });

/* The above code appears to be using a multi-line comment syntax in JavaScript. The code is using the
`$(document)` syntax which is commonly used in jQuery to select the document object. The ` */
$(document).ready(function () {
    $(document).ajaxComplete(function (_, xhr) {
        window.avatarGradient.init();
    });
    $(document).on("input", ".html-not-allowed", function(){
    console.log("input triggered on html-not-allowed");
      validateHtmlTags((this));
    });
    /* --- Navs Tab --- */
    $(".tabs-section #tabs-nav li:first-child").addClass("active"); // Show the first tab and hide the rest
    $(".tabs-section .tab-content").hide();
    $(".tabs-section .tab-content:first").show();

    // Click function for tabs
    $(".tabs-section #tabs-nav li").click(function () {
        $(".tabs-section #tabs-nav li").removeClass("active");
        $(this).addClass("active");
        $(".tabs-section .tab-content").hide();
        var activeTab = $(this).find("a").attr("href");
        $(activeTab).fadeIn();
        return false;
    });

    // Click function for the Next button
    $(".next-tab-button").click(function () {
        var $activeTab = $(".tabs-section #tabs-nav li.active");
        var $nextTab = $activeTab.next("li");

        if ($nextTab.length) {
            $activeTab.removeClass("active");
            $nextTab.addClass("active");
            $(".tabs-section .tab-content").hide();
            var activeTab = $nextTab.find("a").attr("href");
            $(activeTab).fadeIn();
        }
    });
    /* --- # Navs Tab --- */

    $(document).on("click", ".next", function () {
        // Get the current active page number
        var currentPage = $(".pagination a.active-page.active").data("max");

        // Calculate the next page number
        var nextPage = currentPage + 1;

        // Load data for the next page if it is within valid range
        //loadData(nextPage);
    });

    $(document).on("click", ".prev", function () {
        // Get the current active page number
        var currentPage = $(".pagination a.active-page.active").data("max");

        // Calculate the next page number
        var prevPage = currentPage - 1;

        // Load data for the next page if it is within valid range
        //loadData(prevPage);
    });

    // var inputs = document.querySelectorAll(".phoneno");
    // for (var i = 0; i < inputs.length; i++) {
    // // $(".phoneno").each(function(){
    //     // var e = $(this);
    //     initPhoneNo(inputs[i]);
    // }
});

function initFrontPagination(data) {
    if (data.last_page !== undefined) {
        $(".no-record-available").remove();

        // Update page information
        $(".last-page").text(data.last_page);
        $(".current-page").text(data.current_page);
        $(".total-records").text(data.total_records);

        // Generate pagination HTML
        var paginationHtml =
            '<a href="javascript:;" class="prev"><i class="fa-solid fa-chevron-left"></i></a>';
        for (let i = 1; i <= data.last_page; i++) {
            const isActive = i === data.current_page ? "active" : "";
            paginationHtml += `<a href="javascript:;" class="active-page page ${isActive}" data-max=${i} data-min=1>${i}</a>`;
        }
        paginationHtml +=
            '<a href="javascript:;" class="next"><i class="fa-solid fa-chevron-right"></i></a>';

        // Replace existing pagination with the new one
        $(".pagination").html(paginationHtml);

        // Update navigation buttons
        if (data.current_page === 1) {
            $("a.prev").addClass("disabled-btn").css("pointer-events", "none");
        } else {
            $("a.prev")
                .removeClass("disabled-btn")
                .css("pointer-events", "auto");
        }

        if (data.current_page === data.last_page) {
            $("a.next").addClass("disabled-btn").css("pointer-events", "none");
        } else {
            $("a.next")
                .removeClass("disabled-btn")
                .css("pointer-events", "auto");
        }

        // Show 'No records available' message if there are no records
        if (data.total_records === 0) {
            $(".job-post-data").after(
                '<div class="no-record-available">No records available</div>'
            );
        }
    }
}

// initialize editor
// for notification open end//
function initEditor(id, type = "full") {
   
    // Remove any existing classes
    $("#"+id).removeClass("html-not-allowed");
    $("#" + id).addClass("html-editor");
    
    // Initialize your custom editor
    const editor = CustomEditor.init("#" + id, {
        // Configuration options
        users: [], // Add your users array or AJAX config
        typeDelay: 500,
        usersLoadDelay: 300,
        autoSaveDelay: 100,
        
        // Event callbacks
        onChange: function(content) {
            console.log('Content changed:', content);
        },
        onType: function(content) {
            console.log('Typing:', content);
        },
        onMention: function(user) {
            console.log('User mentioned:', user);
        }
    });
    
    return editor;
}
// function initEditor(id, type = "full") {
//       $("#"+id).removeClass("html-not-allowed");
//     $("#" + id).addClass("html-editor");
//     var editor = RedactorX("#" + id, {
//         plugins: [
//             "inlineformat",
//             "style",
//             "alignment",
//             "list",
//             "link",
//             "fontcolor",
//             "alignment",
//             "handle",
//             "fontfamily",
//             "fontsize",
//             "blockclass",
//             "blockbackground",
//             "imageposition",
//             "imageresize",
//             "underline",
//             "icons",
//             "selector",
//             "handle",
//             "removeformat",
//             "textdirection",
//             "specialchars",
//             "image",
//         ],
//         minHeight: "800px",
//         placeholder: "Enter your text here...",
//         formatting: ["p", "blockquote", "h1", "h2", "h3", "h4", "h5", "h6"],
//     });

//     return editor;
// }

function initPhoneNo(e) {
    var iti = intlTelInput(e, {
        autoHideDialCode: false,
        autoPlaceholder: "aggressive",
        initialCountry: "ca",
        strictMode: true,
        separateDialCode: true,
        preferredCountries: ["ru", "th"],
        customPlaceholder: function (
            selectedCountryPlaceholder,
            selectedCountryData
        ) {
            return "" + selectedCountryPlaceholder.replace(/[0-9]/g, "X");
        },
        geoIpLookup: function (callback) {
            $.get("https://ipinfo.io", function () {}, "jsonp").always(
                function (resp) {
                    var countryCode =
                            resp && resp.country ? resp.country : "";
                    callback(countryCode);
                }
            );
        },
        utilsScript:
            "https://cdn.jsdelivr.net/npm/intl-tel-input@20.3.0/build/js/utils.js", // just for
    });
    return iti;
}

// google.maps.event.addDomListener(window, "load", initGoogleAddress);
function initGoogleAddress() {
    $(".google-address").each(function () {
        var address = $(this).attr("id");
        var element = $(this).parents(".google-address-area");
        var addressLine1 = "";
        var autocomplete = new google.maps.places.Autocomplete(
            document.getElementById(address),
            { types: ["geocode"] }
        );
        /* google placeholder remove */
        setTimeout(() => {
            document.getElementById(address).removeAttribute("placeholder");
        }, 1500);
        autocomplete.addListener("place_changed", function () {
            var place = autocomplete.getPlace();
            if (!place.geometry) {
                document.getElementById(address).textContent =
                    "No details available for input: '" + place.name + "'";
                return;
            }
            var address = "";
            addressLine1 = formatAddressLine1(place);

            if (place.address_components) {
                address = [
                    (place.address_components[0] &&
                        place.address_components[0].short_name) ||
                        "",
                    (place.address_components[1] &&
                        place.address_components[1].short_name) ||
                        "",
                    (place.address_components[2] &&
                        place.address_components[2].short_name) ||
                        "",
                ].join(" ");
            }
            // auto fill city country state pincode
            const addressComponents = {
                pincode: "",
                country: "",
                state: "",
                city: "",
                address2: "",
            };

            place.address_components.forEach((component) => {
                const componentType = component.types[0];

                if (componentType === "postal_code") {
                    addressComponents.pincode = component.long_name;
                } else if (componentType === "country") {
                    addressComponents.country = component.long_name;
                } else if (componentType === "administrative_area_level_1") {
                    addressComponents.state = component.long_name;
                } else if (componentType === "locality") {
                    addressComponents.city = component.long_name;
                } else if (componentType === "sublocality_level_2") {
                    addressComponents.address2 = component.long_name;
                }
            });

            // Update form fields with retrieved address details
            element.find(".google-address").val(addressLine1).trigger("change");

            element
                .find(".ga-pincode")
                .val(addressComponents.pincode)
                .trigger("change");
            // document.getElementById("country_id").value = addressComponents.country;
            element
                .find(".ga-state")
                .val(addressComponents.state)
                .trigger("change");
            element
                .find(".ga-city")
                .val(addressComponents.city)
                .trigger("change");
            element
                .find(".ga-address2")
                .val(addressComponents.address2)
                .trigger("change");
            // element.find(".ga-country option[value='"+addressComponents.country+"']").attr('selected','selected').trigger('change');

            // element.find(".ga-country").select2("val",addressComponents.country);

            element
                .find(
                    ".ga-country option[value='" +
                        addressComponents.country +
                        "']"
                )
                .attr("selected", "selected")
                .trigger("change");

            // document.getElementById('address').textContent = 'Address: ' + place.formatted_address;
        });
    });
}

function formatAddressLine1(place) {
    const addressComponents = [
        "subpremise",
        "floor",
        "street_number",
        "route",
        "intersection",
        // "neighborhood",
        "sublocality",
        "premise",
        "establishment",
        "point_of_interest",
        "sublocality_level_1",
        "sublocality_level_2",
        "sublocality_level_3",
        "sublocality_level_4",
        "sublocality_level_5",
    ];
    let addarr = [];
    let addressLine1 = addressComponents
        .map((type) => {
            const component = place.address_components.find((c) =>
                c.types.includes(type)
            );
            var addr = component ? component.long_name : "";
            if (!addarr.includes(addr)) {
                addarr.push(addr);
                return component ? component.long_name : "";
            } else {
                return "";
            }
            return component ? component.long_name : "";
        })
        .filter(Boolean)
        .join(", ");
    return addressLine1;
}

function isNumber(value) {
    return typeof value === "number";
}

function googleRecaptcha() {
    $(".google-recaptcha").html("");
    $.ajax({
        type: "GET",
        url: SITEURL + "/google-recaptcha",
        dataType: "json",
        beforeSend: function () {},
        success: function (response) {
            $(".google-recaptcha").html(response.contents);
        },
        error: function () {
            errorMessage("Error while rendering google captcha");
        },
    });
}

$("#zip_code").blur(function () {
    var zip = $(this).val();
    if (zip.length >= 5 && typeof google != "undefined") {
        var addr = {};
        var geocoder = new google.maps.Geocoder();
        geocoder.geocode(
            {
                address: zip,
            },
            function (results, status) {
                if (status == google.maps.GeocoderStatus.OK) {
                    if (results.length >= 1) {
                        for (
                            var ii = 0;
                            ii < results[0].address_components.length;
                            ii++
                        ) {
                            var street_number =
                                (route =
                                street =
                                city =
                                state =
                                zipcode =
                                country =
                                formatted_address =
                                    "");
                            var types =
                                results[0].address_components[ii].types.join(
                                    ","
                                );

                            if (
                                types == "sublocality,political" ||
                                types == "locality,political" ||
                                types == "neighborhood,political" ||
                                types == "administrative_area_level_3,political"
                            ) {
                                addr.city =
                                    city == "" || types == "locality,political"
                                        ? results[0].address_components[ii]
                                              .long_name
                                        : city;
                            }
                        }

                        if (addr.city.trim() == $("#city_id").val().trim()) {
                            $(".zip_code_error").html("");
                        } else {
                            $(".zip_code_error").html(
                                "Zip code does not match for city"
                            );
                        }
                    } else {
                        $(".zip_code_error").html(
                            "Zip code does not match for city"
                        );
                    }
                } else {
                    $(".zip_code_error").html(
                        "Zip code does not match for city"
                    );
                }
            }
        );
    } else {
        $(".zip_code_error").html("Zip code does not match for city");
    }
});

function initFrontPhoneNo() {
    let iti = [];
    let input = [];
    var i = 0;
    $(".phoneno-with-code").each(function () {
        var phone_no_id = $(this).find(".phone_no").attr("id");
        var country_code_id = $(this).find(".country_code").attr("id");
        var default_phone_no = $("#" + phone_no_id).val();
        var default_country_code = $("#" + country_code_id).val();
        input[i] = document.querySelector("#" + phone_no_id);
        iti[i] = window.intlTelInput(input[i], {
            autoHideDialCode: false,
            autoPlaceholder: "aggressive",
            initialCountry: "us",
            separateDialCode: true,
            preferredCountries: ["ru", "th"],
            customPlaceholder: function (
                selectedCountryPlaceholder,
                selectedCountryData
            ) {
                return "" + selectedCountryPlaceholder.replace(/[0-9]/g, "X");
            },
            geoIpLookup: function (callback) {
                $.get("https://ipinfo.io", function () {}, "jsonp").always(
                    function (resp) {
                        var countryCode =
                            resp && resp.country ? resp.country : "";
                        callback(countryCode);
                    }
                );
            },
            utilsScript:
                "https://cdn.jsdelivr.net/npm/intl-tel-input@20.3.0/build/js/utils.js", // just for
        });
        if (default_country_code != "" && default_phone_no != "") {
            iti[i].setNumber(default_country_code + "" + default_phone_no);
        }
        let currentIti = iti[i];

        $("#" + phone_no_id).on(
            "focus click countrychange",
            function (e, countryData) {
                var pl = $(this).attr("placeholder") + "";
                var res = pl.replace(/X/g, "9");
                if (res != "undefined") {
                    $(this).inputmask(res, {
                        placeholder: "X",
                        clearMaskOnLostFocus: true,
                    });
                }
                var intlNumber = currentIti.getNumber();
                var phoneCode = currentIti.getSelectedCountryData().dialCode;
                var phoneNumber = intlNumber.replace("+" + phoneCode, "");
                $("#" + country_code_id).val("+" + phoneCode);
                currentIti.setNumber(intlNumber);
            }
        );
    });
}

function validateTrackingId(input) {
    // Remove any non-digit character
    input.value = input.value.replace(/[^0-9]/g, "");
}
function newMessageNotification(message) {
    const notificationTone = document.getElementById("notification-tone");
    notificationTone.play();
    Swal.fire({
        text: message,
        type: "success",
        icon: "success",
        position: "top-end", // Top-right corner
        showConfirmButton: false, // Hide the OK button
        toast: true, // Make it a toast-style alert
        timer: 13000, // Automatically close after 3 seconds
        width: "300px", // Adjust the width
    });
}

function scheduleDatePicker(id) {
    $("#" + id).flatpickr({
        inline: false,
        minDate: "today", // Prevents selection of future dates
    });
}

function bottomMessage(message) {
    // toastr.options = {
    //     "positionClass": "toast-bottom-center", // Set position to bottom center
    //     "closeButton": true,                   // Enable close button (optional)
    //     "progressBar": true                    // Show progress bar (optional)
    // };
    toastr.info(message, "",{
        "positionClass": "toast-bottom-center", // Set position to bottom center
        "closeButton": true,                   // Enable close button (optional)
        "progressBar": true                    // Show progress bar (optional)
    });
}



function removePaymentMethod(e) {
    var url = $(e).attr("data-href");
    var is_default = $(e).attr("data-default");
 
    if(is_default === 'Yes')
    {
        errorMessage('You can not remove default card. Add another card and make it default card first.');
    }
    else
    {
        Swal.fire({
            title: "Are you sure to Remove this Payment Method?",
            text: "You won't be able to revert this!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes",
            confirmButtonClass: "btn btn-primary",
            cancelButtonClass: "btn btn-danger ml-1",
            buttonsStyling: false,
        }).then(function (result) {
            if (result.value) {
                redirect(url);
            }
        });
    }
    
}

function confirmPaymentMethod(e) {
    var url = $(e).attr("data-href");
   
        Swal.fire({
            title: "Are you sure to make this Default Payment Method?",
            text: "You won't be able to revert this!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes",
            confirmButtonClass: "btn btn-primary",
            cancelButtonClass: "btn btn-danger ml-1",
            buttonsStyling: false,
        }).then(function (result) {
            if (result.value) {
                redirect(url);
            }
        });
    
}

function validateHtmlTags(input) {
  input.value = input.value.replace(/<[^>]*>/g, '');
  
}




/* Sidebar js  */
const sidebar = document.getElementById('sidebar');
const menuContent = document.getElementById('menuContent');
const moreIndicator = document.getElementById('moreIndicator');
const floatingSubmenu = document.getElementById('floatingSubmenu');
let userToggled = false;
$(document).ready(function(){
    $(document).on("click",".has-sub-menu",function(){
        $(this).parents('.menu-item-wrapper').toggleClass("active");
    });
    updateMoreIndicator();
});
if(menuContent){
    menuContent.addEventListener('wheel', (e) => {
        e.preventDefault();
        menuContent.scrollTop += e.deltaY;
        updateMoreIndicator();
        }, { passive: false });

}

window.addEventListener('resize', () => {
  updateMoreIndicator();
});

function updateMoreIndicator() {
  const items = menuContent.querySelectorAll('.menu-item-wrapper');
  let hiddenCount = 0;
  const contentRect = menuContent.getBoundingClientRect();

  items.forEach(item => {
    const rect = item.getBoundingClientRect();
    if (rect.bottom > contentRect.bottom) hiddenCount++;
  });

  if (hiddenCount > 0) {
    moreIndicator.style.display = 'block';
    moreIndicator.textContent = `+${hiddenCount} more item${hiddenCount > 1 ? 's' : ''}`;
  } else {
    moreIndicator.style.display = 'none';
  }
}

function showRightSlidePanel(e, method = "get", paramters = {}) {
    var url = $(e).attr("data-href");
    $.ajax({
        url: url ,
        dataType: "json",
        type: method,
        data: paramters,
        beforeSend: function () {
            showLoader();
        },
        success: function (result) {
            hideLoader();
            if (result.status == true) {
                $("#rightSlidePanel .CDSRightSlidePanel-Body").html(result.contents);
                // $('#rightSlidePanel').addClass('active');

                $('#rightSlidePanel').addClass('active');
                $('#CDSRightSlidePanelOverlay').addClass('active');
                document.body.style.overflow = "hidden";
                initFloatingLabel();
                setTimeout(() => {
                    initSelect();
                }, 500);
            } else {
                if (result.message != undefined) {
                    errorMessage(result.message);
                } else {
                    errorMessage("No Data found");
                }
            }
        },
        complete: function () {
            hideLoader();
        },
        error: function () {
            hideLoader();
            internalError();
        },
    });
}
function openCustomPopup(e, method = "get", paramters = {}) {
    var url = $(e).attr("data-href");
    $.ajax({
        url: url ,
        dataType: "json",
        type: method,
        data: paramters,
        beforeSend: function () {
            showLoader();
        },
        success: function (result) {
            hideLoader();
            if (result.status == true) {
                $("#customPopupOverlay").html(result.contents);
                const overlay = document.getElementById('customPopupOverlay');
                overlay.classList.add('active');
                document.body.style.overflow = 'hidden';
                initFloatingLabel();
                setTimeout(() => {
                    initSelect();
                }, 1100);
            } else {
                if (result.message != undefined) {
                    errorMessage(result.message);
                } else {
                    errorMessage("No Modal Data found");
                }
            }
        },
        complete: function () {
            hideLoader();
        },
        error: function () {
            hideLoader();
            internalError();
        },
    });
}

function closeCustomPopup() {
    const overlay = document.getElementById('customPopupOverlay');
    overlay.classList.remove('active');
    document.body.style.overflow = ''; // Restore body scroll
    overlay.innerHTML = '';
}

window.addEventListener("DOMContentLoaded", function () {
  // Enhanced click-outside functionality for right slide panel
  const overlay = document.getElementById("CDSRightSlidePanelOverlay");
  const rightSlidePanel = document.getElementById("rightSlidePanel");
  
  if (overlay) {
    overlay.addEventListener("click", function(e) {
      // Only close if clicking directly on the overlay, not on the panel
      if (e.target === overlay) {
        closeRightSlidePanel();
      }
    });
  }
  
  // Add document click listener to close panel when clicking outside
  document.addEventListener("click", function(e) {
    // Check if panel is active and click is outside both overlay and panel
    if (rightSlidePanel && rightSlidePanel.classList.contains('active')) {
      const isClickInsidePanel = rightSlidePanel.contains(e.target);
      const isClickOnOverlay = overlay && overlay.contains(e.target);
      
      // Close if click is outside both panel and overlay
      if (!isClickInsidePanel && !isClickOnOverlay) {
        closeRightSlidePanel();
      }
    }
  });
  
  // Prevent clicks inside the panel from bubbling up and closing it
  if (rightSlidePanel) {
    rightSlidePanel.addEventListener("click", function(e) {
      e.stopPropagation();
    });
  }
  
  // Add ESC key functionality to close panel
  document.addEventListener("keydown", function(e) {
    if (e.key === "Escape" && rightSlidePanel && rightSlidePanel.classList.contains('active')) {
      closeRightSlidePanel();
    }
  });
});


function closeRightSlidePanel() {
    $("#rightSlidePanel .CDSRightSlidePanel-Body").html('');
    $('#rightSlidePanel').removeClass('active');
    $('#CDSRightSlidePanelOverlay').removeClass('active');
    document.body.style.overflow = "";
}