<?php
/**
 * Template Name: Contact Page
 *
 * @package WordPress
 * @subpackage Twenty_Fourteen
 * @since Twenty Fourteen 1.0
 */

get_header(); ?>



<?php 
	$bg = of_get_option('banner', '');
	$page_bg = wp_get_attachment_image_src(get_field('page_background'),'large');
	$page_bg = $page_bg[0];
	if (!$page_bg) {
		$page_bg = $bg;
	}
	if ($page_bg== "" and $bg == "") {
		$page_bg = get_template_directory_uri()."/assets/images/banner.jpg";
	}

	$user_id =1;
	$post_id =''; 

	if(isset($_GET["contact_id"])){
		$user_id = $_GET["contact_id"];
	}
	if(isset($_GET["post_id"])){
		$post_id = $_GET["post_id"];
	}

	$user_info =  get_userdata($user_id);
?>

<div class="l-content-bg" style="background: url('<?php echo $page_bg; ?>') no-repeat"> 
	<div class="container">
		<div class="row">
			<div class="col-md-9">
				<div class="l-content-container">
					<div class="page-header">
						<div class="row">
							<div class="col-md-6">
								<button class="btn btn-secondary btn-block" data-toggle="modal" data-target=".form-venue">Search Venue</button>
							</div>
							<div class="col-md-6">
								<button class="btn btn-secondary btn-block" data-toggle="modal" data-target=".form-vendor">Search Vendor</button>
							</div>
						</div>	
					</div>
					<div class="page-content">
						<div class="page-title ">
							<h2 class="t-lg">Contact Details</h2>
						</div>
						<div class="section section-contact">
							<div class="contact-avatar">
								<?php if ($post_id) {?>
									<?php echo wp_get_attachment_image( get_post_thumbnail_id( $post_id ),'thumbnail'); ?>
								<?php }else{ ?>
									<?php echo wp_get_attachment_image( $user_info->profile_image, 'thumbnail' ); ?>
								<?php } ?>
							</div>
							<?php if ($post_id) {?>

								<div class="contact-name"> Welcome to <?php echo get_field('company_name',$post_id) ?></div>
								<div class="contact-company">Address: <?php echo get_field('address',$post_id) ?> <?php echo get_field('region',$post_id) ?> <?php echo get_field('post_code',$post_id) ?></div>
								<div class="contact-content">Call us if you prefer to speak to a real life person</div>
								<ul class="list list-contact list-inline">
									<?php if (get_field('mobile',$post_id)) {?>
										<li><a href="tel:<?php echo get_field('mobile',$post_id) ?>"><span class="fa fa-mobile icon-l-sm"></span><?php echo get_field('mobile',$post_id) ?></a></li>
									<?php } ?>
									<?php if (get_field('phone',$post_id)) {?>
										<li><a href="tel:<?php echo get_field('phone',$post_id) ?>"><span class="fa fa-phone icon-l-sm"></span><?php echo get_field('phone',$post_id) ?></a></li>
									<?php } ?>
									<?php if (get_field('email',$post_id)) {?>
										<li><a href="mailto:<?php echo get_field('email',$post_id) ?>"><span class="fa fa-envelope icon-l-sm"></span><?php echo get_field('email',$post_id) ?></a></li>
									<?php } ?>						
								</ul>

							<?php }else{ ?>

								<div class="contact-name"> Hi! I am <?php echo $user_info->first_name ?> <?php echo $user_info->last_name ?></div>
								<div class="contact-company"><?php echo $user_info->company_name ?></div>
								<div class="contact-content">Call me if you prefer to speak to a real life person</div>
								<ul class="list list-contact list-inline">
									<?php if ($user_info->mobile) {?>
										<li><a href="tel:<?php echo $user_info->mobile; ?>"><span class="fa fa-mobile icon-l-sm"></span><?php echo $user_info->mobile; ?></a></li>
									<?php } ?>
									<?php if ($user_info->phone) {?>
										 <li><a href="tel:<?php echo $user_info->phone; ?>"><span class="fa fa-phone icon-l-sm"></span><?php echo $user_info->phone; ?></a></li>
									<?php } ?>
									<?php if ($user_info->user_email) {?>
										<li><a href="mailto:<?php echo $user_info->user_email; ?>"><span class="fa fa-envelope icon-l-sm"></span><?php echo $user_info->user_email; ?></a></li>
									<?php } ?>
									
		                           
		                            
								</ul>

							<?php } ?>
							
						</div>				
						<?php echo do_shortcode('[contact-form-7 id="196" title="Contact ATU" html_class="form form-labeled"]'); ?>
					</div>
				</div>
			</div>
			<div class="col-md-3">
				<?php get_template_part('sidebar','secondary') ?>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	<?php if ($post_id) {?>
		document.getElementById("user_email").value = "<?php echo get_field('email',$post_id) ?>";
  		document.getElementById("company_name").value = "<?php echo get_field('company_name',$post_id) ?>";
	<?php }else{ ?>
		document.getElementById("user_email").value = "<?php echo $user_info->user_email; ?>";
  		document.getElementById("company_name").value = "<?php echo $user_info->company_name ?>";
	<?php } ?>
  
</script>
<?php get_footer(); ?>


<!-- <div class="row">
<div class="col-md-6">
<div class="form-group field-wrap">
<label for="">Name<span class="req">*</span></label>
[text* name class:form-control]
</div>
<div class="form-group field-wrap">
<label for="">Email<span class="req">*</span></label>
[email* email class:form-control]
</div>
<div class="form-group">
<label for="" class="label-drop">Event Type<span class="req">*</label>
[select event id:event_type  class:form-control "Wedding" "Debut" "Birthday"]
</div>
<div class="form-group">
<label for="" class="label-drop">Who do you need?</label>
[select vendor id:vendor_category class:form-control multiple "Planner" "Photographer"]
</div>
<div class="form-group">
<label for="" class="label-drop">Venue type for event</label>
[select venue id:venue_category class:form-control "Beach" "Barn"]
</div>
<div class="form-group">
<label for="" class="label-drop">Date of Event<span class="req">*</label>
[date* date id:date_event class:form-control]
</div>
</div>
<div class="col-md-6">
<div class="form-group field-wrap">
<label for="">Phone<span class="req">*</span></label>
[tel* phone class:form-control]
</div>
<div class="form-group field-wrap">
<label for="">Address<span class="req">*</span></label>
[text* address class:form-control]
</div>
<div class="form-group field-wrap">
<label for="">Message<span class="req">*</span></label>
[textarea* message x14 class:form-control]
</div>
</div>
</div>
[hidden user_email id:user_email]
[hidden company_name id:company_name]
[submit class:btn class:btn-primary class:btn-block class:btn-lg "Send Message"] -->