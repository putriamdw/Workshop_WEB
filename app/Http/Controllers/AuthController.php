<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
public function redirectToGoogle() // mengarahkan ke Google untuk login (belum ada login, session)
{
    return Socialite::driver('google')->redirect();
}

public function handleGoogleCallback()
{
     $googleUser = Socialite::driver('google')->stateless()->user();

    $user = User::updateOrCreate( // email ada (update data), email belum ada (buat user baru)
        ['email' => $googleUser->email],
        [
            'name' => $googleUser->name,
            'id_google' => $googleUser->id,
            'password' => Hash::make(Str::random(16)),
            'role' => 'user'
        ]
    );

    $otp = rand(100000, 999999);
    $user->update(['otp' => $otp]);

    session([ // simpan session/email sementara karena user belum resmi logim
    'otp_email' => $user->email,
    'login_via_google' => true
]);

    Mail::raw("Kode OTP Anda: $otp", function ($message) use ($user) { // sistem kirim otp ke email
        $message->to($user->email)
                ->subject('Kode OTP Login');
    });
    return redirect()->route('otp.form'); // diarahkan ke halaman input otp
}

public function showOtpForm()
{
    if (!session('login_via_google')) { // jika tidak login via google, tidak otp (langsung ke login biasa)
        return redirect('/login');
    }

    return view('auth.otp');
}

public function verifyOtp(Request $request)
{
    $request->validate([
        'otp' => 'required|digits:6'
    ]);

    $email = session('otp_email');

    if (!$email) {
        return redirect('/login');
    }

    $user = User::where('email', $email)->first();

    if (!$user || !$user->otp) {
        return redirect('/login')
               ->with('error', 'OTP sudah tidak valid, silakan login ulang.');
    }

    if (trim($user->otp) === trim($request->otp)) {
        $request->session()->regenerate();
        Auth::login($user);
        $user->update(['otp' => null]);
        session()->forget(['otp_email', 'login_via_google']);

        // ✅ Redirect berdasarkan role
        return match($user->role) {
            'admin'  => redirect()->route('admin.dashboard'),
            'vendor' => redirect()->route('vendor.dashboard'),
            default  => redirect()->route('dashboard'),
        };
    }

    return back()->with('error', 'OTP salah');
}
}