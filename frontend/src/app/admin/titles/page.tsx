"use client";

import { useState, useEffect, useCallback, useMemo } from "react";
import api from "@/lib/api";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { Input } from "@/components/ui/input";
import { BookOpen, Users, Search } from "lucide-react";
import { Loading } from "@/components/ui/loading";
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select";

interface Period {
  id: number;
  name: string;
  is_active: boolean;
}

interface Group {
  id: number;
  code?: string;
  status: string;
  members: { student: { name: string } }[];
  title: { title: string } | null;
  supervisors: { lecturer: { name: string } }[];
}

export default function AdminTitlesPage() {
  const [periods] = useState<Period[]>([]);
  const [selectedPeriod, setSelectedPeriod] = useState<string>("");
  const [searchQuery, setSearchQuery] = useState("");
  const [groups, setGroups] = useState<Group[]>([]);
  const [loading, setLoading] = useState(true);

  const fetchData = useCallback(
    async (periodId?: string) => {
      setLoading(true);
      try {
        const currentPeriodId = periodId || selectedPeriod;

        if (!currentPeriodId) {
          setLoading(false);
          return;
        }

        const res = await api.get(`/admin/groups?period_id=${currentPeriodId}`);
        setGroups(res.data || []);
      } catch (err) {
        console.error("Failed to fetch groups", err);
      } finally {
        setLoading(false);
      }
    },
    [selectedPeriod]
  );

  useEffect(() => {
    fetchData();
  }, [fetchData, selectedPeriod]);

  const handlePeriodChange = (val: string) => {
    setSelectedPeriod(val);
    fetchData(val);
  };

  const filteredGroups = useMemo(() => {
    if (!searchQuery) return groups;
    const q = searchQuery.toLowerCase();
    return groups.filter(
      (g) =>
        g.title?.title.toLowerCase().includes(q) ||
        g.members.some((m) => m.student.name.toLowerCase().includes(q)) ||
        g.supervisors?.some((s) => s.lecturer.name.toLowerCase().includes(q)) ||
        g.id.toString().includes(q)
    );
  }, [groups, searchQuery]);

  if (loading && !groups.length) {
    return <Loading variant="section" />;
  }

  // State machine status map for progress calculation
  const statusProgress: Record<string, number> = {
    FORMING: 0,
    FORMING_SOLO: 0,
    READY_FOR_BIDDING: 10,
    KELOMPOK_FINAL: 20,
    PDC1_ACTIVE: 30,
    READY_FOR_SEMPRO: 40,
    SEMPRO_DONE: 50,
    PDC2_ACTIVE: 60,
    TA_DRAFT: 65,
    PDC2_READY_FOR_EXPO: 70,
    EXPO_REGISTERED: 80,
    EXPO_DONE: 90,
    READY_FOR_TA_INDIVIDUAL: 100,
    CLOSED: 100,
  };

  return (
    <div className="space-y-6">
      <div className="flex flex-col items-start justify-between gap-4 md:flex-row md:items-center">
        <div>
          <h1 className="text-3xl font-bold tracking-tight">
            Titles & Progress
          </h1>
          <p className="text-muted-foreground">
            Monitor the progress of all student groups.
          </p>
        </div>
        <Select
          value={selectedPeriod}
          onValueChange={handlePeriodChange}
          disabled={loading}
        >
          <SelectTrigger className="w-[180px]">
            <SelectValue placeholder="Select period" />
          </SelectTrigger>
          <SelectContent>
            {periods.map((p) => (
              <SelectItem key={p.id} value={p.id.toString()}>
                {p.name}
              </SelectItem>
            ))}
          </SelectContent>
        </Select>
      </div>

      <div className="relative max-w-sm">
        <Search className="text-muted-foreground absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2" />
        <Input
          placeholder="Search by title, student, or supervisor..."
          className="pl-9"
          value={searchQuery}
          onChange={(e: React.ChangeEvent<HTMLInputElement>) =>
            setSearchQuery(e.target.value)
          }
        />
      </div>

      {filteredGroups.length === 0 ? (
        <div className="rounded-lg border border-dashed py-12 text-center">
          <BookOpen className="text-muted-foreground mx-auto mb-4 h-12 w-12 opacity-50" />
          <h2 className="mb-2 text-xl font-bold">
            {searchQuery ? "No matching groups found" : "No Groups"}
          </h2>
          <p className="text-muted-foreground">
            {searchQuery
              ? "Try adjusting your search query."
              : "There are no groups in this period yet."}
          </p>
        </div>
      ) : (
        <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
          {filteredGroups.map((group) => {
            const progress = statusProgress[group.status] || 0;
            return (
              <Card key={group.id}>
                <CardHeader className="pb-3">
                  <div className="flex items-start justify-between">
                    <CardTitle className="text-base font-bold">
                      {group.code || `Group ${group.id}`}
                    </CardTitle>
                    <Badge variant={progress === 100 ? "default" : "secondary"}>
                      {group.status}
                    </Badge>
                  </div>
                </CardHeader>
                <CardContent className="space-y-3">
                  <div className="text-sm">
                    <strong className="text-muted-foreground mb-1 block">
                      Title:
                    </strong>
                    <span className="font-medium">
                      {group.title?.title || "No title assigned yet"}
                    </span>
                  </div>
                  <div className="text-sm">
                    <div className="text-muted-foreground mb-1 flex items-center gap-1">
                      <Users className="h-3 w-3" /> <strong>Members:</strong>
                    </div>
                    {group.members.map((m) => m.student.name).join(", ")}
                  </div>
                  <div className="text-sm">
                    <strong className="text-muted-foreground mb-1 block">
                      Supervisors:
                    </strong>
                    {group.supervisors && group.supervisors.length > 0
                      ? group.supervisors.map((s) => s.lecturer.name).join(", ")
                      : "None"}
                  </div>

                  <div className="pt-2">
                    <div className="text-muted-foreground mb-1 flex justify-between text-xs">
                      <span>Progress</span>
                      <span>{progress}%</span>
                    </div>
                    <div className="bg-muted h-2 w-full rounded-full">
                      <div
                        className={`h-2 rounded-full transition-all ${progress === 100 ? "bg-green-500" : "bg-primary"}`}
                        style={{ width: `${progress}%` }}
                      />
                    </div>
                  </div>
                </CardContent>
              </Card>
            );
          })}
        </div>
      )}
    </div>
  );
}
