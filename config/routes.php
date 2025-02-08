<?php
// /opt/lampp/htdocs/PAI/config/routes.php

class Router
{
    private static $routes = [];
    private static $baseUrl = '/PAI';

    // Define routes and their handlers
    public static function init()
    {
        // Auth routes
        self::$routes = [
            // Auth routes
            'auth/login' => [
                'controller' => 'AuthController',
                'action' => 'login',
                'auth' => false
            ],
            'auth/register' => [
                'controller' => 'AuthController',
                'action' => 'register',
                'auth' => false
            ],
            'auth/logout' => [
                'controller' => 'AuthController',
                'action' => 'logout',
                'auth' => true
            ],

            // Admin routes
            'admin/dashboard' => [
                'controller' => 'AdminController',
                'action' => 'dashboard',
                'auth' => true,
                'role' => 'admin'
            ],
            'admin/manajemen-guru' => [
                'controller' => 'AdminController',
                'action' => 'manajemenGuru',
                'auth' => true,
                'role' => 'admin'
            ],
            'admin/manajemen-siswa' => [
                'controller' => 'AdminController',
                'action' => 'manajemenSiswa',
                'auth' => true,
                'role' => 'admin'
            ],
            'admin/laporan' => [
                'controller' => 'AdminController',
                'action' => 'laporan',
                'auth' => true,
                'role' => 'admin'
            ],

            // Guru routes
            'guru/dashboard' => [
                'controller' => 'GuruController',
                'action' => 'dashboard',
                'auth' => true,
                'role' => 'guru'
            ],
            'guru/materi' => [
                'controller' => 'MateriController',
                'action' => 'index',
                'auth' => true,
                'role' => 'guru'
            ],
            'guru/materi/create' => [
                'controller' => 'MateriController',
                'action' => 'create',
                'auth' => true,
                'role' => 'guru'
            ],
            'guru/quiz' => [
                'controller' => 'QuizController',
                'action' => 'index',
                'auth' => true,
                'role' => 'guru'
            ],
            'guru/penilaian' => [
                'controller' => 'GuruController',
                'action' => 'penilaian',
                'auth' => true,
                'role' => 'guru'
            ],

            // Siswa routes
            'siswa/dashboard' => [
                'controller' => 'SiswaController',
                'action' => 'dashboard',
                'auth' => true,
                'role' => 'siswa'
            ],
            'siswa/materi' => [
                'controller' => 'MateriController',
                'action' => 'listMateriSiswa',
                'auth' => true,
                'role' => 'siswa'
            ],
            'siswa/quiz' => [
                'controller' => 'QuizController',
                'action' => 'listQuizSiswa',
                'auth' => true,
                'role' => 'siswa'
            ],
            'siswa/progres-belajar' => [
                'controller' => 'SiswaController',
                'action' => 'progresBelajar',
                'auth' => true,
                'role' => 'siswa'
            ],

            // Orang Tua routes
            'orangtua/dashboard' => [
                'controller' => 'OrangTuaController',
                'action' => 'dashboard',
                'auth' => true,
                'role' => 'orangtua'
            ],
            'orangtua/anak-progress' => [
                'controller' => 'OrangTuaController',
                'action' => 'anakProgress',
                'auth' => true,
                'role' => 'orangtua'
            ],
            'orangtua/feedback' => [
                'controller' => 'OrangTuaController',
                'action' => 'feedback',
                'auth' => true,
                'role' => 'orangtua'
            ],
            'orangtua/notifications' => [
                'controller' => 'NotifikasiController',
                'action' => 'index',
                'auth' => true,
                'role' => 'orangtua'
            ]
        ];
    }

    // Get route information
    public static function getRoute($path)
    {
        // Remove trailing slashes and convert to lowercase
        $path = strtolower(trim($path, '/'));

        // Initialize routes if not already done
        if (empty(self::$routes)) {
            self::init();
        }

        // Check if route exists
        if (isset(self::$routes[$path])) {
            return self::$routes[$path];
        }

        // Handle dynamic routes (e.g., materi/view/1, quiz/attempt/2)
        foreach (self::$routes as $route => $config) {
            $pattern = preg_replace('/\{([a-zA-Z]+)\}/', '([^/]+)', $route);
            $pattern = str_replace('/', '\/', $pattern);
            if (preg_match('/^' . $pattern . '$/', $path, $matches)) {
                array_shift($matches);
                $config['params'] = $matches;
                return $config;
            }
        }

        return null;
    }

    // Get base URL
    public static function getBaseUrl()
    {
        return self::$baseUrl;
    }

    // Generate URL
    public static function url($path = '')
    {
        return self::$baseUrl . '/' . ltrim($path, '/');
    }

    // Check if current user has access to route
    public static function hasAccess($route)
    {
        if (!isset($route['auth']) || !$route['auth']) {
            return true;
        }

        if (!isset($_SESSION['user_id'])) {
            return false;
        }

        if (isset($route['role']) && $_SESSION['role'] !== $route['role']) {
            return false;
        }

        return true;
    }

    // Load and execute route
    public static function load($path)
    {
        $route = self::getRoute($path);

        if (!$route) {
            http_response_code(404);
            require_once __DIR__ . '/../views/errors/404.php';
            return;
        }

        if (!self::hasAccess($route)) {
            if (!isset($_SESSION['user_id'])) {
                header('Location: ' . self::url('auth/login'));
                exit;
            }
            http_response_code(403);
            require_once __DIR__ . '/../views/errors/403.php';
            return;
        }

        $controllerName = $route['controller'];
        $actionName = $route['action'];
        $controllerFile = __DIR__ . "/../controllers/{$controllerName}.php";

        if (!file_exists($controllerFile)) {
            throw new Exception("Controller file not found: {$controllerFile}");
        }

        require_once $controllerFile;
        $controller = new $controllerName();

        if (!method_exists($controller, $actionName)) {
            throw new Exception("Action {$actionName} not found in controller {$controllerName}");
        }

        $params = isset($route['params']) ? $route['params'] : [];
        return call_user_func_array([$controller, $actionName], $params);
    }
}

// Initialize routes
Router::init();
