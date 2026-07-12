<?php include $_SERVER["DOCUMENT_ROOT"] . "/project_nextLv/inc/header.php"; ?>

<div class="container d-flex justify-content-center align-items-center" style="min-height: 90vh;">
  <div class="card p-4 shadow-sm" style="width: 100%; max-width: 480px;">
    <h4 class="text-center mb-4 fw-bold">회원가입</h4>

    <form method="post" action="signup_ok.php" id="signupForm" onsubmit="return validateForm();">
      <!-- 이름 -->
      <div class="mb-3">
        <label class="form-label">이름</label>
        <input type="text" name="name" id="name" class="form-control">
        <div class="form-text text-danger d-none" id="nameError">이름을 입력해주세요.</div>
      </div>

      <!-- 아이디 + 중복확인 -->
      <div class="mb-3">
        <label class="form-label">아이디</label>
        <div class="input-group">
          <input type="text" name="user_id" id="user_id" class="form-control">
          <button type="button" class="btn btn-outline-secondary" onclick="checkDuplicateId()">중복확인</button>
        </div>
        <div class="form-text text-danger d-none" id="idError">아이디를 입력해주세요.</div>
        <div class="form-text text-success d-none" id="idSuccessMsg">사용 가능한 아이디입니다.</div>
        <div class="form-text text-danger d-none" id="idFailMsg">이미 사용 중인 아이디입니다.</div>
      </div>

      <!-- 이메일 -->
      <div class="mb-3">
        <label class="form-label">이메일</label>
        <div class="input-group">
          <input type="text" id="email_id" class="form-control" placeholder="example">
          <span class="input-group-text">@</span>
          <select id="email_domain" class="form-select">
            <option value="naver.com">naver.com</option>
            <option value="gmail.com">gmail.com</option>
            <option value="hanmail.net">hanmail.net</option>
            <option value="kakao.com">kakao.com</option>
          </select>
        </div>
        <input type="hidden" name="email" id="email">
        <div class="form-text text-danger d-none" id="emailError">이메일을 입력해주세요.</div>
      </div>

      <!-- 비밀번호 -->
      <div class="mb-3">
        <label class="form-label">비밀번호</label>
        <input type="password" name="password" id="password" class="form-control">
        <div class="form-text text-danger d-none" id="pwError">비밀번호를 입력해주세요.</div>
      </div>

      <!-- 생년월일 -->
      <div class="mb-3">
        <label class="form-label">생년월일</label>
        <input type="date" name="birth_date" id="birth_date" class="form-control">
        <div class="form-text text-danger d-none" id="birthError">생년월일을 입력해주세요.</div>
      </div>

      <!-- 가입 버튼 -->
      <div class="d-grid mb-3">
        <button type="submit" class="btn btn-primary">회원가입</button>
      </div>

      <div class="text-center">
        <span class="small">이미 회원이신가요?</span>
        <a href="/project_nextLv/member/login.php" class="small fw-bold">로그인</a>
      </div>
    </form>
  </div>
</div>

<script>
let isIdChecked = false;

// 아이디 입력 시 중복확인 상태 초기화
document.getElementById("user_id").addEventListener("input", function () {
  isIdChecked = false;
  document.getElementById("idSuccessMsg").classList.add("d-none");
  document.getElementById("idFailMsg").classList.add("d-none");
});

function validateForm() {
  let isValid = true;

  const name = document.getElementById("name");
  const user_id = document.getElementById("user_id");
  const pw = document.getElementById("password");
  const birth = document.getElementById("birth_date");
  const email_id = document.getElementById("email_id").value.trim();
  const email_domain = document.getElementById("email_domain").value.trim();
  const full_email = email_id && email_domain ? `${email_id}@${email_domain}` : "";

  document.getElementById("email").value = full_email;
  document.querySelectorAll('.form-text.text-danger').forEach(el => el.classList.add('d-none'));
  document.querySelectorAll('.form-text.text-success').forEach(el => el.classList.add('d-none'));

  if (name.value.trim() === '') {
    document.getElementById("nameError").classList.remove("d-none");
    isValid = false;
  }

  if (user_id.value.trim() === '') {
    document.getElementById("idError").classList.remove("d-none");
    isValid = false;
  }

  if (!isIdChecked) {
    alert("아이디 중복확인을 해주세요.");
    isValid = false;
  }

  if (full_email === "") {
    document.getElementById("emailError").classList.remove("d-none");
    isValid = false;
  }

  if (pw.value.trim() === '') {
    document.getElementById("pwError").classList.remove("d-none");
    isValid = false;
  }

  if (birth.value.trim() === '') {
    document.getElementById("birthError").classList.remove("d-none");
    isValid = false;
  }

  return isValid;
}

function checkDuplicateId() {
  const user_id = document.getElementById("user_id").value.trim();
  if (!user_id) {
    alert("아이디를 입력하세요.");
    return;
  }

  fetch('check_userid.php?user_id=' + encodeURIComponent(user_id))
    .then(response => response.json())
    .then(result => {
      document.getElementById("idSuccessMsg").classList.add("d-none");
      document.getElementById("idFailMsg").classList.add("d-none");

      if (result.status === 'available') {
        document.getElementById("idSuccessMsg").classList.remove("d-none");
        isIdChecked = true;
      } else if (result.status === 'taken') {
        document.getElementById("idFailMsg").classList.remove("d-none");
        isIdChecked = false;
      } else {
        alert("아이디 확인 중 오류 발생: " + result.message);
        isIdChecked = false;
      }
    });
}
</script>

<?php include $_SERVER["DOCUMENT_ROOT"] . "/project_nextLv/inc/footer.php"; ?>