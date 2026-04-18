<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TwoFactorController extends Controller
{
    public function index() {
        $user = Auth::user();
        if (!$user->two_factor_code || $user->two_factor_expires_at < now()) {
            $user->two_factor_code = rand(100000, 999999);
            $user->two_factor_expires_at = now()->addMinutes(10);
            $user->save();
            // Send the 2FA code via email
            \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\TwoFactorCode($user->two_factor_code));
        }
        return view('admin.auth.2fa');
    }
    public function verify(Request $request) {
        $request->validate(['code' => 'required|numeric']);
        $user = Auth::user();
        if ($request->code == $user->two_factor_code && $user->two_factor_expires_at > now()) {
            $user->two_factor_code = null;
            $user->two_factor_expires_at = null;
            $user->save();
            $request->session()->put('2fa_verified', true);
            return redirect()->route('admin.dashboard');
        }
        return redirect()->back()->withErrors(['code' => 'Invalid or expired code.']);
    }
}