<?php
require_once 'config2.php';
session_start();


if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

global $conn;
$db = new Database();
$conn = $db->getConnection();

class All {
    private $conn;

    public function __construct($conn){
        $this->conn = $conn;
    }

   public function tampilan(){
    $take = "SELECT saran.id_nama, saran.pengisi, saran.katagori, saran.isi_saran, saran.tanggapan
    FROM saran
    JOIN user ON saran.id_user = user.id_user
    ORDER BY saran.id_user DESC";

    $get = $this->conn->prepare($take);
    if(!$get){
        die("Prepare failed: " . $this->conn->error);
    }

    if(!$get->execute()){
        die("Execute failed: " . $this->conn->error);
    }

    $result = $get->get_result();
    if(!$result){
        die("Getting result failed: " . $this->conn->error);
    }

    $data = [];
    while ($row = $result->fetch_assoc()){
        $data[] = $row;
    }
    return $data;
}

    }

        $all = new All($conn);
        
        $list = $all->tampilan();

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kotak Saran Dashboard</title>
    <style>
        :root {
            --primary-color: #f87448;
            --secondary-color: #45526c;
            --dark-color: #333e54;
            --text-light: #fff9f9;
            --text-dark: #070707;
            --gray-bg: rgba(186, 186, 186, 0.22);
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
            overflow-x: hidden;
        }

        /* Sidebar Styles */
        .sidebar {
            width: var(--sidebar-width);
            background: var(--primary-color);
            padding: 20px;
            position: fixed;
            height: 100vh;
            display: flex;
            text-decoration: none;
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
            text-decoration: none;
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

        .divider {
            height: 1px;
            background: #D6D1D1;
            margin: 15px 0;
        }

        .stats-banner {
            height: 150px;
            background: linear-gradient(145.72deg, var(--secondary-color) 40.23%, var(--dark-color) 134.91%);
            border-radius: 8px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }

        .stats-number {
            font-size: 48px;
            font-weight: 700;
            color: white;
        }

        .stats-label {
            font-size: 24px;
            font-weight: 500;
            color: white;
            margin-top: 10px;
        }

        /* Table Styles */
        .table-container {
            background: var(--gray-bg);
            border-radius: 8px;
            overflow: hidden;
        }

        .table-header {
            display: flex;
            padding: 15px 20px;
            background: var(--gray-bg);
            font-weight: 500;
        }

        .header-item {
            flex: 1;
            font-size: 16px;
            color: var(--text-dark);
            text-align: center;
        }

        .table-row {
            display: flex;
            padding: 15px 20px;
            background: white;
            margin-top: 2px;
        }

        .row-item {
            flex: 1;
            font-size: 14px;
            color: var(--text-dark);
            text-align: center;
        }

        /* Popup Styles */
        input[type="checkbox"] {
            display: none;
        }

        .popup {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            width: 320px;
            padding: 20px;
            background: white;
            border: 2px solid #444;
            transform: translate(-50%, -50%);
            z-index: 999;
            box-shadow: 0 0 20px rgba(0,0,0,0.3);
            border-radius: 8px;
        }

        .popup h3 {
            margin-top: 0;
            color: var(--secondary-color);
        }

        .close-btn {
            float: right;
            font-size: 20px;
            cursor: pointer;
            color: #f00;
        }

        /* Show popup when checkbox is checked */
        .popup-toggle:checked + .table-row + .popup {
            display: block;
        }

        /* Mobile Menu Toggle */
        .menu-toggle {
            display: none;
            position: fixed;
            top: 15px;
            left: 15px;
            z-index: 1100;
            background: var(--primary-color);
            color: white;
            border: none;
            font-size: 20px;
            padding: 8px 12px;
            border-radius: 5px;
            cursor: pointer;
        }
        
        .btn {
            background-color: orange;
            color: white;
            padding: 6px 12px;
            border-radius: 12px;
            border: none;
            margin-right: 6px;
            font-size: 14px;
            text-decoration:none;
            cursor: pointer;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 10px rgba(0, 0, 0, 0.15);
        }

        /* Row hover effect */
        .clickable-row {
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .clickable-row:hover {
            background-color: #f0f0f0;
        }

        /* Responsive Styles */
        @media (max-width: 992px) {
            :root {
                --sidebar-width: 200px;
            }
            
            .logo-text {
                font-size: 20px;
            }
            
            .nav-item {
                font-size: 16px;
            }
            
            .stats-number {
                font-size: 36px;
            }
            
            .stats-label {
                font-size: 20px;
            }
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
                z-index: 1000;
            }
            
            .sidebar.active {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
                width: 100%;
            }
            
            .menu-toggle {
                display: block;
            }
            
            .stats-banner {
                height: 120px;
            }
            
            .table-header, .table-row {
                padding: 12px 15px;
            }
            
            .header-item {
                font-size: 14px;
            }
            
            .popup {
                width: 280px;
            }
        }
        
        @media (max-width: 576px) {
            .logo {
                margin-bottom: 30px;
            }
            
            .logo-icon {
                width: 40px;
                height: 30px;
            }
            
            .logo-text {
                font-size: 18px;
            }
            
            .nav-item {
                font-size: 14px;
                padding: 6px 10px;
                margin: 8px 0;
            }
            
            .stats-banner {
                height: 100px;
            }
            
            .stats-number {
                font-size: 32px;
            }
            
            .stats-label {
                font-size: 18px;
            }
            
            .breadcrumb {
                font-size: 16px;
            }
            
            .header-item {
                font-size: 12px;
            }
            
            .row-item {
                font-size: 12px;
            }
            
            .popup {
                width: 250px;
                padding: 15px;
            }
        }
    </style>
</head>
<body>
    <!-- Mobile Menu Toggle -->
    <button class="menu-toggle" id="menuToggle">☰</button>

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
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
    <main class="main-content" id="mainContent">
        <div class="breadcrumb">Home</div>
        <div class="divider"></div>
        
        <div class="stats-banner">
            <div class="stats-number"><?php echo count($list); ?></div>
            <div class="stats-label">Saran</div>
        </div>
        
        <!-- Table Container -->
        <div class="table-container">
            <!-- Table Header -->
            <div class="table-header">
                <div class="header-item">No</div>
                <div class="header-item">Nama</div>
                <div class="header-item">Kategori</div>
                <div class="header-item">Saran</div>
                <div class="header-item">Aksi</div>
            </div>
            
            <!-- Table Rows -->
            <?php 
            $no = 1;
            foreach($list as $row): 
                $popupId = 'popup-' . $row['id_nama'];
                $checkboxId = 'toggle-' . $row['id_nama'];
            ?>
                <!-- Checkbox trigger (harus tepat sebelum row) -->
                <input type="checkbox" id="<?php echo $checkboxId; ?>" class="popup-toggle">
                
                <!-- Table row -->
                <div class="table-row clickable-row">
                    <label for="<?php echo $checkboxId; ?>" class="row-item"><?php echo $no++; ?></label>
                    <label for="<?php echo $checkboxId; ?>" class="row-item"><?php echo htmlspecialchars($row['pengisi']); ?></label>
                    <label for="<?php echo $checkboxId; ?>" class="row-item"><?php echo htmlspecialchars($row['katagori']); ?></label>
                    <label for="<?php echo $checkboxId; ?>" class="row-item"><?php echo htmlspecialchars($row['isi_saran']); ?></label>
                    <div class="row-item">
                        <a href="delete.php?id_nama=<?php echo $row['id_nama'];?>" class="btn">Delete</a>
                        <a href="tanggapan.php?id_nama=<?php echo $row['id_nama'];?>" class="btn">Tanggapi</a>
                    </div>
                </div>
                
                <!-- Popup (harus tepat setelah row) -->
                <div class="popup" id="<?php echo $popupId; ?>">
                    <label for="<?php echo $checkboxId; ?>" class="close-btn">&times;</label>
                    <?php if (!empty($row['tanggapan'])): ?>
                        <h3>Tanggapan</h3>
                        <p><?php echo htmlspecialchars($row['tanggapan']); ?></p>
                    <?php else: ?>
                        <p>Belum ada tanggapan</p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </main>

    <script>
        // Toggle sidebar di mobile
        const menuToggle = document.getElementById('menuToggle');
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('mainContent');
        
        menuToggle.addEventListener('click', () => {
            sidebar.classList.toggle('active');
        });
        
        // Tutup sidebar saat mengklik konten utama di mobile
        mainContent.addEventListener('click', () => {
            if (window.innerWidth <= 768 && sidebar.classList.contains('active')) {
                sidebar.classList.remove('active');
            }
        });
    </script>
</body>
</html>
