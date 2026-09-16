"use client";

import { useState, useEffect, useCallback } from "react";
import { useRouter } from "next/navigation";
import api from "@/lib/api";
import { Card, CardContent } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import {
  Loader2,
  Bell,
  CheckCheck,
  Mail,
  MailOpen,
  XCircle,
} from "lucide-react";
import { toast } from "sonner";
import { formatDistanceToNow } from "date-fns";
import { id } from "date-fns/locale";

interface Notification {
  id: number;
  type: string;
  title: string;
  message: string;
  is_read: boolean;
  related_type: string | null;
  related_id: number | null;
  created_at: string;
  invitation_status?: string;
  action_url?: string | null;
}

interface PaginatedNotifications {
  data: Notification[];
  current_page: number;
  last_page: number;
  total: number;
}

export default function NotificationsPage() {
  const router = useRouter();
  const [notifications, setNotifications] = useState<Notification[]>([]);
  const [loading, setLoading] = useState(true);
  const [page, setPage] = useState(1);
  const [lastPage, setLastPage] = useState(1);
  const [total, setTotal] = useState(0);
  const [invitationActions, setInvitationActions] = useState<
    Record<number, string>
  >({});

  const fetchNotifications = useCallback(async (p: number) => {
    try {
      const res = await api.get(`/notifications?page=${p}&per_page=10`);
      const data: PaginatedNotifications = res.data;
      setNotifications(data.data || []);
      setPage(data.current_page);
      setLastPage(data.last_page);
      setTotal(data.total);
    } catch (err) {
      console.error("Failed to fetch notifications", err);
    } finally {
      setLoading(false);
    }
  }, []);

  useEffect(() => {
    fetchNotifications(1);
  }, [fetchNotifications]);

  const markAsRead = async (id: number) => {
    try {
      await api.put(`/notifications/${id}/read`);
      setNotifications((prev) =>
        prev.map((n) => (n.id === id ? { ...n, is_read: true } : n))
      );
    } catch (err) {
      console.error("Failed to mark as read", err);
    }
  };

  const handleInvitation = async (
    invitationId: number,
    action: "accept" | "reject",
    notificationId: number
  ) => {
    try {
      const response = await api.post(
        `/mahasiswa/group-invitations/${invitationId}/${action}`
      );

      // Show auto-registration notification if applicable
      if (action === "accept" && response.data?.auto_registered) {
        toast.success(
          response.data?.message ||
            "You have been automatically registered and added to the group."
        );
      } else {
        toast.success(`Invitation ${action}ed successfully`);
      }

      setInvitationActions((prev) => ({ ...prev, [notificationId]: action }));
      markAsRead(notificationId);
    } catch (error) {
      if (api.isAxiosError(error)) {
        toast.error(
          error.response?.data?.message || `Failed to ${action} invitation`
        );
      } else {
        toast.error(`Failed to ${action} invitation`);
      }
    }
  };

  const handleNotificationClick = (n: Notification) => {
    if (n.action_url) {
      router.push(n.action_url);
    }
    if (!n.is_read) {
      markAsRead(n.id);
    }
  };

  const markAllAsRead = async () => {
    try {
      await api.put("/notifications/read-all");
      setNotifications((prev) => prev.map((n) => ({ ...n, is_read: true })));
      toast.success("All notifications marked as read");
    } catch {
      toast.error("Failed to mark all as read");
    }
  };

  const typeIcon = (type: string) => {
    const styles: Record<string, string> = {
      PROPOSAL_SUBMITTED: "bg-blue-50 text-blue-600",
      PROPOSAL_APPROVED: "bg-green-50 text-green-600",
      PROPOSAL_REJECTED: "bg-red-50 text-red-600",
      PROPOSAL_RESUBMITTED: "bg-amber-50 text-amber-600",
      EXPO_REGISTRATION: "bg-primary-50 text-primary-500",
      SCHEDULE_APPROVED: "bg-emerald-50 text-emerald-600",
      SCHEDULE_REJECTED: "bg-rose-50 text-rose-600",
      GROUP_INVITATION: "bg-indigo-50 text-indigo-600",
    };
    return styles[type] || "bg-gray-50 text-gray-500";
  };

  const unreadCount = notifications.filter((n) => !n.is_read).length;

  if (loading) {
    return (
      <div className="flex h-64 items-center justify-center">
        <Loader2 className="h-8 w-8 animate-spin" />
      </div>
    );
  }

  return (
    <div className="space-y-6">
      <div className="flex items-start justify-between">
        <div>
          <h1 className="text-3xl font-bold tracking-tight">Notifications</h1>
          <p className="text-muted-foreground">
            {total} notifications · {unreadCount} unread
          </p>
        </div>
        {unreadCount > 0 && (
          <Button variant="outline" size="sm" onClick={markAllAsRead}>
            <CheckCheck className="mr-2 h-4 w-4" /> Mark All Read
          </Button>
        )}
      </div>

      {notifications.length === 0 ? (
        <div className="rounded-lg border border-dashed py-12 text-center">
          <Bell className="text-muted-foreground mx-auto mb-4 h-12 w-12 opacity-50" />
          <h2 className="mb-2 text-xl font-bold">No Notifications</h2>
          <p className="text-muted-foreground">You&#39;re all caught up!</p>
        </div>
      ) : (
        <div className="space-y-2">
          {notifications.map((n) => (
            <Card
              key={n.id}
              className={`cursor-pointer transition-all duration-200 ${!n.is_read ? "border-primary/20 bg-primary/5 shadow-sm" : "hover:border-primary/20 hover:bg-muted/30 shadow-none"}`}
              onClick={() => handleNotificationClick(n)}
            >
              <CardContent className="flex items-start gap-4 p-5">
                <div
                  className={`flex h-10 w-10 shrink-0 items-center justify-center rounded-full ${typeIcon(n.type)}`}
                >
                  {n.is_read ? (
                    <MailOpen className="h-4 w-4" />
                  ) : (
                    <Mail className="h-4 w-4" />
                  )}
                </div>
                <div className="min-w-0 flex-1">
                  <div className="flex items-center gap-2">
                    <span
                      className={`text-sm font-medium ${!n.is_read ? "text-primary" : ""}`}
                    >
                      {n.title}
                    </span>
                    {!n.is_read && (
                      <Badge variant="default" className="px-1.5 py-0 text-xs">
                        New
                      </Badge>
                    )}
                  </div>
                  <p className="text-muted-foreground mt-0.5 line-clamp-2 text-sm">
                    {n.message}
                  </p>

                  {n.type === "GROUP_INVITATION" && n.related_id && (
                    <div className="mt-4 flex gap-2">
                      {n.invitation_status === "PENDING" &&
                      !invitationActions[n.id] ? (
                        <>
                          <Button
                            size="sm"
                            onClick={(e) => {
                              e.stopPropagation();
                              handleInvitation(n.related_id!, "accept", n.id);
                            }}
                          >
                            Accept
                          </Button>
                          <Button
                            size="sm"
                            variant="outline"
                            onClick={(e) => {
                              e.stopPropagation();
                              handleInvitation(n.related_id!, "reject", n.id);
                            }}
                          >
                            Reject
                          </Button>
                        </>
                      ) : (
                        <span className="text-muted-foreground flex items-center gap-2 text-sm font-medium">
                          {invitationActions[n.id] === "accept" ||
                          n.invitation_status === "ACCEPTED" ? (
                            <>
                              <CheckCheck className="h-4 w-4 text-green-500" />{" "}
                              You accepted this invitation
                            </>
                          ) : invitationActions[n.id] === "reject" ||
                            n.invitation_status === "REJECTED" ? (
                            <>
                              <XCircle className="h-4 w-4 text-red-500" /> You
                              rejected this invitation
                            </>
                          ) : null}
                        </span>
                      )}
                    </div>
                  )}
                  <div className="text-muted-foreground mt-2 text-xs whitespace-nowrap">
                    {formatDistanceToNow(new Date(n.created_at), {
                      addSuffix: true,
                      locale: id,
                    })}
                  </div>
                </div>
              </CardContent>
            </Card>
          ))}
        </div>
      )}

      {/* Pagination */}
      {lastPage > 1 && (
        <div className="flex justify-center gap-2">
          <Button
            variant="outline"
            size="sm"
            disabled={page <= 1}
            onClick={() => fetchNotifications(page - 1)}
          >
            Previous
          </Button>
          <span className="text-muted-foreground flex items-center px-3 text-sm">
            Page {page} of {lastPage}
          </span>
          <Button
            variant="outline"
            size="sm"
            disabled={page >= lastPage}
            onClick={() => fetchNotifications(page + 1)}
          >
            Next
          </Button>
        </div>
      )}
    </div>
  );
}
