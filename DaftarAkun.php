<?php
// Mengaktifkan laporan error
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Koneksi ke database
$host = 'localhost'; 
$dbname = 'tugas_akhir'; 
$username = 'root'; 
$password = ''; 

try {
  $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  die("Koneksi gagal: " . $e->getMessage());
}

$error_message = "";

// Cek apakah form telah dikirim
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Ambil data dari form
  $nama = trim($_POST['nama_lengkap']);
  $nomor_induk = trim($_POST['nomor_induk']);
  $password = trim($_POST['password']);
  $jenis_pengguna = trim($_POST['jenis_pengguna']);
  $email = trim($_POST['email']);
  $username = trim($_POST['username']);

  // Validasi form
  if ($nama && $nomor_induk && $password && $jenis_pengguna !== '-' && $email && $username) {
    // Pastikan Nomor Induk hanya berisi angka
    if (!preg_match('/^\d+$/', $nomor_induk)) {
      $error_message = "Nomor Induk harus berupa angka!";
    } else {
      // Cek apakah Nomor Induk sudah terdaftar
      $stmt = $pdo->prepare("SELECT COUNT(*) FROM daftarakun WHERE Nomor_Induk = ?");
      $stmt->execute([$nomor_induk]);
      $count = $stmt->fetchColumn();

      if ($count > 0) {
        $error_message = "Nomor Induk sudah terdaftar!";
      } else {
        // Enkripsi password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Query untuk memasukkan data ke tabel DaftarAkun
        $query = "INSERT INTO daftarakun (Nama_Lengkap, Username, Nomor_Induk, email, password, jenis_pengguna) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($query);

        try {
          // Eksekusi query untuk menyimpan data akun
          $stmt->execute([$nama, $username, $nomor_induk, $email, $hashed_password, $jenis_pengguna]);

          // Setelah data berhasil masuk ke tabel DaftarAkun, masukkan data ke tabel profil yang sesuai
          if ($jenis_pengguna === 'Mahasiswa') {
            // Query untuk memasukkan data ke tabel profil_mahasiswa
            $profilQuery = "INSERT INTO profil (Nomor_Induk, Nama_Lengkap, Username, email) VALUES (?, ?, ?, ?)";
            $profilStmt = $pdo->prepare($profilQuery);
            $profilStmt->execute([$nomor_induk, $nama, $username, $email]);
          } elseif ($jenis_pengguna === 'Dosen Pembimbing') {
            // Query untuk memasukkan data ke tabel profil_dosen
            $profilQuery = "INSERT INTO profil_dosen (Nomor_Induk, Nama_Lengkap, Username, email) VALUES (?, ?, ?, ?)";
            $profilStmt = $pdo->prepare($profilQuery);
            $profilStmt->execute([$nomor_induk, $nama, $username, $email]);
          } elseif ($jenis_pengguna === 'Koordinator') {
            // Query untuk memasukkan data ke tabel profil_koordinator
            $profilQuery = "INSERT INTO profil_koordinator (Nomor_Induk, Nama_Lengkap, Username, email) VALUES (?, ?, ?, ?)";
            $profilStmt = $pdo->prepare($profilQuery);
            $profilStmt->execute([$nomor_induk, $nama, $username, $email]);
          }

          // Redirect ke dashboard berdasarkan jenis pengguna
          switch ($jenis_pengguna) {
            case 'Mahasiswa':
              $redirect_url = 'dashboard.html';
              break;
            case 'Dosen Pembimbing':
              $redirect_url = 'dashboard_pembimbing.html';
              break;
            case 'Koordinator':
              $redirect_url = 'dashboard_koordinator.html';
              break;
            default:
              $redirect_url = 'dashboard.html'; 
              break;
          }

          // Redirect setelah pendaftaran sukses
          echo "<script>alert('Pendaftaran berhasil!'); window.location.href = '$redirect_url';</script>";
          exit;
        } catch (PDOException $e) {
          $error_message = "Terjadi kesalahan saat pendaftaran: " . $e->getMessage();
        }
      }
    }
  } else {
    $error_message = "Mohon lengkapi semua data!";
  }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pendaftaran Akun</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #fff;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 130vh;
      margin: 0;
    }

    .register-container {
      background-color: #f2f2f2;
      border-radius: 10px;
      padding: 30px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      width: 300px;
      text-align: left;
    }

    .register-container img {
      display: block;
      margin: 0 auto 20px auto;
      max-width: 100px;
      height: auto;
    }

    .register-container h1 {
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

    .register-button {
      background-color: #007BFF;
      color: white;
      border: none;
      padding: 10px;
      width: 100%;
      border-radius: 25px;
      cursor: pointer;
      font-size: 16px;
    }

    .register-button:hover {
      background-color: #0056b3;
    }

    .error-message {
      color: red;
      text-align: center;
      margin-bottom: 20px;
    }

    .login-link {
      text-align: center;
      margin-top: 20px;
    }

    .login-link a {
      color: #007BFF;
      text-decoration: none;
    }

    .login-link a:hover {
      text-decoration: underline;
    }
  </style>
</head>

<body>
  <div class="register-container">
    <img src="images/logo.png" alt="Logo Aplikasi">
    <h1>Pendaftaran Akun</h1>

    <!-- Pesan error jika ada -->
    <?php if (!empty($error_message)) : ?>
      <div class="error-message">
        <?php echo htmlspecialchars($error_message); ?>
      </div>
    <?php endif; ?>

    <!-- Formulir pendaftaran -->
    <form id="registerForm" action="DaftarAkun.php" method="POST">
      <div class="input-field">
        <label for="nama_lengkap">Nama Lengkap</label>
        <input type="text" name="nama_lengkap" id="nama_lengkap" placeholder="Masukkan nama lengkap" required>
      </div>
      <div class="input-field">
        <label for="nomor_induk">Nomor Induk</label>
        <input type="text" name="nomor_induk" id="nomor_induk" placeholder="Masukkan Nomor Induk" required>
      </div>
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
          <option value="Koordinator">Koordinator</option>
        </select>
      </div>
      <div class="input-field">
        <label for="email">Email</label>
        <input type="email" name="email" id="email" placeholder="Masukkan email" required>
      </div>
      <button type="submit" class="register-button">Daftar</button>
    </form>

    <div class="login-link">
      <p>Sudah Punya Akun? <a href="Login.php">Login</a></p>
    </div>
  </div>
</body>

</html>