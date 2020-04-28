<?php

if ( ! defined( 'ABSPATH' ) ) exit;

function display_admin_request( $requests, $total_pages, $current_page, $per_page, $count ) {
    if ( ! current_user_can( 'manage_options' ) ) return; ?>



<?php
dump($requests);

  /**
  * Pagination Block
  */
  if ( $count > $per_page ) {
    echo '<div class="ch-pagination">';
    echo paginate_links( array(
            'base' => add_query_arg( 'cpage', '%#%' ),
            'format' => '?page=%#%',
            'prev_text' => __('&laquo;'),
            'next_text' => __('&raquo;'),
            'total' => $total_pages,
            'current' => $current_page,
            'type' => 'list'
    ));
    echo '</div>';
  }
}
