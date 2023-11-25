<?php

namespace Thuleen\Ssomfa\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Logo\Logo;
use Dotenv\Dotenv;
use Thuleen\Ssomfa\SsomfaPackageState;
use Illuminate\Support\Facades\Cache;

class SsoMfaMiddleware
{
    protected $apiUrl;
    protected $dappUrl;
    protected $state;

    public function __construct()
    {
        $packageRoot = dirname(__DIR__, 3);
        $dotenv = Dotenv::createImmutable($packageRoot);
        $dotenv->load();
        $this->state = new SsomfaPackageState();
        $this->apiUrl = config('ssomfa.api_url'); // Refer config/ssomfa.php file
        try {
            $appId = config('app.id');
            $appName = config('app.name');
            $ssoApiUrl = $this->apiUrl . '/init' . '/' . $appId . '/' . $appName;
            $response = Http::get($ssoApiUrl);
            $responseData = $response->json();
            $this->dappUrl = $responseData['dappUrl'];
            $this->state->setContractIsLoaded(strlen($responseData['contractName']) > 0);
            $this->state->setMfaContractAddress($responseData['contractAddress']);
            $this->state->setDevMode($responseData['devMode']);
        } catch (\Exception $e) {
            // dump($e);
            $this->state->setContractIsLoaded(false);
        }
    }

    public function handle($request, Closure $next)
    {
        $apiUrl = $this->apiUrl;
        $appId = config('app.id');
        $appName = config('app.name');
        $email = $request->user()->email;
        $this->state->setUserEmail($email);
        $timestamp = time();

        $isContractLoaded = $this->state->isContractLoaded();
        $devMode = $this->state->getDevMode();

        if (!$isContractLoaded) {
            $dappUrl = $this->dappUrl;
            // Redirect to the warning page
            return response(view('ssomfa::warning', compact('isContractLoaded', 'dappUrl', 'apiUrl')));
        }

        $count = Cache::get('thuleen.ssomfa.user.otp.count', 0);

        if (!$this->isMfaVerified($timestamp) && !$this->state->getUserOtpGuess()) {

            $url = $this->generateUrl($email, $timestamp);
            $qrCode = QrCode::create($url);
            $qrCode->setForegroundColor(new Color(39, 60, 117));
            $logo = Logo::create(__DIR__ . '/assets/images/logo.png')
                // ->setResizeToWidth(55)
                ->setPunchoutBackground(true);
            $writer = new PngWriter();
            $result = $writer->write($qrCode, $logo);
            $dataUri = $result->getDataUri();

            $mfaContractAddr = $this->state->getMfaContractAddress();
            $isOtpValid = null;
            return response(view('ssomfa::qrcode', compact('devMode', 'dataUri', 'url', 'appName', 'email', 'isContractLoaded', 'mfaContractAddr', 'isOtpValid', 'timestamp', 'count', 'apiUrl')));
        } elseif (!$this->isMfaVerified($timestamp) && $this->state->getUserOtpGuess()) {

            $url = $this->generateUrl($email, $timestamp);
            $qrCode = QrCode::create($url);
            $qrCode->setForegroundColor(new Color(39, 60, 117));
            $logo = Logo::create(__DIR__ . '/assets/images/logo.png')
                // ->setResizeToWidth(55)
                ->setPunchoutBackground(true);
            $writer = new PngWriter();
            $result = $writer->write($qrCode, $logo);
            $dataUri = $result->getDataUri();

            $mfaContractAddr = $this->state->getMfaContractAddress();
            $isOtpValid = $this->state->isOtpValid();
            return response(view('ssomfa::qrcode', compact('devMode', 'dataUri', 'url', 'appName', 'email', 'isContractLoaded', 'mfaContractAddr', 'isOtpValid', 'timestamp', 'count', 'apiUrl')));
        }

        $otp = $this->state->getUserOtpGuess();
        $ssoApiUrl = $this->apiUrl . '/logged';
        Http::post($ssoApiUrl, ['appId' => $appId, 'appName' => $appName, 'email' => $email, 'timestamp' => $timestamp, 'otp' => $otp]);
        Cache::set('thuleen.ssomfa.user.otp.count', 0);
        return $next($request);
    }

    public function submitOtpForm(Request $request)
    {
        $this->state->incrementGuessCounter(); // Increment the counter

        $otp = $request['otp-digit-1'] . $request['otp-digit-2'] . $request['otp-digit-3'] . $request['otp-digit-4'] . $request['otp-digit-5'];
        $this->state->setTimestamp($request['timestamp']);
        $this->state->setUserOtpGuess($otp);
        // Redirect to the dashboard or the intended URL
        return redirect(route('dashboard'));
    }

    private function isMfaVerified()
    {
        try {
            $timestamp = $this->state->getTimestamp();
            $appId = config('app.id');
            $appName = config('app.name');
            $email = $this->state->getUserEmail();
            $otp = $this->state->getUserOtpGuess();
            $ssoApiUrl = $this->apiUrl . '/login';
            // Make a request to the verification endpoint
            $response = Http::post($ssoApiUrl, ['appId' => $appId, 'appName' => $appName, 'email' => $email, 'timestamp' => $timestamp, 'otp' => $otp]);
            $resDat = $response->json();

            $this->state->setOtpValid($resDat['okToLogin'] === true);

            return $resDat['okToLogin'] === true;
        } catch (\Exception $e) {
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
        $email = $this->state->getUserEmail();
        $ssoApiUrl = $this->apiUrl . '/logout';
        // Make a request to the verification endpoint
        Http::post($ssoApiUrl, ['appId' => $appId, 'email' => $email]);
    }
}
