<?php
    /**
    * Plugin Name: JCCS Cron Email
    * Plugin URI: https://github.com/iOrchK
    * Description: Cron to send an Email Plugin
    * Author: Jorge Chable
    * Version: 1.0
    * Author URI: https://github.com/iOrchK
    * Licence: GPL2
    */

    /**
     * Initialize the cron job
     */
    add_action("init", "jc_cronJobInit");

    /**
     * Add a hook to send an email
     */
    add_action("jc_sendmailHook", "jc_sendmail");

    /**
     * Register the cron job time, recurrence and hook
     */
    if (!function_exists("jc_cronJobInit"))
    {
        function jc_cronJobInit()
        {
            if (!wp_next_scheduled("jc_sendmailHook"))
            {
                wp_schedule_event(time(), "hourly", "jc_sendmailHook");
            }
        }
    }

    /**
     * Send the email to the administrator
     */
    if (!function_exists("jc_sendmail"))
    {
        function jc_sendmail()
        {
            $jc_admin_email = get_bloginfo("admin_email");

            wp_mail($jc_admin_email, "admin", "Cron Job Test");
        }
    }
?>