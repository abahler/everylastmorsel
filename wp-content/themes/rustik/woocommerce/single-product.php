<?php get_header('shop'); ?>
<?php do_action('woocommerce_sidebar'); ?>
<?php do_action('woocommerce_before_main_content'); // <div id="container"><div id="content" role="main"> ?>

	<?php if ( have_posts() ) while ( have_posts() ) : the_post(); global $_product; $_product = &new WC_Product( $post->ID ); ?>
		
		
	
		<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			
			<div class="summary">
			

			<?php do_action('woocommerce_before_single_product_summary', $post, $_product); ?>
			<?php do_action('woocommerce_single_product_summary', $post, $_product); ?>
			
			<!-- AddThis Button BEGIN -->
	<div class="addthis_toolbox addthis_default_style ">
	<a class="addthis_button_facebook_like" fb:like:layout="button_count"></a>
	<a class="addthis_button_tweet"></a>
	<a class="addthis_button_pinterest_pinit"></a>
	<a class="addthis_counter addthis_pill_style"></a>
	</div>
	<script type="text/javascript">var addthis_config = {"data_track_addressbar":true};</script>
	<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-5127ab9c6a80738c"></script>
	<!-- AddThis Button END -->
			</div>
		</div>
		<?php //do_action('woocommerce_after_single_product_summary', $post, $_product); ?>
		<?php do_action('woocommerce_after_single_product', $post, $_product); ?>
	
	<?php endwhile; ?>

<?php do_action('woocommerce_after_main_content'); // </div></div> ?>

<?php get_footer('shop'); ?>