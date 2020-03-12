<?php

    /**
     * Plugin Name: JC Plugin
     * Plugin URI: http://iorchk.com/plugins/
     * Description: This is a plugin test for get user list an items from https://jsonplaceholder.typicode.com/
     * Author: Jorge Chable
     * Version: 1.0
     * Author URI: http://iorchk.com/
     * Licence: GPL2
     */

    // add_filter("the_content", "jc_getUserById");
    add_filter("the_content", "jc_getUserList");

    /**
     * Change the content in all pages and
     * display an user list by http get from the API Url of https://jsonplaceholder.typicode.com/users
     */
    if (!function_exists("jc_getUserList"))
    {
        function jc_getUserList($content)
        {
            if (is_singular("post"))
            {
                return $content;
            }

            $json = wp_remote_get("https://jsonplaceholder.typicode.com/users");
            $p_userList = json_decode($json["body"]);
            $content = "
                <h3>JC Plugin Activated</h3>
                <table>
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
                    <tr title='Click to view detail'>
                        <th><a href='<?php jc_getUserById({$user->id}); ?>'>{$user->id}</a></th>
                        <td><a href='<?php jc_getUserById({$user->id}); ?>'>{$user->name}</a></td>
                        <td><a href='<?php jc_getUserById({$user->id}); ?>'>{$user->username}</a></td>
                    </tr>
                ";
            }

            $content .= "
                    </tbody>
                </table>
            ";

            return $content;
        }
    }

    /**
     * Change the content in all pagesa and
     * display the user detail by the user at the list that was made clic by http get from 
     * the API Url of https://jsonplaceholder.typicode.com/users
     */
    if (!function_exists("jc_getUserById"))
    {
        function jc_getUserById($id) {
            $json = wp_remote_get("https://jsonplaceholder.typicode.com/users/" . $id);
            $p_user = json_decode($json["body"]);
            $content = "
                <p>Detail:</p>
                <dl>
                    <dt>Id:</dt>
                    <dd>{$p_user->id}</dd>

                    <dt>Name:</dt>
                    <dd>{$p_user->name}</dd>

                    <dt>User Name:</dt>
                    <dd>{$p_user->username}</dd>
                </dl>
            ";

            return $content;
        }
    }
    
?>