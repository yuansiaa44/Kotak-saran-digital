<?php 
require_once 'config2.php';
session_start();
if (!isset($_SESSION['id_user'])) {
    echo "<script>alert('Anda harus login terlebih dahulu'); window.location.href='login.php';</script>";
    exit;
}

class Form extends Database {
    protected $conn;
    private $id_user;
    private $pengisi;
    private $katagori;
    private $isi_saran;

    public function __construct() {
        parent::__construct();
        $this->conn = $this->getConnection();  
    }

    public function setUserId($id_user) {
        $this->id_user = mysqli_real_escape_string($this->conn, $id_user);
    }

    public function setPengisi($pengisi){
        $this->pengisi = mysqli_real_escape_string($this->conn, $pengisi);
    }

    public function setKatagori($katagori){
        $this->katagori = mysqli_real_escape_string($this->conn, $katagori);
    }

    public function setIsi($isi_saran){
        $this->isi_saran = mysqli_real_escape_string($this->conn, $isi_saran);
    }


     public function getKatagori(){
        return $this->katagori;
    }

     public function getIsi(){
        return $this->isi_saran;
    }

    public function isiSaran(){
    $stmt = $this->conn->prepare("INSERT INTO saran (id_user, pengisi, katagori, isi_saran) VALUES (?, ?, ?, ?)");
    if(!$stmt){
        echo "Prepare failed: (" . $this->conn->errno . ") " . $this->conn->error;
        return false;
    }
    $stmt->bind_param("isss", $this->id_user, $this->pengisi, $this->katagori, $this->isi_saran);

    if(!$stmt->execute()){
        echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
        return false;
    }
    $affected_rows = $stmt->affected_rows;
    $stmt->close();
    return $affected_rows;
}


}

    if ($_SERVER['REQUEST_METHOD']==='POST' &&  isset($_POST['pengisi'], $_POST['katagori'], $_POST['isi_saran'])){
        $form = new Form();
        $form->setUserId($_SESSION['id_user']);
        $form->setPengisi($_POST['pengisi']);
        $form->setKatagori($_POST['katagori']);
        $form->setIsi($_POST['isi_saran']);
       


        $result = $form->isiSaran();
      if ($result > 0){
           echo " <script>
               alert ('Data berhasil ditambahkan');
              document.location.href = 'index.php';
           </script>";
      } else {
            echo"<script>
               alert ('Data gagal ditambahkan');
         
         document.location.href = 'form.php';
          </script>";
       }
    }
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Saran</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #f87448;
            --secondary-color: #5aa897;
            --text-dark: #252424;
            --text-light: #fff9f9;
            --input-bg: #d9d9d9;
            --sidebar-width: 240px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            display: flex;
            min-height: 100vh;
            background: #fff;
        }

        /* Sidebar Styles */
        .sidebar {
            width: var(--sidebar-width);
            background: var(--primary-color);
            padding: 20px;
            position: fixed;
            height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .logo {
            display: flex;
            align-items: center;
            margin-bottom: 60px;
            position: relative;
        }

        .logo-icon {
            width: 50px;
            height: 38px;
            margin-right: 15px;
        }

        .logo-text {
            font-size: 24px;
            color: #000;
            line-height: 1.2;
        }

        .nav-menu {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .nav-item {
            color: var(--text-light);
            font-size: 18px;
            margin: 12px 0;
            padding:8px 12px;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s;
        }

        .nav-item:hover {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 8px;
        }

        .about-link {
            margin-top: auto;
            margin-bottom: 20px;
        }

        /* Main Content Styles */
        .main-content {
            flex: 1;
            margin-left: var(--sidebar-width);
            padding: 20px;
        }

        .breadcrumb {
            font-size: 18px;
            color: var(--text-dark);
            margin-bottom: 20px;
        }

        .page-title {
            font-size: 32px;
            font-weight: 500;
            color: #070707;
            margin-bottom: 30px;
        }

        .divider {
            height: 1px;
            background: #D6D1D1;
            margin: 15px 0;
        }

        .form-group {
            margin-bottom: 30px;
        }

        .form-label {
            font-size: 20px;
            color: var(--text-dark);
            display: block;
            margin-bottom: 15px;
        }

        .form-input {
            width: 100%;
            height: 53px;
            background: var(--input-bg);
            border: none;
            padding: 0 15px;
            font-size: 18px;
        }

        .form-textarea {
            width: 100%;
            height: 220px;
            background: var(--input-bg);
            border: none;
            padding: 15px;
            font-size: 18px;
            resize: none;
        }

        .form-textarea::placeholder {
            color: rgba(37, 36, 36, 0.2);
            font-size: 24px;
        }

        .btn {
            width: 90px;
            height: 45px;
            border-radius: 16px;
            background: var(--secondary-color);
            color: var(--text-dark);
            font-size: 20px;
            border: none;
            cursor: pointer;
            box-shadow: 0px 4px 4px 0 rgba(0,0,0,0.2);
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0px 6px 6px 0 rgba(0,0,0,0.2);
        }

        /* Responsive Styles */
        @media (max-width: 992px) {
            :root {
                --sidebar-width: 220px;
            }
            
            .logo-text {
                font-size: 30px;
            }
            
            .nav-item {
                font-size: 20px;
            }
            
            .page-title {
                font-size: 36px;
            }
        }

        @media (max-width: 768px) {
            body {
                flex-direction: column;
            }
            
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
                padding: 15px;
                flex-direction: row;
                align-items: center;
            }
            
            .logo {
                margin-bottom: 0;
                margin-right: auto;
            }
            
            .nav-menu {
                flex-direction: row;
                align-items: center;
                flex-grow: 0;
                margin-left: 20px;
            }
            
            .nav-item {
                margin: 0 10px;
                padding: 8px 12px;
            }
            
            .about-link {
                margin: 0 0 0 10px;
            }
            
            .main-content {
                margin-left: 0;
                padding: 20px;
            }
            
            .form-group {
                margin-bottom: 20px;
            }
        }

        @media (max-width: 576px) {
            .logo-text {
                font-size: 24px;
            }
            
            .logo-icon {
                width: 50px;
                height: 38px;
            }
            
            .nav-item {
                font-size: 16px;
                padding: 6px 8px;
            }
            
            .page-title {
                font-size: 28px;
            }
            
            .form-label, .breadcrumb {
                font-size: 20px;
            }
            
            .form-input {
                height: 45px;
                font-size: 16px;
            }
            
            .form-textarea {
                height: 250px;
                font-size: 16px;
            }
            
            .form-textarea::placeholder {
                font-size: 20px;
            }
            
            .btn {
                width: 80px;
                height: 45px;
                font-size: 20px;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="logo">
            <svg class="logo-icon" viewBox="0 0 70 53" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M0.21875 6.80312C0.659829 4.87044 1.74412 3.14481 3.29403 1.90885C4.84395 0.672891 6.76762 -0.000133413 8.75 1.98369e-08H61.25C63.2324 -0.000133413 65.1561 0.672891 66.706 1.90885C68.2559 3.14481 69.3402 4.87044 69.7812 6.80312L35 28.0612L0.21875 6.80312ZM0 11.7994V42.8794L25.3881 27.3131L0 11.7994ZM29.5794 29.8813L0.835625 47.4994C1.5458 48.9969 2.66668 50.2619 4.06779 51.1472C5.46891 52.0324 7.09264 52.5016 8.75 52.5H61.25C62.9071 52.5004 64.5302 52.0302 65.9305 51.1441C67.3308 50.2581 68.4508 48.9926 69.16 47.495L40.4162 29.8769L35 33.1888L29.5794 29.8813ZM44.6119 27.3175L70 42.8794V11.7994L44.6119 27.3175Z" fill="#FFF7F7"></path>
            </svg>
            <div class="logo-text">
                <div>Kotak</div>
                <div>Saran</div>
            </div>
        </div>
        
        <nav class="nav-menu">
            <a href="index.php" class="nav-item">Home</a>
           <a href="form.php" class="nav-item">Form Saran</a>
             <a href="logout.php" class="nav-item about-link">Logout</a>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <div class="breadcrumb">Home / edit</div>
        <div class="divider"></div>
        <h1 class="page-title">Send Your Feedback</h1>
        
        <form action="form.php" method="POST">
            <div class="form-group">
                <label class="form-label">Pengisi</label>
                <input type="text" class="form-input" name="pengisi" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">Category</label>
                <select name="katagori" class="form-input" style="width: 261px;" required>
                    <option value=""></option>
                    <option value="Saran">Saran</option>
                    <option value="Kritik">Kritik</option>
                    <option value="Pertanyaan">Pertanyaan</option>
                </select>
            </div>
            
            <div class="form-group">
                <label class="form-label">Feedback</label>
                <textarea name="isi_saran" class="form-textarea"placeholder="tulis saran, kritik, atau pertanyaan anda disini" required></textarea>
            </div>
            
            <button type="submit" class="btn">Kirim</button>
        </form>
    </main>
</body>
</html>