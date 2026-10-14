/* 
 * Script to validate Cerere recenzie
 */

// Wait for jQuery and jQuery validator to be loaded
(function() {
    function initValidator() {
        // Check if jQuery and jQuery validator are available
        if (typeof jQuery === 'undefined' || typeof jQuery.fn.validate === 'undefined' || typeof jQuery.validator === 'undefined') {
            // Retry after a short delay
            setTimeout(initValidator, 100);
            return;
        }
        
        var $ = jQuery;
        
        /*
         * Translated default messages for the jQuery validation plugin.
         * Locale: RO (Romanian, limba română)
         */
        if ($.validator && $.validator.messages) {
            $.extend($.validator.messages, {
                required: "Acest câmp este obligatoriu.",
                remote: "Va rugăm să completati acest câmp.",
                email: "Va rugăm să introduceti o adresă de email validă",
                url: "Va rugăm sa introduceti o adresă URL validă.",
                date: "Va rugăm să introduceti o dată corectă.",
                dateISO: "Va rugăm să introduceti o dată (ISO) corectă.",
                number: "Va rugăm să introduceti un număr întreg valid.",
                digits: "Va rugăm să introduceti doar cifre.",
                creditcard: "Va rugăm să introduceti un numar de carte de credit valid.",
                equalTo: "Va rugăm să reintroduceti valoarea.",
                extension: "Va rugăm să introduceti o valoare cu o extensie validă.",
                maxlength: $.validator.format("Va rugăm să nu introduceti mai mult de {0} caractere."),
                minlength: $.validator.format("Va rugăm să introduceti cel puțin {0} caractere."),
                rangelength: $.validator.format("Va rugăm să introduceti o valoare între {0} și {1} caractere."),
                range: $.validator.format("Va rugăm să introduceti o valoare între {0} și {1}."),
                max: $.validator.format("Va rugăm să introduceti o valoare egal sau mai mică decât {0}."),
                min: $.validator.format("Va rugăm să introduceti o valoare egal sau mai mare decât {0}.")
            });
        }

        // Initialize form validation when DOM is ready
        $(document).ready(function () {
            // Check again if validator is available
            if (typeof $.fn.validate === 'undefined') {
                console.warn('jQuery Validator not loaded, retrying...');
                setTimeout(arguments.callee, 100);
                return;
            }
            
            $('#cerere-oferta').validate({
                ignore: ".ignore",
                rules: {
                    hiddenRecaptcha: {
                        required: function () {
                            if (typeof grecaptcha !== 'undefined' && grecaptcha.getResponse() == '') {
                                return true;
                            } else {
                                return false;
                            }
                        }
                    }
                },
                messages: {
                    hiddenRecaptcha: "Va rugam sa validati ca nu sunteti robot"
                },
                errorElement: "em",
                errorPlacement: function (error, element) {
                    // Add the `help-block` class to the error element
                    error.addClass("help-block");

                    if (element.prop("type") === "checkbox") {
                        error.insertAfter(element.parent("label"));
                    } else {
                        error.insertAfter(element);
                    }

                },
                highlight: function (element, errorClass, validClass) {
                    $(element).parents(".col-sm-5").addClass("has-error").removeClass("has-success");
                },
                unhighlight: function (element, errorClass, validClass) {
                    $(element).parents(".col-sm-5").addClass("has-success").removeClass("has-error");
                }
            });
        });
    }
    
    // Start initialization
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initValidator);
    } else {
        initValidator();
    }
})();
