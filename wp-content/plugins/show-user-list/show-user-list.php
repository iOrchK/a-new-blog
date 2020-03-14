<?php
    /**
    * Plugin Name: Show User List
    * Plugin URI: https://github.com/iOrchK
    * Description: This is a plugin display a user list by requesting an API using the filter hook
    * Author: Jorge Chable
    * Version: 1.0
    * Author URI: https://github.com/iOrchK
    * Licence: GPL2
    */
    add_filter("the_content", "jc_getUserList");

    /**
     * Renderize a table list of users with detail on click
     */
    if (!function_exists("jc_getUserList"))
    {
        function jc_getUserList($content)
        {
            $jcScriptPath = __DIR__;
            $explode = explode("\wp-content", $jcScriptPath);
            $jcScriptPath = "wp-content" . $explode[1] . "\get-user-by-id.js";

            if (is_singular("post"))
            {
                return $content;
            }

            $json = wp_remote_get("https://jsonplaceholder.typicode.com/users");

            if (is_wp_error($json))
            {
                return "Request error: " . $json->get_error_code() . " " . $json->get_error_message();
            }

            $p_userList = json_decode($json["body"]);
            $content .= "
                <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/uikit@3.3.3/dist/css/uikit.min.css'/>

                <h2><b>Show User List Plugin Activated</b></h2>
                <p>Filter Hook test from a plugin</p>
                <table class='uk-table'>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>User Name</th>
                        </tr>
                    </thead>
                    <tbody>
            ";

            foreach ($p_userList as $user)
            {
                $content .= "
                    <tr title='Click to view detail' style='cursor: pointer;'>
                        <th class='jc-user-item' id='jc-user-{$user->id}'>{$user->id}</th>
                        <td class='jc-user-item' id='jc-user-{$user->id}'>{$user->name}</td>
                        <td class='jc-user-item' id='jc-user-{$user->id}'>{$user->username}</td>
                    </tr>
                ";
            }

            $content .= "
                    </tbody>
                </table>
                
                <script src='https://code.jquery.com/jquery-3.4.1.min.js'></script>
                <script src='https://cdn.jsdelivr.net/npm/uikit@3.3.3/dist/js/uikit.min.js'></script>
                <script src='https://cdn.jsdelivr.net/npm/uikit@3.3.3/dist/js/uikit-icons.min.js'></script>
                <script src='${jcScriptPath}'></script>
            ";

            return $content;
        }
    }
    
?>