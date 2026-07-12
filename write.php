<?php
include $_SERVER["DOCUMENT_ROOT"] . "/project_nextLv/inc/header.php";
if (!isset($_SESSION['UID'])) {
  echo "<script>alert('로그인이 필요합니다.'); location.href='login.php';</script>";
  exit;
}

$pid = $_GET['pid'] ?? null;
$edit = false;
$title = $content = $region = $sub_region = $fraud_type = '';

if ($pid) {
  $result = $mysqli->query("SELECT * FROM post WHERE post_id = $pid") or die($mysqli->error);
  $row = $result->fetch_assoc();
  if ($row['author_id'] !== $_SESSION['UID']) {
    echo "<script>alert('본인 글만 수정할 수 있습니다.'); history.back();</script>";
    exit;
  }
  $edit = true;
  extract($row);
}

// 사기유형 목록
$fraud_types = ["이중계약", "허위 정보 제공", "가짜 임대인", "보증금 미반환", "명의 도용", "기타"];
?>

<h3 class="mb-4"><?= $edit ? "✏ 글 수정" : "✍ 글쓰기" ?></h3>
<form method="post" action="write_ok.php">
  <?php if ($edit): ?><input type="hidden" name="pid" value="<?= $post_id ?>"><?php endif; ?>

  <div class="mb-3">
    <label class="form-label">제목</label>
    <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($title) ?>" required>
  </div>

  <div class="mb-3">
    <label class="form-label">내용</label>
    <textarea name="content" class="form-control" rows="5" required><?= htmlspecialchars($content) ?></textarea>
  </div>

  <!-- 지역: 시/도, 시군구 -->
  <div class="mb-3">
    <label class="form-label">지역</label>
    <div class="row g-2">
      <div class="col">
        <select name="region" id="region" class="form-select" required>
          <option value="">시/도 선택</option>
        </select>
      </div>
      <div class="col">
        <select name="sub_region" id="sub_region" class="form-select" required>
          <option value="">시/군/구 선택</option>
        </select>
      </div>
    </div>
  </div>

  <div class="mb-3">
    <label class="form-label">사기유형</label>
    <select name="fraud_type" class="form-select">
      <option value="">선택하세요</option>
      <?php foreach ($fraud_types as $f): ?>
        <option value="<?= $f ?>" <?= $fraud_type === $f ? 'selected' : '' ?>><?= $f ?></option>
      <?php endforeach; ?>
    </select>
  </div>

  <div class="text-end">
    <button type="submit" class="btn btn-primary"><?= $edit ? "수정" : "등록" ?></button>
  </div>
</form>

<!-- 지역 드롭다운 스크립트 -->
<script>
const regionData = {
  "서울특별시": ["종로구", "중구", "용산구", "강남구", "서초구"],
  "부산광역시": ["해운대구", "사하구", "동래구", "부산진구"],
  "경기도": ["수원시", "성남시", "용인시", "고양시", "부천시"],
  "강원도": ["춘천시", "원주시", "강릉시"],
  "충청북도": ["청주시", "충주시", "제천시"],
  "충청남도": ["천안시", "공주시", "아산시"],
  "전라북도": ["전주시", "익산시", "군산시"],
  "전라남도": ["여수시", "순천시", "목포시"],
  "경상북도": ["포항시", "구미시", "경주시"],
  "경상남도": ["창원시", "진주시", "김해시"],
  "제주특별자치도": ["제주시", "서귀포시"]
};

window.addEventListener('DOMContentLoaded', () => {
  const regionSelect = document.getElementById("region");
  const subRegionSelect = document.getElementById("sub_region");

  // 시/도 옵션 추가
  for (const sido in regionData) {
    const opt = document.createElement("option");
    opt.value = sido;
    opt.textContent = sido;
    if ("<?= $region ?>" === sido) opt.selected = true;
    regionSelect.appendChild(opt);
  }

  function updateSubRegions() {
    const selectedSido = regionSelect.value;
    subRegionSelect.innerHTML = '<option value="">시/군/구 선택</option>';

    if (selectedSido && regionData[selectedSido]) {
      regionData[selectedSido].forEach(sgg => {
        const opt = document.createElement("option");
        opt.value = sgg;
        opt.textContent = sgg;
        if ("<?= $sub_region ?>" === sgg) opt.selected = true;
        subRegionSelect.appendChild(opt);
      });
    }
  }

  regionSelect.addEventListener("change", updateSubRegions);
  updateSubRegions(); // 초기값 설정 (수정 모드 대비)
});
</script>

<?php include $_SERVER["DOCUMENT_ROOT"] . "/project_nextLv/inc/footer.php"; ?>