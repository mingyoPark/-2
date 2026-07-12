<?php
include $_SERVER["DOCUMENT_ROOT"] . "/project_nextLv/inc/dbcon.php";
session_start();

// 관리자 검증
if (!isset($_SESSION['UID'])) {
    echo "<script>alert('로그인이 필요합니다.'); location.href='/project_nextLv/member/login.php';</script>";
    exit;
}

$uid = $_SESSION['UID'];
$check_admin = $mysqli->query("SELECT role FROM users WHERE user_id = '$uid'") or die($mysqli->error);
$row = $check_admin->fetch_object();
if ($row->role !== 'admin') {
    echo "<script>alert('관리자만 등록할 수 있습니다.'); location.href='/project_nextLv/index.php';</script>";
    exit;
}

// 입력값 받기
$title = trim($_POST['title'] ?? '');
$content = trim($_POST['content'] ?? '');

if (!$title || !$content) {
    echo "<script>alert('제목과 내용을 모두 입력해주세요.'); history.back();</script>";
    exit;
}

// DB 등록
$stmt = $mysqli->prepare("INSERT INTO notice (title, content, admin_id) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $title, $content, $uid);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo "<script>alert('공지사항이 등록되었습니다.'); location.href='/project_nextLv/notice.php';</script>";
} else {
    echo "<script>alert('등록에 실패했습니다.'); history.back();</script>";
}
?>