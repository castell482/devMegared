<?php

class EnvLoader
{
    private static $loaded = false;

    public static function load($path)
    {
        if (self::$loaded) return;
        if (!file_exists($path)) {
            throw new Exception(".env file not found!");
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) continue;
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);

            if (!array_key_exists($key, $_SERVER) && !array_key_exists($key, $_ENV)) {
                putenv("$key=$value");
                $_ENV[$key] = $value;
                $_SERVER[$key] = $value;
            }
        }

        self::$loaded = true;
    }

    public static function env($key, $default = null)
    {
        if (!self::$loaded) {
            self::load(__DIR__ . '/../../.env');
        }

        return getenv($key) ?: $_ENV[$key] ?? $_SERVER[$key] ?? $default;
    }
}
