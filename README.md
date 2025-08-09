# 🚀 Professional Site Monitor Dashboard

A modern, responsive website monitoring solution with a beautiful professional interface built using **Bulma CSS Framework**. Perfect for monitoring websites with real-time status updates, detailed analytics, and easy access - no login required!

**🎯 Zero Setup Required** - Just upload and access your dashboard instantly!

![Professional Dashboard](https://img.shields.io/badge/UI-Bulma%20CSS-00d1b2)
![PHP](https://img.shields.io/badge/PHP-7.4+-777bb4)
![Status](https://img.shields.io/badge/Status-Production%20Ready-success)
![Setup](https://img.shields.io/badge/Setup-Zero%20Config-brightgreen)

## ✨ Features

### 🎨 Professional Interface
- **Modern Design**: Clean, responsive interface using Bulma CSS framework
- **Beautiful Dashboard**: Professional cards, statistics, and data visualization
- **Mobile Responsive**: Fully optimized for desktop, tablet, and mobile devices
- **Icon Integration**: Font Awesome icons throughout the interface
- **Toast Notifications**: Elegant success/error notifications

### 🛡️ Simple & Secure
- **No Authentication Required**: Instant access to monitoring dashboard
- **Data Protection**: JSON files stored outside web access
- **Input Validation**: Comprehensive sanitization and validation
- **Secure Storage**: Protected data directory with .htaccess restrictions

### 📊 Monitoring Capabilities
- **Real-time Status**: Live website monitoring with visual indicators
- **Response Time Tracking**: Measure page load performance
- **SSL Certificate Monitoring**: Track certificate validity and expiration
- **Uptime Statistics**: Calculate and display uptime percentages
- **HTTP Status Codes**: Monitor server response codes
- **Error Logging**: Detailed error messages and debugging

### 🚀 Advanced Features
- **Auto-refresh**: Dashboard updates automatically
- **Bulk Operations**: Check all sites simultaneously
- **Configurable Intervals**: Set custom monitoring frequencies

## 🏗️ Architecture

### Clean Code Structure
```
site-monitor/
├── 📄 index.php          # Main dashboard interface
├── 🔌 api.php            # RESTful API endpoints  
├── 📊 monitor.php        # Site monitoring logic
├── ⚙️ config.php         # Configuration settings
├── 🕒 cron.php           # Automated monitoring
├── 🔒 .htaccess          # Security rules
├── 📁 assets/            # Separated assets
│   ├── 🎨 css/style.css  # Custom styling
│   └── 📜 js/app.js      # Application logic
└── 📁 data/              # Secure data storage (auto-created)
    ├── 🌐 sites.json     # Monitored sites
    └── 🔒 .htaccess      # Access protection
```

### Technology Stack
- **Frontend**: HTML5, Bulma CSS, Font Awesome, Vanilla JavaScript
- **Backend**: PHP 7.4+, JSON file storage
- **Security**: Input validation, file protection, secure storage
- **Monitoring**: cURL, SSL certificate checking, response time measurement

## 🚀 Quick Start

### 1. Installation
```bash
# Clone or download to your web server
# Example: /var/www/html/site-monitor/

# Set permissions (if needed)
chmod 755 site-monitor/
```

### 2. Access Dashboard
- **URL**: `http://yourdomain.com/site-monitor/`
- **Ready to Use**: No login required - instant access!
- **Auto-Setup**: Data directory and files created automatically
- **Start Monitoring**: Add websites immediately - no configuration needed

### 3. Configure Automation (Optional)
```bash
# Add to crontab for automated monitoring
*/5 * * * * /usr/bin/php /path/to/site-monitor/cron.php
```

## 🎯 Usage Guide

### Adding Sites
1. **Open** the dashboard in your browser
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
- **HTTP Status Code**: Server response code
- **Last Checked**: Timestamp of last monitoring check
- **Uptime Percentage**: Calculated availability statistics
- **SSL Certificate**: Expiration date and validity

## ⚙️ Configuration

### Basic Settings (`config.php`)
```php
// Configure monitoring
define('DEFAULT_TIMEOUT', 30);
define('MAX_REDIRECTS', 5);

// Email alerts (optional)
define('EMAIL_ENABLED', true);
define('ALERT_EMAIL', 'admin@yourdomain.com');

// Site checking intervals
define('DEFAULT_CHECK_INTERVAL', 300); // 5 minutes
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

## 🔧 API Reference

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

### Data Protection
- **File Security**: Data stored outside web-accessible directories
- **Input Validation**: All inputs sanitized and validated
- **XSS Protection**: Output escaping and Content Security Policy
- **Secure Storage**: JSON files protected with .htaccess restrictions

### Access Control
- **Directory Protection**: .htaccess files block direct access to data
- **File Permissions**: Proper Unix file permissions
- **Error Handling**: Secure error messages without information disclosure
- **Web Server Security**: Configure access restrictions as needed

## 📱 Mobile Experience

### Responsive Design
- **Touch Optimized**: Large buttons and touch-friendly interface
- **Mobile Navigation**: Collapsible menu for small screens
- **Fast Loading**: Optimized for mobile data connections
- **Offline Indicators**: Clear status when connection is lost

### Performance
- **Lightweight**: Minimal JavaScript and CSS footprint
- **Loading States**: Visual feedback during operations
- **Empty States**: Helpful messages when no data exists

## 🚨 Troubleshooting

### Common Issues

#### File Permission Issues
```bash
# Check file permissions
chmod 750 data/
chown www-data:www-data data/

# Verify web server access
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
# Check JavaScript console for errors
# Verify all assets are loading properly
```

### Performance Optimization

#### For Large Deployments
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

### Alert Management
- **Email Notifications**: Set up SMTP for downtime alerts
- **Escalation**: Multiple contact methods for critical issues
- **False Positives**: Fine-tune timeouts to reduce noise

## 🤝 Contributing

### Development Setup
```bash
# Clone repository
git clone https://github.com/amigdheena/site-monitor.git

# Set up local environment
# No database required - uses JSON files
```

### Code Standards
- **PHP**: Follow PSR-12 coding standards
- **JavaScript**: Use vanilla JS, no frameworks required
- **CSS**: Use Bulma utilities when possible

## 📄 License

This project is licensed under the MIT License - see the LICENSE file for details.

## 🙏 Acknowledgments

- **Bulma CSS**: Beautiful modern CSS framework
- **Font Awesome**: Comprehensive icon library
- **PHP Community**: Excellent documentation and support
- **Open Source**: Built with love for the community

---

**Made with ❤️ for simple, effective website monitoring**