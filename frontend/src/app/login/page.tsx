"use client";

import { useEffect } from "react";

export default function LoginPage() {
    useEffect(() => {
        window.location.href = process.env.NEXT_PUBLIC_WEBTEKKOM_URL || "/";
    }, []);

    return <p>Mengalihkan ke login...</p>;
}
