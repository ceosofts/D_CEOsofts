<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except(['logout', 'authenticated']);
        
        // Add throttle middleware to prevent brute force attacks (5 attempts per minute)
        $this->middleware('throttle:5,1')->only('login');
    }

    /**
     * Show the application's login form.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        return view('auth.login', [
            'pageTitle' => 'เข้าสู่ระบบ',
            'description' => 'กรุณาเข้าสู่ระบบเพื่อใช้งาน CEOSOFTS'
        ]);
    }

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(Request $request)
    {
        $this->validateLogin($request);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if (method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
            
            Log::warning('Login locked out due to too many attempts', [
                'email' => $request->input($this->username()),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            return $this->sendLockoutResponse($request);
        }

        $credentials = $this->credentials($request);
        $remember = $request->filled('remember');

        if ($this->attemptLogin($request)) {
            $user = Auth::user();
            
            // Log successful login
            Log::info('User logged in successfully', [
                'id' => $user->id,
                'email' => $user->email,
                'name' => $user->name,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);
            
            // Update last login timestamp
            $user->last_login_at = Carbon::now();
            $user->last_login_ip = $request->ip();
            $user->save();
            
            if ($request->wantsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'เข้าสู่ระบบสำเร็จ',
                    'redirect' => $this->redirectPath(),
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email
                    ]
                ]);
            }

            return $this->sendLoginResponse($request);
        }

        // If the login attempt was unsuccessful, we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);
        
        // Log failed login attempt
        Log::warning('Failed login attempt', [
            'email' => $request->input($this->username()),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        if ($request->wantsJson()) {
            throw ValidationException::withMessages([
                $this->username() => [trans('auth.failed')],
            ])->status(422);
        }

        return $this->sendFailedLoginResponse($request);
    }

    /**
     * Validate the user login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validateLogin(Request $request)
    {
        $request->validate([
            $this->username() => 'required|string',
            'password' => 'required|string',
        ], [
            'email.required' => 'กรุณากรอกอีเมลของคุณ',
            'email.string' => 'อีเมลต้องเป็นข้อความ',
            'password.required' => 'กรุณากรอกรหัสผ่านของคุณ',
            'password.string' => 'รหัสผ่านต้องเป็นข้อความ',
        ]);
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $user = Auth::user();
        
        if ($user) {
            Log::info('User logged out', [
                'id' => $user->id,
                'email' => $user->email,
                'name' => $user->name,
                'ip' => $request->ip()
            ]);
        }

        $this->guard()->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'ออกจากระบบสำเร็จ'
            ]);
        }

        return redirect('/login')->with('status', 'คุณได้ออกจากระบบเรียบร้อยแล้ว');
    }
    
    /**
     * Check if user is authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkAuthentication(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'status' => 'error',
                'authenticated' => false,
                'message' => 'ไม่ได้เข้าสู่ระบบ'
            ], 401);
        }
        
        $user = Auth::user();
        
        return response()->json([
            'status' => 'success',
            'authenticated' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'roles' => $user->roles->pluck('name'),
                'permissions' => $user->getAllPermissions()->pluck('name')
            ]
        ]);
    }
    
    /**
     * Get the failed login response instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        throw ValidationException::withMessages([
            $this->username() => [trans('auth.failed')],
        ])->redirectTo(route('login'))->status(422);
    }
    
    /**
     * Get the needed authorization credentials from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        $credentials = $request->only($this->username(), 'password');
        
        // Add additional conditions if needed
        // For example, check if user is active
        $credentials['active'] = 1;
        
        return $credentials;
    }
    
    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function username()
    {
        return 'email';
    }
    
    /**
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        // Set session data if needed
        Session::put('user_role', $user->roles->pluck('name')->first());
        
        // Different redirect paths based on role
        if ($user->hasRole('admin')) {
            return redirect()->intended('/admin/dashboard');
        }
        
        if ($user->hasRole('manager')) {
            return redirect()->intended('/manager/dashboard');
        }
        
        return redirect()->intended($this->redirectPath());
    }
}
