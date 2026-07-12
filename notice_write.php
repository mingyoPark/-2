<?php
include $_SERVER["DOCUMENT_ROOT"] . "/project_nextLv/inc/header.php";

// 관리자 체크
if (!isset($_SESSION['UID'])) {
    echo "<script>alert('로그인이 필요합니다.'); location.href='/project_nextLv/member/login.php';</script>";
    exit;
}

$uid = $_SESSION['UID'];
$check_admin = $mysqli->query("SELECT role FROM users WHERE user_id = '$uid'") or die($mysqli->error);
$row = $check_admin->fetch_object();
if ($row->role !== 'admin') {
    echo "<script>alert('관리자만 작성 가능합니다.'); location.href='/project_nextLv/index.php';</script>";
    exit;
}
?>

<!-- ✅ 타이틀 -->
<div class="page-title-bar">📢 공지사항 작성</div>

<form action="/project_nextLv/notice_write_ok.php" method="post">
  <div class="mb-3">
    <label class="form-label">제목</label>
    <input type="text" name="title" class="form-control" required>
  </div>
  <div class="mb-3">
    <label class="form-label">내용</label>
    <textarea name="content" class="form-control" rows="8" required></textarea>
  </div>
  <div class="text-end">
    <button type="submit" class="btn btn-primary">등록</button>
    <a href="/project_nextLv/notice.php" class="btn btn-secondary">취소</a>
  </div>
</form>

<?php include $_SERVER["DOCUMENT_ROOT"] . "/project_nextLv/inc/footer.php"; ?>