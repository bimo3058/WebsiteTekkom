class DosenDashboardComponent {
    /**
     * Render all dashboard charts (called on page load)
     */
    static renderCharts() {
        this.renderDonutChart();
        this.renderCplBarChart();
        this.renderMkBarChart();
    }

    static renderDonutChart(elementId = "donutChart", data = null) {
        const svg = document.getElementById(elementId);
        if (!svg) return;

        // Default data if not provided
        const chartData = data || [
            { value: 75, color: "#22C55E" }, // Approved
            { value: 28, color: "#F59E0B" }, // Review
            { value: 15, color: "#3B82F6" }, // Pending
            { value: 10, color: "#EF4444" }, // Rejected
        ];

        const cx = 40,
            cy = 40,
            r = 30,
            stroke = 12;
        const circ = 2 * Math.PI * r;
        const total = chartData.reduce((sum, seg) => sum + seg.value, 0);

        let offset = 0;
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

        // Inner white circle
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

    static renderCplBarChart(elementId = "cplChart", data = null) {
        const wrap = document.getElementById(elementId);
        if (!wrap) return;

        // Default data if not provided
        const chartData = data || {
            "CPL 01": 45,
            "CPL 02": 30,
            "CPL 03": 60,
            "CPL 04": 20,
            "CPL 05": 38,
        };

        const max = Math.max(...Object.values(chartData));
        const BAR_H = 90;
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

    static renderMkBarChart(elementId = "mkChart", data = null) {
        const wrap = document.getElementById(elementId);
        if (!wrap) return;

        // Default data if not provided
        const chartData = data || [
            { mk: "CS-201", count: 54, color: "#22C55E" },
            { mk: "CS-304", count: 32, color: "#22C55E" },
            { mk: "CS-401", count: 0, color: "#CBD5E1" },
        ];

        const max = Math.max(...chartData.map((d) => d.count)) || 1;
        const BAR_H = 90; // max bar height in px
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

    static updateDonutChart(elementId, newData) {
        const svg = document.getElementById(elementId);
        if (!svg) return;
        svg.innerHTML = ""; // Clear existing
        this.renderDonutChart(elementId, newData);
    }

    static updateCplBarChart(elementId, newData) {
        this.renderCplBarChart(elementId, newData);
    }

    static updateMkBarChart(elementId, newData) {
        this.renderMkBarChart(elementId, newData);
    }
}

// Auto-render on DOM ready
if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", () => {
        DosenDashboardComponent.renderCharts();
    });
} else {
    DosenDashboardComponent.renderCharts();
}

// Export as DosenDashboard for global access
const DosenDashboard = DosenDashboardComponent;
