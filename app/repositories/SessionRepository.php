<?php

require_once __DIR__ . '/../models/Session.php';

class SessionRepository
{
    public static function isActive($user_id)
    {
        return Session::findByCondition(['user_id' => $user_id, 'is_active' => true], true);
    }

    public static function create($user_id)
    {
        $ip = filter_var($_SERVER['REMOTE_ADDR'] ?? '', FILTER_VALIDATE_IP) ?: 'unknown';
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
        $current_time = time();
        $expiry_time = $current_time + 3600;

        $token = bin2hex(random_bytes(32));

        return Session::create([
            "user_id" => $user_id,
            "session_token" => $token,
            "is_active" => 1,
            "ip_address" => $ip,
            "user_agent" => $user_agent,
            "created_at" => date('Y-m-d H:i:s', $current_time),
            "expires_at" => date('Y-m-d H:i:s', $expiry_time)
        ]);
    }

    public static function findByToken($token)
    {
        return Session::findByCondition(['session_token' => $token], true);
    }

    public static function extend($id, $expiry_time)
    {
        return Session::update(
            $id,
            [
                "expires_at" => date('Y-m-d H:i:s', $expiry_time)
            ]
        );
    }

    public static function invalidate($session_id)
    {
        return Session::update(
            $session_id,
            ["is_active" => 0]
        );
    }
}
