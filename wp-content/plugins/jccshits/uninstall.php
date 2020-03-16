<?php
    // Only execute the contents of this file if the plugin is really being uninstalled
    if (!defined("WP_UNISNTALL_PLUGIN"))
    {
        exit();
    }

    global $wpdb;
    $tablename = $wpdb->prefix . "hits";

    // If table exists, delete it
    if ($wpdb->get_var("SHOW TABLES LIKE {$tablename}") == $tablename)
    {
        $sql = "DROP TABLE `$tablename`;";
        $wpdb->query($sql);
    }
?>