/* 
 * Custom JS for this website
 * 
 * @author Marius Ionel <webmaster@grupnet.ro>
 * @copyright (c) 2019, Marius Ionel
 */


jQuery(document).ready(function ($) {
    $('div.inventory .options-secondary div').remove();
    $('div.inventory .options-secondary ul li:nth-of-type(1n+7)').remove();
    $('div.inventory .options-secondary ul li *').removeAttr('size');


    // SIDEBAR SEARCH REFRESH ON CHANGE
    if ($(".css-dropdowns").length) {
//        $(".css-dropdowns").selectbox();

        $("select[name*='categorie']").selectbox('detach');
        $("select[name*='subcateg']").selectbox('detach');
        $("select[name*='marcat']").selectbox('detach');
    }

    $("select[name*='categorie']").selectbox({
        onChange: function (str) {
            // we must update selected option
            $("select[name*='categorie']").val(str);

            updateSubCateg(str);
            showUser(str);

        }
    });

    $("select[name*='subcateg']").selectbox();
    $("select[name*='marcat']").selectbox();
    // SIDEBAR SEARCH REFRESH ON CHANGE end

    // use AJAX for search results

    $('.pagination a').click(function (e) {
        e.preventDefault();
        console.log('pagination click');

        navLinkPage = $(this).attr('href');
        url = new URL(window.location.protocol + '//' + window.location.host + navLinkPage);
        var page = url.searchParams.get('page');
        if (!$('#page').length) {
            $('<input>').attr({type: 'hidden', 'id': 'page', 'name': 'page', value: page}).appendTo('#findmycar');
        } else {
            $('#page').attr({value: page});
        }

        findMyCarAJAX();
    });

    $('#findmycar .find_new_vehicle').click(function (e) {
        e.preventDefault();
        console.log('form button click');

        $('#page').attr({value: 1});
        findMyCarAJAX();
    });

    $('#findmycar').submit(function (e) {
        e.preventDefault();
        console.log('submit form');

        findMyCarAJAX();
    });



    // GDPR COOKIE Start HERE
    //     
    $('body').bsgdprcookies(cookieSettings);
    $('#cookiesSettings').on('click', function () {
        $('body').bsgdprcookies(cookieSettings, 'reinit');
    });
    updateCookiePreferences();//update cookie preferences on documnet ready
    // GDPR COOKIE EndHERE

    if ($('#myTab a').length) {
        $('.requestmoreinfo').click(function (e) {

            $('#myTab a[href="#informatii"]')[0].click();
            e.preventDefault();

            $('html, body').stop().animate({
                scrollTop: $('#moreinfo').offset().top
            }, 1000);

        });
    }

    $('form input[type="submit"].validate-before-submit').click(function (e) {
        e.preventDefault();
        var form = $(this).closest('form');

//        $(form).validate({
//            
//        });
        var validator = $(form).validate();
        validator.form();
        console.log(form);
    });

});

var cookieSettings = {
    title: 'Politica de Confidenţialitate şi de Cookies',
    message: 'Utilizăm modulele cookie pentru a vă oferi o experiență mai bună de navigare, pentru a analiza traficul pe site, pentru a personaliza conținutul și pentru a difuza anunțuri direcționate. <b> Citeşte mai mult  ',
    moreLinkLabel: 'aici.</b>',
    messageMaxHeightPercent: 30,
    moreLink: 'politica_de_confidentialitate.php',
    delay: 1000,
    advancedTitle: 'Selectează cookies pe care vrei să le accepţi',
    allowAdvancedOptions: true,
    advancedButtonLabel: 'Personalizez',
    acceptButtonLabel: 'Accept',
    advancedCookiesToSelect: [
        {
            name: 'necessary',
            title: 'Necesar',
            description: 'Obligatoriu pentru ca site-ul să funcționeze corect.',
            isFixed: true
        },
        {
            name: 'preferences',
            title: 'Preferinţe site',
            description: 'Obligatoriu pentru salvarea preferințelor dvs. de site, de ex. reţinerea numelui dvs. de utilizator etc.',
            isFixed: false
        },
        {
            name: 'analytics',
            title: 'Google Analytics',
            description: 'Obligatoriu pentru colectarea vizitelor site-ului, a tipurilor de browser etc.',
            isFixed: false
        },
        {
            name: 'googleads',
            title: 'Google Ads',
            description: 'Obligatoriu pentru personalizare google ads',
            isFixed: false
        },
        {
            name: 'marketing',
            title: 'Marketing',
            description: 'Necesar pentru marketing, de ex. buletine informative, social media etc.',
            isFixed: false
        }
    ],
    OnAccept: function () {
        updateCookiePreferences();
    }
};

var updateCookiePreferences = function () {
    var preferences = $.fn.bsgdprcookies.GetUserPreferences();
    var cookies = {
        necessary: ['PHPSESSID'],
        preferences: ['baz'],
        analytics: ['_gat', '_ga', '_git'],
        googleads: ["ad_storage", "ad_user_data", "ad_personalization", "analytics_storage"],
        marketing: ['maket', 'market1']
    };

    // remove all cookies from unset preferences
    var delete_cookie_types = $(['preferences', 'analytics', 'marketing', 'googleads']).not(preferences).get();

    jQuery.each(delete_cookie_types, function (key, val) {
        //var dele_cookie_types=cookies[val];
        jQuery.each(cookies[val], function (key, val) {
            console.log('delete cookie ' + val);

            //$.cookie(val, null, { path: '/' });
            document.cookie = val + '=; expires=Thu, 01-Jan-70 00:00:01 GMT;';
        });
    });
};

var findMyCarAJAX = function () {
    console.log('update content');
    $form = $('#findmycar');

    // remove txtsrc and marcat from url, if they are empty
    var url = $form.attr('action') + "?" + $form.serialize();
    if ($('#txtsrc').val() == '') {
        url = removeURLParameter(url, 'txtsrc');
    }
    if ($('#marcat option:selected').text() == '') {
        url = removeURLParameter(url, 'marcat');
    }
    //console.log(url);
    $.ajax({
        url: url,
        dataType: 'html',
        method: 'GET',
        success: function (data) {
            //console.log(data);
            $('.car_listings').html($(data).find('.car_listings'));

            $('.pagination a').click(function (e) {
                e.preventDefault();
                //console.log('pagination click');

                navLinkPage = $(this).attr('href');
                url = new URL(window.location.protocol + '//' + window.location.host + navLinkPage);
                var page = url.searchParams.get('page');
                if (!$('#page').length) {
                    $('<input>').attr({type: 'hidden', 'id': 'page', 'name': 'page', value: page}).appendTo('#findmycar');
                } else {
                    $('#page').attr({value: page});
                }

                findMyCarAJAX();
            });
        }
    });
}

function removeURLParameter(url, parameter) {
    //prefer to use l.search if you have a location/link object
    var urlparts = url.split('?');
    if (urlparts.length >= 2) {

        var prefix = encodeURIComponent(parameter) + '=';
        var pars = urlparts[1].split(/[&;]/g);

        //reverse iteration as may be destructive
        for (var i = pars.length; i-- > 0; ) {
            //idiom for string.startsWith
            if (pars[i].lastIndexOf(prefix, 0) !== -1) {
                pars.splice(i, 1);
            }
        }

        url = urlparts[0] + '?' + pars.join('&');
        return url;
    } else {
        return url;
    }
}


/**
 * updateSubCateg
 * @param {type} str
 * @returns {undefined}
 */
var updateSubCateg = function (str) {
    var url = "ajax_subcateg.php";
    url = url + "?q=" + str;
    url = url + "&sid=" + Math.random();
    console.log('Update subcateg ' + str);

    $.ajax({
        url: url,
        dataType: 'html',
        method: "GET",
        success: function (response) {
            //console.log(response);
            $("select[name*='subcateg']").selectbox('detach');
            $("select[name*='subcateg']").parent().html(response);

            // we must rebound $("select[name*='subcateg']")
            $("select[name*='subcateg']").selectbox();

            // ***** prostie
//            $("select[name*='categorie']").selectbox('detach')
//                    .selectbox('attach');
        }
    });

};

/**
 * 
 * @param {type} str
 * @returns {undefined}
 */
var showUser = function (str) {

    var url = "getmake.php";
    url = url + "?q=" + str;
    url = url + "&sid=" + Math.random();

    $.ajax({
        url: url,
        dataType: 'html',
        method: "GET",
        success: function (response) {
            //console.log(response);
            $("select[name*='marcat']").selectbox('detach');
            $("select[name*='marcat']").parent().html(response);
            $("select[name*='marcat']").selectbox();
        }
    });
};

function removeURLParameter(url, parameter) {
    //prefer to use l.search if you have a location/link object
    var urlparts = url.split('?');
    if (urlparts.length >= 2) {

        var prefix = encodeURIComponent(parameter) + '=';
        var pars = urlparts[1].split(/[&;]/g);

        //reverse iteration as may be destructive
        for (var i = pars.length; i-- > 0; ) {
            //idiom for string.startsWith
            if (pars[i].lastIndexOf(prefix, 0) !== -1) {
                pars.splice(i, 1);
            }
        }

        url = urlparts[0] + '?' + pars.join('&');
        return url;
    } else {
        return url;
    }
}
