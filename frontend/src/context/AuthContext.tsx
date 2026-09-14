"use client";

import React, {
  createContext,
  useCallback,
  useContext,
  useEffect,
  useState,
} from "react";
import { usePathname, useRouter } from "next/navigation";
import api, { clearApiResponseCache } from "@/lib/api";
import { usePrefetchDashboards } from "@/hooks/use-prefetch-dashboards";
import {
  getAccessToken,
  performFastLogout,
  redirectToWebsiteLogin,
  SSO_ROLE_KEY,
  SSO_USER_KEY,
} from "@/lib/sso";

interface User {
  id: number;
  name: string;
  email: string;
  role: string;
  roles: string[];
  active_role?: string;
  student_id?: number | null;
  lecturer_id?: number | null;
  nim?: string | null;
  nip?: string | null;
}

interface AuthContextType {
  user: User | null;
  activeRole: string | null;
  login: (userData: User, roles: string[]) => void;
  logout: () => void;
  switchRole: (role: string) => Promise<void>;
  isLoading: boolean;
}

const AuthContext = createContext<AuthContextType | undefined>(undefined);

const readUserSnapshot = (): User | null => {
  if (typeof window === "undefined") return null;

  try {
    return JSON.parse(window.sessionStorage.getItem(SSO_USER_KEY) ?? "null") as User | null;
  } catch {
    window.sessionStorage.removeItem(SSO_USER_KEY);
    return null;
  }
};

const writeUserSnapshot = (user: User): void => {
  window.sessionStorage.setItem(SSO_USER_KEY, JSON.stringify(user));
};

export const AuthProvider = ({ children }: { children: React.ReactNode }) => {
  const [user, setUser] = useState<User | null>(null);
  const [activeRole, setActiveRole] = useState<string | null>(null);
  const [isLoading, setIsLoading] = useState(true);
  const pathname = usePathname();
  const router = useRouter();
  const { prefetchRoleDashboard } = usePrefetchDashboards();
  const isExchangeRoute = pathname.startsWith("/auth/exchange");

  useEffect(() => {
    if (isExchangeRoute) {
      // Route state is synchronized before any asynchronous auth request.
      // eslint-disable-next-line react-hooks/set-state-in-effect
      setIsLoading(false);
      return;
    }

    if (!getAccessToken()) {
      setIsLoading(false);
      redirectToWebsiteLogin();
      return;
    }

    let cancelled = false;
    const snapshot = readUserSnapshot();

    if (snapshot) {
      const storedRole = window.sessionStorage.getItem(SSO_ROLE_KEY);
      const snapshotRole = storedRole && snapshot.roles?.includes(storedRole)
        ? storedRole
        : (snapshot.active_role ?? snapshot.roles?.[0] ?? snapshot.role);

      setUser(snapshot);
      setActiveRole(snapshotRole);
      setIsLoading(false);
      void prefetchRoleDashboard(snapshotRole);
    }

    api.get("/auth/user")
      .then((response) => {
        if (cancelled) return;

        const userData = response.data?.data as User | undefined;
        if (!userData) throw new Error("No user data in response");

        const storedRole = window.sessionStorage.getItem(SSO_ROLE_KEY);
        const selectedRole = storedRole && userData.roles?.includes(storedRole)
          ? storedRole
          : (userData.active_role ?? userData.roles?.[0] ?? userData.role);

        setUser(userData);
        setActiveRole(selectedRole);
        writeUserSnapshot(userData);
        window.sessionStorage.setItem(SSO_ROLE_KEY, selectedRole);
        void prefetchRoleDashboard(selectedRole);
      })
      .catch(() => {
        if (!cancelled) {
          setUser(null);
          setActiveRole(null);
          window.sessionStorage.removeItem(SSO_USER_KEY);
        }
      })
      .finally(() => {
        if (!cancelled) setIsLoading(false);
      });

    return () => {
      cancelled = true;
    };
  }, [isExchangeRoute, prefetchRoleDashboard]);

  const login = useCallback((userData: User, roles: string[]) => {
    const userWithRoles = { ...userData, roles };
    const targetRole = userData.active_role ?? roles[0] ?? userData.role;

    setUser(userWithRoles);
    setActiveRole(targetRole);
    writeUserSnapshot(userWithRoles);
    window.sessionStorage.setItem(SSO_ROLE_KEY, targetRole);
    void prefetchRoleDashboard(targetRole);
    router.replace(`/${targetRole}/dashboard`);
  }, [prefetchRoleDashboard, router]);

  const switchRole = useCallback(async (role: string) => {
    if (!user?.roles.includes(role)) return;

    await api.post("/auth/active-role", { role });
    setActiveRole(role);
    writeUserSnapshot({ ...user, active_role: role });
    window.sessionStorage.setItem(SSO_ROLE_KEY, role);
    void prefetchRoleDashboard(role);
    router.push(`/${role}/dashboard`);
  }, [prefetchRoleDashboard, router, user]);

  const logout = useCallback(() => {
    setUser(null);
    setActiveRole(null);
    clearApiResponseCache();
    performFastLogout();
  }, []);

  return (
    <AuthContext.Provider
      value={{ user, activeRole, login, logout, switchRole, isLoading }}
    >
      {children}
    </AuthContext.Provider>
  );
};

export const useAuth = () => {
  const context = useContext(AuthContext);
  if (context === undefined) {
    throw new Error("useAuth must be used within an AuthProvider");
  }
  return context;
};
