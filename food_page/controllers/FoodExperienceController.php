<?php
declare(strict_types=1);

final class FoodExperienceController extends BaseController
{
    public function index(): void
    {
        $posts = FoodExperience::posts();
        foreach ($posts as &$post) {
            $post['comments'] = FoodExperience::commentsByPost((int) $post['id']);
        }
        unset($post);

        $this->render('food_experience/index', [
            'pageTitle' => 'Food Experience',
            'posts' => $posts,
        ]);
    }

    public function form(array $errors = [], ?array $post = null): void
    {
        require_login();
        if (!$post && !empty($_GET['id'])) {
            $post = FoodExperience::findPost((int) $_GET['id']);
            if (!$post || !can_edit_owner((int) $post['user_id'])) {
                flash('danger', 'You cannot edit that post.');
                redirect_to('food');
            }
        }

        $this->render('food_experience/form', [
            'pageTitle' => $post ? 'Edit Food Experience' : 'Share Food Experience',
            'post' => $post,
            'errors' => $errors,
            'restaurants' => Restaurant::all(),
            'items' => MenuItem::search([]),
        ]);
    }

    public function save(): void
    {
        require_login();
        if (!$this->validateCsrfForPage()) {
            redirect_to('food');
        }

        $id = (int) ($_POST['id'] ?? 0);
        $data = [
            'user_id' => current_user_id(),
            'title' => post_value('title'),
            'content' => post_value('content'),
            'post_type' => post_value('post_type', 'food'),
            'restaurant_id' => (int) ($_POST['restaurant_id'] ?? 0),
            'menu_item_id' => (int) ($_POST['menu_item_id'] ?? 0),
        ];
        $errors = $this->postErrors($data);

        if ($id) {
            $existing = FoodExperience::findPost($id);
            if (!$existing || !can_edit_owner((int) $existing['user_id'])) {
                flash('danger', 'You cannot edit that post.');
                redirect_to('food');
            }
        }

        if ($errors) {
            $this->form($errors, array_merge(['id' => $id], $data));
            return;
        }

        if ($id) {
            FoodExperience::updatePost($id, $data);
            flash('success', 'Food experience updated.');
        } else {
            FoodExperience::createPost($data);
            flash('success', 'Food experience posted.');
        }

        redirect_to('food');
    }

    private function postErrors(array $data): array
    {
        $errors = [];
        if ($data['title'] === '') {
            $errors['title'] = 'Title is required.';
        }
        if ($data['content'] === '') {
            $errors['content'] = 'Content is required.';
        }
        if (!in_array($data['post_type'], ['restaurant', 'food', 'both'], true)) {
            $errors['post_type'] = 'Choose a valid post type.';
        }
        if ($data['restaurant_id'] && !Restaurant::find($data['restaurant_id'])) {
            $errors['restaurant_id'] = 'Choose a valid restaurant.';
        }
        if ($data['menu_item_id'] && !MenuItem::find($data['menu_item_id'])) {
            $errors['menu_item_id'] = 'Choose a valid menu item.';
        }
        return $errors;
    }
}
