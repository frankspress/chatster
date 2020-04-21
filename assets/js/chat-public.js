(function ($) {

  /**
   * It sends a presence ping to the database
   */
  function presence() {
    $.ajax( {

        url: chatsterDataPublic.api_base_url + '/chat/presence/customer',
        method: 'POST',
        beforeSend: function ( xhr ) {
            xhr.setRequestHeader( 'X-WP-Nonce', chatsterDataPublic.nonce );
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
  setInterval(presence, 10000);
  presence();



  function long_poll() {

    $.ajax( {

        url: chatsterDataPublic.api_base_url + '/chat/poll/',
        method: 'POST',
        beforeSend: function ( xhr ) {
            xhr.setRequestHeader( 'X-WP-Nonce', chatsterDataPublic.nonce );
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

        url: chatsterDataPublic.api_base_url + '/chat/insert/',
        method: 'POST',
        beforeSend: function ( xhr ) {
            xhr.setRequestHeader( 'X-WP-Nonce', chatsterDataPublic.nonce );
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



 function chat_form() {

   $.ajax( {

       url: chatsterDataPublic.api_base_url + '/chat/form-data',
       method: 'POST',
       beforeSend: function ( xhr ) {
           xhr.setRequestHeader( 'X-WP-Nonce', chatsterDataPublic.nonce );
       },
       data: {customer_name: 'meeee', chat_subject: 'testing... ee'},
       success: function(data) {
         console.log(data);
       },
       error: function(error) {

       },

     } ).done( function ( response ) {

     });
 }

// setInterval(chat_form, 4000);




})(jQuery);
