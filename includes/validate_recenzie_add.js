/* 
 * Script to validate recenzie
 * 
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
        
        // Set defaults if validator is available
        if ($.validator && $.validator.setDefaults) {
            $.validator.setDefaults({
                // Defaults can be added here if needed
            });
        }

        $(document).ready(function () {
            // Check again if validator is available
            if (typeof $.fn.validate === 'undefined') {
                console.warn('jQuery Validator not loaded, retrying...');
                setTimeout(arguments.callee, 100);
                return;
            }
            
            $('#recenzie').validate({
                ignore: ".ignore",
                rules: {
                    firstname: "required",
                    prenume: {
                        required: true,
                        minlength: 3
                    },
                    nume: {
                        required: true,
                        minlength: 3
                    },
                    
                    continut_recenzie: {
                        required: true,
//                minlength: 100,
//                maxlenght:500
                    },
                    //agree: "required",
                    hiddenRecaptcha: {
                        required: function () {
                            if (typeof grecaptcha !== 'undefined' && grecaptcha.getResponse() == '') {
                                return true;
                            } else {
                                return false;
                            }
                        }
                    },
                },
                messages: {
//            vizNume: {
//                required: "Va rugam sa introduceti numele si prenumele",
//                minlength: "Numele trebuie sa aiba minim 3 caractere"
//            },
                    prenume: "Prenumele trebuie sa aiba minim 3 caractere",
                    nume: "Numele trebuie sa aiba minim 3 caractere",
                    continut_recenzie: "Recenzia trebuie sa fie intre 100 si 500 caractere",
                    hiddenRecaptcha: "",
                    //agree: "Trebuie sa fiti de acord cu termenii si conditiile"
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


            // update the number of character for recenzie
            $('.caractere_recenzie_min').text($('#continut_recenzie').attr('minlength'));
            $('.caractere_recenzie_max').text($('#continut_recenzie').attr('maxlength'));
            $('.caractere_recenzie').text($('#continut_recenzie').attr('maxlength'));
            
            $('#continut_recenzie').keyup(function () {
                var min_lenght = $(this).attr('minlength');
                var max_lenght = $(this).attr('maxlength');

                var text_length = $('#continut_recenzie').val().length;
                var text_remaining = max_lenght - text_length;
                
                $('.caractere_recenzie').text(text_remaining);
                //console.log(text_remaining);
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
