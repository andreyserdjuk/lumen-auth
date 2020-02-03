<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use LumenAuth\Core\Authenticator;

class AuthenticationController extends Controller
{
    /**
     * @var Authenticator
     */
    private $authenticator;

    public function __construct(Authenticator $authenticator)
    {
        $this->authenticator = $authenticator;
    }

    public function auth(Request $request)
    {
        $this->validate($request, [
            'email'    => [
                'required',
                'email',
            ],
            'password' => ['required'],
        ]);
        $email = $request->input('email');
        $password = $request->input('password');
        $ttl = env('APP_SESSION_TTL');
        $key = env('SES_SECRET');

        try {
            $token = $this->authenticator->authenticate($email, $password, $key, $ttl);
        } catch (Exception $e) {
            return new JsonResponse(['messages' => [$e->getMessage()]], 422);
        }

        return new JsonResponse($token);
    }

    public function verify(string $token)
    {
        $key = env('SES_SECRET');
        try {
            $this->authenticator->getPayloadFromToken(urldecode($token), $key);
        } catch (Exception $e) {
            return new JsonResponse(['messages' => [$e->getMessage()]], 401);
        }

        return new JsonResponse(['messages' => ['Token is valid.']], 200);
    }
}
