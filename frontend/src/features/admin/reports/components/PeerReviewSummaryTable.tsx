"use client";

import Link from "next/link";
import { useState, Fragment } from "react";
import { DataTable, DataTableColumn } from "@/components/ui/data-table";
import { TableRow, TableCell } from "@/components/ui/table";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import { Star, ChevronDown, ChevronRight, Loader2 } from "lucide-react";
import { usePeerReviewDetail } from "../hooks/use-reports";
import type {
  PeerReviewSummary,
  PeerReviewSummarySortKey,
  PaginationData,
  SortDir,
} from "../types";

const getScoreColor = (score: number | null): string => {
  if (score === null || score === undefined) return "text-muted-foreground";
  if (score >= 85) return "text-emerald-600";
  if (score >= 70) return "text-blue-600";
  if (score >= 60) return "text-amber-600";
  return "text-red-600";
};

const formatScore = (score: number | null): string => {
  if (score === null || score === undefined) return "-";
  return String(score);
};

function ExpandedDetail({ periodId, revieweeId }: { periodId: string; revieweeId: string }) {
  const { data: detail, isLoading } = usePeerReviewDetail(periodId, revieweeId);

  if (isLoading) {
    return (
      <div className="flex items-center gap-2 py-4 text-sm text-muted-foreground">
        <Loader2 className="h-4 w-4 animate-spin" /> Memuat detail penilaian...
      </div>
    );
  }

  if (!detail || detail.reviewers.length === 0) {
    return (
      <p className="py-4 text-sm text-muted-foreground">
        Belum ada penilaian untuk mahasiswa ini.
      </p>
    );
  }

  return (
    <div className="space-y-4 py-2">
      {detail.reviewers.map((reviewer) => (
        <div key={reviewer.reviewer_id} className="rounded-lg border bg-muted/30 p-3">
          <div className="flex flex-wrap items-center justify-between gap-2">
            <div>
              <p className="text-sm font-semibold">{reviewer.reviewer_name}</p>
              <p className="text-xs text-muted-foreground">
                {reviewer.reviewer_nim || "-"} &middot; {reviewer.reviews_count} indikator
              </p>
            </div>
            <div className="text-right">
              <p className={`text-sm font-bold ${getScoreColor(reviewer.avg_score)}`}>
                {formatScore(reviewer.avg_score)}
              </p>
              <p className="text-xs text-muted-foreground">
                raw {formatScore(reviewer.avg_raw_score)}
              </p>
            </div>
          </div>
          <div className="mt-2 overflow-x-auto">
            <table className="w-full text-sm">
              <thead>
                <tr className="text-left text-xs text-muted-foreground">
                  <th className="py-1 pr-3 font-medium">Indikator</th>
                  <th className="py-1 pr-3 font-medium text-right">Raw</th>
                  <th className="py-1 pr-3 font-medium text-right">Nilai</th>
                  <th className="py-1 font-medium">Komentar</th>
                </tr>
              </thead>
              <tbody>
                {reviewer.details.map((d, i) => (
                  <tr key={i} className="border-t">
                    <td className="py-1.5 pr-3">
                      <span className="font-medium">{d.indicator_name}</span>{" "}
                      <span className="text-xs text-muted-foreground">{d.indicator_code}</span>
                    </td>
                    <td className="py-1.5 pr-3 text-right font-semibold">{d.raw_score ?? "-"}</td>
                    <td className={`py-1.5 pr-3 text-right font-semibold ${getScoreColor(d.score)}`}>
                      {d.score ?? "-"}
                    </td>
                    <td className="py-1.5 text-muted-foreground">{d.comment || "-"}</td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </div>
      ))}
    </div>
  );
}

interface PeerReviewSummaryTableProps {
  summaries: PeerReviewSummary[];
  loading: boolean;
  pagination: PaginationData;
  search: string;
  onSearchChange: (value: string) => void;
  sortBy: PeerReviewSummarySortKey;
  sortOrder: SortDir;
  onSortChange: (field: PeerReviewSummarySortKey) => void;
  onPageChange: (page: number) => void;
  onPerPageChange: (perPage: number) => void;
  periodId: string;
}

const VALID_SORT_KEYS: PeerReviewSummarySortKey[] = [
  "reviewee",
  "group",
  "avg_score",
  "reviewers_count",
];

export function PeerReviewSummaryTable({
  summaries,
  loading,
  pagination,
  search,
  onSearchChange,
  sortBy,
  sortOrder,
  onSortChange,
  onPageChange,
  onPerPageChange,
  periodId,
}: PeerReviewSummaryTableProps) {
  const [expandedId, setExpandedId] = useState<number | null>(null);

  const toggleExpand = (id: number) => {
    setExpandedId((prev) => (prev === id ? null : id));
  };

  const columns: DataTableColumn<PeerReviewSummary>[] = [
    {
      key: "expand",
      header: "",
      width: "w-10",
      render: (row) => (
        <button
          onClick={(e) => {
            e.stopPropagation();
            toggleExpand(row.reviewee_id);
          }}
          className="rounded p-1 text-muted-foreground hover:bg-muted hover:text-foreground"
          aria-label={expandedId === row.reviewee_id ? "Tutup detail" : "Lihat penilai"}
        >
          {expandedId === row.reviewee_id ? (
            <ChevronDown className="h-4 w-4" />
          ) : (
            <ChevronRight className="h-4 w-4" />
          )}
        </button>
      ),
    },
    {
      key: "reviewee",
      header: "Mahasiswa Dinilai",
      sortable: true,
      render: (row) => (
        <div>
          <div className="text-sm font-medium">{row.reviewee_name}</div>
          <div className="text-xs text-muted-foreground">{row.reviewee_nim || "-"}</div>
        </div>
      ),
    },
    {
      key: "group",
      header: "Group",
      sortable: true,
      render: (row) => <span className="text-sm font-medium">{row.group_name}</span>,
    },
    {
      key: "status",
      header: "Status",
      render: (row) =>
        row.status === "DINILAI" ? (
          <Badge variant="default" className="text-[11px]">
            Dinilai
          </Badge>
        ) : (
          <Badge variant="secondary" className="text-[11px]">
            Belum dinilai
          </Badge>
        ),
    },
    {
      key: "reviewers_count",
      header: "Penilai",
      align: "right",
      sortable: true,
      render: (row) => (
        <span className="text-sm font-semibold">
          {row.reviewers_count} orang
          <span className="block text-xs font-normal text-muted-foreground">
            {row.reviews_count} penilaian
          </span>
        </span>
      ),
    },
    {
      key: "avg_score",
      header: "Rata-rata",
      align: "right",
      sortable: true,
      render: (row) => (
        <span>
          <span className={`text-base font-bold ${getScoreColor(row.avg_score)}`}>
            {formatScore(row.avg_score)}
          </span>
          <span className="block text-xs font-normal text-muted-foreground">
            raw {formatScore(row.avg_raw_score)}
          </span>
        </span>
      ),
    },
    {
      key: "action",
      header: "",
      align: "right",
      render: (row) => (
        <Link
          href={`/admin/reports/peer-reviews/${row.reviewee_id}?period_id=${periodId}`}
          onClick={(e) => e.stopPropagation()}
        >
          <Button variant="outline" size="sm">
            Detail
          </Button>
        </Link>
      ),
    },
  ];

  return (
    <DataTable<PeerReviewSummary>
      title="Peer Reviews per Mahasiswa"
      data={summaries}
      columns={columns}
      loading={loading}
      emptyMessage="No peer reviews found"
      emptySubMessage="No students match your search criteria."
      emptyIcon={<Star className="h-10 w-10" />}
      rowIdKey="reviewee_id"
      searchValue={search}
      onSearchChange={onSearchChange}
      searchPlaceholder="Search mahasiswa dinilai (nama/NIM)..."
      sortKey={sortBy}
      sortDir={sortOrder}
      onSort={(key) => {
        if ((VALID_SORT_KEYS as string[]).includes(key)) {
          onSortChange(key as PeerReviewSummarySortKey);
        }
      }}
      rowClickable
      onRowClick={(row) => toggleExpand(row.reviewee_id)}
      renderRow={(row) => {
        const isExpanded = expandedId === row.reviewee_id;
        return (
          <Fragment key={row.reviewee_id}>
            <TableRow
              className="group cursor-pointer"
              onClick={() => toggleExpand(row.reviewee_id)}
            >
              {columns.map((col) => (
                <TableCell
                  key={col.key}
                  className={
                    col.align === "right"
                      ? "py-3 text-right"
                      : col.align === "center"
                        ? "py-3 text-center"
                        : "py-3"
                  }
                >
                  {col.render ? col.render(row, 0) : null}
                </TableCell>
              ))}
            </TableRow>
            {isExpanded && (
              <TableRow className="bg-muted/20 hover:bg-muted/20">
                <TableCell colSpan={columns.length} className="px-6">
                  <ExpandedDetail periodId={periodId} revieweeId={String(row.reviewee_id)} />
                  <div className="pb-2">
                    <Link
                      href={`/admin/reports/peer-reviews/${row.reviewee_id}?period_id=${periodId}`}
                    >
                      <Button variant="link" size="sm" className="px-0">
                        Buka halaman detail &rarr;
                      </Button>
                    </Link>
                  </div>
                </TableCell>
              </TableRow>
            )}
          </Fragment>
        );
      }}
      pagination={pagination}
      onPageChange={onPageChange}
      onPerPageChange={onPerPageChange}
    />
  );
}
