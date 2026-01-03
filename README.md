# EventHorizon - Modern Event Management Platform

A cutting-edge event management platform with premium UI, real-time features, and comprehensive event organization tools.

![PHP](https://img.shields.io/badge/PHP-8.0+-777BB4?style=flat&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-5.7+-4479A1?style=flat&logo=mysql&logoColor=white)
![License](https://img.shields.io/badge/License-MIT-green.svg)

---

## 🌟 Features

### 📅 Event Management
- **Multi-step Event Creation** with auto-saving drafts
- **Ticket Tiers** - Free, VIP, Early Bird with custom pricing
- **Approval System** - Auto or manual guest approval
- **Interactive Seat Maps** - Visual seat selection and booking
- **Rich Event Details** - Location mapping, categories, media headers

### 🎟️ RSVP & Ticketing
- **Smart RSVP** - One-time profile completion
- **Digital Event Kits** - PDF with QR code, calendar invite, certificates
- **QR Authentication** - Secure entry validation via QR scanning
- **Payment Integration** - PayPal sandbox for paid events

### 💬 Community Features
- **Event Chat** - Real-time messaging for attendees
- **Memories Gallery** - Post-event photo sharing
- **Polls** - Live polling for attendee feedback
- **FAQs** - Built-in Q&A section
- **Referral System** - Invite friends with tracking

### 📊 Analytics & Insights
- **Real-time Dashboard** - RSVP trends and attendance tracking
- **Visual Charts** - 7-day analytics with Chart.js
- **Attendee Management** - Approve/reject with email notifications
- **Badge System** - Gamification with achievement badges

### 🎨 Premium UI/UX
- **Glassmorphism Design** - Modern translucent aesthetics
- **Dark Mode** - Neon accents and glow effects
- **Toast Notifications** - Beautiful success/error messages
- **Responsive** - Mobile-first design with TailwindCSS
- **Animations** - Smooth transitions and micro-interactions

---

## 🚀 Quick Start

### Prerequisites
- **XAMPP** (or WAMP/MAMP) with:
  - PHP 8.0+
  - MySQL 5.7+
  - Apache Web Server

### Installation (3 Steps)

1. **Extract to XAMPP:**
   ```bash
   # Extract to:
   C:\xampp\htdocs\Event-Horizin
   ```

2. **Configure Environment:**
   ```bash
   # Copy environment template
   copy .env.example .env
   
   # Edit .env and set your MySQL password
   # For XAMPP: leave DB_PASS empty
   ```

3. **Setup Database:**
   - Open phpMyAdmin: http://localhost/phpmyadmin
   - Create database: `event_platform`
   - Import `event_platform.sql`

4. **Access Application:**
   ```
   http://localhost/Event-Horizin
   ```

**That's it!** 🎉 The app auto-creates all required directories on first run.

---

## ⚙️ Configuration

All settings are in the `.env` file:

### Required Settings
```env
DB_HOST=localhost
DB_NAME=event_platform
DB_USER=root
DB_PASS=              # Your MySQL password (empty for XAMPP)
```

### Optional Settings
```env
# Email (for notifications)
SMTP_USER=your_email@gmail.com
SMTP_PASS=your_app_password

# Application
APP_URL=http://localhost/Event-Horizin
APP_NAME=EventHorizon
```

> **Note:** Email is optional. The app works perfectly without it - emails just won't be sent.

---

## 📧 Email Setup (Optional)

To enable email notifications:

1. **Get Gmail App Password:**
   - Visit: https://myaccount.google.com/apppasswords
   - Enable 2-Step Verification
   - Generate app password for "Mail"

2. **Update .env:**
   ```env
   SMTP_USER=your_email@gmail.com
   SMTP_PASS=abcdefghijklmnop  # 16-char app password
   ```

---

## 🛠️ Technology Stack

- **Backend:** PHP 8.0+ (MVC Architecture)
- **Frontend:** TailwindCSS, Alpine.js, Lucide Icons
- **Database:** MySQL 5.7+
- **PDF Generation:** FPDF (with alpha transparency)
- **QR Codes:** phpqrcode + QRServer API
- **Charts:** Chart.js
- **Email:** SMTP (Gmail)

---

## 📁 Project Structure

```
Event-Horizin/
├── app/
│   ├── controllers/     # MVC Controllers
│   ├── models/          # Database Models
│   ├── views/           # View Templates
│   └── libs/            # Helper Libraries
├── config/
│   ├── Config.php       # Environment Config Loader
│   └── database.php     # Database Connection
├── public/
│   ├── uploads/         # User Uploads (auto-created)
│   └── qrcodes/         # Generated QR Codes
├── vendor/              # Third-party Libraries
├── .env.example         # Configuration Template
├── bootstrap.php        # App Initialization
├── index.php            # Entry Point
└── event_platform.sql   # Database Schema
```

---

## 🔧 Troubleshooting

### Database Connection Error
```
Error: Access denied for user 'root'@'localhost'
```
**Fix:** Check `DB_PASS` in `.env` file matches your MySQL password.

### Unknown Database Error
```
Error: Unknown database 'event_platform'
```
**Fix:** 
1. Open phpMyAdmin
2. Create database: `event_platform`
3. Import `event_platform.sql`

### Upload Directory Errors
**Fix:** Directories are auto-created. If issues persist:
```bash
cd public
mkdir uploads\events uploads\temp uploads\avatars uploads\memories
```

### Email Not Sending
**This is normal if email is not configured.** Check logs:
- `C:\xampp\apache\logs\error.log`
- Look for: "Email not configured"

To enable emails, add `SMTP_USER` and `SMTP_PASS` to `.env`.

---

## 🎯 Key Features Explained

### Digital Event Kits
Auto-generated PDF packages sent to approved attendees containing:
- **Entry Pass** with QR code for check-in
- **Calendar Invite** (.ics file)
- **Event Details** and location
- **Ritual Guide** with step-by-step instructions
- **Certificates** and affirmation cards

### QR Authentication
- Unique QR code per attendee
- Scan at event entry for validation
- Real-time check-in status updates
- Works offline (pre-generated)

### Seat Selection
- Visual interactive seat map
- Real-time availability
- Section-based pricing (VIP, Standard)
- Automatic seat reservation on payment

---

## 🔐 Security Features

✅ **Environment Variables** - Credentials never in code  
✅ **SQL Injection Protection** - PDO prepared statements  
✅ **Session Management** - Secure PHP sessions  
✅ **QR Token Validation** - Cryptographic tokens  
✅ **File Upload Validation** - Type and size checks

---

## 📝 License

This project is licensed under the MIT License.

---

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

---

## 📞 Support

For issues or questions:
1. Check the troubleshooting section above
2. Review Apache error logs: `C:\xampp\apache\logs\error.log`
3. Open an issue on GitHub

---

## 🎨 Screenshots

> Add screenshots of your application here

---

## 🚀 Deployment

### For Production:

1. **Update .env:**
   ```env
   APP_ENV=production
   APP_URL=https://yourdomain.com
   ```

2. **Disable Error Display:**
   In `index.php`, comment out:
   ```php
   // ini_set('display_errors', 1);
   // error_reporting(E_ALL);
   ```

3. **Secure .env:**
   Ensure `.env` is not web-accessible (it's in root, should be fine)

4. **Enable HTTPS:**
   Use SSL certificate for production

---

## ⭐ Features Roadmap

- [ ] Social media integration
- [ ] Mobile app (React Native)
- [ ] Advanced analytics dashboard
- [ ] Multi-language support
- [ ] Stripe payment integration
- [ ] Event templates library

---

**Built with ❤️ for seamless event management**
