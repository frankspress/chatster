(function ($) {
"use strict";

  /**
   * Builds the reply messages and shows the reply section
   */
  function esc_json(str) {
     return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
  }
  function reply_template( message_obj ) {
    let $reply = $("<div>", {id: "message-" + esc_json(message_obj.id), "class": "ch-single-reply"});
    let $reply_info = $("<div>", { "class": "ch-reply-info" });
    let $reply_text = $("<div>", {"class": "ch-reply-text"});

    $reply_info =  '<span>'+ esc_json(message_obj.admin_name ? message_obj.admin_name : '') +'</span>';
    $reply_info += '<span>('+ esc_json(message_obj.admin_email)+')</span>';
    $reply_info += '<span>'+ esc_json(message_obj.replied_at)+'</span>';

    $reply_text.text(message_obj.message);
    $reply.append($reply_info).append($reply_text);
    return $reply;
  }
  function build_reply_conv(replies, request_id) {

    if ( replies ) {
      var $reply_container = $('#reply-section-' + request_id).find('.reply-all-container').empty();
      $.each( replies, function( key, message ) {
        let $reply = reply_template(message);
        $reply_container.append($reply);
      });

      $reply_container.slideDown(300);

    }

  }
  function get_replies(request_id) {

    var payload = { request_id: request_id };

    $.ajax( {
        url: chatsterDataAdmin.api_base_url + '/request/admin/retrieve',
        method: 'POST',
        beforeSend: function ( xhr ) {
            xhr.setRequestHeader( 'X-WP-Nonce', chatsterDataAdmin.nonce );
        },
        data: payload,
        success: function(data) {
          build_reply_conv(data.payload, request_id);
        },
        error: function(error) {

        },

      } ).done( function ( response ) {
          $('#reply-section-' + request_id).find('.ch-small-loader').stop().slideUp(10, function(){ $(this).addClass('hidden');});
      });
  }
  $('.reply').on('click', function(e) {
      e.preventDefault(); e.stopPropagation();
      let request_id = $(this).parent().attr('data-request_id');
      $('.reply-container').slideUp(100);
      $('#reply-section-' + request_id).find('.ch-small-loader').stop().removeClass('hidden').slideDown(100);
      $('#reply-section-' + request_id).find('.reply-container').slideDown(300);
      get_replies(request_id);

  });

  /**
   * Changes the pinned status for each request
   */
  function change_flagged(request_id, flag_status) {
    let $flag = $('#request-' + request_id).find('.pinned-flag');
    // Reverses to actual status if DB not updated.
    if (flag_status) {
      $flag.removeClass('unflagged');
    } else {
      $flag.addClass('unflagged');
    }
    // Removes the request id from the debounce array so it can be called again.
    var index = debounce_id.indexOf(request_id);
    if (index > -1) {
       debounce_id.splice(index, 1);
    }

  }
  function pin_request(request_id, pinned_status) {

    var payload = { request_id: request_id, pinned_value: pinned_status };

    $.ajax( {
        url: chatsterDataAdmin.api_base_url + '/request/admin/pin',
        method: 'POST',
        beforeSend: function ( xhr ) {
            xhr.setRequestHeader( 'X-WP-Nonce', chatsterDataAdmin.nonce );
        },
        data: payload,
        success: function(data) {
          change_flagged(request_id, data.payload);
        },
        error: function(error) {
          change_flagged(request_id, !pinned_status);
        },

      } ).done( function ( response ) {

      });
  }
  var debounce_id = [];
  $('.pinned-flag').on('click', function() {
    var request_id = parseInt( $(this).parent().attr('data-request_id') );
    if ( debounce_id.length == 0 || debounce_id.indexOf(request_id) == -1 ) {
        debounce_id.push(request_id);
        let pinned_status = $(this).hasClass('unflagged') == 1 ? 1 : 0;
        let $flag = $('#request-' + request_id).find('.pinned-flag');
        $flag.toggleClass('unflagged');
        pin_request(request_id, pinned_status);
    }
  });

  /**
   * Sends the email and saves the reply to the database
   */
  function send_reply_email(request_id, reply_text) {

     var payload = { request_id: request_id, reply_text: reply_text };

     $.ajax( {
         url: chatsterDataAdmin.api_base_url + '/request/admin/reply',
         method: 'POST',
         beforeSend: function ( xhr ) {
             xhr.setRequestHeader( 'X-WP-Nonce', chatsterDataAdmin.nonce );
         },
         data: payload,
         success: function(data) {
          if (data.payload) {
            $('#reply-section-' + request_id).find('textarea').val('');
            let $reply_container = $('#reply-section-' + request_id).find('.reply-all-container');
            let $reply = reply_template(data.payload);
            $reply.hide();
            $reply_container.append($reply);
            $reply.slideDown(300);
          }
         },
         error: function(error) {

         },

       } ).done( function ( response ) {
       });
   }
  $(".ch-reply-form").submit(function(e){
      e.preventDefault();
      let reply_text = $(this).find('textarea').val();
      let request_id =  $(this).attr('data-request_id');
      send_reply_email(request_id, reply_text);
  });

})(jQuery);
