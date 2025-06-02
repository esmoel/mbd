<?php
session_start();
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'koneksi.php';

// Cek login (sementara pakai dummy NIM jika belum login)
if (!isset($_SESSION['nim'])) {
  $_SESSION['nim'] = '202210001'; // dummy NIM, sesuaikan nanti
}

$data_json = file_get_contents("php://input");
$data = json_decode($data_json, true);

if (!$data) {
  echo json_encode(["error" => "Gagal parsing JSON"]);
  exit;
}

$kode_ruangan = $data['kode_ruangan'] ?? null;
$tanggal = $data['tanggal'] ?? null;
$waktu_mulai = $data['waktu_mulai'] ?? null;
$waktu_selesai = $data['waktu_selesai'] ?? null;
$keterangan = $data['keterangan'] ?? null;
$nim = $_SESSION['nim'];

if (!$kode_ruangan || !$tanggal || !$waktu_mulai || !$waktu_selesai || !$keterangan) {
  echo json_encode(['error'=>'Data tidak lengkap']);
  exit;
}

$mulai = "$tanggal $waktu_mulai";
$selesai = "$tanggal $waktu_selesai";

// Cek bentrok jadwal
$sql = "SELECT * FROM peminjaman
        WHERE kode_ruangan = ? 
        AND (
            (? BETWEEN waktu_mulai AND waktu_selesai)
            OR (? BETWEEN waktu_mulai AND waktu_selesai)
            OR (waktu_mulai BETWEEN ? AND ?)
        ) AND status = 'Approved'";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sssss", $kode_ruangan, $mulai, $selesai, $mulai, $selesai); // Menggunakan 5 parameter
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode(['error' => 'Ruangan sudah dipinjam di waktu tersebut']);
    exit;
}

// Membuat kode peminjaman baru
$result = $conn->query("SELECT MAX(kode_peminjaman) AS last FROM peminjaman");
$last = $result->fetch_assoc()['last'];

$lastNumber = (int) substr($last, 1); // Ambil angka dari kode_peminjaman terakhir
$newNumber = $lastNumber + 1;
$kode_peminjaman = 'P' . str_pad($newNumber, 3, '0', STR_PAD_LEFT); // Contoh: P011

// Insert data peminjaman baru
$stmt = $conn->prepare("INSERT INTO peminjaman (kode_peminjaman, kode_ruangan, NIM, waktu_mulai, waktu_selesai, tanggal_peminjaman, keterangan) 
                        VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssssss", $kode_peminjaman, $kode_ruangan, $nim, $mulai, $selesai, $tanggal, $keterangan); // Sudah benar
$stmt->execute();
echo json_encode(['success' => true, 'message' => 'Peminjaman berhasil']);
?>