import { test as base, expect } from '@playwright/test';
import * as fs from 'node:fs';
import * as path from 'node:path';

const AUTH_DIR = 'tests/e2e/.auth';

const ACCOUNTS = {
  admin: {
    email: process.env.E2E_ADMIN_EMAIL ?? 'e2e.admin.capstone@undip.ac.id',
    password: process.env.E2E_ADMIN_PASSWORD ?? 'Password123!',
    file: 'admin.json',
    expectRedirect: /capstone/,
  },
  dosen: {
    email: process.env.E2E_DOSEN_EMAIL ?? 'e2e.dosen.capstone@undip.ac.id',
    password: process.env.E2E_DOSEN_PASSWORD ?? 'Password123!',
    file: 'dosen.json',
    expectRedirect: /dashboard|capstone/,
  },
  mahasiswa: {
    email: process.env.E2E_MHS_EMAIL ?? 'e2e.mhs.leader@students.undip.ac.id',
    password: process.env.E2E_MHS_PASSWORD ?? 'Password123!',
    file: 'mahasiswa.json',
    expectRedirect: /dashboard|capstone/,
  },
} as const;

for (const [role, account] of Object.entries(ACCOUNTS)) {
  base(`login via /super as ${role}`, async ({ page }) => {
    const authFile = path.join(AUTH_DIR, account.file);
    fs.mkdirSync(AUTH_DIR, { recursive: true });

    await page.goto('/super');
    await page.locator('.sk-card-title').waitFor({ timeout: 15000 });
    await expect(page.locator('.sk-card-title')).toContainText(/Developer Login/i);

    await page.locator('#email').fill(account.email);
    await page.locator('#password').fill(account.password);
    await page.locator('button.sk-btn-submit').click();

    await page.waitForURL(account.expectRedirect, { timeout: 20000 });

    // Enter Capstone shell. Admin lands directly, dosen/mahasiswa via global dashboard.
    await page.goto('/capstone/launch');
    await page.waitForURL(/capstone\/(dashboard|.*)/, { timeout: 20000 });

    await page.context().storageState({ path: authFile });
  });
}
