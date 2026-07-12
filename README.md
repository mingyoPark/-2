# 🏠 전세사기 예방 커뮤니티 (project_nextLv)

전세사기 피해를 **예방**하기 위한 정보 공유 · 자가진단 웹 서비스입니다.
피해 사례를 공유하는 커뮤니티 게시판, 계약 단계별 체크리스트, 조건 기반 전세보증보험 추천 기능을 제공합니다.

> 본 저장소는 기능 구현에 더해 **QA 산출물(테스트 계획/케이스/결함 리포트)과 E2E 자동화 테스트**를 함께 관리합니다.
> QA 문서는 [`docs/qa/`](docs/qa/README.md), 자동화 테스트는 [`tests/e2e/`](tests/e2e) 를 참고하세요.

---

## 📌 주요 기능

| 기능 | 설명 | 관련 파일 |
|------|------|-----------|
| 회원 | 회원가입/로그인/로그아웃, 아이디 중복확인(AJAX), 비밀번호 bcrypt 해싱 | [`member/`](member) |
| 게시판 | 글 CRUD, 댓글 CRUD, 검색, 정렬(최신/조회순), 페이지네이션, 조회수 | [`board.php`](board.php), [`view.php`](view.php), [`write.php`](write.php) |
| 공지사항 | 공지 목록/상세, 관리자 작성 | [`notice.php`](notice.php) |
| 보험 추천 | 무주택 여부·보증금·소득·지역 등 입력 → HUG/SGI/HF 추천 및 예상 보증료 | [`insurance.php`](insurance.php), [`insurance_result.php`](insurance_result.php) |
| 체크리스트 | 계약 전/중/후 단계별 점검 및 점수화 | [`checklist.php`](checklist.php) |

---

## 🛠 기술 스택

- **Backend**: PHP (mysqli, prepared statement 일부 적용)
- **DB**: MySQL 8 (`rental_fraud_db`)
- **Frontend**: HTML/CSS, Bootstrap 5, Vanilla JS (fetch API)
- **실행 환경**: Docker Compose (PHP 8.1 + Apache / MySQL 8)
- **QA/자동화**: Playwright (E2E), Markdown 기반 QA 문서

---

## 🚀 실행 방법

### 방법 A. Docker (권장 — 한 줄 실행)

사전 준비: Docker Desktop

```bash
# 1) 프로젝트 폴더에서 컨테이너 기동 (DB 스키마·샘플데이터 자동 로드)
docker compose up -d

# 2) 로그인 가능한 테스트 계정 생성
docker compose exec web php /var/www/html/project_nextLv/db/seed_users.php

# 3) 접속
open http://localhost:8080/project_nextLv/index.php
```

종료: `docker compose down` / 데이터까지 초기화: `docker compose down -v`

### 방법 B. XAMPP (기존 개발 환경)

1. `htdocs/project_nextLv/` 에 소스 배치
2. MySQL에서 스키마·데이터 로드
   ```sql
   SOURCE db/01_schema.sql;
   SOURCE db/02_seed.sql;
   ```
3. 로그인 계정 생성: `php db/seed_users.php`
4. 접속: `http://localhost/project_nextLv/index.php`

> `inc/dbcon.php` 는 기본값이 `localhost / root / (비밀번호 없음)` 이라 XAMPP 기본 설정과 호환됩니다.
> 필요 시 환경변수 `DB_HOST/DB_USER/DB_PASS/DB_NAME` 로 덮어쓸 수 있습니다.

### 🔑 테스트 계정

| 아이디 | 비밀번호 | 권한 |
|--------|----------|------|
| `testuser`  | `Test1234!`  | user |
| `testadmin` | `Admin1234!` | admin |

---

## 🧪 QA & 테스트

이 프로젝트는 QA 관점에서 별도로 관리됩니다.

- **QA 문서**: [`docs/qa/`](docs/qa/README.md)
  - 테스트 계획서 · 테스트 케이스 · 결함 리포트 · 테스트 시나리오 · 요구사항 추적 매트릭스
- **E2E 자동화**: [`tests/e2e/`](tests/e2e) — Playwright

```bash
cd tests/e2e
npm install
npx playwright install
npx playwright test          # 앱이 http://localhost:8080 에 떠 있어야 합니다
npx playwright show-report   # 리포트 확인
```

> ⚠️ QA 과정에서 **SQL Injection·XSS·스키마 불일치 등 9건의 결함**을 발견하여
> [`docs/qa/03_defect-report.md`](docs/qa/03_defect-report.md) 에 문서화했습니다.
> 그 중 앱 실행을 막던 스키마 불일치(DEF-003)는 [`db/01_schema.sql`](db/01_schema.sql) 에 반영했습니다.

---

## 📁 디렉터리 구조

```
project_nextLv/
├── index.php, board.php, view.php, ...   # 애플리케이션 페이지
├── member/                               # 회원(가입/로그인/로그아웃)
├── inc/                                  # header/footer/dbcon 공통 include
├── data/                                 # 지역 JSON 데이터
├── db/                                   # 스키마·시드·계정생성 스크립트
│   ├── 01_schema.sql
│   ├── 02_seed.sql
│   └── seed_users.php
├── docs/qa/                              # ✅ QA 산출물
├── tests/e2e/                            # ✅ Playwright E2E 테스트
├── docker-compose.yml                    # ✅ 원클릭 실행
└── README.md
```
