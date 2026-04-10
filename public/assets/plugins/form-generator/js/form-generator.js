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

var fgDropzone = [];
var uploadCount = 0;
// Generate Form
$.fn.formGenerator = function (param = {}) {
    var loader = "<div class='fg-loader'></div>";
    // this.html(loader);
    if (param.previewUrl != undefined) {
        previewUrl = param.previewUrl;
    }
    if (param.saveUrl !== undefined) {
        saveUrl = param.saveUrl;
    }
    if (param.formName !== undefined) {
        formName = param.formName;
    }
    if (param.formType !== undefined) {
        formType = param.formType;
    } else {
        formType = 'step_form';
    }
    if (param.redirectBack !== undefined) {
        redirectBack = param.redirectBack;
    }
    this.addClass("fg-container  cds-form-container-assessment");
    var form_index = getRandom(10);
    var leftHtml = '<div class="cds-form-container-assessment-body-left-panel hide-left">';
    // leftHtml += '<div class="cds-form-container-assessment-field-box-header">';
    // leftHtml += '<div class="cds-form-container-assessment-field-box-header-titlebar"><h4>Form Fields</h4></div>';
    // leftHtml += '<div class="cds-form-container-assessment-field-box-header-toolbar"><button type="button" class="cds-component-add-button-main"><i class="fa fa-plus"></i></button>';
    // leftHtml += '</div>';
    // leftHtml += '<div class="cds-form-container-assessment-field-box-body"><div class="modal fade" tabindex="-1" id="cds-component-model"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><h3 class="modal-title">Form Fields <span class="field-subititle"></span></h3><div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close"><span class="svg-icon svg-icon-1"></span></div></div><div class="modal-body"><ul class="form-fields">';
    // $.map(formFields, function (val, key) {
    //     leftHtml += '<li data-field="' + val.type + '" data-label="' + val.label + '" class="fg-field">';
    //     leftHtml += '<div class="fg-field-text ' + val.icon_class + '">' + val.label + '</div>';
    //     leftHtml += '<button type="button" title="Click to Add" class="fg-btn-add"><i class="fa fa-plus"></i></button>';
    //     leftHtml += '</li>';
    // });
    // leftHtml += '</ul></div><div class="modal-footer"><button type="button" class="btn btn-light close" data-bs-dismiss="modal">Close</button></div></div></div></div></div>';
    // leftHtml += '</div>';
    leftHtml += '<div class="field-list">';
    leftHtml += '<div class="cds-form-container-assessment-field-box-header">';
    leftHtml += '<div class="cds-form-container-assessment-field-box-header-toolbar"><h4>Form Fields</h4><button type="button" class="cds-component-expand-button-main cds-component-add-button-main "></button><button type="button" class="cds-component-expand-button-main close-panel"></button></div>';
    leftHtml += '<div class="cds-form-container-assessment-field-box-header-toolbar">';
    leftHtml += '</div>';
    leftHtml += '<ul class="input-fields form-fields">';
    $.map(formFields, function (val, key) {
        leftHtml += '<li data-field="' + val.type + '" data-label="' + val.label + '" data-icon="' + val.icon_class + '" class="fg-field">';
        leftHtml += '<div class="fg-field-text"><span class="' + val.icon_class + ' fg-btn-add icon-span">&nbsp;</span><span class="input-label">' + val.label + '</span></div>';
        leftHtml += '<button type="button" title="Click to Add" class="fg-btn-add"></button>';
        leftHtml += '</li>';
    });
    leftHtml += '</ul>';
    leftHtml += '</div>';
    leftHtml += '</div>';
    leftHtml += '<div class="selected-fields">';
    leftHtml += '<ul class="fg-selected-fields ' + ((formType == 'step_form') ? 'step-fields' : '') + ' sortable-list"><li class="disable-sort-item" style="visibility:hidden"><div>Item ' + getRandom(9) + '</div></li></ul>'
    leftHtml += '</div>';
    leftHtml += '</div>';
    var mainHtml = '<div class="cds-form-container-assessment-body-main-panel-wrapper"><div class="cds-form-container-assessment-body-main-panel">' + loader + '</div></div>';
    var rightHtml = '<div class="cds-form-container-assessment-body-right-panel"></div>';
    var form_index = getRandom(10);
    var form = '<form class="fg-form" method="post" id="fg-form-' + form_index + '"><div class="cds-form-container-assessment-view">';
    form += '<div class="fg-group cds-form-container-assessment-header">';
    form += '<div class="cds-form-container-assessment-header-right"><div class="cds-form-container-titlebar"><label>Unique Form Name</label>';
    form += '<input type="text" class="form-control" value="' + formName + '" placeholder="Enter Form Name" name="form_name" oninput="validateHtmlTags(this)"/></div>';
    form += '<div class="cds-form-container-selectbar"><label>Select Form Type</label>';
    form += '<select class="form-select" name="form_type">';
    form += '<option disabled value="">Select Form Type</option>';
    form += '<option ' + ((formType == 'step_form') ? 'selected' : '') + ' value="step_form">Step Form</option>';
    form += '<option ' + ((formType == 'single_form') ? 'selected' : '') + ' value="single_form">Single Form</option>';
    form += '</select>';
    form += '</div>';
    form += '</div><div class="cds-form-container-assessment-header-left">';
    if (previewUrl != '') {
        form += '<button type="button" class="cds-component-preview-btn" data-href="' + previewUrl + '" title="Preview">Preview</button>'
    }
    form += '<button type="button" class="cds-component-save-button"><i class="fa fa-save"></i> Save Form</button>'
    form += '</div>';

    form += '</div>';
    form += '</div>';
    form += '<div class="cds-form-container-assessment-field-box-header-titlebar"><h4>Form Fields</h4><div id="infoDiv"></div></div>';

    form += '<div class="cds-form-container-assessment-body"></div>';
    form += '</div></form>';

    this.append(form);
    $("#fg-form-" + form_index + " .cds-form-container-assessment-body").append(leftHtml);
    $("#fg-form-" + form_index + " .cds-form-container-assessment-body").append(mainHtml);
    $("#fg-form-" + form_index + " .cds-form-container-assessment-body").append(rightHtml);
    if (param.defaultJson === undefined) {
        $(".fg-loader").remove();
    }
    $(function () {
        $(".fg-selected-fields").sortable({
            items: 'li',
            sort: function (e) {
                $(".step-fields").addClass("drag-start");
            },
            start(event, ui) {
                $(".step-fields").removeClass("drag-start");;
                var has_class = ui.item.hasClass("droppable");
                if (has_class) {
                    $(".fg-field-block .sortable-list").hide();
                } else {
                    $(".fg-field-block .sortable-list").show();
                }
            },
            stop(event, ui) {
                $(".step-fields").removeClass("drag-start");;
                $(".fg-field-block .sortable-list").show();
            },
            update: function () {
                setTimeout(function () {
                    $(".fg-selected-fields > .fg-field-block").each(function () {
                        var ee = $(this);
                        var fieldType = $(ee).data("type");
                        var fieldIndex = $(ee).data("index");
                        if (fieldType == 'fieldGroups') {

                            if ($(ee).find(".sortable-list > .fg-field-block").length > 0) {
                                $(ee).find(".group-fields").html('');
                                var indx = 0;
                                $(ee).find(".sortable-list > .fg-field-block").not(".disable-sort-item").each(function () {
                                    var eee = $(this);
                                    var subIndex = $(eee).data("index");
                                    var inputfield = '<input type="hidden" class="field-input" name="fg_fields[' + fieldIndex + '][groupFields][' + indx + ']" value="' + subIndex + '" />';
                                    $(ee).find(".group-fields").append(inputfield);
                                    indx++;
                                });
                            }
                        }
                    });
                }, 1500);
            },
            cancel: ".disable-sort-item",
            toleranceElement: '> div'
        });
    });
    $(document).on("change", ".form-select", function () {
        if ($(this).val() == 'step_form') {
            // $(".cds-form-container-assessment-field-box-header-titlebar").append('<p class="step-text">Step Form</p>')
            $(".step-info-field").show();
            $(".fg-selected-fields").addClass("step-fields");
        } else {
            console.log("sdfasd")
            $(".step-info-field").hide();
            $(".fg-selected-fields").removeClass("step-fields");
            // $(".cds-form-container-assessment-field-box-header-titlebar").append('<p class="step-text">Single Form</p>')

        }
    });
    $(document).on("click", ".remove-field", function () {
        if (!confirm("Are you sure to remove field")) {
            return false;
        }
        var e = $(this);
        var index = $(this).parents(".fg-field-block").data("index");
        var type = $(this).parents(".fg-field-block").data("type");
        if (type == 'fieldGroups') {
            $(this).parents(".fg-field-block").find(".sortable-list .fg-field-block").each(function () {
                var subindex = $(this).data("index");
                $(".fg-field-area[data-index=" + subindex + "],.setting-area[data-index=" + subindex + "]").remove();
            });
            setTimeout(function () {
                $(".fg-field-area[data-index=" + index + "],.setting-area[data-index=" + index + "]").remove();
                $(e).parents(".fg-field-block").remove();
            }, 350);
        } else {
            $(".fg-field-block[data-index=" + index + "]").closest(".fg-field-block").remove();
            $(".fg-field-area[data-index=" + index + "],.setting-area[data-index=" + index + "]").remove();

        }
    });
    $(document).on("click", ".copy-field", function () {
        if (!confirm("Are you sure to copy field")) {
            return false;
        }
        var field = $(this).data("field");

        if (field == 'fieldGroups') {
            var clone_field = $(this).parents(".droppable").clone();
            var old_index = $(this).parents(".droppable").data("index");
            $(".step-fields").append(clone_field);
            var new_index = getRandom(9);
            $(".step-fields > .fg-field-block:last-child").attr("id", "fg-" + new_index);
            $(".step-fields > .fg-field-block:last-child").attr("data-index", new_index);

            $("#fg-" + new_index).insertAfter($("#fg-" + old_index));

            var targetString = old_index;
            var replacementString = new_index;

            var element = $("#fg-" + new_index);
            var modifiedHtml = element.html().replace(targetString, replacementString);
            element.html(modifiedHtml);


            // field area clone

            var clone_field_area = $("#fga-" + old_index).clone();
            clone_field_area.attr("id", "fga-" + new_index);
            clone_field_area.attr("data-index", new_index);

            $(clone_field_area).insertAfter($("#fga-" + old_index));
            var element = $("#fga-" + new_index);
            var modifiedHtml = element.html().replace(targetString, replacementString);
            element.html(modifiedHtml);

            // field setting clone

            var clone_field_setting = $(".setting-area[data-index=" + old_index + "]").clone();
            clone_field_setting.attr("data-index", new_index);

            $(clone_field_setting).insertAfter($(".setting-area[data-index=" + old_index + "]"));
            var element = $(".setting-area[data-index=" + new_index + "]");
            var modifiedHtml = element.html().replace(targetString, replacementString);
            element.html(modifiedHtml);

            $(".setting-area[data-index=" + old_index + "]").find(".fg-setting-field").each(function (index) {
                var inp_name = $(this).attr("name");
                inp_name = inp_name.replace(old_index, new_index);
                var inputValue = $(this).val();
                $(".setting-area[data-index=" + new_index + "]").find(".fg-setting-field").eq(index).attr("name", inp_name);
                var tag = $(this).get(0).tagName;
                if (tag == 'INPUT') {
                    if ($(this).attr("type") == 'checkbox') {
                        if ($(this).is(":checked")) {
                            $(".setting-area[data-index=" + new_index + "]").find(".fg-setting-field").eq(index).prop("checked", true);
                        }
                    } else {
                        $(".setting-area[data-index=" + new_index + "]").find(".fg-setting-field").eq(index).val(inputValue);
                    }
                } else {
                    $(".setting-area[data-index=" + new_index + "]").find(".fg-setting-field").eq(index).val(inputValue);
                }
            });

            var parent_new_index = new_index;
            $("#fg-" + parent_new_index + " .sortable-list li").remove();

            $("#fg-" + old_index + " .sortable-list .disable-sort-item").each(function () {
                var clone_field = $(this).clone();
                $("#fg-" + parent_new_index + " .sortable-list").append(clone_field);
            });
            $("#fg-" + old_index + " .sortable-list .fg-field-block").each(function () {

                var li_old_index = $(this).data("index");

                // field input clone 
                var clone_field = $(this).clone();
                var li_new_index = getRandom(9);

                clone_field.attr("data-index", li_new_index);
                clone_field.attr("id", "fg-" + li_new_index);

                var targetString = li_old_index;
                var replacementString = li_new_index;

                $("#fg-" + parent_new_index + " .sortable-list").append(clone_field);
                var element = $("#fg-" + li_new_index);

                var modifiedHtml = element.html().replace(targetString, replacementString);
                element.html(modifiedHtml);


                // field area clone

                var clone_field_area = $("#fga-" + li_old_index).clone();
                clone_field_area.attr("id", "fga-" + li_new_index);
                clone_field_area.attr("data-index", li_new_index);

                $(clone_field_area).insertAfter($("#fga-" + li_old_index));
                var element = $("#fga-" + li_new_index);
                var modifiedHtml = element.html().replace(targetString, replacementString);
                element.html(modifiedHtml);


                // field setting clone

                var clone_field_setting = $(".setting-area[data-index=" + li_old_index + "]").clone();
                clone_field_setting.attr("data-index", li_new_index);

                $(clone_field_setting).insertAfter($(".setting-area[data-index=" + li_old_index + "]"));
                var element = $(".setting-area[data-index=" + li_new_index + "]");
                var modifiedHtml = element.html().replace(targetString, replacementString);
                element.html(modifiedHtml);

                $(".setting-area[data-index=" + li_old_index + "]").find(".fg-setting-field").each(function (index) {
                    var inp_name = $(this).attr("name");
                    inp_name = inp_name.replace(li_old_index, li_new_index);
                    var inputValue = $(this).val();
                    $(".setting-area[data-index=" + li_new_index + "]").find(".fg-setting-field").eq(index).attr("name", inp_name);
                    var tag = $(this).get(0).tagName;
                    if (tag == 'INPUT') {
                        if ($(this).attr("type") == 'checkbox') {
                            if ($(this).is(":checked")) {
                                $(".setting-area[data-index=" + li_new_index + "]").find(".fg-setting-field").eq(index).prop("checked", true);
                            }
                        } else {
                            $(".setting-area[data-index=" + li_new_index + "]").find(".fg-setting-field").eq(index).val(inputValue);
                        }
                    } else {
                        $(".setting-area[data-index=" + li_new_index + "]").find(".fg-setting-field").eq(index).val(inputValue);
                    }
                });
            });


        } else {
            var old_index = $(this).parents(".fg-field-block[data-type=" + field + "]").data("index");
            // field input clone 
            var clone_field = $(this).parents(".fg-field-block[data-type=" + field + "]").clone();
            var new_index = getRandom(9);
            clone_field.attr("data-index", new_index);
            clone_field.attr("id", "fg-" + new_index);

            var targetString = old_index;
            var replacementString = new_index;

            $(clone_field).insertAfter($(this).parents(".fg-field-block[data-type=" + field + "]"));
            var element = $("#fg-" + new_index);
            var modifiedHtml = element.html().replace(targetString, replacementString);
            element.html(modifiedHtml);


            // field area clone

            var clone_field_area = $("#fga-" + old_index).clone();
            clone_field_area.attr("id", "fga-" + new_index);
            clone_field_area.attr("data-index", new_index);

            $(clone_field_area).insertAfter($("#fga-" + old_index));
            var element = $("#fga-" + new_index);
            var modifiedHtml = element.html().replace(targetString, replacementString);
            element.html(modifiedHtml);

            // field setting clone

            var clone_field_setting = $(".setting-area[data-index=" + old_index + "]").clone();
            clone_field_setting.attr("data-index", new_index);

            $(clone_field_setting).insertAfter($(".setting-area[data-index=" + old_index + "]"));
            var element = $(".setting-area[data-index=" + new_index + "]");
            var modifiedHtml = element.html().replace(targetString, replacementString);
            element.html(modifiedHtml);

            $(".setting-area[data-index=" + old_index + "]").find(".fg-setting-field").each(function (index) {
                var inp_name = $(this).attr("name");
                inp_name = inp_name.replace(old_index, new_index);
                var inputValue = $(this).val();
                $(".setting-area[data-index=" + new_index + "]").find(".fg-setting-field").eq(index).attr("name", inp_name);
                var tag = $(this).get(0).tagName;
                if (tag == 'INPUT') {
                    if ($(this).attr("type") == 'checkbox') {
                        if ($(this).is(":checked")) {
                            $(".setting-area[data-index=" + new_index + "]").find(".fg-setting-field").eq(index).prop("checked", true);
                        }
                    } else {
                        $(".setting-area[data-index=" + new_index + "]").find(".fg-setting-field").eq(index).val(inputValue);
                    }
                } else {
                    $(".setting-area[data-index=" + new_index + "]").find(".fg-setting-field").eq(index).val(inputValue);
                }
            });
        }

        $(".fg-selected-fields > .fg-field-block").each(function () {
            var ee = $(this);
            var fieldType = $(ee).data("type");
            var fieldIndex = $(ee).data("index");
            if (fieldType == 'fieldGroups') {

                if ($(ee).find(".sortable-list > .fg-field-block").length > 0) {
                    $(ee).find(".group-fields").html('');
                    var indx = 0;
                    $(ee).find(".sortable-list > .fg-field-block").not(".disable-sort-item").each(function () {
                        var eee = $(this);
                        var subIndex = $(eee).data("index");
                        var inputfield = '<input type="hidden" class="field-input" name="fg_fields[' + fieldIndex + '][groupFields][' + indx + ']" value="' + subIndex + '" />';
                        $(ee).find(".group-fields").append(inputfield);
                        indx++;
                    });
                }
            }
        });
        $("#fg-" + new_index).find(".fg-field-block-label").trigger("click");

    });
    $(document).on("click", ".cds-component-add-button,.fg-btn-add", function () {
        var index = getRandom(9);
        var fieldType = $(this).parents('.fg-field').data("field");
        var label = $(this).parents('.fg-field').data("label");
        var icon = $(this).parents('.fg-field').data("icon");
        console.log(icon,"aa")
        addFields(index, fieldType, label, icon);
    });

    $(document).on("click", ".cds-component-preview-btn", function () {
        var url = $(this).data("href");
        showPopup(url);
    });
    $(document).on("click", ".cds-component-save-button", function () {
        $(".fg-error").remove();

        if ($(".fg-selected-fields").find(".fg-field-block").length == 0) {
            alert("Please select the fields to save");
        } else {
            if ($(".form-name").val() == '') {
                $(".form-name").parents(".fg-group").append('<div class="fg-error">The field is mandatory</div>');
                return false;
            }
            $(".fg-field-block").removeClass("fg-error-field");
            var formJson = convertFormToJSON($(".fg-form"));
            var validate = 1;
            $(".fg-options").each(function () {
                if ($(this).val() == '') {
                    validate = 0;
                    var index = $(this).parents(".fg-field-area").data("index");
                    $(this).parents(".option-value").append('<div class="fg-error">The field is mandatory</div>');
                    $(".fg-field-block[data-index=" + index + "]").addClass("fg-error-field");
                }
            });
            $(".step-fields > li[data-type=fieldGroups]").each(function () {
                var index = $(this).data("index");

                if ($(this).find(".sortable-list > li").length <= 1) {

                    $(".fg-field-block[data-index=" + index + "]").addClass("fg-error-field");
                    var html = '<div class="fg-error">Field group required some fields in it.</div>';
                    $(".fg-field-area[data-index=" + index + "]").append(html);
                    validate = 0;
                }
            })
            if (validate == 0) {
                return false;
            }
            $(this).attr("disabled", "disabled");
            $.ajax({
                type: 'POST',
                url: saveUrl,
                data: formJson,
                dataType: "json",
                success: function (response) {
                    $(this).removeAttr("disabled");
                    if (response.status == true) {
                        if (redirectBack != '') {
                            window.location.href = redirectBack;
                        } else {
                            if (response.redirect_back !== undefined) {
                                window.location.href = response.redirect_back;
                            } else {
                                location.reload();
                            }
                        }
                    }
                }
            });
        }
    });
    $(document).on("click", ".cds-component-expand-button-main", function () {
        $(".cds-form-container-assessment-body-left-panel").toggleClass("hide-left");
    });
    $(document).on("click", ".btn-setting", function () {
        $(".cds-form-container-assessment-body-right-panel").slideToggle();
    });
    $(document).on("click", ".close-setting", function () {
        $(".cds-form-container-assessment-body-right-panel").slideUp();

    });
    
    

    $(document).on("click", ".cds-component-add-button-main", function () {
        $('#cds-component-model').modal('toggle');
        var ee = $(".fg-field-block.active");
        $(".field-subititle").html('');
        if ($(ee).data("type") == 'fieldGroups') {
            var subtitle = $(".fg-field-block.active > .fg-label > .fg-field-block-label > span").text().trim();
            $(".field-subititle").html("<small>(" + subtitle + ")</small>");
        }
    });
    $(document).on("click", ".close", function () {
        $('#cds-component-model').modal('hide');
    });

    // $('.close').click();   

    $(document).on("click", ".fg-field-block", function () {
        var fieldType = $(this).parents(".fg-field-block").data("type");
        var index = $(this).data("index");
        var form_select = $(".form-select").val();
        if (form_select != 'step_form') {
            $(".setting-area[data-index=" + index + "]").find(".step-info-field").hide();
        } else {
            $(".setting-area[data-index=" + index + "]").find(".step-info-field").show();

            if ($(this).parents(".fg-field-block").data("index") !== undefined) {
                var i = $(this).parents(".fg-field-block").data("index");
                $(".fs-settings[data-index=" + index + "]").find(".step-info-field").hide();
            } else {
                $(".fs-settings[data-index=" + index + "]").find(".step-info-field").show();
            }
        }

    });
    $(document).on("click", ".step-fields > li > span:first-child,.step-fields > li > .label-bottom", function () {
        $(this).parent(".fg-field-block").find(".fieldGroups").trigger("click");
    });
    $(document).on("click", ".fg-field-block .sortable-list > li > span:first-child,.fg-field-block .sortable-list .label-bottom", function () {
        $(this).parent(".fg-field-block").find(".fg-field-block-label").trigger("click");
    });
    $(document).on('click', function (event) {
        const parentElement = $('.cds-form-container-assessment-body-left-panel');

        // Check if the clicked element is the parent or its children
        if (!parentElement.is(event.target) && parentElement.has(event.target).length === 0) {
            // If the clicked element is outside the parent, remove the class
            $(".fg-field-block").removeClass('active');
        }
    });
    if ($(".cds-form-container-assessment-body-main-panel").children().length == 0) {
        $(".cds-form-container-assessment-body-main-panel").append('<div class="no-data-message">No data available</div>');
    }
    $(document).on("click", ".fg-field-block-label", function () {
        $(".fg-field-block").removeClass("active");
        $(this).closest("li").addClass("active");

        var fieldType = $(this).parents(".fg-field-block").data("type");

        var label = $(this).parents(".fg-field-block").data("label");
        var icon = $(this).parents(".fg-field-block").data("icon");
        var index = $(this).parents(".fg-field-block").data("index");
        console.log(icon,"asda")
        html = '<div id="fga-' + index + '" data-type="' + fieldType + '" data-index="' + index + '" class="fg-field-area">';
        // html += actionBtn;
        html += '<div class="setting-btn">';
        html += '<button type="button" class="btn-setting"><span class="setting-icon"></span></button>';
        html += '</div>';
        html += '<div class="right-head-block"><p class="' + icon + '"></p><label class="fg-label editable-label" id="editable-' + index + '" placeholder="Input Label..." contenteditable="true"><span>' + label + '</span></label></div>';
        // html += '<label class="fg-label editable-label" id="editable-' + index + '" placeholder="Input Label..." contenteditable="true"><span>' + label + '</span></label>';
        html += '<p class="fg-description editable-desc" id="description-' + index + '" contenteditable="true" placeholder="Description..."></p>';
        if (fieldType == 'textInput') {
            html += '<input type="text" class="fg-control" />';
        }
        if (fieldType == 'addressInput') {
            html += '<input type="text" class="fg-control google-address" />';
        }
        if (fieldType == 'fgDropzone') {
            html += '<input type="text" class="fg-control fg-dropzone" />';
        }
        if (fieldType == 'numberInput') {
            html += '<input type="number" class="fg-control" />';
        }

        if (fieldType == 'emailInput') {
            html += '<input type="email" class="fg-control" />';
        }

        if (fieldType == 'url') {
            html += '<input type="url" class="fg-control" />';
        }

        if (fieldType == 'textarea') {
            html += '<textarea type="url" class="fg-control"></textarea>';
        }

        if (fieldType == 'dateInput') {
            html += '<input type="text" class="fg-control datepicker" />';
        }

        if (fieldType == 'checkbox') {
            var rndindex = getRandom(5);
            html += '<div class="multiple-options"><div class="option-value"><input type="text" name="fg_fields[' + index + '][settings][options][' + rndindex + ']" class="fg-control fg-options" placeholder="Enter Option Value"><a href="javascript:;" class="remove-choice"><i class="fa fa-times"></i></a></div></div>';
            html += '<div class="choice-action"><button type="button" class="add-choice"><i class="fa fa-plus"></i> Add Options</button></div>';
        }

        if (fieldType == 'radio') {
            var rndindex = getRandom(5);
            html += '<div class="multiple-options"><div class="option-value"><input type="text" name="fg_fields[' + index + '][settings][options][' + rndindex + ']" class="fg-control fg-options" placeholder="Enter Option Value"><a href="javascript:;" class="remove-choice"><i class="fa fa-times"></i></a></div></div>';
            html += '<div class="choice-action"><button type="button" class="add-choice"><i class="fa fa-plus"></i> Add Options</button></div>';
        }
        if (fieldType == 'dropDown') {
            var rndindex = getRandom(5);
            html += '<div class="multiple-options"><div class="option-value"><input type="text" name="fg_fields[' + index + '][settings][options][' + rndindex + ']" class="fg-control fg-options" placeholder="Enter Option Value"><a href="javascript:;" class="remove-choice"><i class="fa fa-times"></i></a></div></div>';
            html += '<div class="choice-action"><button type="button" class="add-choice"><i class="fa fa-plus"></i> Add Options</button></div>';
        }
        html += '</div>';
        $(".cds-form-container-assessment-body-main-panel").find(".fg-field-area").hide();
       
        if ($(".cds-form-container-assessment-body-main-panel").find(".fg-field-area[data-index=" + index + "]").length == 0) {
      

            $(".cds-form-container-assessment-body-main-panel").find(".no-data-message").remove();

            $(".cds-form-container-assessment-body-main-panel").append(html);
            $(".cds-form-container-assessment-body-main-panel").find(".fg-loader").remove();
            $(".cds-form-container-assessment-body-main-panel").find(".fg-field-area[data-index=" + index + "]").show();
        } else {
            $(".cds-form-container-assessment-body-main-panel").find(".fg-field-area[data-index=" + index + "]").show();
            console.log("something")

        }
        var fieldSettings = '<div class="setting-area" data-index="' + index + '">';
        fieldSettings += '<div class="right-head">';
        fieldSettings += '<h4 class="fg-head">Field Settings</h4><button type="button" class="close-setting"></button>';
        fieldSettings += '</div>';
        fieldSettings += '<div class="fs-settings" data-index="' + index + '">';
        // console.log("ATTRS:");
        // console.log(attributes);
        if (fieldType != 'fieldGroups') {
            $.map(attributes['default'], function (val, key) {
                var cls = '';
                if (val.field !== undefined && val.field == 'hidden') {
                    cls = 'fg-hide';
                }
                var form_select = $(".form-select").val();
                if (val.type == 'stepHeading' || val.type == 'stepDescription') {
                    if (form_select == 'step_form') {
                        cls += ' step-info-field';
                    } else {
                        cls += ' default-hide step-info-field';
                    }
                }
                fieldSettings += '<div class="fg-field-setting ' + cls + '">';
                fieldSettings += '<label class="fs-label">' + val.label + '</label>';
                if (val.field !== undefined) {
                    var defaultValue = '';
                    if (val.field == 'checkbox') {
                        fieldSettings += ' <input type="checkbox" value="1" name="fg_fields[' + index + '][settings][' + val.type + ']" data-fieldtype="' + val.type + '" class="fg-setting-field" />';
                    }
                    if (val.value !== undefined && val.value != '') {
                        defaultValue = val.value;
                    }

                    if (val.field == 'hidden') {
                        if (val.type == 'label') {
                            defaultValue = label;
                        }
                        if (val.value !== undefined && val.value != '') {
                            defaultValue = val.value;
                        }
                        if (val.type == 'name') {
                            defaultValue = "fg_" + getRandom(5);
                        }
                        fieldSettings += '<input type="hidden" value="' + defaultValue + '" name="fg_fields[' + index + '][settings][' + val.type + ']" data-fieldtype="' + val.type + '" class="fg-control fg-setting-field" />';
                    }
                    if (val.field == 'color') {
                        fieldSettings += '<input type="color" value="' + defaultValue + '" name="fg_fields[' + index + '][settings][' + val.type + ']" data-fieldtype="' + val.type + '" class="fg-control fg-setting-field" />';
                    }

                    if (val.field == 'dropdown') {
                        if (val.value !== undefined && val.value != '') {
                            defaultValue = val.value;
                        }
                        fieldSettings += '<select name="fg_fields[' + index + '][settings][' + val.type + ']" data-fieldtype="' + val.type + '" class="fg-control fg-setting-field">';
                        var values = val.options;
                        $.map(values, function (val, key) {
                            var sel = (defaultValue == val) ? 'selected' : '';
                            fieldSettings += '<option ' + sel + ' value="' + val + '">' + key + '</option>';
                        });
                        fieldSettings += '</select>';
                    }
                } else {
                    var defaultValue = '';
                    if (val.value !== undefined && val.value != '') {
                        defaultValue = val.value;
                    }
                    fieldSettings += '<input type="text" value="' + defaultValue + '" name="fg_fields[' + index + '][settings][' + val.type + ']" data-fieldtype="' + val.type + '" class="fg-control fg-setting-field" />';
                }
                fieldSettings += '</div>';
            });
        }
        if (typeof attributes[fieldType] !== undefined) {
            $.map(attributes[fieldType], function (val, key) {
                var cls = '';

                if (val.field !== undefined && val.field == 'hidden') {
                    cls = 'fg-hide';
                }
                if (val.field !== undefined && val.field == 'hidden') {
                    cls = 'fg-hide';
                }
                if (val.type == 'stepHeading' || val.type == 'stepDescription') {
                    cls += ' default-hide step-info-field';
                }
                var defaultValue = '';
                fieldSettings += '<div class="fg-field-setting ' + cls + '">';
                fieldSettings += '<label class="fs-label">' + val.label + '</label>';
                if (val.value !== undefined && val.value != '') {
                    defaultValue = val.value;
                }
                if (val.field !== undefined) {
                    if (val.field == 'checkbox') {
                        fieldSettings += ' <input type="checkbox" value="1" name="fg_fields[' + index + '][settings][' + val.type + ']" data-fieldtype="' + val.type + '" class="fg-setting-field" />';
                    }
                    if (val.field == 'hidden') {

                        if (val.type == 'label') {
                            defaultValue = label;
                        }
                        fieldSettings += '<input type="hidden" value="' + defaultValue + '" name="fg_fields[' + index + '][settings][' + val.type + ']" data-fieldtype="' + val.type + '" class="fg-control fg-setting-field" />';
                    }
                    if (val.field == 'color') {
                        fieldSettings += '<input type="color" value="' + defaultValue + '" name="fg_fields[' + index + '][settings][' + val.type + ']" data-fieldtype="' + val.type + '" class="fg-control fg-setting-field" />';
                    }
                    if (val.field == 'number') {
                        fieldSettings += '<input type="number" value="' + defaultValue + '" name="fg_fields[' + index + '][settings][' + val.type + ']" data-fieldtype="' + val.type + '" class="fg-control fg-setting-field" />';
                    }
                    if (val.field == 'dropdown') {

                        fieldSettings += '<select name="fg_fields[' + index + '][settings][' + val.type + ']" data-fieldtype="' + val.type + '" class="fg-control fg-setting-field">';
                        var values = val.options;
                        $.map(values, function (val, key) {
                            var sel = (defaultValue == val.value) ? 'selected' : '';
                            fieldSettings += '<option ' + sel + ' value="' + val.value + '">' + val.label + '</option>';
                        });
                        fieldSettings += '</select>';
                    }

                } else {
                    var defaultValue = '';
                    if (val.value !== undefined && val.value != '') {
                        defaultValue = val.value;
                    }
                    fieldSettings += '<input type="text" value="' + defaultValue + '" name="fg_fields[' + index + '][settings][' + val.type + ']" data-fieldtype="' + val.type + '" class="fg-control fg-setting-field" />';
                }
                // fieldSettings += '<input type="text" data-fieldtype="'+val.type+'" name="fg_fields['+index+'][settings]['+val.type+']" class="fg-control" />';
                fieldSettings += '</div>';
            });
        }
        fieldSettings += '</div></div>';
        $(".cds-form-container-assessment-body-right-panel").find(".setting-area").hide();
        if ($(".cds-form-container-assessment-body-right-panel").find(".setting-area[data-index=" + index + "]").length == 0) {
            $(".cds-form-container-assessment-body-right-panel").append(fieldSettings);
            $(".cds-form-container-assessment-body-right-panel").find(".setting-area[data-index=" + index + "]").show();
        } else {
            $(".cds-form-container-assessment-body-right-panel").find(".setting-area[data-index=" + index + "]").show();
        }
        $(".cds-form-container-assessment-body-right-panel").find("select[data-fieldtype=textLimit]").trigger("change");
    });
    $(document).on("change", ".fg-setting-field", function () {
        var fieldtype = $(this).data("fieldtype");
        if (fieldtype == 'label') {
            var fg_index = $(this).parents(".fs-settings").data("index");
            $("#fg-" + fg_index).find("#editable-" + fg_index).html($(this).val());
        }

        if (fieldtype == 'placeholder') {
            var fg_index = $(this).parents(".fs-settings").data("index");
            $("#fg-" + fg_index).find(".fg-control").attr("placeholder", $(this).val());
        }

        if (fieldtype == 'class') {
            var fg_index = $(this).parents(".fs-settings").data("index");
            $("#fg-" + fg_index).find(".fg-control").addClass("placeholder", $(this).val());
        }
    });
    $(document).on("change", "select[data-fieldtype=textLimit]", function () {
        var value = $(this).val();
        if (value == "none") {
            $(this).parents('.fs-settings').find("input[data-fieldtype=addLength]").parents(".fg-field-setting").hide();
            $(this).parents('.fs-settings').find("input[data-fieldtype=addLength]").parents(".fg-field-setting").find("input").val('');
        } else {
            $(this).parents('.fs-settings').find("input[data-fieldtype=addLength]").parents(".fg-field-setting").show();
        }
    });
    $('body').on('focus', '[contenteditable]', function () {
        const $this = $(this);
        $this.data('before', $this.html());
    }).on('blur keyup paste input', '[contenteditable]', function () {
        const $this = $(this);
        if ($this.data('before') !== $this.html()) {
            $this.data('before', $this.html());
            $this.trigger('change');

            var text = $this.text();
            var fg_index = $this.parents(".fg-field-area").data("index");
            if ($this.hasClass("editable-label")) {
                $(".fs-settings[data-index=" + fg_index + "]").find(".fg-control[data-fieldtype=label]").val(text);
                $(".fg-field-block[data-index=" + fg_index + "] > .fg-label .fg-field-block-label span").html(text);
            }

            if ($this.hasClass("editable-desc")) {
                $(".fs-settings[data-index=" + fg_index + "]").find(".fg-control[data-fieldtype=shortDesc]").val(text);
            }
        }

    });

    $(document).on("click", ".add-choice", function () {
        var index = $(this).parents(".fg-field-area").data("index");
        var rndindex = getRandom(5);
        var type = $(this).parents(".fg-field-area").data("type");
        var options = '<div class="option-value"><input type="text" name="fg_fields[' + index + '][settings][options][' + rndindex + ']" class="fg-control fg-options" placeholder="Enter Option Value"><a href="javascript:;" class="remove-choice"><i class="fa fa-times"></i></a></div>';
        $(this).parents(".fg-field-area").find(".multiple-options").append(options);
    });
    $(document).on("click", ".remove-choice", function () {
        if ($(this).parents(".multiple-options").find(".option-value").length <= 1) {
            alert("Cannot delete the option. Need atleast one option");
            return false;
        }
        $(this).parents(".option-value").remove();
    });



    if (param.defaultJson !== undefined) {
        defaultJson = JSON.parse(param.defaultJson);
        let prevAttr = [];
        // console.log("defaultJson",defaultJson);
        var lindex;
        $.map(defaultJson, function (val, key) {
            var field = $(".fg-field[data-field=" + defaultJson[key]['fields'] + "] .fg-btn-add").parents(".fg-field").data("field");
            var label = $(".fg-field[data-field=" + defaultJson[key]['fields'] + "] .fg-btn-add").parents(".fg-field").data("label");
            // console.log(key,val);
            lindex = val.index;
            addFields(lindex, field, label);
        });
        setTimeout(function () {
            if ($(".fg-selected-fields .fg-field-block[data-type=fieldGroups]").length > 0) {
                $.map(defaultJson, function (val, key) {
                    var field = $(".fg-field[data-field=" + defaultJson[key]['fields'] + "] .fg-btn-add").parents(".fg-field").data("field");
                    var label = $(".fg-field[data-field=" + defaultJson[key]['fields'] + "] .fg-btn-add").parents(".fg-field").data("label");
                    if (field == 'fieldGroups') {
                        // console.log(val);
                        var groupFields = val.groupFields;
                        for (var i = 0; i < groupFields.length; i++) {
                            $($("#fg-" + groupFields[i]).detach()).appendTo("#fg-" + val.index + " .sortable-list");
                            var subIndex = groupFields[i];
                            var fieldIndex = val.index;
                            var indx = i;
                            var inputfield = '<input type="hidden" class="field-input" name="fg_fields[' + fieldIndex + '][groupFields][' + indx + ']" value="' + subIndex + '" />';
                            $("#fg-" + val.index).find(".group-fields").append(inputfield);
                        }
                    }
                });
            }
            var defaultSettings = [];
            $.map(defaultJson, function (val, key) {
                defaultSettings.push(val);
            });

            var i = 0;
            $(".cds-form-container-assessment-body-right-panel .setting-area").each(function () {
                var index = $(this).data("index");
                var fieldtype = defaultSettings[i].fields;
                var settings = defaultSettings[i].settings;
                var j = 0;

                $.each(settings, function (key, val) {
                    if (key == 'label') {
                        $("#editable-" + index).html(val);
                        $(".fg-field-block[data-index=" + index + "] .fg-label span").html(val);
                    }
                    if (key == 'shortDesc') {
                        $("#description-" + index).html(val);
                    }

                    if (key == 'options') {
                        if (fieldtype == 'dropDown' || fieldtype == 'checkbox' || fieldtype == 'radio') {
                            $(".fg-field-area[data-index=" + index + "]").find(".multiple-options").html('');
                            $.each(settings.options, function (key, val) {
                                var rndindex = getRandom(5);
                                var options = '<div class="option-value"><input value="' + val + '" type="text" name="fg_fields[' + index + '][settings][options][' + rndindex + ']" class="fg-control fg-options" placeholder="Enter Option Value"><a href="javascript:;" class="remove-choice"><i class="fa fa-times"></i></a></div>';
                                $(".fg-field-area[data-index=" + index + "]").find(".multiple-options").append(options);
                            });
                        }
                    }
                    // console.log("fg_fields["+index+"][settings]["+key+"]"+" = "+val);
                    // console.log("\n")
                    if (key == 'font_size') {
                        $("select[name='fg_fields[" + index + "][settings][" + key + "]']").find("option[value=" + val + "]").attr("selected", "selected");
                    } else if (key == 'textLimit') {
                        $("select[name='fg_fields[" + index + "][settings][" + key + "]']").find("option[value=" + val + "]").attr("selected", "selected");
                    } else {
                        $("input[name='fg_fields[" + index + "][settings][" + key + "]']").val(val);
                    }

                    if (key == 'required') {
                        if (val == 'on' || val == 1)
                            $("input[name='fg_fields[" + index + "][settings][" + key + "]']").prop("checked", true);
                    }
                });
                i++;
            })
        }, 1500);
    }
};

function addFields(index, fieldType, label, icon) {
    console.log(icon,"aslk")
    var html = '';

    // var fieldType = $(this).parents('.fg-field').data("field");
    // var label = $(this).parents('.fg-field').data("label"); 
    var actionBtn = '';
    actionBtn += '<div class="action-fields">';
    actionBtn += '<button type="button" data-field="' + fieldType + '" title="Copy Field" class="copy-field"></button>';
    actionBtn += '<button type="button" class="remove-field"></button>';
    actionBtn += '</div>';

    html = '<li id="fg-' + index + '" data-type="' + fieldType + '" data-label="' + label + '" data-icon="' + icon + '" data-index="' + index + '" class="fg-field-block">';

    html += '<div class="fg-label"><div class="fg-field-block-label ' + fieldType + '"><p class="' + icon + '"></p><span>' + label + '</span></div>';
    html += actionBtn + '</div><div class="label-bottom"></div>';
    html += '<input type="hidden" class="field-input" value="' + fieldType + '" name="fg_fields[' + index + '][fields]">';
    if (fieldType == 'fieldGroups') {
        html += '<ul class="sortable-list"></ul>';
        html += '<div class="group-fields"></div>';
    }
    html += '</li>';

    // $(".fg-selected-fields").append(html);
    var active_field = $(".fg-field-block.active").attr("data-type");
    if (fieldType != 'fieldGroups' && active_field == 'fieldGroups') {

        $(".fg-field-block.active .sortable-list").append(html);
        var ee = $(".fg-field-block.active");
        var fieldIndex = $(ee).data("index");
        if ($(ee).find(".sortable-list > .fg-field-block").length > 0) {
            $(ee).find(".group-fields").html('');
            var indx = 0;
            $(ee).find(".sortable-list > .fg-field-block").not(".disable-sort-item").each(function () {
                var eee = $(this);
                var subIndex = $(eee).data("index");
                var inputfield = '<input type="hidden" class="field-input" name="fg_fields[' + fieldIndex + '][groupFields][' + indx + ']" value="' + subIndex + '" />';
                $(ee).find(".group-fields").append(inputfield);
                indx++;
            });
        }
        setTimeout(function () {
            $(".fg-field-block[data-index=" + index + "]").find(".fg-field-block-label").trigger("click");
            $(".fg-field-block").removeClass("active");
            $(".fg-field-block[data-index=" + fieldIndex + "]").addClass("active");
        }, 1000);
    } else {
        $(".fg-selected-fields").append(html);
        var html2 = '<li class="disable-sort-item" style="visibility:hidden"><div>Item ' + getRandom(9) + '</div></li>';
        // html2 += '<li><div>Item '+getRandom(9)+'</  div></li>';
        $('#fg-' + index + ' .sortable-list').append(html2);
        if (fieldType == 'fieldGroups') {
            $("#fg-" + index).addClass("droppable");
        }
        setTimeout(function () {
            $(".fg-field-block[data-index=" + index + "]").find(".fg-field-block-label").trigger("click");
        }, 1000);
    }


}
function convertFormToJSON(form) {
    return $(form)
        .serializeArray()
        .reduce(function (json, { name, value }) {
            json[name] = value;
            return json;
        }, {});
}
function getRandom(length) {
    return Math.floor(Math.pow(10, length - 1) + Math.random() * 9 * Math.pow(10, length - 1));
}


// Render HTML
$.fn.formRender = function (param = {}) {

    var formJson = JSON.parse(param.formJson);
    var formType = param.formType;
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
    renderHtml += '<div class="forms-area cds-fields-groups">';
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

    this.append(renderHtml);
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
        $(".datepicker").flatpickr({
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

        html = "<link href='{{url('public/assets/theme/css/dropzone.min.css')}}' rel='stylesheet' type='text/css' />";
        html += "<script src='{{url('public/assets/plugins/dropzone/dropzone-min.js')}} '></script>";
        html += "<script>Dropzone.autoDiscover = false;</script>";
        $(html).insertBefore("script[src='" + SITEURL + "/assets/plugins/form-generator/js/form-generator.js']");



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
                url: SITEURL + "/upload-fg-files?_token=" + csrf_token + "&timestamp=" + timestamp + "&main_timestamp=" + main_timestamp,
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
};

// $.fn.saveForm = function(param={}) {

//     console.log(formJson);
// }

function validate(type, active_step = '') {
    var validationError = false;
    $(".error-message").remove();
    if (type == 'single_form') {
        $("#fgr-form").find(".fgr-control").each(function () {
            // var name = $(this).attr("name");
            // if($(this).attr("type") == 'radio' || $(this).attr("type") == 'checkbox'){
            //     $(this).closest(".fg-form-group,.form-group").find(".error-message").remove();
            //     if($("[name='"+name+"']:checked").val() == undefined){
            //         validationError = true;
            //         var errorHtml = '<div class="error-message">This field is required</div>';
            //         $(this).closest(".fg-form-group,.form-group").append(errorHtml);
            //     }else{
            //         $(this).closest(".fg-form-group,.form-group").find(".error-message").remove();
            //     }
            // }else{
            //     if($(this).val() == ''){
            //         validationError = true;
            //         var errorHtml = '<div class="error-message">This field is required</div>';
            //         $(this).closest(".fg-form-group,.form-group").append(errorHtml);
            //     }
            //     if($(this).attr("type") == 'email'){
            //         if($(this).val() != ''){
            //             var validRegex = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;

            //             var email = $(this).val();
            //             if (!email.match(validRegex)) {
            //                 validationError = true;
            //                 var errorHtml = '<div class="error-message">Invalid Email value entered</div>';
            //                 $(this).closest(".fg-form-group,.form-group").append(errorHtml);
            //             }
            //         }
            //     }
            // }
            var attr = $(this).attr('required');

            if (typeof attr !== undefined || attr !== false) {
                var name = $(this).attr("name");

                if ($(this).attr("type") == 'radio' || $(this).attr("type") == 'checkbox') {
                    $(this).closest(".fg-form-group").find(".error-message").remove();
                    if ($("[name='" + name + "']:checked").val() == undefined) {
                        validationError = true;
                        var errorHtml = '<div class="error-message">This field is required 1</div>';
                        $(this).closest(".fg-form-group").append(errorHtml);
                    } else {
                        $(this).closest(".fg-form-group").find(".error-message").remove();
                    }
                } else {
                    if (!$(this).hasClass("fg-files")) {
                        if ($(this).val() == '') {
                            validationError = true;
                            var errorHtml = '<div class="error-message">This field is required 2</div>';
                            $(this).closest(".fg-form-group").append(errorHtml);
                        }
                    }
                }
            }
            if (!validationError) {
                if ($(this).attr("type") == 'email') {
                    if ($(this).val() != '') {
                        // var validRegex = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+.(?:\.[a-zA-Z0-9-]+)*$/;
                        // var validRegex = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
                        var email = $(this).val();
                        var atpos = email.indexOf("@");
                        var dotpos = email.lastIndexOf(".");
                        if (atpos < 1 || (dotpos - atpos < 2)) {
                            validationError = true;
                            var errorHtml = '<div class="error-message">Invalid Email value entered</div>';
                            $(this).closest(".fg-form-group,.form-group").append(errorHtml);
                        }
                        // if (!email.match(validRegex)) {
                        //     validationError = true;
                        //     var errorHtml = '<div class="error-message">Invalid Email value entered</div>';
                        //     $(this).closest(".fg-form-group,.form-group").append(errorHtml);
                        // }
                    }
                }
                var attr = $(this).attr('maxlength');
                if (typeof attr !== undefined && attr !== false) {
                    var maxlength = $(this).attr('maxlength');
                    var value = $(this).val();
                    var length = value.length;
                    if (maxlength != '' && length > maxlength) {
                        validationError = true;
                        var errorHtml = '<div class="error-message">Length cannot be greater then ' + maxlength + '</div>';
                        $(this).closest(".fg-form-group,.form-group").append(errorHtml);
                    }
                }
                var attr = $(this).attr('textlimit');
                var attr2 = $(this).attr('addlength');
                if (typeof attr !== undefined && attr !== false && typeof attr2 !== undefined && attr2 !== false) {
                    var textLimit = $(this).attr('textlimit');
                    var addLength = $(this).attr('addlength');
                    var value = $(this).text();
                    if (addLength != '' && !isNaN(addLength)) {
                        if (textLimit == 'characterLimit') {
                            var str_length = value.length;
                            if (str_length > addLength) {
                                validationError = true;
                                var errorHtml = '<div class="error-message">Character cannot be greater then ' + addLength + '</div>';
                                $(this).closest(".fg-form-group,.form-group").append(errorHtml);
                            }
                        }
                        if (textLimit == 'wordLimit') {
                            var count_word = value.split(" ").length;
                            if (count_word > addLength) {
                                validationError = true;
                                var errorHtml = '<div class="error-message">Words cannot be greater then ' + addLength + '</div>';
                                $(this).closest(".fg-form-group,.form-group").append(errorHtml);
                            }
                        }
                    }
                }
            }
        });
    } else {
        var validationError = false;
        $(".fg-form-group[data-step=" + active_step + "]").find(".fgr-control").each(function () {
            var attr = $(this).attr('required');

            if (typeof attr !== 'undefined' && attr !== false) {
                var name = $(this).attr("name");
                if ($(this).attr("type") == 'radio' || $(this).attr("type") == 'checkbox') {
                    $(this).closest(".fg-form-group").find(".error-message").remove();
                    if ($("[name='" + name + "']:checked").val() == undefined) {
                        validationError = true;
                        var errorHtml = '<div class="error-message">This field is required 3</div>';
                        $(this).closest(".fg-form-group").append(errorHtml);
                    } else {
                        $(this).closest(".fg-form-group").find(".error-message").remove();
                    }
                } else {
                    if ($(this).val() == '') {
                        validationError = true;
                        var errorHtml = '<div class="error-message">This field is required 4</div>';
                        $(this).closest(".fg-form-group").append(errorHtml);
                    }
                }
            }
            if (!validationError) {
                if ($(this).attr("type") == 'email') {
                    if ($(this).val() != '') {
                        // var validRegex = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/;
                        var email = $(this).val();
                        var atpos = email.indexOf("@");
                        var dotpos = email.lastIndexOf(".");
                        if (atpos < 1 || (dotpos - atpos < 2)) {
                            validationError = true;
                            var errorHtml = '<div class="error-message">Invalid Email value entered</div>';
                            $(this).closest(".fg-form-group,.form-group").append(errorHtml);
                        }
                    }
                }
                var attr = $(this).attr('maxlength');
                if (typeof attr !== undefined && attr !== false) {
                    var maxlength = $(this).attr('maxlength');
                    var value = $(this).val();
                    var length = value.length;
                    if (maxlength != '' && length > maxlength) {
                        validationError = true;
                        var errorHtml = '<div class="error-message">Length cannot be greater then ' + maxlength + '</div>';
                        $(this).closest(".fg-form-group,.form-group").append(errorHtml);
                    }
                }
                var attr = $(this).attr('textlimit');
                var attr2 = $(this).attr('addlength');
                if (typeof attr !== undefined && attr !== false && typeof attr2 !== undefined && attr2 !== false) {
                    var textLimit = $(this).attr('textlimit');
                    var addLength = $(this).attr('addlength');
                    var value = $(this).text();
                    if (addLength != '' && !isNaN(addLength)) {
                        if (textLimit == 'characterLimit') {
                            var str_length = value.length;
                            if (str_length > addLength) {
                                validationError = true;
                                var errorHtml = '<div class="error-message">Character cannot be greater then ' + addLength + '</div>';
                                $(this).closest(".fg-form-group,.form-group").append(errorHtml);
                            }
                        }
                        if (textLimit == 'wordLimit') {
                            var count_word = value.split(" ").length;
                            if (count_word > addLength) {
                                validationError = true;
                                var errorHtml = '<div class="error-message">Words cannot be greater then ' + addLength + '</div>';
                                $(this).closest(".fg-form-group,.form-group").append(errorHtml);
                            }
                        }
                    }
                }
            }
        });
    }
    if (validationError) {
        $('html, body').animate({
            scrollTop: $(".fgr-render").offset().top
        }, 1000);
        return false;
    } else {
        return true;
    }
}
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
    html += '<label class="dropzone-label" for="upload-file">';
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