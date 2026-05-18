<?php
declare(strict_types=1);

final class HomeController extends BaseController
{
    public function index(): void
    {
        $this->render('home', [
            'pageTitle' => 'Home',
            'restaurants' => array_slice(Restaurant::all(), 0, 6),
            'posts' => array_slice(FoodExperience::posts(), 0, 3),
            'locations' => Restaurant::locations(),
            'areas' => Restaurant::areas(),
        ]);
    }
}

