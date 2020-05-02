(function ($) {
"use strict";


  function esc_json(str) {
     return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
  }
  function build_reply_conv(replies, request_id) {

    if ( replies ) {
      var $reply_container = $('#reply-section-' + request_id).find('.reply-all-container').empty();
      $.each( replies, function( key, message ) {

        let $reply = $("<div>", {id: "message-" + esc_json(message.id), "class": "ch-single-reply"});
        let $reply_info = $("<div>", { "class": "ch-reply-info" });
        let $reply_text = $("<div>", {"class": "ch-reply-text"});

        $reply_info =  '<span>'+ esc_json(message.admin_name ? message.admin_name : '') +'</span>';
        $reply_info += '<span>('+ esc_json(message.admin_email)+')</span>';
        $reply_info += '<span>'+ esc_json(message.replied_at)+'</span>';

        $reply_text.text(message.message);
        $reply.append($reply_info).append($reply_text);

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

  function change_flagged(request_id, flag_status) {
    let $flag = $('#request-' + request_id).find('.pinned-flag');
    if (flag_status) {
      $flag.removeClass('unflagged');
    } else {
      $flag.addClass('unflagged');
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

        },

      } ).done( function ( response ) {
          var index = debounce_id.indexOf(request_id);
          if (index > -1) {
             debounce_id.splice(index, 1);
          }
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

})(jQuery);
