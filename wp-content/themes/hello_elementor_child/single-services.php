
<?php
get_header();
?>
<section class="featured-banner">
    <div class="container-fluid" >
        <div class="row">
            <div class="col-md-12">
                <img src="<?php echo get_the_post_thumbnail_url($post_id, 'large'); ?>" 
                     style="width: 100%; height: 500px; object-fit:cover; display: block;" 
                     class="rounded mb-3" 
                     alt="<?php the_title(); ?>"> 
            </div>
        </div>
    </div>
</section>

<section class="single-service-content"  >
    <div class="container" >
        <div class="row">
            <div class="col-md-12 col-lg-8 ">
                <div class="left-content">
                    <h4 class="hading">
                      <?php echo get_field('service_heading'); ?>
                    </h4>
                    <?php echo get_field('service_date'); ?>

                    <div class="service-description">
                        <?php echo get_field('service_desc'); ?>
                    </div>
                </div>
            </div>

            <div class="col-md-12 col-lg-4 ">
                <div class="right-content">
                    <div class="add-form" >
                        <h3>Admission Form</h3>
                        <p>Explore our academic programs and enroll today for a brighter future.</p>
               <?php echo do_shortcode('[contact-form-7 id="5f9b697" title="posts"]'); ?>

                        <!-- <form>
                            <input type="text" placeholder="Full Name" required>
                            <input type="email" placeholder="Email Address" required>
                            <input type="tel" placeholder="Phone Number" required>
                            <input type="number" placeholder="Enter Class" required>
                            <select required>
                                <option value="" disabled selected>Select Gender</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                            </select>
                            <input type="date" placeholder="Date of Birth" required>
                            <textarea placeholder="Your Query" required></textarea>
                            <button type="submit">Submit</button>
                        </form> -->

                        <div class="custom-posts " style="margin-top:80px;">
                            <h3 class="text-center mb-5">Recent Posts</h3>
                            <?php
                            $args = array(
                                'post_type'      => 'services',
                                'posts_per_page' => -1,
                                  'order' => 'DESC', 
                                 
                            );
                            $query = new WP_Query($args);
                            if ($query->have_posts()) :
                                while ($query->have_posts()) : $query->the_post(); ?>
                                    <a href="<?php the_permalink(); ?>" style="text-decoration: none; color: inherit;">
                                        <div class="custom-post-item d-flex gap-3 mb-4 mt-4">
                                            <div class="image">
                                                <?php if (has_post_thumbnail()) : ?>
                                                    <img src="<?php echo get_the_post_thumbnail_url(get_the_ID(), 'medium'); ?>" 
                                                         style="width: 50px; height: 50px; object-fit:cover; display: block;" 
                                                         class="rounded-circle mb-3" 
                                                         alt="<?php the_title(); ?>">
                                                <?php endif; ?>
                                            </div>
                                            <div class="the-content">
                                                <h5>
                                                    <?php
                                                    $service_heading = get_field('service_heading');
                                                    echo $service_heading ? $service_heading : "Content Not Found";
                                                    ?>
                                                </h5>
                                                <img class="me-3" src="<?php echo get_stylesheet_directory_uri(); ?>/images/Mask.png" alt="Web Logo">
                                                <?php
                                                $service_date = get_field('service_date');
                                                echo $service_date;
                                                ?>
                                            </div>
                                        </div>
                                        <hr>
                                    <?php endwhile;
                                    wp_reset_postdata();
                                    else :
                                        echo '<p>No updates available.</p>';
                                    endif;
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
get_footer();
?>



