<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$jsonFile = 'data-blog.json';
$posts = file_exists($jsonFile) ? json_decode(file_get_contents($jsonFile), true) : [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id = $_POST['id'] ?: date('YmdHis');
  $judul = $_POST['judul'];
  $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $_POST['slug'])));
  $kategori = $_POST['kategori'];
  $konten = $_POST['konten'];

  $gambar = '';
  if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
    $ext = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
    $fileName = "assets/blog/" . $slug . "-" . time() . "." . $ext;
    move_uploaded_file($_FILES['gambar']['tmp_name'], $fileName);
    $gambar = $fileName;
  } else if (!empty($_POST['gambar_lama'])) {
    $gambar = $_POST['gambar_lama'];
  }

  $found = false;
  foreach ($posts as &$post) {
    if ($post['id'] === $id) {
      $post = compact('id', 'judul', 'slug', 'kategori', 'konten', 'gambar');
      $found = true;
      break;
    }
  }
  if (!$found) {
    $posts[] = compact('id', 'judul', 'slug', 'kategori', 'konten', 'gambar');
  }

  file_put_contents($jsonFile, json_encode($posts, JSON_PRETTY_PRINT));
  header('Location: admin-post.php');
  exit;
}

if (isset($_GET['delete'])) {
  $posts = array_filter($posts, fn($p) => $p['id'] !== $_GET['delete']);
  file_put_contents($jsonFile, json_encode(array_values($posts), JSON_PRETTY_PRINT));
  header('Location: admin-post.php');
  exit;
}

$editPost = null;
if (isset($_GET['edit'])) {
  foreach ($posts as $p) {
    if ($p['id'] === $_GET['edit']) {
      $editPost = $p;
      break;
    }
  }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Admin Blog</title>
  <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
  <style>
    body { font-family: sans-serif; padding: 2rem; max-width: 800px; margin: auto; }
    input, select, textarea { width: 100%; padding: 0.5rem; margin-bottom: 1rem; }
    .post { border: 1px solid #ddd; padding: 1rem; border-radius: 6px; margin-bottom: 1rem; }
    .actions a { margin-right: 1rem; }
    img.thumb { max-width: 100px; display: block; margin-top: 0.5rem; }
  </style>
</head>
<body>
  <h1>📚 Admin Blog Post</h1>
  <form method="POST" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?= $editPost['id'] ?? '' ?>">
    <label>Judul</label>
    <input type="text" name="judul" required value="<?= $editPost['judul'] ?? '' ?>">

    <label>Slug (URL)</label>
    <input type="text" name="slug" required value="<?= $editPost['slug'] ?? '' ?>">

    <label>Kategori</label>
    <input type="text" name="kategori" value="<?= $editPost['kategori'] ?? '' ?>">

    <label>Gambar (opsional)</label>
    <input type="file" name="gambar">
    <?php if (!empty($editPost['gambar'])): ?>
      <img class="thumb" src="<?= $editPost['gambar'] ?>">
      <input type="hidden" name="gambar_lama" value="<?= $editPost['gambar'] ?>">
    <?php endif; ?>

    <label>Konten</label>
    <textarea name="konten" id="konten" rows="10"><?= $editPost['konten'] ?? '' ?></textarea>

    <button type="submit">💾 Simpan Postingan</button>
  </form>

  <script>
    ClassicEditor
      .create(document.querySelector('#konten'))
      .catch(error => console.error(error));
  </script>

  <hr>
  <h2>📝 Daftar Postingan</h2>
  <?php foreach ($posts as $post): ?>
    <div class="post">
      <strong><?= $post['judul'] ?></strong> (<?= $post['kategori'] ?>)<br>
      <a class="actions" href="admin-post.php?edit=<?= $post['id'] ?>">✏️ Edit</a>
      <a class="actions" href="admin-post.php?delete=<?= $post['id'] ?>" onclick="return confirm('Hapus postingan ini?')">🗑️ Hapus</a>
    </div>
  <?php endforeach; ?>
</body>
</html>
