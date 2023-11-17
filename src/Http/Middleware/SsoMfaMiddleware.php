<?php

namespace Thuleen\Ssomfa\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Writer\SvgWriter;
use Dotenv\Dotenv;
use Thuleen\Ssomfa\SsomfaPackageState;

class SsoMfaMiddleware
{

    public function __construct()
    {
        $packageRoot = dirname(__DIR__, 3);
        $dotenv = Dotenv::createImmutable($packageRoot);
        $dotenv->load();
        try {
            $appId = config('app.id');
            $appName = config('app.name');
            $ssoApiUrl = env('THULEEN_SSOMFA_API_URL') . 'init' . '/' . $appId . '/' . $appName;
            $response = Http::get($ssoApiUrl);
            $responseData = $response->json();
            SsomfaPackageState::setContractIsLoaded(strlen($responseData['contractName']) > 0);
            SsomfaPackageState::setMfaContractAddress($responseData['contractAddress']);
        } catch (\Exception $e) {
            SsomfaPackageState::setContractIsLoaded(false);
        }
    }

    public function handle($request, Closure $next)
    {
        $appId = config('app.id');
        $appName = config('app.name');
        $email = $request->user()->email;
        SsomfaPackageState::setUserEmail($email);
        $timestamp = time();

        if (!$this->isMfaVerified($timestamp) && !SsomfaPackageState::getUserOtpGuess()) {

            $url = $this->generateUrl($email, $timestamp);
            $writer = new SvgWriter();
            $qrCode = QrCode::create($url);
            $qrCode->setForegroundColor(new Color(39, 60, 117));
            $result = $writer->write($qrCode);
            $dataUri = $result->getDataUri();

            $isContractLoaded = SsomfaPackageState::isContractLoaded();
            $mfaContractAddr = SsomfaPackageState::getMfaContractAddress();
            $isOtpValid = null;
            return response(view('ssomfa::qrcode', compact('dataUri', 'url', 'appName', 'email', 'isContractLoaded', 'mfaContractAddr', 'isOtpValid', 'timestamp')));
        } elseif (!$this->isMfaVerified($timestamp) && SsomfaPackageState::getUserOtpGuess()) {

            $url = $this->generateUrl($email, $timestamp);
            $writer = new SvgWriter();
            $qrCode = QrCode::create($url);
            $qrCode->setForegroundColor(new Color(39, 60, 117));
            $result = $writer->write($qrCode);
            $dataUri = $result->getDataUri();

            $isContractLoaded = SsomfaPackageState::isContractLoaded();
            $mfaContractAddr = SsomfaPackageState::getMfaContractAddress();
            $isOtpValid = SsomfaPackageState::isOtpValid();
            return response(view('ssomfa::qrcode', compact('dataUri', 'url', 'appName', 'email', 'isContractLoaded', 'mfaContractAddr', 'isOtpValid', 'timestamp')));
        }

        $otp = SsomfaPackageState::getUserOtpGuess();
        $ssoApiUrl = env('THULEEN_SSOMFA_API_URL') . 'logged';
        $response = Http::post($ssoApiUrl, ['appId' => $appId, 'appName' => $appName, 'email' => $email, 'timestamp' => $timestamp, 'otp' => $otp]);
        $resDat = $response->json();
        dump($resDat);

        return $next($request);
    }

    public function submitOtpForm(Request $request)
    {
        $otp = $request['otp-digit-1'] . $request['otp-digit-2'] . $request['otp-digit-3'] . $request['otp-digit-4'] . $request['otp-digit-5'];
        SsomfaPackageState::setTimestamp($request['timestamp']);
        SsomfaPackageState::setUserOtpGuess($otp);
        // Redirect to the dashboard or the intended URL
        return redirect(route('dashboard'));
    }

    private function isMfaVerified()
    {
        try {
            $timestamp = SsomfaPackageState::getTimestamp();
            $appId = config('app.id');
            $appName = config('app.name');
            $email = SsomfaPackageState::getUserEmail();
            $otp = SsomfaPackageState::getUserOtpGuess();
            $ssoApiUrl = env('THULEEN_SSOMFA_API_URL') . 'login';
            // Make a request to the verification endpoint
            $response = Http::post($ssoApiUrl, ['appId' => $appId, 'appName' => $appName, 'email' => $email, 'timestamp' => $timestamp, 'otp' => $otp]);
            $resDat = $response->json();

            SsomfaPackageState::setOtpValid($resDat['okToLogin'] === true);

            return $resDat['okToLogin'] === true;
        } catch (\Exception $e) {
            dump($e);
            return false;
        }
    }


    protected function generateUrl($username, $timestamp)
    {
        $appId = env('APP_ID');
        $appName = config('app.name');
        $encodedQr = base64_encode($appName . '__[THUSSOMFA]__' . $username . '__[APPID]__' . $appId . '__[TS]__' . $timestamp);
        $dappUrl = env('THULEEN_SSOMFA_DAPP_URL');
        return $dappUrl . $encodedQr;
    }

    public function logout()
    {
        $appId = config('app.id');
        $email = SsomfaPackageState::getUserEmail();
        $ssoApiUrl = env('THULEEN_SSOMFA_API_URL') . 'logout';
        // Make a request to the verification endpoint
        Http::post($ssoApiUrl, ['appId' => $appId, 'email' => $email]);
    }
}
