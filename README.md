# Trivia Game

## Features

### Core Game Features
- **Smart Question Generation**: Dynamic trivia questions across multiple categories
- **Auto-Save Functionality**: Games automatically save after each question
- **Cross-Session Persistence**: Continue games even after browser restarts
- **Guest & Registered User Support**: Play without registration or create an account for enhanced features
- **Comprehensive Statistics**: Detailed performance tracking and analytics
- **24-Hour Game Expiry**: Automatic cleanup of expired saved games

### Admin Management
- **User Management**: Complete user administration with role assignment
- **Terms of Service Editor**: Advanced WYSIWYG editor with version control
- **Analytics Dashboard**: Game statistics, user engagement metrics
- **Question Testing**: Test mode with answer visibility for administrators
- **Contact Information Management**: Dynamic contact details in terms of service

## Quick Start

### Prerequisites

- **PHP 8.2** or higher
- **Composer** for dependency management
- **Node.js & npm** for asset compilation
- **MySQL** database
- **Web server** (Apache/Nginx) or Laravel's built-in server

### Installation

1. **Clone the repository**

   ```bash
   git clone https://github.com/your-username/trivia-game.git
   cd trivia-game
   ```

2. **Install PHP dependencies**

   ```bash
   composer install
   ```

3. **Install JavaScript dependencies**

   ```bash
   npm install
   ```

4. **Environment Setup**

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Configure Database**

   Edit `.env` file with your database credentials:

   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=trivia
   DB_USERNAME=root
   DB_PASSWORD=your_password
   ```

6. **Run Migrations & Seed Data**

   ```bash
   php artisan migrate
   php artisan db:seed
   ```

7. **Build Assets**

   ```bash
   npm run build
   ```

8. **Start the Development Server**
   ```bash
   php artisan serve
   ```

Visit `http://localhost:8000` to access the application.

## Usage

### Playing the Game

1. **Register/Login**: Create an account or log in to an existing one (optional for basic gameplay)
2. **Start Game**: Click "Start New Game" from the dashboard
3. **Answer Questions**: You'll get 20 multiple-choice questions with intelligent difficulty progression
4. **View Results**: See your final score, accuracy, and detailed game statistics
5. **Dashboard**: Track your progress and view comprehensive game history

**How to use:**

- Start a game and answer some questions
- Close your browser or navigate away
- Return to the homepage - you'll see a "Continue Saved Game" option
- Click "Continue" to resume from your last question
- Choose "Start New Game" or "Abandon Game" for fresh gameplay

### Admin Functions

1. **Become Admin**

   ```bash
   php artisan user:make-admin your-email@example.com
   ```

2. **Access Admin Panel**: Login and click "Admin Panel" in navigation
3. **Manage Users**: View all users, grant/revoke admin privileges, and monitor user activity
4. **Terms of Service Management**: 
   - Advanced WYSIWYG editor with real-time preview
   - Version control and history tracking
   - Contact information management
   - Export functionality (HTML/Markdown)
5. **View Analytics**: Comprehensive game statistics, user engagement metrics, and performance insights
6. **Test Games**: Play with correct answers visible for quality assurance testing

### Maintenance Commands

The application includes several useful commands for game management:

```bash
# Clean up expired saved games (runs automatically daily)
php artisan trivia:clean-expired-games

# Clear all game sessions for testing
php artisan trivia:clear-sessions

# Clear sessions for a specific user
php artisan trivia:clear-sessions --user=email@example.com

# Make a user an admin
php artisan user:make-admin email@example.com
```

## Technical Details

### Architecture

- **Framework**: Laravel 12.x (Latest)
- **Frontend**: Blade templates with Tailwind CSS 4.0
- **Database**: MySQL with Eloquent ORM
- **Authentication**: Laravel's built-in authentication system
- **Asset Building**: Vite for modern, fast asset compilation
- **Styling**: SCSS with modern CSS features
- **JavaScript**: Modern ES6+ with enhanced interactivity

### Key Components

#### Models

- **User**: Enhanced authentication with admin privileges and comprehensive user management
- **GameSession**: Advanced game state tracking with persistence, statistics, and cross-session support
- **TermsOfService**: Complete terms management with versioning, contact information, and admin controls
- **TriviaService**: Intelligent question generation, validation, and difficulty progression

#### Controllers

- **TriviaController**: Core game logic, question handling, and session management
- **AdminController**: Comprehensive admin panel with user management, analytics, and terms of service editor
- **Auth Controllers**: Enhanced user registration, authentication, and password recovery

#### Middleware

- **AdminMiddleware**: Robust protection for admin routes and functionality
- **CheckSavedGame**: Intelligent game persistence and session management

### Database Schema

```sql
-- Users table (enhanced)
users (
    id, name, email, password, is_admin, 
    email_verified_at, remember_token, timestamps
)

-- Game sessions (with advanced persistence)
game_sessions (
    id, user_id, guest_identifier, session_token, game_state, expires_at,
    total_questions, correct_answers, accuracy, start_time, end_time,
    duration_seconds, question_times, completed, timestamps
)

-- Terms of service (with versioning)
terms_of_service (
    id, content, version, effective_date, is_active, updated_by,
    contact_email, contact_phone, contact_address, company_name, timestamps
)

-- Enhanced caching and session tables
cache, sessions, jobs, migrations
```

## Configuration

### Environment Variables

Key configuration options in `.env`:

```env
# Application
APP_NAME="Trivia Game"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=trivia
DB_USERNAME=root
DB_PASSWORD=

# Session & Cache
SESSION_DRIVER=database
SESSION_LIFETIME=120
CACHE_STORE=database

# Mail Configuration (for password reset)
MAIL_MAILER=log  # Use 'smtp' for production
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@triviagame.com"
MAIL_FROM_NAME="Trivia Game"
```

### Admin Configuration

- Admin privileges are stored in the `is_admin` column
- Use the `user:make-admin` command to grant admin access
- Admin middleware protects all admin routes
- Terms of Service management with version control
- Contact information management for legal compliance

### Email Configuration

For production, configure SMTP settings:

```env
# Gmail Example
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_ENCRYPTION=tls

# Outlook Example  
MAIL_HOST=smtp-mail.outlook.com

# Custom SMTP
MAIL_HOST=your-smtp-server.com
```

## Troubleshooting

### Common Issues

**Database Connection Error**

```bash
# Check database credentials in .env
# Ensure MySQL is running
# Verify database exists
php artisan config:clear
php artisan cache:clear
```

**Asset Build Issues**

```bash
# Clear cache and rebuild
npm run build
php artisan view:clear
php artisan route:clear
```

**Permission Issues**

```bash
# Set proper permissions (Linux/macOS)
chmod -R 755 storage bootstrap/cache

# Windows (run as administrator)
icacls storage /grant Users:(OI)(CI)F /T
icacls bootstrap/cache /grant Users:(OI)(CI)F /T
```

**Migration Errors**

```bash
# Reset database if needed (WARNING: This will delete all data)
php artisan migrate:fresh --seed

# Or run migrations step by step
php artisan migrate:status
php artisan migrate
```

**Game Session Issues**

```bash
# Clear expired game sessions
php artisan trivia:clean-expired-games

# Clear all sessions for testing
php artisan trivia:clear-sessions
```

### Performance Optimization

**Production Optimization:**

```bash
# Enable caching
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Optimize autoloader
composer install --optimize-autoloader --no-dev

# Build production assets
npm run build
```

**Environment Recommendations:**

- Enable Redis for caching: `CACHE_STORE=redis`
- Use queue system for background tasks: `QUEUE_CONNECTION=redis`
- Enable OPcache for PHP in production
- Configure proper session storage for multiple servers

### Monitoring & Debugging

```bash
# View application logs
tail -f storage/logs/laravel.log

# Check queue status
php artisan queue:work

# Monitor failed jobs
php artisan queue:failed
```

## Enjoy Playing!

Start your trivia journey today and test your knowledge across various topics! Whether you're a casual player looking for quick entertainment or a trivia enthusiast seeking challenging questions, this game offers:

**Happy Gaming!**

---

<div align="center">

**Built with ❤️ using Laravel 12.x**

[Report Bug](https://github.com/your-username/trivia-game/issues) • [Request Feature](https://github.com/your-username/trivia-game/issues) • [Documentation](https://github.com/your-username/trivia-game/wiki)

</div>
