<?php
// Aktifkan laporan error
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Dummy data untuk validasi login
$valid_username = "admin";
$valid_password = "12345";

// Variabel error_message untuk menampung pesan error
$error_message = "";

// Cek apakah form telah dikirim
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Validasi username dan password
    if ($username !== $valid_username) {
        $error_message = "Username tidak terdaftar!";
    } elseif ($password !== $valid_password) {
        $error_message = "Password salah!";
    } else {
        // Login berhasil
        $_SESSION['username'] = $username;
        header("Location: dashboard.html");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Interface</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .login-container {
            background-color: #f2f2f2;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 300px;
            text-align: left;
        }

        .login-container h1 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 1.5rem;
            color: #333;
        }

        .input-field {
            margin-bottom: 20px;
        }

        .input-field label {
            font-size: 15px;
            color: #333;
            display: block;
            margin-bottom: 5px;
        }

        .input-field input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 25px;
            box-sizing: border-box;
        }

        .login-button {
            background-color: #007BFF;
            color: white;
            border: none;
            padding: 10px;
            width: 100%;
            border-radius: 25px;
            cursor: pointer;
            font-size: 16px;
        }

        .login-button:hover {
            background-color: #0056b3;
        }

        .error-message {
            color: red;
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>Login Interface</h1>
        
        <!-- Pesan error jika ada -->
        <?php if (!empty($error_message)) : ?>
            <div class="error-message">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <!-- Formulir login -->
        <form action="" method="POST">
            <div class="input-field">
                <label for="username">Username</label>
                <input type="text" name="username" id="username" placeholder="Masukkan username" required>
            </div>
            <div class="input-field">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" placeholder="Masukkan password" required>
            </div>
            <button type="submit" class="login-button">Login</button>
        </form>
    </div>
</body>
</html>
