<?php
include $_SERVER["DOCUMENT_ROOT"] . "/project_nextLv/inc/dbcon.php";
session_start();

// 관리자만 삭제 가능
if (!isset($_SESSION['UID']) || $_SESSION['ROLE'] !== 'admin') {
  echo "<script>alert('관리자만 삭제할 수 있습니다.'); history.back();</script>";
  exit;
}

$notice_id = $_GET['id'] ?? null;
if (!$notice_id) {
  echo "<script>alert('잘못된 접근입니다.'); history.back();</script>";
  exit;
}

// 삭제 실행
$delete_result = $mysqli->query("DELETE FROM notice WHERE notice_id = $notice_id");

if ($delete_result) {
  echo "<script>alert('공지사항이 삭제되었습니다.'); location.href='/project_nextLv/notice.php';</script>";
} else {
  echo "<script>alert('삭제에 실패했습니다.'); history.back();</script>";
}
?>