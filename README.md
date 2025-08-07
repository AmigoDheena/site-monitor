# 🚀 Professional Site Monitor Dashboard

A modern, responsive website monitoring solution with a beautiful professional interface built using **Bulma CSS Framework**. Perfect for monitoring client websites with real-time status updates, detailed analytics, and automated alerts.

![Professional Dashboard](https://img.shields.io/badge/UI-Bulma%20CSS-00d1b2)
![PHP](https://img.shields.io/badge/PHP-7.4+-777bb4)
![Status](https://img.shields.io/badge/Status-Production%20Ready-success)

## ✨ Features

### 🎨 Professional Interface
- **Modern Design**: Clean, responsive interface using Bulma CSS framework
- **Beautiful Dashboard**: Professional cards, statistics, and data visualization
- **Mobile Responsive**: Fully optimized for desktop, tablet, and mobile devices
- **Icon Integration**: Font Awesome icons throughout the interface
- **Toast Notifications**: Elegant success/error notifications

### 🔒 Advanced Security
- **Secure Authentication**: PHP-based login with session management
- **Rate Limiting**: Protection against brute force attacks
- **Data Protection**: JSON files stored outside web access
- **Input Validation**: Comprehensive sanitization and validation
- **CSRF Protection**: Security tokens prevent cross-site attacks

### 📊 Monitoring Capabilities
- **Real-time Status**: Live website monitoring with visual indicators
- **Response Time Tracking**: Measure page load performance
- **SSL Certificate Monitoring**: Track certificate validity and expiration
- **Uptime Statistics**: Calculate and display uptime percentages
- **HTTP Status Codes**: Monitor server response codes
- **Error Logging**: Detailed error messages and debugging

### 🚨 Alert System
- **Email Notifications**: Automated alerts for downtime/recovery
- **Visual Indicators**: Color-coded status badges and indicators
- **Toast Messages**: Real-time feedback for user actions
- **Status Changes**: Notifications when sites go up/down

### 🔄 Automation
- **Cron Job Support**: Automated background monitoring
- **Auto-refresh**: Dashboard updates automatically
- **Bulk Operations**: Check all sites simultaneously
- **Configurable Intervals**: Set custom monitoring frequencies

## 🏗️ Architecture

### Clean Code Structure
```
site-monitor/
├── 📄 index.php          # Main dashboard interface
├── 🔌 api.php            # RESTful API endpoints  
├── 🔐 auth.php           # Authentication system
├── 📊 monitor.php        # Site monitoring logic
├── ⚙️ config.php         # Configuration settings
├── 🕒 cron.php           # Automated monitoring
├── 🛠️ setup.php          # Installation script
├── 🔒 .htaccess          # Security rules
├── 📁 assets/            # Separated assets
│   ├── 🎨 css/style.css  # Custom styling
│   └── 📜 js/app.js      # Application logic
└── 📁 data/              # Secure data storage
    ├── 👤 users.json     # User accounts
    ├── 🌐 sites.json     # Monitored sites
    └── 📝 *.log          # Log files
```

### Technology Stack
- **Frontend**: HTML5, Bulma CSS, Font Awesome, Vanilla JavaScript
- **Backend**: PHP 7.4+, JSON file storage
- **Security**: Session management, CSRF protection, input validation
- **Monitoring**: cURL, SSL certificate checking, response time measurement

## 🚀 Quick Start

### 1. Installation
```bash
# Clone or download to your web server
# Example: /var/www/html/site-monitor/

# Set permissions
chmod 755 site-monitor/
chmod 750 site-monitor/data/
```

### 2. Run Setup
```bash
# Via command line
php setup.php

# Or visit in browser
http://yourdomain.com/site-monitor/setup.php
```

### 3. Access Dashboard
- **URL**: `http://yourdomain.com/site-monitor/`
- **Username**: `admin`
- **Password**: `SiteMonitor@2025!`

⚠️ **Important**: Change the default password immediately!

### 4. Configure Automation
```bash
# Add to crontab for automated monitoring
*/5 * * * * /usr/bin/php /path/to/site-monitor/cron.php
```

## 🎯 Usage Guide

### Adding Sites
1. **Login** to the dashboard
2. **Fill in** the "Add New Site" form:
   - Site Name: "Company Website"
   - Site URL: "https://example.com"
3. **Click** "Add Site"
4. **Monitor** real-time status updates

### Dashboard Features

#### 📊 Statistics Cards
- **Total Sites**: Number of monitored websites
- **Online**: Currently accessible sites
- **Offline**: Sites experiencing downtime
- **Warning**: Sites with issues (slow response, errors)

#### 🌐 Site Management
- **Add Sites**: Simple form to add new monitoring targets
- **Edit Sites**: Update site information
- **Delete Sites**: Remove sites from monitoring
- **Bulk Check**: Test all sites simultaneously

#### 🔍 Site Details
Each monitored site displays:
- **Status Indicator**: Color-coded status (green/red/yellow)
- **Response Time**: Page load speed in milliseconds
- **HTTP Status**: Server response codes
- **Uptime Percentage**: Availability statistics
- **Last Checked**: Timestamp of last monitoring
- **Error Messages**: Detailed failure information

## ⚙️ Configuration

### Basic Settings (`config.php`)
```php
// Change default credentials
define('DEFAULT_ADMIN_USERNAME', 'your_username');
define('DEFAULT_ADMIN_PASSWORD', 'your_secure_password');

// Configure monitoring
define('DEFAULT_TIMEOUT', 30);
define('MAX_REDIRECTS', 5);

// Email alerts
define('EMAIL_ENABLED', true);
define('ALERT_EMAIL', 'admin@yourdomain.com');
```

### Dashboard Customization (`assets/css/style.css`)
```css
/* Customize colors */
:root {
    --primary-color: #3273dc;
    --success-color: #23d160;
    --warning-color: #ffdd57;
    --danger-color: #ff3860;
}
```

### JavaScript Configuration (`assets/js/app.js`)
```javascript
// Modify monitoring intervals
this.config = {
    checkInterval: 300000, // 5 minutes
    autoRefresh: true,
    toastDuration: 5000
};
```

## 🔧 API Reference

### Authentication Endpoints
```javascript
POST /api.php?action=login      // User login
POST /api.php?action=logout     // User logout  
GET  /api.php?action=me         // Current user info
```

### Site Management
```javascript
GET    /api.php?action=sites           // List all sites
POST   /api.php?action=sites           // Add new site
GET    /api.php?action=site&id={id}    // Get site details
PUT    /api.php?action=site&id={id}    // Update site
DELETE /api.php?action=site&id={id}    // Delete site
```

### Monitoring Operations
```javascript
POST /api.php?action=check-site&id={id}  // Check single site
POST /api.php?action=check-all           // Check all sites
GET  /api.php?action=stats               // Get statistics
```

## 🛡️ Security Features

### Authentication Security
- **Session Management**: Secure PHP sessions with timeouts
- **Login Protection**: Rate limiting and IP-based lockouts
- **Password Security**: Bcrypt hashing for password storage
- **Remember Me**: Secure token-based persistent login

### Data Protection
- **File Security**: Data stored outside web-accessible directories
- **Input Validation**: All inputs sanitized and validated
- **SQL Injection**: N/A (using JSON file storage)
- **XSS Protection**: Output escaping and Content Security Policy

### Access Control
- **Directory Protection**: .htaccess files block direct access
- **File Permissions**: Proper Unix file permissions
- **Error Handling**: Secure error messages without information disclosure

## 📱 Mobile Experience

The dashboard is fully responsive and optimized for mobile devices:

- **Touch-friendly**: Large buttons and touch targets
- **Responsive Layout**: Adapts to all screen sizes
- **Mobile Navigation**: Optimized menu and controls
- **Fast Loading**: Lightweight assets and efficient code

## 🎨 UI/UX Features

### Modern Design Elements
- **Card-based Layout**: Clean, organized information cards
- **Color-coded Status**: Intuitive visual status indicators
- **Smooth Animations**: Hover effects and transitions
- **Professional Typography**: Readable fonts and spacing

### User Experience
- **Real-time Updates**: Live status without page refresh
- **Toast Notifications**: Non-intrusive success/error messages
- **Loading States**: Visual feedback during operations
- **Empty States**: Helpful messages when no data exists

## 🚨 Troubleshooting

### Common Issues

#### Login Problems
```bash
# Check file permissions
chmod 750 data/
chown www-data:www-data data/

# Verify PHP sessions
# Check server error logs
```

#### Monitoring Issues
```bash
# Test cURL functionality
php -m | grep curl

# Check network connectivity
curl -I https://example.com

# Verify cron jobs
crontab -l
```

#### UI Problems
```bash
# Clear browser cache
# Check console for JavaScript errors
# Verify CSS/JS file loading
```

### Performance Optimization

#### For Large Sites (100+ monitored sites)
- **Stagger Monitoring**: Use different cron intervals
- **Increase Timeouts**: Adjust for slow sites
- **Resource Monitoring**: Watch server resources
- **Database Migration**: Consider MySQL for large deployments

#### Server Requirements
- **Small**: 1-20 sites → 512MB RAM, 1 CPU
- **Medium**: 20-100 sites → 1GB RAM, 2 CPU  
- **Large**: 100+ sites → 2GB+ RAM, 4+ CPU

## 📈 Monitoring Best Practices

### Site Selection
- **Critical Sites**: Monitor your most important websites
- **Client Sites**: Track customer website performance
- **Dependencies**: Monitor APIs and services you depend on

### Check Intervals
- **Critical Sites**: Every 1-5 minutes
- **Standard Sites**: Every 5-15 minutes
- **Low Priority**: Every 30-60 minutes

### Alert Configuration
- **Immediate**: Critical business sites
- **Delayed**: Allow for temporary network issues
- **Escalation**: Multiple notification methods

## 🔮 Future Enhancements

### Planned Features
- **Multi-user Support**: Role-based access control
- **Advanced Reporting**: Historical data and charts
- **Slack Integration**: Team notifications
- **Database Support**: MySQL/PostgreSQL option
- **API Webhooks**: Integration with external services
- **Dark Theme**: Modern dark mode interface

### Integration Possibilities
- **Monitoring Services**: Pingdom, UptimeRobot integration
- **Analytics**: Google Analytics integration
- **Ticketing**: JIRA, ServiceNow integration
- **Communication**: Teams, Discord notifications

## 📞 Support

### Getting Help
1. **Check Documentation**: Review this comprehensive guide
2. **Check Logs**: Review error logs and cron logs
3. **Test Individually**: Verify single site monitoring first
4. **Community**: Share issues and solutions

### Maintenance
- **Regular Updates**: Keep PHP and server software updated
- **Log Rotation**: Monitor and rotate log files
- **Backup**: Regular backups of configuration and data
- **Security**: Review and update security measures

---

## 📄 License

This professional site monitoring solution is designed for business use in monitoring client websites and internal infrastructure. Ensure compliance with applicable laws and terms of service when monitoring third-party websites.

## 🏆 Credits

- **Bulma CSS**: Modern CSS framework
- **Font Awesome**: Professional icon library
- **PHP**: Server-side scripting language
- **cURL**: Website connectivity testing

---

**Professional Site Monitor** - Reliable, beautiful, and feature-rich website monitoring for modern businesses.

🌟 **Star this project** if you find it useful for your business!