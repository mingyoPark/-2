<?php
include $_SERVER["DOCUMENT_ROOT"] . "/project_nextLv/inc/header.php";
include $_SERVER["DOCUMENT_ROOT"] . "/project_nextLv/inc/dbcon.php";

// 관리자 여부 판단
$is_admin = false;
if (isset($_SESSION['UID'])) {
    $uid = $_SESSION['UID'];
    $check_admin = $mysqli->query("SELECT role FROM users WHERE user_id = '$uid'") or die($mysqli->error);
    if ($check_admin && $row = $check_admin->fetch_object()) {
        $is_admin = $row->role === 'admin';
    }
}

// 페이지네이션 설정
$page = max(1, (int)($_GET['page'] ?? 1));
$limit = 5;
$offset = ($page - 1) * $limit;

// 총 공지 수 조회
$count_result = $mysqli->query("SELECT COUNT(*) AS cnt FROM notice") or die($mysqli->error);
$total_notices = $count_result->fetch_object()->cnt;
$total_pages = ceil($total_notices / $limit);

// 공지사항 목록 불러오기
$result = $mysqli->query("SELECT * FROM notice ORDER BY notice_id DESC LIMIT $offset, $limit") or die($mysqli->error);
$notices = [];
while ($row = $result->fetch_object()) {
    $notices[] = $row;
}

// 페이지 버튼 범위 설정
$btn_count = 5;
$start_page = max(1, $page - floor($btn_count / 2));
$end_page = min($total_pages, $start_page + $btn_count - 1);
if ($end_page - $start_page < $btn_count - 1) {
    $start_page = max(1, $end_page - $btn_count + 1);
}
?>

<!-- ✅ 타이틀 바 -->
<div class="page-title-bar">공지사항</div>

<!-- 관리자만 글쓰기 버튼 -->
<div class="d-flex justify-content-end mb-3">
  <?php if ($is_admin): ?>
    <a href="/project_nextLv/notice_write.php" class="btn btn-primary">공지 작성</a>
  <?php endif; ?>
</div>

<!-- 공지사항 테이블 -->
<table class="table table-striped">
  <thead class="table-light">
    <tr>
      <th>번호</th>
      <th>제목</th>
      <th>작성자</th>
      <th>조회수</th>
      <th>작성일</th>
    </tr>
  </thead>
  <tbody>
    <?php if (!empty($notices)):
      $num = $total_notices - $offset;
      foreach ($notices as $n): ?>
      <tr>
        <td><?= $num-- ?></td>
        <td><a href="/project_nextLv/notice_view.php?id=<?= $n->notice_id ?>"><?= htmlspecialchars($n->title) ?></a></td>
        <td><?= htmlspecialchars($n->writer ?? $n->admin_id ?? '-') ?></td>
        <td><?= $n->views ?? 0 ?></td>
        <td><?= $n->created_at ?></td>
      </tr>
    <?php endforeach; else: ?>
      <tr><td colspan="5" class="text-center">공지사항이 없습니다.</td></tr>
    <?php endif; ?>
  </tbody>
</table>

<!-- 페이지네이션 -->
<?php if ($total_pages > 1): ?>
  <nav class="mt-4">
    <ul class="pagination justify-content-center">
      <?php if ($page > 1): ?>
        <li class="page-item"><a class="page-link" href="?page=<?= $page - 1 ?>">이전</a></li>
      <?php endif; ?>

      <?php for ($p = $start_page; $p <= $end_page; $p++): ?>
        <li class="page-item <?= $p == $page ? 'active' : '' ?>">
          <a class="page-link" href="?page=<?= $p ?>"><?= $p ?></a>
        </li>
      <?php endfor; ?>

      <?php if ($page < $total_pages): ?>
        <li class="page-item"><a class="page-link" href="?page=<?= $page + 1 ?>">다음</a></li>
      <?php endif; ?>
    </ul>
  </nav>
<?php endif; ?>

<?php include $_SERVER["DOCUMENT_ROOT"] . "/project_nextLv/inc/footer.php"; ?>