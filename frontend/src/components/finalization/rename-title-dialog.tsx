'use client';

import { useState } from 'react';
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

interface RenameTitleDialogProps {
  open: boolean;
  onOpenChange: (open: boolean) => void;
  currentTitle: string;
  loading: boolean;
  onSubmit: (title: string) => Promise<void>;
}

const MAX_TITLE_LENGTH = 255;

export function RenameTitleDialog({
  open,
  onOpenChange,
  currentTitle,
  loading,
  onSubmit,
}: RenameTitleDialogProps) {
  // NOTE: callers pass a `key` combining the title id and open state so
  // the input resets to `currentTitle` on every open without an effect.
  const [value, setValue] = useState(currentTitle);

  const trimmed = value.trim();
  const isValid = trimmed.length > 0 && trimmed.length <= MAX_TITLE_LENGTH;
  const isUnchanged = trimmed === currentTitle.trim();

  const handleSubmit = async () => {
    if (!isValid || isUnchanged || loading) return;
    await onSubmit(trimmed);
  };

  return (
    <Dialog open={open} onOpenChange={onOpenChange}>
      <DialogContent className="sm:max-w-[500px]">
        <DialogHeader>
          <DialogTitle>Ubah Judul</DialogTitle>
          <DialogDescription>
            Perbarui teks judul. Status grup yang menggunakan judul ini tidak
            akan berubah.
          </DialogDescription>
        </DialogHeader>
        <div className="space-y-2 py-4">
          <Label htmlFor="rename-title-input">Judul</Label>
          <Input
            id="rename-title-input"
            value={value}
            maxLength={MAX_TITLE_LENGTH}
            onChange={(e) => setValue(e.target.value)}
            onKeyDown={(e) => {
              if (e.key === 'Enter') {
                e.preventDefault();
                void handleSubmit();
              }
            }}
            placeholder="Masukkan judul baru..."
            disabled={loading}
          />
        </div>
        <DialogFooter>
          <Button
            variant="outline"
            onClick={() => onOpenChange(false)}
            disabled={loading}
          >
            Batal
          </Button>
          <Button
            onClick={() => void handleSubmit()}
            disabled={!isValid || isUnchanged || loading}
          >
            {loading ? 'Menyimpan...' : 'Simpan'}
          </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>
  );
}
