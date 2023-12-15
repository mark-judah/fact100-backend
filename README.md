<!DOCTYPE html>
<html>

<body>

<h1>Fact100 Backend - Laravel Lumen API</h1>

<p>The Fact100 backend, built using Laravel Lumen, offers an API for managing blogs, comments, likes, blog categories, and users.</p>

<h2>API Endpoints</h2>
<ul>
  <li>Adding and retrieving blogs</li>
  <li>Managing comments, likes, and blog categories</li>
  <li>User-related operations</li>
</ul>

<h2>Disclaimer</h2>
<p>This project was experimental, testing Lumen's performance. However, Lumen is no longer supported due to Laravel performance improvements(laravel octane)</p>

<h2>Setup Instructions</h2>

<h3>Prerequisites</h3>
<ul>
  <li>PHP installed</li>
  <li>Composer installed</li>
  <li>Lumen CLI</li>
</ul>

<h3>Steps to Set Up the Lumen Project</h3>
<ol>
  <li>Clone the Fact100 backend repository:</li>
  <pre><code>
  git clone https://github.com/mark-jk/fact100-backend.git
  </code></pre>

  <li>Navigate to the project directory:</li>
  <pre><code>
  cd fact100-backend
  </code></pre>

  <li>Install dependencies:</li>
  <pre><code>
  composer install
  </code></pre>

  <li>Create a copy of the .env.example file and name it .env:</li>
  <pre><code>
  cp .env.example .env
  </code></pre>

  <li>Set the database connection in the .env file:</li>
  <pre><code>
  DB_CONNECTION=mysql
  DB_HOST=127.0.0.1
  DB_PORT=3306
  DB_DATABASE=your_database_name
  DB_USERNAME=your_database_username
  DB_PASSWORD=your_database_password
  </code></pre>

  <li>Run migrations and seeders (if any):</li>
  <pre><code>
  php artisan migrate --seed
  </code></pre>

  <li>Start the Lumen development server:</li>
  <pre><code>
  php -S localhost:8000 -t public
  </code></pre>
</ol>

<p>The Fact100 Lumen backend is now available at <code>http://localhost:8000</code>.</p>


</body>
</html>
