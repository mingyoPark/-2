<?php
include $_SERVER["DOCUMENT_ROOT"] . "/project_nextLv/inc/header.php";
include $_SERVER["DOCUMENT_ROOT"] . "/project_nextLv/inc/dbcon.php";

$pid = $_GET['pid'] ?? 0;
if (!$pid) {
  echo "<script>alert('잘못된 접근입니다.'); location.href='index.php';</script>";
  exit;
}

// 조회수 증가
$mysqli->query("UPDATE post SET views = views + 1 WHERE post_id = $pid");

// 게시글 조회
$result = $mysqli->query("SELECT * FROM post WHERE post_id = $pid") or die($mysqli->error);
$post = $result->fetch_assoc();
if (!$post) {
  echo "<script>alert('해당 글을 찾을 수 없습니다.'); location.href='index.php';</script>";
  exit;
}

// 지역 출력
$full_region = trim(($post['region'] ?? '') . ' ' . ($post['sub_region'] ?? ''));
$is_owner = isset($_SESSION['UID']) && $_SESSION['UID'] === $post['author_id'];
?>

<div class="container-fluid px-5 py-4">
  <!-- 제목 -->
  <h2 class="fw-bold mb-4 border-bottom pb-2"><?= htmlspecialchars($post['title']) ?></h2>

  <!-- 작성 정보 -->
  <div class="text-muted mb-4">
    <strong>작성자:</strong> <?= htmlspecialchars($post['author_id']) ?> |
    <strong>지역:</strong> <?= htmlspecialchars($full_region ?: '-') ?> |
    <strong>사기유형:</strong> <?= htmlspecialchars($post['fraud_type'] ?? '-') ?> |
    <strong>작성일:</strong> <?= $post['created_at'] ?> |
    <strong>조회수:</strong> <?= $post['views'] ?>
  </div>

  <!-- 본문 -->
  <div class="border rounded p-4 mb-5 bg-light" style="min-height: 200px; white-space: pre-wrap;">
    <?= htmlspecialchars($post['content']) ?>
  </div>

  <!-- 버튼 -->
  <div class="text-end mb-5">
    <a href="index.php" class="btn btn-secondary">목록</a>
    <?php if ($is_owner): ?>
      <a href="write.php?pid=<?= $post['post_id'] ?>" class="btn btn-primary">수정</a>
      <a href="delete.php?pid=<?= $post['post_id'] ?>" class="btn btn-danger" onclick="return confirm('정말 삭제하시겠습니까?')">삭제</a>
    <?php endif; ?>
  </div>

  <!-- 댓글 작성 -->
  <?php if (isset($_SESSION['UID'])): ?>
    <form action="/project_nextLv/comment_ok.php" method="post" class="mb-4">
      <input type="hidden" name="post_id" value="<?= $post['post_id'] ?>">
      <div class="mb-2">
        <textarea name="content" class="form-control" rows="3" placeholder="댓글을 입력하세요" required></textarea>
      </div>
      <div class="text-end">
        <button type="submit" class="btn btn-sm btn-outline-primary">댓글 등록</button>
      </div>
    </form>
  <?php endif; ?>

  <!-- 댓글 목록 -->
  <h5 class="mb-3">💬 댓글</h5>
  <?php
  $comment_result = $mysqli->query("SELECT * FROM comment WHERE post_id = {$post['post_id']} ORDER BY created_at ASC");
  if ($comment_result->num_rows === 0): ?>
    <div class="text-muted mb-4">등록된 댓글이 없습니다.</div>
  <?php else:
    while ($cmt = $comment_result->fetch_assoc()): ?>
      <div class="border p-3 rounded mb-3" id="comment-box-<?= $cmt['comment_id'] ?>">
        <div class="mb-1 d-flex justify-content-between">
          <div>
            <strong><?= htmlspecialchars($cmt['author_id']) ?></strong>
            <span class="text-muted small"> | <?= $cmt['created_at'] ?></span>
            <?php if (!empty($cmt['updated_at'])): ?>
              <span class="text-muted small">(수정됨)</span>
            <?php endif; ?>
          </div>
          <?php if (isset($_SESSION['UID']) && $_SESSION['UID'] === $cmt['author_id']): ?>
            <div class="small">
              <a href="javascript:void(0);" class="me-2 text-decoration-none" onclick="enableEdit(<?= $cmt['comment_id'] ?>)">✏ 수정</a>
              <a href="comment_delete.php?cid=<?= $cmt['comment_id'] ?>&pid=<?= $post['post_id'] ?>" class="text-danger text-decoration-none" onclick="return confirm('댓글을 삭제하시겠습니까?')">🗑 삭제</a>
            </div>
          <?php endif; ?>
        </div>

        <!-- 원본 댓글 표시 -->
        <div id="comment-content-<?= $cmt['comment_id'] ?>">
          <?= nl2br(htmlspecialchars($cmt['content'])) ?>
        </div>

        <!-- 수정 폼 (초기에는 숨김) -->
        <form method="post" action="comment_edit_ok.php" class="d-none mt-2" id="edit-form-<?= $cmt['comment_id'] ?>">
          <input type="hidden" name="cid" value="<?= $cmt['comment_id'] ?>">
          <input type="hidden" name="post_id" value="<?= $post['post_id'] ?>">
          <textarea name="content" class="form-control mb-2" rows="3"><?= htmlspecialchars($cmt['content']) ?></textarea>
          <div class="text-end">
            <button type="submit" class="btn btn-sm btn-success">수정 완료</button>
            <button type="button" class="btn btn-sm btn-secondary" onclick="cancelEdit(<?= $cmt['comment_id'] ?>)">취소</button>
          </div>
        </form>
      </div>
  <?php endwhile; endif; ?>
</div>

<script>
function enableEdit(cid) {
  document.getElementById(`comment-content-${cid}`).classList.add('d-none');
  document.getElementById(`edit-form-${cid}`).classList.remove('d-none');
}

function cancelEdit(cid) {
  document.getElementById(`edit-form-${cid}`).classList.add('d-none');
  document.getElementById(`comment-content-${cid}`).classList.remove('d-none');
}
</script>

<?php include $_SERVER["DOCUMENT_ROOT"] . "/project_nextLv/inc/footer.php"; ?>