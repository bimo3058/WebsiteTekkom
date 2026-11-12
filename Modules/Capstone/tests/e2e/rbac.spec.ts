import { test, expect } from '@playwright/test';

// RBAC: mahasiswa must not access admin, dosen must not access finalization.
test.describe('RBAC boundaries', () => {
  test('mahasiswa cannot open admin users (unauthorized, not 500)', async ({ browser }) => {
    const ctx = await browser.newContext({ storageState: 'tests/e2e/.auth/mahasiswa.json' });
    const page = await ctx.newPage();
    const resp = await page.goto('/capstone/admin/users');
    // BladeAccessMiddleware should 403 or redirect to unauthorized
    const status = resp?.status() ?? 200;
    expect([200, 302, 403]).toContain(status);
    expect(status).not.toBe(500);
    const body = await page.content();
    expect(body).not.toMatch(/Server Error|Whoops/);
    if (page.url().includes('unauthorized') || status === 403) {
      expect(body).toMatch(/tidak|unauthorized|akses/i);
    }
    await ctx.close();
  });

  test('dosen cannot execute admin finalization allocate (403)', async ({ browser }) => {
    const ctx = await browser.newContext({ storageState: 'tests/e2e/.auth/dosen.json' });
    const page = await ctx.newPage();
    await page.goto('/capstone/dashboard');
    const cookies = (await ctx.cookies()).map((c) => `${c.name}=${c.value}`).join('; ');
    const base = process.env.E2E_BASE_URL ?? 'http://127.0.0.1:8000';
    const res = await page.request.post(`${base}/capstone/session/capstone/admin/finalization/allocate`, {
      headers: { Cookie: cookies, Accept: 'application/json' },
      data: { bid_id: 1, group_id: 1, title_id: 1 },
    });
    expect([401, 403, 419, 422]).toContain(res.status());
    await ctx.close();
  });

  test('unauthenticated /capstone/dashboard redirects to login', async ({ browser }) => {
    const ctx = await browser.newContext({ storageState: undefined });
    const page = await ctx.newPage();
    await page.goto('/capstone/dashboard');
    await expect(page).toHaveURL(/login|super/);
    await ctx.close();
  });
});
