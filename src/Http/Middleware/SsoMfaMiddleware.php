<?php

namespace Thuleen\Ssomfa\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Http\Response; // Import the Response class at the top
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Writer\SvgWriter;
use Dotenv\Dotenv;
use Illuminate\Support\Facades\Cache;


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
        $appName = config('app.name');
        $email = $request->user()->email;
        if (!$this->isMfaVerified($request, $appName, $email)) {

            // MFA is not verified, create a view response and return it
            $url = $this->generateUrl($email);
            $writer = new SvgWriter();
            $qrCode = QrCode::create($url);
            $qrCode->setForegroundColor(new Color(39, 60, 117));
            $result = $writer->write($qrCode);
            $dataUri = $result->getDataUri();

            return response(view('ssomfa::qrcode', compact('dataUri', 'url', 'appName', 'email')));
            // return redirect(route('verify.qrcode'))->with(['dataUri', $dataUri]);
        }

        return $next($request);
    }

    public function submitOtpForm(Request $request)
    {

        $otp = $request['digit-1'] . $request['digit-2'] . $request['digit-3'] . $request['digit-4'] . $request['digit-5'];

        $cacheKey = $request->input('email') . '_otp';
        Cache::put($cacheKey, $otp, now()->addMinutes(3)); // Adjust the expiration time as needed

        // Redirect to the dashboard or the intended URL
        return redirect(route('dashboard'));
    }

    private function isMfaVerified(Request $request, $appName, $email)
    {
        $email = $request->input('email');
        $cacheKey = $email . '_otp';
        $otp = Cache::get($cacheKey);
        // request rest api at endpoint http://localhost:9000/verify
        // Make a request to the verification endpoint
        $response = Http::post('http://localhost:9000/verify', ['appName' => $appName, 'email' => $email, 'otp' => $otp]);

        $responseData = $response->json();
        dump($responseData['verify']);

        // Return false if any checks fail
        return $responseData['verify'] === true;
    }


    protected function generateUrl($username)
    {
        dump($username);
        $timestamp = time();
        $appName = config('app.name');
        $sessionId = session()->getId();
        $encodedQr = base64_encode($appName . '__[THUSSOMFA]__' . $username . '__[SID]__' . $sessionId . '__[TS]__' . $timestamp);
        $dappUrl = env('THULEEN_DAPP_URL');
        return $dappUrl . $encodedQr;
    }
}
