import sys

with open("Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php", "r", encoding="utf-8") as f:
    text = f.read()

# I used pendaftaran-koor.update-status with PUT
# But the routes are named dosen.pendaftaran-koor.approve and dosen.pendaftaran-koor.reject and they use POST

old_approve = """<form action="{{ route('pendaftaran-koor.update-status', $p->id) }}" method="POST">
                                                    @csrf @method('PUT')
                                                    <input type="hidden" name="status_dosen" value="disetujui">
                                                    <button type="submit" class="mp-btn sm" style="background:#F3F4F6;color:#0B266E;font-weight:600;display:flex;gap:4px;border:none;">
                                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg> Setujui
                                                    </button>
                                                </form>"""

new_approve = """<form action="{{ route('dosen.pendaftaran-koor.approve', $p->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="mp-btn sm" style="background:#F3F4F6;color:#0B266E;font-weight:600;display:flex;gap:4px;border:none;">
                                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg> Setujui
                                                    </button>
                                                </form>"""

old_reject = """<form action="{{ route('pendaftaran-koor.update-status', $p->id) }}" method="POST">
                                                    @csrf @method('PUT')
                                                    <input type="hidden" name="status_dosen" value="ditolak">
                                                    <button type="submit" class="mp-btn sm" style="background:#DF1C41;color:white;font-weight:600;display:flex;gap:4px;border:none;">
                                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg> Tolak
                                                    </button>
                                                </form>"""

new_reject = """<form action="{{ route('dosen.pendaftaran-koor.reject', $p->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="mp-btn sm" style="background:#DF1C41;color:white;font-weight:600;display:flex;gap:4px;border:none;">
                                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg> Tolak
                                                    </button>
                                                </form>"""

text = text.replace(old_approve, new_approve)
text = text.replace(old_reject, new_reject)

with open("Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php", "w", encoding="utf-8") as f:
    f.write(text)

print("done")