<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\ConfirmsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ConfirmPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Confirm Password Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password confirmations and
    | uses a simple trait to include the behavior. You're free to explore
    | this trait and override any functions that require customization.
    |
    */

    use ConfirmsPasswords;

    /**
     * Where to redirect users when the intended url fails.
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
        $this->middleware('auth');
    }
    
    /**
     * Display the password confirmation view.
     *
     * @return \Illuminate\View\View
     */
    public function showConfirmForm()
    {
        return view('auth.passwords.confirm', [
            'pageTitle' => 'ยืนยันรหัสผ่าน',
            'description' => 'กรุณายืนยันรหัสผ่านของคุณก่อนดำเนินการต่อ'
        ]);
    }

    /**
     * Confirm the given user's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function confirm(Request $request)
    {
        try {
            $request->validate($this->rules(), $this->validationErrorMessages());

            $this->resetPasswordConfirmationTimeout($request);

            // Log successful password confirmation
            Log::info('Password confirmed successfully', [
                'user_id' => $request->user()->id,
                'email' => $request->user()->email,
                'ip' => $request->ip()
            ]);

            return $request->wantsJson()
                ? response()->json(['message' => 'รหัสผ่านถูกต้อง'])
                : redirect()->intended($this->redirectPath());
        } catch (\Exception $e) {
            Log::warning('Password confirmation failed', [
                'user_id' => $request->user() ? $request->user()->id : null,
                'email' => $request->user() ? $request->user()->email : null,
                'ip' => $request->ip(),
                'error' => $e->getMessage()
            ]);
            
            throw $e;
        }
    }

    /**
     * Get the password confirmation validation rules.
     *
     * @return array
     */
    protected function rules()
    {
        return [
            'password' => 'required|current_password:web',
        ];
    }

    /**
     * Get the password confirmation validation error messages.
     *
     * @return array
     */
    protected function validationErrorMessages()
    {
        return [
            'password.required' => 'กรุณากรอกรหัสผ่าน',
            'password.current_password' => 'รหัสผ่านไม่ถูกต้อง',
        ];
    }
    
    /**
     * Get the post password confirmation redirect path.
     *
     * @return string
     */
    public function redirectPath()
    {
        if (method_exists($this, 'redirectTo')) {
            return $this->redirectTo();
        }

        return property_exists($this, 'redirectTo') ? $this->redirectTo : RouteServiceProvider::HOME;
    }
}
