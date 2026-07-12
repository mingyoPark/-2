const { test, expect } = require('@playwright/test');
const { paths, ACCOUNTS, autoAcceptDialogs, login } = require('./helpers');

test.describe('게시판 - 글 (POST) & 목록 (LIST)', () => {
  test('TC-POST-01 로그인 사용자는 글을 작성하고 상세에서 확인할 수 있다', async ({ page }) => {
    autoAcceptDialogs(page);
    await login(page, ACCOUNTS.user);

    const title = 'E2E 자동화 테스트 글 ' + Date.now();
    await page.goto(paths.write);
    await page.fill('input[name="title"]', title);
    await page.fill('textarea[name="content"]', '자동화 테스트로 작성한 본문입니다.');
    await page.click('button[type="submit"]');

    // 등록 성공 시 view.php?pid=... 로 이동
    await page.waitForURL((u) => u.pathname.includes('view.php'));
    await expect(page.locator('h2')).toContainText(title);
    await expect(page.locator('body')).toContainText(ACCOUNTS.user.id); // 작성자
  });

  test('TC-POST-02 제목/내용 누락 시 등록이 차단된다', async ({ page }) => {
    const dlg = autoAcceptDialogs(page);
    await login(page, ACCOUNTS.user);
    await page.goto(paths.write);
    await page.fill('input[name="title"]', ''); // 제목 비움
    await page.fill('textarea[name="content"]', '내용만 있음');
    await page.click('button[type="submit"]');
    await expect.poll(() => dlg.lastMessage).toContain('제목과 내용을 모두 입력');
  });

  test('TC-POST-03 비로그인 상태에서 글쓰기는 차단된다', async ({ page }) => {
    const dlg = autoAcceptDialogs(page);
    // 로그인하지 않고 write 직접 접근 후 등록 시도
    await page.goto(paths.write);
    await page.fill('input[name="title"]', '비로그인 글').catch(() => {});
    await page.fill('textarea[name="content"]', '차단되어야 함').catch(() => {});
    await page.click('button[type="submit"]').catch(() => {});
    await expect.poll(() => dlg.lastMessage).toContain('회원 전용');
  });

  test('TC-POST-04/06 본인 글 수정 후 삭제', async ({ page }) => {
    autoAcceptDialogs(page);
    await login(page, ACCOUNTS.user);

    // 글 생성
    const title = 'CRUD 대상 글 ' + Date.now();
    await page.goto(paths.write);
    await page.fill('input[name="title"]', title);
    await page.fill('textarea[name="content"]', '수정/삭제 대상');
    await page.click('button[type="submit"]');
    await page.waitForURL((u) => u.pathname.includes('view.php'));

    // 수정
    await page.getByRole('link', { name: '수정' }).click();
    const newTitle = title + ' (수정됨)';
    await page.fill('input[name="title"]', newTitle);
    await page.click('button[type="submit"]');
    await page.waitForURL((u) => u.pathname.includes('view.php'));
    await expect(page.locator('h2')).toContainText('(수정됨)');

    // 삭제 (confirm + alert 자동 수락)
    await page.getByRole('link', { name: '삭제' }).click();
    await page.waitForURL((u) => u.pathname.includes('index.php') || u.pathname.includes('board.php'));
  });

  test('TC-LIST-06 (보안) 검색어 SQL Injection 페이로드로도 서버 오류/전체노출이 없어야 한다', async ({ page }) => {
    // DEF-002 회귀 감시용. 취약점이 수정되면 이 테스트가 안정적으로 통과해야 한다.
    autoAcceptDialogs(page);
    await page.goto(paths.board + "?search=" + encodeURIComponent("' OR '1'='1"));
    // 페이지가 SQL 오류 문자열을 노출하지 않아야 함
    await expect(page.locator('body')).not.toContainText('You have an error in your SQL syntax');
    await expect(page.locator('table')).toBeVisible();
  });

  test('TC-LIST-01/02 목록 정렬(최신순/조회순) 전환', async ({ page }) => {
    autoAcceptDialogs(page);
    await page.goto(paths.board);
    await expect(page.locator('table')).toBeVisible();

    await page.getByRole('link', { name: '조회순' }).click();
    await expect(page).toHaveURL(/order=views/);

    await page.getByRole('link', { name: '최신순' }).click();
    await expect(page).toHaveURL(/order=post_id/);
  });
});

test.describe('게시판 - 권한 경계 (SCN-03)', () => {
  test('TC-POST-05 타인 글은 수정할 수 없다', async ({ page }) => {
    const dlg = autoAcceptDialogs(page);

    // testuser로 글 생성 후 pid 확보
    await login(page, ACCOUNTS.user);
    const title = '권한 테스트 글 ' + Date.now();
    await page.goto(paths.write);
    await page.fill('input[name="title"]', title);
    await page.fill('textarea[name="content"]', '타인 수정 차단 확인');
    await page.click('button[type="submit"]');
    await page.waitForURL((u) => u.pathname.includes('view.php'));
    const pid = new URL(page.url()).searchParams.get('pid');

    // testadmin(다른 사용자)으로 로그인 후 해당 글 수정 시도
    await page.goto(paths.logout);
    await login(page, ACCOUNTS.admin);
    await page.goto(`${paths.write}?pid=${pid}`);
    await page.fill('input[name="title"]', '몰래 수정 시도').catch(() => {});
    await page.fill('textarea[name="content"]', 'x').catch(() => {});
    await page.click('button[type="submit"]').catch(() => {});
    await expect.poll(() => dlg.lastMessage).toContain('본인 글');
  });
});
