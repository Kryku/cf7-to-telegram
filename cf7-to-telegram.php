<?php
/*
Plugin Name: CF7 to Telegram
Description: Sends Telegram notifications on successful Contact Form 7 submissions.
Version: 1.0
Author: V.Krykun
*/

if (!defined('ABSPATH')) {
    exit;
}

add_action('admin_menu', 'cf7_telegram_add_settings_page');
function cf7_telegram_add_settings_page() {
    add_menu_page(
        'CF7 to Telegram Settings',
        'CF7 to Telegram',
        'manage_options',
        'cf7-telegram-settings',
        'cf7_telegram_render_settings_page',
        'dashicons-email-alt',
        25
    );
}

add_action('admin_init', 'cf7_telegram_register_settings');
function cf7_telegram_register_settings() {
    register_setting('cf7_telegram_settings_group', 'cf7_telegram_bot_token', 'sanitize_text_field');
    register_setting('cf7_telegram_settings_group', 'cf7_telegram_chat_id', 'sanitize_text_field');
    register_setting('cf7_telegram_settings_group', 'cf7_telegram_form_id', 'absint');
    register_setting('cf7_telegram_settings_group', 'cf7_telegram_message_template', 'sanitize_textarea_field');
}

function cf7_telegram_render_settings_page() {
    if (!current_user_can('manage_options')) {
        return;
    }

    $forms = WPCF7_ContactForm::find();
    $saved_form_id = get_option('cf7_telegram_form_id');
    $saved_template = get_option('cf7_telegram_message_template', "🔔 New Submission\nName: [your-name]\nMessage: [your-message]");
    ?>
    <div class="wrap">
        <h1>CF7 to Telegram Settings</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('cf7_telegram_settings_group');
            do_settings_sections('cf7_telegram_settings_group');
            ?>

            <table class="form-table">
                <tr>
                    <th><label for="cf7_telegram_bot_token">Telegram Bot Token</label></th>
                    <td><input type="text" name="cf7_telegram_bot_token" id="cf7_telegram_bot_token" value="<?php echo esc_attr(get_option('cf7_telegram_bot_token')); ?>" class="regular-text" /></td>
                </tr>
                <tr>
                    <th><label for="cf7_telegram_chat_id">Telegram Chat ID</label></th>
                    <td><input type="text" name="cf7_telegram_chat_id" id="cf7_telegram_chat_id" value="<?php echo esc_attr(get_option('cf7_telegram_chat_id')); ?>" class="regular-text" /></td>
                </tr>
                <tr>
                    <th><label for="cf7_telegram_form_id">Select CF7 Form</label></th>
                    <td>
                        <select name="cf7_telegram_form_id" id="cf7_telegram_form_id">
                            <option value="0">-- Select a Form --</option>
                            <?php foreach ($forms as $form) : ?>
                                <option value="<?php echo esc_attr($form->id()); ?>" <?php selected($saved_form_id, $form->id()); ?>>
                                    <?php echo esc_html($form->title()); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><label for="cf7_telegram_message_template">Message Template</label></th>
                    <td>
                        <textarea name="cf7_telegram_message_template" id="cf7_telegram_message_template" rows="5" cols="50"><?php echo esc_textarea($saved_template); ?></textarea>
                        <p class="description">Use [field-name] to include form fields (e.g., [your-name], [your-message]). Add emojis or text as needed.</p>
                    </td>
                </tr>
            </table>

            <?php submit_button('Save Settings'); ?>
        </form>
    </div>
    <?php
}

add_action('wpcf7_mail_sent', 'cf7_telegram_send_notification', 10, 1);
function cf7_telegram_send_notification($contact_form) {
    $selected_form_id = get_option('cf7_telegram_form_id');

    if (!$selected_form_id || $contact_form->id() != $selected_form_id) {
        return;
    }

    $submission = WPCF7_Submission::get_instance();
    if (!$submission) {
        return;
    }
    $form_data = $submission->get_posted_data();

    $bot_token = get_option('cf7_telegram_bot_token');
    $chat_id = get_option('cf7_telegram_chat_id');
    $template = get_option('cf7_telegram_message_template');

    if (empty($bot_token) || empty($chat_id) || empty($template)) {
        return;
    }

    $message = $template;
    foreach ($form_data as $key => $value) {
        $message = str_replace("[$key]", esc_html($value), $message);
    }

    $url = "https://api.telegram.org/bot{$bot_token}/sendMessage";
    $args = [
        'body' => [
            'chat_id' => $chat_id,
            'text' => $message,
            'parse_mode' => 'HTML'
        ],
        'timeout' => 10,
    ];

    $response = wp_remote_post($url, $args);
    
    /*
    if (is_wp_error($response)) {
        error_log('CF7 to Telegram Error: ' . $response->get_error_message());
    }
    */
}