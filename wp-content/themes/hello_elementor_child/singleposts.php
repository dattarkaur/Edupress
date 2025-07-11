

<?php
get_header(); 
if (have_posts()) :
    while (have_posts()) : the_post(); ?>
          <!-- Featured Image -->
            <?php if (has_post_thumbnail()) : ?>
<img src="<?php echo get_the_post_thumbnail_url(get_the_ID(), 'large'); ?>" style="width: 100%; height: 500px; object-fit:cover; display: block;" class="rounded mb-3" alt="<?php the_title(); ?>">            <?php endif; ?>
<div class="container d-flex mt-5  "style="gap:90px;">
    <div class="full-desc" style="width:70%;">  
       <h2 class="fw-bold heading" style="color:#392C7D; font-family:Quicksand";>
                <?php
                $service_heading =get_field('service_heading');
                if ($service_heading){
                    echo $service_heading;  
                }
                else{
                    echo "Content Not Found";
                }
               
                ?>
            </h2>
             <div class="date fs-5 fw-bold">
            <?php
             $service_date = get_field('service_date');
                echo $service_date;
                ?>
                </div>

         <div class="post-content fs-5 mt-4 fw-medium" style="font-family:Quicksand";>
           <?php
            $service_desc = get_field('service_desc');
            if ($service_desc) {
                echo '<div>' . $service_desc . '</div>'; // Displays content with HTML tags properly
            } else {
                echo '<p>No content found.</p>';
            }
            ?>
         </div>
         <div class="elements mt-5 ">
            <h4>Given below are some of the key elements of modern classroom design-</h4>
            <ul style="font-family:quicksand;" class="list fs-5 fw-medium mt-5">
                <li>Flexible furniture and adaptable space</li>
                <li>Spaces for collaboration and independent study</li>
                <li>Encouraging movement</li>
                <li>Fostering inspiration and creativity</li>
                <li>Technology integration</li>
            </ul>
         </div>

         <div class="element-content mb-5">
            <h3 class="element-head mt-5">
             <?php
            $heading_1 = get_field('heading_1');
            if ($heading_1) {
                echo '<div>' . $heading_1 . '</div>'; // Displays content with HTML tags properly
            } else {
                echo '<p>No content found.</p>';
            }
            ?>
            </h3>
              <div class="element-desc mt-5">
                <?php
                $desc_1 = get_field('desc_1');
                echo $desc_1;
                ?>
            </div>

             <h3 class="element-head  mt-5">
             <?php
            $heading_2 = get_field('heading_2');
            if ($heading_2) {
                echo '<div>' . $heading_2 . '</div>'; // Displays content with HTML tags properly
            } else {
                echo '<p>No content found.</p>';
            }
            ?>
            </h3>
              <div class="element-desc mt-5">
                <?php
                $desc_2 = get_field('desc_2');
                echo $desc_2;
                ?>
            </div>

             <h3 class="element-head  mt-5">
             <?php
            $heading_3 = get_field('heading_3');
            if ($heading_3) {
                echo '<div>' . $heading_3 . '</div>'; // Displays content with HTML tags properly
            } else {
                echo '<p>No content found.</p>';
            }
            ?>
            </h3>
              <div class="element-desc mt-5">
                <?php
                $desc_3 = get_field('desc_3');
                echo $desc_3;
                ?>
            </div>

            <!-- 4 -->
              <h3 class="element-head  mt-5">
             <?php
            $heading_4 = get_field('heading_4');
            if ($heading_4) {
                echo '<div>' . $heading_4 . '</div>'; // Displays content with HTML tags properly
            } else {
                echo '<p>No content found.</p>';
            }
            ?>
            </h3>
              <div class="element-desc mt-5">
                <?php
                $desc_4 = get_field('desc_4');
                echo $desc_4;
                ?>
            </div>
<!-- 5 -->
  <h3 class="element-head mt-4">
             <?php
            $heading_5 = get_field('heading_5');
            if ($heading_5) {
                echo '<div>' . $heading_5 . '</div>'; // Displays content with HTML tags properly
            } else {
                echo '<p>No content found.</p>';
            }
            ?>
            </h3>
              <div class="element-desc mt-5">
                <?php
                $desc_5 = get_field('desc_5');
                echo $desc_5;
                ?>
            </div>

         </div>
                </div>
<div class="add-form" style="width:30%; min-height: auto;">
                    <h1>Admission Form</h1>
        <p>Explore our academic programs and enroll today for a brighter future.</p>
        <form>
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
        </form>
          <div class="custom-posts" style="margin-top:120px;">

    <?php
    $args = array(
        'post_type'      => 'services', // Replace with your actual custom post type
        'posts_per_page' => -1, // Fetch up to 5 posts
    );

    $query = new WP_Query($args);

    if ($query->have_posts()) :
        while ($query->have_posts()) : $query->the_post(); ?>
                    <a href="<?php the_permalink(); ?>" style="text-decoration: none; color: inherit;">
            <div class="custom-post-item d-flex gap-5 mt-5 mb-5">
                <div class="image">
                   <?php if (has_post_thumbnail()) : ?>
<img src="<?php echo get_the_post_thumbnail_url(get_the_ID(), 'medium'); ?>" style="width: 50px; height: 50px; object-fit:cover; display: block;" class="rounded-circle mb-3" alt="<?php the_title(); ?>"><?php endif; ?></div>
               <div class="the-content ">
               <h5> <?php
                $service_heading =get_field('service_heading');
                if ($service_heading){
                    echo $service_heading;  
                }
                else{
                    echo "Content Not Found";
                }
                ?></h5>
              
           <img class="me-3" src="<?php echo get_stylesheet_directory_uri(); ?>/images/Mask.png" alt="Web Logo">

                <?php
             $service_date = get_field('service_date');
                echo $service_date;
                ?>
              
             <!-- <a href="<?php the_permalink(); ?>" class="btn btn-primary">Read More</a> -->

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
</a>
    <?php endwhile;
endif;
get_footer();
?>




