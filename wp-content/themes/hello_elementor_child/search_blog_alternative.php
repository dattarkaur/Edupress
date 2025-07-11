// Shortcode for Navbar Search Bar
add_shortcode('navbar_page_search', 'navbar_page_search_shortcode');
function navbar_page_search_shortcode() {
    ob_start();
       ?>
        <div class="navbar-search-wrapper d-flex">
            <input type="text" id="navbar-search" placeholder="Search Pages" />
            <button type="submit" id="navbar-search-button">Search</button>
            <div id="navbar-search-results"></div> <!-- Moved below input field -->
        </div>
     <?php
    return ob_get_clean();
}

// AJAX Handler for Page Search
add_action('wp_ajax_navbar_page_search', 'handle_navbar_page_search');
add_action('wp_ajax_nopriv_navbar_page_search', 'handle_navbar_page_search');
function handle_navbar_page_search() {
    $query = sanitize_text_field($_POST['query']);

    $args = [
        'post_type' => 'page',
        'post_status' => 'publish',
        's' => $query,
        'posts_per_page' => 5,
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
    }
    wp_send_json($results);
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

// fetch blog page services
add_shortcode('fetch_blog_services', 'fetch_blog_services_shortcode');
function fetch_blog_services_shortcode(){
    ob_start();
  ?>  <div class="container" id="ajax-blogpost-results"><?php
     $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
            $args = array(
                'post_type'      => 'blog', 
                'posts_per_page' => 4,
                'paged'          => $paged, 
                'post_status'    => 'publish',  
                'order'          => 'ASC',
                'meta_query' => array(
                array(
                    'key'     => 'blog_status',
                    'value'   => 'active',
                    'compare' => '=',
                )
            ));
      $service_query = new WP_Query($args);
      ?>
        <div class="container">
            <div class="blog-container" style="margin-top:60px; max-width:1600px; margin:auto;">
                <div class="blog-grid gap-4" style="margin-bottom:85px;">
                    <?php
                    if ($service_query->have_posts()) :
                        while ($service_query->have_posts()) : $service_query->the_post();
                    ?>
                    <!-- blog Structure -->
                    <div class="blog-card mb-5 ">
                        <img src="<?php echo get_the_post_thumbnail_url(get_the_ID(), 'medium'); ?>" class="service-img" alt="<?php echo esc_attr(get_the_title()); ?>">
                        <div class="blog-content">
                            <p class="blog-title">
                                <?php
                                $blog_heading = get_field('blog_heading');
                                echo $blog_heading ? wp_kses_post($blog_heading) : 'Not specified';
                                ?>
                            </p>
                            <p class="blog-desc hidden-content">
                                <?php
                                    $full_desc = get_field('blog_desc'); // Fetch ACF field
                                    $excerpt_length = 18; // Limit to 20 words

                                    if ($full_desc) {
                                        $short_desc = wp_trim_words($full_desc, $excerpt_length);
                                        echo '<p>' . $short_desc . '</p>';
                                    } else {
                                        echo '<p>Content not found.</p>';
                                    }
                                ?>
                            </p>
                            <a href="<?php the_permalink(); ?>" class="blog-link read-more-btn" style="font-size:16px; font-weight:600;">View More</a>
                        </div>
                    </div>
                              
                    <?php 
                        endwhile;
                        wp_reset_postdata();
                    else :
                        echo '<p>No posts found.</p>';
                    endif;
                    ?>
                    <?php
                      echo '<div class="pagination">';
                        echo paginate_links(array(
                            'total'   => $service_query->max_num_pages,
                            'current' => $paged,
                            'prev_text' => '« Prev',
                            'next_text' => 'Next »',
                        ));
                        echo '</div>';
                        ?>
      </div>   
            </div>
        </div>
        </div>
      <?php   
    return ob_get_clean(); // Return the buffered content
}

//  blog service page search bar 
add_shortcode('blog_searchbox', 'custom__search_box'); 
function custom__search_box() {
    ob_start();
    ?>
      <form class="search-box" method="get" id="ajax-blogpost-search-btn">
          <div class="search-container d-flex">
        <input type="text" name="blog-search" id="ajax-blogpost-search" placeholder="Search..." />
        <button type="submit" class="blog-btn">Search</button>
           </div>
      </form>                                    
    <?php
    return ob_get_clean();
}

function custom_search_where( $where, $wp_query ) {
    global $wpdb;
    if ( $search = $wp_query->get( 'custom_search' ) ) {
        $where .= " AND (
            {$wpdb->posts}.post_title LIKE '%" . esc_sql( $wpdb->esc_like( $search ) ) . "%'
            
            OR EXISTS (
                SELECT 1 FROM {$wpdb->postmeta}
                WHERE {$wpdb->postmeta}.post_id = {$wpdb->posts}.ID
                AND {$wpdb->postmeta}.meta_key = 'blog_desc'
                AND {$wpdb->postmeta}.meta_value LIKE '%" . esc_sql( $wpdb->esc_like( $search ) ) . "%'
            )
        )";
    }
    return $where;
}
add_filter( 'posts_where', 'custom_search_where', 10, 2 );

add_action('wp_ajax_search_blog_posts_ajax', 'ajax_search_blog_posts_ajax');
add_action('wp_ajax_nopriv_search_blog_posts_ajax', 'ajax_search_blog_posts_ajax');

function ajax_search_blog_posts_ajax() {
    $search = sanitize_text_field($_POST['search_blog']);
    $paged = isset($_POST['paged']) ? intval($_POST['paged']) : 1;

    $posts_by_title = [];
    $posts_by_desc = [];

    // First query: Search in title
    $title_query = new WP_Query([
        'post_type' => 'blog',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        's' => $search,
        'meta_query' => [
            [
                'key' => 'blog_status',
                'value' => 'active',
                'compare' => '='
            ]
        ]
    ]);

    if ($title_query->have_posts()) {
        foreach ($title_query->posts as $post) {
            $posts_by_title[] = $post->ID;
        }
    }

    // Second query: Search in blog_desc custom field
    $desc_query = new WP_Query([
        'post_type' => 'blog',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'meta_query' => [
            'relation' => 'AND',
            [
                'key' => 'blog_status',
                'value' => 'active',
                'compare' => '='
            ],
            [
                'key' => 'blog_desc',
                'value' => $search,
                'compare' => 'LIKE'
            ]
        ]
    ]);

    if ($desc_query->have_posts()) {
        foreach ($desc_query->posts as $post) {
            if (!in_array($post->ID, $posts_by_title)) {
                $posts_by_desc[] = $post->ID;
            }
        }
    }

    // Merge both arrays and paginate
    $final_post_ids = array_merge($posts_by_title, $posts_by_desc);

    $paged_ids = array_slice($final_post_ids, ($paged - 1) * 4, 4);
    $total_pages = ceil(count($final_post_ids) / 4);

    // Final query to fetch post data
    $final_query = new WP_Query([
        'post_type' => 'blog',
        'post__in' => $paged_ids,
        'orderby' => 'post__in',
        'posts_per_page' => 4,
    ]);
    ob_start();
    ?>
    <div class="container">
        <div class="blog-container" style="margin-top:60px; max-width:1600px; margin:auto;">
            <div class="blog-grid gap-4" style="margin-bottom:85px;">
                <?php if ($final_query->have_posts()) : while ($final_query->have_posts()) : $final_query->the_post(); ?>
                    <div class="blog-card mb-5">
                        <img src="<?php echo get_the_post_thumbnail_url(get_the_ID(), 'medium'); ?>" class="service-img" alt="<?php echo esc_attr(get_the_title()); ?>">
                        <div class="blog-content">
                            <p class="blog-title">
                                <?php
                                $blog_heading = get_field('blog_heading');
                                echo $blog_heading ? wp_kses_post($blog_heading) : 'Not specified';
                                ?>
                            </p>
                            <p class="blog-desc hidden-content">
                                <?php
                                $full_desc = get_field('blog_desc');
                                $excerpt_length = 18;
                                if ($full_desc) {
                                    echo '<p>' . wp_trim_words($full_desc, $excerpt_length) . '</p>';
                                } else {
                                    echo '<p>Content not found.</p>';
                                }
                                ?>
                            </p>
                            <a href="<?php the_permalink(); ?>" class="blog-link read-more-btn" style="font-size:16px; font-weight:600;">View More</a>
                        </div>
                    </div>
                <?php endwhile; wp_reset_postdata(); else : ?>
                    <p>No posts found.</p>
                <?php endif; ?>
            </div>
            <div class="pagination">
                <?php
                echo paginate_links([
                    'total' => $total_pages,
                    'current' => $paged,
                    'prev_text' => '« Prev',
                    'next_text' => 'Next »',
                ]);
                ?>
            </div>
        </div>
    </div>
    <?php
    $response = ob_get_clean();
    echo $response;
    wp_die();
}
