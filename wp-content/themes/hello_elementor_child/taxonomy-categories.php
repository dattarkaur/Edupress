<?php
get_header(); ?>

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
    </div>
</section>

<?php $term = get_queried_object();

    $args = array( 
        'post_type'      => 'ace_blog',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
        'orderby'        => 'date',
        'order'          => 'DESC',
        'tax_query' => [
            [
                'taxonomy' => $term->taxonomy,
                'field'    => 'slug',
                'terms'    => $term->slug,
            ],
        ],
    );
    $blog_query = new WP_Query($args);
?>

<section class="blog-section">
    <div class="container">
        <?php if ($blog_query->have_posts()) : ?>
        <div class="row blog-posts">
            <?php while ($blog_query->have_posts()) : $blog_query->the_post(); ?>
                <div class="col-md-4 blog-content">
                    <div class="blog-img">
                        <?php the_post_thumbnail('full'); ?>
                        <h6><?php echo str_replace(' ', '<br>', get_the_date('j F')); ?></h6>
                    </div>
                    <div class="blog-inner-text">
                        <h2><?php echo wp_trim_words(get_the_title(), 5, '...'); ?></h2>
                        <p><?php echo wp_trim_words(get_the_content(), 15, '...'); ?></p>
                        <a href="<?php the_permalink(); ?>">Read More <i class="fa-solid fa-plus"></i></a>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
        <?php else : ?>
        <p>No posts found related this category.</p>
        <?php endif; ?>
        <?php wp_reset_postdata(); ?>
    </div>
</section>

<?php get_footer(); ?>
