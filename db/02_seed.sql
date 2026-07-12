-- =====================================================================
-- 전세사기 커뮤니티 - 샘플 데이터 (게시판/공지 열람용)
-- =====================================================================
-- 아래 seed 사용자의 password 컬럼은 "로그인 불가"한 자리표시자입니다.
-- (bcrypt 해시가 아니므로 password_verify 실패 → 화면 열람/작성자 표시 전용)
-- 로그인 가능한 테스트 계정은 db/seed_users.php 로 생성하세요. (README 참고)
-- =====================================================================

USE rental_fraud_db;

-- 열람용 작성자 (로그인 불가)
INSERT INTO users (user_id, name, password, email, birth_date, role) VALUES
  ('honggildong', '홍길동', 'DISPLAY_ONLY_NOT_HASHED', 'hong@example.com', '1990-01-01', 'user'),
  ('kimcs',       '김철수', 'DISPLAY_ONLY_NOT_HASHED', 'kim@example.com',  '1988-05-12', 'user'),
  ('adminuser',   '관리자', 'DISPLAY_ONLY_NOT_HASHED', 'admin@example.com','1985-03-03', 'admin')
ON DUPLICATE KEY UPDATE name = VALUES(name);

-- 공지사항
INSERT INTO notice (title, content, admin_id) VALUES
  ('[안내] 전세보증보험 가입 요건이 변경되었습니다', '2024년부터 전세가율 기준이 강화되었습니다. 자세한 내용은 본문을 확인하세요.', 'adminuser'),
  ('[공지] 커뮤니티 이용 수칙 안내', '허위 정보 게시 및 광고성 글은 사전 통보 없이 삭제될 수 있습니다.', 'adminuser'),
  ('[업데이트] 체크리스트 기능이 개선되었습니다', '계약 전/중/후 단계별 점검 항목이 추가되었습니다.', 'adminuser');

-- 게시글 (피해/문의 사례)
INSERT INTO post (author_id, title, content, region, sub_region, fraud_type, views) VALUES
  ('honggildong', '깡통전세 의심됩니다. 조언 부탁드려요', '전세가율이 90%가 넘는데 계약해도 될까요? 등기부등본상 근저당이 잡혀 있습니다.', '서울특별시', '강서구', '깡통전세', 42),
  ('kimcs',       '이중계약 사기 당했습니다', '집주인이 같은 집을 여러 명과 계약했다고 합니다. 어떻게 대응해야 하나요?', '경기도', '수원시', '이중계약', 87),
  ('honggildong', '전입신고 후 근저당이 잡혔어요', '확정일자를 받았는데 그 다음날 근저당이 설정됐습니다. 대항력이 유지되나요?', '인천광역시', '미추홀구', '근저당', 15),
  ('kimcs',       '보증금 반환 지연, 어떻게 하나요', '계약 만료 두 달이 지났는데 보증금을 돌려주지 않습니다.', '부산광역시', '해운대구', '보증금미반환', 31),
  ('honggildong', '신탁부동산 전세계약 주의하세요', '신탁된 부동산은 소유자가 아니라 신탁사 동의가 필요합니다. 저처럼 당하지 마세요.', '서울특별시', '관악구', '신탁사기', 63);

-- 댓글
INSERT INTO comment (post_id, author_id, content) VALUES
  (1, 'kimcs',       '전세가율 90%면 위험합니다. 보증보험 가입 가능한지 먼저 확인하세요.'),
  (1, 'adminuser',   '등기부등본 을구의 근저당 채권최고액을 꼭 확인하시기 바랍니다.'),
  (2, 'honggildong', '경찰 신고와 함께 전세보증금 반환보증 이행청구를 검토해보세요.'),
  (5, 'kimcs',       '신탁원부까지 확인하는 게 안전합니다. 좋은 정보 감사합니다.');
