# JoyArt

JoyArt is a web-based platform designed to facilitate the creation and sharing of digital art. Built with Laravel and Tailwind CSS, it offers a seamless and intuitive user experience for artists and enthusiasts alike.

## Features

- **User Registration and Authentication**: Secure user sign-up and login functionalities.
- **Art Creation Tools**: A suite of tools enabling users to create and edit digital artworks directly within the platform.
- **API fetching from [safebooru](https://safebooru.org/)**: Admin command for fatch some artworks from free API safebooru.
- **Gallery Display**: Showcase of user-created artworks in a public gallery.
- **Likes comments and prodiles**: Showcase of users activity and profile information.
- **Responsive Design**: Optimized for various devices, ensuring accessibility and usability.

## Technologies Used

- **PHP 8.4+**: Server-side scripting language that generates dynamic content on the server and interacts with databases, forms, and sessions..
- **Laravel 11+**: PHP framework for building robust web applications.
- **Tailwind CSS**: Utility-first CSS framework for rapid UI development.
- **JavaScript**: In this web-product utility for CSS.
- **Vite**: Frontend build tool for fast and efficient development.
- **PostgreSQL**: Relational database management system for data storage.

## Installation

1. **Clone the repository**:
   ```bash
   git clone https://github.com/k1fl1k/joyart.git
   cd joyart
   ```

2. **Install dependencies**:
   ```bash
   composer install
   npm install
   ```

3. **Set up environment variables**:
   - Copy `.env.example` to `.env`:
     ```bash
     cp .env.example .env
     ```
   - Generate application key:
     ```bash
     php artisan key:generate
     ```

4. **Configure database**:
   - Update `.env` with your database credentials.
   - Run migrations:
     ```bash
     php artisan migrate
     ```
   - Run seeder:
     ```bash
     php artisan db:seed
     ```

5. **Build frontend assets**:
   ```bash
   npm run dev
   ```
   OR
   ```bash
   npm run build
   ```

7. **Start the development server**:
   ```bash
   php artisan serve
   ```

## License

This project is licensed under the MIT License.
