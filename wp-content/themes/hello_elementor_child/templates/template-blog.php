<?php
/* Template Name: Blog Posts  */

get_header();

//  Blog section 
    
function Blog_Section(){
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
    $blog_args = array(
    'post_type'      => 'ace_blog',
    'posts_per_page' => 3,
    'paged'          => $paged,
    'post_status'    => 'publish',  
    'order'          => 'DESC',
   
    );
    $blog_query = new WP_Query($blog_args); ?>
        <div class="blog-section">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <h4 class="subtitle"></h4>
                        <h2 class="title">LATEST BLOG POSTS</h2>
                    </div>
                    <div class="blog-posts">
                        <?php
                        if ($blog_query->have_posts()) :
                            while ($blog_query->have_posts()) : $blog_query->the_post(); ?>  
                                <div class="blog-content">
                                    <div class="blog-img">
                                    <?php  the_post_thumbnail('full'); ?>
                                       <h6><?php echo str_replace(' ', '<br>', get_the_date('j F')); ?></h6>
                                    </div>
                                    <div class="blog-inner-text">
                                        <?php  $title = get_the_title(); 
                                        $limit_title = wp_trim_words($title , 5, '...');
                                        echo '<h2>'.  $limit_title.  '</h2>';
                                        $content = get_the_content(); 
                                        $limit_words = wp_trim_words($content , 10, '...');
                                        echo '<p>'.  $limit_words.  '</p>';
                                        ?> 
                                        <a href="#">Read More <i class="fa-solid fa-plus"></i></a>
                                    </div>
                                </div>
                            <?php endwhile;  ?>
                            <?php wp_reset_postdata(); 
                        endif; ?>  
                    </div>
                </div>
            </div>
             <!--  Pagination -->
                        <div class="pagination">
                            <?php
                            echo paginate_links(array(
                                'total'     => $blog_query->max_num_pages,
                                'current'   => $paged,
                                'format'    => '?paged=%#%',
                                'prev_text' => '« Prev',
                                'next_text' => 'Next »',
                                'type'      => 'plain',
                            ));
                            ?>
                        </div>
        </div>
  <?php    
}
Blog_Section(); 
get_footer();
?>