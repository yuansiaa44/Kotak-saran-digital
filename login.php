<?php
// Mulai buffer output dan session di awal
ob_start();

ini_set('session.cookie_lifetime', 86400); // 1 hari
session_start();
error_log("PATH: " . __FILE__);
error_log("Server: " . print_r($_SERVER, true));
error_log("POST: " . print_r($_POST, true));

error_reporting(E_ALL);
ini_set('display_errors', 1);


error_log("Session dimulai. ID: " . session_id());
error_log("Data session saat ini: " . print_r($_SESSION, true));

require_once 'config2.php';

class User extends Database {
    private $username;
    private $password;
    
    public function __construct($username, $password) {
        parent::__construct();
        $this->username = trim($username);
        $this->password = trim($password);
    }
    
    public function login() {
        try {
            // Dapatkan koneksi database
            $conn = $this->getConnection();
            if (!$conn || $conn->connect_error) {
                error_log("Koneksi database gagal: " . ($conn ? $conn->connect_error : "Tidak ada koneksi"));
                return false;
            }
            
            // Gunakan prepared statement untuk mencegah SQL injection
            $stmt = $conn->prepare("SELECT id_user, password, role FROM user WHERE nama_user = ?");

            if (!$stmt) {
                error_log("Persiapan statement gagal: " . $conn->error);
                return false;
            }
            
            $stmt->bind_param("s", $this->username);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 1) {
                $row = $result->fetch_assoc();
                
                // Debug informasi password
                error_log("User ditemukan. Hash dari DB: " . $row['password']);
                error_log("Panjang password input: " . strlen($this->password));
                
                // Verifikasi password
                if (password_verify($this->password, $row['password'])) {
                    $_SESSION['id_user'] = $row['id_user'];
                    $_SESSION['user_ip'] = $_SERVER['REMOTE_ADDR'];
                    $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
                    $_SESSION['role'] = $row['role']; // ⬅️ simpan role ke session
                    error_log("Verifikasi password berhasil");
                    return true;
                }

                error_log("Verifikasi password gagal");
            } else {
                error_log("User tidak ditemukan atau ada duplikat");
            }
            return false;
        } catch (Exception $e) {
            error_log("Error login: " . $e->getMessage());
            return false;
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    error_log("Percobaan login dari IP: " . $_SERVER['REMOTE_ADDR']);
    
    try {
        // Validasi input
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        
        if (empty($username) || empty($password)) {
            throw new Exception("Username dan password harus diisi");
        }
        
        $user = new User($username, $password);
        
        if ($user->login()) {
            // Regenerasi ID session untuk mencegah fixation
            session_regenerate_id(true);
            
            $_SESSION['username'] = $username;
            $_SESSION['logged_in'] = true;
            $_SESSION['last_activity'] = time();
            
            error_log("Login berhasil. Session: " . print_r($_SESSION, true));
            
            // Handle redirect setelah login sukses
            $redirect_url = $_SESSION['redirect_url'] ?? 'index.php';
            unset($_SESSION['redirect_url']);
            
            // Bersihkan output buffer sebelum redirect
            while (ob_get_level() > 0) ob_end_clean();
            
            header("Location: " . $redirect_url);
            exit();
        }
        
        throw new Exception("Username atau password salah");
        
    } catch (Exception $e) {
        error_log("Error login: " . $e->getMessage());
        $_SESSION['login_error'] = $e->getMessage();
        
        // Simpan input form jika error
        $_SESSION['form_input'] = [
            'username' => $_POST['username'] ?? ''
        ];
        
        // Bersihkan output buffer sebelum redirect
        while (ob_get_level() > 0) ob_end_clean();
        
        header("Location: login.php");
        exit();
    }
}

?>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #45526c;
            --secondary-color: #f8a488;
            --bg-color: #f8f5f1;
            --text-dark: #0e0e0e;
            --text-light: #ffffff;
            --input-bg: rgba(69, 82, 108, 0.32);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: var(--bg-color);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .login-container {
            width: 100%;
            max-width: 500px;
            background-color: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            padding: 40px 30px;
            position: relative;
            overflow: hidden;
        }

        .login-title {
            text-align: center;
            color: var(--text-dark);
            font-size: 2.2rem;
            margin-bottom: 40px;
            font-weight: 600;
        }

        .input-group {
            position: relative;
            margin-bottom: 25px;
        }

        .input-field {
            width: 100%;
            padding: 15px 15px 15px 50px;
            border-radius: 12px;
            border: none;
            background-color: var(--input-bg);
            font-size: 1rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 4px rgba(0, 0, 0, 0.05);
        }

        .input-field:focus {
            outline: none;
            box-shadow: 0 0 0 2px var(--primary-color);
        }

        .input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--primary-color);
            font-size: 1.2rem;
        }

        .button-group {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
            gap: 15px;
        }

        .btn {
            flex: 1;
            padding: 15px;
            border-radius: 12px;
            border: none;
            font-size: 1.1rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .btn-register {
            background-color: var(--primary-color);
            color: var(--text-light);
        }

        .btn-login {
            background-color: var(--secondary-color);
            color: var(--text-dark);
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 10px rgba(0, 0, 0, 0.15);
        }

        /* Responsive Design */
        @media (max-width: 600px) {
            .login-container {
                padding: 30px 20px;
                border-radius: 15px;
            }
            
            .login-title {
                font-size: 1.8rem;
                margin-bottom: 30px;
            }
            
            .input-field {
                padding: 12px 12px 12px 45px;
                font-size: 0.9rem;
            }
            
            .button-group {
                flex-direction: column;
            }
            
            .btn {
                width: 100%;
            }
        }

        @media (max-width: 400px) {
            .login-title {
                font-size: 1.5rem;
            }
            
            .input-icon {
                font-size: 1rem;
                left: 12px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1 class="login-title">Login</h1>
        <?php if (!empty($message)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>
        <form method="POST" action="">  
    <div class="input-group">
        <i class="fas fa-user input-icon"></i>
        <input type="text" name="username" class="input-field" placeholder="Username" required>
    </div>
    
    <div class="input-group">
        <i class="fas fa-lock input-icon"></i>
        <input type="password" name="password" class="input-field" placeholder="Password" required>
    </div>
    
    <div class="button-group">
        <button type="button" class="btn btn-register" onclick="window.location.href='regis.php'">Register</button>
        <button type="submit" class="btn btn-login">Masuk</button>
    </div>
</form>
    </div>
</body>
</html>

<?php ob_end_flush(); ?> 
