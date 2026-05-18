<?php
declare(strict_types=1);

final class AuthController extends BaseController
{
    public function registerForm(array $errors = [], array $old = []): void
    {
        $this->render('auth/register', [
            'pageTitle' => 'Create Account',
            'errors' => $errors,
            'old' => $old,
        ]);
    }

    public function register(): void
    {
        if (!$this->validateCsrfForPage()) {
            $this->registerForm([], $_POST);
            return;
        }

        $data = [
            'name' => post_value('name'),
            'email' => strtolower(post_value('email')),
            'role' => 'member',
            'password' => (string) ($_POST['password'] ?? ''),
            'password_confirm' => (string) ($_POST['password_confirm'] ?? ''),
        ];

        $errors = $this->validateRegistration($data);
        if ($errors) {
            $this->registerForm($errors, $data);
            return;
        }

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'],
            'password_hash' => password_hash($data['password'], PASSWORD_DEFAULT),
        ]);

        flash('success', 'Account created. You can log in now.');
        redirect_to('auth/login');
    }

    public function loginForm(array $errors = [], array $old = []): void
    {
        $this->render('auth/login', [
            'pageTitle' => 'Login',
            'errors' => $errors,
            'old' => $old,
        ]);
    }

    public function login(): void
    {
        if (!$this->validateCsrfForPage()) {
            $this->loginForm([], $_POST);
            return;
        }

        $email = strtolower(post_value('email'));
        $password = (string) ($_POST['password'] ?? '');
        $user = User::findByEmail($email);

        if (!$user || !password_verify($password, $user['password_hash'])) {
            $this->loginForm(['email' => 'Invalid email or password.'], ['email' => $email]);
            return;
        }

        login_user($user);

        if (!empty($_POST['remember_me'])) {
            $token = bin2hex(random_bytes(32));
            User::setRememberToken((int) $user['id'], hash('sha256', $token));
            set_remember_cookie($token);
        }

        flash('success', 'Welcome back, ' . $user['name'] . '.');
        redirect_to($user['role'] === 'admin' ? 'admin/dashboard' : 'home');
    }

    public function logout(): void
    {
        if (request_method() === 'POST' && csrf_valid()) {
            if (current_user_id()) {
                User::setRememberToken(current_user_id(), null);
            }
            clear_remember_cookie();
            $_SESSION = [];
            session_destroy();
        }

        redirect_to('home');
    }

    public function profileForm(array $errors = []): void
    {
        require_login();
        $this->render('auth/profile', [
            'pageTitle' => 'Profile',
            'user' => User::find(current_user_id()),
            'errors' => $errors,
        ]);
    }

    public function profileUpdate(): void
    {
        require_login();
        if (!$this->validateCsrfForPage()) {
            $this->profileForm();
            return;
        }

        $user = User::find(current_user_id());
        if (!$user) {
            redirect_to('auth/logout');
        }

        $data = [
            'name' => post_value('name'),
            'email' => strtolower(post_value('email')),
        ];

        $errors = [];
        if ($data['name'] === '') {
            $errors['name'] = 'Name is required.';
        }
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Enter a valid email address.';
        } elseif (User::emailExists($data['email'], current_user_id())) {
            $errors['email'] = 'That email is already registered.';
        }

        $profilePicture = uploaded_image('profile_picture', 'profiles', $errors, false);

        $currentPassword = (string) ($_POST['current_password'] ?? '');
        $newPassword = (string) ($_POST['new_password'] ?? '');
        $confirm = (string) ($_POST['new_password_confirm'] ?? '');
        $passwordRequested = $currentPassword !== '' || $newPassword !== '' || $confirm !== '';

        if ($passwordRequested) {
            if (!password_verify($currentPassword, $user['password_hash'])) {
                $errors['current_password'] = 'Current password is incorrect.';
            }
            if (strlen($newPassword) < 8) {
                $errors['new_password'] = 'New password must be at least 8 characters.';
            }
            if ($newPassword !== $confirm) {
                $errors['new_password_confirm'] = 'Passwords do not match.';
            }
        }

        if ($errors) {
            $this->profileForm($errors);
            return;
        }

        User::updateProfile(current_user_id(), [
            'name' => $data['name'],
            'email' => $data['email'],
            'profile_picture' => $profilePicture,
        ]);

        if ($passwordRequested) {
            User::updatePassword(current_user_id(), password_hash($newPassword, PASSWORD_DEFAULT));
        }

        $_SESSION['name'] = $data['name'];
        flash('success', 'Profile updated successfully.');
        redirect_to('auth/profile');
    }

    private function validateRegistration(array $data): array
    {
        $errors = [];
        if ($data['name'] === '') {
            $errors['name'] = 'Name is required.';
        }
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Enter a valid email address.';
        } elseif (User::emailExists($data['email'])) {
            $errors['email'] = 'That email is already registered.';
        }
        if (!in_array($data['role'], ['admin', 'member'], true)) {
            $errors['role'] = 'Choose admin or member.';
        }
        if (strlen($data['password']) < 8) {
            $errors['password'] = 'Password must be at least 8 characters.';
        }
        if ($data['password'] !== $data['password_confirm']) {
            $errors['password_confirm'] = 'Passwords do not match.';
        }

        return $errors;
    }
}
