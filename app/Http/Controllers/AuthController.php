<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Exception;

class AuthController extends Controller
{
    public function loginPage()
    {
        return view('auth.login');
    }

    public function registerPage()
    {
        return view('auth.register');
    }


    public function customeRegister(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'email' => 'required|email|unique:users',
                'password' => 'required|min:6'
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $role = Role::where('name','user')->first();

            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role_id' => $role->id
            ]);

            return redirect()->route('login-page')->with('success','User Registered Successfully');

        } 
        catch (Exception $e) {

            return redirect()->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }


    public function customeLogin(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email'    => 'required|email|exists:users,email',
            'password' => 'required'
        ]);

        // Validation fail
        if ($validator->fails()) {

            if ($request->expectsJson()) {
                return response()->json([
                    'status'=>false,
                    'errors'=>$validator->errors()
                ],422);
            }

            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $credentials = $request->only('email','password');

        try {

            if (Auth::attempt($credentials)) {

                $user = Auth::user();

                if ($request->expectsJson()) {

                    $token = $user->createToken('api_token')->plainTextToken;

                    return response()->json([
                        'status'=>true,
                        'message'=>'Login successful',
                        'token'=>$token,
                        'user'=>$user
                    ]);
                }

                $request->session()->regenerate();


                // Web login
                return redirect()->route('dashboard')
                        ->with('success','Login successful');
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'status'=>false,
                    'message'=>'Invalid credentials'
                ],401);
            }

            return redirect()->back()
                ->with('error','Invalid email or password')
                ->withInput();

        } catch (\Exception $e) {

            if ($request->expectsJson()) {
                return response()->json([
                    'status'=>false,
                    'message'=>$e->getMessage()
                ],500);
            }

            return redirect()->back()
                ->with('error',$e->getMessage())
                ->withInput();
        }

    }

    public function logout(Request $request)
    {
        Auth::logout();                       
        $request->session()->invalidate();     
        $request->session()->regenerateToken(); 

        return redirect()->route('login-page')->with('success', 'Logged out successfully');
    }

    public function dashboard()
    {

        if(auth()->user()->role->name == 'admin'){

            $total = Project::count();

            $pending = Project::where('status','pending')->count();

            $approved = Project::where('status','approved')->count();

            $rejected = Project::where('status','rejected')->count();

        }else{

            $userId = auth()->id();

            $total = Project::where('user_id',$userId)->count();

            $pending = Project::where('user_id',$userId)
                        ->where('status','pending')
                        ->count();

            $approved = Project::where('user_id',$userId)
                        ->where('status','approved')
                        ->count();

            $rejected = Project::where('user_id',$userId)
                        ->where('status','rejected')
                        ->count();
        }

        // Percentage
        $pendingPercent = $total ? round(($pending/$total)*100) : 0;
        $approvedPercent = $total ? round(($approved/$total)*100) : 0;
        $rejectedPercent = $total ? round(($rejected/$total)*100) : 0;

        return view('dashboard',compact(
            'total',
            'pending',
            'approved',
            'rejected',
            'pendingPercent',
            'approvedPercent',
            'rejectedPercent'
        ));
    }

}
