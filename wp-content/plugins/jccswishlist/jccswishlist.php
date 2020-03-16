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
     * Add wishlist settings widget
     */
    add_action("admin_menu", "jc_wishlistSettingsWidget");

    /**
     * Initialize wishlist form
     */
    add_action("admin_init", "jc_wishlistFormInit");

    /**
     * Register widget
     */
    add_action("widgets_init", "jc_wishlistWidgetInit");

    /**
     * Register external files to widget
     */
    add_action("wp", "jc_wishlistExternalFilesInit");

    /**
     * Register AJAX callback for add to wishlist function
     * wp_ajax_... is a prefix that need to be added to the first parameter. If is not logged in use the prefix wp_ajax_nopriv_...
     */
    add_action("wp_ajax_jc_addToWishlist", "jc_addToWishlistProcess");

    /**
     * Add wishlist dashboard widget
     */
    add_action("wp_dashboard_setup", "jc_wishlistDashboardWidget");

    /**
     * Add jc wishlist option to the settings menu
     */
    if (!function_exists("jc_wishlistSettingsWidget"))
    {
        function jc_wishlistSettingsWidget()
        {
            add_options_page("JC Wishlist Options", "JC Wishlist", "manage_options", "jc", "jc_pluginWishlistOptions");
        }
    }

    /**
     * Register jc wishlist groups
     */
    if (!function_exists("jc_wishlistFormInit"))
    {
        function jc_wishlistFormInit()
        {
            register_setting("jc-group", "jc_dashboardTitle");
            register_setting("jc-group", "jc_numberOfItems");
        }
    }

    /**
     * Show jc wishlist form
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
                                <th scope="row"> <label for="jc_dashboardTitle"> Título del widget </label> </th>
                                <td>
                                    <input type="text" name="jc_dashboardTitle" id="dashboard_title" value="<?php echo get_option("jc_dashboardTitle"); ?>">
                                    <br> <small>Help text for this field</small>
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row"> <label for="jc_numberOfItems"> Número de elementos por mostrar </label> </th>
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
     * Initialize wishlist widget
     */
    if (!function_exists("jc_wishlistWidgetInit"))
    {
        function jc_wishlistWidgetInit()
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

                if (!is_user_logged_in())
                {
                    echo "Please sign in to use this widget";
                } else
                {
                    $post = get_post();
                    
                    if (jc_postHasWishlisted($post->ID))
                    {
                        echo "<span id=\"jc_add_wishlist_div\"> Guardado en la lista de deseos </span>";
                    } else
                    {
                        echo "<span id=\"jc_add_wishlist_div\"> <a id=\"jc_add_wishlist\" href=\"#\"> Agregar a la lista de deseos </a> </span>";
                    }
                }
                
                echo $after_widget;
            }
        }
    }


    /**
     * Load external files
     */
    if (!function_exists("jc_wishlistExternalFilesInit"))
    {
        function jc_wishlistExternalFilesInit()
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
                "postId" => $post->ID,
                "action" => "jc_addToWishlist"
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
     * AJAX callback to process the data
     */
    if (!function_exists("jc_addToWishlistProcess"))
    {
        function jc_addToWishlistProcess()
        {
            $post_id = (int) $_POST["postId"];
            $user = wp_get_current_user();

            if (!jc_postHasWishlisted($post_id))
            {
                add_user_meta($user->ID, "jc_wanted_posts", $post_id);
                echo "Hecho";
            }

            exit();
        }
    }

    /**
     * Validate if post is into wishlist
     */
    if (!function_exists("jc_postHasWishlisted"))
    {
        function jc_postHasWishlisted($post_id)
        {
            $user = wp_get_current_user();
            $user_meta_list = get_user_meta($user->ID, "jc_wanted_posts");

            foreach ($user_meta_list as $row)
            {
                if ($row == $post_id)
                {
                    return true;
                }
            }

            return false;
        }
    }

    /**
     * Create wishlist dashboard widget
     */
    if (!function_exists("jc_wishlistDashboardWidget"))
    {
        function jc_wishlistDashboardWidget()
        {
            $title = get_option("jc_dashboardTitle") ? get_option("jc_dashboardTitle") : "Título";
            wp_add_dashboard_widget("css_wishlist_dashboard_widget_id", $title, "jc_showWishlistDashboardWidget");
        }
    }

    /**
     * Show wishlist dashboard widget
     */
    if (!function_exists("jc_showWishlistDashboardWidget"))
    {
        function jc_showWishlistDashboardWidget()
        {
            $user = wp_get_current_user();
            $values = get_user_meta($user->ID, "jc_wanted_posts");

            $limit = (int) get_option("jc_numberOfItems") ? (int) get_option("jc_numberOfItems") : 4;

            echo "<ul>";

            foreach($values as $i => $value)
            {
                if ($i == $limit)
                {
                    break;
                }

                $currentPost = get_post($value);

                echo "<li>{$currentPost->post_title}</li>";
            }

            echo "</ul>";
        }
    }
?>