<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $data = file_get_contents('php://input');
  file_put_contents('paket-list.json', $data);
  echo json_encode(['status' => 'success']);
} else {
  echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
}
?>
