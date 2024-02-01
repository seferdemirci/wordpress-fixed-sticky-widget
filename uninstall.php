<?php

if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

delete_option('fs_widget_options');
delete_site_option('fs_widget_options');
