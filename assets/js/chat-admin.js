(function ($) {

console.log(chatsterDataAdmin);




function long_poll() {

  $.ajax( {
      url: chatsterDataAdmin.api_base_url + '/product/',
      method: 'POST',
      beforeSend: function ( xhr ) {
          xhr.setRequestHeader( 'X-WP-Nonce', chatsterDataAdmin.nonce );
      },
      data: payload,
      success: function(data) {

      },
      error: function(error) {

      },

    } ).done( function ( response ) {

    });

}




})(jQuery);
