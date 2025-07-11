//search practice
<?php
add_shortcode('search_practice','search_practice_shortcode');
    function search_practice_shortcode(){
    ?>
        <div class="search-wrapper d-flex">
            <input type="text" id="search-prac" placeholder="Search Pages" />
            <button type="submit" id="search-button">Search</button>
        </div>
        <div id="practice"></div> 
        <div id="prac-blogpost-results">
        <?php
          echo sanjha();
         echo '</div>';
}

//new common funtion

function sanjha($search=null){
       $search_args = array(
            'post_type' => 'blog',
            'post_status' => 'publish',
            'posts_per_page' => -1,
                'meta_query' => array(
                        array(
                        'key' => 'blog_status',
                        'value' => 'active',
                        'compare' => '=',
                        ),
                ),     
        );
         if(!empty($search)){
          $search_args['s'] = $search;
         }
        $searched = new WP_Query($search_args);
                ob_start();
                ?>
            <div class="container">   
                    <div class="blog-container" style="margin-top:60px; max-width:1600px; margin:auto;">
                        <div class="blog-grid gap-4" style="margin-bottom:85px;">
                            <?php
                        if($searched->have_posts()) :{
                            while ( $searched->have_posts()) : $searched->the_post();{ ?>
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
                                        
                            <?php } endwhile; 
                        }  endif; ?>
                        </div>
                    </div>
                    </div>
            </div>
            <?php
            return ob_get_clean();
         wp_reset_postdata();
}
    add_action('wp_ajax_search_prac_posts','ajax_search_prac_posts');
    add_action('wp_ajax_nopriv_search_prac_posts', 'ajax_search_prac_posts');
function ajax_search_prac_posts() {
            $search_prac = sanitize_text_field($_POST['search_prac']);
            $html = sanjha($search_prac);
            if(!empty($html)){
             wp_send_json_success(['practice' => $html]);
            }
            else{
            wp_send_json_error(['practice' => 'No post found.']);
            }
}

?>

    <!-- search practice -->
   <script>
      jQuery(document).ready(function($) {
       $('#search-button').on("click", function(e) {
        e.preventDefault();
          let searchPrac = $('#search-prac').val();
           console.log(searchPrac);  
                $.ajax({
                    url: ajax_object.ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'search_prac_posts',
                        search_prac: searchPrac,
                    },
                    success: function (response) {
                          console.log(response.data.practice);
                        if (response.success) {
                            $('#prac-blogpost-results').html(response.data.practice);
                            $('#practice').html('');
                        }
                        else{
                            $('#prac-blogpost-results').html('');
                            $('#practice').html(response.data.practice);
                          
                        }
                    }
                });
            });

         })
         <script>