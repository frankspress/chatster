(function ($) {

/**
 * Insert Link Autocomplete to chat
 */
 var autocompleteURL = chatsterDataAdmin.wp_api_base_url + 'wp/v2/search';
 $('.ch-chat-autocomplete').autocomplete({ hint: false },
    [
      {
         source: function(query, cb) {
             $.ajax( {
                 url: autocompleteURL + '?search=' + query + '&_embed' ,
                 method: 'GET',
                 beforeSend: function ( xhr ) {
                     xhr.setRequestHeader( 'X-WP-Nonce', chatsterDataAdmin.nonce );
                 }
               } ).then( function ( data ) {
                 console.log(data);
                    // cb(data.payload)
               })
         },
         debounce: 500,
         templates: {
            suggestion: function(suggestion, answer) {
                if ( suggestion.type == 'product' ) {
                  return ch_product_suggestion(suggestion);
                } else {
                  return ch_post_suggestion(suggestion);
                }
            }
         }
       }
     ]
   );

   /**
    * Template functions
    */
    function ch_product_suggestion( list ) {

      return template;
    }
    function ch_post_suggestion( list ) {

      return template;
    }

})(jQuery);
