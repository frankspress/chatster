<?php

if ( ! defined( 'ABSPATH' ) ) exit;
use Chatster\Api\ChatCollection;

class CronManager  {
  use ChatCollection;

  public function __construct() {
    add_filter( 'cron_schedules', array($this, 'add_intervals'));
    add_action( 'chatster_remove_old_convs', array($this, 'remove_old_convs'));
  }

  private function add_intervals($schedules) {
    if(!isset($schedules["5min"])){
      $schedules["5min"] = array(
          'interval' => 5*60,
          'display' => __('Once every 5 minutes'));
    }
    return $schedules;
  }

}

new CronManager;
