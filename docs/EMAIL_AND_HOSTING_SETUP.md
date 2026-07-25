# eKesihatan Email + Hosting Setup (Brevo)

## 1) Is Brevo 300 emails/day enough?

Use this quick estimate:

- Registration success email: **1 per new user**
- Booking success email: **1 per booking**
- Reschedule email: **only when changed**

Example:

- 40 new users/day
- 120 bookings/day
- 20 reschedules/day

Total = **180 emails/day** (Brevo free tier is enough).

If daily volume is consistently above 300, upgrade Brevo plan before go-live.

---

## 2) Recommended sender identity

Do **not** send using `noreply@gmail.com`.

Use your domain:

- `no-reply@ekesihatan.my`

This improves trust and inbox delivery.

---

## 3) Required DNS records (Brevo)

In your domain DNS panel for `ekesihatan.my`, add Brevo-provided records:

- SPF (TXT)
- DKIM (TXT/CNAME)
- Brevo tracking/verification records
- DMARC (TXT) recommendation:
  - Start with: `v=DMARC1; p=none; rua=mailto:admin@ekesihatan.my`
  - Tighten policy later after validation.

---

## 4) Laravel environment settings

Set these values in `.env`:

```env
MAIL_MAILER=smtp
MAIL_SCHEME=tls
MAIL_HOST=smtp-relay.brevo.com
MAIL_PORT=587
MAIL_USERNAME=your-brevo-login@email.com
MAIL_PASSWORD=your-brevo-smtp-key
MAIL_FROM_ADDRESS=no-reply@ekesihatan.my
MAIL_FROM_NAME="Unit Kesihatan UiTM Perlis"
MAIL_EHLO_DOMAIN=ekesihatan.my
```

Apply config:

```bash
php artisan config:clear
php artisan config:cache
```

---

## 5) Notifications implemented in this app

The system now sends email notifications for:

1. Successful registration
2. Successful appointment booking
3. Appointment rescheduled by admin

---

## 6) Hosting recommendation

For production, avoid free hosting for this app because you need:

- stable uptime
- queue worker support
- cron jobs
- reliable SMTP/network access
- backups

Minimum reliable setup:

- 1 small VPS (2 vCPU, 2-4 GB RAM)
- PHP 8.3 + MySQL
- Nginx
- SSL (Let's Encrypt)
- Supervisor for queue worker

Queue worker example:

```bash
php artisan queue:work --tries=3 --timeout=120
```

Cron example:

```cron
* * * * * cd /var/www/ekesihatan && php artisan schedule:run >> /dev/null 2>&1
```

---

## 7) Post-deploy checks

1. Register a test patient account -> receives registration email
2. Book an appointment -> receives booking email
3. Mark appointment as `rescheduled` in admin -> receives reschedule email
4. Check Brevo logs for delivery/bounce
5. Check spam folder and adjust DNS/authentication if needed
