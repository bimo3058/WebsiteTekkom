class DosenDashboardComponent {
    // Merender seluruh grafik dashboard dosen.
    static renderCharts() {
        this.renderDonutChart();
        this.renderCplBarChart();
        this.renderMkBarChart();
    }

    // Merender donut chart untuk ringkasan status soal.
    static renderDonutChart(elementId = "donutChart", data = null) {
        const svg = document.getElementById(elementId);
        if (!svg) return;

        // DataDummy.
        const chartData = data || [
            { value: 75, color: "#22C55E" }, // Disetujui
            { value: 28, color: "#F59E0B" }, // Perlu review
            { value: 15, color: "#3B82F6" }, // Menunggu
            { value: 10, color: "#EF4444" }, // Ditolak
        ];

        const cx = 40,
            cy = 40,
            r = 30,
            stroke = 12;
        const circ = 2 * Math.PI * r;
        const total = chartData.reduce((sum, seg) => sum + seg.value, 0);

        let offset = 0;
        if (total === 0) {
            const el = document.createElementNS(
                "http://www.w3.org/2000/svg",
                "circle",
            );
            el.setAttribute("cx", cx);
            el.setAttribute("cy", cy);
            el.setAttribute("r", r);
            el.setAttribute("fill", "none");
            el.setAttribute("stroke", "#E2E8F0");
            el.setAttribute("stroke-width", stroke);
            svg.appendChild(el);
        } else {
            chartData.forEach((seg) => {
                const pct = seg.value / total;
                const dash = pct * circ;
                const gap = circ - dash;
                const rot = -90 + (offset / total) * 360;

                const el = document.createElementNS(
                    "http://www.w3.org/2000/svg",
                    "circle",
                );
                el.setAttribute("cx", cx);
                el.setAttribute("cy", cy);
                el.setAttribute("r", r);
                el.setAttribute("fill", "none");
                el.setAttribute("stroke", seg.color);
                el.setAttribute("stroke-width", stroke);
                el.setAttribute(
                    "stroke-dasharray",
                    `${dash.toFixed(2)} ${gap.toFixed(2)}`,
                );
                el.setAttribute("stroke-dashoffset", "0");
                el.setAttribute("transform", `rotate(${rot} ${cx} ${cy})`);
                svg.appendChild(el);

                offset += seg.value;
            });
        }

        // Lingkaran putih di tengah diagram.
        const inner = document.createElementNS(
            "http://www.w3.org/2000/svg",
            "circle",
        );
        inner.setAttribute("cx", cx);
        inner.setAttribute("cy", cy);
        inner.setAttribute("r", r - stroke / 2);
        inner.setAttribute("fill", "white");
        svg.appendChild(inner);
    }

    // Merender grafik batang untuk distribusi CPL.
    static renderCplBarChart(elementId = "cplChart", data = null) {
        const wrap = document.getElementById(elementId);
        if (!wrap) return;

        // DataDummy.
        const chartData = data || {
            "CPL 01": 45,
            "CPL 02": 30,
            "CPL 03": 60,
            "CPL 04": 20,
            "CPL 05": 38,
        };

        const isEmpty = !chartData || 
                        (Array.isArray(chartData) && (chartData.length === 0 || chartData.every(d => d.count === 0))) || 
                        (typeof chartData === 'object' && (Object.keys(chartData).length === 0 || Object.values(chartData).every(v => v === 0)));

        if (isEmpty) {
            wrap.className = "h-64 flex flex-col items-center justify-center text-center p-4 w-full";
            wrap.style.cssText = "";
            wrap.innerHTML = `
                <div class="flex flex-col items-center justify-center">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-50 border border-slate-100 text-slate-400 mb-3 shadow-sm">
                        <i class="fas fa-chart-bar text-lg"></i>
                    </div>
                    <p class="text-sm font-semibold text-slate-700">Belum Ada Pemetaan CPL</p>
                    <p class="text-xs text-slate-500 max-w-[280px] mt-1 leading-relaxed">Belum ada pemetaan CPL yang dilakukan. Silakan membuat soal terlebih dahulu.</p>
                </div>
            `;
            return;
        }

        const max = Math.max(...Object.values(chartData)) || 1;
        const BAR_H = 90;
        wrap.className = "h-64 flex items-end gap-4";
        wrap.style.cssText =
            "display:flex;align-items:flex-end;gap:6px;width:100%;padding-top:8px";
        let html = "";

        Object.entries(chartData).forEach(([label, val]) => {
            const h = Math.max(8, Math.round((val / max) * BAR_H));
            html += `<div style="display:flex;flex-direction:column;align-items:center;flex:1;gap:3px">
                    <span style="font-size:10px;font-weight:600;color:#475569">${val}</span>
                    <div style="width:100%;height:${h}px;background:#3b82f6;border-radius:4px 4px 0 0"></div>
                    <span style="font-size:9px;color:#94a3b8;white-space:nowrap">${label}</span>
                </div>`;
        });

        wrap.innerHTML = html;
    }

    // Merender grafik batang untuk jumlah soal per mata kuliah.
    static renderMkBarChart(elementId = "mkChart", data = null) {
        const wrap = document.getElementById(elementId);
        if (!wrap) return;

        // DataDummy.
        const chartData = data || [
            { mk: "CS-201", count: 54, color: "#22C55E" },
            { mk: "CS-304", count: 32, color: "#22C55E" },
            { mk: "CS-401", count: 0, color: "#CBD5E1" },
        ];

        const isEmpty = !chartData || 
                        chartData.length === 0 || 
                        chartData.every(d => d.count === 0);

        if (isEmpty) {
            wrap.className = "h-64 flex flex-col items-center justify-center text-center p-4 w-full";
            wrap.style.cssText = "";
            wrap.innerHTML = `
                <div class="flex flex-col items-center justify-center">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-50 border border-slate-100 text-slate-400 mb-3 shadow-sm">
                        <i class="fas fa-book text-lg"></i>
                    </div>
                    <p class="text-sm font-semibold text-slate-700">Belum Ada Soal per MK</p>
                    <p class="text-xs text-slate-500 max-w-[280px] mt-1 leading-relaxed">Belum ada soal yang dibuat untuk mata kuliah yang diampu. Silakan membuat soal terlebih dahulu.</p>
                </div>
            `;
            return;
        }

        const max = Math.max(...chartData.map((d) => d.count)) || 1;
        const BAR_H = 90; // Tinggi maksimum batang dalam piksel.
        wrap.className = "h-64 flex items-end gap-4";
        wrap.style.cssText =
            "display:flex;align-items:flex-end;gap:10px;width:100%;padding-top:8px";
        let html = "";

        chartData.forEach((d) => {
            const h = Math.max(8, Math.round((d.count / max) * BAR_H));
            const valColor = d.count > 0 ? "#22C55E" : "#9CA3AF";
            html += `<div style="display:flex;flex-direction:column;align-items:center;flex:1;gap:3px">
                    <span style="font-size:10px;font-weight:600;color:${valColor}">${d.count || ""}</span>
                    <div style="width:100%;height:${h}px;background:${d.color};border-radius:4px 4px 0 0"></div>
                    <span style="font-size:10px;color:#94a3b8;white-space:nowrap">${d.mk}</span>
                </div>`;
        });

        wrap.innerHTML = html;
    }

    // Memperbarui diagram donat dengan data baru.
    static updateDonutChart(elementId, newData) {
        const svg = document.getElementById(elementId);
        if (!svg) return;
        svg.innerHTML = ""; // Hapus isi lama.
        this.renderDonutChart(elementId, newData);
    }

    // Memperbarui grafik CPL dengan data baru.
    static updateCplBarChart(elementId, newData) {
        this.renderCplBarChart(elementId, newData);
    }

    // Memperbarui grafik mata kuliah dengan data baru.
    static updateMkBarChart(elementId, newData) {
        this.renderMkBarChart(elementId, newData);
    }
}

// Merender grafik otomatis saat DOM siap.
if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", () => {
        DosenDashboardComponent.renderCharts();
    });
} else {
    DosenDashboardComponent.renderCharts();
}

// Instance global agar bisa dipakai dari script lain.
const DosenDashboard = DosenDashboardComponent;
