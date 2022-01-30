<?php

namespace App\Http\Controllers\Auth\User;


use App\{
    Http\Requests\UserRequest,
    Http\Controllers\Controller,
    Repositories\Front\UserRepository
};

use Illuminate\Http\Request;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

use App\Models\Otp;

class RegisterController extends Controller
{

    /**
     * Constructor Method.
     *
     * Setting Authentication
     *
     * @param  \App\Repositories\Back\UserRepository $repository
     *
     */
    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
        $this->smsKey = "B5TScQushXLqVHyo6N2ZKt1Ek0j8al7iOUwg9xdzDApmWrIeRF693H5YtzkhEDdfQKSVi4LIyA0xv2BJ";
    }


    public function showForm()
    {
        return view('user.auth.register');
    }


    public function otp(Request $request){
        $fourRandomDigit = mt_rand(1000,9999);
        $fourRandomDigit2 = mt_rand(1000,9999);
        $fields = array(
            "variables_values" => "".$fourRandomDigit,
            "route" => "otp",
            "numbers" => "".$request->otp_phone,
        );

        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => "https://www.fast2sms.com/dev/bulkV2",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_SSL_VERIFYHOST => 0,
          CURLOPT_SSL_VERIFYPEER => 0,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS => json_encode($fields),
          CURLOPT_HTTPHEADER => array(
            "authorization: ".$this->smsKey,
            "accept: */*",
            "cache-control: no-cache",
            "content-type: application/json"
          ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return response()->json(['sms_status'=> false]);
        } else {

            $otp = Otp::create([
                'phone' => $request->otp_phone,
                'otp' => $fourRandomDigit,
                'rq' => $fourRandomDigit2,
            ]);
        }
        // exit;
        return response()->json(['sms_status'=> true, 'rq'=>$fourRandomDigit2]);
    }

    public function register(UserRequest $request) {

        $request->validate([
            'email' => 'required|email|unique:users,email'
        ]);
        $otp = Otp::where('phone', $request->phone)->where('rq', $request->rq)->first();

        if($request->otp == $otp->otp){
            $this->repository->register($request);
            Session::flash('success',__('Account Register Successfully please login'));
        } else {
            Session::flash('error', "Invalid OTP!");
        }

        return redirect()->back();
    }



    public function verify($token)
    {
        $user = User::where('email_token',$token)->first();

        if($user){

            Auth::login($user);

            return redirect(route('user.dashboard'));
        }else{
            return redirect(route('user.login'));
        }
    }



}
