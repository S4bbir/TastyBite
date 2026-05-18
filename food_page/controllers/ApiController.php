<?php
declare(strict_types=1);

final class ApiController extends BaseController
{
    public function search(): void
    {
        $filters = [
            'q' => trim((string) ($_GET['q'] ?? '')),
            'location' => trim((string) ($_GET['location'] ?? '')),
            'area' => trim((string) ($_GET['area'] ?? '')),
            'min_price' => trim((string) ($_GET['min_price'] ?? '')),
            'max_price' => trim((string) ($_GET['max_price'] ?? '')),
        ];

        foreach (['min_price', 'max_price'] as $field) {
            if ($filters[$field] !== '' && !is_numeric($filters[$field])) {
                json_response(['ok' => false, 'message' => 'Price filters must be numeric.'], 422);
            }
        }

        if (
            $filters['min_price'] !== ''
            && $filters['max_price'] !== ''
            && (float) $filters['min_price'] > (float) $filters['max_price']
        ) {
            json_response(['ok' => false, 'message' => 'Minimum price cannot exceed maximum price.'], 422);
        }

        json_response([
            'ok' => true,
            'restaurants' => Restaurant::search($filters),
            'items' => MenuItem::search($filters),
        ]);
    }

    public function addReview(): void
    {
        require_csrf();
        require_member();

        $menuItemId = (int) ($_POST['menu_item_id'] ?? 0);
        $comment = trim((string) ($_POST['comment'] ?? ''));
        if (!MenuItem::find($menuItemId)) {
            json_response(['ok' => false, 'message' => 'Menu item not found.'], 404);
        }
        if ($comment === '' || strlen($comment) > 1000) {
            json_response(['ok' => false, 'message' => 'Write a review under 1000 characters.'], 422);
        }

        $id = Review::create($menuItemId, current_user_id(), $comment);
        json_response([
            'ok' => true,
            'message' => 'Review posted.',
            'review' => [
                'id' => $id,
                'author' => current_user_name(),
                'comment' => $comment,
                'created_at' => date('Y-m-d H:i:s'),
                'can_delete' => true,
            ],
        ]);
    }

    public function deleteReview(): void
    {
        require_csrf();
        $this->requireLoggedJson();

        $review = Review::find((int) ($_GET['id'] ?? $_POST['id'] ?? 0));
        if (!$review) {
            json_response(['ok' => false, 'message' => 'Review not found.'], 404);
        }
        if (!can_edit_owner((int) $review['user_id'])) {
            json_response(['ok' => false, 'message' => 'You can delete only your own reviews.'], 403);
        }

        Review::delete((int) $review['id']);
        json_response(['ok' => true, 'message' => 'Review deleted.']);
    }

    public function addRestaurantReview(): void
    {
        require_csrf();
        require_member();

        $restaurantId = (int) ($_POST['restaurant_id'] ?? 0);
        $rating = (int) ($_POST['rating'] ?? 0);
        $comment = trim((string) ($_POST['comment'] ?? ''));
        if (!Restaurant::find($restaurantId)) {
            json_response(['ok' => false, 'message' => 'Restaurant not found.'], 404);
        }
        if ($rating < 1 || $rating > 5 || $comment === '' || strlen($comment) > 1000) {
            json_response(['ok' => false, 'message' => 'Choose a rating and write a short review.'], 422);
        }

        RestaurantReview::create($restaurantId, current_user_id(), $rating, $comment);
        json_response(['ok' => true, 'message' => 'Restaurant review posted.']);
    }

    public function deleteRestaurantReview(): void
    {
        require_csrf();
        $this->requireLoggedJson();

        $review = RestaurantReview::find((int) ($_GET['id'] ?? $_POST['id'] ?? 0));
        if (!$review) {
            json_response(['ok' => false, 'message' => 'Restaurant review not found.'], 404);
        }
        if (!can_edit_owner((int) $review['user_id'])) {
            json_response(['ok' => false, 'message' => 'You can delete only your own restaurant reviews.'], 403);
        }

        RestaurantReview::delete((int) $review['id']);
        json_response(['ok' => true, 'message' => 'Restaurant review deleted.']);
    }

    public function addFoodComment(): void
    {
        require_csrf();
        $this->requireLoggedJson();

        $postId = (int) ($_POST['post_id'] ?? 0);
        $comment = trim((string) ($_POST['comment'] ?? ''));
        if (!FoodExperience::findPost($postId)) {
            json_response(['ok' => false, 'message' => 'Post not found.'], 404);
        }
        if ($comment === '' || strlen($comment) > 1000) {
            json_response(['ok' => false, 'message' => 'Write a comment under 1000 characters.'], 422);
        }

        FoodExperience::createComment($postId, current_user_id(), $comment);
        json_response(['ok' => true, 'message' => 'Comment posted.']);
    }

    public function deleteFoodComment(): void
    {
        require_csrf();
        $this->requireLoggedJson();

        $comment = FoodExperience::findComment((int) ($_GET['id'] ?? $_POST['id'] ?? 0));
        if (!$comment) {
            json_response(['ok' => false, 'message' => 'Comment not found.'], 404);
        }
        if (!can_edit_owner((int) $comment['user_id'])) {
            json_response(['ok' => false, 'message' => 'You can delete only your own comments.'], 403);
        }

        FoodExperience::deleteComment((int) $comment['id']);
        json_response(['ok' => true, 'message' => 'Comment deleted.']);
    }

    public function deleteFoodPost(): void
    {
        require_csrf();
        $this->requireLoggedJson();

        $post = FoodExperience::findPost((int) ($_GET['id'] ?? $_POST['id'] ?? 0));
        if (!$post) {
            json_response(['ok' => false, 'message' => 'Post not found.'], 404);
        }
        if (!can_edit_owner((int) $post['user_id'])) {
            json_response(['ok' => false, 'message' => 'You can delete only your own posts.'], 403);
        }

        FoodExperience::deletePost((int) $post['id']);
        json_response(['ok' => true, 'message' => 'Post deleted.']);
    }

    public function deleteMember(): void
    {
        require_csrf();
        if (!is_admin()) {
            json_response(['ok' => false, 'message' => 'Admin access is required.'], 403);
        }

        $deleted = User::deleteMember((int) ($_GET['id'] ?? $_POST['id'] ?? 0));
        json_response(['ok' => $deleted, 'message' => $deleted ? 'Member deleted.' : 'Member not found.']);
    }

    private function requireLoggedJson(): void
    {
        if (!is_logged_in()) {
            json_response(['ok' => false, 'message' => 'Please log in first.'], 401);
        }
    }
}
