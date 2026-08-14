"use client";

import { PageHeader } from "@/components/common/PageHeader";
import { UserTable, useUserColumns, useUsers } from "@/features/admin/users";

export function UsersFeature() {
  const {
    users,
    loading,
    pagination,
    filters,
    setSearch,
    setRole,
    setStatus,
    setSort,
    setPage,
    setPerPage,
  } = useUsers();

  const columns = useUserColumns({ readOnly: true });

  return (
    <div className="space-y-6">
      <PageHeader title="Direktori Pengguna SSO" />
      <p className="text-sm text-muted-foreground">
        Akun dan peran dikelola oleh WebsiteTekkom. CTMS hanya menampilkan identitas yang sudah tersinkron melalui SSO.
      </p>
      <UserTable
        users={users}
        loading={loading}
        pagination={pagination}
        search={filters.search}
        onSearchChange={setSearch}
        activeTab={filters.role}
        onTabChange={setRole}
        statusFilter={filters.status}
        onStatusChange={setStatus}
        sortKey={filters.sortKey}
        sortDir={filters.sortDir}
        onSort={setSort}
        onPageChange={setPage}
        onPerPageChange={setPerPage}
        columns={columns}
        onRowClick={() => undefined}
        selectedIds={new Set<number>()}
        onToggleSelectAll={() => undefined}
        onToggleSelectOne={() => undefined}
        selectable={false}
      />
    </div>
  );
}
