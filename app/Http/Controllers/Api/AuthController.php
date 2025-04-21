<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Resources\UsersResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
    
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $resource = new UsersResource($user);
            $token = $user->createToken('MyApp')->plainTextToken;
    
            $data = [
                'resource' => $resource,
                'token' => $token,
            ];
    
            return response()->api($data, 0, 'Login successful');
        } else {
            return response()->api(null, 1, 'Unauthorized');
        }
    }
    

    public function register(Request $request)
{
    // Step 1: Validate request
    $validator = Validator::make($request->all(), [
        'username' => 'required|string|max:255',
        'firstname'=> 'required|string|max:255',
        'lastname' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8|confirmed',
    ]);

    if ($validator->fails()) {
        return response()->api(null, 1, $validator->errors()->first());
    }

    // Step 2: Create the user (password will be hashed via cast)
    $user = User::create([
        'username' => $request->username,
        'firstname' => $request->firstname,
        'lastname' => $request->lastname,
        'email' => $request->email,
        'password' => $request->password,
    ]);

    // Step 3: Prepare the response
    $data = [
        'resource' => new UsersResource($user),
        'token' => $user->createToken('MyApp')->plainTextToken,
    ];

    return response()->api($data, 0, 'User registered successfully');
}

}
