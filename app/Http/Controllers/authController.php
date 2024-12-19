<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use hisorange\BrowserDetect\Parser as Browser;
use App\Jobs\SendLoginDetailsEmail;
use Illuminate\Support\Facades\Hash;

class authController extends Controller
{
    public function login(Request $request)
    {
        try {
            $validated = $request->validate([
                'email' => 'required|string|email',
                'password' => 'required|string|min:6',
            ]);

            $credentials = ['email' => $validated['email'], 'password' => $validated['password']];

            if (Auth::attempt($credentials)) {
                /** @var \App\Models\User $user */
                $user = Auth::user();

                if (!$user->api_key) {
                    $user->api_key = Str::random(60);
                    $user->save();
                }
                $user->load('employee');
                $user->load('role');
                $userData = $user->toArray();

                $ipAddress = $request->ip();
                $client = new Client();

                $locationData = null;
                try {
                    $response = $client->get("http://ip-api.com/json/$ipAddress");
                    $locationData = json_decode($response->getBody(), true);
                } catch (\Exception $e) {
                }

                $device = Browser::deviceType();
                $platform = Browser::platformName();
                $browser = Browser::browserFamily();
                $loginTime = now()->toDateTimeString();

                $details = [
                    'user_name' => $user->user_name,
                    'ip_address' => $ipAddress,
                    'login_time' => $loginTime,
                    'device' => $device,
                    'platform' => $platform,
                    'browser' => $browser,
                    'locationData' => $locationData
                ];


                SendLoginDetailsEmail::dispatch($user, $details);
                

                return response()->json([
                    'status_code' => 200,
                    'message' => 'Login successful',
                    'success' => true,
                    'api_key' => $user->api_key,
                    'user' => $userData,
                ], 200);
            } else {
                return response()->json([
                    'status_code' => 401,
                    'message' => 'The email or password is incorrect. Please try again.',
                    'success' => false,
                ], 401);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status_code' => 422,
                'message' => $e->getMessage(),
                'success' => false,
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status_code' => 500,
                'message' => 'An unexpected error occurred',
                'success' => false,
            ], 500);
        }
    }



    public function sendResetLinkEmail(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email|exists:users,email',
            ]);

            $status = Password::sendResetLink($request->only('email'));

            if ($status === Password::RESET_LINK_SENT) {
                return response()->json([
                    'status_code' => 200,
                    'message' => 'Reset link has been sent to your email.',
                    'success' => true,
                ]);
            } else {
                return response()->json([
                    'status_code' => 500,
                    'message' => 'Failed to send reset link.',
                    'success' => false,
                ]);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
      
            return response()->json([
                'status_code' => 422,
                'message' => 'Invalid email address.',
                'success' => false,
            ],422);
        } catch (\Exception $e) {
       
            return response()->json([
                'status_code' => 500,
                'message' => 'An unexpected error occurred.',
                'success' => false,
            ],500);
        }
    }



    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|confirmed|min:6',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('password.success');
        } else {
            return back()->withErrors(['email' => [__($status)]]);
        }
    }






    public function showResetForm(Request $request, $token)
    {
        return view('auth.passwords.reset', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }
}
