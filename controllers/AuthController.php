// AuthController.php
<?php
class AuthController {
    private $db;
    
    public function __construct() {
        require_once 'config/database.php';
        $this->db = new Database();
    }
    
    public function login($username, $password) {
        try {
            $conn = $this->db->getConnection();
            
            // Prepare statement to prevent SQL injection
            $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND status = 'active'");
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user && password_verify($password, $user['password'])) {
                // Start session and store user data
                session_start();
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
                
                // Redirect based on role
                switch($user['role']) {
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
                exit();
            }
            return false;
        } catch(PDOException $e) {
            error_log("Login error: " . $e->getMessage());
            return false;
        }
    }
    
    public function register($userData) {
        try {
            $conn = $this->db->getConnection();
            
            // Hash password
            $userData['password'] = password_hash($userData['password'], PASSWORD_DEFAULT);
            
            // Begin transaction
            $conn->beginTransaction();
            
            // Insert into users table
            $stmt = $conn->prepare("
                INSERT INTO users (username, password, email, role, nama_lengkap, status)
                VALUES (?, ?, ?, ?, ?, 'active')
            ");
            
            $stmt->execute([
                $userData['username'],
                $userData['password'],
                $userData['email'],
                $userData['role'],
                $userData['nama_lengkap']
            ]);
            
            $userId = $conn->lastInsertId();
            
            // Additional data based on role
            switch($userData['role']) {
                case 'siswa':
                    $stmtSiswa = $conn->prepare("
                        INSERT INTO siswa (user_id, nis, kelas_id, tanggal_lahir, jenis_kelamin, alamat)
                        VALUES (?, ?, ?, ?, ?, ?)
                    ");
                    $stmtSiswa->execute([
                        $userId,
                        $userData['nis'],
                        $userData['kelas_id'],
                        $userData['tanggal_lahir'],
                        $userData['jenis_kelamin'],
                        $userData['alamat']
                    ]);
                    break;
                    
                case 'orangtua':
                    if (!empty($userData['siswa_id'])) {
                        $stmtOrangtua = $conn->prepare("
                            INSERT INTO orangtua_siswa (orangtua_id, siswa_id, hubungan)
                            VALUES (?, ?, ?)
                        ");
                        $stmtOrangtua->execute([
                            $userId,
                            $userData['siswa_id'],
                            $userData['hubungan']
                        ]);
                    }
                    break;
            }
            
            $conn->commit();
            return true;
            
        } catch(PDOException $e) {
            $conn->rollBack();
            error_log("Registration error: " . $e->getMessage());
            return false;
        }
    }
    
    public function logout() {
        session_start();
        session_destroy();
        header('Location: /auth/login.php');
        exit();
    }
    
    public function checkAuth() {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: /auth/login.php');
            exit();
        }
        return $_SESSION;
    }
    
    public function checkRole($allowedRoles) {
        $session = $this->checkAuth();
        if (!in_array($session['role'], $allowedRoles)) {
            header('Location: /auth/unauthorized.php');
            exit();
        }
        return $session;
    }
}

