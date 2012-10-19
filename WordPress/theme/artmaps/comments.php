<div id="comments">
<?php if(have_comments()) { ?>
    <h2 id="comments-title">Comments</h2>
    <ol class="commentlist">
	    <?php wp_list_comments(array("callback" => "artmapsComment")); ?>
	</ol>
<?php } ?>
</div>
