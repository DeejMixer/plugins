# Mixlar Plugin Marketplace

A full-featured plugin marketplace with user authentication, admin portal, and plugin management system. Built with Node.js, Express, MongoDB, and vanilla JavaScript.

## Features

### 🔐 Authentication System
- User signup/login
- Password reset with email verification
- JWT-based authentication
- Role-based access control (Admin/User)

### 🏪 Marketplace
- Browse and search plugins
- Filter by category
- Real-time search
- Plugin details and downloads
- Download tracking
- Elgato-style modern UI

### 👑 Admin Portal
- Dashboard with statistics
- Approve/reject plugin submissions
- Feature plugins
- User management
- Role management
- Plugin and user deletion

### 📦 Plugin Management
- Submit plugins for approval
- Update plugin information
- Delete plugins
- Download tracking
- Category organization

## Tech Stack

**Backend:**
- Node.js
- Express.js
- MongoDB with Mongoose
- JWT for authentication
- Bcrypt for password hashing
- Nodemailer for emails

**Frontend:**
- Vanilla JavaScript
- CSS3 with modern design
- Font Awesome icons
- Responsive layout

## Installation

### Prerequisites
- Node.js (v14 or higher)
- MongoDB (local or Atlas)
- npm or yarn

### Setup

1. **Clone the repository**
```bash
git clone <repository-url>
cd plugins
```

2. **Install dependencies**
```bash
npm install
```

3. **Configure environment**
```bash
cp .env.example .env
```

Edit `.env` and configure:
- MongoDB connection string
- JWT secret
- Email settings (SMTP)
- Admin credentials

4. **Seed the database**
```bash
npm run seed
```

This will:
- Create an admin user
- Import existing plugins from `list.json`
- Set up initial data

5. **Start the server**
```bash
# Development
npm run dev

# Production
npm start
```

6. **Access the application**
- Marketplace: http://localhost:3000
- Login: http://localhost:3000/login.html
- Admin Portal: http://localhost:3000/admin.html

## Default Credentials

After seeding, you can login with:
- **Email**: admin@mixlarlabs.com (or as set in .env)
- **Password**: changeme123 (or as set in .env)

**⚠️ Change these credentials immediately in production!**

## API Endpoints

### Authentication
- `POST /api/auth/signup` - Register new user
- `POST /api/auth/login` - Login user
- `POST /api/auth/forgot-password` - Request password reset
- `POST /api/auth/reset-password` - Reset password with token
- `GET /api/auth/me` - Get current user (requires auth)

### Plugins
- `GET /api/plugins` - Get all approved plugins
- `GET /api/plugins/:id` - Get single plugin
- `POST /api/plugins` - Submit new plugin (requires auth)
- `PUT /api/plugins/:id` - Update plugin (requires auth)
- `DELETE /api/plugins/:id` - Delete plugin (requires auth)
- `POST /api/plugins/:id/download` - Increment download count

### Admin (requires admin role)
- `GET /api/admin/plugins` - Get all plugins (including pending)
- `PUT /api/admin/plugins/:id/approve` - Approve plugin
- `PUT /api/admin/plugins/:id/reject` - Reject plugin
- `PUT /api/admin/plugins/:id/feature` - Toggle featured status
- `DELETE /api/admin/plugins/:id` - Delete any plugin
- `GET /api/admin/users` - Get all users
- `PUT /api/admin/users/:id/role` - Change user role
- `DELETE /api/admin/users/:id` - Delete user
- `GET /api/admin/stats` - Get dashboard statistics

## File Structure

```
/plugins
├── backend/
│   ├── config/
│   │   └── db.js              # Database configuration
│   ├── models/
│   │   ├── User.js            # User model
│   │   └── Plugin.js          # Plugin model
│   ├── routes/
│   │   ├── auth.js            # Authentication routes
│   │   ├── plugins.js         # Plugin routes
│   │   └── admin.js           # Admin routes
│   ├── middleware/
│   │   └── auth.js            # Auth middleware
│   ├── utils/
│   │   └── email.js           # Email utilities
│   ├── scripts/
│   │   └── seed.js            # Database seeding
│   └── server.js              # Express server
├── frontend/
│   └── public/
│       ├── css/
│       │   └── styles.css     # All styles
│       ├── js/
│       │   ├── auth.js        # Auth utilities
│       │   ├── marketplace.js # Marketplace logic
│       │   └── admin.js       # Admin panel logic
│       ├── index.html         # Marketplace
│       ├── login.html         # Login page
│       ├── signup.html        # Signup page
│       ├── forgot-password.html
│       ├── reset-password.html
│       └── admin.html         # Admin portal
├── list.json                  # Initial plugin data
├── package.json
├── .env.example
└── README.md
```

## Email Configuration

For password reset functionality, configure SMTP settings in `.env`:

**Gmail Example:**
```env
EMAIL_HOST=smtp.gmail.com
EMAIL_PORT=587
EMAIL_USER=your-email@gmail.com
EMAIL_PASSWORD=your-app-password
EMAIL_FROM=noreply@mixlarlabs.com
```

For Gmail, you need to:
1. Enable 2-factor authentication
2. Generate an App Password
3. Use the App Password in EMAIL_PASSWORD

## Plugin Categories

- **core**: Essential functionality
- **streaming**: Broadcasting and streaming
- **smarthome**: Smart home integrations
- **control**: Control and automation
- **creative**: Creative workflows

## Plugin Status

- **pending**: Awaiting admin approval
- **approved**: Approved and visible
- **rejected**: Rejected by admin
- **instruction**: Requires setup instructions
- **download**: Available for download
- **installed**: Pre-installed

## Security Features

- Password hashing with bcrypt
- JWT token authentication
- Role-based access control
- Protected admin routes
- SQL injection prevention (MongoDB)
- XSS protection
- CORS enabled

## Development

### Running in Development Mode
```bash
npm run dev
```

Uses nodemon for auto-restart on file changes.

### Adding New Plugins
Plugins can be added via:
1. Admin portal (manual entry)
2. API submission (authenticated users)
3. Database seeding (initial data)

## Production Deployment

1. Set `NODE_ENV=production` in `.env`
2. Use a strong JWT secret
3. Configure proper SMTP settings
4. Use MongoDB Atlas or similar
5. Set up SSL/HTTPS
6. Change default admin credentials
7. Consider rate limiting
8. Set up monitoring

## License

Copyright © 2024 MixlarLabs. All rights reserved.

## Support

For issues and questions:
- GitHub Issues
- Documentation
- Community Forum
