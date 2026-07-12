<?php
include $_SERVER["DOCUMENT_ROOT"] . "/project_nextLv/inc/dbcon.php";
session_start();

$cid = $_POST['cid'] ?? 0;
$post_id = $_POST['post_id'] ?? 0;
$content = trim($_POST['content'] ?? '');

if (!$cid || !$post_id || !$content) {
  echo "<script>alert('잘못된 요청입니다.'); history.back();</script>";
  exit;
}

// 댓글 소유자 확인
$result = $mysqli->query("SELECT * FROM comment WHERE comment_id = $cid") or die($mysqli->error);
$comment = $result->fetch_assoc();

if (!$comment || $_SESSION['UID'] !== $comment['author_id']) {
  echo "<script>alert('수정 권한이 없습니다.'); history.back();</script>";
  exit;
}

// 수정 쿼리 실행
$stmt = $mysqli->prepare("UPDATE comment SET content = ? WHERE comment_id = ?");
$stmt->bind_param("si", $content, $cid);
$stmt->execute();

echo "<script>location.href='/project_nextLv/view.php?pid=$post_id';</script>";