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

        $ssoApiUrl = env('THULEEN_SSOMFA_API_URL') . 'init';
        $response = Http::get($ssoApiUrl);
        $responseData = $response->json();

        SsomfaPackageState::setContractIsLoaded(strlen($responseData['contractName']) > 0);
        SsomfaPackageState::setMfaContractAddress($responseData['contractAddress']);
    }

    public function handle($request, Closure $next)
    {
        $appId = config('app.id');
        $appName = config('app.name');
        $email = $request->user()->email;
        SsomfaPackageState::setUserEmail($email);
        if (!$this->isMfaVerified($request, $appId, $appName, $email) && !SsomfaPackageState::getUserOtpGuess()) {

            $url = $this->generateUrl($email);
            $writer = new SvgWriter();
            $qrCode = QrCode::create($url);
            $qrCode->setForegroundColor(new Color(39, 60, 117));
            $result = $writer->write($qrCode);
            $dataUri = $result->getDataUri();

            $isContractLoaded = SsomfaPackageState::isContractLoaded();
            $mfaContractAddr = SsomfaPackageState::getMfaContractAddress();
            $isOtpValid = null;
            return response(view('ssomfa::qrcode', compact('dataUri', 'url', 'appName', 'email', 'isContractLoaded', 'mfaContractAddr', 'isOtpValid')));
        }

        if (!$this->isMfaVerified($request, $appId, $appName, $email) && SsomfaPackageState::getUserOtpGuess()) {

            $url = $this->generateUrl($email);
            $writer = new SvgWriter();
            $qrCode = QrCode::create($url);
            $qrCode->setForegroundColor(new Color(39, 60, 117));
            $result = $writer->write($qrCode);
            $dataUri = $result->getDataUri();

            $isContractLoaded = SsomfaPackageState::isContractLoaded();
            $mfaContractAddr = SsomfaPackageState::getMfaContractAddress();
            $isOtpValid = SsomfaPackageState::isOtpValid();
            return response(view('ssomfa::qrcode', compact('dataUri', 'url', 'appName', 'email', 'isContractLoaded', 'mfaContractAddr', 'isOtpValid')));
        }

        return $next($request);
    }

    public function submitOtpForm(Request $request)
    {
        $otp = $request['otp-digit-1'] . $request['otp-digit-2'] . $request['otp-digit-3'] . $request['otp-digit-4'] . $request['otp-digit-5'];
        SsomfaPackageState::setUserOtpGuess($otp);
        // Redirect to the dashboard or the intended URL
        return redirect(route('dashboard'));
    }

    private function isMfaVerified(Request $request, $appId, $appName, $email)
    {
        $otp = SsomfaPackageState::getUserOtpGuess();
        $ssoApiUrl = env('THULEEN_SSOMFA_API_URL') . 'verify';
        // Make a request to the verification endpoint
        $response = Http::post($ssoApiUrl, ['appId' => $appId, 'appName' => $appName, 'email' => $email, 'otp' => $otp]);

        $responseData = $response->json();

        SsomfaPackageState::setOtpValid($responseData['verify'] === true);

        // Return false if any checks fail
        return $responseData['verify'] === true;
    }


    protected function generateUrl($username)
    {
        // Sample public key
        // $pk = '0x70997970C51812dc3A010C7d01b50e0d17dc79C8';
        $pk = null;
        $timestamp = time();
        $appId = env('APP_ID');
        // dump($appId);
        $appName = config('app.name');
        $encodedQr = base64_encode($appName . '__[THUSSOMFA]__' . $username . '__[APPID]__' . $appId . '__[TS]__' . $timestamp . '__[PK]__' . $pk);
        $dappUrl = env('THULEEN_SSOMFA_DAPP_URL');
        return $dappUrl . $encodedQr;
    }
}
