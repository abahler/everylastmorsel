<?php

/*-----------------------------------------------------------------------------------*/
/* Start WooThemes Functions - Please refrain from editing this section */
/*-----------------------------------------------------------------------------------*/

// Set path to WooFramework and theme specific functions
$functions_path = get_template_directory() . '/functions/';
$includes_path = get_template_directory() . '/includes/';

// WooFramework
require_once ($functions_path . 'admin-init.php' );			// Framework Init

/*-----------------------------------------------------------------------------------*/
/* Load the theme-specific files, with support for overriding via a child theme.
/*-----------------------------------------------------------------------------------*/

$includes = array(
				'includes/theme-options.php', 			// Options panel settings and custom settings
				'includes/theme-functions.php', 		// Custom theme functions
				'includes/theme-plugins.php', 			// Theme specific plugins integrated in a theme
				'includes/theme-actions.php', 			// Theme actions & user defined hooks
				'includes/theme-comments.php', 			// Custom comments/pingback loop
				'includes/theme-js.php', 				// Load JavaScript via wp_enqueue_script
				'includes/sidebar-init.php', 			// Initialize widgetized areas
				'includes/theme-widgets.php',			// Theme widgets
				'includes/theme-install.php',			// Theme Installation
				'includes/theme-woocommerce.php'		// WooCommerce overrides
				);

// Allow child themes/plugins to add widgets to be loaded.
$includes = apply_filters( 'woo_includes', $includes );
				
foreach ( $includes as $i ) {
	locate_template( $i, true );
}

/*-----------------------------------------------------------------------------------*/
/* You can add custom functions below */
/*-----------------------------------------------------------------------------------*/

remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10);
remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10);

if (!function_exists('woocommerce_product_taxonomy_content')) {
	function woocommerce_product_taxonomy_content() { 
		
		global $wp_query; 
		
		$term = get_term_by( 'slug', get_query_var($wp_query->query_vars['taxonomy']), $wp_query->query_vars['taxonomy']);
		
		?><!-- <h1 class="page-title"><?php //echo wptexturize($term->name); ?></h1> -->
		
		<div id="blurb" class="product_top">
			<?php woo_pagenav(); ?>
			<?php if ( woo_active_sidebar( 'attribute_filtering' ) ) : ?>
				<?php woo_sidebar( 'attribute_filtering' ); ?>
			<?php endif; ?>
			<?php
				global $woo_options;
				if ( $woo_options[ 'woo_sort_products_dropdown' ] == "true" ) {
					do_action('woocommerce_pagination'); //add the dropdown alpabetically, price etc
				}
			?>
			<div class="clear">&nbsp;</div>
		</div>
		
		<?php if ($term->description) : ?>
		
			<div class="term_description"><?php echo wpautop(wptexturize($term->description)); ?></div>
			
		<?php endif; ?>
		
		<?php woocommerce_get_template_part( 'loop', 'shop' ); ?>
		
		<?php 
	
	}
}

if (!function_exists('woocommerce_archive_product_content')) {
	function woocommerce_archive_product_content() { 
		
		$shop_page_id = get_option('woocommerce_shop_page_id');
		$shop_page = get_post($shop_page_id);
		$shop_page_title = (get_option('woocommerce_shop_page_title')) ? get_option('woocommerce_shop_page_title') : $shop_page->post_title;
		?>
		<div id="blurb" class="product_top">
			<?php woo_pagenav(); ?>
			
			<?php if ( woo_active_sidebar( 'attribute_filtering' ) ) : ?>
					<?php woo_sidebar( 'attribute_filtering' ); ?>
			<?php endif; ?>
			
			
			<?php
				global $woo_options;
				if ( $woo_options[ 'woo_sort_products_dropdown' ] == "true" ) {
					do_action('woocommerce_pagination'); //add the dropdown alpabetically, price etc
				}
			?>
			<div class="clear">&nbsp;</div>
		</div>
		
		<?php if (is_search()) : ?>		
		<h1 class="page-title"><?php _e('Search Results:', 'woothemes'); ?> &ldquo;<?php the_search_query(); ?>&rdquo; <?php if (get_query_var('paged')) echo ' &mdash; Page '.get_query_var('paged'); ?></h1>
		<?php else : ?>
			
		<?php endif; ?>
		
		<?php echo apply_filters('the_content', $shop_page->post_content); ?>

		<?php woocommerce_get_template_part( 'loop', 'shop' ); ?>	
		
		<div class="clear">&nbsp;</div>
		<?php
	
	}
}

/**
 * Adds Attribute filtering widget.
 */
add_action('init', 'woocommerce_attribute_filtering_init', 1);
add_filter('loop_shop_post_in', 'woocommerce_attribute_filtering_query');



/**
 * Layered Nav Init
 */
function woocommerce_attribute_filtering_init( ) {
	global $_chosen_attributes, $woocommerce, $_attributes_array;   
	
	$_chosen_attributes = array();
	$_attributes_array = array();
	
	$attribute_taxonomies = $woocommerce->attribute_taxonomies;   
	if ( $attribute_taxonomies ) : 
		foreach ($attribute_taxonomies as $tax) :
	    	
	    	$attribute = strtolower(sanitize_title($tax->attribute_name));
	    	$taxonomy = $woocommerce->attribute_taxonomy_name($attribute);  
			
			// create an array of product attribute taxonomies
			$_attributes_array[] = $taxonomy;
			
	    	$name = 'filter_' . $attribute;
	    	$query_type_name = 'query_type_' . $attribute;
	    	
	    	if (isset($_GET[$name]) && $_GET[$name] && taxonomy_exists($taxonomy)) : 
	    		$_chosen_attributes[$taxonomy]['terms'] = explode(',', $_GET[$name] );
	    		if (isset($_GET[$query_type_name]) && $_GET[$query_type_name]=='or') :
	    			$_chosen_attributes[$taxonomy]['query_type'] = 'or';
	    		else :
	    			$_chosen_attributes[$taxonomy]['query_type'] = 'and';
	    		endif;
			endif;
	    endforeach;    	
    endif;  	
}

/**
 * Layered Nav post filter
 */
function woocommerce_attribute_filtering_query( $filtered_posts ) {  
	global $_chosen_attributes, $woocommerce, $wp_query; 

	if (sizeof($_chosen_attributes)>0) :
		
		$matched_products = array();
		$filtered_attribute = false;
		
		foreach ($_chosen_attributes as $attribute => $data) :
			
			$matched_products_from_attribute = array();
			$filtered = false;
			
			if (sizeof($data['terms'])>0) :  
				foreach ($data['terms'] as $value) :
					
					$posts = get_objects_in_term( $value, $attribute );
					
					// AND or OR
					if ($data['query_type']=='or') :
						
						if (!is_wp_error($posts) && (sizeof($matched_products_from_attribute)>0 || $filtered)) :
							$matched_products_from_attribute = array_merge($posts, $matched_products_from_attribute);
						elseif (!is_wp_error($posts)) :
							$matched_products_from_attribute = $posts;
						endif;
						
					else :
					
						if (!is_wp_error($posts) && (sizeof($matched_products_from_attribute)>0 || $filtered)) :
							$matched_products_from_attribute = array_intersect($posts, $matched_products_from_attribute);
						elseif (!is_wp_error($posts)) :
							$matched_products_from_attribute = $posts;
						endif;
					
					endif;
					
					$filtered = true;
					
				endforeach;
			endif;
						
			if (sizeof($matched_products)>0 || $filtered_attribute) :
				$matched_products = array_intersect($matched_products_from_attribute, $matched_products);
			else :
				$matched_products = $matched_products_from_attribute;
			endif;
			
			$filtered_attribute = true;
			
		endforeach;
		
		if ($filtered) :
			
			$woocommerce->query->layered_nav_post__in = $matched_products;
			$woocommerce->query->layered_nav_post__in[] = 0;
			
			if (sizeof($filtered_posts)==0) :
				$filtered_posts = $matched_products;
				$filtered_posts[] = 0;
			else :
				$filtered_posts = array_intersect($filtered_posts, $matched_products);
				$filtered_posts[] = 0;
			endif;
			
		endif;
	endif;   

	return (array) $filtered_posts;
}

/**
 * Layered Nav Widget
 */
class WooCommerce_Widget_Attribute_Filtering extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		parent::__construct(
	 		'woocommerce_attribute_filtering', // Base ID
			'WooCommerce Attribute Filtering', // Name
			array( 'classname' => 'widget_layered_nav', 'description' => __( 'Shows a custom attribute in a widget which lets you narrow down the list of products when viewing product categories.', 'woocommerce' ), ) // Args
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	function widget( $args, $instance ) {
		extract($args);
		
		if (!is_tax( 'product_cat' ) && !is_post_type_archive('product') && !is_tax( 'product_tag' )) return;
		
		global $_chosen_attributes, $woocommerce, $wp_query;
				
		$title = apply_filters('widget_title', $instance['title'], $instance, $this->id_base);
		$taxonomy 	= $woocommerce->attribute_taxonomy_name($instance['attribute']);
		$query_type = (isset($instance['query_type'])) ? $instance['query_type'] : 'and';
		$display_type = (isset($instance['display_type'])) ? $instance['display_type'] : 'list';
		
		if (!taxonomy_exists($taxonomy)) return;

		$args = array(
			'hide_empty' => '1'
		);
		$terms = get_terms( $taxonomy, $args );
		$count = count($terms);
		
		if($count > 0){

			$found = false;
			ob_start();

			echo $before_widget . $before_title . $after_title;
			
			if ( $display_type == 'dropdown' ) {
				echo "<form name='".$title."' class='attributes-filter search".$title."'><div class='styled-select'><select name='SelectURL' onChange=
	'document.location.href=
	document.".$title.".SelectURL.options[document.".$title.".SelectURL.selectedIndex].value'><option>Search by ".$title."</option>";
				
				
				// Force found when option is selected
				if (array_key_exists($taxonomy, $_chosen_attributes)) $found = true;
				
				foreach ($terms as $term) {
					
					// Get count based on current view - uses transients
					$transient_name = 'woocommerce_layered_nav_count_' . sanitize_key($taxonomy) . sanitize_key( $term->term_id );
					
					if ( false === ( $_products_in_term = get_transient( $transient_name ) ) ) {
			
						$_products_in_term = get_objects_in_term( $term->term_id, $taxonomy );
					
						set_transient( $transient_name, $_products_in_term );
					}
					
					$option_is_set = (isset($_chosen_attributes[$taxonomy]) && in_array($term->term_id, $_chosen_attributes[$taxonomy]['terms']));
					
					// If this is an AND query, only show options with count > 0
					if ($query_type=='and') {
						
						$count = sizeof(array_intersect($_products_in_term, $woocommerce->query->filtered_product_ids));

						if ($count>0) $found = true;
					
						if ($count==0 && !$option_is_set) continue;
					
					// If this is an OR query, show all options so search can be expanded
					} else {
						
						$count = sizeof(array_intersect($_products_in_term, $woocommerce->query->unfiltered_product_ids));
						
						if ($count>0) $found = true;

					}
					
					$class = '';
					
					$arg = 'filter_'.strtolower(sanitize_title($instance['attribute']));
					
					if (isset($_GET[ $arg ])) $current_filter = explode(',', $_GET[ $arg ]); else $current_filter = array();
					
					if (!is_array($current_filter)) $current_filter = array();
					
					if (!in_array($term->term_id, $current_filter)) $current_filter[] = $term->term_id;
					
					// Base Link decided by current page
					if (defined('SHOP_IS_ON_FRONT')) :
						$link = home_url();
					elseif (is_post_type_archive('product') || is_page( get_option('woocommerce_shop_page_id') )) :
						$link = get_post_type_archive_link('product');
					else :					
						$link = get_term_link( get_query_var('term'), get_query_var('taxonomy') );
					endif;
					
					// All current filters
					if ($_chosen_attributes) foreach ($_chosen_attributes as $name => $data) :
						if ($name!==$taxonomy) :
							$link = add_query_arg( strtolower(sanitize_title(str_replace('pa_', 'filter_', $name))), implode(',', $data['terms']), $link );
							if ($data['query_type']=='or') $link = add_query_arg( strtolower(sanitize_title(str_replace('pa_', 'query_type_', $name))), 'or', $link );
						endif;
					endforeach;
					
					// Min/Max
					if (isset($_GET['min_price'])) :
						$link = add_query_arg( 'min_price', $_GET['min_price'], $link );
					endif;
					if (isset($_GET['max_price'])) :
						$link = add_query_arg( 'max_price', $_GET['max_price'], $link );
					endif;
					
					// Current Filter = this widget
					if (isset( $_chosen_attributes[$taxonomy] ) && is_array($_chosen_attributes[$taxonomy]['terms']) && in_array($term->term_id, $_chosen_attributes[$taxonomy]['terms'])) :
						$class = 'class="chosen"';
						
						// Remove this term is $current_filter has more than 1 term filtered
						if (sizeof($current_filter)>1) :
							$current_filter_without_this = array_diff($current_filter, array($term->term_id));
							$link = add_query_arg( $arg, implode(',', $current_filter_without_this), $link );
						endif;
						
					else :
						$link = add_query_arg( $arg, implode(',', $current_filter), $link );
					endif;
					
					// Search Arg
					if (get_search_query()) :
						$link = add_query_arg( 's', get_search_query(), $link );
					endif;
					
					// Post Type Arg
					if (isset($_GET['post_type'])) :
						$link = add_query_arg( 'post_type', $_GET['post_type'], $link );
					endif;
					
					// Query type Arg
					if ($query_type=='or' && !( sizeof($current_filter) == 1 && isset( $_chosen_attributes[$taxonomy]['terms'] ) && is_array($_chosen_attributes[$taxonomy]['terms']) && in_array($term->term_id, $_chosen_attributes[$taxonomy]['terms']) )) :
						$link = add_query_arg( 'query_type_'.strtolower(sanitize_title($instance['attribute'])), 'or', $link );
					endif;
					
					echo '<option '.$class.' value='.$link.'>';
					
					if ($count>0 || $option_is_set) echo ''; else echo '<span>';
					
					echo $term->name;
					
					if ($count>0 || $option_is_set) echo ''; else echo '</span>';
					
					echo ' </option>';
					
				}
				
				echo "</select></div></form>";
			} else {
				
				// List display
				echo "<div class='styled-list-layered'><ul>";
				
				foreach ($terms as $term) { 
					
					// Get count based on current view - uses transients
					$transient_name = 'wc_ln_count_' . md5( sanitize_key($taxonomy) . sanitize_key( $term->term_id ) );
					
					if ( false === ( $_products_in_term = get_transient( $transient_name ) ) ) {
			
						$_products_in_term = get_objects_in_term( $term->term_id, $taxonomy );
	
						set_transient( $transient_name, $_products_in_term );
					}
					
					$option_is_set = (isset($_chosen_attributes[$taxonomy]) && in_array($term->term_id, $_chosen_attributes[$taxonomy]['terms'])) ; 
	
					// If this is an AND query, only show options with count > 0
					if ($query_type=='and') {
						
						$count = sizeof(array_intersect($_products_in_term, $woocommerce->query->filtered_product_ids));

						// skip the term for the current archive
						if ( $current_term == $term->term_id ) continue; 
						
						if ($count>0 && $current_term !== $term->term_id ) $found = true;
					
						if ($count==0 && !$option_is_set) continue;
					
					// If this is an OR query, show all options so search can be expanded
					} else { 
						
						// skip the term for the current archive
						if ( $current_term == $term->term_id ) continue;
						
						$count = sizeof(array_intersect($_products_in_term, $woocommerce->query->unfiltered_product_ids));
						
						if ($count>0) $found = true;
	
					}
					
					$class = '';
					
					$arg = 'filter_'.strtolower(sanitize_title($instance['attribute']));
					
					if (isset($_GET[ $arg ])) $current_filter = explode(',', $_GET[ $arg ]); else $current_filter = array();
					
					if (!is_array($current_filter)) $current_filter = array();
					
					if (!in_array($term->term_id, $current_filter)) $current_filter[] = $term->term_id;
					
					// Base Link decided by current page
					if (defined('SHOP_IS_ON_FRONT')) :
						$link = home_url();
					elseif (is_post_type_archive('product') || is_page( woocommerce_get_page_id('shop') )) :
						$link = get_post_type_archive_link('product');
					else :					
						$link = get_term_link( get_query_var('term'), get_query_var('taxonomy') );
					endif;   
			
					// All current filters
					if ($_chosen_attributes) : 	
						foreach ($_chosen_attributes as $name => $data) : 
							if ( $name!==$taxonomy ) :  
								
								//exclude query arg for current term archive term						
								while(in_array($current_term, $data['terms'])) {
									$key = array_search($current_term, $data);
									unset($data['terms'][$key]);
								}
	
								if(!empty($data['terms'])){
									$link = add_query_arg( strtolower(sanitize_title(str_replace('pa_', 'filter_', $name))), implode(',', $data['terms']), $link );  
								}
								
								if ($data['query_type']=='or') $link = add_query_arg( strtolower(sanitize_title(str_replace('pa_', 'query_type_', $name))), 'or', $link ); 
							endif; 
						endforeach;
					endif;
					
					// Min/Max
					if (isset($_GET['min_price'])) :
						$link = add_query_arg( 'min_price', $_GET['min_price'], $link );
					endif;
					if (isset($_GET['max_price'])) :
						$link = add_query_arg( 'max_price', $_GET['max_price'], $link );
					endif;
					
					// Current Filter = this widget
					if (isset( $_chosen_attributes[$taxonomy] ) && is_array($_chosen_attributes[$taxonomy]['terms']) && in_array($term->term_id, $_chosen_attributes[$taxonomy]['terms'])) :
						$class = 'class="chosen"';
						
						// Remove this term is $current_filter has more than 1 term filtered
						if (sizeof($current_filter)>1) :  
							$current_filter_without_this = array_diff($current_filter, array($term->term_id));
							$link = add_query_arg( $arg, implode(',', $current_filter_without_this), $link );
						endif;
						
					else :
						$link = add_query_arg( $arg, implode(',', $current_filter), $link );
					endif;
					
					// Search Arg
					if (get_search_query()) :
						$link = add_query_arg( 's', get_search_query(), $link );
					endif;
					
					// Post Type Arg
					if (isset($_GET['post_type'])) :
						$link = add_query_arg( 'post_type', $_GET['post_type'], $link );
					endif;
					
					// Query type Arg
					if ($query_type=='or' && !( sizeof($current_filter) == 1 && isset( $_chosen_attributes[$taxonomy]['terms'] ) && is_array($_chosen_attributes[$taxonomy]['terms']) && in_array($term->term_id, $_chosen_attributes[$taxonomy]['terms']) )) :
						$link = add_query_arg( 'query_type_'.strtolower(sanitize_title($instance['attribute'])), 'or', $link );
					endif;
					
					echo '<li '.$class.'>';
					
					if ($count>0 || $option_is_set) echo '<a href="'.$link.'">'; else echo '<span>';
					
					echo $term->name;
					
					if ($count>0 || $option_is_set) echo '</a>'; else echo '</span>';
					
					//echo ' <small class="count">'.$count.'</small></li>';
					echo '</li>';
					
				}
				
				echo "</ul></div>"; // Close the list
			}
			echo $after_widget;
			
			if (!$found) :
				ob_clean();
				return;
			else :
				$widget = ob_get_clean();
				echo $widget;
			endif;
			
		}
	}
	
	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		global $woocommerce;
		if (!isset($new_instance['title']) || empty($new_instance['title'])) $new_instance['title'] = $woocommerce->attribute_label($new_instance['attribute']);
		$instance['title'] = strip_tags(stripslashes($new_instance['title']));
		$instance['attribute'] = stripslashes($new_instance['attribute']);
		$instance['query_type'] = stripslashes($new_instance['query_type']);
		$instance['display_type'] = stripslashes($new_instance['display_type']);
		return $instance;
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		global $woocommerce;
		
		if (!isset($instance['query_type'])) $instance['query_type'] = 'and';
		if (!isset($instance['display_type'])) $instance['display_type'] = 'list';
		?>
			<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'woocommerce') ?></label>
			<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id('title') ); ?>" name="<?php echo esc_attr( $this->get_field_name('title') ); ?>" value="<?php if (isset ( $instance['title'])) {echo esc_attr( $instance['title'] );} ?>" /></p>
			
			<p><label for="<?php echo $this->get_field_id('attribute'); ?>"><?php _e('Attribute:', 'woocommerce') ?></label>
			<select id="<?php echo esc_attr( $this->get_field_id('attribute') ); ?>" name="<?php echo esc_attr( $this->get_field_name('attribute') ); ?>">
				<?php
				$attribute_taxonomies = $woocommerce->get_attribute_taxonomies();
				if ( $attribute_taxonomies ) :
					foreach ($attribute_taxonomies as $tax) :
						if (taxonomy_exists( $woocommerce->attribute_taxonomy_name($tax->attribute_name))) :
							
							echo '<option value="'.$tax->attribute_name.'" ';
							if (isset($instance['attribute']) && $instance['attribute']==$tax->attribute_name) :
								echo 'selected="selected"';
							endif;
							echo '>'.$tax->attribute_name.'</option>';
							
						endif;
					endforeach;
				endif;
				?>
			</select></p>
			
			<p><label for="<?php echo $this->get_field_id('display_type'); ?>"><?php _e('Display Type:', 'woocommerce') ?></label>
			<select id="<?php echo esc_attr( $this->get_field_id('display_type') ); ?>" name="<?php echo esc_attr( $this->get_field_name('display_type') ); ?>">
				<option value="list" <?php selected($instance['display_type'], 'list'); ?>><?php _e('List', 'woocommerce'); ?></option>
				<option value="dropdown" <?php selected($instance['display_type'], 'dropdown'); ?>><?php _e('Dropdown', 'woocommerce'); ?></option>
			</select></p>
			
			<p><label for="<?php echo $this->get_field_id('query_type'); ?>"><?php _e('Query Type:', 'woocommerce') ?></label>
			<select id="<?php echo esc_attr( $this->get_field_id('query_type') ); ?>" name="<?php echo esc_attr( $this->get_field_name('query_type') ); ?>">
				<option value="and" <?php selected($instance['query_type'], 'and'); ?>><?php _e('AND', 'woocommerce'); ?></option>
				<option value="or" <?php selected($instance['query_type'], 'or'); ?>><?php _e('OR', 'woocommerce'); ?></option>
			</select></p>
		<?php 
	}

} // class WooCommerce_Widget_Attribute_Filtering

// register WooCommerce_Widget_Attribute_Filtering widget
add_action( 'widgets_init', create_function( '', 'register_widget( "WooCommerce_Widget_Attribute_Filtering" );' ) );

/**
 * Include the TGM_Plugin_Activation class for installing the nivo slider by default
 */
require_once dirname( __FILE__ ) . '/class-tgm-plugin-activation.php';

add_action( 'tgmpa_register', 'my_theme_register_required_plugins' );

/**
 * Register the required plugins for this theme.
 *
 * In this example, we register two plugins - one included with the TGMPA library
 * and one from the .org repo.
 *
 * The variable passed to tgmpa_register_plugins() should be an array of plugin
 * arrays.
 *
 * This function is hooked into tgmpa_init, which is fired within the
 * TGM_Plugin_Activation class constructor.
 */
function my_theme_register_required_plugins() {

	/**
	 * Array of plugin arrays. Required keys are name and slug.
	 * If the source is NOT from the .org repo, then source is also required.
	 */
	$plugins = array(

		// This is an example of how to include a plugin pre-packaged with a theme
		array(
			'name'     				=> 'Nivo Slider for WordPress', // The plugin name
			'slug'     				=> 'nivo-slider-for-wordpress', // The plugin slug (typically the folder name)
			'source'   				=> get_stylesheet_directory() . '/plugins/nivo-slider-for-wordpress.zip', // The plugin source
			'required' 				=> true, // If false, the plugin is only 'recommended' instead of required
			'version' 				=> '', // E.g. 1.0.0. If set, the active plugin must be this version or higher, otherwise a notice is presented
			'force_activation' 		=> true, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
			'force_deactivation' 	=> false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
			'external_url' 			=> '', // If set, overrides default API URL and points to an external URL
		),
		
		// This is an example of how to include a plugin from the WordPress Plugin Repository
		array(
			'name' 		=> 'WooCommerce',
			'slug' 		=> 'woocommerce',
			'required' 	=> true,
		),

	);

	// Change this to your theme text domain, used for internationalising strings
	$theme_text_domain = 'woocommerce';

	/**
	 * Array of configuration settings. Amend each line as needed.
	 * If you want the default strings to be available under your own theme domain,
	 * leave the strings uncommented.
	 * Some of the strings are added into a sprintf, so see the comments at the
	 * end of each line for what each argument will be.
	 */
	$config = array(
		'domain'       		=> $theme_text_domain,         	// Text domain - likely want to be the same as your theme.
		'default_path' 		=> '',                         	// Default absolute path to pre-packaged plugins
		'parent_menu_slug' 	=> 'themes.php', 				// Default parent menu slug
		'parent_url_slug' 	=> 'themes.php', 				// Default parent URL slug
		'menu'         		=> 'install-required-plugins', 	// Menu slug
		'has_notices'      	=> true,                       	// Show admin notices or not
		'is_automatic'    	=> true,					   	// Automatically activate plugins after installation or not
		'message' 			=> '',							// Message to output right before the plugins table
		'strings'      		=> array(
			'page_title'                       			=> __( 'Install Required Plugins', $theme_text_domain ),
			'menu_title'                       			=> __( 'Install Plugins', $theme_text_domain ),
			'installing'                       			=> __( 'Installing Plugin: %s', $theme_text_domain ), // %1$s = plugin name
			'oops'                             			=> __( 'Something went wrong with the plugin API.', $theme_text_domain ),
			'notice_can_install_required'     			=> _n_noop( 'This theme requires the following plugin: %1$s.', 'This theme requires the following plugins: %1$s.' ), // %1$s = plugin name(s)
			'notice_can_install_recommended'			=> _n_noop( 'This theme recommends the following plugin: %1$s.', 'This theme recommends the following plugins: %1$s.' ), // %1$s = plugin name(s)
			'notice_cannot_install'  					=> _n_noop( 'Sorry, but you do not have the correct permissions to install the %s plugin. Contact the administrator of this site for help on getting the plugin installed.', 'Sorry, but you do not have the correct permissions to install the %s plugins. Contact the administrator of this site for help on getting the plugins installed.' ), // %1$s = plugin name(s)
			'notice_can_activate_required'    			=> _n_noop( 'The following required plugin is currently inactive: %1$s.', 'The following required plugins are currently inactive: %1$s.' ), // %1$s = plugin name(s)
			'notice_can_activate_recommended'			=> _n_noop( 'The following recommended plugin is currently inactive: %1$s.', 'The following recommended plugins are currently inactive: %1$s.' ), // %1$s = plugin name(s)
			'notice_cannot_activate' 					=> _n_noop( 'Sorry, but you do not have the correct permissions to activate the %s plugin. Contact the administrator of this site for help on getting the plugin activated.', 'Sorry, but you do not have the correct permissions to activate the %s plugins. Contact the administrator of this site for help on getting the plugins activated.' ), // %1$s = plugin name(s)
			'notice_ask_to_update' 						=> _n_noop( 'The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.', 'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.' ), // %1$s = plugin name(s)
			'notice_cannot_update' 						=> _n_noop( 'Sorry, but you do not have the correct permissions to update the %s plugin. Contact the administrator of this site for help on getting the plugin updated.', 'Sorry, but you do not have the correct permissions to update the %s plugins. Contact the administrator of this site for help on getting the plugins updated.' ), // %1$s = plugin name(s)
			'install_link' 					  			=> _n_noop( 'Begin installing plugin', 'Begin installing plugins' ),
			'activate_link' 				  			=> _n_noop( 'Activate installed plugin', 'Activate installed plugins' ),
			'return'                           			=> __( 'Return to Required Plugins Installer', $theme_text_domain ),
			'plugin_activated'                 			=> __( 'Plugin activated successfully.', $theme_text_domain ),
			'complete' 									=> __( 'All plugins installed and activated successfully. %s', $theme_text_domain ), // %1$s = dashboard link
			'nag_type'									=> 'updated' // Determines admin notice type - can only be 'updated' or 'error'
		)
	);

	tgmpa( $plugins, $config );

}
/*-----------------------------------------------------------------------------------*/
/* Don't add any code below here or the sky will fall down */
/*-----------------------------------------------------------------------------------*/
?>