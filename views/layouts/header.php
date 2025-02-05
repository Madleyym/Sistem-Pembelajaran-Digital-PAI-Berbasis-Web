<!-- views/layouts/header.php -->
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Pembelajaran PAI Digital</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

        body {
            font-family: 'Poppins', sans-serif;
        }

        .glass-effect {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }

        .nav-link {
            @apply px-4 py-2 text-gray-600 hover:text-blue-600 transition-all duration-300 relative font-medium;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: 0;
            left: 50%;
            background-color: #2563EB;
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }

        .nav-link:hover::after {
            width: 100%;
        }

        .nav-link.active {
            @apply text-blue-600 font-semibold;
        }

        .nav-link.active::after {
            width: 100%;
        }

        .premium-shadow {
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        }

        .notification-badge {
            @apply absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-xs rounded-full flex items-center justify-center;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.1);
            }

            100% {
                transform: scale(1);
            }
        }

        .mobile-menu {
            @apply fixed top-0 left-0 w-72 h-full bg-white shadow-2xl transform -translate-x-full transition-transform duration-300 ease-in-out z-50;
            background: linear-gradient(to bottom right, #ffffff, #f8fafc);
        }

        .mobile-menu.active {
            @apply translate-x-0;
        }

        .user-menu {
            @apply absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-xl py-2 transform scale-95 opacity-0 invisible transition-all duration-200;
        }

        .user-menu.active {
            @apply scale-100 opacity-100 visible;
        }

        .premium-button {
            @apply px-4 py-2 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg font-medium transition-all duration-300 hover:shadow-lg hover:from-blue-700 hover:to-blue-800;
        }
    </style>
</head>

<body class="min-h-screen flex flex-col bg-gray-50">
    <header class="fixed w-full z-50">
        <nav class="glass-effect premium-shadow">
            <div class="container mx-auto px-4 py-4">
                <div class="flex justify-between items-center">
                    <div class="flex items-center space-x-8">
                        <button class="md:hidden text-gray-600 hover:text-blue-600 transition-colors" id="mobile-menu-button">
                            <i class="fas fa-bars text-2xl"></i>
                        </button>
                        <a href="/" class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-gradient-to-tr from-blue-600 to-blue-400 rounded-lg flex items-center justify-center">
                                <span class="text-white text-xl font-bold">P</span>
                            </div>
                            <span class="text-xl font-bold bg-gradient-to-r from-blue-600 to-blue-400 text-transparent bg-clip-text">PAI Digital</span>
                        </a>

                        <!-- Desktop Menu -->
                        <div class="hidden md:flex items-center space-x-1">
                            <?php if ($_SESSION['role'] === 'admin'): ?>
                                <a href="/admin/dashboard" class="nav-link">Dashboard</a>
                                <a href="/admin/manajemen-guru" class="nav-link">Manajemen Guru</a>
                                <a href="/admin/manajemen-siswa" class="nav-link">Manajemen Siswa</a>
                                <a href="/admin/laporan" class="nav-link">Laporan</a>
                            <?php elseif ($_SESSION['role'] === 'guru'): ?>
                                <a href="/guru/dashboard" class="nav-link">Dashboard</a>
                                <a href="/guru/materi" class="nav-link">Materi</a>
                                <a href="/guru/quiz" class="nav-link">Quiz</a>
                                <a href="/guru/penilaian" class="nav-link">Penilaian</a>
                            <?php elseif ($_SESSION['role'] === 'siswa'): ?>
                                <a href="/siswa/dashboard" class="nav-link">Dashboard</a>
                                <a href="/siswa/materi" class="nav-link">Materi</a>
                                <a href="/siswa/quiz" class="nav-link">Quiz</a>
                                <a href="/siswa/progres" class="nav-link">Progres</a>
                            <?php elseif ($_SESSION['role'] === 'orangtua'): ?>
                                <a href="/orangtua/dashboard" class="nav-link">Dashboard</a>
                                <a href="/orangtua/anak-progress" class="nav-link">Progres Anak</a>
                                <a href="/orangtua/feedback" class="nav-link">Feedback</a>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Right Section -->
                    <div class="flex items-center space-x-6">
                        <!-- Notifications -->
                        <div class="relative">
                            <button class="text-gray-600 hover:text-blue-600 transition-colors">
                                <i class="fas fa-bell text-xl"></i>
                                <span class="notification-badge">3</span>
                            </button>
                        </div>

                        <!-- User Menu -->
                        <div class="relative">
                            <button class="flex items-center space-x-3 focus:outline-none" id="user-menu-button">
                                <div class="relative">
                                    <img src="/assets/images/avatar.png" alt="Avatar" class="h-10 w-10 rounded-full object-cover border-2 border-blue-500">
                                    <div class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 rounded-full border-2 border-white"></div>
                                </div>
                                <div class="hidden md:block text-left">
                                    <p class="text-sm font-semibold text-gray-700"><?php echo $_SESSION['nama_lengkap']; ?></p>
                                    <p class="text-xs text-gray-500 capitalize"><?php echo $_SESSION['role']; ?></p>
                                </div>
                                <i class="fas fa-chevron-down text-xs text-gray-500"></i>
                            </button>

                            <div class="user-menu" id="user-menu">
                                <div class="px-4 py-3 border-b">
                                    <p class="text-sm font-semibold text-gray-700"><?php echo $_SESSION['nama_lengkap']; ?></p>
                                    <p class="text-xs text-gray-500"><?php echo $_SESSION['email']; ?></p>
                                </div>
                                <a href="/profil" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-user w-5"></i>
                                    <span>Profil Saya</span>
                                </a>
                                <a href="/pengaturan" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-cog w-5"></i>
                                    <span>Pengaturan</span>
                                </a>
                                <div class="border-t">
                                    <a href="/logout" class="flex items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                        <i class="fas fa-sign-out-alt w-5"></i>
                                        <span>Keluar</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Mobile Menu -->
        <div class="mobile-menu" id="mobile-menu">
            <div class="p-6">
                <div class="flex items-center justify-between mb-8">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-gradient-to-tr from-blue-600 to-blue-400 rounded-lg flex items-center justify-center">
                            <span class="text-white text-xl font-bold">P</span>
                        </div>
                        <span class="text-xl font-bold">PAI Digital</span>
                    </div>
                    <button class="text-gray-500 hover:text-gray-700" id="mobile-menu-close">
                        <i class="fas fa-times text-2xl"></i>
                    </button>
                </div>

                <div class="space-y-2">
                    <?php if ($_SESSION['role'] === 'admin'): ?>
                        <a href="/admin/dashboard" class="block px-4 py-3 rounded-lg hover:bg-blue-50 text-gray-700 hover:text-blue-600 transition-colors">
                            <i class="fas fa-home w-6"></i> Dashboard
                        </a>
                        <a href="/admin/manajemen-guru" class="block px-4 py-3 rounded-lg hover:bg-blue-50 text-gray-700 hover:text-blue-600 transition-colors">
                            <i class="fas fa-chalkboard-teacher w-6"></i> Manajemen Guru
                        </a>
                        <a href="/admin/manajemen-siswa" class="block px-4 py-3 rounded-lg hover:bg-blue-50 text-gray-700 hover:text-blue-600 transition-colors">
                            <i class="fas fa-user-graduate w-6"></i> Manajemen Siswa
                        </a>
                        <a href="/admin/laporan" class="block px-4 py-3 rounded-lg hover:bg-blue-50 text-gray-700 hover:text-blue-600 transition-colors">
                            <i class="fas fa-chart-bar w-6"></i> Laporan
                        </a>
                    <?php endif; ?>
                    <!-- Similar blocks for other roles -->
                </div>

                <div class="mt-8 pt-8 border-t">
                    <a href="/bantuan" class="block px-4 py-3 rounded-lg hover:bg-blue-50 text-gray-700 hover:text-blue-600 transition-colors">
                        <i class="fas fa-question-circle w-6"></i> Bantuan
                    </a>
                    <a href="/logout" class="block px-4 py-3 rounded-lg hover:bg-red-50 text-red-600 transition-colors">
                        <i class="fas fa-sign-out-alt w-6"></i> Keluar
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Spacer for fixed header -->
    <div class="h-20"></div>

    <main class="flex-grow container mx-auto px-4 py-8">