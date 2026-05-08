"use client";
import { useEffect, useState } from "react";
import { useRouter, useSearchParams } from "next/navigation";
import api from "@/lib/api";

export default function ExchangePage() {
    const router = useRouter();
    const searchParams = useSearchParams();
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        const ott = searchParams.get("ott");

        // Tidak ada OTT = bukan dari redirect webtekkom, abaikan
        if (!ott) return;

        // Cek apakah OTT ini sudah pernah diproses (cegah React Strict Mode double-fire)
        const processedOtt = sessionStorage.getItem("processed_ott");
        if (processedOtt === ott) return;

        sessionStorage.setItem("processed_ott", ott);

        api.post("/auth/exchange", { ott })
            .then((res) => {
                // Handle double-encoded JSON dari Laravel
                console.log("typeof res.data:", typeof res.data);
                console.log(
                    "res.data === object?",
                    typeof res.data === "object",
                );
                console.log("res.data keys:", Object.keys(res.data || {}));
                console.log(
                    "res.data.access_token:",
                    res.data?.access_token,
                );
                console.log("res.data.user:", res.data?.user);
                const data = typeof res.data === 'string' ? JSON.parse(res.data) : res.data;
                const { access_token, user } = data;

                if (!access_token || !user || !user.role) {
                    console.error("Response tidak lengkap:", data);
                    sessionStorage.removeItem("processed_ott");
                    setError("Response tidak lengkap.");
                    return;
                }

                window.history.replaceState({}, "", "/auth/exchange");
                localStorage.setItem("token", access_token);
                localStorage.setItem("user", JSON.stringify(user));
                api.defaults.headers.common["Authorization"] = `Bearer ${access_token}`;
                sessionStorage.removeItem("processed_ott");
                router.replace(`/${user.role}/dashboard`);
            })
            .catch((err) => {
                console.error("CATCH:", err);
                sessionStorage.removeItem("processed_ott");
                setError(
                    err.response?.data?.message || "Gagal verifikasi token.",
                );
            });
    }, [searchParams, router]);

    if (error) {
        return (
            <div className="min-h-screen flex flex-col items-center justify-center gap-4">
                <p className="text-red-600">{error}</p>
                <a
                    href={process.env.NEXT_PUBLIC_WEBTEKKOM_URL || "/"}
                    className="underline"
                >
                    Kembali ke beranda
                </a>
            </div>
        );
    }

    return (
        <div className="min-h-screen flex items-center justify-center">
            <p>Memverifikasi sesi...</p>
        </div>
    );
}
