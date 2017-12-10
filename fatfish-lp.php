<?php
/**
 * Plugin name: FatFish Landind Page
 * Plugin URI:  https://github.com/shgotlib/HWP-favorites-management
 * Text Domain: FatFishLP
 * Domain Path: /languages
 * Description: Create and style your own Landing pages right from your website.
 * Version:     1.0
 * Required:    4
 * Author:      Shlomi Gottlieb
 * License:     MIT
 * License URI: http://www.opensource.org/licenses/mit-license.php
 *
 * Copyright (c) 2017 Shlomi Gottlieb
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

class FatFishLP {

    function __construct() {
        include_once "inc/settings.php";

        register_activation_hook( __FILE__, array($this, 'plugin_activated') );        

        add_action( 'init', array($this, 'lp_init') );
        add_action('plugins_loaded', array($this, 'plugin_init'));
        add_action( 'add_meta_boxes', array($this, 'add_boxes') );
        add_action( 'save_post', array($this, 'lp_save_meta') );

        add_action('wp_print_scripts', array($this, 'lp_register_scripts'));
        add_action('wp_print_styles', array($this, 'lp_register_styles'));
        add_action( 'admin_enqueue_scripts', array($this, 'lp_include_adminscript') );

        add_filter( 'template_include', array($this, 'include_template_lp'), 1 );

        add_action('wp_footer', array($this, 'add_css_to_footer'), 999);

        add_shortcode('show_testimonials', array($this,'display_testimonial_slider'));
    }

    function plugin_activated() {
        $this->lp_init();
        flush_rewrite_rules();

        if ( is_plugin_active( 'contact-form-7/wp-contact-form-7.php' ) ) {
            $option['form_provider']['cf7'] = '1';
            update_option( 'landing_page_settings_lp', $option );
        } 

        if ( is_plugin_active( 'visual-form-builder/visual-form-builder.php' ) ) {
            $option['form_provider']['vfb'] = '1';
            update_option( 'landing_page_settings_lp', $option );
        }
        if (! term_exists( 'Uncategorised', 'testimonials_cat' )) {
            wp_insert_term( __('Uncategorised', 'FatFishLP'), 'testimonials_cat', array('slug' => 'uncategorised_testimonials' ) );
        }
    }
	
	function include_template_lp( $template_path ) {
	
		if ( get_post_type() != 'lp' ) {
            return $template_path;
        }

        if ( is_single() ) {
            // checks if the file exists in the theme first,
            // otherwise serve the file from the plugin
            if ( $theme_file = locate_template( array ( '/template-parts/single-lp.php' ) ) ) {
                $template_path = $theme_file;
            } else {
                $template_path = plugin_dir_path( __FILE__ ) . '/template-parts/single-lp.php';
            }

            if (file_exists(plugin_dir_path( __FILE__ ) . '/template-parts/header-lp.php')) {
                require plugin_dir_path( __FILE__ ) . '/template-parts/header-lp.php';
            } else {
                get_header('lp');
            }
            
            include $template_path;
            
            if (file_exists(plugin_dir_path( __FILE__ ) . '/template-parts/footer-lp.php')) {
                require plugin_dir_path( __FILE__ ) . '/template-parts/footer-lp.php';
            } else {
                get_footer('lp');
            }
            
            return false;
        }

        if ( is_archive() ) {

            if ( $theme_file = locate_template( array ( 'archive-lp.php' ) ) ) {
                $template_path = $theme_file;
            } else {
                $template_path = plugin_dir_path( __FILE__ ) . '/template-parts/archive-lp.php';
            }
        }

        if ( is_tax() ) {
            if ( $theme_file = locate_template( array ( 'taxonomy-lp.php' ) ) ) {
                $template_path = $theme_file;
            } else {
                $template_path = plugin_dir_path( __FILE__ ) . '/template-parts/taxonomy-lp.php';
            }
        }

        return $template_path;
	}	

    function lp_init() {
        $labels = array(
            'name'               => _x( 'Landing Pages', 'post type general name', 'FatFishLP' ),
            'singular_name'      => _x( 'Landing Page', 'post type singular name', 'FatFishLP' ),
            'menu_name'          => _x( 'Landing Pages', 'admin menu', 'FatFishLP' ),
            'name_admin_bar'     => _x( 'Landing Page', 'add new on admin bar', 'FatFishLP' ),
            'add_new'            => _x( 'Add New', 'Landing Page', 'FatFishLP' ),
            'add_new_item'       => __( 'Add New Landing Page', 'FatFishLP' ),
            'new_item'           => __( 'New Landing Page', 'FatFishLP' ),
            'edit_item'          => __( 'Edit Landing Page', 'FatFishLP' ),
            'view_item'          => __( 'View Landing Page', 'FatFishLP' ),
            'all_items'          => __( 'All Landing Pages', 'FatFishLP' ),
            'search_items'       => __( 'Search Landing Pages', 'FatFishLP' ),
            'parent_item_colon'  => __( 'Parent Landing Pages:', 'FatFishLP' ),
            'not_found'          => __( 'No Landing Pages found.', 'FatFishLP' ),
            'not_found_in_trash' => __( 'No Landing Pages found in Trash.', 'FatFishLP' )
        );
    
        $args = array(
            'labels'             => $labels,
            'description'        => '',
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => array( 'slug' => 'lp' ),
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_icon'			 => 'dashicons-awards',
            'menu_position'      => null,
            'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments', 'revision' )
        );
    
        register_post_type( 'lp', $args );

        $labels = array(
            'name'               => 'ממליצים',
            'singular_name'      => 'ממליץ',
            'menu_name'          => 'ממליצים',
            'name_admin_bar'     => 'ממליץ',
            'add_new'            => 'הוסף ממליץ חדש',
            'add_new_item'       => 'הוסף ממליץ חדש',
            'new_item'           => 'הוסף ממליץ חדש',
            'edit_item'          => 'ערוך ממליץ',
            'view_item'          => 'הצג ממליץ',
            'all_items'          => 'כל הממליצים',
            'search_items'       => 'חיפוש ממליצים',
            'parent_item_colon'  => 'ממליץ אב',
            'not_found'          => 'לא נמצאו ממליצים',
            'not_found_in_trash' => 'לא נמצאו ממליצים באשפה',
        );
        $args = array(
            'public' => true,
            'labels' => $labels,
            'menu_icon' => 'dashicons-testimonial',
            'supports' => array(
                'title',
                'editor',
                'thumbnail',
                'revison'
            )
        );
        register_post_type('testimonials', $args);
        
        $labels = array(
            'name'              => 'סוגי ממליצים',
            'singular_name'     => 'סוג ממליץ',
            'all_items'         => 'כל סוגי הממליצים',
            'edit_item'         => 'עריכת סוג ממליצים',
            'update_item'       => 'עדכון סוג ממליצים',
            'add_new_item'      => 'הוסף סוג ממליצים חדש',
            'new_item_name'     => 'הוסף סוג ממליצים חדש',
            'menu_name'         => 'סוגי ממליצים',
        );
    
        $args = array(
            'hierarchical'      => true,
            'labels'            => $labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array( 'slug' => 'testimonials_cat' ),
        );
    
        register_taxonomy( 'testimonials_cat', array( 'testimonials' ), $args );
    }

    function lp_save_meta( $post_id ) {
        if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) {
            return $post_id;
        }
        // for testimonails
        if ( isset( $_POST['testimonail_link'] ) ) {
            update_post_meta( $post_id, 'testimonail_link', $_POST['testimonail_link']);
        }
        if ( isset( $_POST['testimonail_role'] ) ) {
            update_post_meta( $post_id, 'testimonail_role', esc_html($_POST['testimonail_link']));
        }

        // for landing pages
        if ( isset( $_POST['lp_subtitle'] ) ) {
            update_post_meta( $post_id, 'lp_subtitle', esc_html($_POST['lp_subtitle']));
        }
        if ( isset( $_POST['lp_phone'] ) ) {
            update_post_meta( $post_id, 'lp_phone', esc_html($_POST['lp_phone']));
        }
        if ( isset( $_POST['lp_external_link'] ) ) {
            update_post_meta( $post_id, 'lp_external_link', $_POST['lp_external_link']);
        }
        if ( isset( $_POST['lp_page_form'] ) ) {
            update_post_meta( $post_id, 'lp_page_form', esc_html($_POST['lp_page_form']));
        }
        if ( isset( $_POST['lp_show_testimonials'] ) ) {
            update_post_meta( $post_id, 'lp_show_testimonials', $_POST['lp_show_testimonials']);
        } else {
            update_post_meta( $post_id, 'lp_show_testimonials', 'no');
        }
        if ( isset( $_POST['lp_testimonials_list'] ) ) {
            update_post_meta( $post_id, 'lp_testimonials_list', $_POST['lp_testimonials_list']);
        }
        if ( isset( $_POST['second_featured_img'] ) ) {
            update_post_meta( $post_id, 'second_featured_img', $_POST['second_featured_img'] );
        }
        if ( isset( $_POST['lp_template'] ) ) {
            update_post_meta( $post_id, 'lp_template', $_POST['lp_template'] );
        }
        return $post_id;
    }

    // for testimonails
    function ts_link_function($post) {
        $ts_link = get_post_meta( $post->ID, 'testimonail_link', true );
        ?>
        <input type="text" name="testimonail_link" id="testimonail_link" value="<?php echo $ts_link;?>" size="60" />
        <?php 
    }

    function ts_role_function($post) {
        $ts_role = get_post_meta( $post->ID, 'testimonail_role', true );
        ?>
        <input type="text" name="testimonail_role" id="testimonail_role" value="<?php echo esc_html($ts_role);?>" size="60" />
        <?php 
    }

    // for landing pages
    function lp_sub_title($post) {
        $lp_subtitle = get_post_meta( $post->ID, 'lp_subtitle', true );
        ?>
        <input type="text" name="lp_subtitle" id="lp_subtitle" value="<?php echo esc_html($lp_subtitle);?>" size="60" />
        <?php 
    }

    function lp_phone($post) {
        $lp_phone = get_post_meta( $post->ID, 'lp_phone', true );
        ?>
        <input type="text" name="lp_phone" id="lp_phone" value="<?php echo esc_html($lp_phone);?>" size="60" />
        <?php 
    }

    function lp_external_link($post) {
        $lp_external_link = get_post_meta( $post->ID, 'lp_external_link', true );
        ?>
        <input type="text" name="lp_external_link" id="lp_external_link" value="<?php echo $lp_external_link;?>" size="60" />
        <?php 
    }

    function lp_page_form($post) {
        global $wpdb;
        if (! is_plugin_active( 'visual-form-builder/visual-form-builder.php' ) && ! is_plugin_active(  'contact-form-7/wp-contact-form-7.php' )) {
            _e('You must install Contact form 7 or Visual form builder in order to attach forms to landing pages', 'FatFishLP');
            return $post;
        }
        $lp_page_form = get_post_meta( $post->ID, 'lp_page_form', true );
        $forms_ids = array();
        $args = array(
            'posts_per_page' => -1,
            'post_type'      => array('wpcf7_contact_form')
        );
        $forms = get_posts( $args );
        if($forms) {
            foreach($forms as $k => $v) {
                $forms_ids[$v->post_title]['id'] = $v->ID;
                $forms_ids[$v->post_title]['provider'] = 'cf7';
            }
        }
        if (is_plugin_active( 'visual-form-builder/visual-form-builder.php' )) {
            $order = sanitize_sql_orderby( 'form_id ASC' );
            $form_table_name = $wpdb->prefix . 'visual_form_builder_forms';
            $forms = $wpdb->get_results( "SELECT form_id, form_title FROM $form_table_name ORDER BY $order" );
            foreach( $forms as $form ) {
                $forms_ids[$form->form_title]['id'] = $form->form_id;
                $forms_ids[$form->form_title]['provider'] = 'vfb';
            }
        }
        ?>
        <select name="lp_page_form" id="lp_page_form">
            <option value=""><?php _e('Select Form', 'FatFishLP'); ?></option>
            <?php
                foreach ($forms_ids as $post_title => $form_id) {
                    if ($form_id['provider'] == 'cf7') {
                        echo '<option value="[contact-form-7 id='.$form_id['id'].']" '.selected( $lp_page_form, '[contact-form-7 id='.$form_id['id'].']', true ).'>'.$post_title.'</option>';
                    } else if ($form_id['provider'] == 'vfb') {
                        echo '<option value="[vfb id='.$form_id['id'].']" '.selected( $lp_page_form, '[vfb id='.$form_id['id'].']', true ).'>'.$post_title.'</option>';
                    }
                }
            ?>
        </select>
        <?php 
    }

    function lp_show_testimonials($post) {
        $lp_show_testimonials = get_post_meta( $post->ID, 'lp_show_testimonials', true );
        ?>
        <input value="yes" type="checkbox" name="lp_show_testimonials" id="lp_show_testimonials" <?php checked( $lp_show_testimonials, 'yes', true ); ?>>
        <?php 
    }

    function lp_testimonials_list($post) {
        if (! taxonomy_exists( 'testimonials_cat' ) ) {
            _e('You must activate testimonials category taxonomy in order to choose this option', 'FatFishLP');
            return $post;
        }

        $lp_testimonials_list = get_post_meta( $post->ID, 'lp_testimonials_list', true );
        $terms_ids = array();
        $terms = get_terms( 'testimonials_cat', array(
            'hide_empty' => false,
        ) );

        if (is_wp_error( $terms )) {
            _e('No testimonials category found, first create some terms and try again.', 'FatFishLP');
            return $post;
        }

        foreach ($terms as $term) {
            $terms_ids[$term->name] = $term->term_id;
        }
        
        foreach ($terms_ids as $term_name => $term_id) {
            echo '<label for=""><input id="lp_testimonials_list" name="lp_testimonials_list" type="radio" value="'.$term_id.'" '.checked($lp_testimonials_list, $term_id, false).'>'.$term_name.'</label><br>';
        } 
    }

    function lp_template($post) {
        $lp_template = get_post_meta( $post->ID, 'lp_template', true ) !== "" ? get_post_meta( $post->ID, 'lp_template', true ) : "";
        require __DIR__.'/inc/template.php';
        $templates = array();

        $templates[] = new LP_Template("regular", "Regular", plugin_dir_url( __FILE__ ).'/images/regular.svg');
        $templates[] = new LP_Template("inverse", "Inverse", plugin_dir_url( __FILE__ ).'/images/regular.svg');
        $templates[] = new LP_Template("wide_content", "Wide Contnet", plugin_dir_url( __FILE__ ).'/images/wide-content.svg');
        $templates[] = new LP_Template("top_to_bottom", "Top to Bottom", plugin_dir_url( __FILE__ ).'/images/regular.svg');

        foreach($templates as $template) :
        ?>
            <label for="<?php echo $template->id; ?>">
                <div style="float: left;width: 25%;">
                    <span><?php echo $template->name; ?></span>
                    <input type="radio" value="<?php echo $template->id; ?>" name="lp_template" id="<?php echo $template->id; ?>" <?php checked($lp_template, $template->id, true); ?>>
                    <img style="vertical-align:middle;max-width:100px;" src="<?php echo $template->image; ?>" alt="">
                </div>
            </label>
        <?php endforeach; ?>
        <div class="clear"></div>
        <?php
    }

    function lp_mobile_image( $post ) {
        $meta_key = 'second_featured_img';
        echo $this->lp_image_uploader_field( $meta_key, get_post_meta($post->ID, $meta_key, true) );
    }

    function lp_image_uploader_field( $name, $value = '') {
        $image = ' button">Upload image';
        $image_size = 'thumbnail';
        $display = 'none'; // display state ot the "Remove image" button
    
        if( $image_attributes = wp_get_attachment_image_src( $value, $image_size ) ) {
            $image = '"><img src="' . $image_attributes[0] . '" style="max-width:95%;display:block;" />';
            $display = 'inline-block';
        } 
    
        return '
        <div>
            <a href="#" class="lp_upload_image_button' . $image . '</a>
            <input type="hidden" name="' . $name . '" id="' . $name . '" value="' . $value . '" />
            <a href="#" class="lp_remove_image_button" style="display:inline-block;display:' . $display . '">Remove image</a>
        </div>';
    }

    function add_boxes() {
        add_meta_box( 'mobile-image', __('Mobile Image', 'FatFishLP'), array($this, 'lp_mobile_image'),'lp','normal', 'high' );
        add_meta_box( 'lp-subtitle-meta', __('Subtitle', 'FatFishLP'), array($this, 'lp_sub_title'), 'lp', 'normal', 'high' );
        add_meta_box( 'lp-phone-meta', __('Phone', 'FatFishLP'), array($this, 'lp_phone'), 'lp', 'normal', 'high' );
        add_meta_box( 'lp-external-link-meta', __('External Link', 'FatFishLP'), array($this, 'lp_external_link'), 'lp', 'normal', 'high' );
        add_meta_box( 'lp-form-meta', __('Form for the page', 'FatFishLP'), array($this, 'lp_page_form'), 'lp', 'normal', 'high' );
        add_meta_box( 'lp-show-testimonials-meta', __('Show Testimonials?', 'FatFishLP'), array($this, 'lp_show_testimonials'), 'lp', 'normal', 'high' );
        add_meta_box( 'lp-list-testimonials-meta', __('Testimonials Category', 'FatFishLP'), array($this, 'lp_testimonials_list'), 'lp', 'normal', 'high' );
        add_meta_box( 'lp-template', __('Choose Template', 'FatFishLP'), array($this, 'lp_template'), 'lp', 'normal', 'high' );

        // for testimonials
        add_meta_box( 'ts-link-meta', 'Website Link', array($this, 'ts_link_function'), 'testimonials', 'normal', 'high' );
        add_meta_box( 'ts-role-meta', 'Role or Title', array($this, 'ts_role_function'), 'testimonials', 'normal', 'high' );
    }

    function display_testimonial_slider($atts) {
        ob_start();
        $a = shortcode_atts( array(
        'cat' => '',
        ), $atts );
        $data =  get_option( 'testimonials_settings' );
        $args = array(
            'post_type' => 'testimonials',
            'posts_per_page' => -1,
            'tax_query' => array(
                array(
                    'taxonomy' => 'testimonials_cat',
                    'field'    => 'slug',
                    'terms'    => $a['cat'],
                )
            ),
        );
        ?>
        <script type="text/javascript">
            jQuery(document).ready(function() {
        
            jQuery('.testimonials-slider').bxSlider({
                    minSlides: 1,
                    maxSlides: 1,
                    slideMargin: 10,
                    auto: <?php echo isset($data['auto']) ? $data['auto'] : "true";?>,
                    pager:<?php echo isset($data['pager']) ? $data['pager'] : "true";?>,				
                    adaptiveHeight:true,
                    controls:<?php echo isset($data['controls']) ? $data['controls'] : "true";?>,
                    autoControls: false,
                    speed:<?php echo ((isset($data['speed'])) and (!empty($data['speed']))) ? $data['speed'] : "500";?>,
                    mode:'<?php echo isset($data['mode']) ? $data['mode'] : "horizontal";?>',
                    randomStart:<?php echo isset($data['randomstart']) ? $data['randomstart'] : "false";?>
                    });
        });
        </script>
        <?php
        // We create our html in the result variable
        echo '<ul class="tslider testimonials-slider">';

        $the_query = new WP_Query($args);

        if ( !$the_query->have_posts() ) {
            ob_clean();
            if (current_user_can( 'manage_options' )) {
                return '<pre style="font-size: 12px;text-align: left;">If you see this it\'s because you are an Admin, and you don\'t have testimonails in the selected testimonials category.</pre>';
            } else {
                return '';
            }
        }
        // Creating a new side loop
        while ( $the_query->have_posts() ) : $the_query->the_post();
            $post_id = get_the_ID();
            $client_name_value = get_post_meta($post_id, 'Client Name', true);
            $link_value = get_post_meta( $post_id, 'testimonail_link', true );
            $ts_role = get_post_meta( $post_id, '_role', true );
            $url = has_post_thumbnail() ? get_the_post_thumbnail_url($post_id, 'medium') : plugin_dir_url( __FILE__ ).'/images/default_testimonial.png';

            echo '<li>'.
                    '<div class="cbp-qtcontent">'.
                        '<img  src="'.$url.'" />'.

                        '<blockquote>'.
                            '<p>'.apply_filters('the_content',get_the_content()).'</p>'.
                        '</blockquote>'.
                        '<footer>
                            <a href="'.($link_value).'" >'.get_the_title().'</a>
                        </footer>'.
                    '</div>'.
                '</li>';

        endwhile;
        echo '</ul>';

        return ob_get_clean();
    }

    function lp_register_scripts() {
        wp_register_script('testimonials_slide_js', plugins_url('/js/jquery.bxslider.min.js', __FILE__), array('jquery') );	
        wp_enqueue_script('testimonials_slide_js');
    }

    function lp_register_styles() {
        wp_register_style('testimonials_bxslider_css', plugins_url('/css/jquery.bxslider.css', __FILE__));		
        wp_enqueue_style( 'testimonials_bxslider_css' );
        wp_enqueue_style( 'lp_style', plugins_url('/css/lp-style.css', __FILE__) );
    }

    function lp_include_adminscript() {
        if ( ! did_action( 'wp_enqueue_media' ) ) {
            wp_enqueue_media();
        }
    
        wp_enqueue_script( 'myuploadscript', plugins_url('/js/lp-admin.js', __FILE__), array('jquery'), null, false );
        wp_enqueue_style( 'lp_admin_style', plugins_url('/css/lp-admin.css', __FILE__));
    }

    function add_css_to_footer() {
        $css = get_option( 'landing_page_settings_css', "" );
        echo '<style>'.$css['custom_css'].'</style>';
    }

    function plugin_init() {
        load_plugin_textdomain( 'FatFishLP', false, basename(  __DIR__  ) . '/languages' );
    }
}

new FatFishLP;

