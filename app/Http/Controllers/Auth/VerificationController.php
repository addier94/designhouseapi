<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use App\Providers\RouteServiceProvider;
// use Illuminate\Foundation\Auth\VerifiesEmails;

class VerificationController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('signed')->only('verify');
        $this->middleware('throttle:6,1')->only('verify', 'resend');
    }

    public function verify(Request $request, User $user) 
    {
        // check if the url is a valid signed url
        if (! URL::hasValidSignature($request)) {
            return response()->json(['errors' => [
                "message" => "Enlace de verificación invalido"
            ]], 422);
        }

        // check if the user has already verified accound
        if($user->hasVerifiedEmail()) {
             return response()->json(["errors" => [
                 "message" => "dirección de correo electrónico ya verificada"
             ]], 422);
        }

        $user->markEmailAsVerified();
        event(new Verified($user));

        return response()->json(['message' => 'Correo Verificado con éxito'], 200);
    }

    public function resend(Request $request)
    {
        $this->validate($request, [
            'email' => ['email', 'required']
        ]);

        $user = User::where('email', $request->email)->first();
        if(! $user) {
            return response()->json(["errors" => [
                "email" => "Usuario no encontrado con este email"
            ]], 422);
        }
        if($user->hasVerifiedEmail()){
            return response()->json(["errors" => [
                "message" => "Correo ya ha sido verificado"
            ]], 422);
        }

        $user->sendEmailVerificationNotification();

        return response()->json(['status' => "Enlace de verificación reenviado"]);
    }
}
