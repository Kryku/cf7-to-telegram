# CF7 to Telegram
## Description
A simple WordPress plugin that sends Telegram notifications when a Contact Form 7 (CF7) form is successfully submitted. I built this as a personal tool to integrate CF7 with Telegram, allowing me to select a form, set a chat ID, and customize notification templates with form data and emojis. It’s lightweight, free, and fully customizable—no need for paid alternatives!

![Clear Checkout](https://raw.githubusercontent.com/Kryku/cf7-to-telegram/refs/heads/main/screenshots/cf7-to-telegram.png)

## Features
* Select any Contact Form 7 form from the admin panel.
* Send notifications to a specified Telegram chat ID.
* Customize message templates with form fields (e.g., `[your-name]`), emojis, or signatures.
* Secure and simple settings interface.

## Installation
1. Download the plugin file (`cf7-to-telegram.zip`).
2. Go to **Plugins > Add New** in the WordPress admin.
3. Upload the plugin file and activate it.

## Settings
Go to **CF7 to Telegram** in the WordPress admin menu.

### Here you can configure:
* **Telegram Bot Token**: Enter your bot token from BotFather.
* **Telegram Chat ID**: Specify the chat ID to receive notifications.
* **Select CF7 Form**: Choose the form to monitor.
* **Message Template**: Customize the notification (e.g., "🔔 New Submission\nName: [your-name]").

## Usage
1. Create a Telegram bot via [BotFather](https://t.me/BotFather) to get a token.
2. Get your chat ID.
3. Configure the plugin settings with the token, chat ID, form, and template.
4. Submit the selected CF7 form to test the notification.

## Example Template
```
🎉 New Form Submission!
Name: [your-name]
Email: [your-email]
Message: [your-message]
```

## Bottom Line
This is a straightforward plugin for personal use or testing. It’s not overly polished for production yet, but it gets the job done. Feel free to tweak the code to suit your needs!
