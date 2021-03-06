<?php get_header(); ?>


<div class="l-content-bg" style="background: url('<?php WEPN_Helper::background_image( get_field('page_background', get_the_ID()) ); ?>') no-repeat">
	<div class="container">
		<div class="row">
			<div class="col-md-9">
				<div class="l-content-container">
					<div class="page-header">
						<div class="row">
							<div class="col-md-6">
								<a href="<?php echo get_home_url().'/venue/';?>" class="btn btn-secondary btn-block">Find Venue</a>
							</div>
							<div class="col-md-6">
								<a href="<?php echo get_home_url().'/suppliers/';?>" class="btn btn-secondary btn-block">Find Supplier</a>
							</div>
						</div>	
					</div>
					<div class="page-content">
						<div class="page-title">
							<h2 class="t-lg"></h2>
						</div>
						<div class="mb-20 xx">
							 <?php while ( have_posts() ) : the_post();?>
							 	<?php if (is_author()) {?>
							 		<?php get_template_part( 'author', 'bio');?>	
							 	<?php } else {?>
							 		<?php get_template_part( 'content', 'page');?>
							 	<?php } ?>
							 <?php endwhile;?>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-3">
				<?php if (is_author()) {?>
			 		<?php get_sidebar();?>	
			 	<?php } else {?>
			 		<?php get_template_part('sidebar','secondary') ?>
			 	<?php } ?>
				
			</div>
		</div>
	</div>
</div>
<?php if (is_author()) {?>
	<div class="pagination-single">
		<ul>
			<li class="prev">
				<?php $prepo=get_previous_post(); 
				if ($prepo) {
						$prepoid=$prepo->ID;
						$pre_post_url = get_permalink($prepoid);?>
						<a href="<?php echo $pre_post_url; ?>">
							<span class="label"><i class="fa fa-angle-left icon-l"></i>Previous</span>
							<span><?php echo get_the_title($prepoid); ?></span>
						</a>
				<?php }else{?>
					<div class="disabled">
							<span class="label"><i class="fa fa-angle-left icon-l"></i>Previous</span>
							<span>No previous post</span>
					</div>
				<?php } ?>
				
			</li>
			<li class="back">
				<a href="<?php echo get_permalink( get_page_by_title( 'Blog' ))?>">back to Suppliers Listing</a>
			</li>
			<li class="next">
				<?php $nepo=get_next_post(); 
				if ($nepo) {
						$nepoid=$nepo->ID;
						$ne_post_url = get_permalink($nepoid);?>
						<a href="<?php echo $ne_post_url; ?>">
							<span class="label"><i class="fa fa-angle-left icon-l"></i>Next</span>
							<span><?php echo get_the_title($nepoid); ?></span>
						</a>
				<?php }else{?>
					<div class="disabled">
							<span class="label"><i class="fa fa-angle-left icon-l"></i>Next</span>
							<span>No next post</span>
					</div>
				<?php } ?>
			</li>
		</ul>
	</div>
<?php } ?>
<?php get_footer(); ?>