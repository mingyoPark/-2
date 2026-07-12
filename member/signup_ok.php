<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header("Location: /project_nextLv/index.php");
  exit;
}

include $_SERVER["DOCUMENT_ROOT"] . "/project_nextLv/inc/dbcon.php";
session_start();

$name        = trim($_POST["name"] ?? '');
$user_id     = trim($_POST["user_id"] ?? '');
$password    = trim($_POST["password"] ?? '');
$email       = trim($_POST["email"] ?? '');
$birth_date  = $_POST["birth_date"] ?? null;

// 비밀번호 해싱
$hashed_pw = password_hash($password, PASSWORD_DEFAULT);

// ID 중복 체크
$check = $mysqli->prepare("SELECT user_id FROM users WHERE user_id = ?");
$check->bind_param("s", $user_id);
$check->execute();
$res = $check->get_result();

if ($res->num_rows > 0) {
  echo "<script>alert('이미 사용 중인 아이디입니다.'); history.back();</script>";
  exit;
}

// 사용자 등록 (social_login_type은 NULL로 삽입)
$stmt = $mysqli->prepare("INSERT INTO users (user_id, name, password, email, birth_date, social_login_type, role)
                          VALUES (?, ?, ?, ?, ?, NULL, 'user')");
$stmt->bind_param("sssss", $user_id, $name, $hashed_pw, $email, $birth_date);
$result = $stmt->execute();

if ($result) {
  echo "<script>alert('가입을 환영합니다!'); location.href='/project_nextLv/index.php';</script>";
} else {
  echo "<script>alert('회원가입에 실패했습니다.'); history.back();</script>";
}
?>