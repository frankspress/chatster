(function ($) {
"use strict";
  /**
   * It sends a presence ping to the database
   */
  function presence_admin() {
    $.ajax( {

        url: chatsterDataAdmin.api_base_url + '/chat/presence/admin',
        method: 'POST',
        beforeSend: function ( xhr ) {
            xhr.setRequestHeader( 'X-WP-Nonce', chatsterDataAdmin.nonce );
        },
        data: {},
        success: function(data) {
          //console.log(data);
        },
        error: function(error) {

        },

      } ).done( function ( response ) {

      });
  }
  setInterval(presence_admin, 10000);
  presence_admin();

  /**
   * Inserts current messages into the conversation
   */
  function insert_messages( new_message, temp_id ) {

    let customer_id = $("#ch-message-board").attr("data-curr_customer_id");
    let conv_id = $("#ch-message-board").attr("data-conv_id");

    var payload = { new_message: new_message, msg_link: get_msg_links(), conv_id: conv_id, customer_id: customer_id, temp_id: temp_id };

    $.ajax( {
        url: chatsterDataAdmin.api_base_url + '/chat/insert/admin',
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
  function get_attachment_objs() {

    let attachments = $(".ch-product-auto");
    let attachment_cont = [];
    if ( attachments ) {

      $.each( attachments, function( key, attachment ) {
        let found_attachment = {};
        found_attachment['id'] = $(attachment).attr('data-link_id');
        found_attachment['thumbnail'] = $(attachment).find('img').attr('src');
        found_attachment['title'] = $(attachment).find('.ch-auto-title').text();
        found_attachment['excerpt'] = $(attachment).find('.ch-auto-excerpt').text();
        found_attachment['link'] = $(attachment).find('.ch-auto-exlink a').attr('href');
        attachment_cont.push(found_attachment);
      });
      return attachment_cont;
    }
    return false;
  }
  function get_msg_links() {
    let attachments = $(".ch-product-auto");
    let links = [];
    if ( attachments ) {
      $.each( attachments, function( key, attachment ) {
        let link_id = $(attachment).attr('data-link_id');
        links.push(link_id);
      });
    }
    $("#ch-attachments div").slideUp( 300, function() {$("#ch-attachments div").remove();});
    return links;
  }
  $('#ch-reply').on('keypress', function(e) {
    if ( e.keyCode == 13 && ! e.shiftKey ) {
      e.preventDefault();
      var message = $(this).val().trim();
      if (message && ( message.length <= 799 ) ) {
        $(this).attr('rows', 1 ).val('');
        let temp_id = (new Date()).getTime().toString();
        temp_id = parseInt(temp_id.slice(4, temp_id.length));
        // Create elements
        let $message_cont = $("<div>", {id: "message-"+temp_id, "class": "single-message-local self", "data-author_id": "self" });
        let $message_text = $("<div>", {"class": "ch-msg-text"});
        let $message_links = $("<div>", {"class": "ch-link-cont"});
        // Populate elements
        $message_text.text(message);
        $message_links.html(msg_link_template(get_attachment_objs()));
        // Append elements
        $message_cont.append($message_text);
        $message_cont.append($message_links);
        // Append Message Block
        $("#ch-message-board").append($message_cont);
        // Insert Message - Ajax Call
        insert_messages(message, temp_id);
      }
    }
  });
  // Textarea rows controller
  $('#ch-reply').on('keydown', function(e) {
    if ( e.keyCode == 13 && e.shiftKey ) {
      let rows = $(this).attr('rows');
      $(this).attr('rows', parseInt(rows) + 1 );
    }
    else if ( e.keyCode == 8 ) {
      let lines = $(this).val().split(/\r*\n/).length;
      let rows = parseInt(lines) - 1;
      rows = rows >= 1 ? rows : 1;
      $(this).attr('rows', rows);
    }
  });

  /**
   * Accessory functions to build the conversation list and current convs
   */
  function time_ago(date) {
      var t = date.split(/[- :]/);
      var date = new Date(Date.UTC(t[0], t[1]-1, t[2], t[3], t[4], t[5]));

      var periods = {
        month: 30 * 24 * 60 * 60 * 1000,
        week: 7 * 24 * 60 * 60 * 1000,
        day: 24 * 60 * 60 * 1000,
        hour: 60 * 60 * 1000,
        minute: 60 * 1000
      };

      var diff = Date.now() - date;

      if (diff > periods.day) {
          return chatsterDataAdmin.translation.created + ' ' +chatsterDataAdmin.translation.hours_plus;
      } else if (diff > periods.hour) {
        if (Math.floor(diff > periods.hour) > 1 ) {
          return chatsterDataAdmin.translation.created + ' ' + Math.floor(diff > periods.hour) + ' ' + chatsterDataAdmin.translation.hours;
        } else {
          return chatsterDataAdmin.translation.created + ' ' + Math.floor(diff > periods.hour) + ' ' + chatsterDataAdmin.translation.hour;
        }
      } else if (diff > periods.minute) {
        if (Math.floor(diff / periods.minute) > 1 ) {
          return chatsterDataAdmin.translation.created + ' ' + Math.floor(diff / periods.minute) + ' ' + chatsterDataAdmin.translation.minutes;
        } else {
          return chatsterDataAdmin.translation.created + ' ' + Math.floor(diff / periods.minute) + ' ' + chatsterDataAdmin.translation.minute;
        }
      }
      return chatsterDataAdmin.translation.created + ' ' + chatsterDataAdmin.translation.now;

  }
  function esc_json(str) {
     return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
  }
  function build_convs(convs) {

    if ( convs ) {
       $('#ch-load-conv-container').hide();
       let last_conv_id = convs[Object.keys(convs)[convs.length - 1]].id;
       $("#conversations-block").attr('data-last_conv_id', last_conv_id);

       $.each( convs, function( key, conversation ) {
         let $conversation = $("<div>", {id: "conv-"+conversation.id, "class": "single-conversation", "data-customer_id": conversation.customer_id, "data-single_conv_id": conversation.id, "data-is_connected": true });
         let $subject = $("<div>", { "class": "ch-subject" }).text(conversation.form_data.chat_subject);
         let $email = $("<div>", { "class": "ch-email" }).text(conversation.form_data.customer_email);
         let $customer_name = $("<div>", { "class": "ch-name" }).text(conversation.form_data.customer_name);
         let $info = $("<div>", { "class": "ch-created-at", "data-created_at":conversation.created_at }).text(time_ago(conversation.created_at));
         let $unread = $("<div>", { "class": "unread"}).hide();
         if ( conversation.not_read > 0 ) {
           $unread.text( conversation.not_read ).show();
         }
         $conversation.append($subject).append($email).append($customer_name).append($info).append($unread);
         $conversation.hide();
         $("#conversations-block").append($conversation);
         $conversation.slideDown(200);
       });

    }



  }
  function build_current_conv(current_conv, conv_id) {

    let current_conv_id = $('#ch-message-board').attr('data-conv_id');
    /* If it's not the selected conversation ignore it */
    if ( current_conv && ( current_conv_id == conv_id ) ) {
      let prev_last_msg_id = $("#ch-message-board").attr('data-last_msg_id');
      let last_msg_id = current_conv[Object.keys(current_conv)[current_conv.length - 1]].id;
      if ( prev_last_msg_id < last_msg_id ) {

        $("#ch-message-board").attr('data-last_msg_id', esc_json(last_msg_id) );
        $.each( current_conv, function( key, message ) {

          if ( $("#message-" + message.temp_id).length ) {
            $( "#message-" + message.temp_id ).attr("id", "message-" + message.id );
          } else {
            let is_self = message.is_author == "1" ? "single-message self" : "single-message";
            let $message_cont = $("<div>", {id: "message-" + message.id, "class": is_self });
            let $message_text = $("<div>", {"class": "ch-msg-text"});
            let $message_links = $("<div>", {"class": "ch-link-cont"});
            $message_text.html(message.message);
            $message_links.html(msg_link_template(message.product_ids));
            $message_cont.append($message_text);
            $message_cont.append($message_links);
            $("#ch-message-board").append($message_cont);
          }
        });
      }

    }

  }
  function msg_link_template(links) {

    if ( links ) {
      var template = '';
      $.each( links, function( key, attachment ) {
        let thumbnail = attachment.thumbnail ? attachment.thumbnail : chatsterDataAdmin.no_image_link;
        template += '<div class="ch-link-chat" data-link_id="' + attachment.id + '">';
        template += ' <div class="ch-link-img">';
        template +=    '<img src="' + thumbnail + '" alt="product or page" height="32" width="32">';
        template += ' </div>';
        template += ' <div class="ch-link-descr">';
        template +=     '<div class="ch-link-title">' + attachment.title + '</div>';
        template +=     '<div class="ch-link-excerpt">' + attachment.excerpt + '</div>';
        template += ' </div>';
        template += ' <div class="ch-link-exlink"><a href="' + attachment.link + '"  target="_blank">Open</a></div>';
        template += '</div>';
      });
      return template;
    }
    return '';
  }
  $('.single-conversation').live('click',function() {
    $('.single-conversation').removeClass('selected');
    $(this).addClass('selected');
    $(this).find('.unread').hide(100);
    $('#ch-message-board').empty();
    let current_conv_id = $(this).attr('data-single_conv_id');
    let current_customer_id = $(this).attr('data-customer_id');
    $('#ch-message-board').attr('data-conv_id', current_conv_id);
    $('#ch-message-board').attr('data-curr_customer_id', current_customer_id);
    $("#ch-message-board").attr('data-last_msg_id', 0);
    get_messages();

  });
  // Updates Timestamps on conversations
  setInterval(function(){

    $('.ch-created-at').each(function() {
        let timestamp = $( this ).attr('data-created_at');
        $( this ).text(time_ago(timestamp));
      });

  }, 30000);
  /**
   * Live Update status fns
   */
  function update_disconnected( disconnected ) {
   if ( disconnected ) {
       $.each( disconnected, function( key, conversation ) {
         $('#conv-'+conversation.id).attr( 'data-is_connected', false );
         $('#conv-'+conversation.id).addClass('disconnected');
       });
   }
  }
  function update_unread_messages( new_messages_count ) {
   if ( new_messages_count ) {
     $.each( new_messages_count, function( key, conversation ) {
       if ($('#ch-message-board').attr('data-conv_id') != conversation.id ) {
         $('#conv-'+conversation.id).find('.unread').text( conversation.not_read ).show(300);
         console.log(conversation.not_read);
       }
     });
   }
  }
  function update_queue( queue_number ) {

   if ( queue_number && queue_number == 1 ) {
     $("#ch-queue-counter").find('.ch-plural').slideUp(200);
     let $queue = $("#ch-queue-counter").find('.ch-singular');
     $queue.find('span').text(queue_number);
     $queue.slideDown(200);
   }
   else if ( queue_number > 1 ){
     $("#ch-queue-counter").find('.ch-singular').slideUp(200);
     let $queue = $("#ch-queue-counter").find('.ch-plural');
     $queue.find('span').text(queue_number);
     $queue.slideDown(200);
   } else {
     $("#ch-queue-counter div").hide(100);
   }

  }

  /**
   * Retrieves messages for the current conversation
   */
  function get_messages() {

    let ch_current_conv = $('#ch-message-board').attr('data-conv_id');
    ch_current_conv = ch_current_conv ? ch_current_conv : 0;
    let ch_last_msg = $("#ch-message-board").attr('data-last_msg_id');
    ch_last_msg = ch_last_msg ? ch_last_msg : 0;

    var payload = { current_conv: ch_current_conv, last_message: ch_last_msg };

    $.ajax( {
        url: chatsterDataAdmin.api_base_url + '/chat/messages/admin',
        method: 'POST',
        beforeSend: function ( xhr ) {
            xhr.setRequestHeader( 'X-WP-Nonce', chatsterDataAdmin.nonce );
        },
        data: payload,
        success: function(data) {
          //  $('#ch-roller-container').addClass('hidden');

            if ( $('#chatster-chat-switch').prop("checked") ) {
              build_current_conv(data.payload.current_conv, ch_current_conv);
            }

        },
        error: function(error) {
          //  $('#ch-roller-container').addClass('hidden');
        },

      } ).done( function ( response ) {

      });
  }

  /**
   * Long poll with self relaunching technique
   */
  var LongPollRun = false;
  function long_poll() {
    if ( LongPollRun === false ) {

      LongPollRun = true;
      let ch_last_conv = $('#conversations-block').attr('data-last_conv_id');
      ch_last_conv = ch_last_conv ? ch_last_conv : 0;
      let ch_current_conv = $('#ch-message-board').attr('data-conv_id');
      ch_current_conv = ch_current_conv ? ch_current_conv : 0;
      let ch_last_msg = $("#ch-message-board").attr('data-last_msg_id');
      ch_last_msg = ch_last_msg ? ch_last_msg : 0;
      var ch_conv_list = $(".single-conversation").map(function(){
        if ( $(this).attr("data-is_connected") == 'true' ) { return $(this).attr("data-single_conv_id"); }
          return;
        }).get();
      ch_conv_list = ch_conv_list ? ch_conv_list : 0;
      var payload = { last_conv: ch_last_conv, current_conv: ch_current_conv, last_message: ch_last_msg, conv_ids: ch_conv_list };

      $.ajax( {
          url: chatsterDataAdmin.api_base_url + '/chat/polling/admin',
          method: 'POST',
          beforeSend: function ( xhr ) {
              xhr.setRequestHeader( 'X-WP-Nonce', chatsterDataAdmin.nonce );
          },
          data: payload,
          success: function(data) {
              $('#ch-roller-container').addClass('hidden');
              if ( $('#chatster-chat-switch').prop("checked") ) {

                build_convs(data.payload.convs);
                build_current_conv(data.payload.current_conv, ch_current_conv);
                update_disconnected(data.payload.disconnected);
                update_unread_messages(data.payload.new_messages);
                update_queue(data.payload.queue_number);

                setTimeout( long_poll, 500 );
              }
              LongPollRun = false;

          },
          error: function(error) {
              $('#ch-roller-container').addClass('hidden');
              LongPollRun = false;
              setTimeout( long_poll, 4000 );
          },

        } ).done( function ( response ) {

        });
      }


  }

  /**
   * Changes "Live chat" status and calls a long_poll
   */
  function change_admin_status( admin_status ) {
    $.ajax( {

        url: chatsterDataAdmin.api_base_url + '/chat/is_active/admin',
        method: 'POST',
        beforeSend: function ( xhr ) {
            xhr.setRequestHeader( 'X-WP-Nonce', chatsterDataAdmin.nonce );
        },
        data: { is_active: admin_status },
        success: function(data) {
          if ( admin_status) {
              long_poll();
          }
        },
        error: function(error) {
              $('#ch-roller-container').addClass('hidden');
              $('#chatster-chat-switch').prop("checked", ! admin_status);
        },

      } ).done( function ( response ) {

      });
  }
  $('#chatster-chat-switch').change(function() {
       if( this.checked ) {
          $('#ch-roller-container').removeClass('hidden');
          change_admin_status(true);
       } else {
          change_admin_status(false);
       }
   });
  if ( $('#chatster-chat-switch').prop("checked") ) {

      long_poll();
  }

})(jQuery);
