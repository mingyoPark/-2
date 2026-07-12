<?php
include $_SERVER["DOCUMENT_ROOT"] . "/project_nextLv/inc/dbcon.php";
session_start();

if (!isset($_SESSION['UID'])) {
  echo "<script>alert('로그인이 필요합니다.'); history.back();</script>";
  exit;
}

$post_id = $_POST['post_id'];
$content = trim($_POST['content']);
$author_id = $_SESSION['UID'];

if (!$content) {
  echo "<script>alert('댓글 내용을 입력해주세요.'); history.back();</script>";
  exit;
}

$stmt = $mysqli->prepare("INSERT INTO comment (post_id, author_id, content) VALUES (?, ?, ?)");
$stmt->bind_param("iss", $post_id, $author_id, $content);
$stmt->execute();

echo "<script>location.href='/project_nextLv/view.php?pid=$post_id';</script>";
?>