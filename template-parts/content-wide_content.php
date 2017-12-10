<div class="col-sm-12">
    <div class="newlogoplace">
        <?php if ($lp_external_link) : ?> 
        <a href="<?php echo $lp_external_link; ?>" target="_blank">
            <img src="<?php echo $custom_logo; ?>" alt="logo 2016" width="202" height="106">
        </a>
        <?php else : ?>
            <img src="<?php echo $custom_logo; ?>" alt="logo 2016" width="202" height="106">
        <?php endif; ?>
    </div>

    <div class="content-area">
        <h1><?php the_title(); ?><br></h1>
        <h2 class="subtitle"><?php echo $lp_subtitle; ?></h2>
        <hr class="lp-hr">
        <p class="lp-content"><?php wpautop(the_content()); ?></p>
        
    </div>
</div>

<div class="col-xs-12">
    <div class="slider-area">
        <?php
            $lp_testimonials_list = get_term_by( 'term_taxonomy_id', $lp_testimonials_list, 'testimonials_cat' );
            if ($lp_testimonials_list) {
                $testimonial_cat_slug = $lp_testimonials_list->slug;
            }
            if ($lp_show_testimonials && $lp_testimonials_list && shortcode_exists( 'show_testimonials' )) {
                echo do_shortcode( "[show_testimonials cat='".$testimonial_cat_slug."']" );
            }
        ?>
    </div>
</div>

<div class="col-sm-12">
    <div class="row">
        <div class="col-sm-6 col-sm-offset-3">
            <div class="phoneafor">
                <a href="tel:<?php echo $lp_phone; ?>">
                    <span class="phone-wrap"><i class="glyphicon glyphicon-earphone"></i><span class="seperator"></span><span class="phone"><?php echo $lp_phone; ?></span><i class="glyphicon glyphicon-triangle-left"></i></span>
                </a>
            </div>
            <?php echo $lp_page_form ? do_shortcode($lp_page_form) : $form_content; ?>   
        </div>
    </div>     
</div>



