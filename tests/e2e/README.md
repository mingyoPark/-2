# E2E 자동화 테스트 (Playwright)

전세사기 예방 커뮤니티의 핵심 사용자 흐름을 자동화한 End-to-End 테스트입니다.
QA 문서의 [테스트 시나리오](../../docs/qa/04_test-scenarios.md) / [테스트 케이스](../../docs/qa/02_test-cases.md)와 1:1로 대응합니다.

## 사전 준비

앱이 실행 중이어야 합니다. (프로젝트 루트에서)

```bash
docker compose up -d
docker compose exec web php /var/www/html/project_nextLv/db/seed_users.php
```

## 설치 & 실행

```bash
cd tests/e2e
npm install
npx playwright install        # 브라우저 바이너리 설치(최초 1회)

npx playwright test           # 전체 실행
npx playwright test auth      # 특정 파일만
npm run test:headed           # 브라우저 화면 보며 실행
npm run test:ui               # UI 모드(디버깅)
npm run report                # HTML 리포트 열기
```

대상 주소를 바꾸려면: `BASE_URL=http://localhost/ npx playwright test`

## 테스트 구성

| 파일 | 대응 시나리오 | 커버 케이스 |
|------|---------------|-------------|
| `auth.spec.js` | SCN-01 | TC-AUTH-01,02,03,04,07,08,09 |
| `board.spec.js` | SCN-01,03,06 | TC-POST-01~06, TC-LIST-01,02,06 |
| `comment.spec.js` | SCN-02 | TC-CMT-01~05 |
| `insurance.spec.js` | SCN-04 | TC-INS-01,03,05,07 |
| `checklist.spec.js` | SCN-05 | TC-CHK-01,02,04 |

## 참고 (테스트 설계 메모)

- 이 앱은 결과를 `alert()` + `location.href`로 처리하므로, 헬퍼(`autoAcceptDialogs`)로 다이얼로그를 자동 수락하고 마지막 메시지를 검증합니다.
- 일부 테스트는 결함(예: DEF-002 검색 SQLi, DEF-007 체크리스트 required)의 **회귀 감시** 용도로 작성되어, 해당 결함 수정 후 안정적으로 통과하도록 설계했습니다.
- 상태(글/댓글 생성·삭제)를 공유하므로 `fullyParallel: false`로 순차 실행합니다.
