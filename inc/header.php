<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

include $_SERVER["DOCUMENT_ROOT"] . "/project_nextLv/inc/dbcon.php";

// 자동 로그인 (옵션)
if (!isset($_SESSION['UID']) && isset($_COOKIE['user_token'])) {
  $token = $_COOKIE['user_token'];
  $check = $mysqli->query("SELECT * FROM users WHERE token = '$token'");
  if ($check && $check->num_rows > 0) {
    $u = $check->fetch_assoc();
    $_SESSION['UID'] = $u['user_id'];
    $_SESSION['UNAME'] = $u['name'];
    $_SESSION['ROLE'] = $u['role'];
  }
}

// 현재 페이지 확인
$current = basename($_SERVER['PHP_SELF']);
function activeMenu($page) {
  global $current;
  return $current === $page ? 'text-white bg-primary rounded px-3 py-1' : 'text-dark';
}
?>

<!doctype html>
<html lang="ko">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>전세사기 커뮤니티</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="/project_nextLv/style.css">

  <style>
    .header-nav a {
      text-decoration: none;
      margin: 0 1rem;
    }
    .icon-primary svg {
      color: #4A3AFF;
    }
  </style>
</head>

<body>
  <!-- ✅ 헤더 -->
  <div class="container d-flex justify-content-between align-items-center py-3 border-bottom">
    <!-- 로고 -->
    <a href="/project_nextLv/index.php" class="d-flex align-items-center text-decoration-none text-dark icon-primary">
      <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-house-fill me-2" viewBox="0 0 16 16">
        <path d="M8.707 1.5a1 1 0 0 0-1.414 0L.646 8.146a.5.5 0 0 0 .708.708L8 2.207l6.646 6.647a.5.5 0 0 0 .708-.708L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293z"/>
        <path d="m8 3.293 6 6V13.5a1.5 1.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 2 13.5V9.293z"/>
      </svg>
      <span class="fs-5 fw-bold">전세사기 커뮤니티</span>
    </a>

    <!-- 네비게이션 -->
    <nav class="header-nav d-flex justify-content-center flex-grow-1">
      <a class="nav-link <?= activeMenu('notice.php') ?>" href="/project_nextLv/notice.php">공지사항</a>
      <a class="nav-link <?= activeMenu('board.php') ?>" href="/project_nextLv/board.php">게시판</a>
      <a class="nav-link <?= activeMenu('insurance.php') ?>" href="/project_nextLv/insurance.php">전세 보증</a>
      <a class="nav-link <?= activeMenu('checklist.php') ?>" href="/project_nextLv/checklist.php">체크리스트</a>
    </nav>

    <!-- 사용자 상태 -->
    <div class="d-flex align-items-center">
      <a href="#" class="btn btn-outline-primary btn-sm me-2">무료 법률 자문</a>

      <?php if (isset($_SESSION['UID'])): ?>
        <span class="me-2 fw-bold">👤 <?= $_SESSION['UNAME'] ?> 님</span>
        <a href="/project_nextLv/member/logout.php" class="btn btn-outline-danger btn-sm">로그아웃</a>
      <?php else: ?>
        <a href="/project_nextLv/member/login.php" class="btn btn-outline-secondary btn-sm me-2">로그인</a>
        <a href="/project_nextLv/member/signup.php" class="btn btn-primary btn-sm">회원가입</a>
      <?php endif; ?>
    </div>
  </div>

  <!-- ✅ 본문 시작 -->
  <div class="container mb-5">