<?php
/*
Template Name: Chefs and Farmers
*/
?>

<?php get_header(); ?>
<?php global $woo_options; ?>
       
    <div id="content" class="page col-full">
		
		<div id="main" class="right_products">
		<?php if ( isset( $woo_options[ 'woo_breadcrumbs_show' ] ) && $woo_options[ 'woo_breadcrumbs_show' ] == 'true' ) { ?>
			<?php woo_breadcrumbs(); ?>  
		<?php } ?>

<script>
<!--
function goto(choose){
var selected=choose.options[choose.selectedIndex].value;
    if(selected != ""){
    location.href=selected;
    }
}
//-->
</script>

<div id="chefs" class="quickbox">
 <select accesskey="S" onchange="javascript:goto(this);">
 <option selected>Search by Chef</option>
 <option value="/?page_id=289">Chris McCoy</option>
 <option value="/?page_id=292">Paul Kahn</option>
 <option value="/?page_id=277">Stephanie Izard</option>
 </select>
</div>

<div id="farmers" class="quickbox">
 <select accesskey="S" onchange="javascript:goto(this);">
 <option selected>Search by Farmer</option>
 <option value="/?page_id=329">Bryn Ragel</option>
 <option value="/?page_id=336">Nathan Elliot</option>
 <option value="/?page_id=334">Susan & Jenn Weaver</option>
 </select>
</div>

<br/><br/>
        
        <?php if (have_posts()) : $count = 0; ?>
        <?php while (have_posts()) : the_post(); $count++; ?>
                                                                    
            <div <?php post_class(); ?>>
			   
                <div class="entry">
                	<?php the_content(); ?>

					<?php wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', 'woothemes' ), 'after' => '</div>' ) ); ?>
               	</div><!-- /.entry -->

				<?php edit_post_link( __( '{ Edit }', 'woothemes' ), '<span class="small">', '</span>' ); ?>
                
            </div><!-- /.post -->
            
            <?php $comm = $woo_options[ 'woo_comments' ]; if ( ($comm == "page" || $comm == "both") ) : ?>
                <?php comments_template(); ?>
            <?php endif; ?>
                                                
		<?php endwhile; else: ?>
			<div <?php post_class(); ?>>
            	<p><?php _e( 'Sorry, no posts matched your criteria.', 'woothemes' ) ?></p>
            </div><!-- /.post -->
        <?php endif; ?>  
        </div><!-- /#right products -->
		

        <?php get_sidebar(); ?>
		<div class="clear"></div>
    </div><!-- /#content -->
		
<?php get_footer(); ?>