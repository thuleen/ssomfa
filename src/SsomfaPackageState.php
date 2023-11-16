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

  private static $isContractLoaded = false;

  public static function setContractIsLoaded($isLoaded)
  {
    self::$isContractLoaded = $isLoaded;
    Cache::put(self::IS_CONTRACT_LOADED, $isLoaded);
  }

  public static function isContractLoaded()
  {
    // Check if the property is set; if not, retrieve from the cache
    if (self::$isContractLoaded === null) {
      self::$isContractLoaded = Cache::get(self::IS_CONTRACT_LOADED, false);
    }

    return self::$isContractLoaded;
  }

  public static function setMfaContractAddress($address)
  {
    Cache::put(self::MFA_CONTRACT_ADDRESS, $address);
  }

  public static function getMfaContractAddress()
  {
    return Cache::get(self::MFA_CONTRACT_ADDRESS);
  }

  public static function setUserEmail($email)
  {
    // Store the user's email in the session
    Session::put(self::USER_EMAIL, $email);
  }

  public static function getUserEmail()
  {
    // Retrieve the user's email from the session
    return Session::get(self::USER_EMAIL);
  }

  public static function setUserOtpGuess($otpGuess)
  {
    Cache::put(self::USER_OTP_GUESS, $otpGuess, now()->addMinutes(self::EXPIRATION_MINS)); // Adjust the expiration time as needed
  }

  public static function getUserOtpGuess()
  {
    return Cache::get(self::USER_OTP_GUESS);
  }

  public static function setOtpValid($valid)
  {
    Cache::put(self::USER_OTP_VALID, $valid, now()->addMinutes(self::EXPIRATION_MINS));
  }

  public static function isOtpValid()
  {
    return Cache::get(self::USER_OTP_VALID);
  }

  public static function setTimestamp($ts)
  {
    Cache::put(self::TIMESTAMP, $ts);
  }

  public static function getTimestamp()
  {
    return Cache::get(self::TIMESTAMP);
  }
}
