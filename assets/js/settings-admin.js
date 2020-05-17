(function ($) {

/**
 * Automatically opens the saved tab to show the options menu
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

  function build_qa_list( questions, answer ) {

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

})(jQuery);
