<?php
    /**
    * Plugin Name: JCCS Wishlist
    * Plugin URI: https://github.com/iOrchK
    * Description: Wishlist Plugin
    * Author: Jorge Chable
    * Version: 1.0
    * Author URI: https://github.com/iOrchK
    * Licence: GPL2
    */

    /**
     * Add options of website
     */
    add_action("admin_menu", "jc_pluginMenuWishlist");

    /**
     * Initialize wishlist form
     */
    add_action("admin_init", "jc_adminWishlistInit");

    /**
     * Register widget
     */
    add_action("widgets_init", "jc_widgetWishlistInit");

    /**
     * Register external files
     */
    add_action("wp", "jc_registerExternalFilesWishlistInit");
    add_action("wp_ajax_jc_addWishlist", "jc_addWishlistProcess"); // wp_ajax_... is a prefix that need to be added to the first parameter. If is not logged in use the prefix wp_ajax_nopriv_

    /**
     * Add wishlist options
     */
    if (!function_exists("jc_pluginMenuWishlist"))
    {
        function jc_pluginMenuWishlist()
        {
            add_options_page("JC Wishlist Options", "JC Wishlist", "manage_options", "jc", "jc_pluginWishlistOptions");
        }
    }

    /**
     * Add setting options to wishlist
     */
    if (!function_exists("jc_adminWishlistInit"))
    {
        function jc_adminWishlistInit()
        {
            register_setting("jc-group", "jc_dashboardTitle");
            register_setting("jc-group", "jc_numberOfItems");
        }
    }

    /**
     * Add wishlist form to the view
     */
    if (!function_exists("jc_pluginWishlistOptions"))
    {
        function jc_pluginWishlistOptions()
        {
            ?>
                <div class="wrap">
                    <?php screen_icon(); ?>
                    <h2> JC Wishlist </h2>
                    <form action="options.php" method="post">
                        <?php settings_fields("jc-group"); ?>
                        <?php @do_settings_sections("jc-group"); ?>
                        <table class="form-table">
                            <tr valign="top">
                                <th scope="row"> <label for="jc_dashboardTitle"> Dashboard widget title </label> </th>
                                <td>
                                    <input type="text" name="jc_dashboardTitle" id="dashboard_title" value="<?php echo get_option("jc_dashboardTitle"); ?>">
                                    <br> <small>Help text for this field</small>
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row"> <label for="jc_numberOfItems"> Number of items to show </label> </th>
                                <td>
                                    <input type="text" name="jc_numberOfItems" id="dashboard_title" value="<?php echo get_option("jc_numberOfItems"); ?>">
                                    <br> <small>Help text for this field</small>
                                </td>
                            </tr>
                        </table>
                        <?php @submit_button(); ?>
                    </form>
                </div>
            <?php
        }
    }

    /**
     * Initialize widget
     */
    if (!function_exists("jc_widgetWishlistInit"))
    {
        function jc_widgetWishlistInit()
        {
            register_widget("jc_widgetWishlist");
        }
    }

    /**
     * Widget class
     */
    class jc_widgetWishlist extends WP_Widget
    {
        function jc_widgetWishlist()
        {
            $widget_options = array(
                "classname" => "jc_class", // for CSS
                "description" => "Add items to wishlist."
            );

            // ID for DOM element
            $this->WP_Widget("jc_wwl_id", "Wishlist", $widget_options);
        }

        /**
         * Show widget form in Appearence - Widgets
         */
        function form($instance)
        {
            $defaults = array("title" => "Wishlist");
            $instance = wp_parse_args( (array) $instance, $defaults);
            $title = esc_attr($instance["title"]);

            echo "<p>Title <input class=\"widefat\" name=\"{$this->get_field_name("title")}\" type=\"text\" value=\"{$title}\"></p>";
        }

        /**
         * Save widget form
         */
        function update($new_instance, $old_instance)
        {
            // Process widget options to save
            $instance = $old_instance;
            $instance["title"] = strip_tags($new_instance["title"]);

            return $instance;
        }

        /**
         * Show widget
         */
        function widget($instance, $args)
        {
            extract($args);
            $title = apply_filters("widget_title", $instance["title"]);

            // Show only if single post
            if (is_single())
            {
                echo $before_widget;
                echo $before_title . $title . $after_title;

                echo "<span id=\"jc_add_wishlist_div\"><a id=\"jc_add_wishlist\" href=\"#\">Add to wishlist</a></span>";
                
                echo $after_widget;
            }
        }
    }


    /**
     * Load external files
     */
    if (!function_exists("jc_registerExternalFilesWishlistInit"))
    {
        function jc_registerExternalFilesWishlistInit()
        {
            $post = get_post();

            if ( empty( $post ) )
            {
                return;
            }

            wp_register_script("jccswishlist-js", plugins_url("/jccswishlist.js", __FILE__), array("jquery"));

            // load script
            wp_enqueue_script("jquery");
            wp_enqueue_script("jccswishlist-js");
            
            // Data that will be sent to script
            $script_data = array(
                "admin_ajax" => admin_url("admin-ajax.php"),
                "post_data" => array(
                    "postId" => $post->ID,
                    "action" => "jc_addWishlist"
                )
            );

            // Sent the data to script
            wp_localize_script(
                'jccswishlist-js',
                'jccswishlist_data',
                $script_data
            );
        }
    }

    /**
     * 
     */
    if (!function_exists("jc_addWishlistProcess"))
    {
        function jc_addWishlistProcess()
        {
            echo "The article #{$_POST["postId"]} was added to wishlist";
            wp_die();
        }
    }
?>