<?php
declare(strict_types=1);

final class AdminController extends BaseController
{
    public function dashboard(): void
    {
        require_admin();
        $this->render('admin/dashboard', [
            'pageTitle' => 'Admin Dashboard',
            'stats' => [
                'restaurants' => Restaurant::count(),
                'menuItems' => MenuItem::count(),
                'reviews' => Review::count(),
                'posts' => FoodExperience::countPosts(),
                'members' => User::countByRole('member'),
            ],
        ]);
    }

    public function restaurants(): void
    {
        require_admin();
        $this->render('admin/restaurants', [
            'pageTitle' => 'Manage Restaurants',
            'restaurants' => Restaurant::all(),
        ]);
    }

    public function restaurantForm(array $errors = [], ?array $restaurant = null): void
    {
        require_admin();
        if (!$restaurant && !empty($_GET['id'])) {
            $restaurant = Restaurant::find((int) $_GET['id']);
        }

        $this->render('admin/restaurant_form', [
            'pageTitle' => $restaurant ? 'Edit Restaurant' : 'Add Restaurant',
            'restaurant' => $restaurant,
            'errors' => $errors,
        ]);
    }

    public function saveRestaurant(): void
    {
        require_admin();
        if (!$this->validateCsrfForPage()) {
            redirect_to('admin/restaurants');
        }

        $id = (int) ($_POST['id'] ?? 0);
        $data = [
            'name' => post_value('name'),
            'location' => post_value('location'),
            'area' => post_value('area'),
            'short_background' => post_value('short_background'),
            'goals' => post_value('goals'),
        ];
        $errors = $this->restaurantErrors($data);

        if ($errors) {
            $this->restaurantForm($errors, array_merge(['id' => $id], $data));
            return;
        }

        if ($id) {
            Restaurant::update($id, $data);
            flash('success', 'Restaurant updated.');
        } else {
            Restaurant::create($data);
            flash('success', 'Restaurant created.');
        }

        redirect_to('admin/restaurants');
    }

    public function deleteRestaurant(): void
    {
        require_admin();
        if ($this->validateCsrfForPage()) {
            Restaurant::delete((int) ($_POST['id'] ?? 0));
            flash('success', 'Restaurant and its menu items were deleted.');
        }
        redirect_to('admin/restaurants');
    }

    public function menuItems(): void
    {
        require_admin();
        $restaurantId = (int) ($_GET['restaurant_id'] ?? 0);
        $restaurant = null;
        $items = MenuItem::allWithRestaurant();

        if ($restaurantId > 0) {
            $restaurant = Restaurant::find($restaurantId);
            if (!$restaurant) {
                flash('danger', 'Restaurant not found.');
                redirect_to('admin/restaurants');
            }
            $items = MenuItem::byRestaurant((int) $restaurant['id']);
        }

        $this->render('admin/menu_items', [
            'pageTitle' => $restaurant ? 'Menu Items' : 'All Menu Items',
            'restaurant' => $restaurant,
            'items' => $items,
        ]);
    }

    public function menuForm(array $errors = [], ?array $item = null): void
    {
        require_admin();
        if (!$item && !empty($_GET['id'])) {
            $item = MenuItem::find((int) $_GET['id']);
        }

        $restaurant = Restaurant::find((int) ($_GET['restaurant_id'] ?? ($item['restaurant_id'] ?? 0)));
        if (!$restaurant) {
            flash('danger', 'Restaurant not found.');
            redirect_to('admin/restaurants');
        }

        $this->render('admin/menu_form', [
            'pageTitle' => $item ? 'Edit Menu Item' : 'Add Menu Item',
            'restaurant' => $restaurant,
            'item' => $item,
            'errors' => $errors,
        ]);
    }

    public function saveMenuItem(): void
    {
        require_admin();
        if (!$this->validateCsrfForPage()) {
            redirect_to('admin/restaurants');
        }

        $id = (int) ($_POST['id'] ?? 0);
        $restaurantId = (int) ($_POST['restaurant_id'] ?? 0);
        $data = [
            'restaurant_id' => $restaurantId,
            'name' => post_value('name'),
            'description' => post_value('description'),
            'price' => post_value('price'),
        ];
        $errors = $this->menuItemErrors($data);
        $image = uploaded_image('image', 'menu', $errors, false);

        if ($errors) {
            $this->menuForm($errors, array_merge(['id' => $id], $data));
            return;
        }

        $data['price'] = (float) $data['price'];
        $data['image_path'] = $image;

        if ($id) {
            MenuItem::update($id, $data);
            flash('success', 'Menu item updated.');
        } else {
            MenuItem::create($data);
            flash('success', 'Menu item created.');
        }

        redirect_to('admin/menu', ['restaurant_id' => $restaurantId]);
    }

    public function deleteMenuItem(): void
    {
        require_admin();
        $restaurantId = (int) ($_POST['restaurant_id'] ?? 0);
        if ($this->validateCsrfForPage()) {
            MenuItem::delete((int) ($_POST['id'] ?? 0));
            flash('success', 'Menu item deleted.');
        }
        redirect_to('admin/menu', ['restaurant_id' => $restaurantId]);
    }

    public function members(): void
    {
        require_admin();
        $this->render('admin/members', [
            'pageTitle' => 'Member Profiles',
            'members' => User::members(),
        ]);
    }

    public function moderation(): void
    {
        require_admin();
        $this->render('admin/moderation', [
            'pageTitle' => 'Moderation',
            'itemReviews' => Review::allWithContext(),
            'restaurantReviews' => RestaurantReview::allWithContext(),
            'posts' => FoodExperience::posts(),
            'comments' => FoodExperience::allCommentsWithContext(),
        ]);
    }

    private function restaurantErrors(array $data): array
    {
        $errors = [];
        foreach (['name', 'location', 'area', 'short_background', 'goals'] as $field) {
            if ($data[$field] === '') {
                $errors[$field] = 'This field is required.';
            }
        }
        return $errors;
    }

    private function menuItemErrors(array $data): array
    {
        $errors = [];
        foreach (['name', 'description'] as $field) {
            if ($data[$field] === '') {
                $errors[$field] = 'This field is required.';
            }
        }
        if (!Restaurant::find((int) $data['restaurant_id'])) {
            $errors['restaurant_id'] = 'Choose a valid restaurant.';
        }
        if (!is_numeric($data['price']) || (float) $data['price'] <= 0) {
            $errors['price'] = 'Price must be greater than zero.';
        }
        return $errors;
    }
}
