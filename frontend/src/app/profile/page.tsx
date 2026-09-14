"use client";

import { ArrowLeft, GraduationCap, Mail, UserRound } from "lucide-react";
import { useRouter } from "next/navigation";
import DashboardLayout from "@/components/layout/DashboardLayout";
import { Avatar, AvatarFallback } from "@/components/ui/avatar";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { useAuth } from "@/context/AuthContext";

export default function ProfilePage() {
  const router = useRouter();
  const { user } = useAuth();
  const initials = user?.name
    ? user.name.split(" ").map((part) => part[0]).join("").slice(0, 2).toUpperCase()
    : "?";

  return (
    <DashboardLayout>
      <div className="mx-auto max-w-3xl space-y-6">
        <div className="flex items-center gap-4">
          <Button variant="ghost" size="icon" onClick={() => router.back()}>
            <ArrowLeft className="h-5 w-5" />
          </Button>
          <div>
            <h1 className="text-3xl font-bold tracking-tight">Profil SSO</h1>
            <p className="text-muted-foreground">
              Data akun dikelola terpusat melalui WebsiteTekkom.
            </p>
          </div>
        </div>

        <Card>
          <CardHeader>
            <div className="flex items-center gap-4">
              <Avatar className="h-16 w-16">
                <AvatarFallback className="text-lg font-semibold">{initials}</AvatarFallback>
              </Avatar>
              <div>
                <CardTitle>{user?.name ?? "Pengguna"}</CardTitle>
                <div className="mt-2 flex flex-wrap gap-2">
                  {user?.roles.map((role) => <Badge key={role}>{role}</Badge>)}
                </div>
              </div>
            </div>
          </CardHeader>
          <CardContent className="grid gap-4 sm:grid-cols-2">
            <div className="rounded-lg border p-4">
              <div className="mb-1 flex items-center gap-2 text-sm text-muted-foreground">
                <Mail className="h-4 w-4" /> Email
              </div>
              <p className="font-medium">{user?.email ?? "-"}</p>
            </div>
            <div className="rounded-lg border p-4">
              <div className="mb-1 flex items-center gap-2 text-sm text-muted-foreground">
                <GraduationCap className="h-4 w-4" /> Identitas akademik
              </div>
              <p className="font-medium">{user?.nim ?? user?.nip ?? "-"}</p>
            </div>
            <div className="rounded-lg border p-4 sm:col-span-2">
              <div className="mb-1 flex items-center gap-2 text-sm text-muted-foreground">
                <UserRound className="h-4 w-4" /> Sumber akun
              </div>
              <p className="font-medium">WebsiteTekkom SSO</p>
            </div>
          </CardContent>
        </Card>
      </div>
    </DashboardLayout>
  );
}
