<?php
include $_SERVER["DOCUMENT_ROOT"] . "/project_nextLv/inc/header.php";
include $_SERVER["DOCUMENT_ROOT"] . "/project_nextLv/inc/dbcon.php";

// 정렬 조건
$order = $_GET['order'] ?? 'post_id';
$allowed = ['post_id', 'title', 'author_id', 'created_at', 'views'];
$order_by = in_array($order, $allowed) ? $order : 'post_id';

// 검색 처리
$search = $_GET['search'] ?? '';
$where = $search ? "WHERE title LIKE '%$search%' OR author_id LIKE '%$search%'" : '';

// 페이지네이션
$page = max(1, (int)($_GET['page'] ?? 1));
$limit = 5;
$offset = ($page - 1) * $limit;

$count_result = $mysqli->query("SELECT COUNT(*) AS cnt FROM post $where") or die($mysqli->error);
$total_posts = $count_result->fetch_object()->cnt;
$total_pages = ceil($total_posts / $limit);

$btn_count = 5;
$start_page = max(1, $page - floor($btn_count / 2));
$end_page = min($total_pages, $start_page + $btn_count - 1);
if ($end_page - $start_page < $btn_count - 1) {
  $start_page = max(1, $end_page - $btn_count + 1);
}

// 게시글 목록 가져오기
$result = $mysqli->query("SELECT * FROM post $where ORDER BY $order_by DESC LIMIT $offset, $limit") or die($mysqli->error);
$rsc = [];
while ($rs = $result->fetch_object()) {
  $rsc[] = $rs;
}

// 글쓰기 버튼 출력 함수
function printWriteButton() {
  if (isset($_SESSION['UID'])) {
    echo '<a href="/project_nextLv/write.php" class="btn btn-primary">글쓰기</a>';
  } else {
    echo '<a href="/project_nextLv/member/login.php" class="btn btn-outline-secondary">로그인 후 글쓰기</a>';
  }
}
?>

<div class="page-title-bar">게시판</div>

<!-- 정렬 버튼 -->
<div class="d-flex justify-content-between mb-3">
  <div class="btn-group" role="group">
    <a href="?order=views" class="btn <?= $order === 'views' ? 'btn-primary' : 'btn-outline-secondary' ?>">조회순</a>
    <a href="?order=post_id" class="btn <?= $order === 'post_id' ? 'btn-primary' : 'btn-outline-secondary' ?>">최신순</a>
  </div>
  <?php printWriteButton(); ?>
</div>

<table class="table table-striped">
  <thead class="table-light">
    <tr>
      <th>번호</th>
      <th>제목</th>
      <th>작성자</th>
      <th>지역</th>
      <th>사기유형</th>
      <th>조회수</th>
      <th>작성일</th>
    </tr>
  </thead>
  <tbody>
    <?php if (!empty($rsc)):
      $num = $total_posts - $offset;
      foreach ($rsc as $r): ?>
        <tr>
          <td><?= $num-- ?></td>
          <td><a href="/project_nextLv/view.php?pid=<?= $r->post_id ?>"><?= htmlspecialchars($r->title) ?></a></td>
          <td><?= htmlspecialchars($r->author_id) ?></td>
          <td><?= htmlspecialchars($r->region ?? '-') ?></td>
          <td><?= htmlspecialchars($r->fraud_type ?? '-') ?></td>
          <td><?= $r->views ?? 0 ?></td>
          <td><?= $r->created_at ?></td>
        </tr>
      <?php endforeach; ?>
    <?php else: ?>
      <tr><td colspan="7" class="text-center">작성된 게시글이 없습니다.</td></tr>
    <?php endif; ?>
  </tbody>
</table>

<!-- 페이지네이션 -->
<?php if ($total_pages > 1): ?>
  <nav class="mt-4">
    <ul class="pagination justify-content-center">

      <!-- 이전 -->
      <?php if ($page > 1): ?>
        <li class="page-item">
          <a class="page-link" href="?page=<?= $page - 1 ?>&order=<?= $order ?>&search=<?= urlencode($search) ?>">이전</a>
        </li>
      <?php endif; ?>

      <!-- 숫자 버튼 -->
      <?php for ($p = $start_page; $p <= $end_page; $p++): ?>
        <li class="page-item <?= $p == $page ? 'active' : '' ?>">
          <a class="page-link" href="?page=<?= $p ?>&order=<?= $order ?>&search=<?= urlencode($search) ?>"><?= $p ?></a>
        </li>
      <?php endfor; ?>

      <!-- 다음 -->
      <?php if ($page < $total_pages): ?>
        <li class="page-item">
          <a class="page-link" href="?page=<?= $page + 1 ?>&order=<?= $order ?>&search=<?= urlencode($search) ?>">다음</a>
        </li>
      <?php endif; ?>
    </ul>
  </nav>
<?php endif; ?>

<!-- 검색창 -->
<form class="row mt-4" method="get">
  <div class="col-md-10">
    <input type="text" name="search" class="form-control" placeholder="제목 또는 작성자 검색" value="<?= htmlspecialchars($search) ?>">
  </div>
  <div class="col-md-2 text-end">
    <button type="submit" class="btn btn-outline-secondary w-100">검색</button>
  </div>
</form>

<!-- 하단 글쓰기 버튼 -->
<div class="text-end mt-3">
  <?php printWriteButton(); ?>
</div>

<?php include $_SERVER["DOCUMENT_ROOT"] . "/project_nextLv/inc/footer.php"; ?>