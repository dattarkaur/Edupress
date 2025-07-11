<?php
/**
 * Plugin Name: File Upload With CSV
 * Description: Adds a shortcode [my_posts] and an admin page showing how to use it.
 * Version: 1.0
 * Author: Author
 */

 //csv data insert 
function csv_insert_data(){
    
    if (wp_verify_nonce($_POST['my_csv_upload_nonce'], 'my_csv_upload_action')) {

        if(empty($_POST['post_types'])) {
                echo '<p style="color:red;">Please select a post type before submitting.</p>';
        }

        if(!empty($_FILES['my_csv_file']['name'])){
            $file_tmp = $_FILES['my_csv_file']['tmp_name'];
            //Validate MIME type
            $mime = mime_content_type($file_tmp);
            $allowed = ['text/plain', 'text/csv', 'application/vnd.ms-excel'];
            if(!in_array($mime, $allowed))
            {
                echo '<div style="color:red;">Only CSV files are allowed.</div>';
            } 
            else{
                $csv = fopen($file_tmp, 'r');
                $row = 0;
                $inserted = 0;

                while(($data = fgetcsv($csv, 1000, ',')) !== FALSE) {
                    if ($row === 0) {
                        $row++;
                        continue;
                    }
                    $title       = sanitize_text_field($data[0]);
                    $description = sanitize_textarea_field($data[1]);
                    $image_file  = sanitize_file_name($data[2]);
                    $image_path  = ABSPATH . 'wp-content/uploads/' . $image_file;
                    $sub_title = sanitize_textarea_field($data[3]);

                    $errors = [];

                        if(empty($title)) {
                            $errors[] = 'Title is required';
                        }
                        if (empty($description)) {
                            $errors[] = 'Description is required';
                        }
                        if (empty($image_file)) {
                            $errors[] = 'Image file name is required';
                        } 
                        elseif (!file_exists($image_path)) {
                            $errors[] = 'Image not found on server';
                        }
                        if (empty($sub_title)) {
                            $errors[] = 'sub title is required';
                        }
                        if(!empty($errors)) {
                            continue; 
                        }
                        
                        $post_types = $_REQUEST['post_types'];   

                        $post_id = wp_insert_post([
                            'post_title'   => $title,
                            'post_status'  => 'draft',
                            'post_type'    => $post_types,
                        ]);

                        if (!is_wp_error($post_id)) {
                            if ($post_types == 'blog'){
                                update_field('blog_desc', $description, $post_id);
                                update_field('title_blog', $sub_title, $post_id);  
                            }          

                            elseif ($post_types == 'services'){
                                update_field('service_desc', $description, $post_id);
                                update_field('title_services', $sub_title, $post_id);  
                            }

                            elseif ($post_types == 'faculty'){
                                update_field('faculty_desc', $description, $post_id);
                                update_field('title_faculty', $sub_title, $post_id);  
                            }

                            if (file_exists($image_path)) {
                                    require_once ABSPATH . 'wp-admin/includes/image.php';
                                    require_once ABSPATH . 'wp-admin/includes/file.php';
                                    require_once ABSPATH . 'wp-admin/includes/media.php';

                                    $image_data = file_get_contents($image_path);
                                    $upload = wp_upload_bits($image_file, null, $image_data);

                                    if (!$upload['error']) {
                                        $filetype = wp_check_filetype(basename($upload['file']), null);
                                        $attachment = [
                                            'post_mime_type' => $filetype['type'],
                                            'post_title'     => sanitize_file_name($image_file),
                                            'post_content'   => '',
                                            'post_status'    => 'inherit',
                                        ];

                                        $attach_id = wp_insert_attachment($attachment, $upload['file'], $post_id);
                                        if (!is_wp_error($attach_id)) {
                                            $attach_data = wp_generate_attachment_metadata($attach_id, $upload['file']);
                                            wp_update_attachment_metadata($attach_id, $attach_data);
                                            set_post_thumbnail($post_id, $attach_id);
                                        }
                                    }
                            }
                            $inserted++;
                        }
                $row++;
                }
                    foreach ($errors as $error) {
                    echo "<p style='color:red;'>$error</p>";
                    }
                fclose($csv);
                echo '<div style="color:green;"> Inserted ' . $inserted . ' posts.</div>';
            }
        } 
        else{
            echo '<div style="color:orange;">Please select a file to upload.</div>';
        }
    }
    else {
        echo '<div style="color:red;">Invalid form submission.</div>';
    }
                    
}        


// Shortcode to fetch and show 
function my_custom_post_shortcode(){ 
  if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['my_csv_upload_nonce'])) {
    csv_insert_data();
  }
    ob_start();
        ?>
        <div class="csv-upload-wrapper">
            <h3>Upload CSV to Insert Blog Posts</h3>
            <form method="post" enctype="multipart/form-data">
                <select name="post_types" id="post" class="form-control mb-3">
                    <option value="">Select post</option>
                    <option value="blog">Blog</option>
                    <option value="services">Services</option>
                    <option value="faculty">Faculty</option>
                </select>
                <?php wp_nonce_field('my_csv_upload_action', 'my_csv_upload_nonce'); ?>
                <p><input type="file" name="my_csv_file" accept=".csv" class="form-control-file"></p>
                <p><button type="submit" class="btn btn-primary">Upload and Insert Posts</button></p>
            </form>
        </div>
    <?php return ob_get_clean();
}
add_shortcode('csv_post_uploader', 'my_custom_post_shortcode');

// Add plugin admin menu page
function my_plugin_add_menu_page() {
    add_menu_page(
        'My Posts Page',                  // Psubmissionage title
        'CSV File Uploads',                // Menu label
        'manage_options',                 // Capability
        'my-posts-plugin',                // Slug
        'my_plugin_render_admin_page',    // Callback function
        'dashicons-admin-post',           // Icon
        6                                 // Position
    );
}
add_action('admin_menu', 'my_plugin_add_menu_page');

// Render admin page content
function my_plugin_render_admin_page() {
    ?>
<div class="wrap">
    <h1>CSV Upload file to insert post</h1>
    <div style="background: #f1f1f1; border-left: 4px solid #0073aa; padding: 10px; margin: 20px 0;">
        <p><strong>Use this shortcode:</strong> <code>[csv_upload_post]</code></p>
        <p>Paste it into any post, page, or widget to use.</p>
    </div>
    <p>This is a demo. You can later define the actual shortcode functionality using <code>add_shortcode()</code>.</p>
</div>
<?php
}