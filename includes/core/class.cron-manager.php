<?php

if ( ! defined( 'ABSPATH' ) ) exit;
require_once( CHATSTER_PATH . '/includes/core/trait.chat.php' );
use Chatster\Core\ChatCollection;

class CronManager  {
  use ChatCollection;

  public function __construct() {
    add_filter( 'cron_schedules', array($this, 'add_intervals'), 10);
    add_action( 'chatster_remove_old_convs', array($this, 'cron_remove_old_convs'));
    add_action( 'chatster_update_presence', array($this, 'cron_update_presence'));
    add_action( 'chatster_check_new_requests', array($this, 'cron_check_new_requests'));
  }

  public function add_intervals($schedules) {
    if(!isset($schedules["every_three_mins"])){
      $schedules["every_three_mins"] = array(
          'interval' => 3*60,
          'display' => __('Once every 3 minutes'));
    }
    return $schedules;
  }

  public function cron_remove_old_convs() {
    Global $ChatsterOptions;
    $interval = $ChatsterOptions->get_chat_option('ch_chat_remove_offline_conv_int');
    $this->remove_old_convs($interval);
  }

  public function cron_update_presence() {
    Global $ChatsterOptions;
    $interval = $ChatsterOptions->get_chat_option('ch_chat_auto_offline');
    $this->set_admin_offline($interval);
  }

  public function cron_check_new_requests() {

  }

}

new CronManager();
