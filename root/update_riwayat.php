<?php
require 'koneksi.php';

// Ambil peminjaman yang disetujui dan sudah selesai tapi belum masuk ke riwayat
$sql = "
SELECT p.kode_peminjaman, p.keterangan
FROM peminjaman p
LEFT JOIN riwayat_peminjaman rp ON p.kode_peminjaman = rp.kode_peminjaman
WHERE p.status = 'Approved'
  AND p.waktu_selesai < NOW()
  AND rp.kode_peminjaman IS NULL
";

$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
    $kode = $row['kode_peminjaman'];
    $keterangan = $row['keterangan'];
    if ($keterangan === null) {
      $keterangan = 'Tidak ada keterangan'; // atau nilai default lainnya
  }

    // Masukkan ke riwayat dengan nilai default
    // var_dump($row['keterangan']);
    $stmt = $conn->prepare("
      INSERT INTO riwayat_peminjaman (kode_peminjaman, tgl_konfirmasi, kondisi_ruangan, keterangan)
      VALUES (?, NOW(), 'Baik', ?)
    ");
    $stmt->bind_param("ss", $kode, $keterangan); // Bind parameter untuk mencegah SQL Injection
    $stmt->execute();
}
?>