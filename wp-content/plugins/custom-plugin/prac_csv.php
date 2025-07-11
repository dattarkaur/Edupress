
<?php
    /*
     * Plugin Name: My Custom Plugin
     * Description: Adds a custom functionality to my WordPress site.
     * Version: 1.0
     * Author: Your Name
     */

function prac_shortcode(){
    ob_start();?>
        <form method="post" enctype="multipart/form-data">
            <input type="file" name="csv_file" accept=".csv">
            <input type="submit" name="submit_csv" value="Import CSV">
        </form>
      <?php
    return ob_get_clean();
}
add_shortcode('prac_file','prac_shortcode');

// Add admin menu page
add_action('admin_menu', 'my_plugin_menu');
function my_plugin_menu() {
   add_menu_page(
      'My Plugin Settings', // page title
      'Prac CSV', // menu title
      'manage_options', // capability
      'my-plugin-settings', // slug
      'my_plugin_settings_page', // callback function
      'dashicons-admin-plugins', // icon URL
      25// menu position
   );
}

function my_plugin_settings_page() { ?>
   <h2>Hello this is custom plugin for Practice</h2>
   <p><strong>Use this shortcode for using the file uploads</strong><code>[prac_file]</code></p>
   <?php
}
?>