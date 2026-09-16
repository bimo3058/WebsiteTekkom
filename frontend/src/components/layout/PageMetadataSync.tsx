"use client";

import { useEffect } from "react";
import { usePathname } from "next/navigation";
import { getDocumentTitle } from "@/lib/page-title";

export function PageMetadataSync() {
  const pathname = usePathname();

  useEffect(() => {
    document.title = getDocumentTitle(pathname);
  }, [pathname]);

  return null;
}
