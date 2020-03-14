<?php
    /**
    * Plugin Name: New Plugin
    * Plugin URI: https://github.com/iOrchK
    * Description: New Plugin
    * Author: Jorge Chable
    * Version: 1.0
    * Author URI: https://github.com/iOrchK
    * Licence: GPL2
    */
    add_action('init', "jc_registerShortcode");

    if (!function_exists("jc_registerShortcode"))
    {
        function jc_registerShortcode()
        {
            add_shortcode("rate", "jc_rate");
        }
    }

    if (!function_exists("jc_rate"))
    {
        function jc_rate($args, $content)
        {
            return "
                    <b> Show Header Body Plugin Activated </b><br>
                    Action Hook test from a plugin.<br>
                    Rate of change: " . esc_attr($content) . ", " . $args["from"] . " " . $args["to"] . "
                ";
        }
    }
?>