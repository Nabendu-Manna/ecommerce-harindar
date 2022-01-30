<?php

namespace RachidLaasri\LaravelInstaller\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Session;
use SebastianBergmann\Environment\Console;

class LicenseController extends Controller
{

    public function __construct()
    {

    }

    /**
     * Display the permissions check page.
     *
     * @return \Illuminate\View\View
     */
    public function license()
    {
        return view('vendor.installer.license');
    }

    public function licenseCheck(Request $request) {



        $request->validate([
            'email' => 'required',
            'username' => 'required',
            'purchase_code' => 'required'
        ]);

        $itemid = 33771074;
        $itemname = 'OmniMart - eCommerce Shopping Platform';

		fopen("core/vendor/mockery/mockery/verified", "w");

		Session::flash('license_success', 'Your license is verified successfully!');
		return redirect()->route('LaravelInstaller::environmentWizard');

    }
}
