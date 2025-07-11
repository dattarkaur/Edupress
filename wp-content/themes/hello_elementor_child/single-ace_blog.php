<?php get_header(); ?>

<section class="inner-banner">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="inner-banner-content">
                    <h1> <?php the_title(); ?></h1>    
                    <p><?php echo get_field('sub_title');?></p>
            </div>
        </div>
    </div>
</section>

<section class="single-blogs">
    <div class="container">
        <div class="row">
            <div class="col-xl-8">
                <div class="single-blog">
                    <div class="blog-inner"> <?php the_post_thumbnail();?></div>
                        <div class="single-blog-iteams">
                            <div class="single-blog-icones">
                                <p> <i class="fa-regular fa-user"></i> Rakesh</p>
                            </div>
                            <div class="single-blog-icones">
                                <p> <img src="images/blog-sinle-calender.svg" alt=""><?php echo get_the_date();?></p>
                            </div>
                                <p><?php
                                    $terms = get_the_terms( $post->ID , 'categories' );
                                    foreach ( $terms as $term ) {
                                    echo $term->name;
                                    }
                                    ?>
                                </p>

                            <div class="single-blog-icones">
                                <p> <i class="fa-regular fa-comment"></i> Comments</p>
                            </div>
                        </div>
                        <div class="single-blog-content">
                            <h3><?php the_title();  ?></h3>
                            <p> <?php echo get_field('single_desc'); ?> </p>
                        </div>
                </div>
            </div>
            <div class="col-xl-4">
                <div class="right-bar-blog">
                    <div class="serch-box">
                        <h6>Search Here</h6>
                        <input type="text" id="ajax-blog-search" placeholder="Search..">
                    </div> 
                    <div class="more blogs-post">
                        <h5>Popular Post</h5>
                        <?php
                        $args = array(
                        'post_type'      => 'ace_blog', 
                        'posts_per_page' =>  3,
                        'post_status'    => 'publish',  
                        'order'          => 'DESC',
                        );
                        $blog_query = new WP_Query($args); ?>
                        <div class="ace_result"></div>
                            <div id="ajax_post_result">
                                <?php if ($blog_query->have_posts()) :
                                    while ($blog_query->have_posts()) : $blog_query->the_post(); ?>  
                                        <div class="more blogs-post-content">
                                            <a href="<?php the_permalink(); ?>"> <?php the_title();  ?></a>
                                            <p> <img src="images/blog-sinle-calender.svg" alt="">
                                            <?php  echo get_the_date(); ?>  </p>
                                        </div>
                                    <?php endwhile;
                                    else:
                                     echo "No posts related this";
                                endif;    ?>
                          
                            </div>
                        </div>
                        <div class="blog-side-bar-outer">
                            <h5>Trending topic</h5>
                            <div class="blog-side-bar-tabs">
                                <ul>
                                    <?php $terms = get_terms(array(
                                        'taxonomy' => 'categories',
                                        'hide_empty' => false,
                                        'orderby'    => 'count',
                                        'order' =>  'DESC',
                                    ));
                                    foreach ($terms as $term){
                             echo '<li> <a href="' . get_term_link($term) . '">' . esc_html($term->name) .'</a> </li>';}
                                    ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>  

<?php get_footer(); ?>
   