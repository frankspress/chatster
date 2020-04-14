<?php

if ( ! defined( 'ABSPATH' ) ) exit;

function display_admin_header($tab) {
    if ( ! current_user_can( 'manage_options' ) ) return; ?>

    <div id="isa-logo-header" >
      <img src="" style="max-width:260px;">
    </div>
    <h2 class="nav-tab-wrapper">
      <a href="?page=chatster-menu&amp;chtab=chat" class="nav-tab <?php echo ( $tab == 'chat' || empty( $tab ) ) ? 'nav-tab-active' : ''; ?>">Conversations</a>
      <a href="?page=chatster-menu&amp;chtab=request" class="nav-tab <?php echo ( $tab == 'request' ) ? 'nav-tab-active' : ''; ?>">Received Requests</a>
      <a href="?page=chatster-menu&amp;chtab=settings" class="nav-tab <?php echo ( $tab == 'settings' ) ? 'nav-tab-active' : ''; ?>">Settings</a>
    </h2>

<?php
}