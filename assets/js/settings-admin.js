(function ($) {
"use strict";
/**
 * Automatically opens the Current Saved tab to show the options menu as it reloads
 */
  function go_to_option() {
   let open_option = window.location.hash.substr(1);
   $("#"+open_option).delay(200).slideDown(300);
   history.pushState("", document.title, window.location.pathname + window.location.search);
  }
  go_to_option();

/**
 * Animates Option windows slide up and down
 */
  $(".ch-option-block").on('click', function(e) {
    if ( e.target.className == 'ch-option-title' ) {
      $(this).find('.ch-option-container').slideToggle(300, "linear",function() {

      });
    }
  });

/**
 * Asks for confirmation before resetting options.
 */
  $('.submit-reset').on('click', function(e) {
    e.preventDefault();
    if ( confirm("Reset All settings?") ) {
      let $option_form = $(this).parent().parent().parent();
      let option_page = $option_form.find('input[name ="option_page"]').val();
      $('<input>').attr({ type: 'hidden',
                          id: option_page +'_default_settings',
                          name: option_page +'[default_settings]',
                          value: 'reset' })
                  .appendTo($option_form);

      $option_form.submit();
    }
  });

/**
 * Q and A Requests
 */
  function build_qa_list( payload ) {
    if ( payload ) {
       // $('#ch-load-conv-container').hide();
       // let last_conv_id = convs[Object.keys(convs)[convs.length - 1]].id;
       // $("#conversations-block").attr('data-last_conv_id', last_conv_id);

       $.each( payload, function( key, qa_data ) {
           console.log(qa_data.answer_data);
         $.each( qa_data.questions, function( key, question ) {
           console.log(question);
         });
         // let $conversation = $("<div>", {id: "conv-"+conversation.id, "class": "single-conversation", "data-customer_id": conversation.customer_id, "data-single_conv_id": conversation.id, "data-is_connected": true });
         // let $subject = $("<div>", { "class": "ch-subject" }).text(conversation.form_data.chat_subject);
         // let $email = $("<div>", { "class": "ch-email" }).text(conversation.form_data.customer_email);
         // let $customer_name = $("<div>", { "class": "ch-name" }).text(conversation.form_data.customer_name);
         // let $info = $("<div>", { "class": "ch-created-at", "data-created_at":conversation.created_at }).text(time_ago(conversation.created_at));
         // let $unread = $("<div>", { "class": "unread"}).hide();
         // if ( conversation.not_read > 0 ) {
         //   $unread.text( conversation.not_read ).show();
         // }
         // $conversation.append($subject).append($email).append($customer_name).append($info).append($unread);
         // $conversation.hide();
         // $("#conversations-block").append($conversation);
         // $conversation.slideDown(200);
       });

    }
  }
  function save_q_and_a( questions, answer ) {

    var payload = { questions: questions, answer: answer };

    $.ajax( {
       url: chatsterDataAdmin.api_base_url + '/bot/admin/update',
       method: 'POST',
       beforeSend: function ( xhr ) {
           xhr.setRequestHeader( 'X-WP-Nonce', chatsterDataAdmin.nonce );
       },
       data: payload,
       success: function(data) {
         console.log(data);
         // if ( data.payload ) {
         //   build_qa_list(data.payload);
         //   $('#chatster_bot_qa_options_ch_bot_answer').val('');
         //   $('#chatster_bot_qa_options_ch_bot_question').val('');
         // }
       },
       error: function(error) {
         $('#save-bot-q-and-a').attr('disabled', false);
         $('#chatster-bot-and-a-form').find('.ch-smaller-loader').hide(100);
       },

     } ).done( function ( response ) {
         $('#save-bot-q-and-a').attr('disabled', false);
         $('#chatster-bot-and-a-form').find('.ch-smaller-loader').hide(100);
     });
  }
  $('#chatster-bot-and-a-form').on('submit', function(e) {
    e.preventDefault();
    let questions_text = $('#chatster_bot_qa_options_ch_bot_question').val();
    let questions = questions_text.match(/\S[^?]*(?:\?+|$)/g);
    let answer = $('#chatster_bot_qa_options_ch_bot_answer').val();
    $('#save-bot-q-and-a').attr('disabled', true);
    $(this).find('.ch-smaller-loader').show(100);
    save_q_and_a( questions, answer );
  });

  function load_q_and_a_list(page) {

    $.ajax( {
       url: chatsterDataAdmin.api_base_url + '/bot/admin/' + page + '/get-page',
       method: 'POST',
       beforeSend: function ( xhr ) {
           xhr.setRequestHeader( 'X-WP-Nonce', chatsterDataAdmin.nonce );
       },
       data: {},
       success: function(data) {
         if( data.payload ) {
           build_qa_list(data.payload);
         }


       },
       error: function(error) {
         $('#ch-qa-pagination').removeClass('disabled-link');
       },

     } ).done( function ( response ) {
         $('#ch-qa-pagination').removeClass('disabled-link');
         $('#q-and-a-list').find('.ch-small-loader').hide(200);
     });
  }
  var current_page_qa = 1;
  $('#ch-qa-pagination a,#ch-qa-pagination span').on('click', function(e) {
      e.preventDefault(); e.stopPropagation();
      $('#ch-qa-pagination').addClass('disabled-link');
      $('#q-and-a-list .q-and-a-block').slideUp(300, function() {
        $(this).remove();
      });
      $('#q-and-a-list').find('.ch-small-loader').show(200);
      var page = 1;
      if ( $(this).hasClass('next') ) {

         let page_max = parseInt( $('#ch-qa-pagination').attr('data-page_max') );
         if ( current_page_qa < page_max ) {
           page = current_page_qa + 1;
         } else {
           page = current_page_qa;
         }

      } else {
         page = parseInt( $(this).text() );
      }
      load_q_and_a_list(page);
  });
  load_q_and_a_list(1);

})(jQuery);
