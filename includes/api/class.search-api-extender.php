<?php
namespace Chatster\Api;

if ( ! defined( 'ABSPATH' ) ) exit;

class SearchApiExtender {

  public function __construct() {
    add_filter( 'rest_api_init', array( $this,'ch_add_search_api_fields') );
  }

  public function ch_add_search_api_fields () {

    // Embed thumbnail in Search
    register_rest_field(    'product',
                            'ch_thumbnail',
                             array(
                                   'get_callback' => array( $this,'ch_thumbnail')
                                  )
    );
    register_rest_field(    'post',
                            'ch_thumbnail',
                             array(
                                   'get_callback' => array( $this,'ch_thumbnail')
                                  )
    );
    register_rest_field(    'page',
                            'ch_thumbnail',
                             array(
                                   'get_callback' => array( $this,'ch_thumbnail')
                                  )
    );

    // Adds in stock attribute for products
    register_rest_field(    'product',
                            'ch_thumbnail',
                             array(
                                   'get_callback' => array( $this,'ch_thumbnail')
                                  )
    );
    // Adds variation flag for products
    register_rest_field(    'product',
                            'ch_thumbnail',
                             array(
                                   'get_callback' => array( $this,'ch_thumbnail')
                                  )
    );

  }

  public function ch_thumbnail($object) {
      return  esc_url( get_the_post_thumbnail_url($object['id'], 'thumbnail') );
  }

}



new SearchApiExtender();
