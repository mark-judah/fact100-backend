<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use  App\Models\User;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    /**
     * Store a new user.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        //validate incoming request
        $this->validate($request, [
            'name' => 'required|string',
            'email' => 'required|string|unique:users',
            'role' => 'required|string',
            'password' => 'required|confirmed|min:6',
        ]);

        try {
            $user = new User;
            $user->name = $request->input('name');
            $user->email = $request->input('email');
            $user->role = $request->input('role');
            $user->password = app('hash')->make($request->input('password'));
            $user->save();

            return response()->json([
                'table' => 'users',
                'action' => 'create',
                'result' => 'success'
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'table' => 'users',
                'action' => 'create',
                'result' => 'failed'
            ], 409);
        }
    }

    /**
     * Get a JWT via given credentials.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        //validate incoming request
        $this->validate($request, [
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('email', 'password');

        if ($token = auth::attempt($credentials)) {
            return $this->respondWithToken($token);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }


    /**
     * Get user details.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function currentUser()
    {
        if ($user = response()->json(auth()->user())) {
            return $user;
        }
        return response()->json(['error' => 'Unauthorized'], 401);

    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    public function getProfiles()
    {
        $profiles = User::orderBy('created_at', 'desc')->get();
        return json_encode($profiles);
    }

    public function updateProfile(Request $request)
    {
        \Tinify\setKey("1VzK915ZyBgpW4YSZwKtvccYLZ6F1sMp");
        //todo compreess thumbnail with tinyPNG api before upload
        $validator = Validator::make($request->all(), [
            'data.*.logged_in_userId' => 'string|required',
            'data.*.profile_id' => 'string|required',
            'data.*.name' => 'string|required',
            'data.*.email' => 'string|required|unique:podcast',
            'data.*.role' => 'string|required',
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp',
        ]);
        if ($validator->fails()) {
            $error = $validator->errors()->first();
            return response()->json([
                'action' => 'validate',
                'result' => 'failed',
                'errors'=>$error
            ], 403);
        }

        $avatar_file = $request->file('avatar');
        // Define upload path
        $destinationPath = public_path('uploads/avatars'); // upload path
        // Upload Orginal Image
        $user_avatar = date('YmdHis') . "." . $avatar_file->getClientOriginalExtension();
        $avatar_file->move($destinationPath, $user_avatar);
        $source=\Tinify\fromFile("$destinationPath/".$user_avatar);
        $source->toFile("$destinationPath/".$user_avatar);
        $insert['image'] = "$user_avatar";


        $data = json_decode($request->data,true);

        $user = User::where('id',$data['profile_id']) -> first();
        if ($user) {
            if ($data['logged_in_userId']==$data['profile_id']){
                $user->where("id", $data['profile_id'])->update([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'role' =>  $data['role'],
                    'avatar' => "$user_avatar",
                ]);
                return response()->json([
                    'table' => 'users',
                    'action' => 'update',
                    'result' => 'user details updated successfully'
                ], 200);
            }else{
                return response()->json([
                    'table' => 'users',
                    'action' => 'update',
                    'result' => 'action denied, only the owner of the account can update details'
                ], 200);
            }
       }

    }

}
