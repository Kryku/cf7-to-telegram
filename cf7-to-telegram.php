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
        'dashicons-format-chat',
        25
    );
}

add_action('admin_init', 'cf7_telegram_register_settings');
function cf7_telegram_register_settings() {
    register_setting('cf7_telegram_settings_group', 'cf7_telegram_bot_token', 'sanitize_text_field');
    register_setting('cf7_telegram_settings_group', 'cf7_telegram_forms_config', [
        'sanitize_callback' => 'cf7_telegram_sanitize_forms_config'
    ]);
}

function cf7_telegram_sanitize_forms_config($input) {
    $sanitized = [];
    if (is_array($input)) {
        foreach ($input as $form_id => $config) {
            $sanitized[$form_id] = [
                'enabled' => isset($config['enabled']) ? 1 : 0,
                'chat_id' => sanitize_text_field($config['chat_id'] ?? ''),
                'template' => sanitize_textarea_field($config['template'] ?? '')
            ];
        }
    }
    return $sanitized;
}

function cf7_telegram_render_settings_page() {
    if (!current_user_can('manage_options')) {
        return;
    }

    $forms = WPCF7_ContactForm::find();
    $bot_token = get_option('cf7_telegram_bot_token');
    $forms_config = get_option('cf7_telegram_forms_config', []);
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
                    <td><input type="text" name="cf7_telegram_bot_token" id="cf7_telegram_bot_token" value="<?php echo esc_attr($bot_token); ?>" class="regular-text" /></td>
                </tr>
            </table>

            <h2>Form Configurations</h2>
            <?php foreach ($forms as $form) : 
                $form_id = $form->id();
                $enabled = $forms_config[$form_id]['enabled'] ?? 0;
                $chat_id = $forms_config[$form_id]['chat_id'] ?? '';
                $template = $forms_config[$form_id]['template'] ?? "🔔 New Submission from {$form->title()}\nName: [your-name]\nMessage: [your-message]";
            ?>
                <h3><?php echo esc_html($form->title()); ?> (ID: <?php echo $form_id; ?>)</h3>
                <table class="form-table">
                    <tr>
                        <th><label for="enabled_<?php echo $form_id; ?>">Enable Telegram Notification</label></th>
                        <td><input type="checkbox" name="cf7_telegram_forms_config[<?php echo $form_id; ?>][enabled]" id="enabled_<?php echo $form_id; ?>" value="1" <?php checked($enabled, 1); ?> /></td>
                    </tr>
                    <tr>
                        <th><label for="chat_id_<?php echo $form_id; ?>">Telegram Chat ID</label></th>
                        <td><input type="text" name="cf7_telegram_forms_config[<?php echo $form_id; ?>][chat_id]" id="chat_id_<?php echo $form_id; ?>" value="<?php echo esc_attr($chat_id); ?>" class="regular-text" /></td>
                    </tr>
                    <tr>
                        <th><label for="template_<?php echo $form_id; ?>">Message Template</label></th>
                        <td>
                            <textarea name="cf7_telegram_forms_config[<?php echo $form_id; ?>][template]" id="template_<?php echo $form_id; ?>" rows="5" cols="50"><?php echo esc_textarea($template); ?></textarea>
                            <p class="description">Use [field-name] for form fields (e.g., [your-name]). Add emojis or text as needed.</p>
                        </td>
                    </tr>
                </table>
            <?php endforeach; ?>

            <?php submit_button('Save Settings'); ?>
        </form>
    </div>
    <?php
}

add_action('wpcf7_mail_sent', 'cf7_telegram_send_notification', 10, 1);
function cf7_telegram_send_notification($contact_form) {
    $form_id = $contact_form->id();
    $forms_config = get_option('cf7_telegram_forms_config', []);
    $bot_token = get_option('cf7_telegram_bot_token');

    if (empty($forms_config[$form_id]) || empty($bot_token) || empty($forms_config[$form_id]['enabled'])) {
        return;
    }

    $chat_id = $forms_config[$form_id]['chat_id'];
    $template = $forms_config[$form_id]['template'];

    if (empty($chat_id) || empty($template)) {
        return;
    }

    $submission = WPCF7_Submission::get_instance();
    if (!$submission) {
        return;
    }
    $form_data = $submission->get_posted_data();

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