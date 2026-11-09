'use client';

import { useParams, useSearchParams } from 'next/navigation';
import Link from 'next/link';
import { usePeerReviewDetail } from '@/features/admin/reports/hooks/use-reports';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { ArrowLeft, Loader2 } from 'lucide-react';

const getScoreColor = (score: number | null): string => {
  if (score === null || score === undefined) return 'text-muted-foreground';
  if (score >= 85) return 'text-emerald-600';
  if (score >= 70) return 'text-blue-600';
  if (score >= 60) return 'text-amber-600';
  return 'text-red-600';
};

export default function PeerReviewDetailPage() {
  const params = useParams();
  const searchParams = useSearchParams();
  const studentId = Array.isArray(params.studentId) ? params.studentId[0] : (params.studentId as string);
  const periodId = searchParams.get('period_id') || '';

  const { data: detail, isLoading } = usePeerReviewDetail(periodId, studentId);

  const backHref = periodId ? `/admin/reports/peer-reviews?period_id=${periodId}` : '/admin/reports/peer-reviews';

  if (!periodId) {
    return (
      <div className="space-y-6">
        <Link href="/admin/reports">
          <Button variant="ghost" className="pl-0">
            <ArrowLeft className="mr-2 h-4 w-4" />
            Back to Reports
          </Button>
        </Link>
        <Card>
          <CardContent className="py-12 text-center">
            <h3 className="text-lg font-semibold mb-2">No Period Selected</h3>
            <p className="text-muted-foreground">Please open this page from the Peer Reviews report.</p>
          </CardContent>
        </Card>
      </div>
    );
  }

  return (
    <div className="space-y-6">
      <div className="flex items-center gap-4">
        <Link href={backHref}>
          <Button variant="ghost" size="icon">
            <ArrowLeft className="h-4 w-4" />
          </Button>
        </Link>
        <div>
          <h1 className="text-3xl font-bold tracking-tight">Detail Peer Review</h1>
          <p className="text-muted-foreground">Dinilai oleh siapa saja dan nilainya berapa.</p>
        </div>
      </div>

      {isLoading ? (
        <Card>
          <CardContent className="py-12 flex justify-center">
            <Loader2 className="h-8 w-8 animate-spin text-muted-foreground" />
          </CardContent>
        </Card>
      ) : !detail ? (
        <Card>
          <CardContent className="py-12 text-center">
            <h3 className="text-lg font-semibold mb-2">Data tidak ditemukan</h3>
            <p className="text-muted-foreground">Mahasiswa tidak ditemukan pada periode ini.</p>
          </CardContent>
        </Card>
      ) : (
        <>
          <Card>
            <CardHeader>
              <CardTitle className="text-base">Mahasiswa Dinilai</CardTitle>
            </CardHeader>
            <CardContent>
              <div className="flex flex-wrap items-center justify-between gap-4">
                <div>
                  <p className="text-xl font-semibold">{detail.reviewee.name}</p>
                  <p className="text-sm text-muted-foreground">
                    {detail.reviewee.nim || '-'} &middot; {detail.reviewee.group_name}
                  </p>
                </div>
                <div className="flex items-center gap-3">
                  {detail.overall.status === 'DINILAI' ? (
                    <Badge variant="default">Dinilai</Badge>
                  ) : (
                    <Badge variant="secondary">Belum dinilai</Badge>
                  )}
                  <div className="text-right">
                    <p className={`text-2xl font-bold ${getScoreColor(detail.overall.avg_score)}`}>
                      {detail.overall.avg_score ?? '-'}
                    </p>
                    <p className="text-xs text-muted-foreground">
                      {detail.overall.reviewers_count} penilai &middot; {detail.overall.reviews_count} penilaian
                    </p>
                  </div>
                </div>
              </div>
            </CardContent>
          </Card>

          {detail.reviewers.length === 0 ? (
            <Card>
              <CardContent className="py-12 text-center">
                <p className="text-muted-foreground">Belum ada penilaian untuk mahasiswa ini.</p>
              </CardContent>
            </Card>
          ) : (
            detail.reviewers.map((reviewer) => (
              <Card key={reviewer.reviewer_id}>
                <CardHeader>
                  <CardTitle className="text-base flex flex-wrap items-center justify-between gap-2">
                    <span>
                      {reviewer.reviewer_name}
                      <span className="ml-2 text-sm font-normal text-muted-foreground">
                        {reviewer.reviewer_nim || '-'}
                      </span>
                    </span>
                    <span className={`text-lg font-bold ${getScoreColor(reviewer.avg_score)}`}>
                      {reviewer.avg_score ?? '-'}
                      <span className="ml-2 text-xs font-normal text-muted-foreground">
                        raw {reviewer.avg_raw_score ?? '-'}
                      </span>
                    </span>
                  </CardTitle>
                </CardHeader>
                <CardContent>
                  <div className="overflow-x-auto">
                    <table className="w-full text-sm">
                      <thead>
                        <tr className="border-b text-left text-xs text-muted-foreground">
                          <th className="py-2 pr-4 font-medium">Indikator</th>
                          <th className="py-2 pr-4 font-medium">Bobot</th>
                          <th className="py-2 pr-4 font-medium text-right">Raw (1-4)</th>
                          <th className="py-2 pr-4 font-medium text-right">Nilai (0-100)</th>
                          <th className="py-2 font-medium">Komentar</th>
                        </tr>
                      </thead>
                      <tbody>
                        {reviewer.details.map((d, i) => (
                          <tr key={i} className="border-b last:border-0">
                            <td className="py-2 pr-4">
                              <span className="font-medium">{d.indicator_name}</span>{' '}
                              <span className="text-xs text-muted-foreground">{d.indicator_code}</span>
                            </td>
                            <td className="py-2 pr-4 text-muted-foreground">{d.weight ?? '-'}</td>
                            <td className="py-2 pr-4 text-right font-semibold">{d.raw_score ?? '-'}</td>
                            <td className={`py-2 pr-4 text-right font-bold ${getScoreColor(d.score)}`}>
                              {d.score ?? '-'}
                            </td>
                            <td className="py-2 text-muted-foreground">{d.comment || '-'}</td>
                          </tr>
                        ))}
                      </tbody>
                    </table>
                  </div>
                </CardContent>
              </Card>
            ))
          )}
        </>
      )}
    </div>
  );
}
