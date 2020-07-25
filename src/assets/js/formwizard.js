/*jshint esversion: 6 */
/*globals $:true, */

if (typeof jQuery === "undefined") {
    throw new Error("jQuery plugins need to be before formizard.js file.");
}

$.formwizard = {
    triggerEvent: (event, eventSelector, eventParams) => {
        $(eventSelector).trigger(event, eventParams);
    },
    currentButtonTarget: null,
    resetCurrentTarget: true,
    observerObj: null,
    fields: [],
    previewHeadings: [],
    previewEmptyText: '',
    options: [],
    submit: false,
    helper: {
        showMessage: message => {
            alert(message);
        },
        removeField: element => {
            let formId = $(element)
                .closest("form")
                .attr("id");
            let currentIndex = $.formwizard.helper.currentIndex("#" + formId);
            let field = $(element).attr("id");
            let currentStepFields = $.formwizard.fields[formId][currentIndex];

            //update fields array for the current form wizard
            $.formwizard.fields[formId][currentIndex] = $.grep(
                currentStepFields,
                function (value) {
                    return value != field;
                }
            );
        },
        clearField: (element) => {
            $(element).val('');
        },
        addField: (formId, element, currentStep) => {
            $.formwizard.fields[formId][currentStep].push(element.id);
        },
        shake: function (form) {
            $(form + " .sw-main").addClass("shake animated");
            setTimeout(function () {
                $(form + " .sw-main").removeClass("shake animated");
            }, 1000);
        },
        appendButtons: function ({
            form,
            labelNext,
            labelPrev,
            labelFinish,
            labelRestore,
            iconNext,
            iconPrev,
            iconFinish,
            iconRestore,
            classNext,
            classPrev,
            classFinish,
            classRestore,
            enablePersistence = false
        }) {
            let buttons = [];

            if (enablePersistence) {
                buttons.push(
                    $(
                        '<button class="formwizard_restore" type="button"/></button>'
                    )
                    .html(iconRestore + "&nbsp;" + labelRestore)
                    .addClass(classRestore)
                );
            }

            //add to buttons array
            buttons.push(
                $('<button class="formwizard_prev"></button>')
                .html(iconPrev + "&nbsp;" + labelPrev)
                .addClass(classPrev)
                .on("click", function (e) {
                    e.preventDefault();
                    $.formwizard.formNavigation.previous(e.target);
                })
            );

            // Toolbar next, previous and finish custom buttons
            let formwizardBtnNext = $('<button class="formwizard_next"></button>')
                .html(labelNext + "&nbsp" + iconNext)
                .addClass(classNext);

            //add to return array
            buttons.push(
                formwizardBtnNext
            );

            let formwizardBtnFinish = $(
                    '<button class="formwizard_finish" type="submit"/></button>'
                )
                .html(iconFinish + "&nbsp;" + labelFinish)
                .addClass(classFinish);
            //add to buttons array
            buttons.push(
                formwizardBtnFinish
            );

            var combined = formwizardBtnNext.add(formwizardBtnFinish);

            //bind validation for the button next and finish on click
            $(combined).on("click", function (e) {
                e.preventDefault();
                setTimeout(
                    function () {
                        if ($(form).yiiActiveForm("data").attributes.length) {
                            return $.formwizard.formValidation.run(form, e);
                        }
                        if ($(e.target).hasClass("formwizard_finish")) {
                            $(form).yiiActiveForm("submitForm");
                        }
                        $.formwizard.formNavigation.next(e.target);
                    },
                    200
                );

            });

            return buttons;
        },
        updateButtons: function (wizardContainerId) {
            $(wizardContainerId).on("showStep", function (
                e,
                anchorObject,
                stepNumber,
                stepDirection,
                stepPosition
            ) {
                let btnPrev = $(
                    wizardContainerId +
                    " > .sw-toolbar > .sw-btn-group-extra button.formwizard_prev"
                );
                let btnFinish = $(
                    wizardContainerId +
                    " > .sw-toolbar > .sw-btn-group-extra >button.formwizard_finish"
                );
                let btnNext = $(
                    wizardContainerId +
                    " > .sw-toolbar > .sw-btn-group-extra >button.formwizard_next "
                );

                if (stepPosition === "first") {
                    btnPrev.addClass("disabled");
                    btnFinish.addClass("hidden d-none");
                    btnNext.removeClass("hidden d-none");
                } else if (stepPosition === "final") {
                    btnNext.addClass("hidden d-none");
                    btnFinish.removeClass("hidden d-none");
                    btnPrev.removeClass("disabled hidden d-none");
                    //call preview step if enabled
                    $.formwizard.previewStep.add(wizardContainerId);
                } else {
                    btnPrev.removeClass("disabled");
                    btnNext.removeClass("disabled hidden d-none");
                    btnFinish.addClass("hidden d-none");
                }

            });
        },
        currentIndex: function (form) {
            return $(form + " ul.step-anchor>li.active").index();
        },

    },
    previewStep: {
        add: (wizardContainerId) => {
            let formwizardOptions = $.formwizard.options;
            let formId = $(wizardContainerId).closest('form').attr('id');
            let fragment = document.createDocumentFragment();
            let currentStep = $.formwizard.helper.currentIndex('#' + formId);
            let stepContainer = document.querySelector('#step-' + currentStep);
            let bsVersion = formwizardOptions[formId].bsVersion;
            let classListGroup = formwizardOptions[formId].classListGroup;
            let classListGroupHeading = formwizardOptions[formId].classListGroupHeading;

            stepContainer.querySelectorAll("." + classListGroup).forEach(element => {
                element.remove();
            });

            if (formwizardOptions.hasOwnProperty(formId) && formwizardOptions[formId].enablePreview) {
                let fields = $.formwizard.fields[formId];

                //iterate steps
                fields.forEach(function (stepFields, step) {
                    let stepPreviewContainer = document.createElement("div");
                    stepPreviewContainer.setAttribute('class', classListGroup + ' preview-container');
                    stepPreviewContainer.dataset.step = step;
                    let stepType = $(wizardContainerId).find('#step-' + step).data('step').type;
                    let stepHeading = $.formwizard.previewHeadings[step] == '' ? 'Step ' + parseInt(step + 1) : $.formwizard.previewHeadings[step];
                    let rowHtml = '<h4 class="' + classListGroupHeading + '">' + stepHeading + '</h4>';

                    //iterate step fields
                    stepFields.forEach(function (fieldName, index) {
                        let inputLabel = $.formwizard.previewStep.getLabel(fieldName);
                        let inputValue = $.formwizard.previewStep.getValue(formId, fieldName);

                        let stepData = {
                            "label": inputLabel == '' ? $.formwizard.previewEmptyText : inputLabel,
                            "value": inputValue == '' ? $.formwizard.previewEmptyText : inputValue,
                            "target": fieldName
                        };

                        if ($('#' + formId + ' #' + fieldName).attr("type") !== 'hidden') {
                            rowHtml += $.formwizard.previewStep.getTemplate(stepData, bsVersion, formwizardOptions[formId]);
                        }

                        //if tabular step then add divider after every model 
                        if (stepType == 'tabular') {
                            let rows = $(wizardContainerId).find('#step-' + step).find('.fields_container .tabular-row').length;
                            divider = stepFields.length / rows;
                            if (((index + 1) % divider) == 0) {
                                rowHtml += '<hr class="tabular-divider" />';
                            }
                        }
                    });

                    stepPreviewContainer.innerHTML = rowHtml;
                    fragment.appendChild(stepPreviewContainer);
                });

                stepContainer.appendChild(fragment);
                $(".preview-button").on('click', function (e) {
                    let stepNo = $(this).closest('div.preview-container').data('step');
                    $.formwizard.formNavigation.goToStep(wizardContainerId, stepNo);
                    $.formwizard.previewStep.highlightTarget($(this).val());
                });
            }
        },
        getLabel: (fieldName) => {
            let text = $('#' + fieldName).siblings('label').text();
            if (text !== '') {
                return text;
            }
            return $('#' + fieldName).attr("placeholder");
        },
        getValue: (formId, fieldName) => {
            let inputType = $('#' + formId + ' #' + fieldName);

            let inputPackage = {
                select: function () {
                    // <select> element.
                    if ($.formwizard.previewEmptyText !== '') {
                        let selectValue = $('#' + formId + ' #' + fieldName + ' option:selected').val();
                        let selectLabel = $('#' + formId + ' #' + fieldName + ' option:selected').text();

                        if (selectValue == '') {
                            return $.formwizard.previewEmptyText == 'NA' ? selectLabel : $.formwizard.previewEmptyText;
                        }
                        return $('#' + formId + ' #' + fieldName + ' option:selected').text();
                    }

                    return $('#' + formId + ' #' + fieldName + ' option:selected').text();
                },
                radiogroup: function () {
                    let radio = inputType.find('input:checked');
                    return (radio.length) ? radio.val() : '';
                },
                checkboxgroup: function () {
                    let checkboxes = inputType.find('input:checked');
                    let choices = '';
                    checkboxes.each(function (index, checkbox) {
                        choices += $(checkbox).val() + ',';
                    });
                    return choices;
                },
                checkbox: function () {
                    return inputType.is(":checked") ? inputType.val() : '';
                },
                file: function () {
                    return inputType.get(0).files.length + ' files';
                }
            };

            if (inputType.is("select")) {
                return inputPackage.select();
            }

            if (inputType.is('div') && inputType.attr('role') == 'radiogroup') {
                return inputPackage.radiogroup();
            }

            if (inputType.is('div')) {
                return inputPackage.checkboxgroup();
            }

            //check if single checkbox input
            if (inputType.attr("type") == 'checkbox') {
                return inputPackage.checkbox();
            }

            if (inputType.attr("type") == "file") {
                return inputPackage.file();
            }

            // <textarea> <input> element.
            return inputType.val();

        },
        getTemplate: (params, bsVersion, formwizardOptions) => {
            let bsClass = bsVersion == 4 ? 'list-group-item-action' : '';
            return `<button type="button" class="list-group-item ${formwizardOptions.classListGroupItem} ${bsClass} preview-button" value="${params.target}">
                    <span class="badge ${formwizardOptions.classListGroupBadge}">
                        ${params.label}
                    </span>
                    ${params.value}
                    </button>`;
        },
        highlightTarget: function (target) {
            $('.field-' + target).addClass('notify-target');
            setTimeout(function () {
                $('.field-' + target).removeClass('notify-target');
            }, 2000);
        }
    },
    formValidation: {
        run: function (form, event) {
            $.formwizard.resetCurrentTarget = false;
            $.formwizard.currentButtonTarget = event.target;
            $(form).yiiActiveForm("validate", true);
        },
        bindAfterValidate: function (form) {
            $(form)
                .on("beforeValidate", function (event, messages, deferreds) { //added beforeValidate event for the skippable step
                    let formName = $(this).attr("id");
                    let currentIndex = $.formwizard.helper.currentIndex(form);
                    const isSkippableStep = $("#step-" + currentIndex).data('step').skipable;
                    let allEmpty = true;

                    //check all input types if any of the inputs are not empty
                    $("#step-" + currentIndex + ' .fields_container').find(":input").each(function (index, input) {
                        inputTypes = {
                            text: function (input) {
                                if ($(input).val() !== '') {
                                    return false;
                                }
                            },
                            radio: function (input) {
                                return !$(input).is(':checked');
                            },
                            "select-one": function (input) {
                                if ($(input).val() !== '') {
                                    return false;
                                }
                            },
                            "select-multiple": function (input) {
                                if ($(input).val() !== '') {
                                    return false;
                                }
                            },
                            number: function (input) {
                                if ($(input).val() !== '') {
                                    return false;
                                }
                            },
                            range: function (input) {
                                if ($(input).val() !== '') {
                                    return false;
                                }
                            },
                            password: function (input) {
                                if ($(input).val() !== '') {
                                    return false;
                                }
                            },
                            file: function (input) {
                                if ($(input).val() !== '') {
                                    return false;
                                }
                            },
                        };

                        if (inputTypes.hasOwnProperty(input.type)) {
                            if (inputTypes[input.type].call(this, input) === false) {
                                allEmpty = false;
                                return false;
                            }
                        }

                    });
                    //if skippable step and all the inputs are empty then 
                    //remove all the fields of the step from the validation
                    if (isSkippableStep && allEmpty) {
                        $.each($.formwizard.fields[formName][currentIndex], function (index, fieldId) {
                            $("#" + formName).yiiActiveForm("remove", fieldId);
                        });
                    }
                })
                .on("afterValidate", function (event, messages, errorAttributes) {

                    //reset the current target button if not clicked on the next button
                    if ($.formwizard.resetCurrentTarget) {
                        $.formwizard.currentButtonTarget = null;
                    }

                    event.preventDefault();

                    //form name
                    let formName = $(this).attr("id");
                    //current step index
                    let currentIndex = $.formwizard.helper.currentIndex(form);
                    //is last step
                    const isLastStep = currentIndex == $(form + " .step-anchor").find("li").length - 1;
                    //is preview step
                    const isPreviewStep = $.formwizard.options[formName].enablePreview && isLastStep;
                    //is skipable step
                    const isSkippableStep = $("#step-" + currentIndex).data('step').skipable;

                    //check if the preview step OR skippable step then skip validation messages check
                    let res = (isPreviewStep || isSkippableStep) ? 0 : $.formwizard.fields[formName][currentIndex].diff(messages);

                    if ($.formwizard.formValidation.editModeErrors(formName, messages)) {
                        return;
                    }

                    if (!res.length) {
                        //check if last step then submit form

                        if (isLastStep) {
                            $.formwizard.submit = true;
                            return true;
                        } else {
                            $(form).yiiActiveForm("resetForm");

                            //check if target null dont call the next navigation
                            ($.formwizard.currentButtonTarget !== null) && $.formwizard.formNavigation.next($.formwizard.currentButtonTarget);
                        }
                    } else if ($.formwizard.resetCurrentTarget === false) {
                        $.formwizard.helper.shake(form);
                    }
                    return false;
                })
                .on("beforeSubmit", function (event) {
                    event.preventDefault();
                    if ($.formwizard.submit) {
                        $.formwizard.persistence.clearStorage();
                        return true;
                    }
                    return false;
                });
        },
        editModeErrors: function (formName, messages) {
            //if edit mode then highlight error steps
            if ($.formwizard.options[formName].editMode) {
                //get all fields
                let allFields = $.formwizard.fields[formName];
                let errorSteps = [];

                //iterate all the fields
                $.each(allFields, function (index, stepFields) {
                    //if the step is skipable 
                    let isSkippable = $("#step-" + index).data('step').skipable;

                    //if not skipable and has errors
                    //push the step index to the errorsteps array
                    (!isSkippable && stepFields.diff(messages).length) && errorSteps.push(index);
                });

                //update error step higlightening
                let wizardContainerId = $.formwizard.options[formName].wizardContainerId;
                $("#" + wizardContainerId).smartWizard('updateErrorStep', errorSteps);

                //if no error steps then clear errors
                if (errorSteps.length) {
                    $.formwizard.formNavigation.goToStep("#" + wizardContainerId, errorSteps[0]);
                    return true;
                }
            }
        },
        isValid: function (messages) {
            for (var i in messages) {
                if (messages[i].length > 0) {
                    return true;
                }
            }
            return false;
        },
        updateErrorMessages: function (form, messages) {
            for (var i in messages) {
                if (messages[i].length) {
                    let attribute = i.replace(/\-([0-9]+)/g, "");
                    $(form).yiiActiveForm("updateAttribute", attribute, messages[i]);
                }
            }
        },
        addField: (form, fieldOptions) => {
            $("#" + form).yiiActiveForm("add", fieldOptions);
        },
        removeField: (element) => {
            let formId = $(element)
                .closest("form")
                .attr("id");
            $("#" + formId).yiiActiveForm("remove", element.id);
        }
    },
    formNavigation: {
        next: (target) => {
            let containerId = $(target)
                .parent()
                .closest(".sw-main")
                .attr("id");
            $("#" + containerId).smartWizard("next");
        },
        goToStep: (wizardContainerId, stepno) => {
            $(wizardContainerId).smartWizard("goToStep", stepno);
        },
        previous: (target) => {
            let containerId = $(target)
                .parent()
                .closest(".sw-main")
                .attr("id");
            $("#" + containerId).smartWizard("prev");
        }
    },
    observer: {
        start: function (selector) {
            // select the target node in select2
            var target = document.querySelector(selector);

            $.formwizard.observerObj = $.formwizard.observer.observerInstance(selector);

            // configuration of the observer:
            var config = {
                childList: true,
                attributes: true,
                subtree: false
            };

            // pass in the target node, as well as the observer options
            $.formwizard.observerObj.observe(target, config);
        },

        /**
         *
         * @returns {MutationObserver}
         */
        observerInstance: function (selector) {
            // create an observer instance
            return new MutationObserver(function (mutations) {
                mutations.forEach(function (mutation) {

                    if (mutation.type == "childList") {

                        //check if material theme used 
                        if (typeof $.themematerial !== 'undefined') {
                            $.themematerial.init();
                        }

                        //init the button events for thetabular steps
                        $.formwizard.init(selector);
                        $.formwizard.observerObj.disconnect();
                    }
                });
            });
        }
    },
    tabular: {
        addRow: element => {
            let currentContainer = $(element).siblings(".fields_container");
            let currentIndex = currentContainer.find(".tabular-row").length;
            let limitOver = currentIndex === currentContainer.data('rows-limit');

            //if row limit exceeded
            if (limitOver) {
                $.formwizard.helper.showMessage("Cannot add any more.");
                return;
            }

            let documentFragment = document.createDocumentFragment();
            let row = $(currentContainer)[0].firstChild;
            let formId = $(element)
                .closest("form")
                .attr("id");
            let currentStep = $.formwizard.helper.currentIndex("#" + formId);
            let tabular = $.formwizard.tabular;

            //get all inputs 
            let oldFieldCollection = $(row).find('input,select,textarea');
            let eventTrigger = $.formwizard.triggerEvent;


            //trigger beforeClone event for all the inputs inside the tabular row to be cloned
            oldFieldCollection.each(function (index, element) {
                if (typeof $(element).attr('id') !== 'undefined') {
                    //trigger beforeclone event
                    eventTrigger("formwizard.beforeClone", "#" + formId + " #step-" + currentStep + " #" + element.id);
                }
            });

            //clone node
            documentFragment.appendChild(row.cloneNode(true));

            //trigger afterClone event for all the inputs inside the tabular row
            oldFieldCollection.each(function (index, element) {
                if (typeof $(element).attr('id') !== 'undefined') {
                    //trigger beforeclone event
                    eventTrigger("formwizard.afterClone", "#" + formId + " #step-" + currentStep + " #" + element.id);
                }
            });

            let rowClone = documentFragment.querySelector("div.tabular-row");

            //update row container id
            rowClone.id = rowClone.id.replace(
                /\_[^\_]+$/,
                "_" + parseInt(currentIndex)
            );

            let newFields = [];
            //update input ids
            documentFragment
                .querySelectorAll("input,select,textarea")
                .forEach(function (element, index) {

                    //save old id
                    let oldFieldId = element.id;

                    //update input attributes
                    tabular.updateFieldAttributes(element, currentIndex);
                    if (typeof $(element).attr('id') !== 'undefined') {
                        //get the default field options ActiveForm
                        let fieldOptions = tabular.setFieldDefaults(
                            element,
                            formId,
                            oldFieldId
                        );

                        //add field to the formwizard step fields list
                        $.formwizard.helper.addField(formId, element, currentStep);
                        newFields.push(element.id);
                        if (typeof fieldOptions !== 'undefined') {
                            //add field to the activeform validation
                            $.formwizard.formValidation.addField(formId, fieldOptions);
                        }
                    }
                });

            //insert row content
            $(currentContainer)[0].appendChild(documentFragment);

            //update the remove button
            let removeIcon = document.querySelector("#row_" + currentIndex + " i.remove-row");
            removeIcon.dataset.rowid = currentIndex;

            //trigger the afterInsert event 
            eventTrigger("formwizard.afterInsert", "#" + formId + " #step-" + currentStep + " #row_" + currentIndex, {
                rowIndex: currentIndex
            });
        },
        removeRow: rowid => {
            let rowContainer = $("#row_" + rowid);
            let isLastRow = rowContainer.closest('.fields_container').find('.tabular-row').length == 1;

            rowContainer.find("textarea,input,select").each(function (index, element) {

                if (isLastRow) {
                    //clear the fields list if last row in tabular step
                    return $.formwizard.helper.clearField(element);
                }

                //remove from the fromwizard field list
                $.formwizard.helper.removeField(element);

                //remove from the ActiveForm validation list
                $.formwizard.formValidation.removeField(element);
            });

            !isLastRow && rowContainer.remove();
        },
        setFieldDefaults: (element, formId, oldFieldId) => {
            // get then name only for the tabular input
            let nameOnly = element.name.match(/(\[[\d]{0,}\].*)/);
            let fieldProperty = $("#" + formId).yiiActiveForm("find", oldFieldId);
            let fieldOptions;

            if (typeof fieldProperty !== 'undefined') {
                //set the default options for the ActiveForm field
                fieldOptions = {
                    id: element.id,
                    name: nameOnly[0],
                    container: ".field-" + element.id,
                    input: "#" + element.id,
                    error: ".help-block.help-block-error",
                    value: element.value,
                    status: 0,
                    validate: fieldProperty.hasOwnProperty('validate') ? fieldProperty.validate : function (attribute, value, messages, deferred, $form) {}
                };
            }

            return fieldOptions;

        },
        updateFieldAttributes: (element, currentIndex) => {
            let hasContainer = $(element)
                .parent()
                .hasClass("field-" + element.id);

            if (typeof $(element).attr('id') !== 'undefined') {
                //update counter of the id
                element.id = element.id.replace(
                    /\-([\d]+)\-/,
                    "-" + parseInt(currentIndex) + "-"
                );
            }


            //update the counter of the name
            element.name = element.name.replace(
                /\[([\d]+)\]/,
                "[" + parseInt(currentIndex) + "]"
            );

            //reset value
            element.value = "";

            //if the field container is available
            if (hasContainer) {
                let container = $(element).parent();
                $(container).removeClassesExceptThese(["form-group", "required"]);
                $(container).addClass("field-" + element.id);
                $(container)
                    .find("label")
                    .attr("for", element.id);
                $(container)
                    .find(".help-block")
                    .html("");
            }
        }
    },
    init: (selector) => {

        //bind remove row for tabular steps
        $(selector).on("click", ".remove-row", function (e) {
            $.formwizard.tabular.removeRow($(this).data("rowid"));
        });

        //bind addRow for tabular step
        $(selector + " .add_row").on("click", function (e) {
            $.formwizard.tabular.addRow($(this));
        });

        //bind blur & change to reset the target input
        $(selector).find(':input:not(button)').on('blur change', function (e) {
            e.preventDefault();
            $.formwizard.resetCurrentTarget = true;
        });
    },
    persistence: {
        assign: (obj, keyPath, value) => {
            lastKeyIndex = keyPath.length - 1;
            for (var i = 0; i < lastKeyIndex; ++i) {
                key = keyPath[i];
                if (!(key in obj)) {
                    obj[key] = {};
                }
                obj = obj[key];
            }
            obj[keyPath[lastKeyIndex]] = value;
        },
        storageFields: {},
        savefield: (fieldObject, formId, stepData) => {
            let fieldId = fieldObject.id;
            let fieldType = $(fieldObject).get(0).type;
            let stepNumber = stepData.number;
            let stepType = stepData.type;


            if (!$.formwizard.persistence.storageFields.hasOwnProperty('step-' + stepNumber)) {
                //set the step type
                $.formwizard.persistence.storageFields['step-' + stepNumber] = {
                    stepType: stepType,
                    fields: {}
                };
            }


            let fieldTypes = {
                'select-one': (fieldId) => {
                    if ($.formwizard.persistence.storageFields['step-' + stepNumber].stepType == 'tabular') {
                        let rowId = $("#" + formId + " #" + fieldId).closest('div.tabular-row').attr('id');
                        if (!$.formwizard.persistence.storageFields['step-' + stepNumber].fields.hasOwnProperty(rowId)) {
                            $.formwizard.persistence.storageFields['step-' + stepNumber].fields[rowId] = {};
                        }
                        $.formwizard.persistence.storageFields['step-' + stepNumber].fields[rowId][fieldId] = document.querySelector("#" + formId + " #" + fieldId).value;
                    } else {
                        //add fields to the local fieldstorage property
                        $.formwizard.persistence.storageFields['step-' + stepNumber].fields[fieldId] = document.querySelector("#" + formId + " #" + fieldId).value;
                    }

                },
                text: function (fieldId) {
                    if ($.formwizard.persistence.storageFields['step-' + stepNumber].stepType == 'tabular') {
                        let rowId = $("#" + formId + " #" + fieldId).closest('div.tabular-row').attr('id');

                        if (!$.formwizard.persistence.storageFields['step-' + stepNumber].fields.hasOwnProperty(rowId)) {
                            $.formwizard.persistence.storageFields['step-' + stepNumber].fields[rowId] = {};
                        }
                        $.formwizard.persistence.storageFields['step-' + stepNumber].fields[rowId][fieldId] = document.querySelector("#" + formId + " #" + fieldId).value;
                    } else {
                        //add fields to the local fieldstorage property
                        $.formwizard.persistence.storageFields['step-' + stepNumber].fields[fieldId] = document.querySelector("#" + formId + " #" + fieldId).value;
                    }
                },
                radio: (fieldId) => {
                    let radioList = $("#" + formId + " #" + fieldId).closest('div[role="radiogroup"]').find('input:radio');
                    if ($.formwizard.persistence.storageFields['step-' + stepNumber].stepType == 'tabular') {
                        let rowId = $("#" + formId + " #" + fieldId).closest('div.tabular-row').attr('id');
                        if (!$.formwizard.persistence.storageFields['step-' + stepNumber].fields.hasOwnProperty(rowId)) {
                            $.formwizard.persistence.storageFields['step-' + stepNumber].fields[rowId] = {};
                        }
                        if (radioList.length) {
                            radioList.each(function (index, element) {
                                //add fields to the local fieldstorage property
                                $.formwizard.persistence.storageFields['step-' + stepNumber].fields[rowId][element.id] = element.checked;
                            });
                        } else {
                            //add fields to the local fieldstorage property
                            $.formwizard.persistence.storageFields['step-' + stepNumber].fields[rowId][fieldId] = $("#" + formId + " #" + fieldId).is(":checked");
                        }
                    } else {
                        if (radioList.length) {
                            radioList.each(function (index, element) {
                                //add fields to the local fieldstorage property
                                $.formwizard.persistence.storageFields['step-' + stepNumber].fields[element.id] = element.checked;
                            });
                        } else {
                            //add fields to the local fieldstorage property
                            $.formwizard.persistence.storageFields['step-' + stepNumber].fields[fieldId] = $("#" + formId + " #" + fieldId).is(":checked");
                        }
                    }
                },
                checkbox: (fieldId) => {
                    let isCheckBoxList = $('#' + formId + " #" + fieldId).attr('name').match(/\[\]$/g);

                    if ($.formwizard.persistence.storageFields['step-' + stepNumber].stepType == 'tabular') {
                        let rowId = $("#" + formId + " #" + fieldId).closest('div.tabular-row').attr('id');
                        if (!$.formwizard.persistence.storageFields['step-' + stepNumber].fields.hasOwnProperty(rowId)) {
                            $.formwizard.persistence.storageFields['step-' + stepNumber].fields[rowId] = {};
                        }
                        if (isCheckBoxList.length) {
                            let checkboxList = $("input[name='" + $("#" + formId + " #" + fieldId).attr('name') + "']");
                            checkboxList.each(function (index, element) {
                                //add fields to the local fieldstorage property
                                $.formwizard.persistence.storageFields['step-' + stepNumber].fields[rowId][element.id] = element.checked;
                            });
                        } else {
                            //add fields to the local fieldstorage property
                            $.formwizard.persistence.storageFields['step-' + stepNumber].fields[rowId][fieldId] = $("#" + formId + " #" + fieldId).is(":checked");
                        }
                    } else {
                        if (isCheckBoxList && isCheckBoxList.length) {
                            let checkboxList = $("input[name='" + $("#" + formId + " #" + fieldId).attr('name') + "']");
                            checkboxList.each(function (index, element) {
                                //add fields to the local fieldstorage property
                                $.formwizard.persistence.storageFields['step-' + stepNumber].fields[element.id] = element.checked;
                            });
                        } else {
                            //add fields to the local fieldstorage property
                            $.formwizard.persistence.storageFields['step-' + stepNumber].fields[fieldId] = $("#" + formId + " #" + fieldId).is(":checked");
                        }
                    }

                }
            };

            // save the complete json form inputs object to the local variable
            fieldTypes.hasOwnProperty(fieldType) && fieldTypes[fieldType].call(this, fieldId);

            //save the complete fields json to localstorage 
            localStorage.setItem('formwizard.' + formId, JSON.stringify($.formwizard.persistence.storageFields));
        },
        clearStorage: () => {
            //the prefix for the storage
            let prefix = "formwizard.";

            //iterate all items in the storage
            for (var key in localStorage) {

                //match the prefix text
                if (key.indexOf(prefix) == 0) {
                    localStorage.removeItem(key);
                }
            }
            //clear storage fields variable
            $.formwizard.persistence.storageFields = {};
        },
        loadForm: (formId) => {
            //load fields stored
            $.formwizard.persistence.storageFields = JSON.parse(localStorage.getItem("formwizard." + formId));

            let storageFields = $.formwizard.persistence.storageFields;
            let fieldTypes = {
                'select-one': (fieldId, value) => {
                    let field = document.querySelector("#" + formId + " #" + fieldId);

                    // restore value
                    field.value = value;

                    //trigger change event for select2
                    $("#" + fieldId).trigger('change');

                    //trigger the afterRestoreEvent
                    $.formwizard.triggerEvent('formwizard.' + formId + '.afterRestore', "#" + formId + " #" + fieldId, {
                        fieldId: fieldId,
                        fieldValue: value
                    });
                },
                text: function (fieldId, value) {
                    let field = document.querySelector("#" + formId + " #" + fieldId);

                    // restore value
                    field.value = value;

                    //trigger the afterRestoreEvent
                    $.formwizard.triggerEvent('formwizard.' + formId + '.afterRestore', "#" + formId + " #" + fieldId, {
                        fieldId: fieldId,
                        fieldValue: value
                    });
                },
                radio: (fieldId, value) => {
                    let field = document.querySelector("#" + formId + " #" + fieldId);

                    // restore value
                    field.checked = value;

                    //trigger the afterRestoreEvent
                    $.formwizard.triggerEvent('formwizard.' + formId + '.afterRestore', "#" + formId + " #" + fieldId, {
                        fieldId: fieldId,
                        fieldValue: value
                    });
                },
                checkbox: (fieldId, value) => {
                    let field = document.querySelector("#" + formId + " #" + fieldId);

                    // restore value
                    field.checked = value;

                    //trigger the afterRestoreEvent
                    $.formwizard.triggerEvent('formwizard.' + formId + '.afterRestore', "#" + formId + " #" + fieldId, {
                        fieldId: fieldId,
                        fieldValue: value
                    });
                }
            };

            //iterate an retore data for all the fields
            for (let steps in storageFields) {
                if (storageFields.hasOwnProperty(steps)) {
                    let stepData = storageFields[steps];

                    if (stepData.stepType == 'tabular') {
                        $.formwizard.persistence.tabularData(stepData, steps, fieldTypes);
                        continue;
                    }

                    let fields = stepData.fields;

                    for (let id in fields) {
                        if (fields.hasOwnProperty(id)) {
                            let value = fields[id];

                            //get the field type
                            let fieldType = $("#" + id).get(0).type;

                            //cal the relative method to restore the value
                            fieldTypes.hasOwnProperty(fieldType) && fieldTypes[fieldType].call(this, id, value);
                        }
                    }
                }
            }
        },
        tabularData: function (stepData, steps, fieldTypes) {
            let rows = stepData.fields;

            //get the rows length
            let rowsLength = Object.keys(rows).length;

            //trigger click for add row if more than 1
            if (rowsLength > 1) {
                for (let iter = 1; iter <= rowsLength - 1; iter++) {
                    //trigger the click for the Add Row button
                    $("#" + steps + " .add_row").trigger('click');
                }
            }

            //iterate the rows
            for (let row in rows) {

                if (rows.hasOwnProperty(row)) {
                    let fields = rows[row];

                    //iterate the fields
                    for (let id in fields) {
                        if (fields.hasOwnProperty(id)) {
                            let value = fields[id];

                            //get the field type
                            let fieldType = $("#" + id).get(0).type;

                            //cal the relative method to restore the value
                            fieldTypes.hasOwnProperty(fieldType) && fieldTypes[fieldType].call(this, id, value);
                        }

                    }
                }
            }
        },
        init: (formId) => {

            //bind the onchange for the form inputs to update the form data as soon it is updated
            $(document).on("change", "#" + formId + " :input", function (e) {
                let stepData = $(this).closest("div.step-content").data('step');
                $.formwizard.persistence.savefield(e.currentTarget, formId, stepData);
            });

            //bind restore button
            $("#" + formId + " button.formwizard_restore").on("click", function (e) {
                e.preventDefault();
                $.formwizard.persistence.loadForm(formId);
            });

        }
    }
};

//used for the class difference to remove the classes
Array.prototype.classDiff = function (a) {
    return this.filter(function (i) {
        return a.indexOf(i) < 0;
    });
};

//removes all the classes from the element other than the specified
$.fn.removeClassesExceptThese = function (classList) {
    /* pass mutliple class name in array like ["first", "second"] */
    var $elem = $(this);

    if ($elem.length > 0) {
        var existingClassList = $elem.attr("class").split(" ");
        var classListToRemove = existingClassList.classDiff(classList);
        $elem
            .removeClass(classListToRemove.join(" "))
            .addClass(classList.join(" "));
    }
    return $elem;
};

Array.prototype.diff = function (arr2) {
    var ret = [];
    for (var i in this) {
        if (this.hasOwnProperty(i)) {
            if (arr2.hasOwnProperty(this[i]) && arr2[this[i]].length > 0) {
                ret.push(this[i]);
            }
        }
    }
    return ret;
};