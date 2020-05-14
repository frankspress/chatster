<?php

namespace Chatster\Options;

if ( ! defined( 'ABSPATH' ) ) exit;

class OptionsGlobal {

  public static $option_group = null;

  public function text_field_callback( $args ) {

  	$options = get_option( static::$option_group , static::default_values() );

  	$id    = isset( $args['id'] )    ? $args['id']    : '';
  	$label = isset( $args['label'] ) ? $args['label'] : '';
    $placeholder = isset( $args['placeholder'] ) ? $args['placeholder'] : '';
  	$description = isset( $args['description'] ) ? $args['description'] : '';
  	$value = isset( $options[$id] ) ? sanitize_text_field( $options[$id] ) : ''; ?>

    <tr valign="top">
      <th scope="row"><?php echo $label; ?></th>
      <td>
          <input id="chatster_bot_options_<?php echo $id; ?>" name="chatster_bot_options[<?php echo $id; ?>]"
                 placeholder="<?php echo $placeholder; ?>" type="text" size="50" maxlength="<?php echo esc_attr(ism_get_text_length($id)) ?>" value="<?php echo $value; ?>"><br />
          <p class="description"><?php echo $description; ?></p>
      </td>
    </tr>
    <?php
  }

  public function switch_field_callback( $args ) {

  	$options = get_option( static::$option_group, static::default_values()  );
  	$id    = isset( $args['id'] )    ? $args['id']    : '';
  	$label = isset( $args['label'] ) ? $args['label'] : '';
    $placeholder = isset( $args['placeholder'] ) ? $args['placeholder'] : '';
  	$description = isset( $args['description'] ) ? $args['description'] : '';
  	$safe_value = ( isset( $options[$id] ) && rest_sanitize_boolean( $options[$id] ) ) ? 'checked': '';

  	?>
    <tr valign="top">
      <th scope="row"><?php echo esc_html($label); ?></th>
      <td>
  			<label class="switch" style="margin-top: 6px;">
  				<input id="chatster_bot_options_<?php echo $id; ?>" name="chatster_bot_options[<?php echo esc_attr($id); ?>]" type="checkbox" <?php echo $safe_value; ?> >
  				<span class="slider round"></span></label><br/>
        <p class="description"><?php echo $description; ?></p>
      </td>
    </tr>
    <?php
  }

  public function description() {
    echo  '';
  }


}
