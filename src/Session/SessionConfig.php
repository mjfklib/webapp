<?php

declare(strict_types=1);

namespace mjfklib\WebApp\Session;

use mjfklib\Container\ArrayValue;
use mjfklib\Container\Env;

class SessionConfig
{
    public const SESSION_USE_STRICT_MODE = 'SESSION_USE_STRICT_MODE';
    public const SESSION_COOKIE_HTTPONLY = 'SESSION_COOKIE_HTTPONLY';
    public const SESSION_COOKIE_SECURE = 'SESSION_COOKIE_SECURE';
    public const SESSION_COOKIE_SAMESITE = 'SESSION_COOKIE_SAMESITE';
    public const SESSION_COOKIE_LIFETIME = 'SESSION_COOKIE_LIFETIME';
    public const SESSION_GC_MAXLIFETIME = 'SESSION_GC_MAXLIFETIME';
    public const SESSION_SID_LENGTH = 'SESSION_SID_LENGTH';
    public const SESSION_SID_BITS_PER_CHARACTER = 'SESSION_SID_BITS_PER_CHARACTER';


    /**
     * @param Env $env
     * @return self
     */
    public static function create(Env $env): self
    {
        $values = $env->getArrayCopy();
        return new self(
            useStrictMode: ArrayValue::getIntNull($values, static::SESSION_USE_STRICT_MODE) ?? 1,
            cookieHttpOnly: ArrayValue::getIntNull($values, static::SESSION_COOKIE_HTTPONLY) ?? 1,
            cookieSecure: ArrayValue::getIntNull($values, static::SESSION_COOKIE_SECURE) ?? 0,
            cookieSameSite: ArrayValue::getStringNull($values, static::SESSION_COOKIE_SAMESITE) ?? 'Lax',
            cookieLifetime: ArrayValue::getIntNull($values, static::SESSION_COOKIE_LIFETIME) ?? 7200,
            gcMaxLifetime: ArrayValue::getIntNull($values, static::SESSION_GC_MAXLIFETIME) ?? 7200,
            sidLength: ArrayValue::getIntNull($values, static::SESSION_SID_LENGTH) ?? 48,
            sidBitsPerCharacter: ArrayValue::getIntNull($values, static::SESSION_SID_BITS_PER_CHARACTER) ?? 5
        );
    }


    /**
     * @param int $useStrictMode
     * @param int $cookieHttpOnly
     * @param int $cookieSecure
     * @param string $cookieSameSite
     * @param int $cookieLifetime
     * @param int $gcMaxLifetime
     * @param int $sidLength
     * @param int $sidBitsPerCharacter
     */
    public function __construct(
        public int $useStrictMode = 1,
        public int $cookieHttpOnly = 1,
        public int $cookieSecure = 0,
        public string $cookieSameSite = 'Lax',
        public int $cookieLifetime = 7200,
        public int $gcMaxLifetime = 7200,
        public int $sidLength = 48,
        public int $sidBitsPerCharacter = 5
    ) {
    }


    /**
     * @return mixed[]
     */
    public function getOptions(): array
    {
        return [
            'use_strict_mode' => $this->useStrictMode,
            'cookie_httponly' => $this->cookieHttpOnly,
            'cookie_secure' => $this->cookieSecure,
            'cookie_samesite' => $this->cookieSameSite,
            'cookie_lifetime' => $this->cookieLifetime,
            'gc_maxlifetime' => $this->gcMaxLifetime,
            'sid_length' => $this->sidLength,
            'sid_bits_per_character' => $this->sidBitsPerCharacter,
        ];
    }
}
