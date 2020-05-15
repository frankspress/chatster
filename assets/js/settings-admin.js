(function ($) {

/**
 * Automatically opens the saved tab to show the options menu
 */
 function go_to_option() {
   let open_option = window.location.hash.substr(1);
   $("#"+open_option).delay(200).slideDown(300);
   history.pushState("", document.title, window.location.pathname + window.location.search);
 }
 go_to_option();

/**
* Animates Option windows slide up and down
*/
$(".ch-option-block").on('click', function(e) {
  if ( e.target.className == 'ch-option-title' ) {
    $(this).find('.ch-option-container').slideToggle(300, "linear",function() {

    });
  }
});


})(jQuery);
