// Wait for jQuery and jQuery validator to be loaded
(function() {
    function initValidator() {
        // Check if jQuery and jQuery validator are available
        if (typeof jQuery === 'undefined' || typeof jQuery.fn.validate === 'undefined' || typeof jQuery.validator === 'undefined') {
            // Retry after a short delay
            setTimeout(initValidator, 100);
            return;
        }
        
        jQuery.validator.setDefaults({
            submitHandler: function(form) {

                var vizUrl = window.location;
                jQuery('#vizUrl').val(vizUrl);
                var okMessage = "Notificarea dumneavoastra a fost trimisa cu succes!<br>";
                okMessage = okMessage + "Va rugam sa verificati acum mesajul AUTOLAND primit pe adresa de email furnizata prin care v-am transmis cateva informatii interesante, cu caracter general.<br>";
                okMessage = okMessage + "Detalii in plus privind oferta noastra de finantare auto puteti gasi in site pe pagina Leasing/Rate.<br>";
                okMessage = okMessage + "Pentru informatii suplimentare privind masina dorita de dumneavoastra (eventual consultanta necesara pentru a gasi impreuna, de comun acord cea mai buna varianta auto pentru dvs) precum si pentru eventuale intrebari privind sistemul nostru de leasing auto sau rate va rugam sa ne contactati telefonic de Luni pana Vineri in intervalul 10-18 la unul din numerele din pagina Contact. <br>";
                okMessage = okMessage + "Va multumim pentru interesul dumneavoastra si va asiguram ca ati gasit si veti colabora cu acei profesionisti de nota 10 (zece) pe care ii doriti.  <br>";
                okMessage = okMessage + "<br>";
                okMessage = okMessage + "Cu respect,<br>";
                okMessage = okMessage + "Echipa AUTOLAND ROMANIA";


                jQuery.ajax({
                    url: BASE_PATH + "includes/form_interesatde_mail_processor.php",
                    type: "post",
                    data: jQuery(form).serialize(),
                    success: function(response) {
                        if (response.status == 'success') {
                            jQuery("#result").hide().html('<div class="success">' + okMessage + '</div>').slideDown();
                            // reset inputed values
                            jQuery(form).trigger("reset");
                            jQuery('.back_to_top').click();
                        } else {
                            jQuery("#result").hide().html('<div class="error">' + "Mesajul nu a fost trimis. Va rugam sa verificati datele introduse!" + response.error + '</div>').slideDown();
                        }
                        // you will get response from your php page (what you echo or print)                 
                        //console.log(response);
                        //alert(response);
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        // here we have real trouble connecting with server
                        //console.log(jqXHR);
                        jQuery("#result").hide().html('<div class="error">' + (jqXHR.statusText || textStatus || 'Eroare de conexiune') + '</div>').slideDown();
                        //console.log(textStatus);
                        //console.log(errorThrown);
                        //console.log(textStatus, errorThrown);
                    }


                });
            }
        });

        jQuery(document).ready(function($) {
            jQuery("#frmInteresat").validate({
                //debug: true,// must be commented out when in production, because it inhibits
                ignore: ".ignore",
                rules: {
                    firstname: "required",
                    vizNume: {
                        required: true,
                        minlength: 3
                    },
                    vizTelefon: {
                        required: true,
                        phoneUK: true
                    },
                    vizEmail: {
                        required: true,
                        email: true
                    },
                    vizMesaj: {
                        required: true,
                        minlength: 10
                    },
                    vizJudet: {
                        required: true
                    },
                    vizAchizitie: {
                        required: true
                    },
                    vizPlatain: {
                        required: true
                    },
                    //agree: "required",
                    hiddenRecaptcha: {
                        required: function() {
                            if (typeof grecaptcha !== 'undefined' && grecaptcha.getResponse() == '') {
                                return true;
                            } else {
                                return false;
                            }
                        }
                    },
                },
                messages: {
                    vizNume: {
                        required: "Va rugam sa introduceti numele si prenumele",
                        minlength: "Numele trebuie sa aiba minim 3 caractere"
                    },
                    vizEmail: "Va rugam sa introduci o adresa de email valida",
                    vizTelefon: "Va rugam sa introduceti telefonul",
                    vizMesaj: "Mesajul nu poate fi mai scurt de 50 de caractere",
                    vizJudet: "Va rugam sa selectati judetul",
                    vizAchizitie: "Va rugam sa selectati tipul de achizitie",
                    vizPlatain: "Va rugam sa selectati modalitatea de plata",
                    hiddenRecaptcha: "",
                    //agree: "Trebuie sa fiti de acord cu termenii si conditiile"
                },
                errorElement: "em",
                errorPlacement: function(error, element) {
                    // Add the `help-block` class to the error element
                    error.addClass("help-block");

                    if (element.prop("type") === "checkbox") {
                        error.insertAfter(element.parent("label"));
                    } else {
                        error.insertAfter(element);
                    }

                },
                highlight: function(element, errorClass, validClass) {
                    jQuery(element).parents(".col-sm-5").addClass("has-error").removeClass("has-success");
                },
                unhighlight: function(element, errorClass, validClass) {
                    jQuery(element).parents(".col-sm-5").addClass("has-success").removeClass("has-error");
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
