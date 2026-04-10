var formFields = [
    {
        'label': "Fields Groups",
        'icon_class': "from-group-icon",
        'type': "fieldGroups"
    },
    {
        'label': "Text Input",
        'icon_class': "text-input-icon",
        'type': "textInput",
        'img': "<img src=css/text-input.svg />",
    }, {
        'label': "Number Input",
        'icon_class': "number-input-icon",
        'type': "numberInput"
    }, {
        'label': "Text Editor",
        'icon_class': "textarea-input-icon",
        'type': "textarea"
    }, {
        'label': "Email",
        'icon_class': "email-input-icon",
        'type': "emailInput"
    }, {
        'label': "Link Input",
        'icon_class': "link-input-icon",
        'type': "url"
    }, {
        'label': "Drop Down",
        'icon_class': "dropDown-icon",
        'type': "dropDown"
    }, {
        'label': "Checkbox",
        'icon_class': "checkbox-icon",
        'type': "checkbox"
    }, {
        'label': "Radio",
        'icon_class': "radio-icon",
        'type': "radio"
    }, {
        'label': "Datepicker",
        'icon_class': "datepicker-icon",
        'type': "dateInput"
    }, {
        'label': "Google Address",
        'icon_class': "google-address-icon",
        'type': "addressInput"
    }, {
        'label': "Document Upload",
        'icon_class': "document-upload-icon",
        'type': "fgDropzone"
    }];

    var RENDER_BASE_URL = "http://localhost/rating-v2/super-admin";
    var render_csrf_token = "{{ csrf_token() }}";
    var RENDER_SITE_URL = "http://localhost/rating-v2";
let attributes = {
    'default': [{
        'label': "Label",
        'type': 'label',
        'value': '',
        'field': 'hidden'
    }, {
        'label': "Name",
        'type': 'name',
        'value': '',
        'field': 'hidden'
    }, {
        'label': "Short Description",
        'type': "shortDesc",
        'value': '',
        'field': 'hidden'
    }, {
        'label': "Placeholder",
        'type': 'placeholder',
        'value': '',
    }, {
        'label': "Max Length",
        'type': 'maxlength',
        'value': '',
    }, {
        'label': "Required",
        'type': 'required',
        'field': "checkbox",
        'value': '',
    }, {
        'label': "Step Heading",
        'type': 'stepHeading',
        'value': 'Step Heading',
    }, {
        'label': "Step Description",
        'type': 'stepDescription',
        'value': 'Step Description',
    }],
    'fieldGroups': [{
        'label': "Label",
        'type': 'label',
        'field': "hidden"
    }, {
        'label': "Short Description",
        'type': "shortDesc",
        'value': '',
        'field': 'hidden'
    }, {
        'label': "Font Size",
        'type': 'font_size',
        'field': "dropdown",
        'options': [{
            "label": "H1",
            "value": "32"
        }, {
            "label": "H2",
            "value": "24"
        }, {
            "label": "H3",
            "value": "18"
        }, {
            "label": "H4",
            "value": "16"
        }, {
            "label": "H5",
            "value": "13"
        }, {
            "label": "H6",
            "value": "12"
        }]
    }, {
        'label': "Step Heading",
        'type': 'stepHeading',
        'value': 'Step Heading',
    }, {
        'label': "Step Description",
        'type': 'stepDescription',
        'value': 'Step Description',
    }],
    'textarea': [{
        'label': "Text Limit",
        'type': 'textLimit',
        'field': "dropdown",
        'options': [{
            "label": "Word Limit",
            "value": "wordLimit"
        }, {
            "label": "Character Limit",
            "value": "characterLimit"
        }, {
            "label": "None",
            "value": "none"
        }]
    }, {
        'label': "Add Length",
        'type': 'addLength',
        'field': "number",
        'value': '',
    }]
};
let saveUrl = '';
let redirectBack = '';
let defaultJson = '';
let formName = '';
let formType = '';
let previewUrl = '';

$(document).ready(function() {
    $(".tv-form").each(function () {
        var form_id = $(this).data('form');
        var ele_id = $(this).attr('id');

        var fr = $('#'+ele_id).formRender({
            // formType: "{{$record->form_type}}",
            // formJson: formJson,
            divID:ele_id,
            formID:form_id,
            ajax_call: false,
            // saveUrl: "{{ baseUrl('forms/save') }}",
        });
        $(".finish-btn").remove();
    })
});
// Render HTML
$.fn.formRender = function (param = {}) {
    var formJson = "";
    var formType = "";
   
    $.ajax({
        type: "GET",
        url: RENDER_BASE_URL + '/form-scripts/generate-script/'+param.formID,
        data:{
            _token:render_csrf_token,
        },
        dataType:'json',
        success: function (data) {
            formJson = JSON.parse(data.form_json);
            formType = data.form_type;
            var divID = param.divID;
            showRenderData(formJson,formType,'',param.divID);
        },
        error:function(){
            internalError();
        }
    });
};

function showRenderData(formJson,formType,param = {},divID)
{
    var saveUrl = '';
    var redirectBack = '';

    if (param.saveUrl != undefined) {
        saveUrl = param.saveUrl;
    }
    if (param.redirectBack != undefined) {
        redirectBack = param.redirectBack;
    }

    var renderHtml = '<div class="cds-assessment-form-render"><div id="fgr-form" class="cds-assessment-form-render-container">';
    renderHtml += '<div class="fgr-render cds-assessment-form-render-wrap">';
    renderHtml += '<div class="fgr-render-inner cds-assessment-form-render-body">';
    groupFieldsIds = [];
    Object.keys(formJson).forEach(function (key) {
        if (formJson[key]['fields'] == 'fieldGroups') {
            var groupFields = formJson[key]['groupFields'];
            for (var i = 0; i < groupFields.length; i++) {
                if (!groupFieldsIds.includes(groupFields[i])) {
                    groupFieldsIds.push(groupFields[i]);
                }
            }
        }
    });
    if (formType == 'step_form') {
        renderHtml += '<div class="cds-assessment-form-render-body-left-panel">';
        renderHtml += '<div class="form-steps">';
        renderHtml += '</div>';
        renderHtml += '</div>';
    }
    renderHtml += '<div class="cds-assessment-form-render-body-right-panel"><h4 class="step-heading">Step Heading</h4>';
    renderHtml += '<div class="forms-area">';
        Object.keys(formJson).forEach(function (key) {
        var flag = 1;
        for (var i = 0; i < groupFieldsIds.length; i++) {
            if (formJson[key]['index'] == groupFieldsIds[i]) {
                flag = 0;
            }
        }
        if (flag == 1) {

            if (formJson[key]['fields'] == 'fieldGroups') {
                renderHtml += renderfieldGroups(formJson, key);
                console.log(key,"asdasd")

            } else {
                renderHtml += renderField(formJson, key);
                console.log(key,"asdasd")

            }

        }
    });

    renderHtml += '</div>';
    renderHtml += '</div>';
    renderHtml += '</div>';
    renderHtml += '</div></div></div>';
  
    $('#'+divID).append(renderHtml);
    if (formType == 'step_form') {
        var step_html = '';
        var total_step = $(".forms-area > .fg-form-group").length;
        var i = 1;
        $(".forms-area > .fg-form-group").each(function () {
            step_html += '<div class="step-count" data-step="' + i + '">';
            step_html += '<div class="stepno">';
            step_html += '<span>' + i + '</span>';
            step_html += '</div>';
            step_html += '<div class="step-info"><span>' + $(this).data("stephead") + '</span>';
            // step_html += '<p>' + $(this).data("stepdesc") + '</p>';
            // step_html += '<img src="public/assets/plugins/form-generator/icons/right-arrow.svg" alt="Arrow"/>';
            step_html += '<svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg"><path class="arrow-svg-icon" d="M6.71289 14.9405L11.6029 10.0505C12.1804 9.47305 12.1804 8.52805 11.6029 7.95055L6.71289 3.06055" stroke="black" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/></svg>';
            step_html += '</div>';
            step_html += '</div>';
            i++;
        });
        $(".form-steps").html(step_html);
    }
    if (param.defaultValues !== undefined && param.defaultValues != '') {
        let defaultValues = JSON.parse(param.defaultValues);

        $.map(defaultValues, function (val, key) {
            var f_name = 'fg_field[' + key + ']';
            var field = $("." + key).attr("data-field");
            if (field == 'checkbox' || field == 'radio') {
                $("." + key).each(function () {
                    if (Array.isArray(val)) {
                        if (val.includes($(this).val())) {
                            $(this).prop("checked", true);
                        }
                    } else {
                        if ($(this).val() == val) {
                            $(this).prop("checked", true);
                        }
                    }
                })
            } else {
                $("[name='" + f_name + "']").val(val);
            }

        });
    }

    if (formType == 'step_form') {

        $(".fgr-render-inner").addClass("step-form");
        $(".forms-area > .fg-form-group").hide();
        var i = 1;
        $(".forms-area > .fg-form-group").each(function () {
            $(this).attr("data-step", i);
            i++;
        });
        $(".fgr-render-inner .forms-area > .fg-form-group:first-child").addClass("step-active");
        // $(".fgr-render-inner .form-steps > .step-count:first-child .stepno").addClass("stepno-active");
        $(".fgr-render-inner .form-steps > .step-count:first-child").addClass("stepno-active");
        var stepBtn = '<div class="steps-btn-area">';
        stepBtn += '<button type="button" class="prev-btn step-btn">Prev</button>';
        stepBtn += '<button type="button" class="next-btn step-btn">Next</button>';
        stepBtn += '<button type="button" class="finish-btn save-form step-btn">Save</button>';
        stepBtn += '</div>';

        $(".fgr-render").append(stepBtn);
        $(".prev-btn").hide();
        var total_step = $("#fgr-form .form-steps .step-count").length;
        if (total_step == 1) {
            $(".next-btn").hide();
            $(".finish-btn").show();
        }
        $(document).on("click", "#fgr-form .prev-btn", function () {
            var active_step = $("#fgr-form .step-active").data("step");

            var step = active_step - 1;
            $("#fgr-form .fg-form-group").removeClass("step-active");
            // $("#fgr-form .fg-form-group").removeClass("stepno-complete");
            $("#fgr-form .fg-form-group[data-step=" + step + "]").addClass("step-active");
            $("#fgr-form .step-count").removeClass("stepno-active");
            // $("#fgr-form .step-count").removeClass("stepno-complete");
            $("#fgr-form .step-count[data-step=" + step + "]").addClass("stepno-active");
            if (step == 1) {
                $(".prev-btn").hide();
            } else {
                $(".prev-btn").show();
            }
            $('html, body').animate({
                scrollTop: $(".fgr-render").offset().top
            }, 1000);
        });


        $(document).on("click", "#fgr-form .next-btn", function () {
            var active_step = $("#fgr-form .step-active").data("step");
            if ($("#fgr-form").find("*[required]").length > 0) {
                // var requiredFlag = validate(formType,active_step);
                if (!validate(formType, active_step)) {
                    return false;
                }
            }

            if (param.ajax_call !== undefined && param.ajax_call == false) {

                var active_step = $(".step-active").data("step");
                var step = active_step + 1;
                var total_step = $("#fgr-form .form-steps .step-count").length;


                $("#fgr-form .fg-form-group").removeClass("step-active");

                $("#fgr-form .fg-form-group[data-step=" + step + "]").addClass("step-active");
                $("#fgr-form .step-count").removeClass("stepno-active");

                $("#fgr-form .step-count[data-step=" + step + "]").addClass("stepno-active");
                // $("#fgr-form .step-count[data-step=" + step + "]").find(".stepno").addClass("stepno-active");
                $("#fgr-form .step-count").removeClass("stepno-complete");
                $("#fgr-form .prev-btn").show();

                for (var i = 1; i < step; i++) {
                    $("#fgr-form .step-count[data-step=" + i + "]").addClass("stepno-complete");
                    // $("#fgr-form .step-count[data-step=" + i + "]").find(".stepno").addClass("stepno-complete");
                }
                $('html, body').animate({
                    scrollTop: $("#fgr-form .fgr-render").offset().top
                }, 1000);
            } else {
                submitForm('next');
            }
        });
    } else {
        var stepBtn = '<div class="save-btn-area">';
        stepBtn += '<button type="button" class="finish-btn save-form">Save</button>';
        stepBtn += '</div>';
        $(".fgr-render").append(stepBtn);
    }
    if ($(".fgr-render .datepicker").length > 0) {
        $(".datepicker").datepicker({
            format: "dd-mm-yyyy"
        })
    }
    if ($(".fgr-render .fg-editor").length > 0) {
        $(".fgr-render .fg-editor").each(function () {
            initEditor($(this).attr("id"));
        })
    }
    $(document).on("click", "#fgr-form .save-form", function () {
        submitForm('save');
    });
    function initFgDropzone() {
        // Create a new script element

        html = "<link href='assets/theme/css/dropzone.min.css' rel='stylesheet' type='text/css' />";
        html += "<script src='assets/theme/js/dropzone.min.js'></script>";
        html += "<script>Dropzone.autoDiscover = false;</script>";
        $(html).insertBefore("script[src='" + RENDER_SITE_URL + "/assets/plugins/form-generator/js/form-generator.js']");



        //  var scriptElement = document.createElement("script");
        //  scriptElement.src = "assets/theme/js/dropzone.min.js";
        //  document.body.appendChild(scriptElement);
        //  var linkElement = document.createElement("link");

        //  // Set the rel and href attributes for the CSS file you want to include
        //  linkElement.rel = "stylesheet";
        //  linkElement.type = "text/css";
        //  linkElement.href = "assets/theme/css/dropzone.min.css";

        //  // Append the link element to the head of the document
        //  document.head.appendChild(linkElement);


        var i = 0;
        var main_timestamp = getRandom(10);
        var files_uploaded = [];
        $(".fg-dropzone").each(function () {
            var id = $(this).attr("id");
            var index = $(this).data("index");
            var timestamp = $("#" + id).find(".fg-timestamp").val();
            $(".fg-main-timestamp").val(main_timestamp);
            files_uploaded[main_timestamp + "_" + timestamp] = [];
            fgDropzone[index] = new Dropzone("#" + id, {
                url: RENDER_SITE_URL + "/upload-fg-files?_token=" + csrf_token + "&timestamp=" + timestamp + "&main_timestamp=" + main_timestamp,
                autoProcessQueue: false,
                addRemoveLinks: true,
                maxFilesize: 6,
                acceptedFiles: '.jpg,.jpeg,.gif,.tiff,.png,.pdf,.docx,.doc,.txt,.csv,.xls,.xlsx',
                parallelUploads: 40,
                maxFiles: 60,

                success: function (file, response) {
                    files_uploaded[main_timestamp + "_" + timestamp].push(response.filename);
                },
                queuecomplete: function (file) {
                    uploadCount++;
                    var file_value = main_timestamp + "_" + timestamp + ";" + files_uploaded[main_timestamp + "_" + timestamp].join(",");

                    $("#" + id).find(".fg-files").val(file_value);
                    if (formType == 'single_form') {
                        if (uploadCount == $(".fg-dropzone").length) {
                            submitForm('save');
                        }
                    } else {
                        var active_step = $(".step-active").attr("data-step");
                        var last_step = $(".forms-area > .fg-form-group").length;
                        if (active_step < last_step) {
                            submitForm('next');
                        } else {
                            submitForm('save');
                        }
                    }

                },
                init: function () {
                    this.on("error", function (file, errorMessage) {
                        isError = 1;
                        this.removeFile(file);
                    });
                }
            });
            i++;
        })

    }

    function submitForm(savefrom) {
        var formJson = convertFormToJSON($("#fgr-form").closest("form"));
        var submit_form = 1;
        if (formType == 'single_form') {
            // var requiredFlag = validate(formType);
            if (!validate(formType)) {
                return false;
            }
            if ($(".fg-dropzone").length > 0) {

                if (uploadCount == $(".fg-dropzone").length) {
                    submit_form = 1;
                } else {
                    $(".fg-dropzone").each(function () {
                        var uploadIndex = $(this).data("index");
                        if (fgDropzone[uploadIndex].files.length > 0) {
                            fgDropzone[uploadIndex].processQueue();
                            submit_form = 0;
                        }
                        uploadIndex++;
                    });
                }
            }
        } else {
            var active_step = $(".step-active").attr("data-step");
            // var requiredFlag = validate(formType,active_step);
            if (!validate(formType, active_step)) {
                return false;
            }

            if ($(".forms-area > .fg-form-group[data-step=" + active_step + "] .fg-dropzone").length > 0) {
                // if(uploadCount == $(".step-active .fg-dropzone").length) {
                //     submit_form = 1;
                // }else{
                if ($(".forms-area > .fg-form-group[data-step=" + active_step + "] .fg-dropzone").length != uploadCount) {
                    $(".forms-area > .fg-form-group[data-step=" + active_step + "] .fg-dropzone").each(function () {
                        var uploadIndex = $(this).data("index");

                        if (fgDropzone[uploadIndex].files.length > 0) {
                            fgDropzone[uploadIndex].processQueue();
                            submit_form = 0;
                        }
                        uploadIndex++;
                    });
                } else {
                    submit_form = 1;
                }
                // }
                // alert("submit_form = "+submit_form);
            }
        }
        // var requiredFlag = true;
        // $(".user-info").find("*[required]").each(function(){
        //     if($(this).val() == ''){
        //         validationError = true;
        //         var errorHtml = '<div class="error-message">This field is required</div>';
        //         $(this).closest(".form-group").append(errorHtml);
        //     }
        // });
        // if(!requiredFlag){
        //     return false;
        // }
        if (submit_form == 1) {
            uploadCount = 0;
            $.ajax({
                type: 'POST',
                url: saveUrl + "?savefrom=" + savefrom,
                data: formJson,
                dataType: "json",
                success: function (response) {
                    if (response.status == true) {
                        if (redirectBack != '') {
                            if (formType != 'single_form') {
                                form_id = response.form_id;
                                var active_step = $(".step-active").attr("data-step");
                                var last_step = $(".forms-area > .fg-form-group").length;
                                // var requiredFlag = validate(formType,active_step);
                                if (!validate(formType, active_step)) {
                                    return false;
                                }
                                if (last_step == active_step) {
                                    window.location.href = redirectBack;
                                }
                                var step = parseInt(active_step) + 1;
                                var last_step = $(".forms-area > .fg-form-group").length;
                                $(".fg-form-group").removeClass("step-active");
                                $(".fg-form-group[data-step=" + step + "]").addClass("step-active");
                                $(".step-count").removeClass("stepno-active");
                                $(".step-count[data-step=" + step + "]").addClass("stepno-active");
                                // $(".step-count[data-step=" + step + "]").find(".stepno").addClass("stepno-active");
                                // $(".stepno").removeClass("stepno-complete");
                                $(".step-count").removeClass("stepno-complete");
                                for (var i = 1; i < step; i++) {
                                    $(".step-count[data-step=" + i + "]").addClass("stepno-complete");
                                    $(".step-count[data-step=" + i + "]").addClass("stepno-complete");
                                }
                                $('html, body').animate({
                                    scrollTop: $(".fgr-render").offset().top
                                }, 1000);

                                $(".prev-btn").show();
                                if (step == last_step) {
                                    $("#fgr-form .next-btn").hide();
                                    $("#fgr-form .finish-btn").show();
                                } else {
                                    $("#fgr-form .next-btn").show();
                                    $("#fgr-form .finish-btn").hide();
                                }
                            } else {
                                window.location.href = redirectBack;
                            }

                        } else {
                            location.reload();
                        }
                    }
                }
            });
        }
    }
    initFgDropzone();
    initFgMap();
}

// $.fn.saveForm = function(param={}) {

//     console.log(formJson);
// }

function renderField(formJson, key) {
    var renderHtml = '';
    if (formJson[key]['fields'] == 'textInput') {
        renderHtml += renderTextInput(formJson[key]['settings'], key);
    }
    if (formJson[key]['fields'] == 'addressInput') {
        renderHtml += renderaddressInput(formJson[key]['settings'], key);
    }
    if (formJson[key]['fields'] == 'fgDropzone') {
        renderHtml += renderfgDropzone(formJson[key]['settings'], key);
    }
    // if(formJson[key]['fields'] == 'fieldGroups'){
    //     renderHtml += renderfieldGroups(formJson[key],formJson);
    // }
    if (formJson[key]['fields'] == 'numberInput') {
        renderHtml += renderNumberInput(formJson[key]['settings'], key);
    }
    if (formJson[key]['fields'] == 'emailInput') {
        renderHtml += renderEmailInput(formJson[key]['settings'], key);
    }
    if (formJson[key]['fields'] == 'textarea') {
        renderHtml += renderTextarea(formJson[key]['settings'], key);
    }
    if (formJson[key]['fields'] == 'url') {
        renderHtml += renderUrlInput(formJson[key]['settings'], key);
    }
    if (formJson[key]['fields'] == 'dropDown') {
        renderHtml += renderDropDown(formJson[key]['settings'], key);
    }
    if (formJson[key]['fields'] == 'checkbox') {
        renderHtml += renderCheckbox(formJson[key]['settings'], key);
    }
    if (formJson[key]['fields'] == 'radio') {
        renderHtml += renderRadio(formJson[key]['settings'], key);
    }
    if (formJson[key]['fields'] == 'dateInput') {
        renderHtml += renderDateInput(formJson[key]['settings'], key);
    }
    return renderHtml;
}
function renderTextInput(settings, index) {
    var stepHeading = settings.stepHeading;
    var stepDescription = settings.stepDescription;
    if ((stepHeading != '' && stepHeading !== undefined) || (stepDescription != '' && stepDescription !== undefined)) {
        var html = '<div class="fg-form-group" data-stephead="' + stepHeading + '" data-stepdesc="' + stepDescription + '">';
    } else {
        var html = '<div class="fg-form-group">';
    }
    var required = '';
    var req_mark = '';
    if (settings.required !== undefined && settings.required == 1) {
        required = 'required';
        req_mark = '<span class="req_mark">*</span>';
    }
    html += '<label>' + settings.label + req_mark + '</label>';
    if (settings.shortDesc != null) {
        html += '<div class="fgr-short-desc">' + settings.shortDesc + '</div>';
    }
    var placeholder = settings.placeholder !== null ? settings.placeholder : '';
    var maxlength = settings.maxlength !== null ? 'maxlength="' + settings.maxlength + '"' : '';

    html += '<input type="text" ' + required + ' ' + maxlength + ' name="fg_field[' + settings.name + ']" data-field="textbox" class="fgr-control ' + settings.name + '" placeholder="' + placeholder + '">';
    html += '</div>';
    return html;
}
function renderaddressInput(settings, index) {
    var stepHeading = settings.stepHeading;
    var stepDescription = settings.stepDescription;
    if ((stepHeading != '' && stepHeading !== undefined) || (stepDescription != '' && stepDescription !== undefined)) {
        var html = '<div class="fg-form-group" data-stephead="' + stepHeading + '" data-stepdesc="' + stepDescription + '">';
    } else {
        var html = '<div class="fg-form-group">';
    }
    var required = '';
    var req_mark = '';
    if (settings.required !== undefined && settings.required == 1) {
        required = 'required';
        req_mark = '<span class="req_mark">*</span>';
    }
    html += '<label>' + settings.label + req_mark + '</label>';
    if (settings.shortDesc != null) {
        html += '<div class="fgr-short-desc">' + settings.shortDesc + '</div>';
    }
    var placeholder = settings.placeholder !== null ? settings.placeholder : '';
    var maxlength = settings.maxlength !== null ? 'maxlength="' + settings.maxlength + '"' : '';
    var txtindex = getRandom(10);
    html += '<input id="address-' + txtindex + '" type="text" ' + required + ' ' + maxlength + ' name="fg_field[' + settings.name + ']" data-field="textbox" class="fgr-control google-address-input ' + settings.name + '" placeholder="' + placeholder + '">';
    html += '</div>';
    return html;
}
function renderfgDropzone(settings, index) {
    var stepHeading = settings.stepHeading;
    var stepDescription = settings.stepDescription;
    if ((stepHeading != '' && stepHeading !== undefined) || (stepDescription != '' && stepDescription !== undefined)) {
        var html = '<div class="fg-form-group" data-stephead="' + stepHeading + '" data-stepdesc="' + stepDescription + '">';
    } else {
        var html = '<div class="fg-form-group">';
    }
    var required = '';
    var req_mark = '';
    if (settings.required !== undefined && settings.required == 1) {
        required = 'required';
        req_mark = '<span class="req_mark">*</span>';
    }
    html += '<label>' + settings.label + req_mark + '</label>';
    if (settings.shortDesc != null) {
        html += '<div class="fgr-short-desc">' + settings.shortDesc + '</div>';
    }

    var txtindex = getRandom(10);

    html += '<div id="dZUpload-' + txtindex + '" data-index="' + txtindex + '" class="dropzone fg-dropzone w-100">';
    html += '<input type="hidden" class="fg-timestamp" name="fg_field[' + settings.name + '][timestamp]" value="' + txtindex + '" />';
    html += '<input type="hidden" class="fg-main-timestamp" name="fg_field[' + settings.name + '][main_timestamp]" value="" />';

    html += '<input id="fg-files-' + txtindex + '" type="hidden" name="fg_field[' + settings.name + ']" data-field="textbox" class="fgr-control fg-files ' + settings.name + '">';
    html += '<div class="dz-default dz-message text-center">';
    html += '<label class="" for="upload-file">';
    html += '<img src="assets/fs-assets/img/empty/upload.svg" alt="">';
    html += '<span class="dy7-font-bold dy7-color-contrast-higher ">Drag and drop your files here</span>';
    html += '<span class="dy7-inline-block dy7-padding-top-3xs dy7-color-contrast-medium dy7-text-sm">or click to browse your files</span>';
    html += '</label>';
    html += '</div>';
    html += '</div>';
    html += '</div>';
    return html;
}

function renderfieldGroups(formJson, key) {
    var fieldGroups = formJson[key];
    var settings = fieldGroups['settings'];
    var groupFields = fieldGroups['groupFields'];
    var stepHeading = settings.stepHeading;
    var stepDescription = settings.stepDescription;
    if ((stepHeading != '' && stepHeading !== undefined) || (stepDescription != '' && stepDescription !== undefined)) {
        var renderHtml = '<div class="fg-form-group" data-stephead="' + stepHeading + '" data-stepdesc="' + stepDescription + '">';
    } else {
        var renderHtml = '<div class="fg-form-group">';
    }
    renderHtml += '<div class="group-head" style="background-color:' + settings.background_color + ';font-size:' + settings.font_size + 'px;color:' + settings.font_color + '">';
    renderHtml += settings.label;
    renderHtml += '</div>';
    for (var i = 0; i < groupFields.length; i++) {
        Object.keys(formJson).forEach(function (k) {
            if (formJson[k]['index'] == groupFields[i]) {
                renderHtml += renderField(formJson, k);
            }
        });

    }
    renderHtml += '</div>';
    return renderHtml;
}

function renderNumberInput(settings, index) {
    var stepHeading = settings.stepHeading;
    var stepDescription = settings.stepDescription;
    if ((stepHeading != '' && stepHeading !== undefined) || (stepDescription != '' && stepDescription !== undefined)) {
        var html = '<div class="fg-form-group" data-stephead="' + stepHeading + '" data-stepdesc="' + stepDescription + '">';
    } else {
        var html = '<div class="fg-form-group">';
    }
    var required = '';
    var req_mark = '';
    var maxlength = settings.maxlength !== null ? 'maxlength="' + settings.maxlength + '"' : '';
    if (settings.required !== undefined && settings.required == 1) {
        required = 'required';
        req_mark = '<span class="req_mark">*</span>';
    }
    html += '<label>' + settings.label + req_mark + '</label>';
    if (settings.shortDesc != null) {
        html += '<div class="fgr-short-desc">' + settings.shortDesc + '</div>';
    }
    var placeholder = settings.placeholder !== null ? settings.placeholder : '';

    html += '<input type="number" ' + required + ' ' + maxlength + ' name="fg_field[' + settings.name + ']" data-field="number" class="fgr-control ' + settings.name + '" placeholder="' + placeholder + '">';
    html += '</div>';
    return html;
}

function renderEmailInput(settings, index) {
    var stepHeading = settings.stepHeading;
    var stepDescription = settings.stepDescription;
    if ((stepHeading != '' && stepHeading !== undefined) || (stepDescription != '' && stepDescription !== undefined)) {
        var html = '<div class="fg-form-group" data-stephead="' + stepHeading + '" data-stepdesc="' + stepDescription + '">';
    } else {
        var html = '<div class="fg-form-group">';
    }
    var required = '';
    var req_mark = '';
    if (settings.required !== undefined && settings.required == 1) {
        required = 'required';
        req_mark = '<span class="req_mark">*</span>';
    }
    html += '<label>' + settings.label + req_mark + '</label>';
    if (settings.shortDesc != null) {
        html += '<div class="fgr-short-desc">' + settings.shortDesc + '</div>';
    }
    var placeholder = settings.placeholder !== null ? settings.placeholder : '';

    html += '<input type="email" ' + required + ' name="fg_field[' + settings.name + ']" data-field="email" class="fgr-control ' + settings.name + '" placeholder="' + placeholder + '">';
    html += '</div>';
    return html;
}
function renderDateInput(settings, index) {
    var stepHeading = settings.stepHeading;
    var stepDescription = settings.stepDescription;
    if ((stepHeading != '' && stepHeading !== undefined) || (stepDescription != '' && stepDescription !== undefined)) {
        var html = '<div class="fg-form-group" data-stephead="' + stepHeading + '" data-stepdesc="' + stepDescription + '">';
    } else {
        var html = '<div class="fg-form-group">';
    }
    var required = '';
    var req_mark = '';
    if (settings.required !== undefined && settings.required == 1) {
        required = 'required';
        req_mark = '<span class="req_mark">*</span>';
    }
    html += '<label>' + settings.label + req_mark + '</label>';
    if (settings.shortDesc != null) {
        html += '<div class="fgr-short-desc">' + settings.shortDesc + '</div>';
    }
    var placeholder = settings.placeholder !== null ? settings.placeholder : '';

    html += '<input type="text" ' + required + ' name="fg_field[' + settings.name + ']" data-field="datepicker" class="fgr-control datepicker ' + settings.name + '" placeholder="' + placeholder + '">';


    html += '</div>';
    return html;
}
function renderUrlInput(settings, index) {
    var stepHeading = settings.stepHeading;
    var stepDescription = settings.stepDescription;
    if ((stepHeading != '' && stepHeading !== undefined) || (stepDescription != '' && stepDescription !== undefined)) {
        var html = '<div class="fg-form-group" data-stephead="' + stepHeading + '" data-stepdesc="' + stepDescription + '">';
    } else {
        var html = '<div class="fg-form-group">';
    }
    var required = '';
    var req_mark = '';
    if (settings.required !== undefined && settings.required == 1) {
        required = 'required';
        req_mark = '<span class="req_mark">*</span>';
    }
    html += '<label>' + settings.label + req_mark + '</label>';
    if (settings.shortDesc != null) {
        html += '<div class="fgr-short-desc">' + settings.shortDesc + '</div>';
    }
    var placeholder = settings.placeholder !== null ? settings.placeholder : '';

    html += '<input type="url" ' + required + ' name="fg_field[' + settings.name + ']" data-field="url" class="fgr-control ' + settings.name + '" placeholder="' + placeholder + '">';
    html += '</div>';
    return html;
}

function renderTextarea(settings, index) {
    var stepHeading = settings.stepHeading;
    var stepDescription = settings.stepDescription;
    if ((stepHeading != '' && stepHeading !== undefined) || (stepDescription != '' && stepDescription !== undefined)) {
        var html = '<div class="fg-form-group" data-stephead="' + stepHeading + '" data-stepdesc="' + stepDescription + '">';
    } else {
        var html = '<div class="fg-form-group">';
    }
    var required = '';
    var req_mark = '';
    var textLimit = settings.textLimit !== undefined ? 'textLimit="' + settings.textLimit + '"' : '';
    var addLength = settings.addLength !== undefined ? 'addLength="' + settings.addLength + '"' : '';

    if (settings.required !== undefined && settings.required == 1) {
        required = 'required';
        req_mark = '<span class="req_mark">*</span>';
    }
    html += '<label>' + settings.label + req_mark + '</label>';
    if (settings.shortDesc != null) {
        html += '<div class="fgr-short-desc">' + settings.shortDesc + '</div>';
    }
    var placeholder = settings.placeholder !== null ? settings.placeholder : '';
    var txtindex = getRandom(10);
    html += '<textarea ' + textLimit + ' ' + addLength + ' ' + required + ' name="fg_field[' + settings.name + ']" class="fgr-control fg-editor ' + settings.name + '" data-field="textarea" id="editor-' + txtindex + '" placeholder="' + placeholder + '"></textarea>';
    html += '</div>';
    return html;
}

function renderDropDown(settings, index) {
    var stepHeading = settings.stepHeading;
    var stepDescription = settings.stepDescription;
    if ((stepHeading != '' && stepHeading !== undefined) || (stepDescription != '' && stepDescription !== undefined)) {
        var html = '<div class="fg-form-group" data-stephead="' + stepHeading + '" data-stepdesc="' + stepDescription + '">';
    } else {
        var html = '<div class="fg-form-group">';
    }
    var required = '';
    var req_mark = '';
    if (settings.required !== undefined && settings.required == 1) {
        required = 'required';
        req_mark = '<span class="req_mark">*</span>';
    }
    html += '<label>' + settings.label + req_mark + '</label>';
    if (settings.shortDesc != null) {
        html += '<div class="fgr-short-desc">' + settings.shortDesc + '</div>';
    }

    html += '<div class="fgr-select-input"><select ' + required + ' class="fgr-select ' + settings.name + '" data-field="dropdown" name="fg_field[' + settings.name + ']">';
    var options = settings.options;
    // html += '<option value=""></option>';
    Object.keys(options).forEach(function (key) {
        html += '<option value="' + options[key] + '">' + options[key] + '</option>';
    });
    html += '</select></div></div>';
    return html;
}

function renderCheckbox(settings, index) {
    var stepHeading = settings.stepHeading;
    var stepDescription = settings.stepDescription;
    if ((stepHeading != '' && stepHeading !== undefined) || (stepDescription != '' && stepDescription !== undefined)) {
        var html = '<div class="fg-form-group" data-stephead="' + stepHeading + '" data-stepdesc="' + stepDescription + '">';
    } else {
        var html = '<div class="fg-form-group">';
    }
    var required = '';
    var req_mark = '';
    if (settings.required !== undefined && settings.required == 1) {
        required = 'required';
        req_mark = '<span class="req_mark">*</span>';
    }
    html += '<label>' + settings.label + req_mark + '</label>';
    if (settings.shortDesc != null) {
        html += '<div class="fgr-short-desc">' + settings.shortDesc + '</div>';
    }
    html += '<div class="fgr-checkbox">';
    var options = settings.options;
    var i = 0;
    Object.keys(options).forEach(function (key) {
        html += '<label for="chk-' + key + '"><input ' + required + ' id="chk-' + key + '" class="' + settings.name + '" name="fg_field[' + settings.name + '][' + i + ']" data-field="checkbox" type="checkbox" value="' + options[key] + '"> <span>' + options[key] + '</span></label>';
        i++;
    });
    html += '</div></div>';
    return html;
}

function renderRadio(settings, index) {
    var stepHeading = settings.stepHeading;
    var stepDescription = settings.stepDescription;
    if ((stepHeading != '' && stepHeading !== undefined) || (stepDescription != '' && stepDescription !== undefined)) {
        var html = '<div class="fg-form-group" data-stephead="' + stepHeading + '" data-stepdesc="' + stepDescription + '">';
    } else {
        var html = '<div class="fg-form-group">';
    }
    var required = '';
    var req_mark = '';
    if (settings.required !== undefined && settings.required == 1) {
        required = 'required';
        req_mark = '<span class="req_mark">*</span>';
    }
    html += '<label>' + settings.label + req_mark + '</label>';
    if (settings.shortDesc != null) {
        html += '<div class="fgr-short-desc">' + settings.shortDesc + '</div>';
    }
    html += '<div class="fgr-radio">';
    var options = settings.options;

    Object.keys(options).forEach(function (key) {
        html += '<label for="chk-' + key + '"><input ' + required + ' id="chk-' + key + '" name="fg_field[' + settings.name + ']" data-field="radio" class="' + settings.name + '" type="radio" value="' + options[key] + '"> <span>' + options[key] + '</span></label>';
    });
    html += '</div></div>';
    return html;
}




function initFgMap() {
    var autocomplete;
    var i = 0;
    setTimeout(() => {
        $(".google-address-input").each(function () {
            var adrs_id = $(this).attr("id");
            autocomplete = new google.maps.places.Autocomplete(
                document.getElementById(adrs_id), {
                types: ['geocode']
            }
            );

            autocomplete.addListener('place_changed', function () {
                var place = autocomplete.getPlace();
                // if (!place.geometry) {
                //     window.alert("No details available for input: '" + place.name + "'");
                //     return;
                // }

                // Extract the city name from the address components
                // var city = '';
                // for (var i = 0; i < place.address_components.length; i++) {
                //     var component = place.address_components[i];
                //     for (var j = 0; j < component.types.length; j++) {
                //         if (component.types[j] === 'locality' || component.types[j] === 'administrative_area_level_2') {
                //             city = component.long_name;
                //             break;
                //         }
                //     }
                // }
                // var addressComponents = place.address_components;

                // // Loop through the address components
                // for (var i = 0; i < addressComponents.length; i++) {
                //     var component = addressComponents[i];
                //     for (var j = 0; j < component.types.length; j++) {
                //         // Check for state
                //         if (component.types[j] === 'administrative_area_level_1') {
                //             state = component.long_name;
                //         }
                //         // Check for country
                //         if (component.types[j] === 'country') {
                //             country = component.long_name;
                //         }
                //         // Check for postal code
                //         if (component.types[j] === 'postal_code') {
                //             postalCode = component.long_name;
                //         }
                //     }
                // }
            });
            i++;
        });
    }, 1000);
}

$(document).ready(function() {
    // Function to update the div based on the selected value
    function updateDiv(selectedValue) {

        var $infoDiv = $("#infoDiv");


        if (selectedValue) {

            if (selectedValue === "step_form") {
    console.log(selectedValue,"as3423sdaf")

                $infoDiv.text("Step Form");
            } else if (selectedValue === "single_form") {
                $infoDiv.text("Single Form");
            }

            // Show the div
            $infoDiv.show();
        } else {
            // Hide the div if no option is selected
            // $infoDiv.hide();
        }
    }

    // Initialize the default text on page load
    var $selectElement = $(".form-select");
    updateDiv($selectElement.val());

    // Update the div when the selection changes
    $selectElement.on("change", function() {
        var selectedValue = $(this).val();
        
        updateDiv(selectedValue);
    });
});