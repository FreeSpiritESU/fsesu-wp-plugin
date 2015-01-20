<?php
    $post = get_post($_GET['id']);
    if ($post) : 
    setup_postdata($post); ?>
	<div class="whatever">
		<h2 class="entry-title"><?php the_title() ?></h2>
		<div class="entry-content">
			Content: <?php the_content(); ?>
		</div>
	</div>
<?php endif;