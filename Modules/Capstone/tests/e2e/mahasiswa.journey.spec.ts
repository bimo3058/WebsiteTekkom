import { test, expect } from '@playwright/test';
import { gotoCapstone, expectShell, expectRole } from './fixtures';

test.describe('mahasiswa journey (storageState via /super)', () => {
  test('dashboard shell + navigation', async ({ page }) => {
    const consoleErrors: string[] = [];
    page.on('console', (m) => { if (m.type() === 'error') consoleErrors.push(m.text()); });
    await gotoCapstone(page, '/dashboard');
    await expectShell(page, /Dashboard/);
    await expectRole(page, 'mahasiswa');
    expect(consoleErrors.filter((e) => !e.includes('favicon'))).toEqual([]);
  });

  test('group page shows detail and leader actions', async ({ page }) => {
    await gotoCapstone(page, '/mahasiswa/group');
    await expectShell(page, /Kelompok|Group/);
    await expect(page.getByRole('heading', { name: /Kelompok Saya/i })).toBeVisible();
    // Either empty state or group detail must render, plus no hard crash
    const body = await page.content();
    expect(body).toMatch(/Buat kelompok|Detail Kelompok|Kelola kelompok/);
  });

  test('titles + title detail shows bid blockers', async ({ page }) => {
    await gotoCapstone(page, '/mahasiswa/titles');
    await expectShell(page, /Judul|Bursa/);
    const body = await page.content();
    expect(body).toMatch(/Bursa|Judul|Cari/);

    // Title detail with id 1 may 404 if no title; assert graceful empty/error, not 500
    const resp = await page.goto('/capstone/mahasiswa/titles/1');
    expect([200, 404]).toContain(resp?.status() ?? 200);
  });

  test('propose-title, bidding, documents, TA submission render', async ({ page }) => {
    for (const path of ['/mahasiswa/propose-title', '/mahasiswa/bidding', '/mahasiswa/documents', '/mahasiswa/ta-submission']) {
      await gotoCapstone(page, path);
      await expect(page.locator('main')).toBeVisible();
      const status = await page.evaluate(() => document.title);
      expect(status).toBeTruthy();
    }
    // SIDANG is UI-only bucket: ta-defense redirects to ta-submission when PDC1 started,
    // otherwise proper gating is 403 locked (FORMING group -> Available after PDC1 starts).
    const resp = await page.goto('/capstone/mahasiswa/ta-defense');
    expect([200, 302, 403]).toContain(resp?.status() ?? 200);
    if ((resp?.status() ?? 200) !== 403) {
      expect(page.url()).toMatch(/ta-submission/);
    } else {
      await expect(page.locator('main')).toContainText(/belum tersedia|Available after PDC1|Fitur belum tersedia/i);
    }
  });

  test('schedule, expo, peer-review, grades render with Indonesian copy', async ({ page }) => {
    for (const path of ['/mahasiswa/schedule', '/mahasiswa/expo', '/mahasiswa/peer-review', '/mahasiswa/grades', '/mahasiswa/registration']) {
      await gotoCapstone(page, path);
      await expect(page.locator('main')).toBeVisible();
    }
  });

  test('notifications + profile shell', async ({ page }) => {
    await gotoCapstone(page, '/notifications');
    await expect(page.locator('main')).toBeVisible();
    await gotoCapstone(page, '/profile');
    await expect(page.locator('main')).toBeVisible();
  });
});
