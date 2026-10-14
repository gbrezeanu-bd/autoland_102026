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


//    if ($(".css-dropdowns").length) {
////        $(".css-dropdowns").selectbox();
//
//        $("select[name*='categorie']").selectbox('detach');
//        $("select[name*='subcateg']").selectbox('detach');
//        $("select[name*='marcat']").selectbox('detach');
//    }
//    
//    $("select[name*='categorie']").selectbox({
//        onChange: function (str) {
//            // we must update selected option
//            $("select[name*='categorie']").val(str);
//
//            updateSubCateg(str);
//            showUser(str);
//
//        }
//    });
//    $("select[name*='subcateg']").selectbox();
//    $("select[name*='marcat']").selectbox();
    



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



// OLD SELECTMAKE.JS
//var xmlHttpsubcateg = null;
//var xmlHttpmarca = null;

//function updateSubCateg(str) {
//	xmlHttpsubcateg = GetXmlHttpObject();
//	if (xmlHttpsubcateg == null) {
//		alert ("Browser does not support HTTP Request");
//		return;
//	}
//	var url = "includes/ajax_subcateg.php";
//	url = url + "?q=" + str;
//	url = url + "&sid=" + Math.random();
//	xmlHttpsubcateg.onreadystatechange = stateSubCategChanged; 
//	xmlHttpsubcateg.open("GET",url,true);
//	xmlHttpsubcateg.send(null);	
//}
//function stateSubCategChanged() {
//	if (xmlHttpsubcateg.readyState == 4 || xmlHttpsubcateg.readyState == "complete")
//	{ 
//            //$()
//            document.getElementById("subcategHint").innerHTML = xmlHttpsubcateg.responseText;
//	}
//}

//function showUser(str)
//{ 
//	xmlHttpmarca=GetXmlHttpObject()
//	if (xmlHttpmarca==null)
//	{
//		alert ("Browser does not support HTTP Request")
//		return
//	}
//	var url="getmake.php"
//	url=url+"?q="+str
//	url=url+"&sid="+Math.random()
//	xmlHttpmarca.onreadystatechange=stateChanged 
//	xmlHttpmarca.open("GET",url,true)
//	xmlHttpmarca.send(null)
//}

//function stateChanged() 
//{ 
//	if (xmlHttpmarca.readyState==4 || xmlHttpmarca.readyState=="complete")
//	{ 
//		document.getElementById("txtHint").innerHTML = xmlHttpmarca.responseText 
//	} 
//}

//function GetXmlHttpObject()
//{
//var xmlHttp=null;
//try
// {
// // Firefox, Opera 8.0+, Safari
// xmlHttp=new XMLHttpRequest();
// }
//catch (e)
// {
// //Internet Explorer
// try
//  {
//  xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
//  }
// catch (e)
//  {
//  xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
//  }
// }
//return xmlHttp;
//}