<?php
/**
 * The template used for displaying page content
 *
 * @package WordPress
 * @subpackage Twenty_Fifteen
 * @since Twenty Fifteen 1.0
 */
?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<div class="page-title ">
		<h1 class="t-lg"><?php the_title(); ?></h1>
		</div>
		<?php if ( has_post_thumbnail() ) { ?>
			<div class="post-img well-img mb-20">
				<?php the_post_thumbnail('img-wide'); ?>
			</div>
		<?php } ?>
		<div class="post-core">
		<div class="post-content copy mb-30">
			<div class="panel-parent" id="panel-about">			
				<?php if (of_get_option('video_about', '')) {?>
				  <div class="panel panel-group">
				    <div class="panel-heading">
				      <a class="panel-toggle link" data-toggle="collapse" data-parent="#panel-about" href="#collaplse-about">
				        <span class="icon icon-l-sm fa fa-play-circle"></span> Watch <?php echo of_get_option('video_about_text', ''); ?>
				      </a>
				    </div>
				    <div id="collaplse-about" class="panel-body collapse in">
				      <div class="panel-inner">
				        <?php echo of_get_option('video_about', ''); ?>
				      </div>
				    </div>
				  </div>
			  	<?php } ?>
			  	<?php if (of_get_option('video_blooper', '')) {?>
				  <div class="panel panel-group">
				    <div class="panel-heading">
				      <a class="panel-toggle link" data-toggle="collapse" data-parent="#panel-about" href="#collaplse-blooper">
				        <span class="icon icon-l-sm fa fa-play-circle"></span> Watch <?php echo of_get_option('video_blooper_text', ''); ?>
				      </a>
				    </div>
				    <div id="collaplse-blooper" class="panel-body collapse">
				      <div class="panel-inner">
				        <?php echo of_get_option('video_blooper', ''); ?>
				      </div>
				    </div>
				  </div>
			  	<?php } ?>
			</div>
			<p><?php the_content(); ?></p>
		</div>
		<div class="post post-block">
			<div class="row">
				<?php 
					if( have_rows('member') ):
					    while ( have_rows('member') ) : the_row();?>

							<div class="col-md-4 col-sm-6">
								<div class="post-item">
									<div class="post-header"><?php the_sub_field('member_name'); ?></div>
									<div class="post-img">
										<?php $member_img = wp_get_attachment_image_src(get_sub_field('member_image'),'img-avatar'); ?>
										<img src="<?php echo $member_img[0];?>"/>
									</div>
									<div class="post-content t-sm">
										<?php the_sub_field('member_title'); ?>
										<span><?php the_sub_field('member_sub_title'); ?></span>
									</div>
								</div>
							</div>

					    <?php endwhile;?>
				<?php endif;?>
				<?php wp_reset_query(); ?>
				
				
			</div>
		</div>
	</div>
</article>



