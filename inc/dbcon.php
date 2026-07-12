<?php
// DB 접속 정보
// 기본값은 기존 XAMPP 환경(localhost / root / 비밀번호 없음)과 동일합니다.
// Docker 등에서는 환경변수(DB_HOST, DB_USER, DB_PASS, DB_NAME)로 덮어쓸 수 있습니다.
$hostname = getenv('DB_HOST') ?: "localhost";
$dbuserid = getenv('DB_USER') ?: "root";
$dbpasswd = getenv('DB_PASS') !== false ? getenv('DB_PASS') : "";
$dbname   = getenv('DB_NAME') ?: "rental_fraud_db";  // ✅ 반드시 이 이름으로 설정

$mysqli = new mysqli($hostname, $dbuserid, $dbpasswd, $dbname);
if ($mysqli->connect_errno) {
    die("DB 연결 실패: " . $mysqli->connect_error);
}
$mysqli->set_charset("utf8mb4");

?>