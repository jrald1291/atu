<?php

/**

 * Template Name: Front Page

 *

 * @package WordPress

 * @subpackage Twenty_Fourteen

 * @since Twenty Fourteen 1.0

 */



get_header(); ?>



<div class="section section-banner banner" style="background: url('<?php WEPN_Helper::background_image( get_field('page_background', get_the_ID()) ); ?>') no-repeat">

    <div class="banner-content">

        <div class="banner-actions">

            <h1 class="t-md t-title">

                <?php if (of_get_option('intro', '')): ?>

                    <?php echo of_get_option('intro', ''); ?>

                <?php endif ?>

            </h1>

            <form method="post" id="mainSearch" action="<?php echo home_url('/suppliers'); ?>">

                <?php WEPN_Helper::dropwdown_cities('Select City'); ?>

                <div class="actions">

                    <input type="hidden" name="s" value="" />

                    <button type="submit" name="post_type" value="vendor" onclick='this.form.action="<?php echo home_url('/suppliers'); ?>";' class="btn btn-wooden btn-lg">Find a Supplier</button>

                    <button type="submit" name="post_type" value="venue" onclick='this.form.action="<?php echo home_url('/venue'); ?>";'  class="btn btn-wooden btn-lg">Find a Venue</button>

                </div>

            </form>

        </div>

        <?php if (get_field('banner_slogan') and get_field('slogan_line2')):?>

            <div class="banner-intro">

                <h2 class="t-title t-huge"><?php the_field('banner_slogan'); ?></h2>

                <h3 class="t-title t-md"><?php the_field('slogan_line2'); ?></h3>

            </div>

        <?php endif ?>

    </div>

    <a href="#scroll-target" class="t-upper scroll-down link scroll_to">Scroll Down</a>

</div>

<?php if (of_get_option('rsvp_text', '') and of_get_option('rsvp_link', '')): ?>

    <div class="section-rsvp section-dark section-l0" id="section-rsvp">

        <div class="container">

            <span class="t-sm mr-10"><?php echo of_get_option('rsvp_text', ''); ?></span> <a href="<?php echo of_get_option('rsvp_link', ''); ?>" target="_blank" class="btn btn-md btn-secondary">Meeting RSVP</a>

        </div>

    </div>

<?php endif ?>

<?php if (get_field('intro_text') and get_field('intro_image')):?>

    <div id="scroll-target" class="section section-l4">

        <div class="container"> 

            <div class="col-sm-6 col-sm-push-6">

                <div class="well-img-bordered mb-20">

                    <?php $intro_img = wp_get_attachment_image_src(get_field('intro_image'),'img-lscape'); ?>

                    <img src="<?php echo $intro_img[0];?>"/>

                </div>

                <?php if (of_get_option('video_diff', '')) {?>

                    <a href="#main_vid" role="button" data-toggle="modal" class="btn btn-primary btn-lg btn-block mb-20"><span class="fa fa-play-circle icon-l"></span> Watch our Video</a>

                <?php } ?>



            </div>

            <div class="col-sm-6 col-sm-pull-6">

                <div class="t-title t-lg text-right t-reset">

                  <?php the_field('intro_text'); ?>

                </div>

            </div>

        </div>

    </div>

    <div class="section section-dark section-fl section-l3 parallax">

<?php else: ?>

    <div id="scroll-target" class="section section-dark section-fl section-l3 parallax">

<?php endif ?>

    <div class="container">

        <h2 class="t-title t-huge mb-30">What is New Today?</h2>

        <div class="post-inline post-blog post-blog-md mb-30">

            <div class="well well-transparent">

            <h4 class="title-l1">Latest Articles</h4>

             <?php 

                $paged = get_query_var('paged');

                $args = array( 'post_type' => 'post', 'posts_per_page' => 3,'paged' => $paged, 'order' => 'DESC','post_status'  => 'publish' );

                $loop = new WP_Query( $args );



                    while ( $loop->have_posts() ) : $loop->the_post();?>



                        <article id="post-<?php the_ID(); ?>" <?php post_class("post-item"); ?>>

                            <div class="post-img well-img">

                                <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('thumbnail'); ?></a>

                            </div>

                            <div class="post-core">

                                <div class="post-title t-normal"><a href="<?php the_permalink(); ?>" class="link"><?php the_title(); ?></a></div>

                                    <div class="post-meta"><div class="meta date"><?php echo get_the_date(); ?></div>
                                        <div class="meta author t-upper">
                                            <a href="<?php echo WEPN_Helper::get_user_permalink(get_the_author_meta('ID')); ?>" class="link"><?php the_author(); ?></a>
                                        </div>
                                    </div>

                                <div class="post-content">

                                    <p><?php echo content(strip_shortcodes(wp_trim_words(get_the_content())),40) ?> <a href="<?php the_permalink(); ?>">read more</a></p>

                                </div>

                            </div>

                        </article>



                    <?php endwhile; ?>   

                <?php wp_reset_postdata(); ?>

            </div>

        </div>

        <div class="row">

            <div class="col-md-6 mb-30">

                <h4 class="title-l1">Latest Venue</h4>

                <?php

                /**

                 * Get latest venue

                 */

                $wp_venue_query = new WP_Query( array( 'post_type' => 'venue', 'orderby' => 'date', 'order' => 'desc', 'post_status' => 'publish', 'posts_per_page' => -1 ) );

                ?>

                <?php if ( $wp_venue_query->have_posts(  ) ): ?>

                    <div class="slider slider-l1 mb-20">

                        <div class="slider-venue slider-capt flexslider mb-0">



                                <ul class="slides">

                                <?php while( $wp_venue_query->have_posts() ): $wp_venue_query->the_post();?>

                                    <li>
                                        <img src="<?php echo WEPN_Helper::venue_avatar(get_post_thumbnail_id(), 'venue-medium'); ?>" />

                                        <div class="slider-caption">

                                            <a href="<?php echo get_permalink(); ?>" class="link"><div class="slide-title"><?php the_title(); ?></div></a>

                                            <div class="slide-desc"><?php echo wp_trim_words(  get_the_content(), $num_words = 10, $more = ' <a href="'. get_permalink() .'">read more</a>' ); ?></div>

                                        </div>

                                    </li>

                                <?php endwhile; ?>

                                </ul>





                        </div>

                        <div id="carousel-venue" class="flexslider mb-0">

                         <ul class="slides">

                             <?php while( $wp_venue_query->have_posts() ): $wp_venue_query->the_post();?>

                                 <li>
                                     <img src="<?php echo WEPN_Helper::venue_avatar(get_post_thumbnail_id(), 'venue-small-thumb'); ?>" />
                                 </li>

                             <?php endwhile; ?>

                         </ul>



                        </div>

                    </div>

                <a href="<?php echo get_post_type_archive_link( 'venue' ); ?>" class="btn btn-opposite btn-block btn-md"><?php _e( 'See all venues', 'atu' ); ?></a>

                <?php else: ?>

                    <h3><?php _e( 'No Venue found.', 'atu' ); ?></h3>

                <?php endif; ?>

            </div>

            <div class="col-md-6 mb-30">

                <h4 class="title-l1"><?php _e( 'Latest Supplier List', 'atu' ); ?> </h4>



                <?php

                /**

                 * Get latest Supplier

                 */



                $wp_venue_query = new WP_Query( array(

                    'post_type' => 'vendor',

                    'meta_key' => 'vendor',

                    'orderby' => 'meta_value_num',

                    'order' => 'DESC',

                    'post_status' => 'publish',

                    'posts_per_page' => 4,

                    'meta_query' => array(

                        array(

                            'key' => 'vendor', // name of custom field

                            'value' => WEPN_Helper::get_user_ids_by_role('vendor'), // matches exaclty "red", not just red. This prevents a match for "acquired"

                            'compare' => 'IN'

                        )

                    )) );





                if ( $wp_venue_query->have_posts() ): ?>



                    <ul class="post-inline post-member mb-20">



                        <?php while( $wp_venue_query->have_posts() ): $wp_venue_query->the_post();



                            $user_id = get_post_meta( get_the_ID(), 'vendor', true );



                            $main_cat = get_post_meta( get_the_ID(), 'category', true );

                            $taxonomy = get_user_meta($user_id, 'city', true);

                            $cat = get_term_by( 'slug', $main_cat, $taxonomy );



                            $cat_name = !is_wp_error($cat) ? $cat->name : '';



                            $vendor_info = $user_id;



                            $description = wp_trim_words(  get_user_meta( $user_id, 'description', true ), $num_words = 16, $more = '...' );

                            $image_id = get_user_meta( $user_id, 'profile_image', true );

                            $company_name = get_user_meta( $user_id, 'company_name', true );



                            ?>

                            <li class="post-item">

                                <div class="post-img well-img">

                                    <a href="<?php the_permalink(); ?>">
                                        <img src="<?php echo WEPN_Helper::supplier_avatar($image_id, 'vendor-small-thumb'); ?>" />
                                    </a>

                                </div>

                                <div class="post-core">

                                    <a href="<?php the_permalink() ?>" class="link">

                                        <div class="post-title t-normal"><?php the_title() ?>

                                            <span class="post-cat t-highlight"><?php echo $cat_name; ?></span>

                                        </div>

                                    </a>

                                    <p><?php echo $description; ?>.</p>

                                </div>

                            </li>

                        <?php endwhile; wp_reset_query();?>

                    </ul>

                    <a href="<?php echo home_url('/suppliers/') ?>" class="btn btn-opposite btn-block btn-md"><?php _e( 'See all Suppliers', 'atu'); ?></a>

                <?php else: ?>



                    <h3><?php _e( 'No Suppliers found.', 'atu' ); ?></h3>



                <?php endif; ?>



            </div>

            

        </div>

        

    </div>

</div>

<div class="section section-l4">

    <div class="container">

        <div class="t-md typo-lora text-center">

            <?php

            while ( have_posts() ) : the_post();

            ?>

            <?php the_content(); ?>

            <?php endwhile; ?>

        </div>



    </div>

</div>



<?php if (of_get_option('video_diff', '')) {?>

    <div id="main_vid" class="modal modal-md fade in " tabindex="-1" role="dialog" aria-labelledby="<?php echo of_get_option('video_diff_text', ''); ?>">

      <div class="modal-dialog modal-lg">

      <div class="modal-content">

              <div class="modal-header t-normal">

                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>

                  <h3 class="t-title text-center"><?php echo of_get_option('video_diff_text', ''); ?></h3>

                  </div>

              <div class="modal-body">

                  <?php echo of_get_option('video_diff', ''); ?>

              </div>

       </div>

      </div>

    </div>

<?php } ?>









<?php get_footer(); ?>

