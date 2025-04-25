<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 Not Found</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: fadeIn 1s;
        }

        .error-container {
            text-align: center;
            max-width: 600px;
            padding: 30px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            animation: slideIn 0.5s;
        }

        .error-container h1 {
            font-size: 100px;
            font-weight: bold;
            color: #dc3545;
            animation: bounce 1s infinite;
        }

        .error-container h2 {
            font-size: 24px;
            margin-bottom: 20px;
        }

        .error-container p {
            font-size: 16px;
            margin-bottom: 30px;
        }

        .btn-custom {
            background-color: #007bff;
            color: white;
        }

        @keyframes  fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        @keyframes  slideIn {
            from {
                transform: translateY(-30px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        @keyframes  bounce {
            0%, 20%, 50%, 80%, 100% {
                transform: translateY(0);
            }
            40% {
                transform: translateY(-30px);
            }
            60% {
                transform: translateY(-15px);
            }
        }
    </style>
</head>
<body>

<div class="error-container">
    <h1>404</h1>
    <h2>Oops! Page Not Found</h2>
    <p>We can't seem to find the page you're looking for.</p>
    <a href="<?php echo e(route('home')); ?>" class="btn btn-custom">Go Back Home</a>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
<?php /**PATH C:\wamp64\www\template1\resources\views/errors/404.blade.php ENDPATH**/ ?>