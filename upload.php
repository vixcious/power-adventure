<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

function resizeImage($sourcePath, $targetPath, $maxWidth = 800) {
  $info = getimagesize($sourcePath);
  list($width, $height) = $info;
  $type = $info['mime'];

  // Hitung ukuran baru proporsional
  if ($width > $maxWidth) {
    $ratio = $maxWidth / $width;
    $newWidth = $maxWidth;
    $newHeight = $height * $ratio;
  } else {
    // Tidak perlu resize
    move_uploaded_file($sourcePath, $targetPath);
    return;
  }

  // Buat image resource dari file
  switch ($type) {
    case 'image/jpeg':
      $srcImage = imagecreatefromjpeg($sourcePath);
      break;
    case 'image/png':
      $srcImage = imagecreatefrompng($sourcePath);
      break;
    case 'image/webp':
      $srcImage = imagecreatefromwebp($sourcePath);
      break;
    default:
      return false;
  }

  $resizedImage = imagecreatetruecolor($newWidth, $newHeight);
  imagecopyresampled($resizedImage, $srcImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

  // Simpan ulang
  switch ($type) {
    case 'image/jpeg':
      imagejpeg($resizedImage, $targetPath, 80);
      break;
    case 'image/png':
      imagepng($resizedImage, $targetPath);
      break;
    case 'image/webp':
      imagewebp($resizedImage, $targetPath, 80);
      break;
  }

  imagedestroy($srcImage);
  imagedestroy($resizedImage);
  return true;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $uploadDir = 'assets/galeri/';
  $jsonFile = 'data-galeri.json';

  if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
  }

  $category = $_POST['category'];
  $originalName = basename($_FILES['file']['name']);
  $fileName = $category . '-' . time() . '-' . $originalName;
  $tempPath = $_FILES['file']['tmp_name'];
  $targetPath = $uploadDir . $fileName;

  // Resize & simpan
  $resizeSuccess = resizeImage($tempPath, $targetPath);

  if ($resizeSuccess !== false || file_exists($targetPath)) {
    $newEntry = [
      'src' => $targetPath,
      'kategori' => $category
    ];

    if (file_exists($jsonFile)) {
      $jsonData = json_decode(file_get_contents($jsonFile), true);
      if (!is_array($jsonData)) $jsonData = [];
    } else {
      $jsonData = [];
    }

    $jsonData[] = $newEntry;
    file_put_contents($jsonFile, json_encode($jsonData, JSON_PRETTY_PRINT));
    echo "✅ Upload & resize berhasil!<br><a href='$targetPath' target='_blank'>Lihat Gambar</a>";
  } else {
    echo "❌ Upload gagal (format tidak didukung).";
  }
}
?>
