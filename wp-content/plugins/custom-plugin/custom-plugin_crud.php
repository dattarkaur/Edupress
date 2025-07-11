
<!-- <style>.updates-table td input,.widefat tfoot td input,.widefat th input,.widefat thead td input {
    margin: 0 0 0 0px !important;
    }
</style> -->
<?php
/**
 * Plugin Name: Crud Operation
 * Plugin URI: https://example.com/my-custom-plugin
 * Description: This is a custom plugin for CRUD operations.
 * Version: 1.0.0
 * Author: crud
 * Author URI: https://example.com
 */


function load_bootstrap_admin_assets() {
    wp_enqueue_style('bootstrap5-css', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css');
    wp_enqueue_script('bootstrap5-js', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js', [], null, true);
    wp_enqueue_script('globel-js', get_stylesheet_directory_uri() . '/js/globel.js', array('jquery','slick-js'), null, true);
 
    wp_localize_script('globel-js', 'ajax_object',[
      'ajaxurl' => admin_url('admin-ajax.php'),
    ]);
    }
add_action('admin_enqueue_scripts', 'load_bootstrap_admin_assets');

function register_custom_menu_page() {
   add_menu_page(
    'Custom Menu Title',
    'All users',
    'add_users', 
    'custompage',
    '_custom_menu_page',
    'dashicons-admin-post',
    21
    );
    add_submenu_page(
        'custompage',
        'My Custom Submenu', 
        'Role',
        'manage_options', 
        'my-custom-submenu',
        'my_custom_submenu_page_content'
    );
 
}

add_action('admin_menu', 'register_custom_menu_page');
function _custom_menu_page() {  

    //  Add new user
    $error = $first_name_error = $last_name_error = $email_error = $password_error  = $confirm_pass_error = $role_error;
    $first_name = $last_name = $email = $password  = $confirm_pass = $role;

    if(isset($_POST['submit'])) {
          
        $first_name = sanitize_text_field($_POST['first_name'] ?? '');
        if (empty($first_name)) {
            $first_name_error = "First name cannot be empty.";
        } elseif (strlen($first_name) < 4) {
            $first_name_error = "First name must be at least 4 characters long.";
        } elseif (strlen($first_name) > 15) {
            $first_name_error = "First name cannot exceed 15 characters.";
        }

        if (empty($_POST['last_name'])) {
           $last_name_error = "Last name cannot be empty";
        } else {
            $last_name = $_POST['last_name'];

            if (strlen($last_name) < 4) {
                $last_name_error = "last name must be at least 4 characters long.";
            } elseif (strlen($last_name) > 20) {
                $last_name_error = "last name cannot exceed 15 characters.";
            }
        }

        if (empty($_POST['email'])){
          $email_error = "Email cannot be empty";
        } elseif (username_exists($_POST['email'])) {
           $email_error = "Email is already taken";
        } else {
            $email = $_POST['email'];
        }
    
        if (empty($_POST['password'])){
            $password_error = "Password cannot be empty";
        } elseif (strlen($_POST['password']) < 8) {
            $password_error = "Password must be at least 8 characters long";
        } 

        if (empty($_POST['confirm_pass'])){
        $confirm_pass_error = "Confirm Password cannot be empty";
            } elseif ($_POST['password'] !== $_POST['confirm_pass']) {
            $confirm_pass_error = "Passwords do not match";
        }
        if(empty($confirm_pass_error)) {
        $password =($_POST['password']);
        }

        if (empty($_POST['role'])){
            $role_error[] = "Please select a role";
        } else {
            $role = $_POST['role'];
        }

        if (empty($error)){

        $new_user_data = [
                'user_login' => $email,
                'first_name' => $first_name,
                'last_name' => $last_name,
                'display_name' => $first_name .' '. $last_name,
                'user_pass' => $password,
                'user_email' => $email,
                'role'       => $role,
            ];

            $user_id = wp_insert_user($new_user_data);

            if (!is_wp_error($user_id)){
               echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
               User Inserted Successfully.
               <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
               </div>';
            } else {
                $error[] = "Failed to add user:" . $user_id->get_error_message();
            }
        }
    } 

    // edit user info
    $edit_error = "";
    $show_edit_modal = false;

    if (isset($_POST['edit'])){
        $user_id = intval($_POST['user_id']);
        $f_name = sanitize_text_field($_POST['f_name'] ?? '');
        $l_name = sanitize_text_field($_POST['l_name'] ?? '');
        $mail = sanitize_email($_POST['mail'] ?? '');
        $role = sanitize_text_field($_POST['user_role'] ?? '');
        $pass = $_POST['pass'] ?? '';

        if (empty($f_name) || empty($l_name) || empty($mail) || empty($role)) {
            $edit_error = '<p style="color:red;">All fields are required</p>';
            $show_edit_modal = true; 
        } else {
            wp_update_user([
                'ID'           => $user_id,
                'first_name'   => $f_name,
                'last_name'    => $l_name,
                'display_name' => $f_name . ' ' . $l_name,
                'user_pass'    => $pass,
                'user_email'   => $mail,
                'role'         => $role,
            ]);
            echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
            User Updated successfully.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>';
        }
    }
    

    // user deletion
    if (isset($_POST['delete_btn']) && isset($_POST['user_id']) && current_user_can('delete_users')) {
        $user_id = intval($_POST['user_id']);
        if ($user_id !== get_current_user_id()) {
            $deleted = wp_delete_user($user_id);
            if ($deleted) {
            echo '<div class="alert alert-success" role="alert">User deleted successfully.</div>';
            } else {
                echo "<p style='color:red;'>Failed to delete user.</p>";
            }
        }else {
            echo "<p style='color:red;'>You cannot delete your own account.</p>";
         }
    }  ?>
   

    <!-- Add user button -->

    <div class="one d-flex gap-3 mt-4 mb-4">
        <h4>Users</h4>
        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#exampleModal">Add user</button> 
    </div>

    <!-- role,bulk,search -->
    <div class="flex-col d-flex justify-content-between  mb-4 mx-2">
        <div class="both d-flex">
                <select name="bulk_action" form="bulk_action_form" class="form-select" style="width:auto;">
                    <option value="">Bulk Actions</option>
                    <option value="delete">Delete</option>
                </select>
                <button type="submit" name="apply_bulk_action" form="bulk_action_form" class="btn btn-primary btn-sm mx-1"onclick="return confirm('Are you sure you want to delete selected users?');">Apply</button>
    
            <div class="role-user mx-4">
                <form method="post">
                    <select name="role_filter" class="form-select" style="width:200px; display:inline-block;">
                        <option value="">-- Select Role --</option>
                        <?php foreach (get_editable_roles() as $role_slug => $role_info): ?>
                            <option value="<?php echo esc_attr($role_slug); ?>" <?php selected($_POST['role_filter'] ?? '', $role_slug); ?>>
                                <?php echo esc_html($role_info['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <input type="submit" name="filter_role" value="Filter" class="btn btn-primary btn-sm" />
                </form>
            </div>
        </div>

        <div class="search-user">
            <form method="post">
                <input type="text" name="user_search" placeholder="Search users...">
                <input type="submit" value="Search" class="btn btn-primary btn-sm">
            </form>
        </div>
    </div>
   

    <!-- Modal for add new user --> 
    <div class="modal fade mt-4 <?php echo !empty($error) ? 'show' : ''; ?>" id="exampleModal"   style="<?php echo !empty($error) ? 'display:block;' : ''; ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add user</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="post">
                        <table class="form-table">
                            <tr>
                                <th><label for="first_name">First Name*</label></th>
                                <td><input type="text" name="first_name" id="first_name" class="regular-text">
                                <span class="text-danger small fw-semibold d-block mt-1">
                                <?php echo esc_html($first_name_error); ?></span>
                            </tr>
                            <tr>
                                <th><label for="last_name">Last Name*</label></th>
                                <td><input type="text" name="last_name" id="last_name" class="regular-text">
                                <span class="text-danger small fw-semibold d-block mt-1"><?php echo $last_name_error;?></span></td>
                            </tr>
                             <tr>
                                <th><label for="name">Password*</label></th>
                                <td><input type="password" name="password" id="password" class="regular-text">   
                                <span class="text-danger small fw-semibold d-block mt-1"> <?php echo $password_error;?></span></td>
                            </tr>
                           <tr>
                                <th><label for="name">Confirm Password*</label></th>
                                <td><input type="password" name="confirm_pass" id="confirm_pass" class="regular-text">
                                <span class="text-danger small fw-semibold d-block mt-1"><?php echo $confirm_pass_error;?></span></td>
                            </tr>
                            <tr>
                                <th><label for="email">Email*</label></th>
                                <td><input type="email" name="email" id="email" class="regular-text">
                                <span class="text-danger small fw-semibold d-block mt-1"> <?php echo $email_error;?></span></td>
                            </tr>
                            <tr>
                                <th><label for="role">Role*</label></th>
                                <td>
                                   <select id="roles" name="role" class="fre-chosen-single">
                                    <option value="">select Role</option>
                                        <?php
                                            foreach (get_editable_roles() as  $role_name => $role_info) {
                                            echo '<option value="'.$role_name.'">' . $role_info['name'] . '</option>';
                                            }?>
                                            <span class="text-danger small fw-semibold d-block mt-1"> <?php echo $role_error;?></span></td>
                                    </select>
                                </td>
                            </tr>
                        </table>
                        <div class="modal-footer justify-content-between">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <p><input type="submit" name="submit" class="btn btn-primary" value="Add User"></p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- script for show error message on that time not after re-enter the btn in add user -->
    <?php if (!empty($first_name_error) || !empty($last_name_error) || !empty($email_error) || !empty($password_error) || !empty($confirm_pass_error) || !empty($role_error)) : ?>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const modal = new bootstrap.Modal(document.getElementById('exampleModal'));
                modal.show();
            });
        </script>
    <?php endif;  


    // Display users list

    $users_per_page = 10;
    $current_page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
    $offset = ($current_page - 1) * $users_per_page; 

    $search_term = sanitize_text_field($_POST['user_search'] ?? '');
    $selected_role = sanitize_text_field($_POST['role_filter'] ?? '');  

    $args = [
        'number' => $users_per_page,
        'offset' => $offset,
        'orderby' => 'registered',
        'order' => 'DESC',
        'fields' => 'all',
    ];

    if (!empty($search_term)) {
        $args['search'] = '*' . esc_attr($search_term) . '*';
        $args['search_columns'] = ['user_login', 'user_email', 'display_name', 'role'];
    }
    if (!empty($selected_role)) {
    $args['role'] = $selected_role;
    }

    // Fetch users
    $users = get_users($args);

    $count_args = $args;
    unset($count_args['number'], $count_args['offset']); // remove pagination
    $total_users = count(get_users($count_args));
    $total_pages = ceil($total_users / $users_per_page);  

    if (isset($_POST['apply_bulk_action']) && $_POST['bulk_action'] === 'delete') {
        if (!empty($_POST['bulk_user_ids'])) {
            foreach ($_POST['bulk_user_ids'] as $user_id) {
                $user_id = intval($user_id);
                if ($user_id > 0 && current_user_can('delete_user', $user_id)) {
                    require_once(ABSPATH . 'wp-admin/includes/user.php');
                    wp_delete_user($user_id);
                }
            }
            echo '<div class="mb-4 notice notice-success is-dismissible"><p>Selected users have been deleted.</p></div>';
        } else {
            echo '<div class="notice notice-warning is-dismissible"><p>No users selected.</p></div>';
        }
    } ?>

    <form method="post" id="bulk_action_form">
        <table class="widefat fixed striped">
            <thead>
                <tr>
                    <th><input type="checkbox" id="select-all-users"></th>
                    <th>Username</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($users)) : ?>
                    <?php foreach ($users as $index => $user) : ?>
                        <tr>
                            <td><input type="checkbox" name="bulk_user_ids[]" value="<?php echo esc_attr($user->ID); ?>"></td>
                            <td><?php echo esc_html($user->user_login); ?></td>
                            <td><?php echo esc_html($user->display_name); ?></td>
                            <td><?php echo esc_html($user->user_email); ?></td>
                            <td><?php echo esc_html(implode(', ', $user->roles)); ?></td>
                            <td>
                                <input type="hidden" name="user_id" value="<?php echo esc_attr($user->ID); ?>">
                                <button type="submit" name="delete_btn" class="btn-sm btn btn-danger" onclick="return confirm('Are you sure you want to delete this user?');">Delete</button>
                                <button type="button" class="btn btn-success btn-sm edit-btn"
                                    data-bs-toggle="modal"
                                    data-bs-target="#staticBackdrop"
                                    data-id="<?php echo esc_attr($user->ID); ?>"
                                    data-fname="<?php echo esc_attr(get_user_meta($user->ID, 'first_name', true)); ?>"
                                    data-lname="<?php echo esc_attr(get_user_meta($user->ID, 'last_name', true)); ?>"
                                    data-email="<?php echo esc_attr($user->user_email); ?>"
                                    data-role="<?php echo esc_attr($user->roles[0]); ?>">Edit
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="5">
                            <p class="text-muted" style="text-align:center; padding:10px;">No users found for your search.</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </form>

    <script>
    document.getElementById('select-all-users').addEventListener('click', function() {
        const checkboxes = document.querySelectorAll("input[name='bulk_user_ids[]']");
        checkboxes.forEach(cb => cb.checked = this.checked);
    });
    </script>

    <?php
    $pagination_links = paginate_links([
        'base' => add_query_arg('paged', '%#%'),
        'format' => '',
        'prev_text' => __('« Prev'),
        'next_text' => __('Next »'),
        'total' => $total_pages,
        'current' => $current_page,
        'type' => 'array',
    ]);

    if (!empty($pagination_links)) {
        echo '<nav><ul class="pagination mt-4 justify-content-end">';
        foreach ($pagination_links as $link) {
            // Add active class if it's the current page
            if (strpos($link, 'current') !== false) {
                echo '<li class="page-item active">' . str_replace('page-numbers', 'page-link', $link) . '</li>';
            } else {
                echo '<li class="page-item">' . str_replace('page-numbers', 'page-link', $link) . '</li>';
            }
        }
        echo '</ul></nav>';
    }
    ?>


    <!-- modal for edit user -->
    <div class="modal fade mt-4" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Update User</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <?php  if(!empty($edit_error))  echo $edit_error ?>
                    <form method="post">
                        <table class="form-table">
                            <input type="hidden" name="user_id" id="edit_user_id">
                            <tr>
                                <th><label for="first_name">First Name</label></th>
                                <td><input type="text" name="f_name" id="f_name" class="regular-text"></td>
                            </tr>
                            <tr>
                                <th><label for="last_name">Last Name</label></th>
                                <td><input type="text" name="l_name" id="l_name" class="regular-text"></td>
                            </tr>
                                <tr>
                                <th><label for="name">Password</label></th>
                                <td><input type="password" name="pass" id="pass" class="regular-text"></td>
                            </tr>
                            <tr>
                                <th><label for="email">Email</label></th>
                                <td><input type="email" name="mail" id="mail" class="regular-text"></td>
                            </tr>
                            <tr>
                                <th><label for="role">Role</label></th>
                                <td> 
                                    <select name="user_role" id="user_role" class="form-control">
                                        <?php
                                        $roles = wp_roles()->get_names(); 
                                        foreach ($roles as $role_slug => $role_name) {
                                            echo '<option value="' . esc_attr($role_slug) . '">' . esc_html($role_name) . '</option>';
                                        }  ?>
                                    </select>
                                </td>   
                            </tr>
                        </table>
                        <div class="modal-footer justify-content-between">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <p><input type="submit" name="edit" class="btn btn-primary" value="Edit User"></p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const editButtons = document.querySelectorAll('.edit-btn');
            editButtons.forEach(button => {
                button.addEventListener('click', () => {
                    document.getElementById('edit_user_id').value = button.dataset.id;
                    document.getElementById('f_name').value = button.dataset.fname;
                    document.getElementById('l_name').value = button.dataset.lname;
                    document.getElementById('mail').value = button.dataset.email;
                    document.getElementById('pass').value = button.dataset.pass;
                    const roleDropdown = document.getElementById('user_role');
                    roleDropdown.value = button.dataset.role;
                });
            });
        });
    </script>

    <!-- js for show error message after click the btn(not show next time)  -->

    <?php if (!empty($edit_error) && $show_edit_modal): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var editModal = new bootstrap.Modal(document.getElementById('staticBackdrop'));
                editModal.show();
            });
        </script>
    <?php endif;  
} 


// Role menu 

function my_custom_submenu_page_content() {

    //Insert role

    $create_error = "";
    if (isset($_POST['create_role'])) {
        if (empty($_POST['role_label'])) {
            $create_error = 'Select the Role';
        } else {
            $label = sanitize_text_field($_POST['role_label']);
        }

        $slug = 'custom_' . sanitize_key(strtolower(str_replace(' ', '_', $label)));

        if (get_role($slug)) {
            echo '<p style="color:red;">Role already exists.</p>';
            return;
        }

        if (empty($create_error)) {
            $selected_caps = $_POST['capabilities'] ?? [];
            $capabilities = [];

            if (is_array($selected_caps)) {
                foreach ($selected_caps as $cap) {
                    $capabilities[sanitize_text_field($cap)] = true;
                }
            }   
            add_role($slug, $label, $capabilities);
              echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
              Role Inserted successfully.
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>';
        }
    }

    //Edit role

     $edit_role_err = "";
    if (isset($_POST['rename_role']) && current_user_can('manage_options')) {
        $old_role_slug = sanitize_key($_POST['old_role_slug'] ?? '');
        $new_role_label = sanitize_text_field($_POST['new_role_label'] ?? '');
        $new_capabilities_input = $_POST['capabilities_edit'] ?? [];

        if (empty($old_role_slug) || empty($new_role_label)) {
                $edit_role_err = "Select Role";
        }
        $new_capabilities = [];
        if (is_array($new_capabilities_input)) {
            foreach ($new_capabilities_input as $cap) {
                $new_capabilities[sanitize_text_field($cap)] = true;
            }
        }
        $wp_roles = wp_roles();
        if ($role = $wp_roles->get_role($old_role_slug)){
            $new_role_slug = 'custom_' . sanitize_key(strtolower(str_replace(' ', '_', $new_role_label)));
            if ($new_role_slug !== $old_role_slug && get_role($new_role_slug)) {
                add_action('admin_notices', function () {
                    ?>
                    <div class="notice notice-error is-dismissible">
                        <p><?php _e('Cannot rename: Role slug already exists.', 'your-text-domain'); ?></p>
                    </div>
                    <?php
                });
                return;
            }
            if ($new_role_slug !== $old_role_slug) {
                remove_role($old_role_slug);
                add_role($new_role_slug, $new_role_label, $new_capabilities);
            } else {

                foreach ($role->capabilities as $cap => $has_cap) {
                    if (!isset($new_capabilities[$cap])) {
                        $role->remove_cap($cap);
                    }
                }
                foreach ($new_capabilities as $cap => $enabled) {
                    if ($enabled && !$role->has_cap($cap)) {
                        $role->add_cap($cap);
                    }
                }
                $wp_roles->roles[$old_role_slug]['name'] = $new_role_label;
                $wp_roles->role_names[$old_role_slug] = $new_role_label;
            }
                ?>
                <div class="alert alert-success is-dismissible">
                    <p><?php _e('Role updated successfully.', 'your-text-domain'); ?></p>
                </div>
                <?php
            wp_roles()->reinit();
        } else {
            add_action('admin_notices', function () {
                ?>
                <div class="notice notice-error is-dismissible">
                    <p><?php _e('Role not found.', 'your-text-domain'); ?></p>
                </div>
                <?php
            });
        }
    }

    //Delete role

    if (isset($_POST['delete_role']) && current_user_can('manage_options')){
        $role_slug = sanitize_key($_POST['delete_role_slug']);

        $protected_roles = ['administrator', 'editor', 'author', 'subscriber', 'contributor'];
        if (in_array($role_slug, $protected_roles)) {
            add_action('admin_notices', function () {
                echo '<div class="notice notice-error is-dismissible"><p>Cannot delete default WordPress roles.</p></div>';
            });
            return;
        }
        if (get_role($role_slug)) {
            remove_role($role_slug);
                echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                Role deleted successfully.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>';
        } else {
            echo '<div class="notice notice-error is-dismissible"><p>Role not found or already deleted.</p></div>';
    
        }
    }


    $capabilities = [
        'read' => 'Read',
        'edit_posts' => 'Edit Posts',
        'publish_posts' => 'Publish Posts',
        'edit_others_posts' => 'Edit Others\' Posts',
        'delete_posts' => 'Delete Posts',
        'upload_files' => 'Upload Files',
        'manage_options' => 'Manage Options',
    ]; ?>

    <div class="roles">
        <h4 class="mt-4">Add User Role</h4>
        <form method="post" class="mt-4 p-4 border rounded bg-light" style="max-width: 400px;">
            <div class="mb-3"><?php
                if (!empty($create_error)) {
                    echo '<div class="alert alert-danger" role="alert">' . esc_html($create_error) . '</div>';
                } ?>
                <label for="role_label" class="form-label">Add Role</label>
                <input type="text" name="role_label" id="role_label" class="form-control">
            </div>
         
            <?php $selected_caps = [];
            if (!empty($_POST['capabilities']) && is_array($_POST['capabilities'])) {
                $selected_caps = array_map('sanitize_text_field', $_POST['capabilities']);
            } else {
                $selected_caps = ['read']; 
            }
            ?>
            <div class="mb-3">
                <label class="form-label">Capabilities</label><br>
                <?php foreach ($capabilities as $cap => $label): ?>
                    <input type="checkbox" name="capabilities[]" value="<?php echo esc_attr($cap); ?>" id="cap_<?php echo esc_attr($cap); ?>"
                    <?php echo in_array($cap, $selected_caps) ? 'checked' : ''; ?>>
                    <label for="cap_<?php echo esc_attr($cap); ?>"><?php echo esc_html($label); ?></label><br>
                <?php endforeach; ?>
            </div>
         <button type="submit" name="create_role" class="btn btn-success">Create Role</button>
        </form>
    </div>

    <!-- next-section -->

    <div class="wrap">
        <h1>Manage User Role</h1>

        <?php $roles = wp_roles()->get_names(); ?>
        <table class="widefat striped">
            <thead>
                <tr>
                    <th>Role</th>
                    <th>Capability</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($roles as $slug => $label): ?>
                    <tr>
                        <td><?php echo esc_html(translate_user_role($label)); ?></td>
                        <td class="word-wrap-break-word" style="max-width:100px;">
                            <?php
                            $role_object = get_role($slug);
                            if ($role_object && !empty($role_object->capabilities)) {
                                $caps = [];

                                foreach ($role_object->capabilities as $cap => $enabled) {
                                    if ($enabled && is_string($cap)) {
                                        $caps[] = $cap;
                                    }
                                }
                                $all_caps = implode(', ', $caps);
                                $truncated_output = (mb_strlen($all_caps) > 130) ? mb_substr($all_caps, 0, 130) . '...' : $all_caps;

                                echo esc_html($truncated_output);
                            } else {
                                echo 'No capabilities found';
                            }
                            ?>
                        </td>
                        <td>
                            <button type="button"
                                class="btn btn-sm btn-primary edit-role"
                                data-bs-toggle="modal"
                                data-bs-target="#roleModal"
                                data-role-slug="<?php echo esc_attr($slug); ?>"
                                data-role-label="<?php echo esc_attr($label); ?>">
                                Edit
                            </button>
                            <form method="post" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this role?');">
                                <input type="hidden" name="delete_role_slug" value="<?php echo esc_attr($slug); ?>">
                                <button type="submit" name="delete_role" class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- modal for edit role and capability -->
        <div class="modal fade mt-4" id="roleModal" tabindex="-1" aria-labelledby="roleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <form method="post" class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="roleModalLabel">Edit Role</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body"> <?php
                        if (!empty($edit_role_err)) {
                            echo '<div class="alert alert-danger" role="alert">' . esc_html($edit_role_err) . '</div>';
                        } ?>
                        <label for="role_label" class="form-label">Add New Role</label>
                        <input type="text" name="new_role_label" id="modal_role_label" class="form-control">
                        <input type="hidden" name="old_role_slug" id="modal_role_slug">

                        <div class="mt-3">
                            <label class="form-label">Capabilities</label><br>
                            <div id="capabilities_edit_wrapper">
                                <?php foreach ($capabilities as $cap => $label): ?>
                                    <div>
                                        <input type="checkbox" name="capabilities_edit[]" value="<?php echo esc_attr($cap); ?>" id="edit_cap_<?php echo esc_attr($cap); ?>">
                                        <label for="edit_cap_<?php echo esc_attr($cap); ?>"><?php echo esc_html($label); ?></label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="rename_role" class="btn btn-warning">Rename</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    

    <script>
        const roleCapabilities = <?php 
            $all_caps = [];
            $roles = wp_roles()->roles;
            foreach ($roles as $slug => $role) {
                $caps = [];
                foreach ($role['capabilities'] as $cap => $enabled) {
                    if ($enabled) {
                        $caps[] = $cap;
                    }
                }
                $all_caps[$slug] = $caps;
            }
            echo json_encode($all_caps);
        ?>;

        document.addEventListener('DOMContentLoaded', function () {
            const modal = document.getElementById('roleModal');
            modal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const label = button.getAttribute('data-role-label');
                const slug = button.getAttribute('data-role-slug');
                document.getElementById('modal_role_label').value = label;
                document.getElementById('modal_role_slug').value = slug;

                // Uncheck all checkboxes first
                const checkboxes = modal.querySelectorAll('#capabilities_edit_wrapper input[type="checkbox"]');
                checkboxes.forEach(checkbox => checkbox.checked = false);

                // Check only the capabilities that belong to this role
                if (roleCapabilities[slug]) {
                    roleCapabilities[slug].forEach(cap => {
                        const checkbox = modal.querySelector(`#edit_cap_${cap}`);
                        if (checkbox) {
                            checkbox.checked = true;
                        }
                    });
                }
            });
        });
    </script>  
<?php }

