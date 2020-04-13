<?php

if ( ! defined( 'ABSPATH' ) ) exit;
require_once( CHATSTER_PATH . '/includes/core/trait.chat.php' );
use Chatster\Api\ChatCollection;

class CronManager  {
  use ChatCollection;

  public function __construct() {
    add_filter( 'cron_schedules', array($this, 'add_intervals'), 10);
    add_action( 'chatster_remove_old_convs', array($this, 'remove_old_convs'));
  }

  public function add_intervals($schedules) {
    if(!isset($schedules["every_three_mins"])){
      $schedules["every_three_mins"] = array(
          'interval' => 3*60,
          'display' => __('Once every 3 minutes'));
    }
    return $schedules;
  }

  public function remove_old_convs() {

  }

}

new CronManager;
