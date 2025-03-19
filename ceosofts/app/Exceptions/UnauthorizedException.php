<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class UnauthorizedException extends Exception
{
    protected $message = 'คุณไม่มีสิทธิ์เข้าถึงส่วนนี้';
    protected $code = 403;

    /**
     * Render the exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function render(Request $request)
    {
        if ($request->expectsJson()) {
            return new JsonResponse([
                'message' => $this->getMessage(),
                'status' => 'error'
            ], $this->code);
        }

        return new RedirectResponse(
            url()->previous(),
            302,
            ['X-Error' => $this->getMessage()]
        )->with('error', $this->getMessage());
    }
}
