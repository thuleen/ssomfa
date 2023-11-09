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

    public function index()
    {
        return view('ssomfa::verifyotp');
    }

    public function submitOtpForm(Request $request)
    {
    }
}
