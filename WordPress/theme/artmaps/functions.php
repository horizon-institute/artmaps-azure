<?php

/* TODO: Cleanup these functions into class structure */
function isUsingOpenID() {
    global $wpdb;
    global $current_user;
    get_currentuserinfo();
    $res = $wpdb->get_var(
            $wpdb->prepare(
                    "SELECT COUNT(1) FROM "
                    . openid_identity_table()
                    . " WHERE user_id = %s",
            $current_user->id));
    return $res != 0;
}
/* End functions to clean up  */

/* Removes contact methods fields from 'Edit Profile' */
add_filter("user_contactmethods",
        function ($cm) {
            foreach($cm as $k => $v)
                unset($cm[$k]);
            return $cm;
        });

/* Removes change password fields from 'Edit Profile' for OpenID users */
add_filter("show_password_fields",
        function () { return !isUsingOpenID(); });

/* Prevents the OpenID plugin from showing the extended User profile tabs */
add_action("show_user_profile",
        function () {
            remove_action("show_user_profile", "openid_extend_profile", 5);
        }, 99);
add_action("admin_menu",
        function () {
            remove_action("admin_menu", "openid_admin_panels");
        }, 0);

/*
 * Changes the registration URL to the ArtMaps registration URL
 * TODO: There needs to be a way for child themes register.php to be picked up first
 * TODO: Need to intercept login.php?action=register
 */
add_filter("register",
        function ($link) {
            $url = substr(parse_url(get_stylesheet_directory_uri(), PHP_URL_PATH)
                    . "/register.php", 1);
            return preg_replace("/wp-login.php/", $url, $link, 1);
        });

/*
 * On login, redirects the user to the 'Edit Profile' screen if they
 * have not set up a blog for publishing to
 */
add_action("wp_login",
        function ($login, $user = null) {
            if($user == null)
                $user = get_user_by("login", $login);
            $cfg = get_user_meta($user->id, "artmaps_blog_config", true);
            if($cfg == null || $cfg == "")
                add_filter("login_redirect",
                        function ($redirectTo, $request = null) {
                            return "wp-admin/profile.php";
                        });
        });

function getUsersArtMapsBlog($user) {
    $cfg = get_user_meta($user->id, "artmaps_blog_config", true);
    if($cfg == null || $cfg == "") {
        $un = uniqid("artmaps");
        $cfg = array(
                    "IsInternal" => true,
                    "ExternalURL" => "",
                    "ExternalUsername" => "",
                    "ExternalPassword" => "",
                    "InternalURL" => "http://" . $un . ".am.wp.horizon.ac.uk",
                    "InternalUsername" => $un,
                    "InternalPassword" => uniqid()
                );
        update_user_meta($user->id, "artmaps_blog_config", $cfg);
    }
    return $cfg;
}

$ArtMapsProfileFields =
    function ($user = null) {
        if($user == null) {
            global $current_user;
            get_currentuserinfo();
            $user = $current_user;
        }
        $blog = getUsersArtMapsBlog($user);
        ?>
        <script type="text/javascript">
        function toggleArtMapsBlogOptions(cb) {
            var area = jQuery("#ArtMaps_Use_Personal_Blog_Options");
            if(jQuery(cb).prop("checked"))
                area.show("blind");
            else
                area.hide("blind");
        };
        </script>
        <h3>My Blog</h3>
        <table class="form-table">
            <tr>
                <th><label for="ArtMaps_Use_Personal_Blog">Use my Blog</label></th>
                <td>
                    <input type="checkbox"
                            name="ArtMaps_Use_Personal_Blog"
                            id="ArtMaps_Use_Personal_Blog"
                            onchange="toggleArtMapsBlogOptions(this);"
                            <?= $blog["IsInternal"] ? "" : "checked=\"checked\"" ?>
                            value="1" />
                    <br />
				    <span class="description">
				        ArtMaps can use your personal WordPress blog for publishing
				        if you would like to share your ArtMaps activities with
				        your subscribers.  If you do not want to utilise this
				        functionality leave this option unchecked and ArtMaps
				        will manage your activities internally.
				    </span>
				</td>
		    </tr>
            <tr id="ArtMaps_Use_Personal_Blog_Options" <?= $blog["IsInternal"] ? "style=\"display: none;\"" : "" ?>>
                <td colspan="2">
                    <table class="form-table">
        		        <tr>
                            <th><label for="ArtMaps_Use_Personal_Blog_URL">Blog URL</label></th>
                            <td>
                                <input type="text" id="ArtMaps_Use_Personal_Blog_URL" name="ArtMaps_Use_Personal_Blog_URL" value="<?= $blog["ExternalURL"] ?>" />
            				    <span class="description">Your blog URL</span>
            				</td>
        				</tr>
        				<tr>
            				<th><label for="ArtMaps_Use_Personal_Blog_Username">Blog Username</label></th>
                            <td>
                                <input type="text" id="ArtMaps_Use_Personal_Blog_Username" name="ArtMaps_Use_Personal_Blog_Username" value="<?= $blog["ExternalUsername"] ?>" />
            				    <span class="description">Your blog username</span>
            				</td>
        				</tr>
        				<tr>
            				<th><label for="ArtMaps_Use_Personal_Blog_Password">Blog Password</label></th>
                            <td>
                                <input type="password" id="ArtMaps_Use_Personal_Blog_Password" name="ArtMaps_Use_Personal_Blog_Password" value="" />
            				    <span class="description">Your blog password (not shown for security)</span>
            				</td>
        				</tr>
    				</table>
                </td>
            </tr>
        </table>
        <?php
    };
add_action("edit_user_profile", $ArtMapsProfileFields);
add_action("show_user_profile", $ArtMapsProfileFields);

$ArtMapsUpdateProfile =
    function($userID) {
        if(!current_user_can("edit_user", $userID) )
            return false;
        global $current_user;
        get_currentuserinfo();
        $cfg = getUsersArtMapsBlog($current_user);
        if($_POST["ArtMaps_Use_Personal_Blog"]) {
            $cfg["IsInternal"] = false;

            $burl = trim($_POST["ArtMaps_Use_Personal_Blog_URL"]);
            if(strpos($burl, "http") !== 0)
                $burl = "http://" . $burl;
            if(strrpos($burl, "/") == strlen($burl) - 1)
                $burl = substr($burl, 0, strlen($burl) - 1);

            $cfg["ExternalURL"] = $burl;
            $cfg["ExternalUsername"] = trim($_POST["ArtMaps_Use_Personal_Blog_Username"]);
            $pw = trim($_POST["ArtMaps_Use_Personal_Blog_Password"]);
            if($pw != "")
                $cfg["ExternalPassword"] = $pw;
        }
        else {
            $cfg["IsInternal"] = true;
            $cfg["ExternalURL"] = "";
            $cfg["ExternalUsername"] = "";
            $cfg["ExternalPassword"] = "";
        }
        update_user_meta($current_user->id, "artmaps_blog_config", $cfg);
    };
add_action("personal_options_update", $ArtMapsUpdateProfile);
add_action("edit_user_profile_update", $ArtMapsUpdateProfile);

add_filter("body_class",
        function ($classes) {
            if(is_page_template("template-general.php")) {
                $classes = array_filter($classes, function ($var){
                    return $var != "singular";
                });
            }
            if(is_page_template("template-artwork.php")) {
                $classes[] = "artmaps-artwork";
            }
            if(is_page("Home")) {
                $classes[] = "artmaps-help";
            }
            if(is_page("The Art Map")) {
                $classes[] = "artmaps-artmap";
            }
            return $classes;
        }, 99);

if(!function_exists("artmapsComment")) {
function artmapsComment($comment, $args, $depth) {
    switch($comment->comment_type) {
        case "pingback":
        case "trackback":
        ?>
            <li class="artmaps-comment">
                <div class="artmaps-comment-author"><?= $comment->comment_author; ?></div>
                <?= $comment->comment_content; ?>
                <div class="artmaps-comment-link"><a href="<?= $comment->comment_author_url; ?>">Read more</a></div>
    	<?php
    	    break;
        default: break;
    }
}}


wp_register_script("google-maps",
        "http://maps.google.com/maps/api/js?sensor=true&key=AIzaSyBDotOtQIdRgtPB6GJnMwRfUEAoluvrdqk");
wp_register_script("jquery-xcolor", get_stylesheet_directory_uri() . "/js/lib/jquery.xcolor.min.js");
wp_register_script("markerclusterer", get_stylesheet_directory_uri() . "/js/lib/markerclusterer.js");
wp_register_script("jquery-bbq", get_stylesheet_directory_uri() . "/js/lib/jquery.ba-bbq.min.js"); 
wp_register_script("styledmarker", get_stylesheet_directory_uri() . "/js/lib/styledmarker.js");
wp_register_style("artmaps", get_stylesheet_directory_uri() . "/css/artmaps.css");

?>
