<?php
header('Content-Type: application/json');
include $_SERVER["DOCUMENT_ROOT"] . "/project_nextLv/inc/dbcon.php";

$user_id = trim($_GET['user_id'] ?? '');

if ($user_id === '') {
  echo json_encode(['status' => 'invalid', 'message' => '아이디가 전달되지 않았습니다.']);
  exit;
}

$stmt = $mysqli->prepare("SELECT user_id FROM users WHERE user_id = ?");
if (!$stmt) {
  echo json_encode(['status' => 'error', 'message' => '쿼리 준비 실패']);
  exit;
}

$stmt->bind_param("s", $user_id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows > 0) {
  echo json_encode(['status' => 'taken']);
} else {
  echo json_encode(['status' => 'available']);
}
?>