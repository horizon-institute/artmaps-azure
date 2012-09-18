<?php
/*
 * Template Name: ArtMaps General Template
 */
get_header();
?>
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