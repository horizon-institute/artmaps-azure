<?php
foreach(array('artmaps-template-general') as $style)
    wp_enqueue_style($style);
if(have_posts())
    the_post();
global $ArtmapsPageTitle;
$ArtmapsPageTitle = the_title('', '', false);
get_header();
?>
<article>
    <header><?= $ArtmapsPageTitle ?></header>
	<div><?php the_content(); ?></div>
</article>
<?php
global $comments, $comment;
$number = 5;
$comments = get_comments(array( 'number' => $number, 'status' => 'approve', 'post_status' => 'publish'));
$output = '<ul id="recentcomments">';
if($comments) {
    foreach((array)$comments as $comment) {
        $output .=  '<li class="recentcomments">' . sprintf(_x('%1$s on %2$s', 'widgets'), get_comment_author_link(), '<a href="' . esc_url( get_comment_link($comment->comment_ID) ) . '">' . get_the_title($comment->comment_post_ID) . '</a>') . '</li>';
	}
 }
$output .= '</ul>';
echo $output;
?>
<ul>
    <li><?php wp_register('', ''); ?></li>
    <li><?php wp_loginout(); ?></li>
</ul>
<?php get_footer(); ?>