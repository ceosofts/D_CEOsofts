<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\VerifiesEmails;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Log;
use App\Providers\RouteServiceProvider;

class VerificationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Email Verification Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling email verification for any
    | user that recently registered with the application. Emails may also
    | be re-sent if the user didn't receive the original email message.
    |
    */

    use VerifiesEmails;

    /**
     * Where to redirect users after verification.
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
        $this->middleware('signed')->only('verify');
        $this->middleware('throttle:6,1')->only('verify', 'resend');
    }
    
    /**
     * Show the email verification notice.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function show(Request $request)
    {
        return $request->user()->hasVerifiedEmail()
            ? redirect($this->redirectPath())
            : view('auth.verify', [
                'pageTitle' => 'ยืนยันอีเมล',
                'description' => 'กรุณาตรวจสอบอีเมลของคุณเพื่อยืนยันตัวตน'
            ]);
    }

    /**
     * Mark the authenticated user's email address as verified.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function verify(Request $request)
    {
        if (! hash_equals((string) $request->route('id'), (string) $request->user()->getKey())) {
            return redirect()->route('verification.notice')
                ->with('error', 'ลิงก์ยืนยันอีเมลไม่ถูกต้อง กรุณาลองอีกครั้ง');
        }

        if (! hash_equals((string) $request->route('hash'), sha1($request->user()->getEmailForVerification()))) {
            return redirect()->route('verification.notice')
                ->with('error', 'ลิงก์ยืนยันอีเมลไม่ถูกต้อง กรุณาลองอีกครั้ง');
        }

        if ($request->user()->hasVerifiedEmail()) {
            return $request->wantsJson()
                ? new JsonResponse([], 204)
                : redirect($this->redirectPath())
                    ->with('status', 'อีเมลของคุณได้รับการยืนยันแล้ว');
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
            
            // Log successful email verification
            Log::info('Email verified successfully', [
                'user_id' => $request->user()->id,
                'email' => $request->user()->email,
                'ip' => $request->ip()
            ]);
        }

        if ($response = $this->verified($request)) {
            return $response;
        }

        return $request->wantsJson()
            ? new JsonResponse(['status' => 'success', 'message' => 'อีเมลได้รับการยืนยันเรียบร้อยแล้ว'], 200)
            : redirect($this->redirectPath())
                ->with('status', 'อีเมลได้รับการยืนยันเรียบร้อยแล้ว ขอบคุณ!');
    }

    /**
     * Resend the email verification notification.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function resend(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return $request->wantsJson()
                ? new JsonResponse(['status' => 'success', 'message' => 'อีเมลได้รับการยืนยันแล้ว'], 200)
                : redirect($this->redirectPath())->with('status', 'อีเมลของคุณได้รับการยืนยันแล้ว');
        }

        $request->user()->sendEmailVerificationNotification();

        // Log resent verification email
        Log::info('Verification email resent', [
            'user_id' => $request->user()->id,
            'email' => $request->user()->email,
            'ip' => $request->ip()
        ]);

        return $request->wantsJson()
            ? new JsonResponse(['status' => 'success', 'message' => 'ส่งลิงก์ยืนยันไปยังอีเมลของคุณเรียบร้อยแล้ว'], 202)
            : back()->with('status', 'ส่งลิงก์ยืนยันไปยังอีเมลของคุณเรียบร้อยแล้ว');
    }
    
    /**
     * The user has been verified.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    protected function verified(Request $request)
    {
        // You can add custom logic here that runs after verification
        
        if ($request->user()->onboarding_completed === false) {
            return redirect()->route('user.onboarding')
                ->with('status', 'อีเมลของคุณได้รับการยืนยันแล้ว กรุณากรอกข้อมูลเพิ่มเติมเพื่อเริ่มต้นใช้งาน');
        }
        
        return null;
    }
}
