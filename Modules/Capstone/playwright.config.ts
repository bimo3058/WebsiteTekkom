import { defineConfig, devices } from '@playwright/test';

export default defineConfig({
  testDir: './tests/e2e',
  fullyParallel: false,
  forbidOnly: !!process.env.CI,
  retries: process.env.CI ? 1 : 0,
  workers: process.env.CI ? 1 : undefined,
  timeout: 60_000,
  expect: { timeout: 15_000 },
  reporter: [
    ['list'],
    ['html', { outputFolder: 'tests/artifacts/html', open: 'never' }],
  ],
  use: {
    baseURL: process.env.E2E_BASE_URL ?? 'http://127.0.0.1:8000',
    trace: 'retain-on-failure',
    screenshot: 'only-on-failure',
    video: 'off',
  },
  projects: [
    { name: 'setup', testMatch: /auth\.setup\.ts/ },
    {
      name: 'mahasiswa',
      testMatch: /mahasiswa\.journey\.spec\.ts/,
      dependencies: ['setup'],
      use: { storageState: 'tests/e2e/.auth/mahasiswa.json' },
    },
    {
      name: 'dosen',
      testMatch: /dosen\.workspace\.spec\.ts/,
      dependencies: ['setup'],
      use: { storageState: 'tests/e2e/.auth/dosen.json' },
    },
    {
      name: 'admin',
      testMatch: /(admin\.smoke|admin\.critical)\.spec\.ts/,
      dependencies: ['setup'],
      use: { storageState: 'tests/e2e/.auth/admin.json' },
    },
    {
      name: 'lifecycle',
      testMatch: /lifecycle\.chained\.spec\.ts/,
      dependencies: ['setup'],
      use: { storageState: 'tests/e2e/.auth/admin.json' },
    },
    {
      name: 'rbac',
      testMatch: /rbac\.spec\.ts/,
      dependencies: ['setup'],
    },
    {
      name: 'ux-audit',
      testMatch: /ux-audit\.shell\.spec\.ts/,
      dependencies: ['setup'],
      use: { storageState: 'tests/e2e/.auth/mahasiswa.json' },
    },
    {
      name: 'mobile-767px',
      testMatch: /ux-audit\.shell\.spec\.ts/,
      dependencies: ['setup'],
      use: {
        ...devices['iPhone 12'],
        viewport: { width: 767, height: 800 },
        storageState: 'tests/e2e/.auth/mahasiswa.json',
      },
    },
  ],
  webServer: undefined,
});
