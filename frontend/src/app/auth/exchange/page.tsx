"use client";

import { Suspense, useEffect, useState } from "react";
import { useSearchParams } from "next/navigation";
import { isAxiosError, type AxiosResponse } from "axios";
import api from "@/lib/api";
import { createSingleFlightByKey } from "@/lib/single-flight";
import {
  redirectToWebsiteLogin,
  clearSsoSession,
  saveSsoSession,
  SSO_ROLE_KEY,
} from "@/lib/sso";

interface ExchangePayload {
  access_token: string;
  user: {
    roles?: string[];
    role?: string;
    active_role?: string;
  };
  logout?: {
    url: string;
    csrf_token: string | null;
  };
}

interface ExchangeEnvelope {
  data?: ExchangePayload;
  access_token?: string;
  user?: ExchangePayload["user"];
  logout?: ExchangePayload["logout"];
}

interface ExchangeError {
  code?: string;
  redirect_url?: string;
}

// The backend deliberately consumes an OTT only once. React may re-run an
// effect while checking it in development, so every component instance must
// share the same in-flight request for a given OTT.
const exchangeOttOnce = createSingleFlightByKey(
  (ott): Promise<AxiosResponse<ExchangeEnvelope>> =>
    api.post<ExchangeEnvelope>("/auth/exchange", { ott })
);

function ExchangeContent() {
  const searchParams = useSearchParams();
  const [message, setMessage] = useState("Menghubungkan sesi SSO…");

  useEffect(() => {
    const ott = searchParams.get("ott");

    if (!ott) {
      redirectToWebsiteLogin();
      return;
    }

    let cancelled = false;

    exchangeOttOnce(ott)
      .then((response) => {
        if (cancelled) return;

        const payload = (response.data?.data ??
          response.data) as ExchangePayload;
        if (!payload.access_token || !payload.user) {
          throw new Error("Respons pertukaran sesi tidak lengkap.");
        }

        // Persist the verified user snapshot before redirecting. The dashboard
        // can render immediately while AuthContext revalidates it in background.
        saveSsoSession(payload.access_token, payload.logout, payload.user);

        const roles = payload.user.roles ?? [payload.user.role ?? "mahasiswa"];
        const activeRole = payload.user.active_role ?? roles[0] ?? "mahasiswa";
        window.sessionStorage.setItem(SSO_ROLE_KEY, activeRole);
        window.location.replace(`/${activeRole}/dashboard`);
      })
      .catch((error: unknown) => {
        if (cancelled) return;

        if (isAxiosError<ExchangeError>(error)) {
          const data = error.response?.data;
          if (
            data?.code === "SICATA_STUDENT_NOT_REGISTERED" &&
            data.redirect_url
          ) {
            clearSsoSession();
            window.location.replace(data.redirect_url);
            return;
          }
        }

        setMessage("Sesi SSO tidak valid. Mengalihkan ke halaman login…");
        window.setTimeout(redirectToWebsiteLogin, 300);
      });

    return () => {
      cancelled = true;
    };
  }, [searchParams]);

  return <p className="text-muted-foreground text-sm">{message}</p>;
}

export default function ExchangePage() {
  return (
    <main className="flex min-h-screen items-center justify-center">
      <Suspense
        fallback={
          <p className="text-muted-foreground text-sm">
            Menghubungkan sesi SSO…
          </p>
        }
      >
        <ExchangeContent />
      </Suspense>
    </main>
  );
}
