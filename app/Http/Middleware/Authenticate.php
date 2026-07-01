<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        if ($request->expectsJson()) {
            return null;
        }

        if ($request->is('portal/*') || $request->is('portal')) {
            // O slug vem da rota atual (/portal/{tenantSlug}/...). Passamos explicitamente
            // porque este middleware roda antes do ResolveTenant (que define o URL default),
            // então route('portal.login') sozinho ficaria sem o tenantSlug.
            $slug = $request->route('tenantSlug');

            return $slug ? route('portal.login', ['tenantSlug' => $slug]) : route('landing');
        }

        return route('auth-login-basic');
    }
}
