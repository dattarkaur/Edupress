
<?php

add_action('wp_enqueue_scripts', 'my_child_theme_enqueue_styles');
function my_child_theme_enqueue_styles() {
    wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css');
    	wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/style.css',array('parent-style') );
      wp_enqueue_style( 'bootstrap-style', get_stylesheet_directory_uri() . '/css/bootstrap.css',array('child-style') );
	 wp_enqueue_script( 'bootstrap-js', get_stylesheet_directory_uri() . '/js/bootstrap-js.js',array('child-style') );
        wp_enqueue_style( 'style', get_stylesheet_directory_uri() . '/css/style.css',array('child-style') );
    wp_enqueue_script('globel-js', get_template_directory_uri() . '/js/globel.js', array('jquery'), null, true);
}

function enqueue_slick_slider_assets() {
    wp_enqueue_style('slick-css', 'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css');
    wp_enqueue_style('slick-theme-css', 'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css');

    wp_enqueue_script('slick-js', 'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js', array('jquery'), null, true);


   wp_add_inline_script('slick-js', "
        jQuery(document).ready(function($) {
  if ($(window).width() > 768) {    
    $('.testimonial-wrapper').slick({
      dots: true,
      arrows: false,
      infinite: true,
      speed: 300,
      slidesToShow: 1
    });
  }
});

    ");
}
add_action('wp_enqueue_scripts', 'enqueue_slick_slider_assets');


//post type

add_action('init', 'products_post_type');  
function products_post_type() {
    register_post_type('services', array(
        'label'        => 'Services',
        'public'       => true,
        'supports'     => array('title', 'thumbnail', 'custom-fields'),
        'has_archive'  => true,
        'rewrite'      => array('slug' => 'services'),
    ));

 register_post_type('about', array(
        'label'        => 'About',
        'public'       => true,
        'supports'     => array('title', 'thumbnail', 'custom-fields'),
        'has_archive'  => true,
        'rewrite'      => array('slug' => 'about'),
    ));
    register_post_type('faculty', array(
        'label'        => 'Faculty',
        'public'       => true,
        'supports'     => array('title', 'thumbnail', 'custom-fields'),
        'has_archive'  => true,
        'rewrite'      => array('slug' => 'faculty'),
    ));


}

function fetch_services_shortcode() {
    ob_start();
    $args = array(
        'post_type'      => 'services', 
        'posts_per_page' => -1,
        'post_status'    => 'publish',  
        'order'          => 'ASC',
    );
    $service_query = new WP_Query($args);
    ?>
<div class="container" >
    <div class="services-container" style="margin-top:60px; max-width:1600px; margin:auto;">
        <div class="services-grid" style="margin-bottom:85px;">
            <?php
            if ($service_query->have_posts()) :
                while ($service_query->have_posts()) : $service_query->the_post();
            ?>
            <!-- services Structure -->
            <div class="service-card">
                <img src="<?php echo get_the_post_thumbnail_url(get_the_ID(), 'medium'); ?>" class="service-img" alt="<?php echo esc_attr(get_the_title()); ?>">
                <div class="service-content">
                    <p class="service-title">
                        <?php
                        $service_heading = get_field('service_heading');
                        echo $service_heading ? wp_kses_post($service_heading) : 'Not specified';
                        ?>
                    </p>
                    

                    <p class="service-desc hidden-content">
                     <?php
$service_desc = get_field('service_desc');
$excerpt_length = 20; // Limit to 20 words

if ($service_desc) {
    $words = explode(" ", $service_desc);
    $short_desc = implode(" ", array_slice($words, 0, $excerpt_length));

    echo '<p class="service-desc">' . $short_desc . ' <a href="' . get_permalink() . '" class="service-link read-more-btn">View More</a></p>';
} else {
    echo '<p class="service-desc">Not specified</p>';
}
?>

                </div>
            </div>
            <?php 
                endwhile;
                wp_reset_postdata();
            else :
                echo '<p>No services found.</p>';
            endif;
            ?>
        </div>
    </div>
</div>
  
    <?php   
    return ob_get_clean(); // Return the buffered content
}
add_shortcode('fetch_services', 'fetch_services_shortcode');

//fetch testimonial

function get_testimonials_shortcode($atts) {
    ob_start();

    $args = array(
        'post_type'      => 'testimonial',
        'posts_per_page' => -1,
        'post_status'    => 'publish'
    );

    $testimonial_query = new WP_Query($args);

    if ($testimonial_query->have_posts()) :
        echo '<div class="testimonial-wrapper ">';
         while ($testimonial_query->have_posts()) : $testimonial_query->the_post(); 
         $post_id = get_the_id();
         $tss_designation = get_post_meta($post_id, 'tss_designation', true);
       
    ?>

            <div class="testimonial-item">
                <div class="testimonialTopHead">
                    <div class="d-flex">
                    <?php if (has_post_thumbnail()) : ?>
                        <div class="testimonial-image">
                            <?php the_post_thumbnail('medium'); ?>
                        </div>
                    <?php endif; ?>
                    <div class="contain">
                     <div class="custom-stars">
                    ★★★★★
                    </div> 
                    <h3 class="testimonial-title"><?php the_title(); ?></h3>
                    </div>
                    <h6 class="testimonial-desig"> &nbsp;- <?php echo $tss_designation; ?></h6>
                    
                    </div>
                </div>
                <div class="testimonial-content">
                    <?php the_content(); ?>
                </div>
            </div>

          <?php endwhile;
        echo '</div>';
        wp_reset_postdata();
    else :
        echo '<p>No testimonials found.</p>';
    endif;

    return ob_get_clean();
}
add_shortcode('testimonials', 'get_testimonials_shortcode');


//search 
function search_pages() {
    $query = sanitize_text_field($_POST['query']);
    
    $args = array(
        'post_type' => 'page',
        's' => $query,
        'posts_per_page' => 5
    );

    $search_query = new WP_Query($args);
    
    if ($search_query->have_posts()) {
        while ($search_query->have_posts()) {
            $search_query->the_post();
            echo '<p><a href="' . get_permalink() . '">' . get_the_title() . '</a></p>';
        }
    } else {
        echo '<p>No pages found.</p>';
    }

    wp_die();
}
add_action('wp_ajax_search_pages', 'search_pages');
add_action('wp_ajax_nopriv_search_pages', 'search_pages');


// about post


function fetch_about_shortcode() {
 

    ob_start();
    $args = array(
        'post_type'      => 'about', 
        'posts_per_page' => -1,
        'post_status'    => 'publish',  
        'order'          => 'ASC',
    );
    $service_query = new WP_Query($args);
    ?>
<div class="container" >
    <div class="about-container" style="margin-top:60px; max-width:1600px; margin:auto;">
        <div class="about-grid">
            <?php
            if ($service_query->have_posts()) :
                while ($service_query->have_posts()) : $service_query->the_post();
                     echo the_content();
            ?>
            <!-- services Structure -->
            <div class="about-card">
                <img src="<?php echo get_the_post_thumbnail_url(get_the_ID(), 'medium'); ?>" class="service-img" alt="<?php echo esc_attr(get_the_title()); ?>">
                <div class="about-content">
                    <p class="about-title">
                        <?php
                        echo get_the_title();
                        ?>
                    </p>
                    <p class="about-desc hidden-content">
                        <?php
                        $about_desc = get_field('about_desc');
                        echo $about_desc ? wp_kses_post($about_desc) : 'Not specified';
                        ?>
                    </p>
                    <a href="<?php the_permalink(); ?>" class="about-link read-more-btn">Read More</a>
                </div>
            </div>
            <?php 
                endwhile;
                wp_reset_postdata();
            else :
                echo '<p>No services found.</p>';
            endif;
            ?>
        </div>
    </div>
</div>
  
    <?php   
    return ob_get_clean(); // Return the buffered content
}
add_shortcode('fetch_about', 'fetch_about_shortcode');


//faculty posts

function fetch_faculty_shortcode() {
 

    ob_start();
    $args = array(
        'post_type'      => 'faculty', 
        'posts_per_page' => -1,
        'post_status'    => 'publish',  
        'order'          => 'ASC',
    );
    $faculty_query = new WP_Query($args);
    ?>
<div class="container" >
    <div class="about-container" style="margin-top:60px; max-width:1600px; margin:auto;">
        <div class="faculty-grid">
            <?php
            if ($faculty_query->have_posts()) :
                while ($faculty_query->have_posts()) : $faculty_query->the_post();
            ?>
            <!-- services Structure -->
            <div class="faculty-card">
                <img src="<?php echo get_the_post_thumbnail_url(get_the_ID(), 'medium'); ?>" class="service-img" alt="<?php echo esc_attr(get_the_title()); ?>">
                <div class="both d-flex justify-content-center gap-4 mt-3 " style="background-color:#F9EDF7";>
                    <div class="name">
                   <?php  $faculty_name = get_field('faculty_name') ;
                   echo $faculty_name ; ?>
                    </div>
                    <div class="date">
                     <?php  $post_date = get_field('post_date') ;
                   echo $post_date ; ?>
                    </div>
                </div>
                <div class="faculty-content">
                    <p class="faculty-title">
                        <?php
                        echo get_the_title();
                        ?>
                    </p>
                    <p class="faculty-desc hidden-content">
                        <?php
                        $faculty_desc = get_field('faculty_desc');
                        echo $faculty_desc ? wp_kses_post($faculty_desc) : 'Not specified';
                        ?>
                    </p>
                    <a href="<?php the_permalink(); ?>" class="faculty-link read-more-btn">Read More</a>
                </div>
            </div>
            <?php 
                endwhile;
                wp_reset_postdata();
            else :
                echo '<p>No services found.</p>';
            endif;
            ?>
        </div>
    </div>
</div>
  
    <?php   
    return ob_get_clean(); // Return the buffered content
}
add_shortcode('fetch_faculty', 'fetch_faculty_shortcode');

