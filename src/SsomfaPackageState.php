<?php

namespace Thuleen\Ssomfa;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;


class SsomfaPackageState
{
  private const EXPIRATION_MINS = 3;
  private const IS_CONTRACT_LOADED = 'thuleen.ssomfa.isContractLoaded';
  private const MFA_CONTRACT_ADDRESS = 'thuleen.ssomfa.contract.mfa.address';
  private const USER_EMAIL = 'thuleen.ssomfa.user.email';
  private const USER_OTP_GUESS = 'thuleen.ssomfa.user.otp';
  private const TIMESTAMP = 'thuleen.ssomfa.user.timestamp';
  private const USER_OTP_VALID = 'thuleen.ssomfa.user.otp.valid';
  private const DEV_MODE = 'thuleen.ssomfa.dev.mode';
  private const SECURED_ROUTE_NAME = 'thuleen.ssomfa.secured.routeName';

  private $isContractLoaded = false;
  private $guessCounter = 0;

  public function setContractIsLoaded($isLoaded)
  {
    $this->isContractLoaded = $isLoaded;
    Cache::put(self::IS_CONTRACT_LOADED, $isLoaded);
  }

  public function isContractLoaded()
  {
    // Check if the property is set; if not, retrieve from the cache
    if ($this->isContractLoaded === null) {
      $this->isContractLoaded = Cache::get(self::IS_CONTRACT_LOADED, false);
    }

    return $this->isContractLoaded;
  }

  public function setMfaContractAddress($address)
  {
    Cache::put(self::MFA_CONTRACT_ADDRESS, $address);
  }

  public function getMfaContractAddress()
  {
    return Cache::get(self::MFA_CONTRACT_ADDRESS);
  }

  public function setUserEmail($email)
  {
    // Store the user's email in the session
    Session::put(self::USER_EMAIL, $email);
  }

  public function getUserEmail()
  {
    // Retrieve the user's email from the session
    return Session::get(self::USER_EMAIL);
  }

  public function setUserOtpGuess($otpGuess)
  {
    Cache::put(self::USER_OTP_GUESS, $otpGuess, now()->addMinutes(self::EXPIRATION_MINS)); // Adjust the expiration time as needed
    $this->guessCounter++;
  }

  public function incrementGuessCounter()
  {
    $count = Cache::get(self::USER_OTP_GUESS . '.count', 0);
    $count++;
    Cache::put(self::USER_OTP_GUESS . '.count', $count);
  }

  public function getUserOtpGuess()
  {
    return Cache::get(self::USER_OTP_GUESS);
  }

  public function setOtpValid($valid)
  {
    Cache::put(self::USER_OTP_VALID, $valid);
  }

  public function isOtpValid()
  {
    return Cache::get(self::USER_OTP_VALID);
  }

  public function setTimestamp($ts)
  {
    Cache::put(self::TIMESTAMP, $ts);
  }

  public function getTimestamp()
  {
    return Cache::get(self::TIMESTAMP);
  }

  public function setDevMode($mode)
  {
    Cache::put(self::DEV_MODE, $mode);
  }

  public function getDevMode()
  {
    return Cache::get(self::DEV_MODE);
  }

  public function setSecuredRouteName($routeName)
  {
    Cache::put(self::SECURED_ROUTE_NAME, $routeName);
  }

  public function getSecuredRouteName()
  {
    return Cache::get(self::SECURED_ROUTE_NAME);
  }
}
