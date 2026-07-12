const { test, expect } = require('@playwright/test');
const { paths, autoAcceptDialogs } = require('./helpers');

test.describe('계약 체크리스트 (CHK)', () => {
  test('TC-CHK-01 탭 전환 시 해당 단계 섹션만 표시된다', async ({ page }) => {
    autoAcceptDialogs(page);
    await page.goto(paths.checklist);

    // 기본: 계약 전 섹션 활성
    await expect(page.locator('#section-pre')).toHaveClass(/active/);

    await page.click('#btn-mid');
    await expect(page.locator('#section-mid')).toHaveClass(/active/);
    await expect(page.locator('#section-pre')).not.toHaveClass(/active/);

    await page.click('#btn-post');
    await expect(page.locator('#section-post')).toHaveClass(/active/);
  });

  test('TC-CHK-02 모든 항목을 ⭕로 체크하면 만점 메시지가 나온다', async ({ page }) => {
    autoAcceptDialogs(page);
    await page.goto(paths.checklist);

    // 모든 단계의 'yes' 라디오를 선택.
    // (참고: 비활성 탭 항목은 display:none + required 라 실제 UI로는 제출이 막힘 → DEF-007.
    //  여기서는 점수 산출 로직 검증을 위해 값만 세팅한다.)
    const yesCount = await page.evaluate(() => {
      const yes = document.querySelectorAll('input[type=radio][value="yes"]');
      yes.forEach((el) => { el.checked = true; el.removeAttribute('required'); });
      document.querySelectorAll('input[type=radio]:not([value="yes"])')
        .forEach((el) => el.removeAttribute('required'));
      return yes.length;
    });
    expect(yesCount).toBeGreaterThan(0);

    await page.click('button:has-text("체크 완료")');
    await expect(page.locator('body')).toContainText('모든 항목을 점검하셨습니다');
  });

  test('TC-CHK-04 절반 미만 체크 시 경고 메시지가 나온다', async ({ page }) => {
    autoAcceptDialogs(page);
    await page.goto(paths.checklist);

    // 첫 1개 항목만 yes, 나머지는 no 로 설정 후 제출
    await page.evaluate(() => {
      const radios = document.querySelectorAll('input[type=radio]');
      radios.forEach((el) => el.removeAttribute('required'));
      const yes = document.querySelectorAll('input[type=radio][value="yes"]');
      const no = document.querySelectorAll('input[type=radio][value="no"]');
      no.forEach((el) => (el.checked = true));
      if (yes[0]) yes[0].checked = true; // 1개만 yes
    });

    await page.click('button:has-text("체크 완료")');
    await expect(page.locator('body')).toContainText('점검이 충분하지 않습니다');
  });
});
