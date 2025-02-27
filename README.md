# CF7 to Telegram
## Description
A simple WordPress plugin that sends Telegram notifications when a Contact Form 7 (CF7) form is successfully submitted. I built this as a personal tool to integrate CF7 with Telegram, allowing me to select a form, set a chat ID, and customize notification templates with form data and emojis. It’s lightweight, free, and fully customizable—no need for paid alternatives!

![Clear Checkout](https://raw.githubusercontent.com/Kryku/cf7-to-telegram/refs/heads/main/screenshots/cf7-to-telegram-enable.png)

## Features
* Configure notifications for multiple CF7 forms.
* Enable/disable Telegram notifications per form.
* Set unique Telegram Chat IDs per form.
* Customize message templates with form fields (e.g., `[your-name]`), emojis, or text.
* Simple admin interface.
* Low server load.

## Installation
1. Download the plugin file (`cf7-to-telegram.zip`).
2. Go to **Plugins > Add New** in the WordPress admin.
3. Upload the plugin file and activate it.

## Settings
Go to **CF7 to Telegram** in the WordPress admin menu.


### Here you can configure:
* **Telegram Bot Token**: Your bot token (global).
* **Per Form**:
  - Enable/disable Telegram notifications.
  - Chat ID for notifications.
  - Message template.

## Usage
1. Create a Telegram bot via [BotFather](https://t.me/BotFather) to get a token.
2. Find Chat IDs for your chats.
3. Set the token and configure forms in the plugin settings (enable only the forms you want).
4. Test by submitting your CF7 forms.

## Example Template
```
🎉 New Form Submission!
Name: [your-name]
Email: [your-email]
Message: [your-message]
```

## Bottom Line
This is a straightforward plugin for personal use or testing. It’s not overly polished for production yet, but it gets the job done. Feel free to tweak the code to suit your needs!
