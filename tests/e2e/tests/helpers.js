// 공통 헬퍼: 경로 상수, 다이얼로그 자동 수락, 로그인/로그아웃
const APP = '/project_nextLv';

const paths = {
  index: `${APP}/index.php`,
  login: `${APP}/member/login.php`,
  signup: `${APP}/member/signup.php`,
  logout: `${APP}/member/logout.php`,
  board: `${APP}/board.php`,
  write: `${APP}/write.php`,
  view: (pid) => `${APP}/view.php?pid=${pid}`,
  insurance: `${APP}/insurance.php`,
  checklist: `${APP}/checklist.php`,
};

const ACCOUNTS = {
  user: { id: 'testuser', pw: 'Test1234!', name: '테스트유저' },
  admin: { id: 'testadmin', pw: 'Admin1234!', name: '테스트관리자' },
};

/**
 * 이 앱은 성공/실패를 alert()로 알리고 location.href로 이동합니다.
 * 모든 다이얼로그를 자동 수락하도록 등록합니다.
 * (마지막으로 뜬 alert 메시지는 반환 객체의 lastMessage로 확인 가능)
 */
function autoAcceptDialogs(page) {
  const state = { lastMessage: null };
  page.on('dialog', async (dialog) => {
    state.lastMessage = dialog.message();
    await dialog.accept();
  });
  return state;
}

/** 로그인. 성공 시 index로 이동됨. */
async function login(page, account = ACCOUNTS.user) {
  await page.goto(paths.login);
  await page.fill('input[name="userId"]', account.id);
  await page.fill('input[name="passwd"]', account.pw);
  await Promise.all([
    page.waitForNavigation({ url: (u) => u.pathname.includes('index.php') }).catch(() => {}),
    page.click('button[type="submit"]'),
  ]);
}

async function logout(page) {
  await page.goto(paths.logout);
}

module.exports = { APP, paths, ACCOUNTS, autoAcceptDialogs, login, logout };
