<?php
    /**
    * Plugin Name: Personalized Metadata
    * Plugin URI: https://github.com/iOrchK
    * Description: Plugin to create personalized metadata
    * Author: Jorge Chable
    * Version: 1.0
    * Author URI: https://github.com/iOrchK
    * Licence: GPL2
    */

    /**
     * Show metabox in post editor
     */
    add_action("add_meta_boxes", "jc_addMetabox");

    /**
     * Save metadata
     */
    add_action("save_post", "jc_saveMetabox");

    /**
     * Add plugin metaboxes
     */
    if (!function_exists("jc_addMetabox"))
    {
        function jc_addMetabox()
        {
            add_meta_box("jc_youtube", "YouTube Video Link", "jc_youtubeHandler", "post");
        }
    }

    /**
     * Metabox handler
     */
    if (!function_exists("jc_youtubeHandler"))
    {
        function jc_youtubeHandler()
        {
            $values = get_post_custom($post->ID);
            $link = esc_attr($values["jc_youtube"][0]);
            echo "
                <label for=\"jc_youtube\"> YouTube Video Link </label>
                <input type=\"text\" id=\"jc_youtube\" name=\"jc_youtube\" value=\"" . $link . "\">    
            ";
        }
    }

    /**
     * Save metabox
     */
    if (!function_exists("jc_saveMetabox"))
    {
        function jc_saveMetabox($post_id)
        {
            // Don't save metadata if it's autosave
            if (defined("DOING_AUTOSAVE") && DOING_AUTOSAVE)
            {
                return;
            }

            // Check if user can edit the post
            if (!current_user_can("edit_post"))
            {
                return;
            }

            if (isset($_POST["jc_youtube"]))
            {
                update_post_meta($post_id, "jc_youtube", esc_url($_POST["jc_youtube"]));
            }
        }
    }
?>