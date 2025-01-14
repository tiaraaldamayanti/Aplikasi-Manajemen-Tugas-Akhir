<?php
// Mengaktifkan laporan error
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Koneksi ke database
$host = 'localhost';
$dbname = 'tugas_akhir';
$username_db = 'root';
$password_db = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username_db, $password_db);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Koneksi gagal: " . $e->getMessage());
}

session_start();

$error_message = "";

// Cek apakah form telah dikirim
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Debug: Tampilkan data POST yang diterima
    echo "<pre>POST Data: ";
    print_r($_POST);
    echo "</pre>";

    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $jenis_pengguna = trim($_POST['jenis_pengguna']);

    // Debug: Tampilkan input pengguna setelah trim
    echo "Input - Username: $username, Jenis Pengguna: $jenis_pengguna<br>";

    // Cek apakah username dan jenis pengguna valid
    $query = "SELECT * FROM daftarakun WHERE username = ? AND jenis_pengguna = ?";
    $stmt = $pdo->prepare($query);

    try {
        $stmt->execute([$username, $jenis_pengguna]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Debug: Tampilkan hasil query
        echo "<pre>Query Result: ";
        print_r($user);
        echo "</pre>";

        if ($user) {
            // Debug: Password verifikasi
            echo "Password (input): $password<br>";
            echo "Password (hash dari DB): " . $user['password'] . "<br>";

            if (password_verify($password, $user['password'])) {
                // Set session untuk login berhasil
                session_regenerate_id(true); // Amankan sesi
                $_SESSION['username'] = $user['username'];
                $_SESSION['nama'] = $user['Nama_Lengkap'];
                $_SESSION['jenis_pengguna'] = $user['jenis_pengguna'];

                // Redirect berdasarkan jenis pengguna
                switch ($user['jenis_pengguna']) {
                    case 'Mahasiswa':
                        header("Location: dashboard.php?role=Mahasiswa");
                        break;
                    case 'Dosen Pembimbing':
                        header("Location: dashboard.php?role=Dosen Pembimbing");
                        break;
                    case 'Dosen Penguji':
                        header("Location: dashboard.php?role=Dosen Penguji");
                        break;
                    case 'Koordinator':
                        header("Location: dashboard.php?role=Koordinator");
                        break;
                    default:
                        header("Location: dashboard.php");
                }
                exit;
            } else {
                // Debug: Password salah
                echo "Password salah!<br>";
                $error_message = "Username, password, atau jenis pengguna salah!";
            }
        } else {
            // Debug: Pengguna tidak ditemukan
            echo "Pengguna tidak ditemukan!<br>";
            $error_message = "Username, password, atau jenis pengguna salah!";
        }
    } catch (PDOException $e) {
        // Debug: Kesalahan query
        echo "Query error: " . $e->getMessage() . "<br>";
        $error_message = "Terjadi kesalahan pada sistem. Coba lagi nanti.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Login</title>
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

        .login-container img {
            display: block;
            margin: 0 auto 20px auto;
            max-width: 100px;
            height: auto;
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

        .input-field input,
        .input-field select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 25px;
            box-sizing: border-box;
            color: #333;
            font-size: 14px;
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

        .register-link {
            text-align: center;
            margin-top: 20px;
        }

        .register-link a {
            color: #007BFF;
            text-decoration: none;
        }

        .register-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <img src="images/logo.png" alt="Logo Aplikasi">
        <h1>Portal Login</h1>

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
            <div class="input-field">
                <label for="jenis_pengguna">Jenis Pengguna</label>
                <select name="jenis_pengguna" id="jenis_pengguna" required>
                    <option value="-">- Pilih Jenis Pengguna -</option>
                    <option value="Mahasiswa">Mahasiswa</option>
                    <option value="Dosen Pembimbing">Dosen Pembimbing</option>
                    <option value="Dosen Pembimbing">Dosen Penguji</option>
                    <option value="Koordinator">Koordinator</option>
                </select>
            </div>
            <button type="submit" class="login-button">Login</button>
        </form>

        <!-- Link untuk Daftar akun -->
        <div class="register-link">
            <p>Belum Punya Akun? <a href="DaftarAkun.php">Daftar</a></p>
        </div>
    </div>
</body>

</html>
