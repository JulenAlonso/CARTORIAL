// =======================================================
// PERFIL-GRAPHS.JS
// - Gestiona todos los gráficos CanvasJS del perfil:
//   · KM (día / mes / año)
//   · Gastos (día / mes / año)
//   · Valor (nuevo / 2ª mano)
// - Usa los datos inyectados desde Blade en:
//   window.PERFIL_KM_DATA, window.PERFIL_GASTOS_DATA, window.PERFIL_VALOR_DATA
// =======================================================

// Cuando el DOM está listo, inicializamos todos los gráficos
document.addEventListener("DOMContentLoaded", function () {
    initKmCharts();
    initGastoCharts();
    initValorCharts();
});

/* ========================= KM ========================= */
/* Gráficos de kilómetros por vehículo (Día / Mes / Año) */

function initKmCharts() {
    // Objeto global con datos por idVehiculo => array de registros KM
    const kmData = window.PERFIL_KM_DATA || {};

    // Recorremos cada vehículo que tiene datos de KM
    Object.keys(kmData).forEach(idVehiculo => {
        const dialog      = document.getElementById(`modalKm_${idVehiculo}`);   // <dialog> del gráfico
        const containerId = `chartKm_${idVehiculo}`;                            // id del contenedor del gráfico
        const raw         = kmData[idVehiculo];                                 // array crudo de registros km

        // Si no hay dialog o no hay datos, salimos
        if (!dialog || !raw || !raw.length) return;

        let chartRendered = false; // Para no renderizar dos veces el mismo gráfico
        let chart;                // Instancia de CanvasJS.Chart

        // Agrupa por día → para ese día nos quedamos con el último valor de km
        function groupByDay() {
            const map = {}; // clave: YYYY-MM-DD
            raw.forEach(r => {
                map[r.day] = {
                    x: r.timestamp,   // timestamp en ms (para xValueType: dateTime)
                    y: r.km,          // km actual ese día
                    label: r.dayLabel // etiqueta "dd/mm/YYYY"
                };
            });
            // Ordenamos por fecha (x) ascendente
            return Object.values(map).sort((a, b) => a.x - b.x);
        }

        // Agrupa por mes → último valor de km del mes
        function groupByMonth() {
            const map = {}; // clave: YYYY-MM
            raw.forEach(r => {
                map[r.month] = {
                    x: r.timestamp,
                    y: r.km,
                    label: r.monthLabel // "mm/YYYY"
                };
            });
            return Object.values(map).sort((a, b) => a.x - b.x);
        }

        // Agrupa por año → último valor de km del año
        function groupByYear() {
            const map = {}; // clave: YYYY
            raw.forEach(r => {
                map[r.year] = {
                    x: r.timestamp,
                    y: r.km,
                    label: r.yearLabel // "YYYY"
                };
            });
            return Object.values(map).sort((a, b) => a.x - b.x);
        }

        // Cambia la vista del gráfico (día / mes / año)
        function renderChart(view = "day") {
            let dataPoints;

            if (view === "day")        dataPoints = groupByDay();
            else if (view === "month") dataPoints = groupByMonth();
            else                       dataPoints = groupByYear();

            chart.options.data[0].dataPoints = dataPoints;
            chart.render();
        }

        // Solo renderizamos el gráfico cuando se abre el <dialog> por primera vez
        dialog.addEventListener("toggle", function () {
            // Si el dialog no está abierto o ya está renderizado → nada
            if (!dialog.open || chartRendered) return;
            chartRendered = true;

            // Creamos la instancia de CanvasJS para KM
            chart = new CanvasJS.Chart(containerId, {
                animationEnabled: true,
                theme: "light2",
                title: { text: "Evolución de kilómetros" },
                axisX: {
                    valueFormatString: "DD MMM" // solo formato visual del eje X
                },
                axisY: {
                    title: "Kilómetros",
                    includeZero: false
                },
                data: [{
                    type: "splineArea",
                    color: "#6599FF",          // color del área
                    xValueType: "dateTime",    // usamos timestamp en ms
                    yValueFormatString: "#,##0 km",
                    dataPoints: groupByDay()   // vista inicial → día
                }]
            });

            chart.render();

            // Botones de filtro (Día / Mes / Año) dentro del dialog de este vehículo
            const botones = dialog.querySelectorAll(".btnFiltro");

            // Vista inicial: día → aplicamos clase "active-view"
            botones.forEach(b => b.classList.remove("active-view"));
            const btnDia = dialog.querySelector('[data-view="day"]');
            if (btnDia) btnDia.classList.add("active-view");

            // Asignamos listener a cada botón
            botones.forEach(btn => {
                btn.addEventListener("click", function () {
                    const view = this.dataset.view; // "day", "month" o "year"

                    // Cambiamos la vista del gráfico
                    renderChart(view);

                    // Actualizamos estilos de botón activo (azul)
                    botones.forEach(b => b.classList.remove("active-view"));
                    this.classList.add("active-view");
                });
            });
        });
    });
}

/* ========================= GASTOS ========================= */
/* Gráficos de gastos por vehículo (Día / Mes / Año) */

function initGastoCharts() {
    // Objeto global con datos de gastos por idVehiculo
    const gastosData = window.PERFIL_GASTOS_DATA || {};

    Object.keys(gastosData).forEach(idVehiculo => {
        const dialog      = document.getElementById(`modalGastos_${idVehiculo}`); // <dialog> de gastos
        const containerId = `chartGastos_${idVehiculo}`;                          // id contenedor gráfico
        const raw         = gastosData[idVehiculo];                               // array crudo de gastos

        if (!dialog || !raw || !raw.length) return;

        let chartRendered = false;
        let chart;

        // Agrupación por día → sumamos todos los importes del mismo día
        function groupByDay() {
            const map = {}; // clave: YYYY-MM-DD
            raw.forEach(g => {
                if (!map[g.date]) {
                    map[g.date] = { label: g.dayLabel, y: 0 };
                }
                map[g.date].y += g.importe;
            });
            // Ordenamos por fecha
            return Object.keys(map).sort().map(k => map[k]);
        }

        // Agrupación por mes → sumamos todos los importes del mismo mes
        function groupByMonth() {
            const map = {}; // clave: YYYY-MM
            raw.forEach(g => {
                if (!map[g.month]) {
                    map[g.month] = { label: g.monthLabel, y: 0 };
                }
                map[g.month].y += g.importe;
            });
            return Object.keys(map).sort().map(k => map[k]);
        }

        // Agrupación por año → sumamos todos los importes del mismo año
        function groupByYear() {
            const map = {}; // clave: YYYY
            raw.forEach(g => {
                if (!map[g.year]) {
                    map[g.year] = { label: g.yearLabel, y: 0 };
                }
                map[g.year].y += g.importe;
            });
            return Object.keys(map).sort().map(k => map[k]);
        }

        // Cambia entre vista diaria / mensual / anual
        function renderChart(view = "day") {
            let dataPoints;
            let serieName;
            let axisTitle;

            if (view === "day") {
                dataPoints = groupByDay();
                serieName  = "Gasto diario";
                axisTitle  = "Gasto por día";
            } else if (view === "month") {
                dataPoints = groupByMonth();
                serieName  = "Gasto mensual";
                axisTitle  = "Gasto por mes";
            } else {
                dataPoints = groupByYear();
                serieName  = "Gasto anual";
                axisTitle  = "Gasto por año";
            }

            // Actualizamos título del eje Y y nombre de la serie
            chart.options.axisY.title = axisTitle;
            chart.options.data[0].name = serieName;
            chart.options.data[0].dataPoints = dataPoints;
            chart.render();
        }

        // Renderiza el gráfico únicamente cuando se abre el dialog por primera vez
        dialog.addEventListener("toggle", function () {
            if (!dialog.open || chartRendered) return;
            chartRendered = true;

            chart = new CanvasJS.Chart(containerId, {
                title: {
                    // Aquí podrías poner el total precalculado desde PHP si lo necesitas
                    text: "Gastos Totales (€)"
                },
                theme: "light2",
                animationEnabled: true,
                toolTip: {
                    shared: false,
                    yValueFormatString: "€#,##0.00"
                },
                axisY: {
                    title: "Gasto por día", // título inicial → luego cambia con renderChart()
                    suffix: " €",
                    includeZero: true
                },
                legend: {
                    enabled: false
                },
                data: [{
                    type: "column",
                    name: "Gasto diario", // nombre inicial
                    yValueFormatString: "€#,##0.00",
                    dataPoints: groupByDay() // vista inicial diaria
                }]
            });

            chart.render();

            // Botones de filtro dentro de este modal de gastos
            const botones = dialog.querySelectorAll(".btnFiltro");

            // Seleccionamos la vista "Día" por defecto
            botones.forEach(b => b.classList.remove("active-view"));
            const btnDia = dialog.querySelector('[data-view="day"]');
            if (btnDia) btnDia.classList.add("active-view");

            // Listener para cada botón
            botones.forEach(btn => {
                btn.addEventListener("click", function () {
                    const view = this.dataset.view; // "day" | "month" | "year"

                    // Cambiamos la vista del gráfico
                    renderChart(view);

                    // Actualizamos el estilo visual del botón activo
                    botones.forEach(b => b.classList.remove("active-view"));
                    this.classList.add("active-view");
                });
            });
        });
    });
}

/* ========================= VALOR ========================= */
/* Gráficos de valor del vehículo (nuevo vs 2ª mano, año a año) */

function initValorCharts() {
    // Objeto con datos de valor por idVehiculo:
    // { idVehiculo: { nuevo: [...], segunda: [...] } }
    const valorData = window.PERFIL_VALOR_DATA || {};

    Object.keys(valorData).forEach(idVehiculo => {
        const dialog      = document.getElementById(`modalValor_${idVehiculo}`); // dialog valor
        const containerId = `chartValor_${idVehiculo}`;                          // id div gráfico
        const dataVehiculo = valorData[idVehiculo];                              // { nuevo, segunda }

        if (!dialog || !dataVehiculo) return;

        let chartRendered = false;

        // Solo se crea el gráfico cuando se abre el dialog la primera vez
        dialog.addEventListener("toggle", function () {
            if (!dialog.open || chartRendered) return;
            chartRendered = true;

            const dataNuevo   = dataVehiculo.nuevo   || []; // puntos de "precio nuevo"
            const dataSegunda = dataVehiculo.segunda || []; // puntos de "precio 2ª mano"

            // Instancia de CanvasJS para el valor del vehículo
            const chart = new CanvasJS.Chart(containerId, {
                animationEnabled: true,
                theme: "light2",
                title: {
                    text: "Evolución del valor del vehículo"
                },
                axisX: {
                    valueFormatString: "YYYY" // mostramos solo el año en el eje X
                },
                axisY: {
                    prefix: "€",
                    includeZero: false
                },
                toolTip: {
                    shared: true // tooltip compartido entre las dos series
                },
                legend: {
                    cursor: "pointer",
                    // Al hacer click en la leyenda, ocultamos/mostramos la serie
                    itemclick: function (e) {
                        e.dataSeries.visible = !(e.dataSeries.visible ?? true);
                        e.chart.render();
                    }
                },
                data: [
                    {
                        type: "area",
                        color: "#4A90E2",          // azul → precio nuevo
                        name: "Precio nuevo",
                        showInLegend: true,
                        xValueType: "dateTime",
                        xValueFormatString: "YYYY",
                        yValueFormatString: "€#,##0.##",
                        dataPoints: dataNuevo
                    },
                    {
                        type: "area",
                        color: "#E24A4A",          // rojo → precio 2ª mano
                        name: "Precio 2ª mano",
                        showInLegend: true,
                        xValueType: "dateTime",
                        xValueFormatString: "YYYY",
                        yValueFormatString: "€#,##0.##",
                        dataPoints: dataSegunda
                    }
                ]
            });

            chart.render();
        });
    });
}
