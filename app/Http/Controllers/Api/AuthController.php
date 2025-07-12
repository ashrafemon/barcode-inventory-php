<?php
namespace App\Http\Controllers\Api;

use App\Enums\ResponseTypes;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Services\HelperService;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function __construct(private HelperService $helper)
    {

    }

    public function login(LoginRequest $request)
    {
        try {
            $body = $request->validated();

            if (! $user = User::query()->where(['email' => $body['email']])->first()) {
                return $this->helper->messageException('User not found by this email');
            }

            if (! Hash::check($body['password'], $user->password)) {
                return $this->helper->validateError(['password' => 'The password is wrong'], true);
            }

            $token = auth()->guard('api')->claims(['id' => $user->id])->attempt($body);
            return $this->helper->entity(['token' => $token, 'type' => 'Bearer'], 200, ResponseTypes::SUCCESS, "You've logged into your account successfully.");
        } catch (Exception $e) {
            return $this->helper->serverException($e);
        }
    }

    public function register(RegisterRequest $request)
    {
        try {
            $body = $request->validated();

            if (User::query()->where(['email' => $body['email']])->exists()) {
                return $this->helper->validateError(['email' => 'This email is already in used'], true);
            }

            User::query()->create($request->validated());
            return $this->helper->entity(null, 200, ResponseTypes::SUCCESS, 'User registration successful');
        } catch (Exception $e) {
            return $this->helper->serverException($e);
        }
    }

    public function me()
    {
        try {
            if (! $user = User::query()->where(['id' => auth()->id()])->first()) {
                return $this->helper->messageException('User information not found...');
            }
            return $this->helper->entity($user);
        } catch (Exception $e) {
            return $this->helper->serverException($e);
        }
    }
}
