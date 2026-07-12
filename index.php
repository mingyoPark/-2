<?php
if (session_status() === PHP_SESSION_NONE) session_start();
include $_SERVER["DOCUMENT_ROOT"] . "/project_nextLv/inc/dbcon.php";
include $_SERVER["DOCUMENT_ROOT"] . "/project_nextLv/inc/header.php";
?>

<style>
  .hero-banner {
    background: linear-gradient(135deg, #4A3AFF, #8A7CFF);
    color: white;
    padding: 80px 20px;
    text-align: center;
    border-radius: 20px;
    margin-bottom: 40px;
  }

  .service-box {
    border: 1px solid #ddd;
    border-radius: 10px;
    padding: 30px;
    text-align: center;
    transition: all 0.3s ease;
    background-color: white;
    box-shadow: 0 2px 6px rgba(0,0,0,0.05);
  }

  .service-box:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.1);
  }

  .service-box h4 {
    color: #4A3AFF;
    font-weight: bold;
  }
</style>

<div class="container py-4">
  <!-- ✅ 상단 배너 -->
  <div class="hero-banner">
    <h1 class="display-5 fw-bold">전세사기 피해, 함께 예방해요</h1>
    <p class="lead">체크리스트, 보험 추천, 피해사례 공유로 안전한 계약을 도와드립니다.</p>
    <a href="/project_nextLv/board.php" class="btn btn-light btn-lg mt-3">피해 사례 보러가기</a>
  </div>

  <!-- ✅ 서비스 안내 4박스 -->
  <div class="row text-center g-4 mb-5">
    <div class="col-md-3">
      <div class="service-box">
        <h4>📋 체크리스트</h4>
        <p>계약 전·중·후 단계별 확인 항목 제공</p>
        <a href="/project_nextLv/checklist.php" class="btn btn-outline-primary btn-sm">바로가기</a>
      </div>
    </div>
    <div class="col-md-3">
      <div class="service-box">
        <h4>🛡️ 보험 추천</h4>
        <p>입력 정보 기반 전세보증보험 비교 추천</p>
        <a href="/project_nextLv/insurance.php" class="btn btn-outline-primary btn-sm">추천받기</a>
      </div>
    </div>
    <div class="col-md-3">
      <div class="service-box">
        <h4>📢 공지사항</h4>
        <p>법률 변경, 서비스 업데이트 등 안내</p>
        <a href="/project_nextLv/notice.php" class="btn btn-outline-primary btn-sm">공지 보기</a>
      </div>
    </div>
    <div class="col-md-3">
      <div class="service-box">
        <h4>⚖️ 법률상담</h4>
        <p>무료 법률 자문 예약으로 문제 해결</p>
        <a href="#" class="btn btn-outline-primary btn-sm">예약하기</a>
      </div>
    </div>
  </div>

  <!-- ✅ 최신 공지사항 3개 -->
  <h4 class="mb-3">📌 최근 공지사항</h4>
  <ul class="list-group mb-5">
    <?php
    $notice_result = $mysqli->query("SELECT * FROM notice ORDER BY notice_id DESC LIMIT 3");
    while($n = $notice_result->fetch_object()):
    ?>
      <li class="list-group-item d-flex justify-content-between">
        <a href="/project_nextLv/notice_view.php?nid=<?= $n->notice_id ?>" class="text-decoration-none"><?= htmlspecialchars($n->title) ?></a>
        <span class="text-muted"><?= substr($n->created_at, 0, 10) ?></span>
      </li>
    <?php endwhile; ?>
  </ul>

  <!-- ✅ 빠른 이동 버튼 -->
<div class="text-center">
  <a href="/project_nextLv/board.php" class="btn btn-primary me-2">📂 피해사례 보기</a>

  <?php if (isset($_SESSION['UID'])): ?>
    <a href="/project_nextLv/write.php" class="btn btn-outline-secondary me-2">✏️ 사례 작성하기</a>
    <a href="/project_nextLv/checklist.php" class="btn btn-outline-success">📋 체크리스트</a>
  <?php else: ?>
    <a href="/project_nextLv/member/login.php" class="btn btn-outline-secondary me-2">✏️ 사례 작성하기</a>
    <a href="/project_nextLv/member/login.php" class="btn btn-outline-success">📋 체크리스트</a>
  <?php endif; ?>
</div>


<?php include $_SERVER["DOCUMENT_ROOT"] . "/project_nextLv/inc/footer.php"; ?>
