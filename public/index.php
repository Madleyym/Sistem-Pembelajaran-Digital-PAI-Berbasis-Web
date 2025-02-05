// public/index.php
<?php
session_start();

// Load configuration
require_once '../config/database.php';
require_once '../config/routes.php';

// Load controllers
require_once '../controllers/AuthController.php';
require_once '../controllers/AdminController.php';
require_once '../controllers/GuruController.php';
require_once '../controllers/SiswaController.php';
require_once '../controllers/OrangTuaController.php';
require_once '../controllers/MateriController.php';
require_once '../controllers/QuizController.php';
require_once '../controllers/NotifikasiController.php';

// Load helpers
require_once '../helpers/utils.php';

// Initialize Auth Controller
$auth = new AuthController();

// Basic routing
$request = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

// Remove query strings
$request = strtok($request, '?');

// Define routes
switch ($request) {
    case '/':
        if (isset($_SESSION['user_id'])) {
            switch ($_SESSION['role']) {
                case 'admin':
                    header('Location: /admin/dashboard.php');
                    break;
                case 'guru':
                    header('Location: /guru/dashboard.php');
                    break;
                case 'siswa':
                    header('Location: /siswa/dashboard.php');
                    break;
                case 'orangtua':
                    header('Location: /orangtua/dashboard.php');
                    break;
            }
        } else {
            header('Location: /auth/login.php');
        }
        break;

    case '/auth/login':
        if ($method === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';

            if ($auth->login($username, $password)) {
                // Redirect handled in login method
            } else {
                $_SESSION['error'] = 'Username atau password salah';
                header('Location: /auth/login.php');
            }
        } else {
            require '../views/auth/login.php';
        }
        break;

    case '/auth/register':
        if ($method === 'POST') {
            $userData = [
                'username' => $_POST['username'],
                'password' => $_POST['password'],
                'email' => $_POST['email'],
                'role' => $_POST['role'],
                'nama_lengkap' => $_POST['nama_lengkap']
            ];

            // Add role-specific data
            if ($_POST['role'] === 'siswa') {
                $userData += [
                    'nis' => $_POST['nis'],
                    'kelas_id' => $_POST['kelas_id'],
                    'tanggal_lahir' => $_POST['tanggal_lahir'],
                    'jenis_kelamin' => $_POST['jenis_kelamin'],
                    'alamat' => $_POST['alamat']
                ];
            } elseif ($_POST['role'] === 'orangtua') {
                $userData += [
                    'siswa_id' => $_POST['siswa_id'],
                    'hubungan' => $_POST['hubungan']
                ];
            }

            if ($auth->register($userData)) {
                $_SESSION['success'] = 'Registrasi berhasil. Silakan login.';
                header('Location: /auth/login.php');
            } else {
                $_SESSION['error'] = 'Registrasi gagal. Silakan coba lagi.';
                header('Location: /auth/register.php');
            }
        } else {
            require '../views/auth/register.php';
        }
        break;

    case '/auth/logout':
        $auth->logout();
        break;

    default:
        http_response_code(404);
        require '../views/404.php';
        break;
}
