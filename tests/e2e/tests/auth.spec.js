const { test, expect } = require('@playwright/test');
const { paths, ACCOUNTS, autoAcceptDialogs } = require('./helpers');

test.describe('회원 (AUTH)', () => {
  test('TC-AUTH-07 정상 로그인 시 헤더에 사용자 이름이 노출된다', async ({ page }) => {
    autoAcceptDialogs(page);
    await page.goto(paths.login);
    await page.fill('input[name="userId"]', ACCOUNTS.user.id);
    await page.fill('input[name="passwd"]', ACCOUNTS.user.pw);
    await page.click('button[type="submit"]');
    await page.waitForURL((u) => u.pathname.includes('index.php'));
    await expect(page.locator('body')).toContainText(`${ACCOUNTS.user.name} 님`);
    await expect(page.getByRole('link', { name: '로그아웃' })).toBeVisible();
  });

  test('TC-AUTH-08 잘못된 비밀번호는 안내 후 차단된다', async ({ page }) => {
    const dlg = autoAcceptDialogs(page);
    await page.goto(paths.login);
    await page.fill('input[name="userId"]', ACCOUNTS.user.id);
    await page.fill('input[name="passwd"]', 'wrong-password');
    await page.click('button[type="submit"]');
    await expect.poll(() => dlg.lastMessage).toContain('비밀번호가 일치하지 않습니다');
  });

  test('TC-AUTH-09 존재하지 않는 아이디는 안내 후 차단된다', async ({ page }) => {
    const dlg = autoAcceptDialogs(page);
    await page.goto(paths.login);
    await page.fill('input[name="userId"]', 'nobody-here-xyz');
    await page.fill('input[name="passwd"]', 'whatever1234');
    await page.click('button[type="submit"]');
    await expect.poll(() => dlg.lastMessage).toContain('존재하지 않는 아이디입니다');
  });

  test('TC-AUTH-02/04 중복 아이디 확인 및 미확인 제출 차단', async ({ page }) => {
    const dlg = autoAcceptDialogs(page);
    await page.goto(paths.signup);

    // 이미 존재하는 아이디 → "이미 사용 중"
    await page.fill('#user_id', ACCOUNTS.user.id);
    await page.click('button:has-text("중복확인")');
    await expect(page.locator('#idFailMsg')).toBeVisible();

    // 중복확인 통과하지 않은 채 제출 → alert로 차단
    await page.fill('#name', '홍길동');
    await page.fill('#password', 'Test1234!');
    await page.fill('#birth_date', '1995-06-15');
    await page.fill('#email_id', 'someone');
    await page.click('button[type="submit"]');
    await expect.poll(() => dlg.lastMessage).toContain('아이디 중복확인');
  });

  test('TC-AUTH-01/03 신규 사용자 회원가입 성공', async ({ page }) => {
    autoAcceptDialogs(page);
    const uniqueId = 'e2e_' + Date.now();
    await page.goto(paths.signup);

    await page.fill('#name', 'E2E테스터');
    await page.fill('#user_id', uniqueId);
    await page.click('button:has-text("중복확인")');
    await expect(page.locator('#idSuccessMsg')).toBeVisible(); // "사용 가능한 아이디입니다"

    await page.fill('#email_id', 'e2e');
    await page.selectOption('#email_domain', 'gmail.com');
    await page.fill('#password', 'Test1234!');
    await page.fill('#birth_date', '1995-06-15');

    await page.click('button[type="submit"]');
    // 가입 성공 시 index로 이동
    await page.waitForURL((u) => u.pathname.includes('index.php'));
  });
});
