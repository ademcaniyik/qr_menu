# QR Menu Management System

A modern, efficient QR code-based digital menu management system for restaurants and cafes.

![QR Menu System](public/assets/images/preview.png)

## 🚀 Features

- **Multi-Business Support**: Manage multiple restaurants/cafes from a single platform
- **Dynamic Menu Management**: Easy-to-use interface for menu creation and updates
- **QR Code Generation**: Automatic QR code generation for each menu
- **User Role Management**: Different access levels for administrators and business owners
- **Responsive Design**: Mobile-first approach, works on all devices
- **Secure Authentication**: Advanced security measures for user data protection
- **Real-time Updates**: Instant menu updates without regenerating QR codes
- **Customizable Themes**: Multiple theme options for menu display

## 📋 Requirements

- PHP 8.0 or higher
- MySQL 5.7 or higher
- Composer
- Web Server (Apache/Nginx)

## 🛠️ Installation

1. Clone the repository:
```bash
git clone https://github.com/yourusername/qr-menu-system.git
cd qr-menu-system
```

2. Install dependencies:
```bash
composer install
```

3. Create database and import schema:
```sql
CREATE DATABASE qr_menu;
mysql -u your_username -p qr_menu < database/qr_menu.sql
```

4. Configure environment:
- Copy `.env.example` to `.env`
- Update database credentials and other settings in `.env`

5. Set proper permissions:
```bash
chmod 755 -R storage/
chmod 755 -R cache/
```

6. Start the development server:
```bash
php -S localhost:8000 -t public/
```

## 🔧 Technologies Used

- **Backend**: PHP 8.0+
- **Database**: MySQL 5.7+
- **Frontend**: HTML5, CSS3, JavaScript, Bootstrap 5
- **QR Code**: PHPQRCode, Endroid QR Code
- **Dependencies**: Composer
- **Mail**: PHPMailer
- **Environment**: DotEnv

## 📱 Usage

1. Access the admin panel at `http://your-domain/admin`
2. Login with your credentials
3. Create your restaurant profile
4. Add categories and menu items
5. Generate and download QR codes
6. Display QR codes at your establishment

## 🔐 Security

- Password hashing using modern algorithms
- CSRF protection
- XSS prevention
- SQL injection protection
- Rate limiting
- Secure session management

## 🤝 Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📝 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 📧 Contact

Your Name - [@yourusername](https://twitter.com/yourusername)
Project Link: [https://github.com/yourusername/qr-menu-system](https://github.com/yourusername/qr-menu-system)
