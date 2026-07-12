# 결함 리포트 (Defect Report)

전세사기 예방 커뮤니티에서 QA 수행 중 발견한 결함을 기록합니다.
각 결함은 **재현 절차 · 기대/실제 결과 · 심각도/우선순위 · 원인 · 개선 제안**을 포함합니다.

## 결함 요약

| ID | 제목 | 심각도 | 우선순위 | 유형 | 상태 |
|----|------|--------|----------|------|------|
| DEF-001 | 게시글 상세 pid 파라미터 SQL Injection | 🔴 Critical | P1 | 보안 | Open |
| DEF-002 | 게시판 검색어 SQL Injection | 🔴 Critical | P1 | 보안 | Open |
| DEF-003 | DB 스키마-코드 컬럼 불일치로 기능 오류 | 🔴 Critical | P1 | 기능 | ✅ Fixed |
| DEF-004 | 사용자 이름 저장형 XSS (헤더 출력) | 🟠 Major | P1 | 보안 | Open |
| DEF-005 | GET 방식 삭제 + CSRF 토큰 부재 | 🟠 Major | P2 | 보안 | Open |
| DEF-006 | 회원가입 비밀번호 정책/서버검증 부재 | 🟡 Minor | P2 | 기능 | Open |
| DEF-007 | 체크리스트 숨김 탭 required 라디오 제출 불가 | 🟡 Minor | P2 | UX/기능 | Open |
| DEF-008 | 비로그인 상태 댓글 삭제 요청 시 세션 미검증 경고 | 🟡 Minor | P3 | 보안 | Open |
| DEF-009 | 보험 추천 보증금 음수/비정상 값 미검증 | 🟡 Minor | P3 | 기능 | Open |

> 심각도 분포: 🔴 Critical 3 · 🟠 Major 2 · 🟡 Minor 4

---

## DEF-001 · 게시글 상세 pid 파라미터 SQL Injection 🔴

- **영역/파일**: 게시판 상세 — [`view.php`](../../view.php), [`delete.php`](../../delete.php), [`write_ok.php`](../../write_ok.php)
- **심각도/우선순위**: Critical / P1
- **환경**: 로그인/비로그인 무관, 모든 브라우저

**재현 절차**
1. 브라우저에서 `http://localhost:8080/project_nextLv/view.php?pid=1` 접속 (정상)
2. 주소를 `view.php?pid=1 OR 1=1` 또는 `view.php?pid=0 UNION SELECT ...` 로 변경

**기대 결과**: 잘못된 형식의 pid는 안전하게 거부되고 데이터가 노출되지 않는다.
**실제 결과**: `pid`가 쿼리에 문자열 그대로 삽입되어(`SELECT * FROM post WHERE post_id = $pid`, `UPDATE post SET views = views + 1 WHERE post_id = $pid`) SQL Injection이 가능하다.

**원인**: 사용자 입력(`$_GET['pid']`)을 이스케이프/바인딩 없이 raw query에 연결.

**개선 제안**
- prepared statement + `bind_param("i", $pid)` 사용, 또는 최소 `(int)$pid` 캐스팅.
- 예: `$stmt = $mysqli->prepare("SELECT * FROM post WHERE post_id = ?");`

---

## DEF-002 · 게시판 검색어 SQL Injection 🔴

- **영역/파일**: 게시판 목록 — [`board.php`](../../board.php)
- **심각도/우선순위**: Critical / P1

**재현 절차**
1. 게시판 상단 검색창에 `' OR '1'='1` 입력 후 검색
2. 또는 URL로 `board.php?search=%27%20OR%20%271%27%3D%271`

**기대 결과**: 검색어는 리터럴로 취급되어 해당 문자열을 포함한 글만 조회.
**실제 결과**: `WHERE title LIKE '%$search%' OR author_id LIKE '%$search%'` 에 검색어가 그대로 삽입되어 조건 우회/오류 유발 가능(SQL Injection).

**원인**: `$_GET['search']` 미검증 삽입.
**개선 제안**: prepared statement + `LIKE CONCAT('%', ?, '%')` 바인딩 사용. 정렬 컬럼(`$order_by`)은 이미 화이트리스트 처리되어 있어 동일 원칙을 검색에도 적용.

---

## DEF-003 · DB 스키마-코드 컬럼 불일치로 기능 오류 🔴 ✅Fixed

- **영역/파일**: 스키마 [`rental_fraud_db.sql`](../../rental_fraud_db.sql) vs 코드 다수
- **심각도/우선순위**: Critical / P1
- **상태**: **수정 완료** — 정정 스키마 [`db/01_schema.sql`](../../db/01_schema.sql) 반영

**재현 절차**
1. 원본 `rental_fraud_db.sql`로 DB 구성
2. 로그인 후 글 작성(제목/내용/지역 입력) 시도 → 저장 실패
3. 댓글 수정 후 상세 조회 시 `(수정됨)` 표시 로직에서 오류

**기대 결과**: 코드가 참조하는 컬럼이 모두 스키마에 존재하여 정상 동작.
**실제 결과**: 아래 컬럼이 코드에는 있으나 스키마에 없어 SQL 오류 발생.

| 코드 참조 위치 | 참조 컬럼 | 원본 스키마 |
|---------------|-----------|-------------|
| `write_ok.php`, `view.php` | `post.sub_region` | ❌ 없음 |
| `view.php`, `comment_edit_ok.php` | `comment.updated_at` | ❌ 없음 |
| `inc/header.php` (자동로그인) | `users.token` | ❌ 없음 |

**원인**: 기능 추가 시 코드는 갱신됐으나 DDL(스키마)이 동기화되지 않음.
**개선/조치**: 누락 컬럼 3종을 추가한 정정 스키마 `db/01_schema.sql` 제공. (이 결함은 앱 실행 자체를 막아 다른 테스트의 진입 조건이므로 우선 반영)

---

## DEF-004 · 사용자 이름 저장형 XSS 🟠

- **영역/파일**: 공통 헤더 — [`inc/header.php`](../../inc/header.php)
- **심각도/우선순위**: Major / P1

**재현 절차**
1. 회원가입 시 이름(name)에 `<script>alert(document.cookie)</script>` 입력
2. 로그인 → 모든 페이지 헤더에 `👤 <이름> 님` 출력

**기대 결과**: 이름은 이스케이프되어 텍스트로만 표시.
**실제 결과**: `<?= $_SESSION['UNAME'] ?>` 로 이스케이프 없이 출력되어 스크립트가 실행될 수 있음(저장형 XSS, 세션 탈취 위험).

**원인**: 출력 시 `htmlspecialchars()` 미적용. (게시글 본문/제목은 이미 이스케이프되어 있으나 사용자 이름은 누락)
**개선 제안**: `htmlspecialchars($_SESSION['UNAME'], ENT_QUOTES, 'UTF-8')` 로 출력. 가입 시 이름 허용 문자 제한도 병행.

---

## DEF-005 · GET 방식 삭제 + CSRF 토큰 부재 🟠

- **영역/파일**: [`delete.php`](../../delete.php), [`comment_delete.php`](../../comment_delete.php)
- **심각도/우선순위**: Major / P2

**재현 절차**
1. 로그인 상태에서 `delete.php?pid=<본인글>` 를 담은 외부 이미지/링크를 클릭하게 유도
2. 사용자의 의도와 무관하게 삭제 요청이 전송됨

**기대 결과**: 상태 변경(삭제)은 POST + CSRF 토큰으로 보호되어 외부에서 위조 불가.
**실제 결과**: 삭제가 GET 요청 + 소유자 검사만으로 수행되어 CSRF에 취약.

**원인**: 상태 변경 액션을 GET으로 처리, CSRF 토큰 미사용.
**개선 제안**: 삭제를 POST로 변경하고 세션 기반 CSRF 토큰 검증 추가.

---

## DEF-006 · 회원가입 비밀번호 정책/서버검증 부재 🟡

- **영역/파일**: [`member/signup.php`](../../member/signup.php), [`member/signup_ok.php`](../../member/signup_ok.php)
- **심각도/우선순위**: Minor / P2

**재현 절차**
1. 회원가입에서 비밀번호를 `1` 한 자리로 입력하고 (중복확인 후) 제출

**기대 결과**: 최소 길이/복잡도 정책 위반 시 안내 및 가입 거부.
**실제 결과**: 프론트는 "빈 값" 여부만 검사, 서버(`signup_ok.php`)는 길이/형식 검증이 전혀 없어 1자리 비밀번호도 가입됨. 이메일/생년월일 형식도 서버 미검증.

**원인**: 클라이언트 검증에만 의존, 서버 측 유효성 검증 부재.
**개선 제안**: 서버에서 비밀번호 최소 8자·영문/숫자 조합, 이메일 형식(`filter_var(FILTER_VALIDATE_EMAIL)`), 생년월일 유효성 검증.

---

## DEF-007 · 체크리스트 숨김 탭 required 라디오 제출 불가 🟡

- **영역/파일**: [`checklist.php`](../../checklist.php)
- **심각도/우선순위**: Minor / P2

**재현 절차**
1. 체크리스트 진입(기본 '계약 전' 탭만 표시)
2. '계약 전' 항목만 선택하고 "체크 완료" 클릭

**기대 결과**: 현재 탭 기준으로 제출되거나, 미선택 항목 위치로 자연스럽게 안내.
**실제 결과**: '계약 중/후' 라디오도 `required`인데 `display:none`으로 숨겨져 있어 브라우저가 숨은 필수 필드에 포커스를 주지 못하고 제출이 막히거나 콘솔 경고가 발생(브라우저별 동작 상이).

**원인**: 숨긴 요소에 HTML5 `required` 적용.
**개선 제안**: 활성 탭에만 required 부여(JS로 토글), 또는 필수 대신 미응답 항목을 결과 계산에 포함.

---

## DEF-008 · 비로그인 상태 댓글 삭제 요청 시 세션 미검증 🟡

- **영역/파일**: [`comment_delete.php`](../../comment_delete.php)
- **심각도/우선순위**: Minor / P3

**재현 절차**
1. 로그아웃 상태에서 `comment_delete.php?cid=1&pid=1` 직접 호출

**기대 결과**: 로그인 필요 안내 후 차단.
**실제 결과**: `isset($_SESSION['UID'])` 선검사 없이 `$_SESSION['UID'] !== $comment['author_id']` 를 비교 → 결과적으로 차단되긴 하나 미정의 세션 접근으로 Notice 발생 가능, 방어 로직이 우회적.

**원인**: 인증 여부 명시적 검사 누락(다른 파일은 `isset` 선검사 존재).
**개선 제안**: 처리 초입에 `if (!isset($_SESSION['UID'])) { 차단 }` 추가로 일관성 확보.

---

## DEF-009 · 보험 추천 보증금 음수/비정상 값 미검증 🟡

- **영역/파일**: [`insurance.php`](../../insurance.php), [`insurance_result.php`](../../insurance_result.php)
- **심각도/우선순위**: Minor / P3

**재현 절차**
1. 보험 추천 폼에서 개발자도구로 `min` 우회 또는 직접 POST로 `deposit=-100` 전송

**기대 결과**: 음수/0/비현실적 값은 거부하고 재입력 안내.
**실제 결과**: `(int)$_POST['deposit']` 로 캐스팅만 하고 범위 검증이 없어 음수 보증금으로도 추천/보증료(음수)가 계산됨.

**원인**: 서버 측 값 범위 검증 부재.
**개선 제안**: `deposit`가 양수이며 상식적 상한 이내인지 검증, 미충족 시 입력 폼으로 회귀.

---

## 개선 우선순위 제안 (요약)

1. **P1 즉시**: SQL Injection 전면 제거(DEF-001/002), 출력 이스케이프(DEF-004) — 데이터·계정 보호 직결
2. **P2 차기**: CSRF·서버검증(DEF-005/006/007)
3. **P3 여유**: 방어 일관성·입력 범위(DEF-008/009)
