<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserActivity;
use App\Models\UserDetail;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Mail;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AuthController extends Controller
{
    protected $registrationChannel = "WEB";
    /**
     * Register the new user.
     *
     * @param array $request
     * @return json array response
     */
    public function register(Request $request)
    {

        $validator = Validator::make($request->input(), [
            'group_id' => 'required',
            'employee_name' => 'required',
            'employee_id' => 'required',
            'employee_user_id' => 'required',
            'nid_card_no' => 'required',
            'birth_date' => 'required|date',
            'mobile_no' => $this->phoneValidationRules() . "|unique:users",
            'address' => 'required',
            'gender' => 'required',
            'status' => 'nullable',
            'avatar' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|min:8|max:25',
        ], $this->phoneValidationErrorMessages());

        if ($validator->fails()) {

            $message = "";
            foreach ($validator->errors()->getMessages() ?? [] as $key => $value) {
                $message .= ' ' . $value[0];
            }

            $error = [
                'title' => 'Validation Error',
                'message' => $message,
            ];

            return $this->sendError($error);
        }

        try {

            $validatedData = $request->input();
            $name = $validatedData['employee_name'];
            $authUserId = Auth::id();

            $user = User::create([
                'name' => $name,
                'parent_id' => $authUserId,
                'group_id' => $validatedData['group_id'],
                'mobile_no' => $validatedData['mobile_no'],
                'email' => $validatedData['email'],
                'avatar' => $this->saveAndGetAvatar($validatedData['avatar']),
                'password' => Hash::make($validatedData['password']),
                'created_by' => $authUserId,
                'updated_by' => $authUserId,
            ]);

            $userDetail = UserDetail::create([
                'user_id' => $user->id,
                'employee_id' => $validatedData['employee_id'],
                'employee_name' => $name,
                'employee_user_id' => $validatedData['employee_user_id'],
                'nid_card_no' => $validatedData['nid_card_no'],
                'registered_channel' => $this->registrationChannel,
                'gender' => $validatedData['gender'],
                'birth_date' => date('Y-m-d', strtotime($validatedData['birth_date'])),
                'address' => $validatedData['address'],
                'last_update_date' => Carbon::now(),
            ]);

            $userActivity = UserActivity::create([
                'user_id' => $user->id,
                'login_device_name' => $request->header('User-Agent'),
                'browser' => $request->header('User-Agent'),
                // 'last_online' => now(),
                // 'last_login' => now(),
                // 'last_logout' => now(),
                'creator_ip' => getIPAddress(),
                'creator_device' => $request->header('User-Agent'),
                'last_update_date' => Carbon::now(),
            ]);

        } catch (\Exception $e) {
            Log::error('USER-REGISTRATION-ERROR: ' . $e->getMessage());

            return response()->json([
                'title' => 'Failed to registered.',
                'message' => 'User failed to registered.',
            ], Response::HTTP_PRECONDITION_FAILED);

        }

        // $user->assignRole($request->input('roles'));

        return response()->json([
            'title' => 'Successfully registered.',
            'message' => 'User Successfully registered as a Pending.',
        ], Response::HTTP_OK);

    }

    protected function saveAndGetAvatar($avatar)
    {
        if (!isset($avatar) && !$avatar) {
            return null;
        }

        $imageParts = explode(";base64,", $avatar);
        $imageTypeAux = explode("image/", $imageParts[0]);
        $extension = $imageTypeAux[1];
        $imageBase64 = base64_decode($imageParts[1]);
        $fileNameToStore = uniqid() . '.' . $extension;
        $file = public_path('uploads/') . "images/profile/" . $fileNameToStore;
        file_put_contents($file, $imageBase64);
        return "images/profile/" . $fileNameToStore;
    }

    /**
     * login the user.
     *
     * @param array $request
     * @return json array response with token
     */
    public function login(Request $request)
    {
        if (!\Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'title' => 'Invalid login details',
                'message' => 'Invalid login details',
            ], 401);
        }

        $user = User::where('email', $request['email'])->firstOrFail();

        $token = $user->createToken('authToken')->plainTextToken;

        return response()->json([
            'title' => 'Successfully loggedIn.',
            'message' => 'Successfully loggedIn.',
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    /**
     * forgetpassword
     *
     * @param array $request
     * @return json array response
     */
    public function forgetpassword(Request $request)
    {

        $post_data = $request->validate([
            'email' => 'required|string|email|exists:users',
        ]);

        $token = Str::random(64);

        DB::table('password_resets')->insert([
            'email' => $request->email,
            'token' => $token,
            'created_at' => Carbon::now(),
        ]);

        $logo = env('APP_NAME');

        Mail::send('email.forgetPassword', ['token' => $token, 'logo' => $logo], function ($message) use ($request) {
            $message->to($request->email);
            $message->subject('Reset Password');
        });

        return response()->json([
            'title' => 'Successfully requested.',
            'message' => 'Password reset link has been sent to your email.',
        ], 200);
    }

    /**
     * resetpassword
     *
     * @param array $request
     * @return json array response
     */
    public function resetpassword(Request $request)
    {
        $this->validate($request, [
            'password' => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required',
        ]);

        $updatePassword = DB::table('password_resets')->where('token', $request->token)->first();

        if (!$updatePassword) {
            return response()->json([
                'message' => 'Invalid Token',
            ], 401);
        }

        $uemail = $updatePassword->email;
        $user = User::where('email', $uemail)->update(['password' => Hash::make($request->password)]);
        DB::table('password_resets')->where(['email' => $uemail])->delete();

        return response()->json([
            'title' => 'Password changed.',
            'message' => 'Password has been changed.',
        ]);
    }

    /**
     * logout
     *
     * @param array $request
     * @return json array response
     */
    public function logout(Request $request)
    {
        $userId = $request->id;
        $updateData['last_logout'] = $updateData['last_update_date'] = Carbon::now();
        $userActivity = UserActivity::findOrFail($userId);
        $userActivity->update($updateData);

        // deleting the existing tokens of authenticated user.
        Auth::user()->tokens->each(function ($token, $key) {
            $token->delete();
        });
        return response()->json('Successfully logged out');
    }

    /**
     * LoggedIn Users details with roles and Permissions
     *
     * @param array $request
     * @return json array response
     */
    public function user(Request $request)
    {
        $user = User::find(Auth::user()->id);
        $user['roles'] = $user->getAllPermissions();
        Log::info('USER: ' . json_encode($user));
        $permissions = [];
        foreach ($user['roles'] as $roles) {
            $permissions[] = $roles['name'];
        }
        $user['cando'] = $permissions;
        Log::info('PERMISSIONS: ' . json_encode($permissions) . ' USER: ' . json_encode($user));

        return response()->json($user);
    }

}
