<?php 
add_shortcode('services_shortcode','services_fetch_shortcode');
function services_fetch_shortcode(){ 
     $args = array(
        'post_type' => 'services',
        'posts_per_page' => 6,
        'order' => 'DESC',
        'post_status' => 'publish',
     );

     

    $query = new WP_Query($args);
    ob_start();
    ?> 
    <section class="services-card-wrappers">
         <div class="services card-box"> 
            <?php if ($query->have_posts()) : ?>
                <?php while ($query->have_posts()) : $query->the_post();
                $title = get_the_title(); 
                $service_icon = get_field('service_icon',get_the_ID());
                $description_acf = get_field('services_description'); // ACF field
                $description = wp_trim_words( $description_acf, 20, '...');
                ?>
                <div class="service-box-card">
                            <div class="icon-box"> <img src="<?php echo  $service_icon; ?>"></div>
                            <div class="service-title"><?php echo esc_html($title); ?></div>
                            <div class="service-description"><?php echo $description; ?></div>
                            <a href="<?php echo the_permalink(); ?>" class="read-more">Read More <img src="images/services-arrow.png" alt=""></a>
                    </div>
                <?php endwhile; ?>
                <?php wp_reset_postdata(); ?>
            <?php else : ?>
                <p>No products found.</p>
            <?php endif; ?>
        </div>
    </section>
    <?php
    return ob_get_clean();
}
?>
