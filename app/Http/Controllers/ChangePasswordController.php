<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Api\Controller;
use App\Http\Requests\UpdatePasswordRequest;
use Symfony\Component\HttpFoundation\Response;

class ChangePasswordController extends Controller
{
    public function passwordResetProcess(UpdatePasswordRequest $request)
    {
        return $this->updatePasswordRow($request)
            ->count() > 0 ? $this->resetPassword($request) : $this->tokenNotFoundError();
    }

    private function updatePasswordRow($request)
    {
        return DB::table('password_reset_tokens')->where([
            'email' => $request->email,
            'token' => $request->token
        ]);
    }

    private function resetPassword($request)
    {
        // find email
        $userData = User::whereEmail($request->email)->first();
        // update password
        $userData->update([
            'password' => bcrypt($request->password)
        ]);
        // remove verification data from db
        $this->updatePasswordRow($request)->delete();
        // reset password response
        return response()->json([
            'data' => 'Password has been updated.'
        ], Response::HTTP_CREATED);
    }

    private function tokenNotFoundError()
    {
        return response()->json([
            'error' => '.'
        ], Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
