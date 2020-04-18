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

  function presence() {
    $.ajax( {

        url: chatsterDataAdmin.api_base_url + '/chat/presence/customer',
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
 setInterval(presence, 3000);


 function chat_form() {

   $.ajax( {

       url: chatsterDataAdmin.api_base_url + '/chat/form-data',
       method: 'POST',
       beforeSend: function ( xhr ) {
           xhr.setRequestHeader( 'X-WP-Nonce', chatsterDataAdmin.nonce );
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

 setInterval(chat_form, 4000);

})(jQuery);
