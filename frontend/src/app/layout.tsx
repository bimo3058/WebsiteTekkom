import type { Metadata } from "next";
import "./globals.css";

export const metadata: Metadata = {
  title: "Capstone + TA | Teknik Komputer",
  description: "Sistem Capstone dan Tugas Akhir terintegrasi WebsiteTekkom",
};

import { TooltipProvider } from "@/components/ui/tooltip";
import { AuthProvider } from "@/context/AuthContext";
import Providers from "./providers";

export default function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  return (
    <html lang="id">
      <body className="antialiased">
        <Providers>
          <AuthProvider>
            <TooltipProvider>{children}</TooltipProvider>
          </AuthProvider>
        </Providers>
      </body>
    </html>
  );
}
