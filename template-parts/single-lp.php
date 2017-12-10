<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

global $post;
setup_postdata( $post );
$post_id = get_the_ID();
$lp_subtitle = get_post_meta( $post_id, 'lp_subtitle', true );
$lp_phone = get_post_meta( $post_id, 'lp_phone', true );
$lp_external_link = get_post_meta( $post_id, 'lp_external_link', true );
$lp_page_form = get_post_meta( $post_id, 'lp_page_form', true );
$lp_show_testimonials = get_post_meta( $post_id, 'lp_show_testimonials', true );
$lp_testimonials_list = get_post_meta( $post_id, 'lp_testimonials_list', true );
$second_featured_img = get_post_meta( $post_id, 'second_featured_img', true );

$lp_options = get_option('landing_page_settings_lp');
$test_options = get_option('landing_page_settings_test');
$css_options = get_option('landing_page_settings_css');

$template = get_post_meta( $post_id, 'lp_template', true );

$layout = "";
$row = "";

if (isset($lp_options['text_instead_form']) && !empty($lp_options['text_instead_form'])) {
    $form_content = $lp_options['text_instead_form'];
} else {
    $form_content = "";
}

if (isset($lp_options['layout']) && !empty($lp_options['layout'])) {
    $layout = $lp_options['layout'];
    $row = $layout == "container" ? "row" : "";
}

if (isset($lp_options['cusotm_logo']) && !empty($lp_options['cusotm_logo'])) {
    $custom_logo = wp_get_attachment_image_src( $lp_options['cusotm_logo'], 'full' )[0];
} else if (has_custom_logo()) {
    $custom_logo_id = get_theme_mod( 'custom_logo' );
    $image = wp_get_attachment_image_src( $custom_logo_id , 'full' );
    $custom_logo = $image[0];
} else {
    $custom_logo = plugin_dir_url( __DIR__ ).'/images/logo_ph.png';
}
?>

<section class="section" style="">
    <div class="<?php echo $layout; ?>">
        <div class="<?php echo $row; ?>">
            <?php include ( plugin_dir_path( __FILE__ ).'content-'.$template.'.php'  ); ?>
        </div>
    </div>
</section>

