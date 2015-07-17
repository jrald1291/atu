<?php
/**
 * Template Name: Front Page
 *
 * @package WordPress
 * @subpackage Twenty_Fourteen
 * @since Twenty Fourteen 1.0
 */

get_header(); ?>

<div class="section section-banner banner" style="background: url('<?php ATU_Helper::background_image( get_field('page_background', get_the_ID()) ); ?>') no-repeat">
	<div class="banner-content">
		<div class="banner-actions">
			<h1 class="t-md t-title">
				<?php if (of_get_option('intro', '')): ?>
					<?php echo of_get_option('intro', ''); ?>
				<?php endif ?>
			</h1>
			<div class="actions">
				<a href="<?php echo home_url('/vendors'); ?>" class="btn btn-wooden btn-lg">Search for Vendor</a>
				<a href="<?php echo home_url('/venue'); ?>" class="btn btn-wooden btn-lg">Search for Venue</a>
			</div>
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
<?php if (get_field('intro_text') and get_field('intro_image')):?>
	<div id="scroll-target" class="section section-l4">
		<div class="container">	
			<div class="col-sm-6 col-sm-push-6">
				<div class="well-img-bordered mb-30">
					<?php $intro_img = wp_get_attachment_image_src(get_field('intro_image'),'img-lscape'); ?>
					<img src="<?php echo $intro_img[0];?>"/>
				</div>
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
		<div class="row">
			<div class="col-md-6 mb-30">
				<h4 class="title-l1">Latest Venue</h4>
                <?php
                /**
                 * Get latest venue
                 */
                 wp_list_authors();
                $wp_venue_query = new WP_Query( array( 'post_type' => 'venue', 'orderby' => 'date', 'order' => 'desc', 'post_status' => 'publish', 'posts_per_page' => -1 ) );


                ?>
                <?php if ( $wp_venue_query->have_posts(  ) ): ?>
				<div class="slider slider-l1 mb-20">
					<div class="slider-venue slider-capt flexslider mb-0">

                            <ul class="slides">
                            <?php while( $wp_venue_query->have_posts() ): $wp_venue_query->the_post();?>
                                <li>
                                    <?php do_action('aut_post_thumnail', 'venue-medium'); ?>
                                    <div class="slider-caption">
                                        <a href="#" class="link"><div class="slide-title"><?php the_title(); ?></div></a>
                                        <div class="slide-desc"><?php echo wp_trim_words(  get_the_content(), $num_words = 20, $more = '<a href="'. get_permalink() .'">read more</a>' ); ?></div>
                                    </div>
                                </li>
                            <?php endwhile; ?>
                            </ul>


					</div>
					<div id="carousel-venue" class="flexslider mb-0">
					 <ul class="slides">
                         <?php while( $wp_venue_query->have_posts() ): $wp_venue_query->the_post();?>
                             <li><?php do_action('aut_post_thumnail', 'venue-small-thumb'); ?> </li>
                         <?php endwhile; ?>
					 </ul>

					</div>
				</div>
				<a href="<?php echo get_post_type_archive_link( 'venue' ); ?>" class="btn btn-opposite btn-block btn-md"><?php _e( 'See all venues.', 'atu' ); ?></a>
                <?php else: ?>
                    <h3><?php _e( 'No Venue found.', 'atu' ); ?></h3>
                <?php endif; ?>
			</div>
			<div class="col-md-6 mb-30">
				<h4 class="title-l1"><?php _e( 'Latest Vendor List', 'atu' ); ?> </h4>

                <?php
                /**
                 * Get latest vendors
                 */
                $wp_venue_query = new WP_Query( array( 'post_type' => 'vendor', 'orderby' => 'date', 'order' => 'desc', 'post_status' => 'publish', 'posts_per_page' =>4 ) );


                if ( $wp_venue_query->have_posts(  ) ): ?>

                    <ul class="post-inline post-member mb-20">

                        <?php while( $wp_venue_query->have_posts() ): $wp_venue_query->the_post();

                            $cat_name = get_the_title();

                            $user_id = get_post_meta( get_the_ID(), 'vendor', true );

                            $taxonomy = get_user_meta( $user_id, 'region', true );
                            $cats = get_the_terms( get_the_ID(), $taxonomy );

                            if ( ! empty( $cats ) )$cat_name = $cats[0]->name;

                            $vendor_info = get_userdata($vendor->ID);

                            $description = wp_trim_words(  get_user_meta( $user_id, 'description', true ), $num_words = 16, $more = '...' );
                            $image_id = get_user_meta( $user_id, 'profile_image', true );
                            $company_name = get_user_meta( $user_id, 'company_name', true );

                            ?>
                            <li class="post-item">
                                <div class="post-img well-img">
                                    <a href="<?php the_permalink(); ?>"><?php echo wp_get_attachment_image( $image_id, 'vendor-small-thumb' ); ?></a>
                                </div>
                                <div class="post-core">
                                    <a href="<?php the_permalink() ?>" class="link">
                                        <div class="post-title t-normal"><?php echo $company_name; ?>
                                            <span class="post-cat t-highlight"><?php echo $cat_name; ?></span>
                                        </div>
                                    </a>
                                    <p><?php echo $description; ?>.</p>
                                </div>
                            </li>
                        <?php endwhile; wp_reset_query();?>
                    </ul>
                    <a href="<?php echo home_url('/vendors/') ?>" class="btn btn-opposite btn-block btn-md"><?php _e( 'See all Vendors', 'atu'); ?></a>
                <?php else: ?>

                    <h3><?php _e( 'No Vendors found.', 'atu' ); ?></h3>

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



<?php get_footer(); ?>