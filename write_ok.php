<?php
include $_SERVER["DOCUMENT_ROOT"] . "/project_nextLv/inc/dbcon.php";
session_start();

if (!isset($_SESSION['UID'])) {
  echo "<script>alert('회원 전용 게시판입니다.'); location.href='/project_nextLv/index.php';</script>";
  exit;
}

$pid         = $_POST['pid'] ?? null;
$title       = trim($_POST['title'] ?? '');
$content     = trim($_POST['content'] ?? '');
$region      = trim($_POST['region'] ?? '');
$sub_region  = trim($_POST['sub_region'] ?? '');
$fraud_type  = trim($_POST['fraud_type'] ?? '');
$userId      = $_SESSION['UID'];

// 필수값 확인
if (!$title || !$content) {
  echo "<script>alert('제목과 내용을 모두 입력해주세요.'); history.back();</script>";
  exit;
}

if ($pid) {
  // 수정 처리
  $result = $mysqli->query("SELECT * FROM post WHERE post_id = $pid") or die($mysqli->error);
  $rs = $result->fetch_object();

  if ($rs->author_id !== $userId) {
    echo "<script>alert('본인 글이 아니면 수정할 수 없습니다.'); location.href='/project_nextLv/index.php';</script>";
    exit;
  }

  $stmt = $mysqli->prepare("UPDATE post SET title = ?, content = ?, region = ?, sub_region = ?, fraud_type = ? WHERE post_id = ? AND author_id = ?");
  $stmt->bind_param("sssssss", $title, $content, $region, $sub_region, $fraud_type, $pid, $userId);
  $stmt->execute();

  echo "<script>alert('수정되었습니다.'); location.href='/project_nextLv/view.php?pid=$pid';</script>";

} else {
  // 등록 처리
  $stmt = $mysqli->prepare("INSERT INTO post (title, content, region, sub_region, fraud_type, author_id) VALUES (?, ?, ?, ?, ?, ?)");
  $stmt->bind_param("ssssss", $title, $content, $region, $sub_region, $fraud_type, $userId);
  $stmt->execute();

  $new_id = $mysqli->insert_id;

  if ($new_id) {
    echo "<script>alert('등록되었습니다.'); location.href='/project_nextLv/view.php?pid=$new_id';</script>";
  } else {
    echo "<script>alert('글 등록에 실패했습니다.'); history.back();</script>";
  }
}
exit;