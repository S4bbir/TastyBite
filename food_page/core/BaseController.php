<?php
declare(strict_types=1);

abstract class BaseController
{
    protected function render(string $view, array $data = []): void
    {
        render($view, $data);
    }

    protected function backTo(string $route, array $params = []): void
    {
        redirect_to($route, $params);
    }

    protected function validateCsrfForPage(): bool
    {
        if (!csrf_valid()) {
            flash('danger', 'Invalid security token. Refresh and try again.');
            return false;
        }

        return true;
    }
}

