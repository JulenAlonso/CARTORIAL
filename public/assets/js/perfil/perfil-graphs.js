// =======================================================
// PERFIL-GRAPHS.JS
//  - Gráficos CanvasJS de:
//    · KM (día / mes / año)
//    · Gastos (día / mes / año)
//    · Valor (nuevo / 2ª mano)
//  - Usa window.PERFIL_KM_DATA, window.PERFIL_GASTOS_DATA, window.PERFIL_VALOR_DATA
//  - Funciones públicas para el Blade: openKmModal, openGastosModal, openValorModal
// =======================================================

(function () {
    // Seguridad básica
    if (typeof CanvasJS === "undefined") {
        console.warn("CanvasJS no está cargado.");
        return;
    }

    // ====== HELPERS ===================================================

    function ensureContainer(id) {
        const el = document.getElementById(id);
        if (!el) return null;
        // Limpia el contenedor antes de pintar
        el.innerHTML = "";
        return el;
    }

    function showEmptyMessage(containerId, msg) {
        const el = document.getElementById(containerId);
        if (!el) return;
        el.innerHTML = "<p style='text-align:center; color:#666; font-size:0.9rem;'>" +
            (msg || "No hay datos suficientes para mostrar el gráfico.") +
            "</p>";
    }

    function groupByLastValue(data, keyField) {
        const map = {};
        data.forEach((item) => {
            const key = item[keyField];
            if (!key) return;
            if (!map[key] || item.timestamp > map[key].timestamp) {
                map[key] = item;
            }
        });
        return Object.values(map).sort((a, b) => a.timestamp - b.timestamp);
    }

    function groupSum(data, keyField) {
        const map = {};
        data.forEach((item) => {
            const key = item[keyField];
            if (!key) return;
            if (!map[key]) {
                map[key] = {
                    key,
                    total: 0,
                    // labels para mostrar
                    dayLabel: item.dayLabel,
                    monthLabel: item.monthLabel,
                    yearLabel: item.yearLabel
                };
            }
            map[key].total += Number(item.importe || 0);
        });
        return Object.values(map);
    }

    // ====== KM =========================================================

    function renderKmChart(idVehiculo, view) {
        const raw = (window.PERFIL_KM_DATA && window.PERFIL_KM_DATA[idVehiculo]) || [];
        const containerId = "chartKm_" + idVehiculo;

        if (!raw.length) {
            showEmptyMessage(containerId, "No hay registros de kilómetros para este vehículo.");
            return;
        }

        const container = ensureContainer(containerId);
        if (!container) return;

        let dataPoints = [];
        const sorted = raw.slice().sort((a, b) => a.timestamp - b.timestamp);

        if (view === "day") {
            dataPoints = sorted.map((r) => ({
                x: new Date(r.timestamp),
                y: Number(r.km) || 0
            }));
        } else if (view === "month") {
            const grouped = groupByLastValue(sorted, "month");
            dataPoints = grouped.map((r) => ({
                x: new Date(r.timestamp),
                y: Number(r.km) || 0,
                label: r.monthLabel
            }));
        } else { // year
            const grouped = groupByLastValue(sorted, "year");
            dataPoints = grouped.map((r) => ({
                x: new Date(r.timestamp),
                y: Number(r.km) || 0,
                label: r.yearLabel
            }));
        }

        const chart = new CanvasJS.Chart(containerId, {
            animationEnabled: true,
            theme: "light2",
            axisX: {
                valueFormatString: view === "day"
                    ? "DD/MM/YYYY"
                    : (view === "month" ? "MM/YYYY" : "YYYY"),
                labelAngle: view === "day" ? -45 : 0
            },
            axisY: {
                title: "Kilómetros",
                includeZero: false
            },
            data: [{
                type: "line",
                markerSize: 5,
                xValueType: "dateTime",
                dataPoints: dataPoints
            }]
        });

        chart.render();
    }

    // ====== GASTOS =====================================================

    function renderGastosChart(idVehiculo, view) {
        const raw = (window.PERFIL_GASTOS_DATA && window.PERFIL_GASTOS_DATA[idVehiculo]) || [];
        const containerId = "chartGastos_" + idVehiculo;

        if (!raw.length) {
            showEmptyMessage(containerId, "No hay registros de gastos para este vehículo.");
            return;
        }

        const container = ensureContainer(containerId);
        if (!container) return;

        let grouped;
        if (view === "day") {
            grouped = groupSum(raw, "date");
        } else if (view === "month") {
            grouped = groupSum(raw, "month");
        } else { // year
            grouped = groupSum(raw, "year");
        }

        // Para usar fechas en el eje X, montamos una Date según el tipo
        const dataPoints = grouped.map((g) => {
            let dt;
            if (view === "day") {
                // g.key = "YYYY-MM-DD"
                dt = new Date(g.key + "T00:00:00");
            } else if (view === "month") {
                // g.key = "YYYY-MM"
                dt = new Date(g.key + "-01T00:00:00");
            } else {
                // g.key = "YYYY"
                dt = new Date(g.key + "-01-01T00:00:00");
            }
            return {
                x: dt,
                y: Number(g.total) || 0
            };
        }).sort((a, b) => a.x - b.x);

        const chart = new CanvasJS.Chart(containerId, {
            animationEnabled: true,
            theme: "light2",
            axisX: {
                valueFormatString: view === "day"
                    ? "DD/MM/YYYY"
                    : (view === "month" ? "MM/YYYY" : "YYYY"),
                labelAngle: view === "day" ? -45 : 0
            },
            axisY: {
                title: "Gastos (€)",
                includeZero: true,
                prefix: ""
            },
            data: [{
                type: "column",
                dataPoints: dataPoints
            }]
        });

        chart.render();
    }

    // ====== VALOR ======================================================

    function renderValorChart(idVehiculo) {
        const data = window.PERFIL_VALOR_DATA && window.PERFIL_VALOR_DATA[idVehiculo];
        const containerId = "chartValor_" + idVehiculo;

        if (!data || ((!data.nuevo || !data.nuevo.length) && (!data.segunda || !data.segunda.length))) {
            showEmptyMessage(containerId, "No hay datos de valor para este vehículo.");
            return;
        }

        const container = ensureContainer(containerId);
        if (!container) return;

        const series = [];

        if (data.nuevo && data.nuevo.length) {
            series.push({
                type: "line",
                name: "Precio nuevo",
                showInLegend: true,
                xValueType: "dateTime",
                dataPoints: data.nuevo.map(p => ({
                    x: new Date(p.x),
                    y: Number(p.y) || 0
                }))
            });
        }

        if (data.segunda && data.segunda.length) {
            series.push({
                type: "line",
                name: "Precio 2ª mano",
                showInLegend: true,
                xValueType: "dateTime",
                dataPoints: data.segunda.map(p => ({
                    x: new Date(p.x),
                    y: Number(p.y) || 0
                }))
            });
        }

        const chart = new CanvasJS.Chart(containerId, {
            animationEnabled: true,
            theme: "light2",
            axisX: {
                valueFormatString: "YYYY",
                labelAngle: 0
            },
            axisY: {
                title: "Valor (€)",
                includeZero: false
            },
            legend: {
                verticalAlign: "top",
                horizontalAlign: "center"
            },
            data: series
        });

        chart.render();
    }

    // ====== FUNCIONES PÚBLICAS (usadas en el Blade) ====================

    window.openKmModal = function (idVehiculo) {
        const dialog = document.getElementById("modalKm_" + idVehiculo);
        if (!dialog) return;

        dialog.showModal();
        // por defecto vista "day"
        renderKmChart(idVehiculo, "day");

        // enganchar botones de filtros solo una vez
        if (!dialog.dataset.filtersBound) {
            const btns = dialog.querySelectorAll(".btnFiltro");
            btns.forEach(btn => {
                btn.addEventListener("click", function () {
                    const view = this.dataset.view || "day";
                    renderKmChart(idVehiculo, view);
                });
            });
            dialog.dataset.filtersBound = "1";
        }
    };

    window.openGastosModal = function (idVehiculo) {
        const dialog = document.getElementById("modalGastos_" + idVehiculo);
        if (!dialog) return;

        dialog.showModal();
        renderGastosChart(idVehiculo, "day");

        if (!dialog.dataset.filtersBound) {
            const btns = dialog.querySelectorAll(".btnFiltro");
            btns.forEach(btn => {
                btn.addEventListener("click", function () {
                    const view = this.dataset.view || "day";
                    renderGastosChart(idVehiculo, view);
                });
            });
            dialog.dataset.filtersBound = "1";
        }
    };

    window.openValorModal = function (idVehiculo) {
        const dialog = document.getElementById("modalValor_" + idVehiculo);
        if (!dialog) return;

        dialog.showModal();
        renderValorChart(idVehiculo);
    };

})();
