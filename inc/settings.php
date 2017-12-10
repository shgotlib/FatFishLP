<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

if (! class_exists('LandingPageSettings')) {
    class LandingPageSettings {
        /**
         * Holds the values to be used in the fields callbacks
         */
        private static $lp_options;
        private static $test_options;
        private static $css_options;

        /**
         * Hold the option setting and the global page
         */
        private $my_option_name = 'landing_page_settings';

        /**
         * Start up
         */
        function __construct() {
            add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
            add_action( 'admin_init', array( $this, 'page_init' ) );

            // Set class property
            self::$lp_options = get_option( $this->my_option_name.'_lp'  );
            self::$test_options = get_option( $this->my_option_name.'_test'  );
            self::$css_options = get_option( $this->my_option_name.'_css'  );
        }

        /**
         * Add options page
         */
        function add_plugin_page() {
            // This page will be under "Landing page"
            add_submenu_page(
                'edit.php?post_type=lp', 
                'settings', 
                'Landing page Settings', 
                'manage_options', 
                $this->my_option_name,
                array( $this, 'create_landing_page_settings_page' ));
        }

        static function get_all_options() {
            return array(self::$lp_options, self::$test_options, self::$css_options );
        }  

        /**
         * Options page callback
         */
        function create_landing_page_settings_page() {
            ?>
            <div class="wrap lp-wrap">
                <h1>Landing Page Settings</h1> 
                <h2 class="nav-tab-wrapper">
            		&nbsp;
		            <a class="nav-tab <?php echo isset($_GET['lp']) && $_GET['lp'] == 'landing' ? 'nav-tab-active' : ''; ?>" style="" href="edit.php?post_type=lp&page=landing_page_settings&lp=landing">Landing page Settings</a>
                    <a class="nav-tab <?php echo isset($_GET['lp']) && $_GET['lp'] == 'testimonials' ? 'nav-tab-active' : ''; ?>" style="" href="edit.php?post_type=lp&page=landing_page_settings&lp=testimonials">Testimonials Settings</a>
                    <a class="nav-tab <?php echo isset($_GET['lp']) && $_GET['lp'] == 'custom_css' ? 'nav-tab-active' : ''; ?>" style="" href="edit.php?post_type=lp&page=landing_page_settings&lp=custom_css">Custom CSS</a>
                </h2>    
                <form method="post" action="options.php">
                <?php
                    if (isset($_GET['lp']) && $_GET['lp'] == 'landing') {
                        settings_fields( $this->my_option_name.'_lp' );
                        do_settings_sections( $this->my_option_name.'_lp' );
                    } else if (isset($_GET['lp']) && $_GET['lp'] == 'testimonials') {
                        settings_fields( $this->my_option_name.'_test' );
                        do_settings_sections( $this->my_option_name.'_test' );
                    } else if (isset($_GET['lp']) && $_GET['lp'] == 'custom_css') {
                        settings_fields( $this->my_option_name.'_css' );
                        do_settings_sections( $this->my_option_name.'_css' );
                    }
                    submit_button();
                ?>
                </form>
            </div>
            <?php
        }

        /**
         * Register and add settings
         */
        function page_init() {        
            register_setting( $this->my_option_name.'_lp', $this->my_option_name.'_lp', array( $this, 'lp_sanitize' ) );
            register_setting( $this->my_option_name.'_test', $this->my_option_name.'_test', array( $this, 'test_sanitize' ) );
            register_setting( $this->my_option_name.'_css', $this->my_option_name.'_css', array( $this, 'css_sanitize' ) );

            add_settings_section( 'setting_section_lp', '', array( $this, 'print_section_info' ), $this->my_option_name.'_lp' );  
            add_settings_section('setting_section_test', '', array( $this, 'print_section_info' ), $this->my_option_name.'_test' );  
            add_settings_section( 'setting_section_css', '', array( $this, 'print_css_section_info' ), $this->my_option_name.'_css' );

            add_settings_field( 'page_structure', 'Pages structure', array($this, 'page_structure'), $this->my_option_name.'_lp', 'setting_section_lp' );
            add_settings_field( 'cusotm_logo', 'Main Logo', array($this, 'change_logo'), $this->my_option_name.'_lp', 'setting_section_lp' );
            add_settings_field( 'text_instead_form', 'Text instead of contact form', array($this, 'text_instead_form'), $this->my_option_name.'_lp', 'setting_section_lp' );
            add_settings_field( 'form_provider', 'Choose contact form provider', array($this, 'form_provider'), $this->my_option_name.'_lp', 'setting_section_lp' );

            add_settings_field( 'custom_css', 'Custom CSS', array($this, 'custom_css_lp'), $this->my_option_name.'_css', 'setting_section_css' );

            add_settings_field( 'mode', 'Mode', array( $this, 'mode_callback' ), $this->my_option_name.'_test', 'setting_section_test' );  
            add_settings_field( 'speed', 'Speed', array( $this, 'speed_callback' ), $this->my_option_name.'_test', 'setting_section_test' );      
            add_settings_field( 'controls', 'Controls', array( $this, 'controls_callback' ), $this->my_option_name.'_test', 'setting_section_test' );    
            add_settings_field( 'pager', 'Pager', array( $this, 'pager_callback' ), $this->my_option_name.'_test', 'setting_section_test' );  
            add_settings_field( 'randomstart', 'Random Start', array( $this, 'randomstart_callback' ), $this->my_option_name.'_test', 'setting_section_test' );    
            add_settings_field( 'auto', 'Auto', array( $this, 'auto_callback' ), $this->my_option_name.'_test', 'setting_section_test', 'true' );     
        }

        function print_css_section_info() {
            echo "<p class='description'>".__('Here you can insert your custom CSS for the pages', 'FatFishLP')."</p>";
        }

        /**
         * Sanitize each setting field as needed
         *
         * @param array $input Contains all settings fields as array keys
         */
        function test_sanitize( $input ) {
            $new_input = array();
            if( isset( $input['mode'] ) )
                $new_input['mode'] = sanitize_text_field( $input['mode'] );

            if( isset( $input['speed'] ) )
                $new_input['speed'] = sanitize_text_field( $input['speed'] );
                
            if( isset( $input['controls'] ) )
                $new_input['controls'] = sanitize_text_field( $input['controls'] );
            else
                $new_input['controls'] = 'false';	
            
            if( isset( $input['pager'] ) )
                $new_input['pager'] = sanitize_text_field( $input['pager'] );
            else
                $new_input['pager'] = 'false';	
                
            if( isset( $input['randomstart'] ) )
                $new_input['randomstart'] = sanitize_text_field( $input['randomstart'] );
            else
                $new_input['randomstart'] = 'false';
                
            if( isset( $input['auto'] ) )
                $new_input['auto'] = sanitize_text_field( $input['auto'] );
            else
                $new_input['auto'] = 'false';			

            return $new_input;
        }

        function lp_sanitize( $input ) {
            $new_input = array();
            if( isset( $input['cusotm_logo'] ) )
                $new_input['cusotm_logo'] = $input['cusotm_logo'];
            if( isset( $input['text_instead_form'] ) )
                $new_input['text_instead_form'] = sanitize_text_field( $input['text_instead_form'] );
            if( isset( $input['layout'] ) )
                $new_input['layout'] = $input['layout'];
            if( isset( $input['form_provider'] ) )
                $new_input['form_provider'] = $input['form_provider'];

            return $new_input;
        }

        function css_sanitize( $input ) {
            $new_input = array();
            if( isset( $input['custom_css'] ) )
                $new_input['custom_css'] = wp_kses_post($input['custom_css']);

            return $new_input;
        }

        /** 
         * Print the Section text
         */
        function print_section_info() {
            echo '<p class="description">'.__('Enter here some settings', 'FatFishLP').'</p>';
        }

        /**
         * Get the custom CSS and display it in the wysiwyg area
         */
        function custom_css_lp() { 
            $content = isset(self::$css_options['custom_css']) ? self::$css_options['custom_css'] : esc_html("/* Enter here some custom CSS */");

            $html = '<textarea placeholder="/* Your CSS here */" style="text-align:left;direction:ltr;" name="'.$this->my_option_name.'_css[custom_css]" id="custom_css" cols="50" rows="20">'.$content.'</textarea>';
            
            echo $html;
        }

        function page_structure() {
            $layout = isset(self::$lp_options['layout']) ? self::$lp_options['layout'] : "boxed"; 
            $html = '<label for="boxed"><input value="boxed" id="boxed" name="'.$this->my_option_name.'_lp[layout]" type="radio" '.checked( $layout, "boxed", false ).'>Boxed</label><br>';
            $html .= '<label for="container"><input value="container" id="container" name="'.$this->my_option_name.'_lp[layout]" type="radio" '.checked( $layout, "container", false ).'>Container</label>';

            $html .= '<p class="description">'.__("Choose if you want a boxed template or container template layout.", "FatFishLP").'</p>';


            echo $html;
        }

        function form_provider() {
            $html = '<label for="form_provider_cf7"><input type="checkbox" id="form_provider_cf7" name="'.$this->my_option_name.'_lp[form_provider][cf7]" value="1" '.checked( isset(self::$lp_options['form_provider']['cf7']) && self::$lp_options['form_provider']['cf7'], '1', false).'/>Contact Form 7</label><br>';
            $html .= '<label for="form_provider_vfb"><input type="checkbox" id="form_provider_vfb" name="'.$this->my_option_name.'_lp[form_provider][vfb]" value="1" '.checked( isset(self::$lp_options['form_provider']['vfb']) && self::$lp_options['form_provider']['vfb'], '1', false).'/>Visual Form Builder</label>';

            echo $html;
        }

        /**
         * Textarea for user default message when form ommitted
         */
        function text_instead_form() {
            $content = isset(self::$lp_options['text_instead_form']) ? self::$lp_options['text_instead_form'] : ""; 
            $html = '<textarea placeholder="Call us to +555-555-5555" name="'.$this->my_option_name.'_lp[text_instead_form]" id="text_instead_form" cols="30" rows="20">'.$content.'</textarea>';
            $html .= '<p class="description">Insert a defalut text in pages without a contact form.</p>';

            echo $html;
        }

        /**
         * Default logo field
         */
        function change_logo() {
            $image = ' button">Upload image';
            $image_size = 'thumbnail';
            $display = 'none';
            $image_id = 0;
            if( $image_id = self::$lp_options['cusotm_logo'] ) {
                $image_src = wp_get_attachment_image_src( $image_id, $image_size );
                $image = '"><img src="' . $image_src[0]. '" style="max-width:95%;display:block;" />';
                $display = 'inline-block';
            }
            echo '
            <div>
                <a href="#" class="lp_upload_image_button' . $image . '</a>
                <input type="hidden" name="'.$this->my_option_name.'_lp[cusotm_logo]" id="cusotm_logo" value="'.$image_id.'" />
                <a href="#" class="lp_remove_image_button" style="display:inline-block;display:' . $display . '">Remove image</a>
            </div>';
        }

        /** 
         * Get the settings option array and print one of its values
         */
        function mode_callback() {
            $mode = (isset(self::$test_options['mode']) and (!empty(self::$test_options['mode']))) ? esc_attr( self::$test_options['mode']) : "fade";
            $html = '<select id="mode" name="'.$this->my_option_name.'_test[mode]">';
                $html .= '<option value="horizontal"' . selected( $mode, 'horizontal', false) . '>Horizontal</option>';
                $html .= '<option value="vertical"' . selected( $mode, 'vertical', false) . '>Vertical</option>';
                $html .= '<option value="fade"' . selected( $mode, 'fade', false) . '>Fade</option>';
            $html .= '</select>';
            $html .= '<p class="description">Type of transition between slides</p>';
            
            echo $html;
        }
        
        /** 
         * Get the settings option array and print one of its values
         */
        function speed_callback() {
            $speed = (isset(self::$test_options['speed']) and (!empty(self::$test_options['speed']))) ? esc_attr( self::$test_options['speed']) : "500";
            $html = '<input type="text" id="speed" name="'.$this->my_option_name.'_test[speed]" value="'.$speed.'"/>';
            $html .= '<p class="description">Slide transition duration (in ms) (default:500)</p>';
            
            echo $html;
        }

        /** 
         * Get the settings option array and print one of its values
         */
        function controls_callback() {
            $html = '<input type="checkbox" id="controls" name="'.$this->my_option_name.'_test[controls]" value="true" '.checked( self::$test_options['controls'], 'true', false).'/>';
            $html .= '<p class="description">If true, "Next" / "Prev" controls will be added</p>';
            
            echo $html;
        }
        
        /** 
         * Get the settings option array and print one of its values
         */
        function pager_callback() {
            $html = '<input type="checkbox" id="pager" name="'.$this->my_option_name.'_test[pager]" value="true" '.checked( self::$test_options['pager'], 'true', false).'/>';
            $html .= '<p class="description">If true, a pager will be added</p>';
            
            echo $html;
        }
        
        function randomstart_callback() {
            $html = '<input type="checkbox" id="randomstart" name="'.$this->my_option_name.'_test[randomstart]" value="true" '.checked( self::$test_options['randomstart'], 'true', false).'/>';
            $html .= '<p class="description">Start Slider on a Random Slide</p>';
            
            echo $html;
        }
        function auto_callback($arg) {
            $auto = isset(self::$test_options['auto']) ? self::$test_options['auto'] : "true";
            $html = '<input type="checkbox" id="auto" name="'.$this->my_option_name.'_test[auto]" value="true" '.checked($auto , 'true', false).'/>';
            $html .= '<p class="description">Slides will automatically transition</p>';
            
            echo $html;
        }
    }
}

if( is_admin() ) {
    $my_settings_page = new LandingPageSettings();
}