# Trivia Game

## Quick Start

### Prerequisites

- PHP 8.2 or higher
- Composer
- Node.js & npm
- MySQL database
- Web server (Apache/Nginx) or Laravel's built-in server

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

1. **Register/Login**: Create an account or log in to an existing one
2. **Start Game**: Click "Start New Game" from the dashboard
3. **Answer Questions**: You'll get 20 multiple-choice questions
4. **View Results**: See your final score and game statistics
5. **Dashboard**: Track your progress and view game history

### Game Persistence Features

The game now includes automatic save functionality:

- **Automatic Save**: Games are automatically saved after each question
- **Continue Game**: If you leave or reload the page, you can continue from where you left off
- **24-Hour Expiry**: Saved games expire after 24 hours for security and storage management
- **Cookie-Based**: Uses secure cookies and tokens to identify your saved game
- **Cross-Session**: Works even if you close and reopen your browser
- **Guest Support**: Both registered users and guests can save games

**How to use:**

- Start a game and answer some questions
- Close your browser or navigate away
- Return to the homepage - you'll see a "Continue Saved Game" option
- Click "Continue" to resume from your last question
- Or choose "Start New Game" or "Abandon Game" if you prefer

### Admin Functions

1. **Become Admin**

   ```bash
   php artisan user:make-admin your-email@example.com
   ```

2. **Access Admin Panel**: Login and click "Admin Panel" in navigation
3. **Manage Users**: View all users and grant/revoke admin privileges
4. **View Statistics**: Analyze game data and user engagement
5. **Test Games**: Play with correct answers visible for testing

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

- **Framework**: Laravel 12.x
- **Frontend**: Blade templates with Tailwind CSS
- **Database**: MySQL with Eloquent ORM
- **Authentication**: Laravel's built-in authentication system
- **Asset Building**: Vite for modern asset compilation

### Key Components

#### Models

- **User**: Handles authentication and admin privileges
- **GameSession**: Tracks individual game instances and statistics
- **TriviaService**: Manages question generation and validation

#### Controllers

- **TriviaController**: Core game logic and question handling
- **AdminController**: Admin panel functionality
- **Auth Controllers**: User registration and authentication

#### Middleware

- **AdminMiddleware**: Protects admin routes and functionality

### Database Schema

```sql
-- Users table (extended)
users (
    id, name, email, password, is_admin, timestamps
)

-- Game sessions (with persistence support)
game_sessions (
    id, user_id, guest_identifier, session_token, game_state, expires_at,
    total_questions, correct_answers, accuracy, start_time, end_time,
    duration_seconds, question_times, completed, timestamps
)
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

# Session
SESSION_DRIVER=database
SESSION_LIFETIME=120

# Cache
CACHE_STORE=database
```

### Admin Configuration

- Admin privileges are stored in the `is_admin` column
- Use the `user:make-admin` command to grant admin access
- Admin middleware protects all admin routes

## Troubleshooting

### Common Issues

**Database Connection Error**

```bash
# Check database credentials in .env
# Ensure MySQL is running
# Verify database exists
php artisan config:clear
```

**Asset Build Issues**

```bash
# Clear cache and rebuild
npm run build
php artisan view:clear
```

**Permission Issues**

```bash
# Set proper permissions
chmod -R 755 storage bootstrap/cache
```

**Migration Errors**

```bash
# Reset database if needed
php artisan migrate:fresh --seed
```

### Performance Optimization

- Enable caching for production: `CACHE_STORE=redis`
- Use queue system for background tasks: `QUEUE_CONNECTION=redis`
- Optimize assets: `npm run build` for production

## Contributing

1. Fork the repository
2. Create a feature branch: `git checkout -b feature-name`
3. Commit your changes: `git commit -am 'Add feature'`
4. Push to the branch: `git push origin feature-name`
5. Submit a pull request

## Enjoy Playing!

Start your trivia journey today and test your knowledge across various topics. Whether you're a casual player or a trivia enthusiast, this game offers an engaging experience with smart question generation and comprehensive tracking.

**Happy Gaming!**
