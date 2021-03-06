<?php



get_header(); ?>



<?php if ( have_posts() ): while( have_posts() ): the_post();?>



<div class="l-content-bg" style="background: url('<?php WEPN_Helper::background_image( get_field( 'page_background', get_the_ID() ) ); ?>') no-repeat">

    <div class="container">

        <div class="row">

            <div class="col-md-9">

                <div class="l-content-container">



                    <div class="page-header">



                        <?php do_action( 'wepn_venue_search_form' ); ?>


                    </div>
                    <div class="pagination-single pagination-single-sm">
                        <ul>
                            <li class="next">
                                <?php $nepo=get_next_post(); 
                                if ($nepo) {
                                        $nepoid=$nepo->ID;
                                        $ne_post_url = get_permalink($nepoid);?>
                                        <a href="<?php echo $ne_post_url; ?>">
                                            <span class="label"><i class="fa fa-angle-left icon-l"></i>Previous Venue</span>
                                        </a>
                                <?php }else{?>
                                    <div class="disabled">
                                            <span class="label"><i class="fa fa-angle-left icon-l"></i>No previous Venue</span>
                                    </div>
                                <?php } ?>
                            </li>
                            <li class="back">
                                <a href="<?php echo home_url( '/venue/' ) ?>"><span class="label">back to Venue Listing</span></a>
                            </li>
                            <li class="prev">
                                <?php $prepo=get_previous_post(); 
                                if ($prepo) {
                                        $prepoid=$prepo->ID;
                                        $pre_post_url = get_permalink($prepoid);?>
                                        <a href="<?php echo $pre_post_url; ?>">
                                            <span class="label">Next Venue<i class="fa fa-angle-right icon-r"></i></span>
                                        </a>
                                <?php }else{?>
                                    <div class="disabled">
                                            <span class="label">No Next Venue<i class="fa fa-angle-right icon-r"></i></span>
                                    </div>
                                <?php } ?>
                            </li>
                        </ul>
                    </div>
                    <div class="page-content">

                        <div class="page-title ">

                            <h2 class="t-lg"><?php the_field( 'company_name' ) ?></h2>

                        </div>

                        <div class="slider mb-20">

                            <div class="slider-single slider-capt flexslider mb-0">

                                <?php if ( have_rows( 'gallery' ) ): ?>

                                    <ul class="slides">



                                        <?php  $i = 0; while( have_rows( 'gallery' ) ): the_row(); ?>



                                            <li>

                                                    <?php

                                                    /**

                                                     * Get gallery image

                                                     */

                                                    echo wp_get_attachment_image( get_sub_field( 'gallery_image' ), 'slide-wide', array( 'alt' => 'image' ) );

                                                    ?>

                                            </li>



                                        <?php

                                            if ( $i++ == 5 )

                                                break;

                                        endwhile; reset_rows();?>


                                    </ul>



                                <?php else: ?>



                                    <?php _e( 'No Gallery found', 'atu' ); ?>



                                <?php endif; ?>



                            </div>

                        </div>

                        <div class="section section-tabbed">

                            <ul class="nav nav-tabs" role="tablist">

                                <li role="presentation" class="active"><a href="#description" aria-controls="home" role="tab" data-toggle="tab"><?php _e( 'Description', 'atu'); ?></a></li>

                                <li role="presentation"><a href="#gallery" aria-controls="gallery" role="tab" data-toggle="tab"><?php _e( 'Gallery', 'atu'); ?></a></li>

                                <li role="presentation"><a href="#youtube" aria-controls="youtube" role="tab" data-toggle="tab"><?php _e( 'Videos', 'atu'); ?></a></li>

                                <li role="presentation"><a href="#offers" aria-controls="offers" role="tab" data-toggle="tab"><?php _e( 'Special Offer', 'atu'); ?></a></li>

                                <li role="presentation"><a href="#map" aria-controls="map" role="tab" data-toggle="tab"><?php _e( 'Map', 'atu'); ?></a></li>

                                <li role="presentation"><a href="#ratings" aria-controls="ratings" role="tab" data-toggle="tab">Rating</a></li>

                                <li role="presentation"><a href="#video" aria-controls="video" role="tab" data-toggle="tab"><?php _e( 'WEPN Venue Benefits', 'atu'); ?></a></li>

                            </ul>

                            <div class="tab-content">
                                <div role="tabpanel" id="ratings" class="tab-pane">
                                    <?php echo do_shortcode('[cbratingsystem form_id="1"]'); ?>
                                </div>
                                <div role="tabpanel" class="tab-pane active copy" id="description">

                                    <?php the_content() ?>

                                    <p>Address: <?php the_field( 'address' ); ?></p>

                                    <p>Postcode: <?php the_field( 'post_code' ); ?></p>

                                    <p>Service Area: <?php the_field( 'service_area' ); ?></p>

                                    <p>Business Hours: <?php the_field( 'business_hours' ); ?></p>

                                    <p>Capacity: <?php the_field( 'capacity' ); ?></p>



                                </div>

                                <div role="tabpanel" class="tab-pane" id="gallery">

                                    <div id="grid-gallery" class="grid-gallery">

                                        <section class="grid-wrap">

                                            <?php if ( have_rows( 'gallery' ) ): ?>

                                            <ul class="grid">

                                                <li class="grid-sizer"></li><!-- for Masonry column width -->



                                                <?php while( have_rows( 'gallery' ) ): the_row();?>



                                                    <li>

                                                        <figure>



                                                            <?php

                                                            /**

                                                             * Get gallery image

                                                             */

                                                            echo wp_get_attachment_image( get_sub_field( 'gallery_image' ), 'gallery-thumb', array( 'alt' => 'image' ) );

                                                            ?>

                                                        </figure>

                                                    </li>



                                                <?php endwhile; ?>



                                            </ul>

                                            <?php else: ?>



                                                <?php _e( 'No Gallery found', 'atu' ); ?>



                                            <?php endif; ?>

                                        </section><!-- // grid-wrap -->



                                        <?php if ( have_rows( 'gallery' ) ): ?>

                                        <section class="slideshow">

                                            <ul>



                                                <?php while( have_rows( 'gallery' ) ): the_row();?>

                                                    <li>

                                                        <figure>

                                                            <?php if (get_sub_field( 'gallery_title' ) && get_sub_field( 'gallery_description' ) ) {?>

                                                                 <figcaption>

                                                                    <h3><?php the_sub_field( 'gallery_title' ); ?></h3>

                                                                    <p><?php the_sub_field( 'gallery_description' ); ?></p>

                                                                </figcaption>

                                                            <?php } ?>

                                                           

                                                            <?php

                                                            /**

                                                             * Get gallery image

                                                             */

                                                            echo wp_get_attachment_image( get_sub_field( 'gallery_image' ), 'large', array( 'alt' => 'image' ) );

                                                            ?>

                                                        </figure>

                                                    </li>



                                                <?php endwhile; ?>



                                            </ul>

                                            <nav>

                                                <span class="icon nav-prev"></span>

                                                <span class="icon nav-next"></span>

                                                <span class="icon nav-close"></span>

                                            </nav>

                                            <div class="info-keys icon">Navigate with arrow keys</div>

                                        </section>



                                        <?php endif; ?>

                                    </div><!-- // grid-gallery -->

                                </div>

                                <div role="tabpanel" class="tab-pane" id="youtube">

                                    <?php the_field( 'youtube_iframe' ); ?>

                                </div>

                                <div role="tabpanel" class="tab-pane" id="offers">

                                    <?php if ( have_rows( 'special_offer' ) ): ?>

                                    <ul class="post-inline post-member mb-20">

                                        <?php while( have_rows( 'special_offer' ) ):the_row();  ?>

                                        <li class="post-item">

                                            <div class="post-img well-img">

                                                <img src="<?php echo get_template_directory_uri() ?>/images/placeholders/pdf.jpg" alt="">

                                            </div>

                                            <div class="post-core">

                                                <a href="<?php echo get_sub_field( 'special_offer_pdf', '#' ); ?>" target="_blank" class="link"><div class="post-title t-normal"><?php the_sub_field( 'special_offer_title' ); ?> <span class="post-cat t-highlight">Download PDF</span></div></a>

                                                <p><?php the_sub_field( 'special_offer_description' ); ?></p>

                                            </div>

                                        </li>

                                        <?php endwhile; ?>

                                    </ul>



                                    <?php else: ?>



                                        <?php _e( 'No Special offer found', 'atu' ); ?>



                                    <?php endif; ?>

                                </div>

                                <div role="tabpanel" class="tab-pane" id="map">

                                    <?php



                                    $location = get_field('map');



                                    if( !empty($location) ):

                                    ?>

                                    <div class="acf-map">

                                        <div class="marker" data-lat="<?php echo $location['lat']; ?>" data-lng="<?php echo $location['lng']; ?>"></div>

                                    </div>

                                    <?php endif; ?>

                                </div>

                                <div role="tabpanel" class="tab-pane" id="video">


                                    <?php if (of_get_option('video_venue', '')) {?>

                                       <?php echo of_get_option('video_venue', ''); ?>

                                    <?php } ?>

                                </div>

                            </div>

                        </div>

                        <div class="social-links list-labeled-inline bt-0">

                            <label for="">Follow us in social :</label>

                            <ul>

                                <?php if(get_field( 'instagram' ) != ''): ?>

                                <li><a href="<?php the_field( 'instagram' ); ?>" target="_blank"><span class="fa fa-instagram"></span></a></li>

                                <?php

                                endif;

                                if(get_field( 'youtube' ) != ''): ?>

                                <li><a href="<?php the_field( 'youtube' ); ?>" target="_blank"><span class="fa fa-youtube"></span></a></li>

                                <?php

                                endif;

                                if(get_field( 'google_+' ) != ''): ?>

                                <li><a href="<?php the_field( 'google_+' ); ?>" target="_blank"><span class="fa fa-google-plus"></span></a></li>

                                <?php

                                endif;

                                if(get_field( 'pinterest' ) != ''): ?>

                                <li><a href="<?php the_field( 'pinterest' ); ?>" target="_blank"><span class="fa fa-pinterest"></span></a></li>

                                <?php

                                endif;

                                if(get_field( 'twitter' ) != ''): ?>

                                <li><a href="<?php the_field( 'twitter' ); ?>" target="_blank"><span class="fa fa-twitter"></span></a></li>

                                <?php

                                endif;

                                if(get_field( 'linkedin' ) != ''): ?>

                                <li><a href="<?php the_field( 'linkedin' ); ?>" target="_blank"><span class="fa fa-linkedin"></span></a></li>

                                <?php

                                endif;

                                if(get_field( 'facebook' ) != ''): ?>

                                <li><a href="<?php the_field( 'facebook' ); ?>" target="_blank"><span class="fa fa-facebook"></span></a></li>

                                <?php

                                endif;?>

                            </ul>

                        </div>

                    </div>

                </div>

            </div>

            <div class="col-md-3">

                <?php get_sidebar(); ?>

            </div>

        </div>

    </div>

    <div class="pagination-single">

        <ul>

            <li class="next">

                <?php $nepo=get_next_post(); 


                if ($nepo) {

                        $nepoid=$nepo->ID;

                        $ne_post_url = get_permalink($nepoid);?>

                        <a href="<?php echo $ne_post_url; ?>">

                            <span class="label"><i class="fa fa-angle-left icon-l"></i>Previous</span>

                            <span><?php echo get_the_title($nepoid); ?></span>

                        </a>

                <?php }else{?>

                    <div class="disabled">

                            <span class="label"><i class="fa fa-angle-left icon-l"></i>Previous</span>

                            <span>No previous Venue</span>

                    </div>

                <?php } ?>

            </li>

            <li class="back">

                <a href="<?php echo home_url( '/venue/' ) ?>">back to Venue Listing</a>

            </li>

            <li class="prev">

                <?php $prepo=get_previous_post(); 

                if ($prepo) {

                        $prepoid=$prepo->ID;

                        $pre_post_url = get_permalink($prepoid);?>

                        <a href="<?php echo $pre_post_url; ?>">

                            <span class="label">Next<i class="fa fa-angle-right icon-r"></i></span>

                            <span><?php echo get_the_title($prepoid); ?></span>

                        </a>

                <?php }else{?>

                    <div class="disabled">

                            <span class="label">Next<i class="fa fa-angle-right icon-r"></i></span>

                            <span>No Next Venue</span>

                    </div>

                <?php } ?>

                

            </li>

        </ul>

    </div>

</div>

    <?php endwhile; ?>



<?php endif; ?>



<?php get_footer(); ?>

