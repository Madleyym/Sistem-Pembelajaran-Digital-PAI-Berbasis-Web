<?php
// /auth/register.php
session_start();
require_once __DIR__ . '/../config/database.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    $role = $_SESSION['role'];
    header("Location: /$role/dashboard");
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = Database::getInstance();

    try {
        // Start transaction
        $db->beginTransaction();

        // Validate input
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        $role = $_POST['role'] ?? '';
        $email = trim($_POST['email'] ?? '');
        $nama_lengkap = trim($_POST['nama_lengkap'] ?? '');

        // Basic validation
        if (empty($username) || empty($password) || empty($confirm_password) || empty($role) || empty($email) || empty($nama_lengkap)) {
            throw new Exception('Semua field harus diisi');
        }

        if ($password !== $confirm_password) {
            throw new Exception('Password tidak cocok');
        }

        if (strlen($password) < 6) {
            throw new Exception('Password minimal 6 karakter');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Format email tidak valid');
        }

        // Validate allowed roles
        $allowed_roles = ['siswa', 'orangtua', 'guru'];
        if (!in_array($role, $allowed_roles)) {
            throw new Exception('Role tidak valid');
        }

        // Check if username already exists
        $existing_user = $db->selectOne(
            "SELECT id FROM users WHERE username = ?",
            [$username]
        );

        if ($existing_user) {
            throw new Exception('Username sudah digunakan');
        }

        // Check if email already exists
        $existing_email = $db->selectOne(
            "SELECT id FROM users WHERE email = ?",
            [$email]
        );

        if ($existing_email) {
            throw new Exception('Email sudah digunakan');
        }

        // Insert into users table
        $user_id = $db->insert('users', [
            'username' => $username,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'email' => $email,
            'role' => $role,
            'status' => $role === 'guru' ? 'inactive' : 'active', // Guru perlu approval admin
            'created_at' => date('Y-m-d H:i:s')
        ]);

        // Insert role-specific data
        switch ($role) {
            case 'siswa':
                $db->insert('siswa', [
                    'user_id' => $user_id,
                    'nama_lengkap' => $nama_lengkap,
                    'nis' => $_POST['nis'] ?? null,
                    'kelas' => $_POST['kelas'] ?? null,
                    'tanggal_lahir' => $_POST['tanggal_lahir'] ?? null
                ]);
                break;

            case 'guru':
                $db->insert('guru', [
                    'user_id' => $user_id,
                    'nama_lengkap' => $nama_lengkap,
                    'nip' => $_POST['nip'] ?? null,
                    'bidang_studi' => $_POST['bidang_studi'] ?? null
                ]);
                break;

            case 'orangtua':
                $db->insert('orangtua', [
                    'user_id' => $user_id,
                    'nama_lengkap' => $nama_lengkap,
                    'no_hp' => $_POST['no_hp'] ?? null
                ]);
                break;
        }

        $db->commit();
        $success = 'Registrasi berhasil! Silahkan login.';

        if ($role === 'guru') {
            $success .= ' Akun Anda akan diaktifkan setelah diverifikasi oleh admin.';
        } else {
            $success .= ' Silahkan login untuk melanjutkan.';
        }
    } catch (Exception $e) {
        $db->rollback();
        $error = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Sistem Pembelajaran PAI</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8 bg-white p-8 rounded-lg shadow-md">
            <div>
                <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                    Daftar Akun Baru
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

            <?php if ($success): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline"><?php echo htmlspecialchars($success); ?></span>
                    <div class="mt-2">
                        <a href="/auth/login" class="text-green-700 underline">Klik disini untuk login</a>
                    </div>
                </div>
            <?php endif; ?>

            <form class="mt-8 space-y-6" method="POST" id="registerForm">
                <div class="rounded-md shadow-sm space-y-4">
                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                        <input id="username" name="username" type="text" required
                            class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input id="email" name="email" type="email" required
                            class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>

                    <div>
                        <label for="nama_lengkap" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                        <input id="nama_lengkap" name="nama_lengkap" type="text" required
                            class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>

                    <div>
                        <label for="role" class="block text-sm font-medium text-gray-700">Daftar Sebagai</label>
                        <select id="role" name="role" required
                            class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="">Pilih Role</option>
                            <option value="siswa">Siswa</option>
                            <option value="guru">Guru</option>
                            <option value="orangtua">Orang Tua</option>
                        </select>
                    </div>

                    <!-- Dynamic fields based on role -->
                    <div id="siswaFields" class="hidden">
                        <div class="space-y-4">
                            <div>
                                <label for="nis" class="block text-sm font-medium text-gray-700">NIS</label>
                                <input id="nis" name="nis" type="text"
                                    class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            </div>
                            <div>
                                <label for="kelas" class="block text-sm font-medium text-gray-700">Kelas</label>
                                <input id="kelas" name="kelas" type="text"
                                    class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            </div>
                            <div>
                                <label for="tanggal_lahir" class="block text-sm font-medium text-gray-700">Tanggal Lahir</label>
                                <input id="tanggal_lahir" name="tanggal_lahir" type="date"
                                    class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            </div>
                        </div>
                    </div>

                    <div id="guruFields" class="hidden">
                        <div class="space-y-4">
                            <div>
                                <label for="nip" class="block text-sm font-medium text-gray-700">NIP</label>
                                <input id="nip" name="nip" type="text"
                                    class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            </div>
                            <div>
                                <label for="bidang_studi" class="block text-sm font-medium text-gray-700">Bidang Studi</label>
                                <input id="bidang_studi" name="bidang_studi" type="text"
                                    class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            </div>
                        </div>
                    </div>

                    <div id="orangtuaFields" class="hidden">
                        <div>
                            <label for="no_hp" class="block text-sm font-medium text-gray-700">Nomor HP</label>
                            <input id="no_hp" name="no_hp" type="text"
                                class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                        <input id="password" name="password" type="password" required
                            class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>

                    <div>
                        <label for="confirm_password" class="block text-sm font-medium text-gray-700">Konfirmasi Password</label>
                        <input id="confirm_password" name="confirm_password" type="password" required
                            class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>
                </div>

                <div>
                    <button type="submit" class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Daftar
                    </button>
                </div>

                <div class="text-center">
                    <a href="/auth/login" class="text-sm text-indigo-600 hover:text-indigo-500">
                        Sudah punya akun? Login disini
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('role').addEventListener('change', function() {
            // Hide all role-specific fields
            document.getElementById('siswaFields').classList.add('hidden');
            document.getElementById('guruFields').classList.add('hidden');
            document.getElementById('orangtuaFields').classList.add('hidden');

            // Show fields based on selected role
            const selectedRole = this.value;
            if (selectedRole) {
                document.getElementById(selectedRole + 'Fields').classList.remove('hidden');
            }
        });
    </script>
</body>

</html>