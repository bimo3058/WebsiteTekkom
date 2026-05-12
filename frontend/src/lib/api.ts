import axios from "axios";

const BASE = (process.env.NEXT_PUBLIC_API_URL || "http://localhost:8000/api").replace(/\/$/, "");

const api = axios.create({
    baseURL: BASE + "/capstone",
    headers: {
        "Content-Type": "application/json",
        "Accept": "application/json",
    },
});

api.interceptors.request.use((config) => {
    if (typeof window !== "undefined") {
        const token = localStorage.getItem("token");
        if (token) config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
});

// Auto-redirect ke webtekkom kalau token expired
api.interceptors.response.use(
    (r) => r,
    (err) => {
        if (err.response?.status === 401 && typeof window !== "undefined") {
            // Jangan interrupt halaman exchange
            if (window.location.pathname.startsWith("/auth/exchange")) {
                return Promise.reject(err);
            }
            localStorage.removeItem("token");
            localStorage.removeItem("user");
            const webtekkomUrl = process.env.NEXT_PUBLIC_WEBTEKKOM_URL || "/";
            window.location.href = `${webtekkomUrl}/dashboard`;
        }
        return Promise.reject(err);
    },
);

export default api;
