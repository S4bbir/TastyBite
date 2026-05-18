<?php
declare(strict_types=1);

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function base_url(): string
{
    $script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
    $dir = rtrim(str_replace('\\', '/', dirname($script)), '/');

    return ($dir === '' || $dir === '.' || $dir === '/') ? '' : $dir;
}

function route_path(string $route, array &$params = []): string
{
    $take = static function (string $key) use (&$params): ?string {
        if (!array_key_exists($key, $params) || $params[$key] === '') {
            return null;
        }

        $value = (string) $params[$key];
        unset($params[$key]);
        return rawurlencode($value);
    };

    switch ($route) {
        case 'home':
            return '/';
        case 'auth/login':
            return '/login';
        case 'auth/register':
            return '/register';
        case 'auth/logout':
            return '/logout';
        case 'auth/profile':
            return '/profile';
        case 'browse/restaurants':
            return '/restaurants';
        case 'browse/restaurant':
            $id = $take('id');
            return $id ? "/restaurants/{$id}" : '/restaurants';
        case 'browse/item':
            $id = $take('id');
            return $id ? "/menu-items/{$id}" : '/restaurants';
        case 'food':
            return '/food-experience';
        case 'food/create':
            return '/food-experience/create';
        case 'food/edit':
            $id = $take('id');
            return $id ? "/food-experience/{$id}/edit" : '/food-experience';
        case 'admin/dashboard':
            return '/admin/dashboard';
        case 'admin/restaurants':
            return '/admin/restaurants';
        case 'admin/restaurant/form':
            $id = $take('id');
            return $id ? "/admin/restaurants/{$id}/edit" : '/admin/restaurants/create';
        case 'admin/restaurant/save':
            return '/admin/restaurants/save';
        case 'admin/restaurant/delete':
            return '/admin/restaurants/delete';
        case 'admin/menu':
            return '/admin/menu-items';
        case 'admin/menu/form':
            $id = $take('id');
            return $id ? "/admin/menu-items/{$id}/edit" : '/admin/menu-items/create';
        case 'admin/menu/save':
            return '/admin/menu-items/save';
        case 'admin/menu/delete':
            return '/admin/menu-items/delete';
        case 'admin/members':
            return '/admin/members';
        case 'admin/moderation':
            return '/admin/moderation';
        case 'api/search':
            return '/api/search';
        case 'api/reviews/add':
            return '/api/reviews/add';
        case 'api/reviews/delete':
            $id = $take('id');
            return $id ? "/api/reviews/{$id}" : '/api/reviews/delete';
        case 'api/restaurant-reviews/add':
            return '/api/restaurant-reviews/add';
        case 'api/restaurant-reviews/delete':
            $id = $take('id');
            return $id ? "/api/restaurant-reviews/{$id}" : '/api/restaurant-reviews/delete';
        case 'api/food-comments/add':
            return '/api/food-exp/comments/add';
        case 'api/food-comments/delete':
            $id = $take('id');
            return $id ? "/api/food-exp/comments/{$id}" : '/api/food-exp/comments/delete';
        case 'api/food-posts/delete':
            $id = $take('id');
            return $id ? "/api/food-exp/posts/{$id}" : '/api/food-exp/posts/delete';
        case 'api/members/delete':
            $id = $take('id');
            return $id ? "/api/members/{$id}" : '/api/members/delete';
        default:
            return '/index.php';
    }
}

function url(string $route = 'home', array $params = []): string
{
    $query = $params;
    $path = route_path($route, $query);
    $target = base_url() . $path;

    if ($target === '') {
        $target = '/';
    }

    return $target . ($query ? '?' . http_build_query($query) : '');
}

function asset(string $path): string
{
    return base_url() . '/' . ltrim($path, '/');
}

function site_image(string $filename): string
{
    return asset('uploads/site/' . ltrim($filename, '/'));
}

function restaurant_image(array $restaurant): string
{
    $id = max(1, (int) ($restaurant['id'] ?? 1));
    $imageNumber = (($id - 1) % 4) + 1;

    return site_image("restaurant-{$imageNumber}.jpg");
}

function menu_item_image(array $item): string
{
    if (!empty($item['image_path'])) {
        return asset($item['image_path']);
    }

    $id = max(1, (int) ($item['id'] ?? 1));
    $imageNumber = (($id - 1) % 8) + 1;

    return site_image("menu-{$imageNumber}.jpg");
}

function food_story_image(array $post): string
{
    if (!empty($post['menu_item_id'])) {
        $imageNumber = ((((int) $post['menu_item_id']) - 1) % 8) + 1;
        return site_image("menu-{$imageNumber}.jpg");
    }

    if (!empty($post['restaurant_id'])) {
        $imageNumber = ((((int) $post['restaurant_id']) - 1) % 4) + 1;
        return site_image("restaurant-{$imageNumber}.jpg");
    }

    return site_image('story-card.jpg');
}

function redirect_to(string $route, array $params = []): void
{
    header('Location: ' . url($route, $params));
    exit;
}

function render(string $view, array $data = []): void
{
    extract($data, EXTR_SKIP);
    ob_start();
    require BASE_PATH . "/food_page/views/{$view}.php";
    $content = ob_get_clean();
    require BASE_PATH . '/food_page/views/layout/main.php';
}

function json_response(array $payload, int $status = 200): void
{
    if (array_key_exists('ok', $payload) && !array_key_exists('success', $payload)) {
        $payload['success'] = $payload['ok'];
    }
    if (array_key_exists('success', $payload) && !array_key_exists('ok', $payload)) {
        $payload['ok'] = $payload['success'];
    }

    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($payload, JSON_UNESCAPED_SLASHES);
    exit;
}

function flash(string $type, string $message): void
{
    $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
}

function flashes(): array
{
    $messages = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $messages;
}

function csrf_token(): string
{
    if (empty($_SESSION['_csrf'])) {
        $_SESSION['_csrf'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['_csrf'];
}

function csrf_valid(?string $token = null): bool
{
    $token = $token
        ?? $_POST['_csrf']
        ?? $_SERVER['HTTP_X_CSRF_TOKEN']
        ?? '';

    return is_string($token)
        && isset($_SESSION['_csrf'])
        && hash_equals($_SESSION['_csrf'], $token);
}

function require_csrf(): void
{
    if (!csrf_valid()) {
        json_response(['ok' => false, 'message' => 'Invalid security token. Refresh and try again.'], 419);
    }
}

function current_user_id(): ?int
{
    return isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;
}

function current_user_role(): ?string
{
    return $_SESSION['role'] ?? null;
}

function current_user_name(): ?string
{
    return $_SESSION['name'] ?? null;
}

function is_logged_in(): bool
{
    return current_user_id() !== null;
}

function is_admin(): bool
{
    return current_user_role() === 'admin';
}

function is_member(): bool
{
    return current_user_role() === 'member';
}

function require_login(): void
{
    if (!is_logged_in()) {
        flash('warning', 'Please log in first.');
        redirect_to('auth/login');
    }
}

function require_admin(): void
{
    if (!is_logged_in()) {
        flash('warning', 'Please log in as an admin first.');
        redirect_to('auth/login');
    }

    if (!is_admin()) {
        flash('danger', 'Admin access is required.');
        redirect_to('home');
    }
}

function require_member(): void
{
    if (!is_logged_in()) {
        json_response(['ok' => false, 'message' => 'Please log in as a member first.'], 401);
    }

    if (!is_member()) {
        json_response(['ok' => false, 'message' => 'Only members can perform this action.'], 403);
    }
}

function login_user(array $user): void
{
    session_regenerate_id(true);
    $_SESSION['user_id'] = (int) $user['id'];
    $_SESSION['name'] = $user['name'];
    $_SESSION['role'] = $user['role'];
}

function clear_remember_cookie(): void
{
    setcookie(REMEMBER_COOKIE, '', [
        'expires' => time() - 3600,
        'path' => '/',
        'httponly' => true,
        'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'samesite' => 'Lax',
    ]);
}

function set_remember_cookie(string $token): void
{
    setcookie(REMEMBER_COOKIE, $token, [
        'expires' => time() + 60 * 60 * 24 * 30,
        'path' => '/',
        'httponly' => true,
        'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'samesite' => 'Lax',
    ]);
}

function attempt_remember_login(): void
{
    if (is_logged_in() || empty($_COOKIE[REMEMBER_COOKIE])) {
        return;
    }

    $hash = hash('sha256', (string) $_COOKIE[REMEMBER_COOKIE]);
    $user = User::findByRememberToken($hash);
    if ($user) {
        login_user($user);
    } else {
        clear_remember_cookie();
    }
}

function uploaded_image(string $field, string $folder, array &$errors, bool $required = false): ?string
{
    if (empty($_FILES[$field]) || $_FILES[$field]['error'] === UPLOAD_ERR_NO_FILE) {
        if ($required) {
            $errors[$field] = 'Please upload an image.';
        }
        return null;
    }

    $file = $_FILES[$field];
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errors[$field] = 'The upload failed. Please try another image.';
        return null;
    }

    if ($file['size'] > MAX_UPLOAD_SIZE) {
        $errors[$field] = 'Images must be 2MB or smaller.';
        return null;
    }

    $mime = '';
    if (function_exists('finfo_open')) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = $finfo ? (string) finfo_file($finfo, $file['tmp_name']) : '';
        if ($finfo) {
            finfo_close($finfo);
        }
    } elseif (function_exists('mime_content_type')) {
        $mime = (string) mime_content_type($file['tmp_name']);
    }

    $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png'];
    if (!isset($allowed[$mime])) {
        $errors[$field] = 'Only JPEG and PNG images are allowed.';
        return null;
    }

    $folder = trim($folder, '/');
    $targetDir = UPLOAD_PATH . ($folder ? "/{$folder}" : '');
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0775, true);
    }

    $filename = bin2hex(random_bytes(16)) . '.' . $allowed[$mime];
    $target = "{$targetDir}/{$filename}";
    if (!move_uploaded_file($file['tmp_name'], $target)) {
        $errors[$field] = 'Could not save the uploaded image.';
        return null;
    }

    return 'uploads/' . ($folder ? "{$folder}/" : '') . $filename;
}

function post_value(string $key, string $default = ''): string
{
    return trim((string) ($_POST[$key] ?? $default));
}

function excerpt(?string $value, int $limit = 160): string
{
    $value = trim((string) $value);
    if (strlen($value) <= $limit) {
        return $value;
    }

    return rtrim(substr($value, 0, $limit - 3)) . '...';
}

function request_method(): string
{
    return strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
}

function can_edit_owner(int $ownerId): bool
{
    return is_admin() || current_user_id() === $ownerId;
}
