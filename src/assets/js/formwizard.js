/*jshint esversion: 6 */
/*globals $:true, */

if (typeof jQuery === "undefined") {
    throw new Error("jQuery plugins need to be before this file");
}

$.formwizard = {
    currentButtonTarget: null,
    observerObj: null,
    fields: [],
    submit: false,
    helper: {
        shake: function (form) {
            $(form + " .sw-main").addClass("shake animated");
            setTimeout(function () {
                $(form + " .sw-main").removeClass("shake animated");
            }, 1000);
        },
        appendButtons: function (options) {
            // Toolbar next, previous and finish custom buttons
            var formwizardBtnNext = $('<button class="formwizard-next"></button>').html(options.iconNext + '&nbsp' + options.labelNext)
                .addClass(options.classNext);

            var formwizardBtnPrev = $('<button class="formwizard-prev"></button>').html(options.iconPrev + '&nbsp;' + options.labelPrev)
                .addClass(options.classPrev)
                .on('click', function (e) {
                    e.preventDefault();
                    $.formwizard.formNavigation.previous(e.target);
                });

            var formwizardBtnFinish = $('<button class="formwizard-finish" type="submit"/></button>').html(options.iconFinish + '&nbsp;' + options.labelFinish)
                .addClass(options.classFinish);

            var combined = formwizardBtnNext.add(formwizardBtnFinish);

            $(combined).on('click', function (e) {
                e.preventDefault();
                if ($(options.form).yiiActiveForm('data').attributes.length) {
                    return $.formwizard.validation.run(options.form, e);
                }
                if ($(e.target).hasClass('formwizard-finish')) {
                    $(options.form).yiiActiveForm('submitForm');
                }
                return $.formwizard.formNavigation.next(e.target);
            });

            return [formwizardBtnPrev, formwizardBtnNext, formwizardBtnFinish];
        },
        updateButtons: function (wizardContainerId) {
            $(wizardContainerId).on("showStep", function (e, anchorObject, stepNumber, stepDirection, stepPosition) {
                let btnPrev = $(wizardContainerId + " > .sw-toolbar > .sw-btn-group-extra button.formwizard-prev");
                let btnFinish = $(wizardContainerId + " > .sw-toolbar > .sw-btn-group-extra >button.formwizard-finish");
                let btnNext = $(wizardContainerId + " > .sw-toolbar > .sw-btn-group-extra >button.formwizard-next ");

                if (stepPosition === 'first') {
                    btnPrev.addClass('disabled');
                    btnFinish.addClass('hidden d-none');
                    btnNext.removeClass('hidden d-none');
                } else if (stepPosition === 'final') {
                    btnNext.addClass('hidden d-none');
                    btnFinish.removeClass('hidden d-none');
                    btnPrev.removeClass('disabled hidden d-none');
                } else {
                    btnPrev.removeClass('disabled');
                    btnNext.removeClass('disabled hidden d-none');
                    btnFinish.addClass('hidden d-none');
                }
            });
        },
        currentIndex: function (form) {
            return $(form + " ul.step-anchor>li.active").index();
        }
    },
    validation: {
        run: function (form, event) {
            $.formwizard.currentButtonTarget = event.target;
            $(form).yiiActiveForm('validate', true);
        },
        bindAfterValidate: function (form) {
            $(form).on('afterValidate', function (event, messages, errorAttributes) {
                event.preventDefault();
                let formName = $(this).attr('id');
                let currentIndex = $.formwizard.helper.currentIndex(form);
                let res = $.formwizard.fields[formName][currentIndex].diff(messages);
                console.log(res, $.formwizard.fields);
                if (!res.length) {
                    //check if last step then submit form
                    let isLastStep = currentIndex == $(form + ' .step-anchor').find('li').length - 1;
                    if (isLastStep) {
                        $.formwizard.submit = true;
                        return true;
                    } else {
                        $(form).yiiActiveForm('resetForm');
                        $.formwizard.formNavigation.next($.formwizard.currentButtonTarget);
                    }
                } else {
                    $.formwizard.helper.shake(form);
                }
                return false;

            }).on('beforeSubmit', function (event) {
                event.preventDefault();
                if ($.formwizard.submit) {
                    return true;
                }
                return false;
            });
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
                    let attribute = i.replace(/\-([0-9]+)/g, '');
                    $(form).yiiActiveForm('updateAttribute', attribute, messages[i]);
                }
            }
        }
    },
    formNavigation: {
        next: function (target) {
            let containerId = $(target).parent().closest('.sw-main').attr('id');
            $("#" + containerId).smartWizard('next');
        },
        previous: function (target) {
            let containerId = $(target).parent().closest('.sw-main').attr('id');
            $("#" + containerId).smartWizard('prev');
        }
    },
    observer: {
        start: function (selector) {
            // select the target node in select2
            var target = document.querySelector(selector);

            $.formwizard.observerObj = $.formwizard.observer.observerInstance();
            // configuration of the observer:
            var config = {
                childList: true,
                attributes: true,
                subtree: true
            };
            // pass in the target node, as well as the observer options
            $.formwizard.observerObj.observe(target, config);
        },

        /**
         * 
         * @returns {MutationObserver}
         */
        observerInstance: function () {
            // create an observer instance
            return new MutationObserver(function (mutations) {
                mutations.forEach(function (mutation) {
                    if (mutation.type == 'childList') {
                        $.themematerial.init();
                        $.formwizard.observerObj.disconnect();
                    }
                });
            });
        }
    }

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