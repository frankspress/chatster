<?php

if ( ! defined( 'ABSPATH' ) ) exit;

function display_admin_request( $requests, $total_pages, $current_page, $per_page, $count ) {
    if ( ! current_user_can( 'manage_options' ) ) return; ?>

      <div class="ism-wrap"><?php

        /**
         * If No Results
         */
        if ( !$requests ) { ?>
          <table class="wp-list-table widefat fixed striped posts sent-alert ch-alert-table">
            <tbody id="the-list"><tr><td style="vertical-align: middle; text-align: center;">
              <div class="alert-view-no-results">No requests Received yet</div></td></tr>
            </tbody>
          </table>
          <?php
          return;
        } ?>

        <!-- Table Header -->
        <table class="wp-list-table widefat fixed striped posts">
        <tbody id="the-list">
          <thead>
            <tr>
              <th scope="col" class="ch-th-title" ><div>User Name</div></th>
              <th scope="col" class="ch-th-title" ><div>Email</div></th>
              <th scope="col" class="ch-th-title" ><div>Subject</div></th>
              <th scope="col" class="ch-th-title" ><div>Date Received</div></th>
              <th scope="col" class="ch-th-title" ><div>Replied</div></th>
              <th scope="col" class="ch-th-title" ><div>Flagged</div></th>
            </tr>
          </thead>

          <?php

          /**
          * Loops through the Requests received
          */
          foreach ($requests as $request ) {

              echo '<tr id="request-'.esc_attr( $request->id ).'" class="edit author-self level-0 post-74 type-product status-publish has-post-thumbnail hentry product_cat-posters">';

              echo '<td style="width:10%;"><div>' . esc_html( $request->name ) .'</div></td>';
              echo '<td style="width:10%;"><div>' . esc_html( $request->email ) .'</div></td>';
              echo '<td style="width:10%;"><div>' . esc_html( $request->subject ) .'</div></td>';
              echo '<td style="width:10%;"><div>' . esc_html( $request->created_at ) .'</div></td>';
              echo '<td style="width:10%;"><div>' . esc_html( $request->replied_at ) .'</div></td>';
              echo '<td style="width:10%;"><div>' . esc_html( $request->is_flagged ) .'</div></td>';

              echo '</tr>';
           }

          ?>
          </tbody>
          </table>
        </div>
<?php


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
