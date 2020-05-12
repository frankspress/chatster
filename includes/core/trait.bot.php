<?php

namespace Chatster\Core;

if ( ! defined( 'ABSPATH' ) ) exit;
require_once( CHATSTER_PATH . '/includes/core/trait.table-builder.php' );
use Chatster\Core\ChatsterTableBuilder;

trait BotCollection {
  use ChatsterTableBuilder;

  /**
   * Api Methods
   */

    protected function search_full_text( $question, $excluded_ids = array(), $search_expanded = false, $answer_limit = 1 ) {
      global $wpdb;
      $wp_table_source_q = self::get_table_name('source_q');
      $wp_table_source_a = self::get_table_name('source_a');
      $search_type = $search_expanded ? " WITH QUERY EXPANSION " : " IN NATURAL LANGUAGE MODE ";

      $params = array( $question );

      $sql = " SELECT a.id, a.answer FROM $wp_table_source_q as q
               INNER JOIN $wp_table_source_a as a ON a.id = q.answer_id
               WHERE MATCH (q.question)
               AGAINST ( %s $search_type ) ";

      if ( !empty($excluded_ids) ) {
        $sql_values = array();
        $prep_sql_list = '';
        foreach ($excluded_ids as $key => $value) {
            $prep_sql_list .= ' %d,';
            array_push($params, $value);
        }
        $prep_sql_list = rtrim( $prep_sql_list, "," );
        $sql .= " AND a.id NOT IN ( $prep_sql_list ) ";
      }
      array_push($params, $answer_limit);

      $sql .= " LIMIT %d ";

      $sql = $wpdb->prepare( $sql, $params );
      $result = $wpdb->get_results( $sql );

      return ! empty( $result ) ? $result : false;
    }


}
