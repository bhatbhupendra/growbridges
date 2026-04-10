<!-- 
commands larvel
php artisan migrate:fresh 
-->

<!-- 
npm run build
 -->


<!-- 
setup
index.php 
-->
<?php
    use Illuminate\Foundation\Application;
    use Illuminate\Http\Request;

    define('LARAVEL_START', microtime(true));

    // Determine if the application is in maintenance mode...
    if (file_exists($maintenance = __DIR__.'/../studentresources/storage/framework/maintenance.php')) {
        require $maintenance;
    }

    // Register the Composer autoloader...
    require __DIR__.'/../studentresources/vendor/autoload.php';

    // Bootstrap Laravel and handle the request...
    /** @var Application $app */
    $app = require_once __DIR__.'/../studentresources/bootstrap/app.php';

    $app->handleRequest(Request::capture());
?>

<!-- 
put public folder inside legendresources and legend.growbrideges.com 
-->
.env

APP_NAME=Laravel
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://legend.growbridges.com

DB_CONNECTION=mysql
DB_HOST=student.growbridges.com
DB_PORT=3306
DB_DATABASE=growbrid_growbridges
DB_USERNAME=growbrid_bhat
DB_PASSWORD=tmzrkXklE&pN7$}3

<!-- setup database -->


stream_socket_server
cd legendresources
rm -rf public/storage
php artisan storage:link
ls -l public

ln -s /home/growbrid/studentresources/storage/app/public /home/growbrid/student.growbridges.com/storage