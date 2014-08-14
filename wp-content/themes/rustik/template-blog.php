<?php
/*
Template Name: Blog
*/

get_header();
global $woo_options;
?>
    <!-- #content Starts -->
	<?php woo_content_before(); ?>
    <div id="content" class="content blog">
	<div class="container"> 
            <div class="right_products">
                    
		<?php if ( isset( $woo_options[ 'woo_breadcrumbs_show' ] ) && $woo_options[ 'woo_breadcrumbs_show' ] == 'true' ) { ?>
				<?php woo_breadcrumbs(); ?> 
		<?php } ?>  

        <?php
        	if ( get_query_var( 'paged') ) { $paged = get_query_var( 'paged' ); } elseif ( get_query_var( 'page') ) { $paged = get_query_var( 'page' ); } else { $paged = 1; }
        	
        	$query_args = array(
        						'post_type' => 'post', 
        						'paged' => $paged
        					);
        	
        	$query_args = apply_filters( 'woo_blog_template_query_args', $query_args ); // Do not remove. Used to exclude categories from displaying here.
        	
        	query_posts( $query_args );
        	
        	if ( have_posts() ) {
        		$count = 0;
        		while ( have_posts() ) { the_post(); $count++;
        ?>                                                            
              <div <?php post_class(); ?>>
                      
				<div class="left">
			<h5><?php the_time('d') ?></h5>
			<b><?php the_time('M, y') ?></b>
			<div class="clear"></div>
			</div>
			<div class="right"><h2 class="title"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>"><?php the_title(); ?></a></h2>
			<?php if ( $woo_options[ 'woo_post_content' ] != "content" ) woo_image( 'width='.$woo_options[ 'woo_thumb_w' ].'&height='.$woo_options[ 'woo_thumb_h' ].'&class=thumbnail '.$woo_options[ 'woo_thumb_align' ]); ?>
			<?php if ( $woo_options[ 'woo_post_content' ] == "content" ) the_content(__( 'Read More...', 'woothemes' )); else the_excerpt(); ?>
			<p><?php if ( $woo_options[ 'woo_post_content' ] == "excerpt" ) { ?>
					<span class="read-more"><a href="<?php the_permalink() ?>" title="<?php esc_attr_e( 'Read more', 'woothemes' ); ?>"><?php _e( 'Read more', 'woothemes' ); ?></a></span>
					<span class="commentlink"><?php comments_popup_link(__( 'Comment', 'woothemes' ), __( '1 Comment', 'woothemes' ), __( '% Comments', 'woothemes' )); ?></span>
                    <?php } ?></p>
			</div>
			<div class="clear"></div>	
				
				
				
                
            

            </div><!-- /.post -->
                                                
        <?php
        		} // End WHILE Loop
        	
        	} else {
        ?>
            <div <?php post_class(); ?>>
                <p><?php _e( 'Sorry, no posts matched your criteria.', 'woothemes' ); ?></p>
            </div><!-- /.post -->
        <?php } // End IF Statement ?>  
    
            <?php woo_pagenav(); ?>
			<?php wp_reset_query(); ?>                

        </div><!-- /#main -->
            
		<?php get_sidebar(); ?>

    </div><!-- /#content -->  
    </div>  
		
<?php get_footer(); ?>