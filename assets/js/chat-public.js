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

  }


 setInterval(long_poll, 4000);

long_poll();

})(jQuery);
