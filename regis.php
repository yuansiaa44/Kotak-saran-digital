<?php
require_once 'config2.php';
session_start();

class User {
    private $conn;
    private $table = "user";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function usernameExists($username) {
        $escaped_username = $this->conn->real_escape_string($username);
        $query = "SELECT id_user FROM {$this->table} WHERE nama_user = '$escaped_username'";
        
        $result = $this->conn->query($query);
        if ($result === false) {
            die("Query error: " . $this->conn->error);
        }
        
        return $result->num_rows > 0;
    }

    public function register($username, $password) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        $escaped_username = $this->conn->real_escape_string($username);
        $escaped_password = $this->conn->real_escape_string($hashedPassword);
        
        $query = "INSERT INTO {$this->table} (nama_user, password) VALUES ('$escaped_username', '$escaped_password')";
        
        $result = $this->conn->query($query);
        if ($result === false) {
            die("Insert error: " . $this->conn->error);
        }
        
        return $result;
    }
    public function login($username, $password) {
    $escaped_username = $this->conn->real_escape_string($username);
    $query = "SELECT id_user, password FROM {$this->table} WHERE nama_user = '$escaped_username'";
    
    $result = $this->conn->query($query);
    if ($result === false) {
        die("Query error: " . $this->conn->error);
    }
    
    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id_user'];
            $_SESSION['username'] = $username;
            $_SESSION['logged_in'] = true;
            return true;
        }
    }
    return false;
}
}

// Inisialisasi variabel pesan
$message = "";

// Proses form hanya jika method POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari form
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm'] ?? '';
    
    // Validasi input
    if (empty($username) || empty($password) || empty($confirm)) {
        $message = "Semua field harus diisi!";
    } elseif (strlen($password) < 8) {
        $message = "Password minimal 8 karakter";
    } elseif ($password !== $confirm) {
        $message = "Password dan konfirmasi tidak cocok!";
    } else {
        $db = new Database();
        $user = new User($db->getConnection());

        if ($user->usernameExists($username)) {
            $message = "Username sudah digunakan!";
        } else {
            if ($user->register($username, $password)) {
                $_SESSION['username'] = $username;
                $_SESSION['logged_in'] = true;  // Tambahkan status login
                $_SESSION['new_registration'] = true;
                
                // Redirect setelah registrasi berhasil
                header("Location: index.php");
                exit();
            } else {
                $message = "Terjadi kesalahan saat registrasi.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Regis</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #45526c;
            --secondary-color: #f8a488;
            --bg-color: #ffffff1;
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
        <h1 class="login-title">Register</h1>
        
        <?php if (!empty($message)): ?>
            <div style="color: <?= strpos($message, 'berhasil') !== false ? 'green' : 'red' ?>; 
                    margin-bottom: 20px; text-align: center;">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="regis.php">
            
            
            <div class="input-group">
                <i class="fas fa-user input-icon"></i>
                <input type="text" class="input-field" name="username" placeholder="Username" required>
            </div>
            
            <div class="input-group">
                <i class="fas fa-lock input-icon"></i>
                <input type="password" class="input-field" name="password" placeholder="Password" required>
            </div>
            
            <div class="input-group">
                <i class="fas fa-lock input-icon"></i>
                <input type="password" class="input-field" name="confirm" placeholder="Confirm Password" required>
            </div>
            
            <div class="button-group">
                <button type="submit" class="btn btn-login">Daftar</button>
            </div>
        </form>
    </div>
</body>
</html> 