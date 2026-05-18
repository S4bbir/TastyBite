<?php
declare(strict_types=1);

if (PHP_SAPI === 'cli-server') {
    $requested = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    $file = __DIR__ . str_replace('/', DIRECTORY_SEPARATOR, rawurldecode($requested));
    if (is_file($file)) {
        return false;
    }
}

$basePath = dirname(__DIR__);

require_once $basePath . '/config/config.php';
require_once $basePath . '/config/database.php';

spl_autoload_register(function (string $class): void {
    foreach (['core', 'models', 'controllers'] as $folder) {
        $path = BASE_PATH . "/food_page/{$folder}/{$class}.php";
        if (is_file($path)) {
            require_once $path;
            return;
        }
    }
});

session_name('online_food_blog_session');
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'httponly' => true,
    'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
    'samesite' => 'Lax',
]);
session_start();

require_once $basePath . '/config/helpers.php';
attempt_remember_login();

function path_starts_with(string $value, string $prefix): bool
{
    return $prefix === '' || strncmp($value, $prefix, strlen($prefix)) === 0;
}

function normalize_route(string $route): string
{
    $route = trim($route, " \t\n\r\0\x0B/");
    $aliases = [
        '' => 'home',
        'login' => 'auth/login',
        'register' => 'auth/register',
        'logout' => 'auth/logout',
        'profile' => 'auth/profile',
        'restaurants' => 'browse/restaurants',
        'food-experience' => 'food',
        'food-experience/create' => 'food/create',
        'admin/menu-items' => 'admin/menu',
        'api/food-exp/comments/add' => 'api/food-comments/add',
        'api/food-exp/comments/delete' => 'api/food-comments/delete',
        'api/food-exp/posts/delete' => 'api/food-posts/delete',
    ];

    return $aliases[$route] ?? $route;
}

function match_clean_route(string $path): array
{
    $path = trim($path, '/');
    $static = [
        '' => 'home',
        'login' => 'auth/login',
        'register' => 'auth/register',
        'logout' => 'auth/logout',
        'profile' => 'auth/profile',
        'restaurants' => 'browse/restaurants',
        'food-experience' => 'food',
        'food-experience/create' => 'food/create',
        'admin/dashboard' => 'admin/dashboard',
        'admin/restaurants' => 'admin/restaurants',
        'admin/restaurants/create' => 'admin/restaurant/form',
        'admin/restaurants/save' => 'admin/restaurant/save',
        'admin/restaurants/delete' => 'admin/restaurant/delete',
        'admin/menu-items' => 'admin/menu',
        'admin/menu-items/create' => 'admin/menu/form',
        'admin/menu-items/save' => 'admin/menu/save',
        'admin/menu-items/delete' => 'admin/menu/delete',
        'admin/members' => 'admin/members',
        'admin/moderation' => 'admin/moderation',
        'api/search' => 'api/search',
        'api/reviews/add' => 'api/reviews/add',
        'api/reviews/delete' => 'api/reviews/delete',
        'api/restaurant-reviews/add' => 'api/restaurant-reviews/add',
        'api/restaurant-reviews/delete' => 'api/restaurant-reviews/delete',
        'api/food-exp/comments/add' => 'api/food-comments/add',
        'api/food-exp/comments/delete' => 'api/food-comments/delete',
        'api/food-comments/add' => 'api/food-comments/add',
        'api/food-comments/delete' => 'api/food-comments/delete',
        'api/food-exp/posts/delete' => 'api/food-posts/delete',
        'api/food-posts/delete' => 'api/food-posts/delete',
        'api/members/delete' => 'api/members/delete',
    ];

    if (isset($static[$path])) {
        return [$static[$path], []];
    }

    $patterns = [
        '#^restaurants/(\d+)$#' => ['browse/restaurant', 'id'],
        '#^menu-items/(\d+)$#' => ['browse/item', 'id'],
        '#^food-experience/(\d+)/edit$#' => ['food/edit', 'id'],
        '#^admin/restaurants/(\d+)/edit$#' => ['admin/restaurant/form', 'id'],
        '#^admin/menu-items/(\d+)/edit$#' => ['admin/menu/form', 'id'],
        '#^api/reviews/(\d+)$#' => ['api/reviews/delete', 'id'],
        '#^api/reviews/delete/(\d+)$#' => ['api/reviews/delete', 'id'],
        '#^api/restaurant-reviews/(\d+)$#' => ['api/restaurant-reviews/delete', 'id'],
        '#^api/restaurant-reviews/delete/(\d+)$#' => ['api/restaurant-reviews/delete', 'id'],
        '#^api/food-exp/comments/(\d+)$#' => ['api/food-comments/delete', 'id'],
        '#^api/food-exp/comments/delete/(\d+)$#' => ['api/food-comments/delete', 'id'],
        '#^api/food-exp/posts/(\d+)$#' => ['api/food-posts/delete', 'id'],
        '#^api/food-exp/posts/delete/(\d+)$#' => ['api/food-posts/delete', 'id'],
        '#^api/members/(\d+)$#' => ['api/members/delete', 'id'],
        '#^api/members/delete/(\d+)$#' => ['api/members/delete', 'id'],
    ];

    foreach ($patterns as $pattern => [$route, $key]) {
        if (preg_match($pattern, $path, $matches)) {
            return [$route, [$key => (int) $matches[1]]];
        }
    }

    return [normalize_route($path), []];
}

function route_from_request(): array
{
    if (!empty($_GET['route'])) {
        return [normalize_route((string) $_GET['route']), []];
    }

    $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    $path = rawurldecode($path);
    $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
    $scriptDir = trim($scriptDir, '/');
    $path = trim(str_replace('\\', '/', $path), '/');

    if ($scriptDir !== '' && path_starts_with($path, $scriptDir)) {
        $path = trim(substr($path, strlen($scriptDir)), '/');
    }

    if ($path === 'index.php') {
        $path = '';
    } elseif (path_starts_with($path, 'index.php/')) {
        $path = substr($path, strlen('index.php/'));
    }

    return match_clean_route($path);
}

[$route, $routeParams] = route_from_request();
foreach ($routeParams as $key => $value) {
    $_GET[$key] = $value;
}

$method = request_method();

try {
    switch ($route) {
        case 'home':
            (new HomeController())->index();
            break;

        case 'auth/register':
            $method === 'POST' ? (new AuthController())->register() : (new AuthController())->registerForm();
            break;

        case 'auth/login':
            $method === 'POST' ? (new AuthController())->login() : (new AuthController())->loginForm();
            break;

        case 'auth/logout':
            (new AuthController())->logout();
            break;

        case 'auth/profile':
            $method === 'POST' ? (new AuthController())->profileUpdate() : (new AuthController())->profileForm();
            break;

        case 'browse/restaurants':
            (new BrowseController())->restaurants();
            break;

        case 'browse/restaurant':
            (new BrowseController())->restaurant();
            break;

        case 'browse/item':
            (new BrowseController())->item();
            break;

        case 'food':
            (new FoodExperienceController())->index();
            break;

        case 'food/create':
            $method === 'POST' ? (new FoodExperienceController())->save() : (new FoodExperienceController())->form();
            break;

        case 'food/edit':
            $method === 'POST' ? (new FoodExperienceController())->save() : (new FoodExperienceController())->form();
            break;

        case 'admin/dashboard':
            (new AdminController())->dashboard();
            break;

        case 'admin/restaurants':
            (new AdminController())->restaurants();
            break;

        case 'admin/restaurant/form':
            (new AdminController())->restaurantForm();
            break;

        case 'admin/restaurant/save':
            (new AdminController())->saveRestaurant();
            break;

        case 'admin/restaurant/delete':
            (new AdminController())->deleteRestaurant();
            break;

        case 'admin/menu':
            (new AdminController())->menuItems();
            break;

        case 'admin/menu/form':
            (new AdminController())->menuForm();
            break;

        case 'admin/menu/save':
            (new AdminController())->saveMenuItem();
            break;

        case 'admin/menu/delete':
            (new AdminController())->deleteMenuItem();
            break;

        case 'admin/members':
            (new AdminController())->members();
            break;

        case 'admin/moderation':
            (new AdminController())->moderation();
            break;

        case 'api/search':
            (new ApiController())->search();
            break;

        case 'api/reviews/add':
            (new ApiController())->addReview();
            break;

        case 'api/reviews/delete':
            (new ApiController())->deleteReview();
            break;

        case 'api/restaurant-reviews/add':
            (new ApiController())->addRestaurantReview();
            break;

        case 'api/restaurant-reviews/delete':
            (new ApiController())->deleteRestaurantReview();
            break;

        case 'api/food-comments/add':
            (new ApiController())->addFoodComment();
            break;

        case 'api/food-comments/delete':
            (new ApiController())->deleteFoodComment();
            break;

        case 'api/food-posts/delete':
            (new ApiController())->deleteFoodPost();
            break;

        case 'api/members/delete':
            (new ApiController())->deleteMember();
            break;

        default:
            http_response_code(404);
            render('errors/404', ['pageTitle' => 'Not Found']);
    }
} catch (PDOException $exception) {
    http_response_code(500);
    render('errors/database', [
        'pageTitle' => 'Database Error',
        'message' => $exception->getMessage(),
    ]);
}
