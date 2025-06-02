<?php
$conn = new mysqli("localhost", "root", "", "peminjaman_db");

$no = 1;
$query = "
SELECT 
  p.kode_peminjaman,
  r.nama_ruangan,
  u.nama AS nama_peminjam,
  rp.keterangan,
  p.waktu_mulai,
  p.waktu_selesai,
  CASE 
    WHEN p.status = 'Approved' AND NOW() BETWEEN p.waktu_mulai AND p.waktu_selesai THEN 'Used'
    WHEN p.status = 'Approved' AND NOW() > p.waktu_selesai THEN 'Selesai'
    ELSE p.status
  END AS status
FROM peminjaman p
LEFT JOIN ruangan r ON p.kode_ruangan = r.kode_ruangan
LEFT JOIN user u ON p.NIM = u.NIM
LEFT JOIN riwayat_peminjaman rp ON rp.kode_peminjaman = p.kode_peminjaman
ORDER BY p.waktu_mulai DESC
";
include 'update_riwayat.php'; // Update riwayat
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Daftar Peminjaman</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-white">
  <!-- Navbar -->
  <nav class="flex items-center justify-between px-8 py-4 bg-gray-800 shadow">
    <div class="text-xl font-semibold">Sistem Peminjaman Ruangan</div>
    <ul class="flex space-x-6 text-sm items-center">
      <li><a href="daftar_ruangan.php" class="hover:text-blue-400">Daftar Ruangan</a></li>
      <li><a href="riwayat_peminjaman.php" class="hover:text-blue-400">Riwayat Peminjaman</a></li>
      <li><a href="daftar_peminjaman.php" class="hover:text-blue-400">Daftar Peminjaman</a></li>
      <li><a href="logout.php" class="hover:text-blue-400">Logout</a></li>
    </ul>
  </nav>

  <div class="p-6">
    <h1 class="text-3xl font-bold text-center mb-4">Daftar Peminjaman</h1>

    <div class="overflow-x-auto bg-gray-800 rounded-lg p-4">
      <table class="min-w-full table-auto">
        <thead>
          <tr class="text-left border-b border-gray-700">
            <th class="py-2 px-3">No</th>
            <th class="py-2 px-3">Nama Ruangan</th>
            <th class="py-2 px-3">Nama Peminjam</th>
            <th class="py-2 px-3">Keterangan</th>
            <th class="py-2 px-3">Waktu Mulai</th>
            <th class="py-2 px-3">Waktu Selesai</th>
            <th class="py-2 px-3">Status</th>
          </tr>
        </thead>
        <tbody id="tableBody">
          <?php while ($row = $result->fetch_assoc()) : ?>
            <tr class='border-b border-gray-700'>
              <td class='py-2 px-3'><?= $no++ ?></td>
              <td class='py-2 px-3'><?= $row['nama_ruangan'] ?></td>
              <td class='py-2 px-3'><?= $row['nama_peminjam'] ?></td>
              <td class='py-2 px-3'><?= $row['keterangan'] ?></td>
              <td class='py-2 px-3'><?= $row['waktu_mulai'] ?></td>
              <td class='py-2 px-3'><?= $row['waktu_selesai'] ?></td>
              <td class='py-2 px-3'>
                <span class='px-2 py-1 rounded
                  <?= $row['status'] == 'Used' ? 'bg-red-500' : ($row['status'] == 'Selesai' ? 'bg-green-500' : 'bg-yellow-500') ?>'>
                  <?= $row['status'] ?>
                </span>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>
</body>
</html>