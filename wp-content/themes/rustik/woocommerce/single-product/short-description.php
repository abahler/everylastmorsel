<?php
/**
 * Single Product Short Description
 */

global $woocommerce, $post;
?>

	<?php $heading = apply_filters('woocommerce_product_description_heading', __('Description', 'woocommerce')); ?>
	
	<h2><?php echo $heading; ?>:</h2>
	
	<?php the_content(); ?>


