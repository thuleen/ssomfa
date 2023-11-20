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
    protected $apiUrl;
    protected $dappUrl;

    public function __construct()
    {
        $packageRoot = dirname(__DIR__, 3);
        $dotenv = Dotenv::createImmutable($packageRoot);
        $dotenv->load();
        $this->apiUrl = env('THULEEN_SSOMFA_API_URL');
        try {
            $appId = config('app.id');
            $appName = config('app.name');
            $ssoApiUrl = $this->apiUrl . '/init' . '/' . $appId . '/' . $appName;
            $response = Http::get($ssoApiUrl);
            $responseData = $response->json();
            $this->dappUrl = $responseData['dappUrl'];
            SsomfaPackageState::setContractIsLoaded(strlen($responseData['contractName']) > 0);
            SsomfaPackageState::setMfaContractAddress($responseData['contractAddress']);
        } catch (\Exception $e) {
            // dump($e);
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

        $isContractLoaded = SsomfaPackageState::isContractLoaded();

        if (!$isContractLoaded) {
            $dappUrl = $this->dappUrl;
            // Redirect to the warning page
            return response(view('ssomfa::warning', compact('isContractLoaded', 'dappUrl')));
        }

        if (!$this->isMfaVerified($timestamp) && !SsomfaPackageState::getUserOtpGuess()) {

            $url = $this->generateUrl($email, $timestamp);
            $writer = new SvgWriter();
            $qrCode = QrCode::create($url);
            $qrCode->setForegroundColor(new Color(39, 60, 117));
            $result = $writer->write($qrCode);
            $dataUri = $result->getDataUri();

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

            $mfaContractAddr = SsomfaPackageState::getMfaContractAddress();
            $isOtpValid = SsomfaPackageState::isOtpValid();
            return response(view('ssomfa::qrcode', compact('dataUri', 'url', 'appName', 'email', 'isContractLoaded', 'mfaContractAddr', 'isOtpValid', 'timestamp')));
        }

        $otp = SsomfaPackageState::getUserOtpGuess();
        $ssoApiUrl = $this->apiUrl . '/logged';
        Http::post($ssoApiUrl, ['appId' => $appId, 'appName' => $appName, 'email' => $email, 'timestamp' => $timestamp, 'otp' => $otp]);

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
            $ssoApiUrl = $this->apiUrl . '/login';
            // Make a request to the verification endpoint
            $response = Http::post($ssoApiUrl, ['appId' => $appId, 'appName' => $appName, 'email' => $email, 'timestamp' => $timestamp, 'otp' => $otp]);
            $resDat = $response->json();

            SsomfaPackageState::setOtpValid($resDat['okToLogin'] === true);

            return $resDat['okToLogin'] === true;
        } catch (\Exception $e) {
            // dump($e);
            return false;
        }
    }


    protected function generateUrl($username, $timestamp)
    {
        $appId = env('APP_ID');
        $appName = config('app.name');
        $encodedQr = base64_encode($appName . '__[THUSSOMFA]__' . $username . '__[APPID]__' . $appId . '__[TS]__' . $timestamp);
        $dappUrl = $this->dappUrl;
        return $dappUrl . $encodedQr;
    }

    public function logout()
    {
        $appId = config('app.id');
        $email = SsomfaPackageState::getUserEmail();
        $ssoApiUrl = $this->apiUrl . '/logout';
        // Make a request to the verification endpoint
        Http::post($ssoApiUrl, ['appId' => $appId, 'email' => $email]);
    }
}
