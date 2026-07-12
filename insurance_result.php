
<?php
session_start();
$_SESSION['insurance_input'] = $_POST;

$deposit = (int)$_POST['deposit'];
$is_homeless = (int)$_POST['is_homeless'];
$target_type = $_POST['target_type'];
$income_level = $_POST['income_level'];
$has_prev = (int)$_POST['has_previous_support'];
$region = $_POST['region'] ?? '';

$company = "SGI 서울보증";
$recommend_reason = "";
$link = "https://www.sgic.co.kr";

if ($is_homeless !== 1) {
  $company = "SGI 서울보증";
  $recommend_reason = "유주택자의 경우 공공 보증은 불가하며, SGI 상품만 가입 가능합니다.";
  $link = "https://www.sgic.co.kr";
} elseif ($has_prev === 1) {
  $company = "HF 한국주택금융공사";
  $recommend_reason = "최근 2년 내 보증지원 이력이 있어 HF로 추천됩니다.";
  $link = "https://www.hf.go.kr";
} elseif ($deposit > 40000) {
  $company = "HF 한국주택금융공사";
  $recommend_reason = "보증금이 4억원을 초과하여 HF로 추천됩니다.";
  $link = "https://www.hf.go.kr";
} elseif (
  $deposit <= 10000 &&
  in_array($target_type, ['청년', '신혼부부']) &&
  $income_level === '중위이하'
) {
  $company = "HUG 주택도시보증공사";
  $recommend_reason = "무주택 청년 또는 신혼부부로 조건이 충족되어 HUG로 추천됩니다.";
  $link = "https://www.khug.or.kr";
} elseif ($deposit <= 30000 && $income_level === '중위초과') {
  $company = "SGI 서울보증";
  $recommend_reason = "보증금 3억원 이하이며 소득이 중위초과여서 SGI로 추천됩니다.";
  $link = "https://www.sgic.co.kr";
} elseif (strpos($region, '서울') !== false) {
  $company = "HF 한국주택금융공사";
  $recommend_reason = "서울 지역이므로 HF로 추천됩니다.";
  $link = "https://www.hf.go.kr";
} else {
  $company = "HUG 또는 SGI";
  $recommend_reason = "조건이 혼합되어 HUG 또는 SGI 모두 선택 가능합니다.";
  $link = "https://www.khug.or.kr";
}

$rate = 0.01;
if ($deposit > 30000) $rate = 0.009;
if ($income_level === '중위이하') $rate = 0.007;
$fee = $deposit * $rate;

// 버튼 강조 클래스 설정
$hug_class = $company === "HUG 주택도시보증공사" || $company === "HUG 또는 SGI" ? "btn-primary" : "btn-outline-secondary";
$sgi_class = $company === "SGI 서울보증" || $company === "HUG 또는 SGI" ? "btn-primary" : "btn-outline-secondary";
$hf_class  = $company === "HF 한국주택금융공사" ? "btn-primary" : "btn-outline-secondary";
?>

<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8">
  <title>전세보증보험 추천 결과</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      padding: 3rem;
      background: linear-gradient(to right, #eef2f3, #ffffff);
      font-family: 'Segoe UI', sans-serif;
    }
    .result-card {
      background: #ffffff;
      border-radius: 16px;
      padding: 2.5rem;
      max-width: 720px;
      margin: auto;
      box-shadow: 0 8px 24px rgba(0,0,0,0.1);
      border: 1px solid #dce3ec;
    }
    .result-card h3 {
      color: #2c3e50;
      margin-bottom: 1rem;
    }
    .result-card p {
      font-size: 1.1rem;
    }
    .result-card a.btn {
      font-size: 0.95rem;
    }
  </style>
</head>
<body>
  <div class="result-card">
    <h3>📋 전세보증보험 추천 결과</h3>
    <p><strong>✅ 추천 보험사:</strong> <a href="<?= $link ?>" target="_blank"><?= $company ?></a></p>
    <p><strong>📌 추천 사유:</strong> <?= $recommend_reason ?></p>
    <p><strong>💰 예상 보증료:</strong> <?= number_format($fee, 1) ?> 만원</p>
    <p><strong>🏠 선택한 지역:</strong> <?= htmlspecialchars($region) ?></p>
    <hr class="my-4">
    <div class="mt-3 d-flex gap-3 flex-wrap">
      <a href="https://www.khug.or.kr" target="_blank" class="btn <?= $hug_class ?>">HUG 바로가기</a>
      <a href="https://www.sgic.co.kr" target="_blank" class="btn <?= $sgi_class ?>">SGI 바로가기</a>
      <a href="https://www.hf.go.kr" target="_blank" class="btn <?= $hf_class ?>">HF 바로가기</a>
    </div>
    <div class="mt-4 text-end">
      <a href="insurance.php" class="btn btn-dark">← 다시 입력하기</a>
    </div>
  </div>
</body>
</html>
