<?php
// /auth/login.php
session_start();
require_once __DIR__ . '/../config/database.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    $role = $_SESSION['role'];
    header("Location: /$role/dashboard");
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    try {
        $db = Database::getInstance();
        $user = $db->selectOne(
            "SELECT id, username, password, role, status 
             FROM users 
             WHERE username = ?",
            [$username]
        );

        if ($user && password_verify($password, $user['password'])) {
            if ($user['status'] !== 'active') {
                $error = 'Akun Anda belum diaktifkan. Silakan hubungi administrator.';
            } else {
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];

                // Redirect based on role
                switch ($user['role']) {
                    case 'admin':
                        header('Location: /admin/dashboard');
                        break;
                    case 'guru':
                        header('Location: /guru/dashboard');
                        break;
                    case 'siswa':
                        header('Location: /siswa/dashboard');
                        break;
                    case 'orangtua':
                        header('Location: /orangtua/dashboard');
                        break;
                }
                exit();
            }
        } else {
            $error = 'Username atau password salah';
        }
    } catch (Exception $e) {
        $error = 'Terjadi kesalahan sistem';
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Pembelajaran PAI</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center">
        <div class="max-w-md w-full space-y-8 p-8 bg-white rounded-lg shadow-md">
            <div>
                <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                    Login
                </h2>
                <p class="mt-2 text-center text-sm text-gray-600">
                    Sistem Pembelajaran PAI
                </p>
            </div>

            <?php if ($error): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline"><?php echo htmlspecialchars($error); ?></span>
                </div>
            <?php endif; ?>

            <form class="mt-8 space-y-6" method="POST">
                <div class="rounded-md shadow-sm -space-y-px">
                    <div>
                        <label for="username" class="sr-only">Username</label>
                        <input id="username" name="username" type="text" required
                            class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
                            placeholder="Username">
                    </div>
                    <div>
                        <label for="password" class="sr-only">Password</label>
                        <input id="password" name="password" type="password" required
                            class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
                            placeholder="Password">
                    </div>
                </div>

                <div>
                    <button type="submit" class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Login
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>

<?php
// /controllers/AuthController.php
class AuthController
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';

            try {
                $user = $this->db->selectOne(
                    "SELECT id, username, password, role, status 
                     FROM users 
                     WHERE username = ?",
                    [$username]
                );

                if ($user && password_verify($password, $user['password'])) {
                    if ($user['status'] !== 'active') {
                        return ['error' => 'Akun Anda belum diaktifkan'];
                    }

                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['role'] = $user['role'];

                    return [
                        'success' => true,
                        'redirect' => "/{$user['role']}/dashboard"
                    ];
                }

                return ['error' => 'Username atau password salah'];
            } catch (Exception $e) {
                return ['error' => 'Terjadi kesalahan sistem'];
            }
        }
    }

    public function logout()
    {
        session_destroy();
        header('Location: /auth/login');
        exit();
    }
}
