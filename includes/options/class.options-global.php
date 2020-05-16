<?php

namespace Chatster\Options;

if ( ! defined( 'ABSPATH' ) ) exit;

class OptionsGlobal {

  public static $option_group = null;
  public static $fields_maxlength = null;
  public static $success_set = null;

  public static function get_maxlength( String $id ) {
      return array_key_exists( $id, static::$fields_maxlength) ? static::$fields_maxlength[$id] : 1000;
  }


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
          <input id="<?php echo static::$option_group.'_'.esc_attr($id); ?>" name="<?php echo static::$option_group.'['.esc_attr($id).']'; ?>"
                 placeholder="<?php echo esc_attr($placeholder); ?>" type="text" size="50" maxlength="<?php echo esc_attr(static::get_maxlength($id)) ?>" value="<?php echo esc_attr($value); ?>"><br />
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
  	$safe_value = ( isset( $options[$id] ) && rest_sanitize_boolean( $options[$id] ) ) ? 'checked': ''; ?>

    <tr valign="top">
      <th scope="row"><?php echo esc_html($label); ?></th>
      <td>
  			<label class="switch" style="margin-top: 6px;">
  				<input id="<?php echo static::$option_group.'_'.esc_attr($id); ?>" name="<?php echo static::$option_group.'['.esc_attr($id).']'; ?>" type="checkbox" <?php echo $safe_value; ?> >
  				<span class="slider round"></span></label><br/>
        <p class="description"><?php echo $description; ?></p>
      </td>
    </tr>
    <?php
  }

  public function textarea_field_callback( $args ) {

  	$options = get_option( static::$option_group, static::default_values()  );

  	$id    = isset( $args['id'] )    ? $args['id']    : '';
  	$label = isset( $args['label'] ) ? $args['label'] : '';
  	$description = isset( $args['description'] ) ? $args['description'] : '';
  	$allowed_tags = wp_kses_allowed_html( 'post' );

  	$value = isset( $options[$id] ) ? wp_kses( stripslashes_deep( $options[$id] ), $allowed_tags ) : '';
    $value = trim( preg_replace( '/\h+/', ' ',  $value )  ); ?>

    <tr valign="top">
      <th scope="row"><?php echo $label; ?></th>
      <td>
          <textarea id="<?php echo static::$option_group.'_'.esc_attr($id); ?>" name="<?php echo static::$option_group.'['.esc_attr($id).']'; ?>"
            rows="5" cols="53" maxlength="<?php echo esc_attr(static::get_maxlength($id)) ?>"><?php echo $value; ?></textarea><br />
          <p class="description"><?php echo $description; ?></p>
      </td>
    </tr>
    <?php
  }

  public function color_picker_field_callback( $args ) {

    $default_values = static::default_values();
  	$options = get_option( static::$option_group, $default_values );

  	$id    = isset( $args['id'] )    ? $args['id']    : '';
  	$label = isset( $args['label'] ) ? $args['label'] : '';
  	$description = isset( $args['description'] ) ? $args['description'] : '';
  	$value = isset( $options[$id] ) ? sanitize_hex_color( $options[$id] ) : '';
  	$default_value = isset( $default_values[$id] ) ? sanitize_hex_color( $default_values[$id] ) : ''; ?>

    <tr valign="top">
      <th scope="row"><?php echo $label; ?></th>
      <td>
        <input id="<?php echo static::$option_group.'_'.esc_attr($id); ?>" name="<?php echo static::$option_group.'['.esc_attr($id).']'; ?>" type="text" value="<?php echo $value; ?>" class="my-color-field"
        data-default-color="<?php echo $default_value; ?>" />
        <p class="description"><?php echo $description; ?></p>
      </td>
    </tr>

    <?php
  }


  public function description() {
    echo  '';
    return;
  }

  public function add_success_message( String $err_msg = '') {
    if ( !empty( $err_msg ) ) {
      add_settings_error(
          static::$option_group, // Setting slug
          'error_message',
           $err_msg,
          'error'
      );
    }
    elseif( !static::$success_set ) {
       static::$success_set = true;
       add_settings_error(
          static::$option_group, // Setting slug
          'success_message',
          'Settings Saved!',
          'success'
      );
    }
  }

}
