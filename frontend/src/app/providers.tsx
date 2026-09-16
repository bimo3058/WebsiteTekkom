"use client";

import { QueryClient, QueryClientProvider } from "@tanstack/react-query";
import { useState } from "react";
import api from "@/lib/api";

export default function Providers({ children }: { children: React.ReactNode }) {
  const [queryClient] = useState(
    () =>
      new QueryClient({
        defaultOptions: {
          queries: {
            // Keep navigation instant while mutations still invalidate stale data.
            staleTime: 2 * 60 * 1000,
            refetchOnWindowFocus: false,
            refetchOnReconnect: true,
            placeholderData: (previousData: unknown) => previousData,
            retry: (failureCount, error) => {
              const status = api.isAxiosError(error)
                ? (error.response?.status ?? 0)
                : 0;

              return failureCount < 1 && (status === 0 || status >= 500);
            },
            // Preserve visited screens long enough for back/forward navigation.
            gcTime: 30 * 60 * 1000,
          },
          mutations: {
            retry: false,
          },
        },
      }),
  );

  return (
    <QueryClientProvider client={queryClient}>{children}</QueryClientProvider>
  );
}
