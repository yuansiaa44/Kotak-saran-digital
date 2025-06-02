<?php
require_once 'config2.php';

class Saran extends Database {
    
    public function __construct(){
        parent::__construct();
    }

    public function delete($id_name){
        $query = "DELETE FROM saran WHERE id_nama = ?";
        $stmt = $this->conn->prepare($query);

        if (!$stmt) {
            die("Prepare gagal: " . $this->conn->error); // Tambahan debug
        }

        $stmt->bind_param("i", $id_name);
        $stmt->execute();

        return $stmt->affected_rows;
    }
}

$id_nama = $_GET['id_nama']; // Harus validasi di real app

$saran = new Saran();
$result = $saran->delete($id_nama);

if ($result > 0) {
    echo "<script>alert('Data berhasil dihapus!'); window.location.href='index.php';</script>";
} else {
    echo "Gagal menghapus data.";
}
?>
