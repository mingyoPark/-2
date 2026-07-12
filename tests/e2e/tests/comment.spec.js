const { test, expect } = require('@playwright/test');
const { paths, ACCOUNTS, autoAcceptDialogs, login } = require('./helpers');

// 댓글 생명주기: 작성 → 수정 → 삭제 (SCN-02)
test.describe('댓글 (COMMENT)', () => {
  // 시드 데이터에 존재하는 게시글(pid=1)을 대상으로 한다.
  const targetPid = 1;

  test('TC-CMT-01 로그인 사용자는 댓글을 작성할 수 있다', async ({ page }) => {
    autoAcceptDialogs(page);
    await login(page, ACCOUNTS.user);

    await page.goto(paths.view(targetPid));
    const body = '자동화가 작성한 댓글 ' + Date.now();
    await page.fill('textarea[name="content"]', body);
    await page.click('button:has-text("댓글 등록")');
    await page.waitForURL((u) => u.pathname.includes('view.php'));
    await expect(page.locator('body')).toContainText(body);
  });

  test('TC-CMT-02 빈 댓글은 등록되지 않는다', async ({ page }) => {
    autoAcceptDialogs(page);
    await login(page, ACCOUNTS.user);
    await page.goto(paths.view(targetPid));
    // required 우회를 위해 검증 무력화 후 제출 (서버 측 검증 확인 목적)
    await page.evaluate(() => {
      const t = document.querySelector('textarea[name="content"]');
      if (t) t.removeAttribute('required');
    });
    const dlg = autoAcceptDialogs(page);
    await page.click('button:has-text("댓글 등록")');
    await expect.poll(() => dlg.lastMessage).toContain('댓글 내용을 입력');
  });

  test('TC-CMT-04/05 본인 댓글 수정 후 삭제', async ({ page }) => {
    autoAcceptDialogs(page);
    await login(page, ACCOUNTS.user);

    // 대상 댓글 생성
    await page.goto(paths.view(targetPid));
    const body = '수정삭제 대상 ' + Date.now();
    await page.fill('textarea[name="content"]', body);
    await page.click('button:has-text("댓글 등록")');
    await page.waitForURL((u) => u.pathname.includes('view.php'));

    // 방금 작성한 댓글 박스 찾기
    const box = page.locator('[id^="comment-box-"]', { hasText: body }).first();
    await expect(box).toBeVisible();

    // 수정
    await box.getByText('수정').click();
    const edited = body + ' (edited)';
    await box.locator('textarea[name="content"]').fill(edited);
    await box.getByRole('button', { name: '수정 완료' }).click();
    await page.waitForURL((u) => u.pathname.includes('view.php'));
    await expect(page.locator('body')).toContainText(edited);
    // DEF-003 수정 반영 시 "(수정됨)" 표기가 나타난다
    await expect(page.locator('body')).toContainText('수정됨');

    // 삭제
    const box2 = page.locator('[id^="comment-box-"]', { hasText: edited }).first();
    await box2.getByText('삭제').click();
    await page.waitForURL((u) => u.pathname.includes('view.php'));
    await expect(page.locator('body')).not.toContainText(edited);
  });

  test('TC-CMT-03 비로그인 사용자에게는 댓글 폼이 노출되지 않는다', async ({ page }) => {
    autoAcceptDialogs(page);
    await page.goto(paths.view(targetPid));
    await expect(page.locator('textarea[name="content"]')).toHaveCount(0);
  });
});
