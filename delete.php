<?php
include $_SERVER["DOCUMENT_ROOT"] . "/project_nextLv/inc/dbcon.php";
session_start();
$pid = $_GET['pid'] ?? 0;
if (!$pid || !isset($_SESSION['UID'])) {
  echo "<script>alert('잘못된 접근입니다.'); location.href='index.php';</script>";
  exit;
}

$result = $mysqli->query("SELECT * FROM post WHERE post_id = $pid") or die($mysqli->error);
$post = $result->fetch_assoc();

if ($_SESSION['UID'] !== $post['author_id']) {
  echo "<script>alert('본인 글만 삭제할 수 있습니다.'); history.back();</script>";
  exit;
}

$mysqli->query("DELETE FROM post WHERE post_id = $pid");
echo "<script>alert('삭제되었습니다.'); location.href='index.php';</script>";
exit;
