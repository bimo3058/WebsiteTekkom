'use client';

import React, { createContext, useContext, useState, useEffect } from 'react';
import api from '@/lib/api';
import { useRouter, usePathname } from 'next/navigation';

interface User {
    id: number;
    name: string;
    email: string;
    role: 'admin' | 'dosen' | 'mahasiswa';
}

interface AuthContextType {
    user: User | null;
    login: (token: string, userData: User) => void;
    logout: () => void;
    isLoading: boolean;
}

const AuthContext = createContext<AuthContextType | undefined>(undefined);

export const AuthProvider = ({ children }: { children: React.ReactNode }) => {
    const [user, setUser] = useState<User | null>(null);
    const [isLoading, setIsLoading] = useState(true);
    const router = useRouter();
    const pathname = usePathname(); // ← tambah ini

    useEffect(() => {
        // Skip checkAuth di halaman exchange
        if (pathname === "/auth/exchange") {
            setIsLoading(false);
            return;
        }

        const checkAuth = async () => {
            try {
                const token = localStorage.getItem("token");
                if (token) {
                    api.defaults.headers.common["Authorization"] =
                        `Bearer ${token}`;
                    const response = await api.get("/user");
                    setUser(response.data);
                }
            } catch (error) {
                console.error("Auth check failed", error);
                localStorage.removeItem("token");
            } finally {
                setIsLoading(false);
            }
        };

        checkAuth();
    }, [pathname]);

    const login = (token: string, userData: User) => {
        localStorage.setItem("token", token);
        api.defaults.headers.common["Authorization"] = `Bearer ${token}`;
        setUser(userData);
        router.push(`/${userData.role}/dashboard`);
    };

    const logout = async () => {
        try {
            await api.post("/capstone/auth/logout"); // /api/capstone/auth/logout (kalau prefix di-baseURL)
        } catch (e) {
            /* ignore */
        }

        localStorage.removeItem("token");
        localStorage.removeItem("user");
        window.location.href = process.env.NEXT_PUBLIC_WEBTEKKOM_URL || "/";
    };

    return (
        <AuthContext.Provider value={{ user, login, logout, isLoading }}>
            {children}
        </AuthContext.Provider>
    );
};

export const useAuth = () => {
    const context = useContext(AuthContext);
    if (context === undefined) {
        throw new Error('useAuth must be used within an AuthProvider');
    }
    return context;
};
