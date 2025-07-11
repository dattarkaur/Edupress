<?php
/* Template Name: Sign-In */
get_header();


$error_messages = [];
$success_message = "";


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    $first_name = sanitize_text_field($_POST["first_name"]);
    $last_name = sanitize_text_field($_POST["last_name"]);
    $user_name = sanitize_text_field($_POST["user_name"]);
    $email = sanitize_email($_POST["email"]);
    $password = $_POST["password"];

    // Validate fields
    if (empty($first_name)) {
        $error_messages["first_name"] = "First name is required!";
    }
    if (empty($last_name)) {
        $error_messages["last_name"] = "Last name is required!";
    }
    if (empty($user_name)) {
        $error_messages["user_name"] = "Username is required!";
    } elseif (username_exists($user_name)) {
        $error_messages["user_name"] = "Username already taken!";
    }
    if (empty($email)) {
        $error_messages["email"] = "Email is required!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_messages["email"] = "Invalid email format!";
    } elseif (email_exists($email)) {
        $error_messages["email"] = "Email already registered!";
    }
    if (empty($password)) {
        $error_messages["password"] = "Password is required!";
    } elseif (strlen($password) < 6) {
        $error_messages["password"] = "Password must be at least 6 characters!";
    }

    // If no errors, create the user
    if (empty($error_messages)) {
        $user_id = wp_create_user($user_name, $password, $email);

        if (!is_wp_error($user_id)) {
            update_user_meta($user_id, "first_name", $first_name);
            update_user_meta($user_id, "last_name", $last_name);
            $success_message = "User registered successfully!";
        } else {
            $error_messages["general"] = "Error: " . $user_id->get_error_message();
        }
    }
}
?>
<h2 class="text-center mt-2">Sign in</h2>
<div class="sign-in">
    <div class="container w-50 mt-5 mb-5 text-center">
        <?php if (!empty($success_message)) : ?>
            <p class="success-message"><?php echo $success_message; ?></p>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3 mt-5">
                <div class="row mb-4">
                    <div class="col">
                        <input type="text" name="first_name" class="form-control" placeholder="First name">
                        <span class="error-message"><?php echo $error_messages["first_name"] ?? ""; ?></span>
                    </div>
                    <div class="col mb-4">
                        <input type="text" name="last_name" class="form-control" placeholder="Last name">
                        <span class="error-message"><?php echo $error_messages["last_name"] ?? ""; ?></span>
                    </div>
                </div>
                <input type="text" name="user_name" class="form-control" placeholder="User name">
                <span class="error-message"><?php echo $error_messages["user_name"] ?? ""; ?></span>
               
                <div class="row mt-5">
                    <div class="col">
                        <input type="email" name="email" class="form-control" placeholder="Email">
                        <span class="error-message"><?php echo $error_messages["email"] ?? ""; ?></span>
                    </div>
                    <div class="col">
                        <input type="password" name="password" class="form-control" placeholder="Password">
                        <span class="error-message"><?php echo $error_messages["password"] ?? ""; ?></span>
                    </div>
                </div>
            </div>
            <input type="submit" name="submit" value="Submit" class="sign-in-btn mt-5">
        </form>
    </div>
</div>

<?php get_footer(); ?>
