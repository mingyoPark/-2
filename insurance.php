
<?php
include $_SERVER["DOCUMENT_ROOT"] . "/project_nextLv/inc/header.php";
if (session_status() === PHP_SESSION_NONE) session_start();
?>

<style>
  .recommend-card {
    background: #fff;
    border-radius: 15px;
    padding: 40px;
    box-shadow: 0 6px 20px rgba(0,0,0,0.05);
    margin-bottom: 60px;
  }
  .btn-group-custom .btn {
    min-width: 120px;
  }
</style>

<div class="container py-5">
  <div class="recommend-card">
    <h2 class="mb-4">🏠 전세보증보험 추천</h2>
    <form method="post" action="insurance_result.php" class="row g-3">
      <div class="col-md-6">
        <label class="form-label">무주택 여부 (본인 및 배우자 포함)</label>
        <div class="btn-group w-100 btn-group-custom" role="group">
          <input type="radio" class="btn-check" name="is_homeless" value="1" id="no_house_yes" required>
          <label class="btn btn-outline-primary" for="no_house_yes">예</label>
          <input type="radio" class="btn-check" name="is_homeless" value="0" id="no_house_no">
          <label class="btn btn-outline-primary" for="no_house_no">아니오</label>
        </div>
      </div>

      <div class="col-md-6">
        <label class="form-label">전세보증금 (만원)</label>
        <input type="number" class="form-control" name="deposit" placeholder="예: 8500" required>
      </div>

      <div class="col-md-6">
        <label class="form-label">대상 유형</label>
        <select class="form-select" name="target_type" required>
          <option value="">선택하세요</option>
          <option value="청년">청년</option>
          <option value="신혼부부">신혼부부</option>
          <option value="일반">일반</option>
        </select>
      </div>

      <div class="col-md-6">
        <label class="form-label">소득 수준</label>
        <select class="form-select" name="income_level" required>
          <option value="">선택하세요</option>
          <option value="2000만원 이하">2000만원 이하</option>
          <option value="2000만원~4000만원">2000만원~4000만원</option>
          <option value="4000만원~6000만원">4000만원~6000만원</option>
          <option value="6000만원 초과">6000만원 초과</option>
          <option value="중위이하">중위이하</option>
          <option value="중위초과">중위초과</option>
        </select>
      </div>

      <div class="col-md-6">
        <label class="form-label">주택 유형</label>
        <select class="form-select" name="housing_type" required>
          <option value="">선택하세요</option>
          <option value="아파트">아파트</option>
          <option value="다세대주택">다세대주택</option>
          <option value="오피스텔">오피스텔</option>
        </select>
      </div>

      <div class="col-md-6">
        <label class="form-label">거주 지역</label>
        <select id="sido" class="form-select mb-2" required>
          <option value="">시/도 선택</option>
        </select>
        <select id="sigungu" class="form-select mb-2" required>
          <option value="">시/군/구 선택</option>
        </select>
        <select id="dong" class="form-select mb-2" required>
          <option value="">읍/면/리 선택</option>
        </select>
        <input type="hidden" name="region" id="region">
      </div>

      <div class="col-md-6">
        <label class="form-label">최근 2년 내 보증지원 이력</label>
        <div class="btn-group w-100 btn-group-custom" role="group">
          <input type="radio" class="btn-check" name="has_previous_support" value="1" id="history_yes">
          <label class="btn btn-outline-secondary" for="history_yes">있음</label>
          <input type="radio" class="btn-check" name="has_previous_support" value="0" id="history_no" checked>
          <label class="btn btn-outline-secondary" for="history_no">없음</label>
        </div>
      </div>

      <div class="col-12 text-end mt-4">
        <button type="submit" class="btn btn-primary px-4">추천 결과 보기</button>
      </div>
    </form>
  </div>
</div>

<script>
const sido = document.getElementById("sido");
const sigungu = document.getElementById("sigungu");
const dong = document.getElementById("dong");
const region = document.getElementById("region");

fetch('/project_nextLv/data/region_full_accurate.json')
  .then(res => res.json())
  .then(regionData => {
    Object.keys(regionData).forEach(sd => {
      sido.add(new Option(sd, sd));
    });

    sido.addEventListener("change", function () {
      sigungu.innerHTML = '<option value="">시/군/구 선택</option>';
      dong.innerHTML = '<option value="">읍/면/리 선택</option>';
      const s = this.value;
      if (regionData[s]) {
        Object.keys(regionData[s]).forEach(sg => {
          sigungu.add(new Option(sg, sg));
        });
      }
    });

    sigungu.addEventListener("change", function () {
      dong.innerHTML = '<option value="">읍/면/리 선택</option>';
      const s = sido.value;
      const g = this.value;
      if (regionData[s] && regionData[s][g]) {
        regionData[s][g].forEach(d => {
          dong.add(new Option(d, d));
        });
      }
    });

    dong.addEventListener("change", function () {
      region.value = `${sido.value} ${sigungu.value} ${dong.value}`;
    });
  });
</script>

<?php include $_SERVER["DOCUMENT_ROOT"] . "/project_nextLv/inc/footer.php"; ?>
