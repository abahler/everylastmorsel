<?php get_header('shop'); ?>
<?php do_action('woocommerce_sidebar'); ?>
<?php do_action('woocommerce_before_main_content'); // <div id="container"><div id="content" role="main"> ?>

<?php woocommerce_archive_product_content(); ?>

<?php do_action('woocommerce_after_main_content'); // </div></div> ?>
<?php get_footer('shop'); ?>