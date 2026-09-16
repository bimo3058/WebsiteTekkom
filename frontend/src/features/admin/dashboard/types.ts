export interface AdminDashboardGroupItem {
    id: number;
    code?: string;
    status?: string;
}

export interface AdminDashboardResponse {
    total_users?: number;
    total_periods?: number;
    total_groups?: number;
    pending_finalization?: number;
    recent_groups?: AdminDashboardGroupItem[];
}

export interface AdminPeriodsResponse {
    data?: unknown[];
}

export interface AdminGroupsResponse {
    data?: AdminDashboardGroupItem[];
}

export interface AdminDashboardData {
    totalUsers: number;
    totalPeriods: number;
    totalGroups: number;
    pendingFinalization: number;
    recentGroups: Array<{
        id: number;
        label: string;
        subtitle: string;
        status: {
            label: string;
            variant: 'default' | 'secondary' | 'destructive' | 'outline';
        };
        href: string;
    }>;
}
