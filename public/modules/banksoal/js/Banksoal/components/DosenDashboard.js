/**
 * ════════════════════════════════════════════════════════════════════
 * DosenDashboard.js - Dashboard Charts Component
 * ════════════════════════════════════════════════════════════════════
 * Renders dashboard charts (donut, CPL bar, MK bar) on page load.
 *
 * Usage:
 *   DosenDashboard.renderCharts();
 *
 * Or with custom data:
 *   DosenDashboard.renderDonutChart('donutChart', donutData);
 *   DosenDashboard.renderCplBarChart('cplChart', cplData);
 *   DosenDashboard.renderMkBarChart('mkChart', mkData);
 */

class DosenDashboardComponent {
    /**
     * Render all dashboard charts (called on page load)
     */
    static renderCharts() {
        this.renderDonutChart();
        this.renderCplBarChart();
        this.renderMkBarChart();
    }

    /**
     * Render donut chart
     * @param {string} elementId - SVG element ID
     * @param {Array} data - Chart data (optional, uses default if not provided)
     */
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

        const cx = 80,
            cy = 80,
            r = 60,
            stroke = 22;
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
        inner.setAttribute("r", r - stroke / 2 - 4);
        inner.setAttribute("fill", "white");
        svg.appendChild(inner);
    }

    /**
     * Render CPL distribution bar chart
     * @param {string} elementId - Container element ID
     * @param {Object} data - Chart data (optional)
     */
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
        let html = "";

        Object.entries(chartData).forEach(([label, val]) => {
            const h = Math.max(8, (val / max) * 90);
            html += `
                <div class="bar-group">
                    <span class="bar-val">${val}</span>
                    <div class="bar" style="height:${h}px"></div>
                    <span class="bar-label">${label}</span>
                </div>
            `;
        });

        wrap.innerHTML = html;
    }

    /**
     * Render MK (Mata Kuliah) count bar chart
     * @param {string} elementId - Container element ID
     * @param {Array} data - Chart data (optional)
     */
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
        let html = "";

        chartData.forEach((d) => {
            const h = Math.max(4, (d.count / max) * 80);
            const valColor = d.count > 0 ? "#22C55E" : "var(--gray-400)";
            html += `
                <div style="display:flex;flex-direction:column;align-items:center;gap:6px">
                    <span style="font-size:20px;font-weight:800;color:${valColor}">${d.count || ""}</span>
                    <div style="width:48px;border-radius:8px 8px 0 0;background:${d.color};height:${h}px"></div>
                    <span style="font-size:12px;color:var(--gray-400);font-weight:500">${d.mk}</span>
                </div>
            `;
        });

        wrap.innerHTML = html;
    }

    /**
     * Update donut chart data dynamically
     * @param {string} elementId - SVG element ID
     * @param {Array} newData - New chart data
     */
    static updateDonutChart(elementId, newData) {
        const svg = document.getElementById(elementId);
        if (!svg) return;
        svg.innerHTML = ""; // Clear existing
        this.renderDonutChart(elementId, newData);
    }

    /**
     * Update CPL bar chart data dynamically
     * @param {string} elementId - Container element ID
     * @param {Object} newData - New chart data
     */
    static updateCplBarChart(elementId, newData) {
        this.renderCplBarChart(elementId, newData);
    }

    /**
     * Update MK bar chart data dynamically
     * @param {string} elementId - Container element ID
     * @param {Array} newData - New chart data
     */
    static updateMkBarChart(elementId, newData) {
        this.renderMkBarChart(elementId, newData);
    }
}

// Auto-render on DOM ready
if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", () => {
        DosenDashboard.renderCharts();
    });
} else {
    DosenDashboard.renderCharts();
}
