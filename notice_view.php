<?php
include $_SERVER["DOCUMENT_ROOT"] . "/project_nextLv/inc/header.php";
include $_SERVER["DOCUMENT_ROOT"] . "/project_nextLv/inc/dbcon.php";

$notice_id = $_GET['id'] ?? null;
if (!$notice_id) {
  echo "<script>alert('잘못된 접근입니다.'); history.back();</script>";
  exit;
}

// 조회수 증가
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  $mysqli->query("UPDATE notice SET views = views + 1 WHERE notice_id = $notice_id");
}

// 공지 데이터 조회
$result = $mysqli->query("SELECT * FROM notice WHERE notice_id = $notice_id") or die($mysqli->error);
$notice = $result->fetch_object();
if (!$notice) {
  echo "<script>alert('존재하지 않는 공지입니다.'); history.back();</script>";
  exit;
}

$is_admin = isset($_SESSION['ROLE']) && $_SESSION['ROLE'] === 'admin';

// 수정 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $is_admin) {
  $new_title = trim($_POST['title']);
  $new_content = trim($_POST['content']);

  $stmt = $mysqli->prepare("UPDATE notice SET title = ?, content = ? WHERE notice_id = ?");
  $stmt->bind_param("ssi", $new_title, $new_content, $notice_id);
  $stmt->execute();

  echo "<script>alert('수정되었습니다.'); location.href='notice_view.php?id=$notice_id';</script>";
  exit;
}
?>

<div class="container-fluid px-5 py-4">
  <?php if ($is_admin && isset($_GET['edit']) && $_GET['edit'] === '1'): ?>
    <!-- ✅ 수정 폼 -->
    <h2 class="fw-bold mb-4 border-bottom pb-2">공지사항 수정</h2>
    <form method="post" action="notice_view.php?id=<?= $notice_id ?>">
      <div class="mb-3">
        <label class="form-label">제목</label>
        <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($notice->title) ?>" required>
      </div>
      <div class="mb-3">
        <label class="form-label">내용</label>
        <textarea name="content" class="form-control" rows="8" required><?= htmlspecialchars($notice->content) ?></textarea>
      </div>
      <div class="text-end">
        <button type="submit" class="btn btn-primary">수정 완료</button>
        <a href="notice_view.php?id=<?= $notice_id ?>" class="btn btn-secondary">취소</a>
      </div>
    </form>

  <?php else: ?>
    <!-- ✅ 일반 보기 -->
    <h2 class="fw-bold mb-4 border-bottom pb-2"><?= htmlspecialchars($notice->title) ?></h2>
    <div class="text-muted mb-4">
      <strong>작성자:</strong> <?= htmlspecialchars($notice->admin_id ?? '-') ?> |
      <strong>작성일:</strong> <?= $notice->created_at ?> |
      <strong>조회수:</strong> <?= $notice->views ?>
    </div>

    <div class="border rounded p-4 mb-5 bg-light" style="min-height: 200px; white-space: pre-wrap;">
      <?= nl2br(htmlspecialchars($notice->content)) ?>
    </div>

    <div class="text-end">
      <a href="/project_nextLv/notice.php" class="btn btn-secondary">목록</a>
      <?php if ($is_admin): ?>
        <a href="notice_view.php?id=<?= $notice_id ?>&edit=1" class="btn btn-primary">수정</a>
        <a href="notice_delete.php?id=<?= $notice_id ?>" class="btn btn-danger" onclick="return confirm('공지사항을 삭제하시겠습니까?')">삭제</a>
      <?php endif; ?>
    </div>
  <?php endif; ?>
</div>

<?php include $_SERVER["DOCUMENT_ROOT"] . "/project_nextLv/inc/footer.php"; ?>