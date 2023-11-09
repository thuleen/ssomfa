<?php

namespace Thuleen\SsoMfa;

use Dotenv\Dotenv;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Color\Color;
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
        if (true) {
            return redirect()->route('sso.pending.create.ethacc', ['username' => $username]);
        } else {
            return redirect()->route('sso.form.login')->with('error', 'Registration failed.');
        }
    }

    /**
     * Pending account creation
     */
    public function pendAcc($username)
    {
        $appName = config('app.name');
        $url = $this->generateUrl($username);
        $writer = new SvgWriter();
        $qrCode = QrCode::create($url);
        $qrCode->setForegroundColor(new Color(39, 60, 117));
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
