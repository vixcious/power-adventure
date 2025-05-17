<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Upload Galeri</title>
  <link rel="stylesheet" href="styles.css" />
  <style>
    body { font-family: sans-serif; padding: 2rem; max-width: 800px; margin: auto; }
    h1 { text-align: center; }
    form { margin-bottom: 2rem; }
    input, select { padding: 0.5rem; margin-bottom: 1rem; width: 100%; }
    .gallery-list img { height: 100px; border-radius: 6px; margin-right: 1rem; }
    .gallery-item { display: flex; align-items: center; margin-bottom: 1rem; gap: 1rem; }
    .gallery-item form { display: inline; }
    .actions button { margin-right: 0.5rem; padding: 0.25rem 0.75rem; }
  </style>
</head>
<body>
  <h1>Upload Gambar Galeri</h1>

  <form action="upload.php" method="POST" enctype="multipart/form-data">
    <label>Kategori:</label>
    <select name="category">
      <option value="Offroad">Offroad</option>
      <option value="Rafting">Rafting</option>
      <option value="Flying Fox">Flying Fox</option>
      <option value="Team Building">Team Building</option>
      <option value="ATV">ATV</option>
      <option value="Paint Ball">Paint Ball</option>
    </select>

    <label>Pilih Gambar:</label>
    <input type="file" name="file" required>

    <button type="submit">Upload</button>
  </form>

  <h2>Daftar Gambar Galeri</h2>
  <div class="gallery-list">
    <?php
      $jsonFile = 'data-galeri.json';
      $data = file_exists($jsonFile) ? json_decode(file_get_contents($jsonFile), true) : [];

      foreach ($data as $i => $item): ?>
        <div class="gallery-item">
          <img src="<?php echo $item['src']; ?>" alt="<?php echo $item['kategori']; ?>">
          <div>
            <strong><?php echo $item['kategori']; ?></strong><br>
            <small><?php echo $item['src']; ?></small>
            <div class="actions">
              <form method="POST" action="upload.php">
                <input type="hidden" name="delete" value="<?php echo $item['src']; ?>">
                <button onclick="return confirm('Yakin hapus gambar ini?')">🗑️ Hapus</button>
              </form>
              <form method="GET" action="edit-galeri.php">
                <input type="hidden" name="src" value="<?php echo $item['src']; ?>">
                <button>Edit</button>
              </form>
            </div>
          </div>
        </div>
    <?php endforeach; ?>
  </div>
</body>
</html>
