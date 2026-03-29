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
    if (file_exists($maintenance = __DIR__.'/../legendresources/storage/framework/maintenance.php')) {
        require $maintenance;
    }

    // Register the Composer autoloader...
    require __DIR__.'/../legendresources/vendor/autoload.php';

    // Bootstrap Laravel and handle the request...
    /** @var Application $app */
    $app = require_once __DIR__.'/../legendresources/bootstrap/app.php';

    $app->handleRequest(Request::capture());
?>

<!-- 
put public folder inside legendresources and legend.growbrideges.com 
-->
.env

APP_NAME=Laravel
APP_ENV=local
APP_KEY=base64:S7FVRDGWBNBeRW4bMsI0pwn/98+I3Rh/D1fsuv3zzf8=
APP_DEBUG=true
APP_URL=http://legend.growbridges.com

DB_CONNECTION=mysql
DB_HOST=legend.growbridges.com
DB_PORT=3306
DB_DATABASE=growbrid_growbridges
DB_USERNAME=growbrid_bhat
DB_PASSWORD=tmzrkXklE&pN7$}3

<!-- setup database -->