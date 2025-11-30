// =======================================================
// Calendario grande del perfil (panel-calendario)
// - Muestra un calendario mensual
// - Pinta "badges" de km, gastos y notas por día
// - Actualiza el panel de detalles y la fecha del formulario de nota
// =======================================================

document.addEventListener('DOMContentLoaded', function () {
    // Panel principal del calendario grande
    const panelCalendario = document.getElementById('panel-calendario');
    // Si la vista no tiene el panel (por ejemplo, otra plantilla), no hacemos nada
    if (!panelCalendario) return;

    // Elementos clave del DOM
    const monthLabel     = document.getElementById('cal-month-label');   // Texto con "Mes Año"
    const calBody        = document.getElementById('cal-body');          // <tbody> donde se pintan los días
    const prevBtn        = document.getElementById('cal-prev');          // Botón mes anterior
    const nextBtn        = document.getElementById('cal-next');          // Botón mes siguiente
    const detailsTitle   = document.getElementById('cal-details-title'); // Título del panel de detalles
    const detailsContent = document.getElementById('cal-details-content'); // Contenido HTML del panel de detalles
    const notaFechaInput = document.getElementById('nota_fecha_evento');   // Input date del formulario de nota

    // ==============================
    // Preprocesado de eventos por fecha
    // CALENDAR_EVENTS viene inyectado desde Blade en window.CALENDAR_EVENTS
    // Formato esperado por cada evento: { fecha: 'YYYY-MM-DD', km, gastos, nota, hora_evento, ... }
    // ==============================
    const eventsByDate = {};
    (window.CALENDAR_EVENTS || []).forEach(e => {
        // Si el evento no tiene fecha, lo ignoramos
        if (!e.fecha) return;

        // Agrupamos los eventos por fecha (YYYY-MM-DD)
        if (!eventsByDate[e.fecha]) {
            eventsByDate[e.fecha] = [];
        }
        eventsByDate[e.fecha].push(e);
    });

    // Fecha actual (se usa para decidir qué mes mostrar)
    let current = new Date();

    // Nombres de los meses en español (versión "bonita" del label)
    const monthNames = [
        'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio',
        'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'
    ];

    // Día seleccionado actualmente en el calendario
    let selectedCell = null; // <td> seleccionado
    let selectedDate = null; // fecha en formato "YYYY-MM-DD"

    // Helper: convierte un objeto Date a "YYYY-MM-DD"
    function formatDateISO(date) {
        const y = date.getFullYear();
        const m = String(date.getMonth() + 1).padStart(2, '0');
        const d = String(date.getDate()).padStart(2, '0');
        return `${y}-${m}-${d}`;
    }

    // ==============================
    // Función principal: pintar calendario del mes actual
    // ==============================
    function renderCalendar() {
        const year  = current.getFullYear();   // Año actual
        const month = current.getMonth();      // Mes actual (0-11)

        // Label del encabezado → "Mes Año" con la primera letra en mayúscula
        monthLabel.textContent =
            `${monthNames[month].charAt(0).toUpperCase() + monthNames[month].slice(1)} ${year}`;

        // Limpiamos el cuerpo de la tabla para repintar
        calBody.innerHTML = '';

        // Primer día del mes (ej: 2025-11-01)
        const first = new Date(year, month, 1);
        let startDay = first.getDay(); // getDay(): 0=Domingo, 1=Lunes, ... 6=Sábado

        // Normalizamos para que Lunes=1, Martes=2, ..., Domingo=7
        if (startDay === 0) startDay = 7;

        // Número de días que tiene el mes (p.ej. 30, 31, 28...)
        const daysInMonth = new Date(year, month + 1, 0).getDate();

        let day = 1; // contador de día a pintar

        // Máximo 6 filas (semanas) en la tabla
        for (let week = 0; week < 6; week++) {
            const tr = document.createElement('tr');

            // Recorremos los 7 días de la semana (Lun..Dom)
            for (let dow = 1; dow <= 7; dow++) {
                const td = document.createElement('td');

                // Caso 1: primera semana → pintar celdas vacías antes de "startDay"
                // Caso 2: ya hemos pintado todos los días del mes → resto también vacío
                if ((week === 0 && dow < startDay) || day > daysInMonth) {
                    td.classList.add('empty');
                    tr.appendChild(td);
                    continue;
                }

                // Construimos una fecha con el día actual que toca pintar
                const cellDate = new Date(year, month, day);
                // Obtenemos el ISO "YYYY-MM-DD"
                const iso = formatDateISO(cellDate);

                // Guardamos la fecha en un data-atribute
                td.dataset.date = iso;
                td.classList.add('calendar-day');

                // Número de día en grande
                const dayNumber = document.createElement('div');
                dayNumber.classList.add('day-number');
                dayNumber.textContent = day;
                td.appendChild(dayNumber);

                // Buscamos si hay eventos para ese día
                const items = eventsByDate[iso];
                if (items && items.length) {
                    // Si hay eventos, marcamos la celda con clase "has-data"
                    td.classList.add('has-data');

                    // Contenedor de badges (km, gastos, nota)
                    const badges = document.createElement('div');
                    badges.classList.add('day-badges');

                    // Totalizamos km y gastos del día
                    const totalKm = items.reduce((acc, e) => acc + (Number(e.km) || 0), 0);
                    const totalGastos = items.reduce((acc, e) => acc + (Number(e.gastos) || 0), 0);
                    // Comprobamos si hay alguna nota en ese día
                    const hasNota = items.some(e => e.nota);

                    // Badge de km (solo si hay km > 0)
                    if (totalKm > 0) {
                        const kmBadge = document.createElement('span');
                        kmBadge.classList.add('badge', 'badge-km');
                        kmBadge.textContent = `${totalKm} km`;
                        badges.appendChild(kmBadge);
                    }

                    // Badge de gastos (solo si hay gastos > 0)
                    if (totalGastos > 0) {
                        const gastoBadge = document.createElement('span');
                        gastoBadge.classList.add('badge', 'badge-gastos');
                        // toFixed(2) para mostrar siempre 2 decimales
                        gastoBadge.textContent = `${totalGastos.toFixed(2)} €`;
                        badges.appendChild(gastoBadge);
                    }

                    // Badge de nota (si hay al menos una nota en ese día)
                    if (hasNota) {
                        const notaBadge = document.createElement('span');
                        notaBadge.classList.add('badge', 'badge-nota');

                        // Cogemos la primera nota que tenga hora (si existiera)
                        const firstNota = items.find(i => i.nota);
                        let hora = firstNota?.hora_evento || '';

                        // Recortamos a HH:MM si viene como HH:MM:SS o similar
                        if (hora && hora.length >= 5) {
                            hora = hora.slice(0, 5);
                        }

                        // Texto del badge → "📝 10:30" o solo "📝"
                        notaBadge.textContent = hora ? `📝 ${hora}` : '📝';
                        badges.appendChild(notaBadge);
                    }

                    td.appendChild(badges);
                }

                // Si esta fecha coincide con la última fecha seleccionada, marcamos la celda
                if (iso === selectedDate) {
                    td.classList.add('selected-day');
                    selectedCell = td;
                }

                // Listener de click en cada día del calendario
                td.addEventListener('click', function () {
                    // Quitamos la selección anterior si existe
                    if (selectedCell) {
                        selectedCell.classList.remove('selected-day');
                    }

                    // Marcamos la nueva celda seleccionada
                    selectedCell = td;
                    selectedDate = iso;
                    td.classList.add('selected-day');

                    // Actualizamos el panel de detalles para ese día
                    showDetails(iso);
                });

                tr.appendChild(td);
                day++; // pasamos al siguiente día
            }

            calBody.appendChild(tr);

            // Si ya hemos pintado todos los días del mes, salimos del bucle de semanas
            if (day > daysInMonth) break;
        }
    }

    // ==============================
    // Muestra el panel de detalles para una fecha concreta (iso = "YYYY-MM-DD")
    // ==============================
    function showDetails(iso) {
        const items = eventsByDate[iso] || [];

        // Descomponemos la fecha para mostrarla en formato DD/MM/YYYY
        const [year, month, day] = iso.split('-');
        detailsTitle.textContent = `Detalles del ${day}/${month}/${year}`;

        // Sincronizamos el input "fecha_evento" del formulario de nota con la fecha seleccionada
        if (notaFechaInput) {
            notaFechaInput.value = iso;
        }

        // Si no hay eventos para ese día, mostramos un mensaje por defecto
        if (!items.length) {
            detailsContent.textContent = 'No hay datos registrados para este día.';
            return;
        }

        // Si hay eventos, construimos HTML con las notas, horas, km y gastos
        let html = '';
        items.forEach(e => {
            html += `
                <div class="detail-item">
                    ${e.nota ? `<div class="detail-line"><strong>Nota:</strong> ${e.nota}</div>` : ''}
                    ${e.hora_evento ? `<div class="detail-line"><strong>Hora:</strong> ${e.hora_evento}</div>` : ''}
                    ${e.km ? `<div class="detail-line"><strong>Kilómetros:</strong> ${e.km}</div>` : ''}
                    ${e.gastos ? `<div class="detail-line"><strong>Gastos:</strong> ${Number(e.gastos).toFixed(2)} €</div>` : ''}
                </div>
            `;
        });

        // Metemos todo en el contenedor de detalles
        detailsContent.innerHTML = html;
    }

    // ==============================
    // Listeners para navegar entre meses
    // ==============================
    prevBtn.addEventListener('click', function () {
        // Restamos un mes a "current" y repintamos
        current.setMonth(current.getMonth() - 1);
        renderCalendar();
    });

    nextBtn.addEventListener('click', function () {
        // Sumamos un mes a "current" y repintamos
        current.setMonth(current.getMonth() + 1);
        renderCalendar();
    });

    // Primera renderización al cargar la página
    renderCalendar();
});
