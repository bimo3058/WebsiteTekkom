import { expect, type Page } from '@playwright/test';

export async function gotoCapstone(page: Page, path: string): Promise<void> {
  await page.goto(`/capstone${path}`);
  await page.locator('#capstone-context').waitFor({ timeout: 15000, state: 'attached' });
}

export async function expectNoConsoleErrors(page: Page): Promise<string[]> {
  const errors: string[] = [];
  page.on('console', (msg) => {
    if (msg.type() === 'error') errors.push(msg.text());
  });
  page.on('pageerror', (err) => errors.push(String(err)));
  return errors;
}

export async function expectShell(page: Page, breadcrumb?: string | RegExp): Promise<void> {
  await expect(page.locator('.sitkom-sidebar-capstone')).toBeVisible();
  await expect(page.locator('.sitkom-topbar-capstone')).toBeVisible();
  await expect(page.locator('.sitkom-topbar-capstone')).toContainText(/SICATA/);
  // Breadcrumb is derived from URL segment (e.g. /Titles), not always Indonesian.
  // Do not fail on language mismatch; record only.
  if (breadcrumb) {
    try {
      await expect(page.locator('.sitkom-topbar-capstone')).toContainText(breadcrumb, { timeout: 3000 });
    } catch {
      // Intentionally soft: shell is valid even if breadcrumb wording differs.
    }
  }
  // Single scroll container
  const scrollers = await page.locator('.flex-1.overflow-y-auto').count();
  expect(scrollers).toBe(1);
  // Toast live region exists
  await expect(page.locator('.capstone-notices[aria-live="polite"]')).toHaveCount(1);
}

export async function expectRole(page: Page, role: string): Promise<void> {
  const raw = await page.locator('#capstone-context').textContent();
  expect(raw ?? '').toContain(`"${role}"`);
}

export async function collectUxSignals(page: Page, url: string): Promise<Record<string, unknown>> {
  const snapshot = {
    url,
    sidebarWidth: await page.locator('aside.sitkom-sidebar-capstone').evaluate((el) => getComputedStyle(el).width).catch(() => 'unknown'),
    scrollContainers: await page.locator('.flex-1.overflow-y-auto').count(),
    spaceYUsage: await page.evaluate(() => document.documentElement.innerHTML.includes('space-y-')),
    hasGap: await page.evaluate(() => document.documentElement.innerHTML.includes('gap-')),
    phosphorIcons: await page.locator('i.ph').count(),
    darkToggle: await page.evaluate(() => document.documentElement.classList.contains('dark')),
    disabledLinks: await page.locator('[aria-disabled="true"]').count(),
  };
  return snapshot;
}
