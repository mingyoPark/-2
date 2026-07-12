<?php
include $_SERVER["DOCUMENT_ROOT"] . "/project_nextLv/inc/dbcon.php";
session_start();

$userId = trim($_POST['userId'] ?? '');
$passwd = trim($_POST['passwd'] ?? '');

if (!$userId || !$passwd) {
    echo "<script>alert('아이디와 비밀번호를 모두 입력해주세요.'); history.back();</script>";
    exit;
}

// 사용자 조회
$query = "SELECT * FROM users WHERE user_id = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("s", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();

    if (password_verify($passwd, $user['password'])) {
        $_SESSION['UID'] = $user['user_id'];
        $_SESSION['UNAME'] = $user['name'];
        $_SESSION['ROLE'] = $user['role'];

        echo "<script>alert('로그인 되었습니다.'); location.href='/project_nextLv/index.php';</script>";
    } else {
        echo "<script>alert('비밀번호가 일치하지 않습니다.'); history.back();</script>";
    }
} else {
    echo "<script>alert('존재하지 않는 아이디입니다.'); history.back();</script>";
}
?>