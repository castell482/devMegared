<?php

require_once __DIR__ . '/../repositories/SessionRepository.php';

class SessionService
{
    private static function setCookie($token, $expiry_time)
    {
        $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443;
        $httpOnly = true;

        if (!headers_sent()) {
            setcookie(
                'session_token',
                $token,
                [
                    'expires' => $expiry_time,
                    'path' => '/',
                    'secure' => $secure,
                    'httponly' => $httpOnly,
                    'samesite' => 'Lax'
                ]
            );

            $_COOKIE['session_token'] = $token;
            return true;
        }

        return false;
    }

    private static function extend($session_id)
    {
        $expiry_time = time() + 3600;
        SessionRepository::extend($session_id, $expiry_time);
        if (isset($_COOKIE['session_token'])) static::setCookie($_COOKIE['session_token'], $expiry_time);
    }

    private static function invalidate($session_id)
    {
        SessionRepository::invalidate($session_id);
        if (isset($_COOKIE['session_token'])) setcookie('session_token', '', time() - 3600, '/');
    }

    private static function invalidatePrevious($user_id)
    {
        $sessions = SessionRepository::isActive($user_id);
        return $sessions ? static::invalidate($sessions->id) : false;
    }

    public static function create($user_id)
    {
        static::invalidatePrevious($user_id);
        $session = SessionRepository::create($user_id);
        if ($session) static::setCookie($session->session_token, $session->expires_at);
        return $session;
    }

    public static function findByToken($token)
    {
        return SessionRepository::findByToken($token);
    }

    public static function validate($token)
    {
        $session = SessionRepository::findByToken($token);

        if (!$session) return false;

        $now = new DateTime();
        $expires = new DateTime($session->expires_at);

        if ($now > $expires) {
            static::invalidate($session->id);
            return false;
        }

        $ip = $_SERVER['REMOTE_ADDR'] ?? null;
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? null;

        if ($session->ip_address !== $ip || $session->user_agent !== $user_agent) {
            static::invalidate($session->id);
            return false;
        }

        static::extend($session->id);

        return $session;
    }

    public static function getCurrent()
    {
        if (isset($_COOKIE['session_token'])) return static::validate($_COOKIE['session_token']);
        return false;
    }

    public static function remove()
    {
        if (isset($_COOKIE['session_token'])) {
            $session = SessionRepository::findByToken($_COOKIE['session_token']);
            if ($session) static::invalidate($session->id);
        }
    }
}
