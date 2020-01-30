<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use LumenAuth\Core\RegistrationProcessor;

class RegistrationController extends Controller
{
    /**
     * @var RegistrationProcessor
     */
    private $registrationProcessor;

    public function __construct(RegistrationProcessor $registrationProcessor)
    {
        $this->registrationProcessor = $registrationProcessor;
    }

    /**
     * Accept registration request.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function accept(Request $request)
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

        try {
            $this->registrationProcessor->accept($email, $password);
        } catch (Exception $e) {
            return new JsonResponse(['messages' => [$e->getMessage()]], 422);
        }

        return new JsonResponse(['messages' => ['Accepted.']]);
    }

    /**
     * Activate (confirm) registration request by id. Create account.
     *
     * @param string $id
     * @return JsonResponse
     */
    public function activate(string $id)
    {
        try {
            $this->registrationProcessor->activate($id);
        } catch (Exception $e) {
            return new JsonResponse(['messages' => [$e->getMessage()]], 422);
        }

        return new JsonResponse(['messages' => ['Activated.']]);
    }
}
