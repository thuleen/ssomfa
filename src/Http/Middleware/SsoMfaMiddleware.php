<?php

namespace Thuleen\Ssomfa\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response; // Import the Response class at the top
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Writer\SvgWriter;
use Dotenv\Dotenv;


class SsoMfaMiddleware
{

    public function __construct()
    {
        $packageRoot = dirname(__DIR__, 3);
        $dotenv = Dotenv::createImmutable($packageRoot);
        $dotenv->load();
    }

    public function handle($request, Closure $next)
    {
        if (!$this->isMfaVerified($request)) {
            $appName = config('app.name');
            $email = $request->input('email');
            // MFA is not verified, create a view response and return it
            $url = $this->generateUrl($email);
            $writer = new SvgWriter();
            $qrCode = QrCode::create($url);
            $qrCode->setForegroundColor(new Color(39, 60, 117));
            $result = $writer->write($qrCode);
            $dataUri = $result->getDataUri();

            return response(view('ssomfa::qrcode', compact('dataUri', 'url', 'appName')));
        }

        return $next($request);
    }

    private function isMfaVerified(Request $request)
    {
        // Implement your MFA verification logic here
        // Use your 'thuleen/ssomfa' package's functions to check MFA status

        // Return true if MFA is verified, false if not
        // For example, you might check a session variable or other criteria
        return false;
    }

    protected function generateUrl($username)
    {
        $timestamp = time();
        $appName = config('app.name');
        $sessionId = session()->getId();
        $encodedQr = base64_encode($appName . '__[THUSSOMFA]__' . $username . '__[SID]__' . $sessionId . '__[TS]__' . $timestamp);
        $dappUrl = env('THULEEN_DAPP_URL');
        return $dappUrl . $encodedQr;
    }
}
