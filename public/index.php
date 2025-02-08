<!-- /opt/lampp/htdocs/PAI/public/index.php -->

<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/routes.php';
require_once __DIR__ . '/../helpers/utils.php';

// Error reporting for development
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database connection
try {
    $db = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
        DB_USER,
        DB_PASSWORD
    );
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Function to check if user is logged in
function isLoggedIn()
{
    return isset($_SESSION['user_id']) && isset($_SESSION['role']);
}

// Function to check user role
function hasRole($role)
{
    return isset($_SESSION['role']) && $_SESSION['role'] === $role;
}

// Function to get current page name for active menu highlighting
function getCurrentPage()
{
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    return trim($path, '/');
}

// Function to check if menu item is active
function isActiveMenu($path)
{
    $currentPage = getCurrentPage();
    return strpos($currentPage, $path) === 0 ? 'active' : '';
}

// Get unread notifications count
function getUnreadNotificationsCount($userId)
{
    global $db;
    $stmt = $db->prepare("SELECT COUNT(*) FROM notifikasi WHERE user_id = ? AND dibaca = FALSE");
    $stmt->execute([$userId]);
    return $stmt->fetchColumn();
}

// Redirect if not logged in
if (!isLoggedIn() && !in_array(getCurrentPage(), ['auth/login', 'auth/register'])) {
    header('Location: /auth/login.php');
    exit();
}

// Basic routing
$request = $_SERVER['REQUEST_URI'];
$basePath = '/pai'; // Sesuaikan dengan base path aplikasi Anda
$request = str_replace($basePath, '', $request);

// Start output buffering
ob_start();

// Include header for logged-in users
if (isLoggedIn()) {
    // Get notifications count for the current user
    $notificationCount = getUnreadNotificationsCount($_SESSION['user_id']);
    include '../views/layouts/header.php';
}

// Route to appropriate file
$path = trim(parse_url($request, PHP_URL_PATH), '/');

$routes = [
    'auth/login' => '../auth/login.php',
    'auth/register' => '../auth/register.php',
    'auth/logout' => '../auth/logout.php',

    // Admin routes
    'admin/dashboard' => '../views/admin/dashboard.php',
    'admin/manajemen-guru' => '../views/admin/manajemen_guru.php',
    'admin/manajemen-siswa' => '../views/admin/manajemen_siswa.php',
    'admin/laporan' => '../views/admin/laporan.php',

    // Guru routes
    'guru/dashboard' => '../views/guru/dashboard.php',
    'guru/materi' => '../views/guru/materi.php',
    'guru/quiz' => '../views/guru/quiz.php',
    'guru/penilaian' => '../views/guru/penilaian.php',

    // Siswa routes
    'siswa/dashboard' => '../views/siswa/dashboard.php',
    'siswa/materi' => '../views/siswa/materi.php',
    'siswa/quiz' => '../views/siswa/quiz.php',
    'siswa/progres-belajar' => '../views/siswa/progres_belajar.php',

    // Orang Tua routes
    'orangtua/dashboard' => '../views/orangtua/dashboard.php',
    'orangtua/anak-progress' => '../views/orangtua/anak_progress.php',
    'orangtua/feedback' => '../views/orangtua/feedback.php',
    'orangtua/notifications' => '../views/orangtua/notifications.php'
];

if (array_key_exists($path, $routes)) {
    include $routes[$path];
} else {
    // Handle 404
    http_response_code(404);
    include '../views/errors/404.php';
}

// Include footer for logged-in users
if (isLoggedIn()) {
    include '../views/layouts/footer.php';
}

// Add JavaScript for interactive elements
if (isLoggedIn()):
?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Mobile menu functionality
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            const mobileMenuClose = document.getElementById('mobile-menu-close');
            const mobileMenu = document.getElementById('mobile-menu');

            mobileMenuButton.addEventListener('click', () => {
                mobileMenu.classList.add('active');
            });

            mobileMenuClose.addEventListener('click', () => {
                mobileMenu.classList.remove('active');
            });

            // User menu dropdown functionality
            const userMenuButton = document.getElementById('user-menu-button');
            const userMenu = document.getElementById('user-menu');
            let userMenuOpen = false;

            userMenuButton.addEventListener('click', () => {
                userMenuOpen = !userMenuOpen;
                if (userMenuOpen) {
                    userMenu.classList.add('active');
                } else {
                    userMenu.classList.remove('active');
                }
            });

            // Close user menu when clicking outside
            document.addEventListener('click', (event) => {
                if (!userMenuButton.contains(event.target) && !userMenu.contains(event.target)) {
                    userMenu.classList.remove('active');
                    userMenuOpen = false;
                }
            });

            // Close mobile menu when clicking outside
            document.addEventListener('click', (event) => {
                if (!mobileMenuButton.contains(event.target) &&
                    !mobileMenu.contains(event.target) &&
                    mobileMenu.classList.contains('active')) {
                    mobileMenu.classList.remove('active');
                }
            });

            // Add active class to current page in navigation
            const currentPath = window.location.pathname;
            document.querySelectorAll('.nav-link').forEach(link => {
                if (link.getAttribute('href') === currentPath) {
                    link.classList.add('active');
                }
            });
        });
    </script>
<?php
endif;

// End output buffering and send to browser
ob_end_flush();
?>