<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Http\Request;

final class AuthController extends Controller
{
    public function me(Request $request)
    {
        $authContext = (array) $request->attribute('auth_context', []);

        return $this->success([
            'token_name' => $authContext['token_name'] ?? null,
            'abilities' => $authContext['abilities'] ?? [],
        ], 'Authenticated token loaded successfully.');
    }
}
