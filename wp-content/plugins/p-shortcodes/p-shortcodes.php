<?php
    /*
     * Plugin Name: Personalized Shortcodes
     * Plugin URI: https://github.com/iOrchK
     * Description: Plugin to create personalized shortcodes
     * Author: Jorge Chable
     * Version: 1.0
     * Author URI: https://github.com/iOrchK
     * Licence: GPL2
     */

    /*
     * Create a shortcode to use in post description
     */ 
    add_action('init', "jc_registerShortcode");

    /**
     * Register the shortcode rate in WordPress Core also the method jc_rate it will execute
     */
    if (!function_exists("jc_registerShortcode"))
    {
        function jc_registerShortcode()
        {
            add_shortcode("rate", "jc_rate");
        }
    }

    /**
     * Renderize the shortcode rate parameters ($args) and content ($content) in the post description that use it
     */
    if (!function_exists("jc_rate"))
    {
        function jc_rate($args, $content)
        {
            return "
                    <b> Show Header Body Plugin Activated </b><br>
                    Action Hook test from a plugin.<br>
                    Rate of change: " . $content . ", " . $args["from"] . " " . $args["to"] . "
                ";
        }
    }
?>