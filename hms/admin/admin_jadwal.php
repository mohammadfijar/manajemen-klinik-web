<?php
$conn = new mysqli('localhost', 'root', '', 'klinik');

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];

    if ($action === 'add') {
        $nama = $_POST['nama'];
        $jadwal = $_POST['jadwal'];
        $spesialisasi = $_POST['spesialisasi'];

        $stmt = $conn->prepare("INSERT INTO jadwal_dokter (nama, jadwal, spesialisasi) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $nama, $jadwal, $spesialisasi);
        $stmt->execute();
        $stmt->close();
    } elseif ($action === 'delete') {
        $id = $_POST['id'];

        $stmt = $conn->prepare("DELETE FROM jadwal_dokter WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    } elseif ($action === 'edit') {
        $id = $_POST['id'];
        $nama = $_POST['nama'];
        $jadwal = $_POST['jadwal'];
        $spesialisasi = $_POST['spesialisasi'];

        $stmt = $conn->prepare("UPDATE jadwal_dokter SET nama = ?, jadwal = ?, spesialisasi = ? WHERE id = ?");
        $stmt->bind_param("sssi", $nama, $jadwal, $spesialisasi, $id);
        $stmt->execute();
        $stmt->close();
    }
}

$conn->close();
header("Location: admin_jadwal.php");
exit();
?>
