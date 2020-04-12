(function ($) {



  console.log('cornetto');


  function long_poll() {

    $.ajax( {

        url: chatsterDataAdmin.api_base_url + '/chat/poll/',
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

  function chat_insert() {

    $.ajax( {

        url: chatsterDataAdmin.api_base_url + '/chat/insert/',
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



})(jQuery);
