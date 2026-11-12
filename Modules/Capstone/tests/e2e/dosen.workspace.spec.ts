import { test, expect } from '@playwright/test';
import { gotoCapstone, expectShell, expectRole } from './fixtures';

test.describe('dosen workspace', () => {
  test('dashboard + titles + title-approvals + bids render', async ({ page }) => {
    await gotoCapstone(page, '/dashboard');
    await expectShell(page, /Dashboard/);
    await expectRole(page, 'dosen');

    for (const path of ['/dosen/titles', '/dosen/title-approvals', '/dosen/bids']) {
      await gotoCapstone(page, path);
      await expect(page.locator('main')).toBeVisible();
    }
  });

  test('mentoring: supervised-groups, bimbingan, schedule', async ({ page }) => {
    for (const path of ['/dosen/supervised-groups', '/dosen/bimbingan', '/dosen/schedule', '/dosen/requests']) {
      await gotoCapstone(page, path);
      await expect(page.locator('main')).toBeVisible();
    }
  });

  test('evaluation + supervisor-evaluation render without JS crash', async ({ page }) => {
    const errors: string[] = [];
    page.on('pageerror', (e) => errors.push(String(e)));
    for (const path of ['/dosen/evaluation', '/dosen/supervisor-evaluation', '/dosen/ta-review']) {
      await gotoCapstone(page, path);
      await expect(page.locator('main')).toBeVisible();
    }
    expect(errors).toEqual([]);
  });
});
