<?php
/**
 * 로그인 가능한 테스트 계정 생성 스크립트
 * ------------------------------------------------------------------
 * 회원가입(signup_ok.php)과 동일하게 password_hash(bcrypt)로 저장하므로
 * 실제 로그인 플로우 및 E2E 자동화 테스트에 그대로 사용할 수 있습니다.
 *
 * 실행 (Docker):
 *   docker compose exec web php /var/www/html/project_nextLv/db/seed_users.php
 * 실행 (로컬 PHP):
 *   php db/seed_users.php   (환경변수 DB_HOST 등 필요 시 설정)
 *
 * 생성 계정:
 *   testuser / Test1234!   (role: user)
 *   testadmin / Admin1234! (role: admin)
 */

$host = getenv('DB_HOST') ?: 'localhost';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: '';
$name = getenv('DB_NAME') ?: 'rental_fraud_db';

$mysqli = new mysqli($host, $user, $pass, $name);
if ($mysqli->connect_errno) {
    fwrite(STDERR, "DB 연결 실패: " . $mysqli->connect_error . "\n");
    exit(1);
}
$mysqli->set_charset('utf8mb4');

$accounts = [
    ['testuser',  '테스트유저', 'Test1234!',  'testuser@example.com',  '1995-06-15', 'user'],
    ['testadmin', '테스트관리자', 'Admin1234!', 'testadmin@example.com', '1990-02-20', 'admin'],
];

$sql = "INSERT INTO users (user_id, name, password, email, birth_date, social_login_type, role)
        VALUES (?, ?, ?, ?, ?, NULL, ?)
        ON DUPLICATE KEY UPDATE password = VALUES(password), role = VALUES(role)";
$stmt = $mysqli->prepare($sql);

foreach ($accounts as [$id, $nm, $pw, $email, $birth, $role]) {
    $hash = password_hash($pw, PASSWORD_DEFAULT);
    $stmt->bind_param('ssssss', $id, $nm, $hash, $email, $birth, $role);
    $stmt->execute();
    echo "생성/갱신: {$id} / {$pw}  (role={$role})\n";
}

echo "완료.\n";
