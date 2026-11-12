import { test, expect } from '@playwright/test';
import { gotoCapstone } from './fixtures';

// P0 smoke: every admin page must 200 and render shell, never 500.
const ADMIN_PATHS = [
  '/admin/dashboard',
  '/admin/users',
  '/admin/groups',
  '/admin/locations',
  '/admin/assessment-bank',
  '/admin/periods',
  '/admin/periods/new',
  '/admin/period-assessment-config',
  '/admin/peer-review',
  '/admin/evaluation-setup/grade-configuration',
  '/admin/document-requirements',
  '/admin/document-types',
  '/admin/document-uploads',
  '/admin/finalization',
  '/admin/schedule',
  '/admin/sempro',
  '/admin/expo',
  '/admin/ta-defense',
  '/admin/period-registrations',
  '/admin/analytics/progress',
  '/admin/peer-review-dashboard',
  '/admin/reports',
  '/admin/reports/final-grades',
  '/admin/reports/groups',
  '/admin/titles',
  '/admin/audit-logs',
];

test.describe('admin smoke all pages', () => {
  for (const path of ADMIN_PATHS) {
    test(`GET ${path} renders shell`, async ({ page }) => {
      const errors: string[] = [];
      page.on('pageerror', (e) => errors.push(String(e)));
      await gotoCapstone(page, path);
      await expect(page.locator('.sitkom-sidebar-capstone')).toBeVisible();
      await expect(page.locator('main')).toBeVisible();
      expect(errors).toEqual([]);
    });
  }
});
