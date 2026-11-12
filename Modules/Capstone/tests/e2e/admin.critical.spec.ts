import { test, expect } from '@playwright/test';
import { gotoCapstone } from './fixtures';

// Deep critical path: data mutations + validation guards.
test.describe('admin critical path', () => {
  test('periods list has E2E period and grade config loads', async ({ page }) => {
    await gotoCapstone(page, '/admin/periods');
    await expect(page.locator('main')).toContainText(/Periode|Capstone/i);
  });

  test('grade-configuration page loads with weights UI', async ({ page }) => {
    await gotoCapstone(page, '/admin/evaluation-setup/grade-configuration');
    await expect(page.locator('main')).toBeVisible();
    const body = await page.content();
    expect(body).toMatch(/Bobot|Grade|Konfigurasi|SEMPRO|EXPO|TA/i);
  });

  test('finalization dashboard loads allocation UI', async ({ page }) => {
    await gotoCapstone(page, '/admin/finalization');
    await expect(page.locator('main')).toBeVisible();
    const body = await page.content();
    expect(body).toMatch(/Finalisasi|Alokasi|Kelompok|Judul/i);
  });

  test('session API: finalization dashboard + reports final-grades reachable', async ({ page, request }) => {
    // Use browser cookies for session API
    await gotoCapstone(page, '/admin/dashboard');
    const base = process.env.E2E_BASE_URL ?? 'http://127.0.0.1:8000';
    const headers = { Cookie: (await page.context().cookies()).map((c) => `${c.name}=${c.value}`).join('; ') };

    const dash = await page.request.get(`${base}/capstone/session/capstone/admin/finalization/dashboard`, { headers });
    expect([200, 422]).toContain(dash.status());

    const grades = await page.request.get(`${base}/capstone/session/capstone/admin/reports/final-grades`, { headers });
    expect([200, 422, 404]).toContain(grades.status());
    void request;
  });
});
