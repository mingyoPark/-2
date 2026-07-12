<?php include $_SERVER["DOCUMENT_ROOT"] . "/project_nextLv/inc/header.php"; ?>
<div class="page-title-bar">전세 계약 체크리스트</div>

<style>
  .btn-group .btn {
    width: 48px;
  }
  .checklist-buttons {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    gap: 0.5rem;
  }
  .question-section {
    display: none;
  }
  .question-section.active {
    display: block;
  }
</style>

<!-- 탭 버튼 -->
<div class="text-center my-3" id="tab-buttons">
  <button type="button" class="btn btn-outline-primary me-2" id="btn-pre" onclick="showSection('pre')">계약 전</button>
  <button type="button" class="btn btn-outline-primary me-2" id="btn-mid" onclick="showSection('mid')">계약 중</button>
  <button type="button" class="btn btn-outline-primary" id="btn-post" onclick="showSection('post')">계약 후</button>
</div>

<form method="post" action="checklist.php" class="mb-5">

<?php
$question_list = [
  'pre' => [
    ["q_sise", "주변 시세 확인 및 전세가율 계산"],
    ["q_deung", "등기부등본 확인"],
    ["q_hugcheck", "전세보증보험 가입 여부 확인"],
    ["q_building", "건축물대장 확인"]
  ],
  'mid' => [
    ["q_owner_match", "임대인과 등기부등본 상 소유주 일치 여부"],
    ["q_cancel_clause", "계약 해제 조건 명시 여부"],
    ["q_payment", "계약금/잔금 등 지급 조건 확인"],
    ["q_dates", "계약 시작 및 종료일 명확히 기재"]
  ],
  'post' => [
    ["q_resident_check", "전입세대 열람 확인"],
    ["q_report", "전입신고 및 확정일자 받기"],
    ["q_recheck", "등기부등본 재확인"],
    ["q_join_hug", "전세보증보험 가입"],
    ["q_tax_check", "미납국세 열람 (임대인 동의 필요)"]
  ]
];

$section_titles = ['pre' => '📝 계약 전', 'mid' => '✍️ 계약 중', 'post' => '🔒 계약 후'];

foreach ($question_list as $stage => $questions) {
  echo "<div class='question-section' id='section-{$stage}'>";
  echo "<h5 class='mt-4 text-center'>{$section_titles[$stage]}</h5>";

  foreach ($questions as $q) {
    echo '<div class="row align-items-center justify-content-between border-bottom py-3">';
    echo '<div class="col-md-6 text-start">' . $q[1] . '</div>';
    echo '<div class="col-md-6">';
    echo '<div class="checklist-buttons">';
    echo '<div class="btn-group" role="group">';
    echo "<input type='radio' class='btn-check' name='{$q[0]}' value='yes' id='{$q[0]}_yes' required>";
    echo "<label class='btn btn-outline-primary' for='{$q[0]}_yes'>⭕</label>";
    echo "<input type='radio' class='btn-check' name='{$q[0]}' value='maybe' id='{$q[0]}_maybe'>";
    echo "<label class='btn btn-outline-secondary' for='{$q[0]}_maybe'>△</label>";
    echo "<input type='radio' class='btn-check' name='{$q[0]}' value='no' id='{$q[0]}_no'>";
    echo "<label class='btn btn-outline-danger' for='{$q[0]}_no'>❌</label>";
    echo '</div></div></div></div>';
  }

  echo '</div>';
}
?>

<div class="text-center mt-4">
  <button type="submit" class="btn btn-primary px-5">체크 완료</button>
</div>
</form>

<!-- 결과 표시 -->
<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $all_questions = array_merge(...array_values($question_list));
  $score = 0;
  $total = count($all_questions);

  foreach ($all_questions as $q) {
    if (isset($_POST[$q[0]]) && $_POST[$q[0]] === 'yes') {
      $score++;
    }
  }

  echo '<div class="mt-5 text-center">';
  echo '<h4 class="mb-3">🔍 종합 체크리스트 결과</h4>';

  if ($score === $total) {
    echo '<div class="alert alert-success d-inline-block">✅ 모든 항목을 점검하셨습니다!</div>';
  } elseif ($score >= $total * 0.7) {
    echo '<div class="alert alert-warning d-inline-block">⚠ 대부분 점검하셨지만 일부 항목은 다시 확인해 보세요.</div>';
  } else {
    echo '<div class="alert alert-danger d-inline-block">❗ 점검이 충분하지 않습니다. 계약 전 모든 항목을 꼭 확인하세요.</div>';
  }

  echo "<p class='mt-3'>총 <strong>{$total}</strong>개 항목 중 <strong>{$score}</strong>개를 체크했습니다.</p>";
  echo '<a href="' . $_SERVER['PHP_SELF'] . '" class="btn btn-outline-secondary mt-3">다시하기</a>';
  echo '</div>';
}
?>

<script>
function showSection(stage) {
  const allSections = document.querySelectorAll('.question-section');
  allSections.forEach(sec => sec.classList.remove('active'));

  const target = document.getElementById('section-' + stage);
  if (target) target.classList.add('active');

  // 버튼 색상 토글
  ['pre', 'mid', 'post'].forEach(s => {
    const btn = document.getElementById('btn-' + s);
    if (btn) {
      if (s === stage) {
        btn.classList.remove('btn-outline-primary');
        btn.classList.add('btn-primary');
      } else {
        btn.classList.remove('btn-primary');
        btn.classList.add('btn-outline-primary');
      }
    }
  });
}

document.addEventListener('DOMContentLoaded', () => {
  showSection('pre'); // 기본 탭: 계약 전
});
</script>

<?php include $_SERVER["DOCUMENT_ROOT"] . "/project_nextLv/inc/footer.php"; ?>