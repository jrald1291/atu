<?php

/**

 * The sidebar containing the main widget area

 *

 * @package WordPress

 * @subpackage Twenty_Fifteen

 * @since Twenty Fifteen 1.0

 */





	$current_user = wp_get_current_user();



	$user_info = get_user_meta( $current_user->ID );



?>

<aside class="l-sidebar">
	<?php if (!is_user_logged_in() ) { ?> 
      <div class="widget">
          <a href="<?php echo wp_login_url();?>" class="btn btn-primary btn-block">Member Login</a>
          <a href="<?php echo get_permalink(492);?>" class="btn btn-primary btn-block mb-20">Become a Member</a>
      </div>
    <?php } ?>

	<div class="widget widget-aside">

		<div class="ven-avatar" style="border: 2px solid <?php echo hex2rgba(get_field( 'color')); ?>">


            <img src="<?php echo WEPN_Helper::venue_avatar(get_post_thumbnail_id(), 'img-avatar'); ?>" />


			<div class="ven-name" style="background-color: <?php echo hex2rgba(get_field( 'color'),0.5); ?>"><?php the_title(); ?></div>

		</div>

	</div>

	<div class="widget widget-aside widget-list">

		<div class="widget-list_logo">

            <?php echo wp_get_attachment_image( get_field( 'company_logo' ),  'medium' ); ?>

		</div>





		<div class="widget-header x">

            <?php



            $cat = get_term_by( 'id', get_field('main_category', get_the_ID()), 'venue-category' );



            echo  (!empty($cat) && !is_wp_error($cat)) ? $cat->name : get_the_title(); ?>

        </div>

		<ul class="list">
			<?php if (trim(get_field( 'mobile' ),' ')!="") {?>
				<li><a href="tel:<?php the_field( 'mobile' ); ?>">Mobile: <?php the_field( 'mobile' ); ?></a></li>
			<?php } ?>
			<?php if (trim(get_field( 'phone' ),' ')!="") {?>
				<li><a href="tel:<?php the_field( 'phone' ); ?>">Phone: <?php the_field( 'phone' ); ?></a></li>
			<?php } ?>
			<?php if (trim(get_field( 'email' ),' ')!="") {?>
				<li><a href="mailto:<?php the_field( 'email' ); ?>"><?php the_field( 'email' ); ?></a></li>
			<?php } ?>
			<?php if (trim(get_field( 'website' ),' ')!="") {?>
				<li><a href="<?php the_field( 'website' ); ?>" target="_blank">
				<?php 
					$str_url = get_field( 'website' );
	                echo str_replace(array('http://','https://'), '', $str_url);
                ?>
				</a></li>
			<?php } ?>
		</ul>



	</div>

	<div class="widget widget-aside">

		<a href="<?php echo get_permalink(32).'/?post_id='. get_the_ID() .'&category=' . $cat->name; ?> " class="btn btn-block btn-md btn-primary"><span class="fa icon-l fa-envelope"></span>Contact Venue</a>

	</div>
	<?php if (trim(get_field( 'website' ),' ')!="") {?>
	<div class="widget widget-aside">
		<a href="<?php the_field( 'website' ); ?>" target="_blank" class="btn btn-sm btn-block btn-secondary"><span class="fa icon-l-sm fa-globe"></span>Visit website</a>
	</div>
	<?php } ?>

	<div class="widget widget-aside">

		<div class="call-to-action" style="background-color: <?php echo hex2rgba(get_field( 'color')); ?>">

			<span class="icon icon-tel"></span>

			<p>Any questions?</p>

			<p>Call US Now</p>
			
			<p>
				<?php if (trim(get_field( 'phone' ),' ')!="") {?>
					<a href="tel:<?php the_field( 'phone' ); ?>"><?php the_field( 'phone' ); ?></a>
				<?php }else{ ?>
					<a href="tel:<?php the_field( 'mobile' ); ?>"><?php the_field( 'mobile' ); ?></a>
				<?php } ?>
			</p>

		</div>

	</div>

</aside>