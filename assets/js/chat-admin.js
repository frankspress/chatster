(function ($) {

console.log(chatsterDataAdmin);


function presence_admin() {
  $.ajax( {

      url: chatsterDataAdmin.api_base_url + '/chat/presence/admin',
      method: 'POST',
      beforeSend: function ( xhr ) {
          xhr.setRequestHeader( 'X-WP-Nonce', chatsterDataAdmin.nonce );
      },
      data: {},
      success: function(data) {
        console.log(data);
      },
      error: function(error) {

      },

    } ).done( function ( response ) {

    });
}
// setInterval(long_poll, 3000);
setInterval(presence_admin, 3000);

function long_poll() {

  $.ajax( {
      url: chatsterDataAdmin.api_base_url + '/product/',
      method: 'POST',
      beforeSend: function ( xhr ) {
          xhr.setRequestHeader( 'X-WP-Nonce', chatsterDataAdmin.nonce );
      },
      data: payload,
      success: function(data) {
          console.log(data);
      },
      error: function(error) {

      },

    } ).done( function ( response ) {

    });

}

function chat_insert() {

}




})(jQuery);
