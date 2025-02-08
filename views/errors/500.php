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
        }

        h1 {
            color: #d32f2f;
        }

        .error-container {
            max-width: 600px;
            margin: 0 auto;
        }

        .back-link {
            margin-top: 20px;
            display: inline-block;
            padding: 10px 20px;
            background-color: #2196f3;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }
    </style>
</head>

<body>
    <div class="error-container">
        <h1>500 - Internal Server Error</h1>
        <p>Sorry, something went wrong on our server. Please try again later.</p>
        <a href="<?= Router::url() ?>" class="back-link">Go to Homepage</a>
    </div>
</body>

</html>