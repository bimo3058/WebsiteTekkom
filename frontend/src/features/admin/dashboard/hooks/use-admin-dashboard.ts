"use client";

import { useMemo } from 'react';
import { useQuery } from '@tanstack/react-query';
import api from '@/lib/api';
import type {
    AdminDashboardResponse,
    AdminDashboardData,
    AdminDashboardGroupItem,
} from '@/features/admin/dashboard/types';

const QUERY_KEY = ["admin", "dashboard"] as const;

const fetchDashboard = async (): Promise<AdminDashboardResponse> => {
    const response = await api.get('/admin/dashboard');
    return response.data?.data ?? response.data;
};

export function useAdminDashboard() {
    const { data: dashboardData, isLoading: isDashboardLoading } = useQuery({
        queryKey: QUERY_KEY,
        queryFn: fetchDashboard,
    });

    const data = useMemo<AdminDashboardData | null>(() => {
        if (!dashboardData) return null;

        const groups = dashboardData.recent_groups ?? [];

        const totalUsers = dashboardData?.total_users ?? 0;
        const pendingFinalization = dashboardData.pending_finalization ?? 0;

        const recentGroups = (Array.isArray(groups) ? groups.slice(0, 5) : []).map(
            (g: AdminDashboardGroupItem) => ({
                id: g.id,
                label: g.code || `Group ${g.id}`,
                subtitle: g.status || '',
                status: {
                    label: g.status || 'Unknown',
                    variant: (g.status === 'READY_FOR_FINALIZATION'
                        ? 'secondary'
                        : g.status === 'CLOSED'
                            ? 'default'
                            : 'outline') as
                        | 'default'
                        | 'secondary'
                        | 'destructive'
                        | 'outline',
                },
                href: '/admin/groups',
            })
        );

        return {
            totalUsers,
            totalPeriods: dashboardData.total_periods ?? 0,
            totalGroups: dashboardData.total_groups ?? 0,
            pendingFinalization,
            recentGroups,
        };
    }, [dashboardData]);

    const isLoading = isDashboardLoading;

    return { data, isLoading };
}
