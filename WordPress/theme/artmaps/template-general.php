<?php
/*
 * Template Name: ArtMaps General Template
 */
add_filter("show_admin_bar", "__return_false" );
remove_action('wp_head', '_admin_bar_bump_cb');
foreach(array("jquery", "jquery-bbq") as $script)
    wp_enqueue_script($script);
get_header();
?>
<script type="text/javascript">
jQuery(document).ready(function($) {
    var navLink = $("#artmaps_meta-2");
    var navText = navLink.children("h3").first();
    navLink.css({"cursor": "pointer"});
    navText.css({});
    <?php if (is_user_logged_in()) { ?>
        navText.text("Logout");
        navText.addClass("artmaps-logout-link");
        navLink.click(function() {
            if(window.confirm("Are you sure you wish to log out?")) {
                document.location.href =
                    "<?= str_replace("&amp;", "&", wp_logout_url(get_permalink())); ?>";
            }
        });
    <?php } else { ?>
        navText.text("Login");
        navText.addClass("artmaps-login-link");
        navLink.click(function() { document.location.href =
            "<?= str_replace("&amp;", "&", wp_login_url(get_permalink())); ?>"; });
    <?php } ?>
});
function changeLocation(url) {
    document.location.href = url;
}
</script>
<div id="primary">
    <div id="content" role="main">
        <?php
        while(have_posts()) {
            the_post();
            get_template_part("content", "page");
        }
        ?>
        <div id="comments"></div>
    </div>
</div>
<?php
get_sidebar();
get_footer();
?>
