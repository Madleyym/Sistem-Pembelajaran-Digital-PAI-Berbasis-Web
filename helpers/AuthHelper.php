<?php
// /helpers/AuthHelper.php

class AuthHelper
{
    private static $db;

    public static function init()
    {
        self::$db = Database::getInstance();
    }

    /**
     * Validate registration data
     */
    public static function validateRegistration($data)
    {
        $errors = [];

        // Required fields
        $required = ['username', 'email', 'password', 'confirm_password', 'role', 'nama_lengkap'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                $errors[] = ucfirst($field) . ' harus diisi';
            }
        }

        // Username validation
        if (!empty($data['username'])) {
            if (strlen($data['username']) < 4) {
                $errors[] = 'Username minimal 4 karakter';
            }
            if (!preg_match('/^[a-zA-Z0-9_]+$/', $data['username'])) {
                $errors[] = 'Username hanya boleh berisi huruf, angka, dan underscore';
            }
        }

        // Email validation
        if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Format email tidak valid';
        }

        // Password validation
        if (!empty($data['password'])) {
            if (strlen($data['password']) < 6) {
                $errors[] = 'Password minimal 6 karakter';
            }
            if ($data['password'] !== $data['confirm_password']) {
                $errors[] = 'Konfirmasi password tidak cocok';
            }
        }

        // Role validation
        if (!empty($data['role'])) {
            $valid_roles = ['siswa', 'guru', 'orangtua'];
            if (!in_array($data['role'], $valid_roles)) {
                $errors[] = 'Role tidak valid';
            }
        }

        // Role-specific validation
        switch ($data['role']) {
            case 'siswa':
                if (empty($data['nis'])) {
                    $errors[] = 'NIS harus diisi';
                }
                if (empty($data['kelas'])) {
                    $errors[] = 'Kelas harus diisi';
                }
                break;

            case 'guru':
                if (empty($data['nip'])) {
                    $errors[] = 'NIP harus diisi';
                }
                if (empty($data['bidang_studi'])) {
                    $errors[] = 'Bidang studi harus diisi';
                }
                break;

            case 'orangtua':
                if (empty($data['no_hp'])) {
                    $errors[] = 'Nomor HP harus diisi';
                }
                break;
        }

        return $errors;
    }

    /**
     * Check if username exists
     */
    public static function usernameExists($username)
    {
        $user = self::$db->selectOne(
            "SELECT id FROM users WHERE username = ?",
            [$username]
        );
        return !empty($user);
    }

    /**
     * Check if email exists
     */
    public static function emailExists($email)
    {
        $user = self::$db->selectOne(
            "SELECT id FROM users WHERE email = ?",
            [$email]
        );
        return !empty($user);
    }

    /**
     * Get user details with role-specific information
     */
    public static function getUserDetails($userId)
    {
        $user = self::$db->selectOne(
            "SELECT u.*, 
                    CASE 
                        WHEN g.id IS NOT NULL THEN g.nama_lengkap
                        WHEN s.id IS NOT NULL THEN s.nama_lengkap
                        WHEN o.id IS NOT NULL THEN o.nama_lengkap
                        ELSE u.username
                    END as nama_lengkap
             FROM users u
             LEFT JOIN guru g ON u.id = g.user_id
             LEFT JOIN siswa s ON u.id = s.user_id
             LEFT JOIN orangtua o ON u.id = o.user_id
             WHERE u.id = ?",
            [$userId]
        );

        if ($user) {
            // Get role-specific details
            switch ($user['role']) {
                case 'guru':
                    $details = self::$db->selectOne(
                        "SELECT * FROM guru WHERE user_id = ?",
                        [$userId]
                    );
                    break;
                case 'siswa':
                    $details = self::$db->selectOne(
                        "SELECT * FROM siswa WHERE user_id = ?",
                        [$userId]
                    );
                    break;
                case 'orangtua':
                    $details = self::$db->selectOne(
                        "SELECT * FROM orangtua WHERE user_id = ?",
                        [$userId]
                    );
                    break;
                default:
                    $details = null;
            }

            $user['details'] = $details;
        }

        return $user;
    }

    /**
     * Create password reset token
     */
    public static function createPasswordResetToken($email)
    {
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

        self::$db->update(
            'users',
            [
                'reset_token' => $token,
                'reset_expires' => $expires
            ],
            'email = ?',
            [$email]
        );

        return $token;
    }

    /**
     * Verify password reset token
     */
    public static function verifyResetToken($token)
    {
        $user = self::$db->selectOne(
            "SELECT id FROM users 
             WHERE reset_token = ? 
             AND reset_expires > NOW() 
             AND reset_token IS NOT NULL",
            [$token]
        );
        return !empty($user);
    }
}

// Initialize the helper
AuthHelper::init();
