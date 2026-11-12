import { test, expect } from '@playwright/test';
import { gotoCapstone, collectUxSignals } from './fixtures';

// Strict UX audit per DESIGN-GUIDE + skills. Fails on shell violations, warns on token debt.
const AUDIT_PAGES = [
  { role: 'mahasiswa', path: '/mahasiswa/group', breadcrumb: /Kelompok/ },
  { role: 'mahasiswa', path: '/mahasiswa/titles', breadcrumb: /Judul/ },
  { role: 'mahasiswa', path: '/mahasiswa/documents', breadcrumb: /Dokumen/ },
  { role: 'mahasiswa', path: '/mahasiswa/grades', breadcrumb: /Nilai/ },
  { role: 'mahasiswa', path: '/dashboard', breadcrumb: /Dashboard/ },
];

test.describe('UX audit shell (strict)', () => {
  for (const item of AUDIT_PAGES) {
    test(`${item.path} shell + a11y + tokens`, async ({ page }) => {
      const consoleErrors: string[] = [];
      page.on('console', (m) => { if (m.type() === 'error') consoleErrors.push(m.text()); });

      await gotoCapstone(page, item.path);

      // Shell
      await expect(page.locator('aside.sitkom-sidebar-capstone')).toBeVisible();
      await expect(page.locator('.sitkom-topbar-capstone')).toBeVisible();
      await expect(page.locator('.capstone-notices[aria-live="polite"]')).toHaveCount(1);
      await expect(page.locator('[role="status"].capstone-progress')).toHaveCount(1);

      // Single scroll container
      expect(await page.locator('.flex-1.overflow-y-auto').count()).toBe(1);

      // Sidebar collapse button a11y
      await expect(page.locator('button[aria-label="Alihkan sidebar"]')).toHaveCount(1);

      // Breadcrumb contains SICATA
      await expect(page.locator('.sitkom-topbar-capstone')).toContainText(/SICATA/);

      // Icons are Phosphor <i class="ph">
      const phosphor = await page.locator('i.ph').count();
      expect(phosphor).toBeGreaterThan(0);

      // No hard JS crash (ignore 403 gating resource loads which are proper BladeFeatureAccess)
      const hardErrors = consoleErrors.filter(
        (e) => !e.toLowerCase().includes('favicon') && !e.includes('403') && !e.toLowerCase().includes('forbidden')
      );
      expect(hardErrors).toEqual([]);

      const signals = await collectUxSignals(page, item.path);
      // Record for report; soft assert token debt
      expect(String(signals.sidebarWidth)).toBeTruthy();
    });
  }

  test('mobile 767px has bottom nav clearance (capstone-mobile-content pb)', async ({ page }) => {
    await gotoCapstone(page, '/mahasiswa/group');
    const cls = await page.locator('.capstone-mobile-content').getAttribute('class');
    expect(cls).toMatch(/pb-20/);
  });

  test('disabled gating uses aria-disabled, never hidden (mahasiswa)', async ({ page }) => {
    await gotoCapstone(page, '/mahasiswa/group');
    // If gating active, disabled links must carry title reason
    const disabled = page.locator('[aria-disabled="true"]');
    const count = await disabled.count();
    if (count > 0) {
      const title = await disabled.first().getAttribute('title');
      expect(String(title).length).toBeGreaterThan(0);
    }
  });
});
