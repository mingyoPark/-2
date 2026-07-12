// @ts-check
const { defineConfig, devices } = require('@playwright/test');

/**
 * 전세사기 예방 커뮤니티 E2E 설정
 * - 앱은 http://localhost:8080/project_nextLv/ 에서 실행 중이어야 합니다. (docker compose up -d)
 * - BASE_URL 환경변수로 대상 주소를 바꿀 수 있습니다.
 */
const BASE_URL = process.env.BASE_URL || 'http://localhost:8080';

module.exports = defineConfig({
  testDir: './tests',
  timeout: 30 * 1000,
  expect: { timeout: 5000 },
  fullyParallel: false, // 게시글 생성/삭제 등 상태 공유가 있어 순차 실행
  retries: process.env.CI ? 1 : 0,
  reporter: [['list'], ['html', { open: 'never' }]],
  use: {
    baseURL: BASE_URL,
    trace: 'on-first-retry',
    screenshot: 'only-on-failure',
    video: 'retain-on-failure',
    locale: 'ko-KR',
  },
  projects: [
    { name: 'chromium', use: { ...devices['Desktop Chrome'] } },
    // 필요 시 아래 주석 해제로 크로스 브라우저 검증
    // { name: 'firefox', use: { ...devices['Desktop Firefox'] } },
    // { name: 'webkit', use: { ...devices['Desktop Safari'] } },
  ],
});
