<?php

namespace Thuleen\SsoMfa;

use Dotenv\Dotenv;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Writer\SvgWriter;
use Closure;

class AuthController extends Controller
{

    public function __construct()
    {
        $packageRoot = dirname(__DIR__, 1);
        $dotenv = Dotenv::createImmutable($packageRoot);
        $dotenv->load();
    }

    public function handle($request, Closure $next)
    {
        // Implement the MFA logic here
        // You can use your 'thuleen/ssomfa' package's functions to verify MFA status
        // For example, if MFA is not verified, display a custom page or redirect the user

        if (!$this->isMfaVerified($request)) {
            // MFA is not verified, redirect to a custom page
            return view('ssomfa::test');
        }

        return $next($request);
    }

    private function isMfaVerified(Request $request)
    {
        // Implement your MFA verification logic here
        // Use your 'thuleen/ssomfa' package's functions to check MFA status

        // Return true if MFA is verified, false if not
        // For example, you might check a session variable or other criteria
    }

    public function auth($username)
    {
        $appName = config('app.name');
        $url = $this->generateUrl($username);
        $writer = new SvgWriter();
        $qrCode = QrCode::create($url);
        $qrCode->setForegroundColor(new Color(39, 60, 117));
        $result = $writer->write($qrCode);
        $dataUri = $result->getDataUri();

        return view('ssomfa::auth', compact('dataUri', 'url', 'appName'));
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
}
