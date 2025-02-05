<!-- views/admin/dashboard.php -->
<?php
// Pastikan user sudah login dan role-nya admin
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /login');
    exit;
}

require_once '../config/database.php';

// Query untuk mengambil statistik
$stats = [
    'total_guru' => $db->query("SELECT COUNT(*) as total FROM users WHERE role = 'guru'")->fetch_assoc()['total'],
    'total_siswa' => $db->query("SELECT COUNT(*) as total FROM users WHERE role = 'siswa'")->fetch_assoc()['total'],
    'total_kelas' => $db->query("SELECT COUNT(*) as total FROM kelas")->fetch_assoc()['total'],
    'total_materi' => $db->query("SELECT COUNT(*) as total FROM materi")->fetch_assoc()['total']
];

// Query untuk siswa terbaru
$latest_students = $db->query("
    SELECT u.nama_lengkap, s.nis, k.nama_kelas, k.tingkat
    FROM users u 
    JOIN siswa s ON u.id = s.user_id
    JOIN kelas k ON s.kelas_id = k.id
    WHERE u.role = 'siswa'
    ORDER BY u.created_at DESC 
    LIMIT 5
");

// Query untuk materi terbaru
$latest_materials = $db->query("
    SELECT m.judul, m.kategori, m.tingkat, u.nama_lengkap as created_by
    FROM materi m
    JOIN users u ON m.created_by = u.id
    ORDER BY m.created_at DESC
    LIMIT 5
");

// Query untuk statistik quiz
$quiz_stats = $db->query("
    SELECT 
        q.judul,
        COUNT(DISTINCT hq.siswa_id) as total_peserta,
        AVG(hq.nilai) as rata_nilai
    FROM quiz q
    LEFT JOIN hasil_quiz hq ON q.id = hq.quiz_id
    GROUP BY q.id
    ORDER BY q.created_at DESC
    LIMIT 5
");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - PAI Digital</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.0/chart.min.js"></script>
</head>

<body class="bg-gray-50">
    <?php include '../views/layouts/header.php'; ?>

    <div class="container mx-auto px-4 py-8">
        <!-- Welcome Section -->
        <div class="bg-gradient-to-r from-blue-600 to-blue-800 rounded-2xl p-8 mb-8 text-white">
            <h1 class="text-3xl font-bold mb-2">Selamat Datang, <?php echo $_SESSION['nama_lengkap']; ?>!</h1>
            <p class="opacity-90">Kelola sistem pembelajaran PAI Digital dengan mudah dan efektif.</p>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Guru -->
            <div class="bg-white rounded-xl shadow-sm p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Total Guru</p>
                        <h3 class="text-2xl font-bold text-gray-800"><?php echo $stats['total_guru']; ?></h3>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-chalkboard-teacher text-blue-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Total Siswa -->
            <div class="bg-white rounded-xl shadow-sm p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Total Siswa</p>
                        <h3 class="text-2xl font-bold text-gray-800"><?php echo $stats['total_siswa']; ?></h3>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-user-graduate text-green-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Total Kelas -->
            <div class="bg-white rounded-xl shadow-sm p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Total Kelas</p>
                        <h3 class="text-2xl font-bold text-gray-800"><?php echo $stats['total_kelas']; ?></h3>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-school text-purple-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Total Materi -->
            <div class="bg-white rounded-xl shadow-sm p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Total Materi</p>
                        <h3 class="text-2xl font-bold text-gray-800"><?php echo $stats['total_materi']; ?></h3>
                    </div>
                    <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-book-open text-yellow-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Siswa Terbaru -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-lg font-semibold text-gray-800">Siswa Terbaru</h2>
                    <a href="/admin/manajemen-siswa" class="text-blue-600 hover:text-blue-700 text-sm">Lihat Semua</a>
                </div>
                <div class="space-y-4">
                    <?php while ($student = $latest_students->fetch_assoc()): ?>
                        <div class="flex items-center space-x-4">
                            <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-user text-gray-500"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-800"><?php echo $student['nama_lengkap']; ?></p>
                                <p class="text-sm text-gray-500">
                                    Kelas <?php echo $student['tingkat'] . ' ' . $student['nama_kelas']; ?> |
                                    NIS: <?php echo $student['nis']; ?>
                                </p>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>

            <!-- Materi Terbaru -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-lg font-semibold text-gray-800">Materi Terbaru</h2>
                    <a href="/admin/materi" class="text-blue-600 hover:text-blue-700 text-sm">Lihat Semua</a>
                </div>
                <div class="space-y-4">
                    <?php while ($material = $latest_materials->fetch_assoc()): ?>
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <h3 class="font-medium text-gray-800"><?php echo $material['judul']; ?></h3>
                            <p class="text-sm text-gray-500 mt-1">
                                Kategori: <?php echo $material['kategori']; ?> |
                                Kelas <?php echo $material['tingkat']; ?>
                            </p>
                            <p class="text-sm text-gray-500 mt-1">
                                Dibuat oleh: <?php echo $material['created_by']; ?>
                            </p>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>

            <!-- Statistik Quiz -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-lg font-semibold text-gray-800">Statistik Quiz</h2>
                    <a href="/admin/quiz" class="text-blue-600 hover:text-blue-700 text-sm">Lihat Semua</a>
                </div>
                <div class="space-y-4">
                    <?php while ($quiz = $quiz_stats->fetch_assoc()): ?>
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <h3 class="font-medium text-gray-800"><?php echo $quiz['judul']; ?></h3>
                            <div class="grid grid-cols-2 gap-4 mt-2">
                                <div>
                                    <p class="text-sm text-gray-500">Total Peserta</p>
                                    <p class="font-medium text-gray-800"><?php echo $quiz['total_peserta']; ?></p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Rata-rata Nilai</p>
                                    <p class="font-medium text-gray-800"><?php echo number_format($quiz['rata_nilai'], 1); ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="mt-8 bg-white rounded-xl shadow-sm p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-6">Aksi Cepat</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <a href="/admin/tambah-guru" class="p-4 bg-blue-50 rounded-lg text-center hover:bg-blue-100 transition-colors">
                    <i class="fas fa-user-plus text-blue-600 text-2xl mb-2"></i>
                    <p class="text-sm font-medium text-gray-800">Tambah Guru</p>
                </a>
                <a href="/admin/tambah-siswa" class="p-4 bg-green-50 rounded-lg text-center hover:bg-green-100 transition-colors">
                    <i class="fas fa-user-graduate text-green-600 text-2xl mb-2"></i>
                    <p class="text-sm font-medium text-gray-800">Tambah Siswa</p>
                </a>
                <a href="/admin/tambah-kelas" class="p-4 bg-purple-50 rounded-lg text-center hover:bg-purple-100 transition-colors">
                    <i class="fas fa-school text-purple-600 text-2xl mb-2"></i>
                    <p class="text-sm font-medium text-gray-800">Tambah Kelas</p>
                </a>
                <a href="/admin/laporan" class="p-4 bg-yellow-50 rounded-lg text-center hover:bg-yellow-100 transition-colors">
                    <i class="fas fa-chart-line text-yellow-600 text-2xl mb-2"></i>
                    <p class="text-sm font-medium text-gray-800">Lihat Laporan</p>
                </a>
            </div>
        </div>
    </div>

    <?php include '../views/layouts/footer.php'; ?>

    <script>
        // Tambahkan grafik atau visualisasi data jika diperlukan
        // Contoh menggunakan Chart.js
    </script>
</body>

</html>