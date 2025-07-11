<?php
/*
Template Name: Elementor About Template
*/
get_header();
?>
<main id="primary" class="site-main">
    <?php
    while ( have_posts() ) :
        the_post();
        the_content(); // This is essential for Elementor
    endwhile;
    ?>
</main>
<?php get_footer(); ?>





