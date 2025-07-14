
<?php
add_action('wp_enqueue_scripts', 'my_child_theme_enqueue_styles');
function my_child_theme_enqueue_styles() {
    $isUserLogin = is_user_logged_in() ? 'true' : 'false';
    $random = "test";
    wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css');
    wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/style.css',array('parent-style') );
    wp_enqueue_style( 'bootstrap-style', get_stylesheet_directory_uri() . '/css/bootstrap.css',array('child-style') );
    wp_enqueue_style( 'bootstrap', get_stylesheet_directory_uri(). '/css/Bootstrap.min.css?' . strtotime('now') );	 
    wp_enqueue_script( 'bootstrap-js', get_stylesheet_directory_uri() . '/js/bootstrap-js.js',array('child-style') );
    wp_enqueue_style( 'style', get_stylesheet_directory_uri() . '/css/style.css',array('child-style'). strtotime('now'));
    wp_enqueue_script('globel-js', get_stylesheet_directory_uri() . '/js/globel.js', array('jquery','slick-js'), null, true);
 
    wp_localize_script('globel-js', 'ajax_object',[
      'ajaxurl' => admin_url('admin-ajax.php'),
      'isLoggedIn' => $isUserLogin,
      'val' => $random
]);
    wp_enqueue_script('jquery');
    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css');
  
}

add_action('wp_enqueue_scripts', 'enqueue_slick_slider_assets');
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
    register_post_type('blog', array(
        'label'        => 'Blog',
        'public'       => true,
        'supports'     => array('title', 'thumbnail', 'custom-fields'),
        'has_archive'  => true,
        'rewrite'      => array('slug' => 'blog'),
    ));
      register_taxonomy('service_category','blog', array(
        'label'        => 'Service Categories',
        'hierarchical' => true, 
        'rewrite'      => array('slug' => 'service-category'),
        'show_admin_column' => true, 
        ));
         register_taxonomy('service_role','blog', array(
        'label'        => 'Service Role',
        'hierarchical' => true, 
        'rewrite'      => array('slug' => 'cservice_role'),
        'show_admin_column' => true, 
        ));
        add_role(
        'manager',
            __( 'Manager' ),
            array(
            'read'         => true,
            'edit_posts'   => true,
            'delete_posts' => false,
            )
        );
        add_role(
        'reporter',
        __( 'Reporter' ),
        array(
            'read'         => true,
            'edit_posts'   => true,
            'delete_posts' => false,
        )
   );
}

add_shortcode('fetch_services', 'fetch_services_shortcode');
function fetch_services_shortcode() {
    ob_start();
    $args = array(
        'post_type'      => 'services', 
        'posts_per_page' => 4,
        'post_status'    => 'publish',  
        'order'          => 'ASC',
    );
    $service_query = new WP_Query($args);
    ?>
 <div class="container">
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
                            $full_desc = get_field('service_desc');
                            $excerpt_length = 18;
                            if ($full_desc) {
                                $short_desc = wp_trim_words($full_desc, $excerpt_length);
                                echo '<p>' . $short_desc . '</p>';
                            }
                          ?>
                    </p>
            
                    <a href="<?php the_permalink(); ?>" class="service-link read-more-btn" style="font-size:16px; font-weight:600;">View More</a>
                </div>
            </div>
            <?php 
                endwhile;
                wp_reset_postdata();
            else :
            wp_send_json_error(['message' => 'Not services found..']);
            endif;
            ?>
        </div>
    </div>
 </div>
    <?php   
    return ob_get_clean(); 
}


//fetch testimonial

add_shortcode('testimonials', 'get_testimonials_shortcode');
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
         wp_send_json_error(['message' => 'Testimonial not found..']);
    endif;
  return ob_get_clean();
}


// about post

add_shortcode('fetch_about', 'fetch_about_shortcode');
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
                                    $full_desc = get_field('about_desc'); // Fetch ACF field
                                    $excerpt_length = 18; // Limit to 20 words

                                    if ($full_desc) {
                                        $short_desc = wp_trim_words($full_desc, $excerpt_length);
                                        echo '<p>' . $short_desc . '</p>';
                                    } 
                                ?>
                            </p>


                            <a href="<?php the_permalink(); ?>" class="about-link read-more-btn">Read More</a>
                        </div>
                    </div>
                    <?php 
                        endwhile;
                        wp_reset_postdata();
                    else :
                    wp_send_json_error(['message' => 'No services found..']);
                    endif;
                    ?>
                </div>
            </div>
        </div>
  
     <?php   
    return ob_get_clean(); 
}

//faculty posts

add_shortcode('fetch_faculty', 'fetch_faculty_shortcode');
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
                        <div class="faculty-card ">
                            <img src="<?php echo get_the_post_thumbnail_url(get_the_ID(), 'medium'); ?>" class="service-img" alt="<?php echo esc_attr(get_the_title()); ?>">
                            <div class="both d-flex justify-content-between mt-3  " style="background-color:#F9EDF7";>
                                <div class="name">
                                    <i class="fa-solid fa-user"></i>
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
                                        $full_desc = get_field('faculty_desc'); // Fetch ACF field
                                        $excerpt_length = 15; // Limit to 20 words

                                        if ($full_desc) {
                                            $short_desc = wp_trim_words($full_desc, $excerpt_length);
                                            echo '<p>' . $short_desc . '</p>';
                                        } 
                                    ?>
                                </p>
                            <?php
                                $url = get_field('about_url');
                                if ($url) {
                                $linked_post_id = url_to_postid($url);
                                if ($linked_post_id) {
                                    echo '<p>Linked Post ID: ' . $linked_post_id . '</p>';
                                }
                            }
                            ?>

                                <a href="<?php the_permalink(); ?>" class="faculty-link read-more-btn">Read More</a>
                            </div>
                        </div>
                        <?php 
                            endwhile;
                            wp_reset_postdata();
                        else :
                        wp_send_json_error(['message' => 'No services found..']);

                        endif;
                        ?>
                    </div>
                </div>
            </div>
      <?php   
    return ob_get_clean(); 
}

//login logout

add_filter('body_class', 'add_logged_in_class');
function add_logged_in_class($classes) {
    if (is_user_logged_in()) {
        $classes[] = 'logged-in';
    } else {
        $classes[] = 'not-logged-in';
    }
    return $classes;
}

//login logout 
add_action('init', 'custom_logout_handler');
function custom_logout_handler() {
    if (isset($_GET['action']) && $_GET['action'] === 'logout') {
        wp_logout();
        wp_redirect(home_url()); 
    }
}

// Nav bar search
add_shortcode('navbar_page_search', 'navbar_page_search_shortcode');
function navbar_page_search_shortcode() {
    ob_start();
    ?>
    <div class="navbar-search-wrapper d-flex">
        <input type="text" id="navbar-search" placeholder="Search Pages" />
        <button type="submit" id="navbar-search-button">Search</button>
        <div id="navbar-search-results"></div> 
        <div id="message"></div> 
    </div>
    <?php
    return ob_get_clean();
}

// AJAX handler
add_action('wp_ajax_navbar_page_search', 'handle_navbar_page_search');
add_action('wp_ajax_nopriv_navbar_page_search', 'handle_navbar_page_search');
function handle_navbar_page_search() {
    $query = sanitize_text_field($_POST['query']);
    $args = [
        'post_type' => 'page',
        'post_status' => 'publish',
        's' => $query,
        'posts_per_page' => 4,
    ];
    $results = [];
    $search = new WP_Query($args);

    if ($search->have_posts()) {
        foreach ($search->posts as $post) {
            $results[] = [
                'title' => get_the_title($post),
                'link' => get_permalink($post)
            ];
        }
        wp_send_json_success(['message' => $results]);
    } else {
        wp_send_json_error(['message' => 'No results found']);
    }
}
// nav bar page end

// blog service page search bar 
add_shortcode('blog_searchbox', 'custom_search_box'); 
function custom_search_box() {
    ob_start();
        ?>
        <div class="search-form-posts">
            <form class="search-box" method="post" id="ajax-blogpost-search-btn">
                <div class="search-container d-flex">
                    <input type="text" name="search_blog" id="ajax-blogpost-search" placeholder="Search..." />
                    <button type="submit" class="blog-btn">Search</button>
                </div>
            </form> 
            <div class="both-tax d-flex gap-5 ">
                <div class="taxonomy-front mt-4 w-50 ">
                    <?php
                        $terms = get_terms(array(
                            'taxonomy' => 'service_category', 
                            'hide_empty' => false,
                        ));
                        if (!empty($terms) && !is_wp_error($terms)) {
                            echo '<select id="taxonomy-select">';
                            echo '<option value="">Select Category</option>';
                            foreach ($terms as $term) {
                                echo '<option value="' . esc_attr($term->slug) . '">' . esc_html($term->name) . '</option>';
                            }
                            echo '</select>';
                        }
                        ?>
                </div>
                <div class="role-front mt-4 w-50">
                    <?php
                        $terms = get_terms(array(
                            'taxonomy' => 'service_role', 
                            'hide_empty' => false,
                        ));
                        if (!empty($terms) && !is_wp_error($terms)) {
                            echo '<select id="taxonomy-role">';
                            echo '<option value="">Select Role</option>';
                            foreach ($terms as $term) {
                                echo '<option value="' . esc_attr($term->slug) . '">' . esc_html($term->name) . '</option>';
                            }
                            echo '</select>';
                        }
                    ?>
                </div>
            </div>
                    <div id="blog-message"></div>
                    <div id="ajax-blogpost-results">
                        <?php
                        $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;   
                        echo get_blog_post($paged); ?>
                    </div>
        </div>                                   
        <?php   
    return ob_get_clean();
}

//  blog descripion search
add_filter( 'posts_where', 'custom_search_where', 10, 2 );
function custom_search_where( $where, $wp_query ) {
    global $wpdb;
    if ( $search = $wp_query->get( 'custom_search' )){
        $where .= " AND(
            {$wpdb->posts}.post_title LIKE '%" . esc_sql( $wpdb->esc_like( $search ) ) . "%'
            OR EXISTS(
                SELECT 1 FROM {$wpdb->postmeta}
                WHERE {$wpdb->postmeta}.post_id = {$wpdb->posts}.ID
                AND {$wpdb->postmeta}.meta_key = 'blog_desc'
                AND {$wpdb->postmeta}.meta_value LIKE '%" . esc_sql( $wpdb->esc_like( $search ) ) . "%'
            )
        )";
    }
    return $where;
}
function get_blog_post($paged = 1, $search = null,$taxonomy = null, $tax_role = null) {
        ob_start();
        
    $args = array(
        'post_type'      => 'blog', 
        'post_status'    => 'publish',
        'posts_per_page' => 4,
        'paged'          => $paged,
        'meta_query'     => array(
            array(
                'key'     => 'blog_status',
                'value'   => 'active',
                'compare' => '=',
            ),
        ),
    );
    if (!empty($search)) {
        $args['custom_search'] = $search;
    }
    
    if (!empty($taxonomy) || !empty($tax_role)) {
           $args['tax_query'] = array(
               'relation' => 'OR',
           );
      if (!empty($taxonomy)) {
        $args['tax_query'][] = array(
            array(
                'taxonomy' => 'service_category',
                'field'    => 'slug',
                'terms'    => $taxonomy,
            ),
        );
      }
       if (!empty($tax_role)) {
        $args['tax_query'][] = array(
            array(
                'taxonomy' => 'service_role',
                'field'    => 'slug',
                'terms'    => $tax_role,
            ),
        );
       }
    }
     $query = new WP_Query($args); 
     ?>
        <div class="container">
            <div class="blog-container" style="margin-top:60px; max-width:1600px; margin:auto;">
                <div class="blog-grid gap-4" style="margin-bottom:85px;">
                    <?php if ($query->have_posts()) : ?>
                        <?php while ($query->have_posts()) : $query->the_post(); ?>
                            <div class="blog-card mb-5">
                                <img src="<?php echo get_the_post_thumbnail_url(get_the_ID(), 'medium'); ?>" class="service-img" alt="<?php echo esc_attr(get_the_title()); ?>">
                                <div class="blog-content">
                                    <p class="blog-title">
                                        <?php
                                        $blog_heading = get_field('blog_heading');
                                        echo $blog_heading ? wp_kses_post($blog_heading) : 'Not specified';
                                        ?>
                                    </p>
                                    <p class="" style="font-weight: bolder; background-color: rgb(247, 230, 230); color: #FF6575; border-radius: 20%;">
                                        <?php
                                        $service_category = get_the_terms(get_the_ID(), 'service_category');
                                        if (!empty($service_category) && !is_wp_error($service_category)) {
                                            foreach ($service_category as $service_category) {
                                                echo esc_html($service_category->name) . ' ';
                                            }
                                        } else {
                                            echo 'No job type assigned';
                                        }
                                    ?>
                                    </p>
                                    <?php
                                        $service_category = get_the_terms(get_the_ID(), 'service_role');
                                        if (!empty($service_category) && !is_wp_error($service_category)) {
                                            foreach ($service_category as $service_category) {
                                                echo esc_html($service_category->name) . ' ';
                                            }
                                        } else {
                                            echo 'No job type assigned';
                                        }
                                    ?>
    
                                    <p class="blog-desc hidden-content">
                                        <?php
                                        $full_desc = get_field('blog_desc');
                                        $excerpt_length = 18;
                                        if ($full_desc) {
                                            $short_desc = wp_trim_words($full_desc, $excerpt_length);
                                            echo '<p>' . esc_html($short_desc) . '</p>';
                                        } else {
                                            echo '<p>No description found.</p>';
                                        }
                                        ?>
                                    </p>
                                    <a href="<?php the_permalink(); ?>" class="blog-link read-more-btn" style="font-size:16px; font-weight:600;">View More</a>
                                </div>
                            </div>
                        <?php endwhile; ?>
                                <?php wp_reset_postdata(); ?>
                            <?php else : 
                            ob_get_clean();
                            return '';
                    endif; ?>
                </div>
                <!-- Pagination -->
                <div class="pagination">
                    <?php
                    echo paginate_links(array(
                        'total'     => $query->max_num_pages,
                        'current'   => $paged,
                        'format'    => '?paged=%#%',
                        'prev_text' => '« Prev',
                        'next_text' => 'Next »',
                        'type'      => 'plain',
                    ));
                    ?>
                </div>
            </div>
        </div>
        <?php
    return ob_get_clean();
}
add_action('wp_ajax_search_blog_posts_ajax', 'ajax_search_blog_posts_ajax');
add_action('wp_ajax_nopriv_search_blog_posts_ajax', 'ajax_search_blog_posts_ajax');
function ajax_search_blog_posts_ajax() {
    $search = sanitize_text_field($_POST['search_blog']);
    $paged  = isset($_POST['paged']) ? intval($_POST['paged']) : 1;
    $taxonomy = sanitize_text_field($_POST['taxonomy']);
    $tax_role = sanitize_text_field($_POST['tax_role']);
    $html = get_blog_post($paged, $search, $taxonomy, $tax_role);
    if(!empty($html)){
        wp_send_json_success(['message' => $html]);
     } else {
       wp_send_json_error(['message' => 'No post found']);
    } 
}

//-------------practice search------------------//

add_shortcode('search_practice', 'search_practice_shortcode');
function search_practice_shortcode() { ?>
    <div class="search-wrapper d-flex">
        <input type="text" id="search-prac" placeholder="Search Pages"/>
        <button type="submit" id="search-button">Search</button>
    </div>
    <div class="both-tax d-flex gap-5 ">
        <div class="taxonomy-front mt-4 w-50 ">
            <?php
                $terms = get_terms(array(
                    'taxonomy' => 'service_category', 
                    'hide_empty' => false,
                ));
                if (!empty($terms) && !is_wp_error($terms)) {
                    echo '<select id="taxonomy-prac">';
                    echo '<option value="">Select Category</option>';
                    foreach ($terms as $term) {
                        echo '<option value="' . esc_attr($term->slug) . '">' . esc_html($term->name) . '</option>';
                    }
                    echo '</select>';
                }
                ?>
        </div>
        <div class="role-front mt-4 w-50">
            <?php
                $terms = get_terms(array(
                    'taxonomy' => 'service_role', 
                    'hide_empty' => false,
                ));
                if (!empty($terms) && !is_wp_error($terms)) {
                    echo '<select id="prac-role">';
                    echo '<option value="">Select Role</option>';
                    foreach ($terms as $term) {
                        echo '<option value="' . esc_attr($term->slug) . '">' . esc_html($term->name) . '</option>';
                    }
                    echo '</select>';
                }
            ?>
        </div>
    </div>
    <div id="practice"></div>
    <div id="prac-blogpost-results">
        <?php  $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;   
        echo common_fun(null,null,null,$paged);  ?>
    </div>          
<?php }   


add_filter( 'posts_where', 'practice_search_where', 10, 2 );
function practice_search_where( $where, $wp_query ) {
    global $wpdb;
    if ( $search = $wp_query->get( 'practice_search' )){
        $where .= " AND(
            {$wpdb->posts}.post_title LIKE '%" . esc_sql( $wpdb->esc_like( $search ) ) . "%'
            OR EXISTS(
                SELECT 1 FROM {$wpdb->postmeta}
                WHERE {$wpdb->postmeta}.post_id = {$wpdb->posts}.ID
                AND {$wpdb->postmeta}.meta_key = 'blog_desc'
                AND {$wpdb->postmeta}.meta_value LIKE '%" . esc_sql( $wpdb->esc_like( $search ) ) . "%'
            )
        )";
    }
    return $where;
}

function common_fun($search_prac=null,$taxonomy_prac = null, $prac_role = null, $paged = 1){
    $query_args = array(
        'post_type' => 'blog',
        'posts_per_page' => 4,
        'post_status' => 'publish',
        'paged'  => $paged,
    );

    if(!empty($search_prac)){
    $query_args['practice_search'] = $search_prac;
    }

    if (!empty($taxonomy_prac) || !empty($prac_role)) {
            $query_args['tax_query'] = array(
                'relation' => 'OR',
            );
        if (!empty($taxonomy_prac)) {
            $query_args['tax_query'][] = array(
                array(
                    'taxonomy' => 'service_category',
                    'field'    => 'slug',
                    'terms'    => $taxonomy_prac,
                ),
            );
        }
        if (!empty($prac_role)) {
            $query_args['tax_query'][] = array(
                array(
                    'taxonomy' => 'service_role',
                    'field'    => 'slug',   
                    'terms'    => $prac_role,
                ),
            );
        }
    }
   $searching = new WP_Query($query_args);
        ob_start();  ?>
                <div class="container">
                    <div class="blog-container" style="margin-top:60px; max-width:1600px; margin:auto;">
                        <div class="blog-grid gap-4" style="margin-bottom:85px;"> <?php
                            if ($searching->have_posts()) :
                                while ($searching->have_posts()) : $searching->the_post();  ?>
                                    <div class="blog-card mb-5">
                                            <img src="<?php echo get_the_post_thumbnail_url(get_the_ID(), 'medium'); ?>" class="service-img" alt="<?php echo esc_attr(get_the_title()); ?>">
                                            <div class="blog-content">
                                                <p class="blog-title">
                                                    <?php
                                                    $blog_heading = get_field('blog_heading');
                                                    echo $blog_heading ? wp_kses_post($blog_heading) : 'Not specified';
                                                    ?>
                                                </p>
                                                <p class="" style="font-weight: bolder; background-color: rgb(247, 230, 230); color: #FF6575; border-radius: 20%;">
                                                    <?php
                                                    $service_category = get_the_terms(get_the_ID(), 'service_category');
                                                    if (!empty($service_category) && !is_wp_error($service_category)) {
                                                        foreach ($service_category as $service_category) {
                                                            echo esc_html($service_category->name) . ' ';
                                                        }
                                                    } 
                                                ?>
                                                </p>
                                                <?php
                                                    $service_category = get_the_terms(get_the_ID(), 'service_role');
                                                    if (!empty($service_category) && !is_wp_error($service_category)) {
                                                        foreach ($service_category as $service_category) {
                                                            echo esc_html($service_category->name) . ' ';
                                                        }
                                                    } 
                                                ?>
                
                                                <p class="blog-desc hidden-content">
                                                    <?php
                                                    $full_desc = get_field('blog_desc');
                                                    $excerpt_length = 18;
                                                    if ($full_desc) {
                                                        $short_desc = wp_trim_words($full_desc, $excerpt_length);
                                                        echo '<p>' . esc_html($short_desc) . '</p>';
                                                    }
                                                    ?>
                                                </p>
                                                <a href="<?php the_permalink(); ?>" class="blog-link read-more-btn" style="font-size:16px; font-weight:600;">View More</a>
                                        </div>
                                    </div>   
                                <?php endwhile; 
                                wp_reset_postdata();
                                    else : 
                                ob_get_clean();
                                return '';
                            endif; ?>
                        </div>
                            <!-- Pagination -->
                        <div class="prac-pagination">
                            <?php
                            echo paginate_links(array(
                                'total'     => $searching->max_num_pages,
                                'current'   => $paged,
                                'format'    => '?paged=%#%',
                                'prev_text' => '« Prev',
                                'next_text' => 'Next »',
                                'type'      => 'plain',
                            ));
                            ?>
                        </div>
                    </div>
                </div>
        <?php return ob_get_clean();
}  
           
add_action('wp_ajax_search_prac_posts', 'ajax_search_prac_posts');
add_action('wp_ajax_nopriv_search_prac_posts', 'ajax_search_prac_posts');

function ajax_search_prac_posts() {
    $search_prac = sanitize_text_field($_POST['searched']);
    $taxonomy_prac = sanitize_text_field($_POST['taxonomy_prac']);
    $prac_role = sanitize_text_field($_POST['prac_role']);
    $practice = common_fun($search_prac,$taxonomy_prac,$prac_role,$paged);
    if(!empty($practice)){
     wp_send_json_success(['practice' =>  $practice]);
    }
   else{
         wp_send_json_error(['practice' =>  "Not Post Found"]);
   }
}


// ace Blog section 

add_action('init', 'blog_post_type');  
function blog_post_type() {
    register_post_type('ace_blog', array(
        'label'        => 'Ace Blog',
        'public'       => true,
        'supports'     => array('title', 'thumbnail',),
        'has_archive'  => true,
        'rewrite'      => array('slug' => 'ace_blog'),
    ));

      register_post_type('ace_services', array(
        'label'        => 'Ace Services',
        'public'       => true,
        'supports'     => array('title','editor', 'thumbnail',),
        'has_archive'  => true,
        'rewrite'      => array('slug' => 'aservices'),
    ));


      register_taxonomy('categories','ace_blog', array(
        'label'        => 'Categories',
        'hierarchical' => true, 
        'rewrite'      => array('slug' => 'blog-category'),
        'show_admin_column' => true, 
        ));

}
add_shortcode('blog_shortcode','blog_posts_shortcode');
function blog_posts_shortcode($atts){

    $blogType = isset($atts['blog_type']) ? $atts['blog_type'] : 'recent-posts';
    $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

    $blog_args = array(
    'post_type'      => 'ace_blog', 
    'posts_per_page' =>  3,
    'post_status'    => 'publish',  
    'order'          => 'DESC',
    );

    if ($blogType == 'recent-posts') {
        $blog_args['paged'] = $paged;
    }

    $blog_query = new WP_Query($blog_args); 
    $total_pages = isset($blog_query->max_num_pages) ? $blog_query->max_num_pages : 1;

	ob_start();
	?>
        <div class="blog-section">
            <div class="blog-posts blog-slick-slider">
                <?php
                if ($blog_query->have_posts()) :
                    while ($blog_query->have_posts()) : $blog_query->the_post(); ?>    
                        <div class="blog-content">
                            <div class="blog-img">
                            <?php  the_post_thumbnail('full'); ?>
                                <h6><?php echo str_replace(' ', '<br>', get_the_date('j F')); ?></h6>
                            </div>
                            <div class="blog-inner-text">
                                <h2><?php echo  wp_trim_words(get_the_title() , 5, '...'); ?></h2>
                                <?php  $content = get_the_content();
                                    $limit_words = wp_trim_words($content , 15, '...');
                                    echo '<p>'.  $limit_words.  '</p>'; ?>
                                <a href="<?php echo get_the_permalink(); ?>">Read More <i class="fa-solid fa-plus"></i></a>
                            </div>
                        </div>
                    <?php endwhile;
                endif; 
                    wp_reset_postdata();
                ?>
            </div>

            <?php if( $blogType == 'recent-posts' && $total_pages > 1) : ?>
                <div class="pagination">
                    <?php
                    echo paginate_links(array(
                        'base' => get_pagenum_link(1) . '%_%',
                        'total'     => $total_pages,
                        'current'   => $paged,
                        'format'    => '?paged=%#%',
                        'prev_text' => '« Prev',
                        'next_text' => 'Next »',
                        'type'      => 'plain',
                    ));

                    ?>
                </div>
                <?php endif;?>
        </div>   
	<?php
 	return ob_get_clean();
}
    

// ajax search ace

add_action('wp_ajax_search_blog_posts', 'ajax_search_blog_posts');
add_action('wp_ajax_nopriv_search_blog_posts', 'ajax_search_blog_posts');
function ajax_search_blog_posts() {
    $search_word = sanitize_text_field($_POST['search_word']);
    ob_start();
        $args = array(
        'post_type'      => 'ace_blog', 
        'posts_per_page' =>  3,
        'post_status'    => 'publish',  
        'order'          => 'DESC',
        's'              =>  $search_word,
        ); 
        $blog_query = new WP_Query($args); 
            if ($blog_query->have_posts()) :
                while ($blog_query->have_posts()) : $blog_query->the_post(); ?>  
                    <div class="more blogs-post-content">
                        <a href="<?php the_permalink(); ?>"> <?php the_title();  ?></a>
                        <p> <img src="images/blog-sinle-calender.svg" alt="">
                        <?php   echo get_the_date(); ?>  </p>
                    </div>
                <?php endwhile;
                wp_reset_postdata();
                else :
                wp_send_json_error(['ace_result' => 'No Posts Found']);
            endif; ?>                                                    
    <?php $ace_result = ob_get_clean();
    wp_send_json_success(['ace_result' => $ace_result]);
}


// ace services
add_shortcode('services_shortcode', 'services_fetch_shortcode');

function services_fetch_shortcode($atts) {
    $atts = shortcode_atts(
        array(
            'limit' => 6, 
        ),
        $atts,
        'services_shortcode'
    );

    $posts_limit = intval($atts['limit']);

    $ace_args = array(
        'post_type'      => 'ace_services',
        'posts_per_page' => $posts_limit,
        'order'          => 'DESC',
        'post_status'    => 'publish',
    );

    $ace_query = new WP_Query($ace_args);
    ob_start();
 ?>
    <div class="services-card-wrappers">
        <div class="services card-box"> 
            <?php if ($ace_query->have_posts()) : ?>
            <?php while ($ace_query->have_posts()) : $ace_query->the_post(); 
            $title = get_the_title(); 
            $service_icon = get_field('service_icon', get_the_ID());
            $icon_hover = get_field('icon_hover', get_the_ID());
            $description_acf = get_field('services_description');
            $description = wp_trim_words($description_acf, 20, '...');
               
            if($posts_limit==4){ ?>
            <div class="service-section">
                <div class="container">
                    <div class="row align-items-center gy-5">
                        <div class="service-card text-start p-4 h-100">
                            <div class="icon mb-3">
                                <?php if ($service_icon): ?>
                                    <img src="<?php echo esc_url($service_icon); ?>" alt="Service Icon" class="icon-default">
                                <?php endif; ?>

                                <?php if ($icon_hover): ?>
                                    <img src="<?php echo esc_url($icon_hover); ?>" alt="Hover Icon" class="icon-hover">
                                <?php endif; ?>
                                </div>
                            <div class="service-title"><?php echo esc_html($title); ?></div>
                            <div class="service-description"><?php echo esc_html($description); ?></div>
                            <a href="#" class="arrow-btn"><i class="fa-solid fa-arrow-right"></i></a>
                        </div>                   
                    </div>
                </div>
            </div> 

            <?php } else { ?>
            <div class="service-box-card">
                <div class="icon-box">
                    <?php if ($service_icon): ?>
                    <img src="<?php echo esc_url($service_icon); ?>" alt="Service Icon">
                    <?php endif; ?>
                </div>
                <div class="service-title"><?php echo esc_html($title); ?></div>
                <div class="service-description"><?php echo esc_html($description); ?></div>
                <a href="<?php the_permalink(); ?>" class="read-more">
                Read More <img src="images/services-arrow.png" alt="Arrow Icon"></a>  
            </div> 
            <?php   }
            endwhile; ?>
                <?php wp_reset_postdata(); ?>
            <?php else : ?>
                <p>No services found.</p>
            <?php endif; ?>
        </div>
    </div>
    <?php return ob_get_clean();
 
}


//find the id in hook 
add_action('wp', function () {
    if (is_singular('about')) {
        global $post, $linked_post_id_from_url;
        $about_url = get_field('about_url', $post->ID);
        $linked_post_id_from_url = get_post_id_from_custom_url($about_url);
    }
});

function get_post_id_from_custom_url($url) {
    if (empty($url)) return 0;

    $parsed_url = wp_parse_url($url);
    $allowed_post_types = get_post_types(['public' => true]);

    if (!empty($parsed_url['query'])) {
        parse_str($parsed_url['query'], $query_vars);

        if (!empty($query_vars['p'])) {
            return (int)$query_vars['p'];
        }

        if (!empty($query_vars['page_id'])) {
            return (int)$query_vars['page_id'];
        }

        if (!empty($query_vars['slug']) && !empty($query_vars['post_type'])) {
            $slug = sanitize_title($query_vars['slug']);
            $post_type = sanitize_title($query_vars['post_type']);

            if (in_array($post_type, $allowed_post_types, true)) {
                $post = get_page_by_path($slug, OBJECT, $post_type);
                return $post ? $post->ID : 0;
            }
        }
        foreach ($allowed_post_types as $pt) {
            if (!empty($query_vars[$pt])) {
                $slug = sanitize_title($query_vars[$pt]);
                $post = get_page_by_path($slug, OBJECT, $pt);
                return $post ? $post->ID : 0;
            }
        }
    }
    if (!empty($parsed_url['path'])) {
        $path_parts = explode('/', trim($parsed_url['path'], '/'));
        $slug = end($path_parts);
        if (!empty($slug)) {
            $post = get_page_by_path($slug, OBJECT, $allowed_post_types);
            return $post ? $post->ID : 0;
        }
    }
    return 0;
}

// Bulk action for update status in blog

function register_custom_bulk_update_action( $bulk_actions ) {
    $bulk_actions['custom_update_action'] = __( 'Update Status', 'textdomain' );
    return $bulk_actions;
}
add_filter( 'bulk_actions-edit-blog', 'register_custom_bulk_update_action' );


function handle_custom_bulk_update_action( $redirect_to, $doaction, $post_ids ) {
    if ( $doaction == 'custom_update_action' ) {
        foreach ( $post_ids as $post_id ) {
            wp_update_post( array(
                'ID'          => $post_id,
                'post_status' => 'pending', 
            ));
            update_field( 'blog_status', 'deactive', $post_id ); 
        }
        $redirect_to = add_query_arg( 'bulk_action_done', count( $post_ids ), $redirect_to ); // Display a confirmation message
    }
    return $redirect_to;
}
add_filter( 'handle_bulk_actions-edit-blog', 'handle_custom_bulk_update_action', 10, 3 ); 
function custom_bulk_update_admin_notice() {
    if ( ! empty( $_REQUEST['bulk_action_done'] ) ) {
        $num_updated = (int) $_REQUEST['bulk_action_done'];
        printf( '<div id="message" class="updated notice is-dismissable"><p>' . __( 'Updated %d posts successfully.', 'textdomain' ) . '</p></div>', $num_updated );
    }
}
add_action( 'admin_notices', 'custom_bulk_update_admin_notice');



add_filter('manage_blog_posts_columns', 'add_acf_status_column');
function add_acf_status_column($columns) {
    $columns['acf_status'] = __('Status', 'your_text_domain');
    return $columns;
}
add_action('manage_blog_posts_custom_column', 'populate_acf_status_column', 10, 2);

function populate_acf_status_column($column_name, $post_id) {
    if ($column_name == 'acf_status') {
        $status = get_field('blog_status', $post_id);
        echo ucfirst($status); 
    }
}

// sort the column according the requirement in blog
add_filter('manage_blog_posts_columns', 'custom_reorder_blog_columns');

function custom_reorder_blog_columns($columns) {
    $new_columns = [];

    if (isset($columns['cb'])) {
        $new_columns['cb'] = $columns['cb'];
    }

    if (isset($columns['title'])) {
        $new_columns['title'] = $columns['title'];
    }

    $new_columns['acf_status'] = __('Status', 'your_text_domain');

    foreach ($columns as $key => $value) {
        if (!isset($new_columns[$key])) {
            $new_columns[$key] = $value;
        }
    }
    return $new_columns;
}
