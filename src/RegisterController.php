<?php

namespace Thuleen\SsoMfa;

use Dotenv\Dotenv;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\SvgWriter;

class RegisterController extends Controller
{

    public function __construct()
    {
        $packageRoot = dirname(__DIR__, 1);
        $dotenv = Dotenv::createImmutable($packageRoot);
        $dotenv->load();
    }

    public function submitForm(Request $request)
    {
        $username = $request->input('username');
        $password = $request->input('password');

        echo '' . $username . '' . $password . '';

        // Pass the form data to the MfaService method in your package
        // $result = $registerService->processForm($username, $password);

        // Handle the result and return a response
        // You can redirect, display a message, or perform any other action here

        // Check if registration was successful
        if (true) {
            // Redirect to the success page
            return redirect()->route('sso.pending.create.ethacc', ['username' => $username]);
        } else {
            // Registration failed, you can handle this accordingly
            // For example, return to the registration form with an error message
            return redirect()->route('sso.form.login')->with('error', 'Registration failed.');
        }
    }

    /**
     * Pending creating a new Ethereum account
     */
    public function pendAcc($username)
    {
        $appName = config('app.name');
        $url = $this->generateUrl($username);
        $writer = new SvgWriter();
        $qrCode = QrCode::create($url);
        $result = $writer->write($qrCode);
        $dataUri = $result->getDataUri();

        return view('ssomfa::pendacc', compact('dataUri', 'url', 'appName'));
    }

    private function generateUrl($username)
    {
        $timestamp = time();
        $appName = config('app.name');
        $sessionId = session()->getId();
        $encodedQr = base64_encode($appName . '__[THUSSOMFA]__' . $username . '__[SID]__' . $sessionId . '__[TS]__' . $timestamp);
        $dappUrl = env('THULEEN_DAPP_URL');
        return $dappUrl . $encodedQr;
    }

    public function index()
    {
        return view('thuleen-mfa::registered');
    }
}
