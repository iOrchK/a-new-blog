<?php
    /**
    * Plugin Name: JCCS YouTube
    * Plugin URI: https://github.com/iOrchK
    * Description: YouTube Playaer in Post Plugin
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
     * Register widget
     */
    add_action("widgets_init", "jc_widgetInit");

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
            $post = get_post();

            if ( empty( $post ) )
            {
                return;
            }
            
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

    /**
     * Initiate widget
     */
    if (!function_exists("jc_widgetInit"))
    {
        function jc_widgetInit()
        {
            register_widget("jc_widget");
        }
    }

    /**
     * Widget class
     */
    class jc_widget extends WP_Widget
    {
        function jc_widget()
        {
            $widget_options = array(
                "classname" => "jc_class", // for CSS
                "description" => "Show a youtube video from post metadata."
            );

            $this->WP_Widget("jc_id", "YouTube Video", $widget_options);
        }

        /**
         * Show widget form un Appereance - Widgets
         */
        function form($instance)
        {
            $defaults = array("title" => "YouTube Video");
            $instance = wp_parse_args( (array) $instance, $defaults);
            $title = esc_attr($instance["title"]);

            echo "<p>Title <input class=\"widefat\" name=\"{$this->get_field_name("title")}\" type=\"text\" value=\"{$title}\"></p>";
        }

        /**
         * Save widget form
         */
        function update($new_instance, $old_instance)
        {
            $instance = $old_instance;
            $instance["title"] = strip_tags($new_instance["title"]);

            return $instance;
        }

        /**
         * Show widget in website
         */
        function widget($instance, $args)
        {
            extract($args);
            $title = apply_filters("widget_title", $instance["title"]);

            // Show if single post
            if (is_single())
            {
                echo $before_widget;
                echo $before_title . $title . $after_title;

                // Get post metadata
                $jc_youtube = esc_url( get_post_meta( get_the_ID(), "jc_youtube", true));

                // Embed video
                echo "<iframe width=\"200\" height=\"200\" src=\"{$this->get_yt_embedurl($jc_youtube)}\" frameborder=\"0\" allowfullscreen=\"true\"></iframe>";

                echo $after_widget;
            }
        }

        /**
         * Convert a common YouTube url in an embed YouTube url
         */
        function get_yt_embedurl($url)
        {
            if (strpos("/embed/", $url))
            {
                return $url;
            }
            
            parse_str(parse_url($url, PHP_URL_QUERY), $yt_params);

            return "http://www.youtube.com/embed/" . $yt_params['v'];
        }
    }
?>