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

        try {
            $token = $this->authenticator->authenticate($email, $password, $ttl);
        } catch (Exception $e) {
            return new JsonResponse(['messages' => [$e->getMessage()]], 422);
        }

        return new JsonResponse($token);
    }

    public function verify(string $token)
    {
        try {
            $isValid = $this->authenticator->verify(urldecode($token));
        } catch (Exception $e) {
            return new JsonResponse(['messages' => [$e->getMessage()]], 422);
        }

        $statusCode = $isValid ? 200 : 401;
        $messages = $isValid ? ['messages' => ['Token is valid.']] : ['messages' => ['Token is invalid.']];

        return new JsonResponse($messages, $statusCode);
    }
}
