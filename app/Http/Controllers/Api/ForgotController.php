<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ForgotRequest;
use App\Http\Requests\ResetRequest;
use App\Models\User;
use App\Models\UserActivity;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ForgotController extends Controller
{
    public function forgot(ForgotRequest $request)
    {
        $email = trim($request->input('email'));

        // Check if the user exists and is active
        $user = User::where('email', $email)->first();
        if (!$user) {
            return response()->json([
                'message' => 'The user doesn\'t exist.',
            ], Response::HTTP_NOT_FOUND);
        }

        if ($user->status !== 'Active') {
            return response()->json([
                'message' => 'Your account status is Pending. Please contact your system administrator.',
            ], Response::HTTP_NOT_FOUND);
        }

        $token = Str::random(60); // Use a longer token for better security

        try {
            // Use upsert to avoid duplicate tokens for the same email
            DB::table('password_resets')->updateOrInsert(
                ['email' => $email],
                [
                    'token' => $token,
                    'created_at' => Carbon::now(),
                ]
            );

            // Send email
            Mail::send('Mails.forgot', ['token' => $token], function (Message $message) use ($email) {
                $message->to($email)
                    ->subject('Reset your password');
            });

            return response()->json([
                'message' => 'Please check your email inbox!',
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Internal error, please try again later.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function reset(ResetRequest $request)
    {
        $token = trim($request->input('token'));

        $passwordReset = DB::table('password_resets')->where('token', $token)->first();

        if (!$passwordReset) {
            return response([
                'message' => 'Invalid token!',
            ], 400);
        }

        $user = User::where('email', $passwordReset->email)->first();

        if (!$user) {
            return response([
                'message' => 'User doesn\'t exist!',
            ], 404);
        }

        $user->password = Hash::make(trim($request->input('password')));
        $user->password_changed_at = Carbon::now();
        $user->save();

        $userActivity = UserActivity::where('user_id', $user->id)->first();

        if ($userActivity) {
            $userActivity->update([
                'last_password_change_time' => Carbon::now(),
                'last_update_date' => Carbon::now(),
            ]);
        }

        return response([
            'message' => 'Success',
        ]);
    }

}
