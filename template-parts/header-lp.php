<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

?>

<!doctype html>
<html <?php language_attributes(); ?>>
<!-- This header generates through Landing page plugin and not from your theme -->
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
	<?php wp_head(); ?>
</head>
<?php
$bg_image = !wp_is_mobile() ? has_post_thumbnail() ? get_the_post_thumbnail_url() : '' : wp_get_attachment_image_url( get_post_meta(get_the_ID(), 'second_featured_img', true), 'full' );
?>
<body <?php body_class(); ?> style="background-image:url(<?php echo $bg_image; ?>)">
