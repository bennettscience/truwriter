<?php
/*
Template Name: Entrance To Writer

Gateway to the Writing tool
*/

// already logged in? go directly to the tool
if ( is_user_logged_in() ) {
	
	if ( current_user_can( 'edit_others_posts' ) ) {

		// If user has edit/admin role, send them to the tool
		wp_redirect ( site_url() . '/write' );
  		exit;

	} else {
	
		// okay user, who are you? we know you are not an admin or editor
		$user = get_user_by( 'login', 'writer');
		
		// if the writer user  found, go directly to the tool
		if (  $user ) {			
	  		wp_redirect ( site_url() . '/write' );
  			exit;
  		} else {
  		
			// they are logged in to splot from somewhere else, zap them out
			wp_logout();
  		}
  	}
}


// ------------------------ defaults ------------------------

// default welcome message
$feedback_msg = '';

// the passcode to enter
$wAccessCode = truwriter_option('accesscode');

// ------------------------ presets ------------------------


// verify that a  form was submitted and it passes the nonce check
if ( 	isset( $_POST['truwriter_form_access_submitted'] ) 
		&& wp_verify_nonce( $_POST['truwriter_form_access_submitted'], 'truwriter_form_access' ) ) {
 
	// grab the variables from the form
	$wAccess = 	stripslashes( $_POST['wAccess'] );
	
	// let's do some validation, store an error message for each problem found
	$errors = array();
	
	if ( $wAccess != $wAccessCode ) $errors[] = '<p><strong>Incorrect Access Code</strong> - try again? Hint: ' . truwriter_option('accesshint'); 	
	
	if ( count($errors) > 0 ) {
		// form errors, build feedback string to display the errors
		$feedback_msg = '';
		
		// Hah, each one is an oops, get it? 
		foreach ($errors as $oops) {
			$feedback_msg .= $oops;
		}
		
		$feedback_msg .= '</p>';
		
	} else {

		wp_redirect ( site_url() . '/wp-login.php?autologin=writer' );
		exit;
	}

		
} // end form submmitted check
?>

<?php get_header(); ?>
			
<div class="content">		

	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>				
	
		<div <?php post_class('post single'); ?>>
		
			<?php if ( has_post_thumbnail() ) : ?>
			
				<?php $thumb = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'thumbnail_size' ); $thumb_url = $thumb['0']; ?>
		
				<div class="featured-media">
				
					<script type="text/javascript">
	
						jQuery(document).ready(function($) {
				
							$(".featured-media").backstretch("<?php echo $thumb_url; ?>");
							
						});
						
					</script>
		
					<?php the_post_thumbnail('post-image'); ?>

					<div class="media-caption-container">
						
							<p class="media-caption"><?php echo get_post(get_post_thumbnail_id())->post_excerpt; ?></p>
							
					</div>					
				</div> <!-- /featured-media -->
					
			<?php endif; ?>
											
			<div class="post-header section">
		
				<div class="post-header-inner section-inner">
																									
					<h2 class="post-title"><?php the_title(); ?></h2>
				
				</div> <!-- /post-header-inner section-inner -->
														
			</div> <!-- /post-header section -->
			    
		    <div class="post-content section-inner thin">
		    
		    	<?php the_content(); ?>
		    	
			    	
		    	
		    	<?php  
		    	// set up box code colors CSS

		    	if ( count( $errors ) ) {
		    		$box_style = '<div class="notify notify-red"><span class="symbol icon-error"></span> ';
		    		echo $box_style . $feedback_msg . '</div>';
		    	}
		    			    	
		    	
		    	?>   
		    	
				<div class="clear"></div>
		    
					
		<?php endwhile; else: ?>
	
			<p><?php _e("We couldn't find any posts that matched your query. Please try again.", "radcliffe"); ?></p>

		<?php endif; ?>
						
		<form  id="comparatorform" class="comparatorform" method="post" action="">
					
				<fieldset>
					<label for="wAccess"><?php _e('Access Code', 'radcliffe' ) ?></label><br />
					<p>Enter a proper code</p>
					<input type="text" name="wAccess" id="wAccess" class="required" value="<?php echo $wAccess; ?>" tabindex="1" />
				</fieldset>	
			
				<fieldset>
					<?php wp_nonce_field( 'truwriter_form_access', 'truwriter_form_access_submitted' ); ?>
					<input type="submit" class="pretty-button pretty-button-blue" value="Check Code" id="checkit" name="checkit" tabindex="15">
				</fieldset>
				
		</form>

			
	</div> <!-- /post -->
	
</div> <!-- /content -->
								
<?php get_footer(); ?>