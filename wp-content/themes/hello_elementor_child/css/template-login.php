<?php
// /* Template Name: Login */
if( is_user_logged_in()) {
    wp_redirect( home_url('/' ) ); // send to homepage
}
get_header();
?>
<?php if ( have_posts() ) : ?>
    <?php while ( have_posts() ) : the_post(); ?>
       <div class="container  mt-5 w-25 shadow p-3 mb-5 bg-body-tertiary rounded">
         <div class="">
           <h4><?php _e( 'Login', 'teamed' ); ?></h4>
             <?php wp_login_form();?>
         </div>
                <div class="login-page-col text-center">
                    <div class="login-page-forgot">
                    <?php// if( false ): ?>
                    <p><?php _e( "Don't have an account yet?", 'teamed' ); ?></p>
                    <a href="<?php echo site_url( '/index.php/sign-in/' ); ?>" class="text-danger">
                    <?php _e( 'Create an Account', 'teamed' ); ?>
                    </a><br><br>
                    </div>
                </div>
       </div>
    <?php endwhile; ?>
<?php endif; ?>
<?php
  get_footer();
?>