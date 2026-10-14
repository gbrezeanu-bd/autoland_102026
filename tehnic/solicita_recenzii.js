/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$(document).ready(function () {
    $('.disabled').click(function (e) {
        e.preventDefault();
    })
    
    $('#recenzii').dynatable();
    
    $('#filtruRecenzii').change(function(){
        console.log('change to ' + $( "#filtruRecenzii option:selected" ).val());
        $('#filter_by').val($( "#filtruRecenzii option:selected" ).val());
        
        // trimitem formularul de selectare
        $('#filtreaza_recenzii').trigger('submit');
    });
});
