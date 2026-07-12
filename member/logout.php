<?php
session_start();
session_unset();
session_destroy();
setcookie("user_token", "", time() - 3600, "/");
echo "<script>alert('로그아웃 되었습니다.'); location.href='/project_nextLv/index.php';</script>";
exit;
?>