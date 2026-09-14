"use client";

import { useEffect } from "react";
import { redirectToWebsiteLogin } from "@/lib/sso";

export default function LoginPage() {
  useEffect(() => {
    redirectToWebsiteLogin();
  }, []);

  return (
    <main className="flex min-h-screen items-center justify-center">
      <p className="text-sm text-muted-foreground">Mengalihkan ke SSO WebsiteTekkom…</p>
    </main>
  );
}
