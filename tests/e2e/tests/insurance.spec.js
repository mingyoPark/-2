const { test, expect } = require('@playwright/test');
const { paths, autoAcceptDialogs } = require('./helpers');

// 지역 3단계 select(시도→시군구→읍면동)는 fetch로 채워진다. 순차 선택하여 hidden region을 설정.
async function selectRegion(page) {
  await expect.poll(async () => page.locator('#sido option').count()).toBeGreaterThan(1);
  await page.selectOption('#sido', { index: 1 });
  await expect.poll(async () => page.locator('#sigungu option').count()).toBeGreaterThan(1);
  await page.selectOption('#sigungu', { index: 1 });
  await expect.poll(async () => page.locator('#dong option').count()).toBeGreaterThan(1);
  await page.selectOption('#dong', { index: 1 });
}

test.describe('전세보증보험 추천 (INS)', () => {
  test('TC-INS-01 무주택이 아니면 SGI 서울보증을 추천한다', async ({ page }) => {
    autoAcceptDialogs(page);
    await page.goto(paths.insurance);

    await page.click('label[for="no_house_no"]');        // is_homeless = 0
    await page.fill('input[name="deposit"]', '15000');
    await page.selectOption('select[name="target_type"]', '일반');
    await page.selectOption('select[name="income_level"]', '중위초과');
    await page.selectOption('select[name="housing_type"]', '아파트');
    await selectRegion(page);

    await page.click('button:has-text("추천 결과 보기")');
    await page.waitForURL((u) => u.pathname.includes('insurance_result.php'));
    await expect(page.locator('body')).toContainText('SGI 서울보증');
  });

  test('TC-INS-03 무주택 + 보증금 4억 초과 시 HF를 추천한다', async ({ page }) => {
    autoAcceptDialogs(page);
    await page.goto(paths.insurance);

    await page.click('label[for="no_house_yes"]');       // is_homeless = 1
    await page.fill('input[name="deposit"]', '40001');   // 4억 초과 경계값
    await page.selectOption('select[name="target_type"]', '일반');
    await page.selectOption('select[name="income_level"]', '중위초과');
    await page.selectOption('select[name="housing_type"]', '아파트');
    await selectRegion(page);

    await page.click('button:has-text("추천 결과 보기")');
    await page.waitForURL((u) => u.pathname.includes('insurance_result.php'));
    await expect(page.locator('body')).toContainText('HF 한국주택금융공사');
  });

  test('TC-INS-05/07 무주택 청년 + 중위이하 + 1억 이하 → HUG, 보증료 70.0만원', async ({ page }) => {
    autoAcceptDialogs(page);
    await page.goto(paths.insurance);

    await page.click('label[for="no_house_yes"]');       // is_homeless = 1
    await page.fill('input[name="deposit"]', '10000');   // 1억
    await page.selectOption('select[name="target_type"]', '청년');
    await page.selectOption('select[name="income_level"]', '중위이하');
    await page.selectOption('select[name="housing_type"]', '아파트');
    await selectRegion(page);

    await page.click('button:has-text("추천 결과 보기")');
    await page.waitForURL((u) => u.pathname.includes('insurance_result.php'));
    await expect(page.locator('body')).toContainText('HUG 주택도시보증공사');
    // 보증료 = 10000 × 0.007 = 70.0 만원
    await expect(page.locator('body')).toContainText('70.0');
  });
});
