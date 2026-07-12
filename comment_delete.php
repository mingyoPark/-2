<?php
include $_SERVER["DOCUMENT_ROOT"] . "/project_nextLv/inc/dbcon.php";
session_start();

$cid = $_GET['cid'] ?? 0;
$post_id = $_GET['pid'] ?? 0;

if (!$cid || !$post_id) {
  echo "<script>alert('잘못된 요청입니다.'); history.back();</script>";
  exit;
}

// 댓글 소유자 확인
$result = $mysqli->query("SELECT * FROM comment WHERE comment_id = $cid") or die($mysqli->error);
$comment = $result->fetch_assoc();

if (!$comment || $_SESSION['UID'] !== $comment['author_id']) {
  echo "<script>alert('삭제 권한이 없습니다.'); history.back();</script>";
  exit;
}

// 삭제 쿼리 실행
$mysqli->query("DELETE FROM comment WHERE comment_id = $cid") or die($mysqli->error);

echo "<script>alert('댓글이 삭제되었습니다.'); location.href='/project_nextLv/view.php?pid=$post_id';</script>";