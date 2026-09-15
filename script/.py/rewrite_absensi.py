
import re

with open("Modules/EOffice/resources/views/manajemen-praktikum/asprak/absensi.blade.php", "r", encoding="utf-8") as f:
    html = f.read()

# Replace the payload.nilai assignment to apply to ALL rows, not restricted to hadir/terlambat!
html = html.replace(
"""                                    if (row.status === 'hadir' || row.status === 'terlambat') {
                                        payload.nilai[row.dp_id] = {
                                            tugas_pendahuluan: row.tugas_pendahuluan || null,
                                            praktikum: row.praktikum || null,
                                            laporan: row.laporan || null,
                                            responsi: row.responsi || null
                                        };
                                    }""",
"""                                    payload.nilai[row.dp_id] = {
                                        tugas_pendahuluan: row.tugas_pendahuluan || null,
                                        praktikum: row.praktikum || null,
                                        laporan: row.laporan || null,
                                        responsi: row.responsi || null
                                    };"""
)

with open("Modules/EOffice/resources/views/manajemen-praktikum/asprak/absensi.blade.php", "w", encoding="utf-8") as f:
    f.write(html)

print("Saved fixed grade logic")

