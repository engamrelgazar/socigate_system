<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset Success</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            background-color: #f9f9f9;
            font-family: Arial, sans-serif;
        }

        .success-container {
            text-align: center;
        }

        .success-icon {
            width: 100px;
            height: 100px;
            margin-bottom: 20px;
        }

        .success-message {
            font-size: 1.5rem;
            font-weight: bold;
            color: #333;
        }

        .success-highlight {
            color: green;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="success-container">
        <img src="{{ asset('images/success.png') }}" alt="Success" class="success-icon">
        <p class="success-message">
            The password has been<br>
            changed <span class="success-highlight">successfully</span>
        </p>
    </div>
</body>
</html>
