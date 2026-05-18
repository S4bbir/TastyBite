<?php
declare(strict_types=1);

final class BrowseController extends BaseController
{
    public function restaurants(): void
    {
        $this->render('browse/restaurants', [
            'pageTitle' => 'Browse Restaurants',
            'restaurants' => Restaurant::all(),
            'locations' => Restaurant::locations(),
            'areas' => Restaurant::areas(),
        ]);
    }

    public function restaurant(): void
    {
        $restaurant = Restaurant::find((int) ($_GET['id'] ?? 0));
        if (!$restaurant) {
            flash('danger', 'Restaurant not found.');
            redirect_to('browse/restaurants');
        }

        $this->render('browse/restaurant', [
            'pageTitle' => $restaurant['name'],
            'restaurant' => $restaurant,
            'items' => MenuItem::byRestaurant((int) $restaurant['id']),
            'restaurantReviews' => RestaurantReview::byRestaurant((int) $restaurant['id']),
        ]);
    }

    public function item(): void
    {
        $item = MenuItem::find((int) ($_GET['id'] ?? 0));
        if (!$item) {
            flash('danger', 'Menu item not found.');
            redirect_to('browse/restaurants');
        }

        $this->render('browse/item', [
            'pageTitle' => $item['name'],
            'item' => $item,
            'reviews' => Review::byMenuItem((int) $item['id']),
        ]);
    }
}

