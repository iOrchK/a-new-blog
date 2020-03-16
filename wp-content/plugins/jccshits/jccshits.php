<?php
    /**
    * Plugin Name: JCCS Hits
    * Plugin URI: https://github.com/iOrchK
    * Description: Count the Hits on every Post Plugin
    * Author: Jorge Chable
    * Version: 1.0
    * Author URI: https://github.com/iOrchK
    * Licence: GPL2
    */

    /**
     * Plugin activation
     */
    register_activation_hook(__FILE__, "jc_hitsPluginActivate");
    
    /**
     * Plugin desactivation
     */
    register_deactivation_hook(__FILE__, "jc_hitsPluginDeactivate");

    /**
     * Hook when showing a post
     */
    add_filter("the_content", "jc_saveHit");

    /**
     * Activate plugin
     */
    if (!function_exists("jc_hitsPluginActivate"))
    {
        function jc_hitsPluginActivate()
        {
            global $wpdb;
            $tablename = $wpdb->prefix . "hits";

            // if table doesn´t exist, create it
            if ($wpdb->get_var("SHOW TABLES LIKE {$tablename}") != $tablename)
            {
                $sql = "CREATE TABLE `$tablename` (
                    `hit_id` INT(11) NOT NULL AUTO_INCREMENT,
                    `hit_ip` VARCHAR(100) NOT NULL,
                    `hit_post_id` INT(11) NOT NULL,
                    `hit_date` DATETIME,
                    PRIMARY KEY (hit_id)
                );";

                require_once(ABSPATH . "wp-admin/includes/upgrade.php");
                dbDelta($sql);
            }
        }
    }

    /**
     * Deactivate plugin
     */
    if (!function_exists("jc_hitsPluginDeactivate"))
    {
        function jc_hitsPluginDeactivate()
        {
            error_log("Plugin deactivated");
        }
    }

    if (!function_exists("jc_saveHit"))
    {
        function jc_saveHit($content)
        {
            // Executing if showing a single post only
            if (!is_single())
            {
                return $content;
            }

            // info
            $post_id = get_the_ID();
            $ip = $_SERVER["REMOTE_ADDR"];

            global $wpdb;
            $tablename = $wpdb->prefix . "hits";

            $newdata = array(
                "hit_ip" => $ip,
                "hit_date" => current_time("mysql"),
                "hit_post_id" => $post_id
            );

            $wpdb->insert($tablename, $newdata);

            return $content;
        }
    }
?>