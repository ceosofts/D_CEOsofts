<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
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
        $this->middleware('guest');
    }

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\View\View
     */
    public function showRegistrationForm()
    {
        return view('auth.register', [
            'pageTitle' => 'สมัครสมาชิก',
            'description' => 'สมัครเข้าใช้งานระบบ CEOSOFTS'
        ]);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/'],
            'terms' => ['required', 'accepted'],
            'phone' => ['nullable', 'string', 'max:20'],
        ], [
            'name.required' => 'กรุณากรอกชื่อของคุณ',
            'email.required' => 'กรุณากรอกอีเมลของคุณ',
            'email.email' => 'กรุณากรอกอีเมลในรูปแบบที่ถูกต้อง',
            'email.unique' => 'อีเมลนี้ถูกใช้งานแล้ว กรุณาใช้อีเมลอื่น',
            'password.required' => 'กรุณากรอกรหัสผ่าน',
            'password.min' => 'รหัสผ่านต้องมีความยาวอย่างน้อย 8 ตัวอักษร',
            'password.confirmed' => 'การยืนยันรหัสผ่านไม่ตรงกัน',
            'password.regex' => 'รหัสผ่านต้องประกอบด้วยตัวพิมพ์เล็ก ตัวพิมพ์ใหญ่ และตัวเลข',
            'terms.required' => 'กรุณายอมรับข้อกำหนดและเงื่อนไข',
            'terms.accepted' => 'คุณต้องยอมรับข้อกำหนดและเงื่อนไขก่อนสมัครสมาชิก',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'phone' => $data['phone'] ?? null,
            'active' => true,
            'email_verified_at' => null,
            'remember_token' => Str::random(10),
        ]);
        
        // Assign default role for new registrations
        $defaultRole = Role::where('name', 'customer')->first();
        if ($defaultRole) {
            $user->assignRole($defaultRole);
        }
        
        Log::info('New user registered', [
            'id' => $user->id,
            'email' => $user->email,
            'name' => $user->name,
        ]);
        
        return $user;
    }
    
    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        event(new Registered($user = $this->create($request->all())));

        $this->guard()->login($user);
        
        if ($response = $this->registered($request, $user)) {
            return $response;
        }

        return $request->wantsJson()
            ? new JsonResponse([
                'status' => 'success',
                'message' => 'ลงทะเบียนสำเร็จ',
                'redirect' => $this->redirectPath(),
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email
                ]
            ], 201)
            : redirect($this->redirectPath())->with('success', 'ยินดีต้อนรับสู่ CEOSOFTS! ลงทะเบียนสำเร็จ');
    }
    
    /**
     * The user has been registered.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function registered(Request $request, $user)
    {
        // Send verification email or welcome notification
        // $user->sendEmailVerificationNotification();
        
        // Redirect user to appropriate dashboard based on role
        if ($user->hasRole('customer')) {
            return redirect()->intended('/customer/dashboard');
        }
        
        return null;
    }
}
