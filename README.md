# 🗓️ Task Scheduler in PHP

This is a lightweight PHP-based task scheduler that allows users to:

- ✅ Add, delete, and complete tasks
- 📧 Subscribe for task reminders via email
- 🔐 Verify subscriptions through email
- 🔕 Unsubscribe with a single click
- 🔁 Send reminder emails every hour using a CRON job

## 📁 Features

- No database used — data stored in JSON files.
- Clean UI with modern styling and gradient colors.
- Emails are sent in HTML format using PHP's `mail()` function.
- All logic lives inside the `src/` folder.

## 🚀 Tech Stack

- PHP (no external libraries)
- JSON files for data storage
- CRON for hourly reminders
- SMTP (tested with Papercut for local email testing)

## 🛠 How to Run Locally

1. Clone this repository.
2. Start a local PHP server:
   ```bash
   php -S localhost:8000 -t src/
````

3. Use [Papercut SMTP](https://github.com/ChangemakerStudios/Papercut-SMTP) to test emails locally.
4. Run the CRON script manually:

   ```bash
   php src/cron.php
   ```

## 📦 File Structure

```
src/
│
├── functions.php         # All business logic
├── index.php             # Main UI and user interaction
├── verify.php            # Handles subscription verification
├── unsubscribe.php       # Handles unsubscribe requests
├── cron.php              # Sends reminder emails
├── setup_cron.sh         # Auto-sets up CRON job (Linux/Mac only)
```

## 📜 Notes

* Emails are stored in `subscribers.txt` and `pending_subscriptions.txt`
* Tasks are saved in `tasks.txt` using JSON format
* HTML structure follows strict guidelines

---
