<?php 
// Palmcode Custom Post Type Contact Submission
function create_custom_post_type() {
    register_post_type('contact_submissions',
        array(
            'labels' => array(
                'name' => __('Contact Submissions'),
                'singular_name' => __('Contact Submission')
            ),
            'public' => true,
            'has_archive' => true,
            'supports' => array('title', 'editor', 'thumbnail'), // Supports thumbnail for image
        )
    );
}
add_action('init', 'create_custom_post_type');

// Palmcode Custom Metabox
function add_custom_metabox() {
    add_meta_box(
        'contact_details_metabox',
        'Contact Details',
        'render_contact_details_metabox',
        'contact_submissions',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'add_custom_metabox');

function render_contact_details_metabox($post) {
    // Add nonce field for security
    wp_nonce_field('save_contact_details', 'contact_metabox_nonce');
    
    // Retrieve saved meta values
    $email = get_post_meta($post->ID, 'email', true);
    $telephone = get_post_meta($post->ID, 'telephone', true);
    $service = get_post_meta($post->ID, 'service', true);
    $image = get_post_meta($post->ID, 'image', true); // For storing image URL or attachment ID

    ?>
    <label for="email">Email:</label>
    <input type="email" name="email" id="email" value="<?php echo esc_attr($email); ?>" />
    
    <label for="telephone">Telephone:</label>
    <input type="text" name="telephone" id="telephone" value="<?php echo esc_attr($telephone); ?>" />

    <label for="service">Service:</label>
    <select name="service" id="service">
        <option value="service1" <?php selected($service, 'service1'); ?>>Service 1</option>
        <option value="service2" <?php selected($service, 'service2'); ?>>Service 2</option>
    </select>

    <label for="upload_image">Upload Image:</label>
    <input type="file" name="upload_image" id="upload_image" />

    <?php
}

// Save the contact details and handle the image upload
function save_contact_details_metabox($post_id, $post) {
    // Verify nonce for security
    if (!isset($_POST['contact_metabox_nonce']) || !wp_verify_nonce($_POST['contact_metabox_nonce'], 'save_contact_details')) {
        return $post_id;
    }

    // Check for autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return $post_id;
    }

    // Check user permissions
    if (!current_user_can('edit_post', $post_id)) {
        return $post_id;
    }

    // Sanitize and save meta fields
    if (array_key_exists('email', $_POST)) {
        update_post_meta($post_id, 'email', sanitize_email($_POST['email']));
    }
    if (array_key_exists('telephone', $_POST)) {
        update_post_meta($post_id, 'telephone', sanitize_text_field($_POST['telephone']));
    }
    if (array_key_exists('service', $_POST)) {
        update_post_meta($post_id, 'service', sanitize_text_field($_POST['service']));
    }

    // Handle image upload (without setting as post thumbnail)
    if (!empty($_FILES['upload_image']['name'])) {
        $uploaded_file = $_FILES['upload_image'];
        $upload = wp_handle_upload($uploaded_file, array('test_form' => false));

        if (isset($upload['file'])) {
            // Prepare the attachment
            $attachment = array(
                'guid' => $upload['url'],
                'post_mime_type' => $upload['type'],
                'post_title' => sanitize_file_name($uploaded_file['name']),
                'post_content' => '',
                'post_status' => 'inherit',
            );

            // Insert the attachment
            $attachment_id = wp_insert_attachment($attachment, $upload['file'], $post_id);
            require_once(ABSPATH . 'wp-admin/includes/image.php');
            $attachment_data = wp_generate_attachment_metadata($attachment_id, $upload['file']);
            wp_update_attachment_metadata($attachment_id, $attachment_data);

            // Save the attachment ID in post meta
            update_post_meta($post_id, 'image', $attachment_id);
        }
    }
}
add_action('save_post', 'save_contact_details_metabox');

// Palmcode Handling AJAX Submission
add_action('wp_ajax_handle_form_submission', 'handle_form_submission');
add_action('wp_ajax_nopriv_handle_form_submission', 'handle_form_submission');

function handle_form_submission() {
    // Verify nonce for security
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'contact_form_nonce')) {
        wp_send_json_error(['data' => 'Invalid nonce.']);
    }

    // Check required fields
    if (empty($_POST['name']) || empty($_POST['message'])) {
        wp_send_json_error(['data' => 'Name and message are required fields.']);
    }

    // Sanitize and validate input
    $name = sanitize_text_field($_POST['name']);
    $message = sanitize_textarea_field($_POST['message']);
    $telephone = !empty($_POST['telephone']) ? sanitize_text_field($_POST['telephone']) : '';
    $email = !empty($_POST['email']) ? sanitize_email($_POST['email']) : '';
    $service = !empty($_POST['service']) ? sanitize_text_field($_POST['service']) : '';

    // Prepare post data for insertion
    $post_data = [
        'post_title'   => $name,
        'post_content' => $message,
        'post_status'  => 'publish',
        'post_type'    => 'contact_submissions',
    ];

    // Insert the post into the database
    $post_id = wp_insert_post($post_data);

    if (!$post_id || is_wp_error($post_id)) {
        wp_send_json_error(['data' => 'Failed to save the submission.']);
    }

    // Save custom meta fields
    if (!empty($telephone)) {
        update_post_meta($post_id, 'telephone', $telephone);
    }
    if (!empty($email)) {
        update_post_meta($post_id, 'email', $email);
    }
    if (!empty($service)) {
        update_post_meta($post_id, 'service', $service);
    }

    // Handle image upload if provided (without setting as post thumbnail)
    if (!empty($_FILES['upload_image']['name'])) {
        $uploaded_file = $_FILES['upload_image'];
        $upload = wp_handle_upload($uploaded_file, ['test_form' => false]);

        if (isset($upload['file'])) {
            // Prepare attachment data
            $attachment = [
                'guid'           => $upload['url'],
                'post_mime_type' => $upload['type'],
                'post_title'     => sanitize_file_name($uploaded_file['name']),
                'post_content'   => '',
                'post_status'    => 'inherit',
            ];

            // Insert the attachment
            $attach_id = wp_insert_attachment($attachment, $upload['file'], $post_id);

            // Include required file for generating attachment metadata
            require_once(ABSPATH . 'wp-admin/includes/image.php');

            // Generate and save attachment metadata
            $attach_data = wp_generate_attachment_metadata($attach_id, $upload['file']);
            wp_update_attachment_metadata($attach_id, $attach_data);

            // Save the attachment ID in post meta (but no longer set it as post thumbnail)
            update_post_meta($post_id, 'image', $attach_id);
        } else {
            wp_send_json_error(['data' => 'Failed to upload the image.']);
        }
    }

    // Return success response
    wp_send_json_success(['data' => 'Your form was submitted successfully!']);
}
?>
