import { useQueryClient } from "@tanstack/react-query";
import { useCallback } from "react";
import api from "@/lib/api";

// Query keys for prefetching
const queryKeys = {
  admin: {
    dashboard: ["admin", "dashboard"],
    periods: ["admin", "periods"],
    groups: ["admin", "groups"],
  },
  dosen: {
    dashboard: (periodId?: string) => ["dosen", "dashboard", periodId],
    supervised: (periodId?: string) => ["dosen", "supervised", periodId],
    evalCount: ["dosen", "eval-count"],
  },
  mahasiswa: {
    myPeriod: ["mahasiswa", "my-period"],
    dashboard: ["mahasiswa", "dashboard"],
    workflow: ["mahasiswa", "workflow"],
  },
};

// Fetch functions
const fetchAdminDashboard = async () => {
  const response = await api.get("/admin/dashboard");
  return response.data?.data ?? response.data;
};

const fetchAdminPeriods = async () => {
  const response = await api.get("/admin/periods");
  return response.data?.data || [];
};

const fetchAdminGroups = async () => {
  const response = await api.get("/admin/groups", { params: { per_page: 5 } });
  return response.data?.data ?? response.data;
};

const fetchDosenDashboard = async (periodId?: string) => {
  const params = periodId ? { period_id: periodId } : undefined;
  const response = await api.get("/dosen/dashboard", { params });
  return response.data?.data ?? response.data;
};

const fetchDosenSupervised = async (periodId?: string) => {
  const params = periodId ? { period_id: periodId } : undefined;
  const response = await api.get("/dosen/groups/supervised", { params });
  return response.data?.data ?? response.data;
};

const fetchDosenEvalCount = async () => {
  const response = await api.get("/dosen/supervisor-evaluation/pending-count");
  return response.data?.data ?? response.data;
};

const fetchMahasiswaMyPeriod = async () => {
  const response = await api.get("/mahasiswa/my-period");
  return response.data?.data ?? response.data;
};

const fetchMahasiswaDashboard = async () => {
  const response = await api.get("/mahasiswa/dashboard");
  return response.data?.data ?? response.data;
};

const fetchMahasiswaWorkflow = async () => {
  const response = await api.get("/mahasiswa/dashboard/workflow");
  return response.data?.data ?? response.data;
};

const fetchMahasiswaGroup = async () => {
  const response = await api.get("/mahasiswa/group");
  return response.data?.data ?? response.data;
};

const fetchMahasiswaSchedules = async () => {
  const response = await api.get("/mahasiswa/all-schedules");
  return response.data?.data ?? response.data;
};

export function usePrefetchDashboards() {
  const queryClient = useQueryClient();

  const prefetchAdminDashboards = useCallback(async () => {
    await Promise.all([
      queryClient.prefetchQuery({
        queryKey: queryKeys.admin.dashboard,
        queryFn: fetchAdminDashboard,
      }),
      queryClient.prefetchQuery({
        queryKey: queryKeys.admin.periods,
        queryFn: fetchAdminPeriods,
      }),
      queryClient.prefetchQuery({
        queryKey: queryKeys.admin.groups,
        queryFn: fetchAdminGroups,
      }),
    ]);
  }, [queryClient]);

  const prefetchDosenDashboards = useCallback(async () => {
    await Promise.all([
      queryClient.prefetchQuery({
        queryKey: queryKeys.dosen.dashboard(undefined),
        queryFn: () => fetchDosenDashboard(undefined),
      }),
      queryClient.prefetchQuery({
        queryKey: queryKeys.dosen.supervised(undefined),
        queryFn: () => fetchDosenSupervised(undefined),
      }),
      queryClient.prefetchQuery({
        queryKey: queryKeys.dosen.evalCount,
        queryFn: fetchDosenEvalCount,
      }),
    ]);
  }, [queryClient]);

  const prefetchMahasiswaDashboards = useCallback(async () => {
    // Warm every dashboard request concurrently. The aggregate dashboard hook
    // then reuses the Axios in-flight/cache entries without a registration RTT.
    await Promise.all([
      queryClient.prefetchQuery({
        queryKey: queryKeys.mahasiswa.myPeriod,
        queryFn: fetchMahasiswaMyPeriod,
      }),
      queryClient.prefetchQuery({
        queryKey: ["mahasiswa", "dashboard-stats"],
        queryFn: fetchMahasiswaDashboard,
      }),
      queryClient.prefetchQuery({
        queryKey: ["mahasiswa", "group"],
        queryFn: fetchMahasiswaGroup,
      }),
      queryClient.prefetchQuery({
        queryKey: ["mahasiswa", "schedules"],
        queryFn: fetchMahasiswaSchedules,
      }),
      queryClient.prefetchQuery({
        queryKey: queryKeys.mahasiswa.workflow,
        queryFn: fetchMahasiswaWorkflow,
      }),
    ]);
  }, [queryClient]);

  const prefetchRoleDashboard = useCallback(async (role: string) => {
    if (role === "admin") return prefetchAdminDashboards();
    if (role === "dosen") return prefetchDosenDashboards();
    if (role === "mahasiswa") return prefetchMahasiswaDashboards();
  }, [prefetchAdminDashboards, prefetchDosenDashboards, prefetchMahasiswaDashboards]);

  const prefetchAllDashboards = useCallback(async (roles: string[]) => {
    const prefetchPromises: Promise<void>[] = [];

    if (roles.includes("admin")) {
      prefetchPromises.push(prefetchAdminDashboards());
    }

    if (roles.includes("dosen")) {
      prefetchPromises.push(prefetchDosenDashboards());
    }

    if (roles.includes("mahasiswa")) {
      prefetchPromises.push(prefetchMahasiswaDashboards());
    }

    await Promise.all(prefetchPromises);
  }, [prefetchAdminDashboards, prefetchDosenDashboards, prefetchMahasiswaDashboards]);

  return {
    prefetchAdminDashboards,
    prefetchDosenDashboards,
    prefetchMahasiswaDashboards,
    prefetchRoleDashboard,
    prefetchAllDashboards,
  };
}
