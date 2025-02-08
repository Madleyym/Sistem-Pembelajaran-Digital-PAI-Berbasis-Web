<?php
// /opt/lampp/htdocs/PAI/views/errors/500.php
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Internal Server Error</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            padding: 50px;
            background-color: #f5f5f5;
            margin: 0;
        }

        .error-container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #d32f2f;
            font-size: 48px;
            margin: 0 0 20px;
        }

        .error-code {
            font-size: 24px;
            color: #666;
            margin-bottom: 30px;
        }

        p {
            color: #444;
            font-size: 18px;
            line-height: 1.6;
            margin-bottom: 30px;
        }

        .back-link {
            display: inline-block;
            padding: 12px 24px;
            background-color: #2196f3;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        .back-link:hover {
            background-color: #1976d2;
        }
    </style>
</head>

<body>
    <div class="error-container">
        <h1>Server Error</h1>
        <div class="error-code">500 Error</div>
        <p>Maaf, terjadi kesalahan pada server kami.<br>Silakan coba lagi nanti atau hubungi administrator jika masalah berlanjut.</p>
        <a href="<?= Router::url() ?>" class="back-link">Kembali ke Beranda</a>
    </div>
</body>

</html>