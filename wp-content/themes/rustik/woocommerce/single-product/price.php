<?php
/**
 * Single Product Price
 */

global $post, $product;
?>
<p itemprop="price" class="price"><?php _e('Price', 'woocommerce');echo ": ".$product->get_price_html(); ?></p>