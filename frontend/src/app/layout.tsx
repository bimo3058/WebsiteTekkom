import type { Metadata } from "next";
import "./globals.css";

export const metadata: Metadata = {
  title: "Beranda | SICATA",
  description: "Sistem Capstone dan Tugas Akhir terintegrasi WebsiteTekkom",
  icons: {
    icon: [{ url: "/images/UNDIPOfficial.png", type: "image/png" }],
    shortcut: "/images/UNDIPOfficial.png",
    apple: "/images/UNDIPOfficial.png",
  },
};

import { TooltipProvider } from "@/components/ui/tooltip";
import { PageMetadataSync } from "@/components/layout/PageMetadataSync";
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
        <PageMetadataSync />
        <Providers>
          <AuthProvider>
            <TooltipProvider>{children}</TooltipProvider>
          </AuthProvider>
        </Providers>
      </body>
    </html>
  );
}
