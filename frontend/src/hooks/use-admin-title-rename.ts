'use client';

import { useCallback } from 'react';
import { useMutation, useQueryClient } from '@tanstack/react-query';
import api from '@/lib/api';
import { toast } from 'sonner';

interface RenameTitlePayload {
  titleId: number;
  title: string;
}

interface UseAdminTitleRenameReturn {
  renameTitle: (titleId: number, title: string) => Promise<boolean>;
  renamingTitle: boolean;
}

/**
 * Shared mutation for admins to rename a capstone title's text.
 * Hits PUT /admin/titles/{title} which only updates text columns —
 * group status is never touched by this endpoint.
 */
export function useAdminTitleRename(): UseAdminTitleRenameReturn {
  const queryClient = useQueryClient();

  const renameMutation = useMutation({
    mutationFn: async ({ titleId, title }: RenameTitlePayload) => {
      const response = await api.put(`/admin/titles/${titleId}`, { title });
      return response.data;
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['admin', 'manual-grouping'] });
      queryClient.invalidateQueries({ queryKey: ['admin', 'finalization-dashboard'] });
      toast.success('Judul berhasil diperbarui');
    },
    onError: (error: unknown) => {
      toast.error(api.getApiErrorMessage(error, 'Gagal memperbarui judul'));
    },
  });

  const renameTitle = useCallback(
    async (titleId: number, title: string): Promise<boolean> => {
      try {
        await renameMutation.mutateAsync({ titleId, title });
        return true;
      } catch {
        return false;
      }
    },
    [renameMutation]
  );

  return { renameTitle, renamingTitle: renameMutation.isPending };
}
