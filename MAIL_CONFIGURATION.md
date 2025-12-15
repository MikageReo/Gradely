# Mail Configuration Guide

## Issues Found
1. **Timeout Error**: `Maximum execution time of 60 seconds exceeded`
2. **SMTP Connection**: Emails timing out when trying to connect to SMTP server

## Quick Fix (Use Log Driver for Now)

**For immediate testing, use the log driver** - emails will be logged to `storage/logs/laravel.log`:

```env
MAIL_MAILER=log
MAIL_FROM_ADDRESS=noreply@gradely.com
MAIL_FROM_NAME="GRADELY"
```

Then check emails in: `storage/logs/laravel.log`

## Proper SMTP Configuration

### Option 1: Use Gmail SMTP (Recommended for Development)

Add these settings to your `.env` file:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="${APP_NAME}"
```

**Important for Gmail:**
1. You need to use an **App Password**, not your regular Gmail password
2. Enable 2-Step Verification on your Google account
3. Generate an App Password: https://myaccount.google.com/apppasswords
4. Use the 16-character app password in `MAIL_PASSWORD`

### Option 2: Use Mailtrap (Recommended for Testing)

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your-mailtrap-username
MAIL_PASSWORD=your-mailtrap-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@gradely.com
MAIL_FROM_NAME="${APP_NAME}"
```

### Option 3: Use Log Driver (For Development - Emails won't send, just logged)

```env
MAIL_MAILER=log
MAIL_FROM_ADDRESS=noreply@gradely.com
MAIL_FROM_NAME="${APP_NAME}"
```

Emails will be logged to `storage/logs/laravel.log` instead of being sent.

## After Configuration

1. Clear config cache:
   ```bash
   php artisan config:clear
   ```

2. Test email sending by registering a new user

3. Check logs if emails still fail:
   ```bash
   tail -f storage/logs/laravel.log
   ```

## Troubleshooting

- **"Connection refused"**: Check firewall/antivirus blocking port 587
- **"Authentication failed"**: Verify username/password are correct
- **"Scheme not supported"**: Remove `MAIL_URL` from `.env` if present
- **Gmail "Less secure app"**: Use App Password instead
- **"Timeout exceeded"**: 
  - Check if SMTP server is reachable
  - Verify firewall isn't blocking SMTP ports
  - Try using log driver temporarily
  - Check network connectivity

## Current Status

✅ **Email sending is now non-blocking** - requests won't timeout even if email fails
✅ **Timeout set to 30 seconds** - prevents long waits
✅ **Errors are logged** - check `storage/logs/laravel.log` for email errors
✅ **Registration/enrollment continues** - even if email fails

**Recommendation**: Use `MAIL_MAILER=log` for development/testing, then configure proper SMTP for production.

