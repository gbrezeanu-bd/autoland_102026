/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$(document).ready(function () {
//    if ($(".css-dropdowns").length) {
//        $("select[name*='users']").selectbox('detach');
//        $("select[name*='subcateg']").selectbox('detach');
//        $("select[name*='marcat']").selectbox('detach');
//    }
//
//    $("select[name*='users']").selectbox({
//        onChange: function(str){
//            updateSubCateg(str);
//            showUser(str);
//        }
//    });
//
//    $("select[name*='subcateg']").selectbox();
//    $("select[name*='marcat']").selectbox();
});


//var updateSubCateg = function (str) {
//    var url = "ajax_subcateg.php";
//    url = url + "?q=" + str;
//    url = url + "&sid=" + Math.random();
//
//    $.ajax({
//        url: url,
//        dataType: 'html',
//        method: "GET",
//        success: function (response) {
//            //console.log(response);
//            //$("select[name*='subcateg']").selectbox('detach');
//            $("select[name*='subcateg']").parent().html(response);
//
//            // we must rebound $("select[name*='subcateg']")
//            $("select[name*='subcateg']").selectbox();
//        }
//    });
//
//};
//
//var showUser = function (str) {
//
//    var url = "getmake.php";
//    url = url + "?q=" + str;
//    url = url + "&sid=" + Math.random();
//
//    $.ajax({
//        url: url,
//        dataType: 'html',
//        method: "GET",
//        success: function (response) {
//            //console.log(response);
//            $("select[name*='marcat']").parent().html(response);
//            $("select[name*='marcat']").selectbox();
//        }
//    });
//};