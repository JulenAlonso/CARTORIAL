// ======================================================
// PERFIL.JS — Control de tarjetas + modos + gráfico de KM
// ======================================================

(function () {
  const section = document.getElementById('seccion-mis-vehiculos');
  const cardsContainer = document.querySelector('.cards');
  const topCards = cardsContainer ? Array.from(cardsContainer.querySelectorAll('.card')) : [];

  // Tarjetas
  const cardVehiculos  = document.getElementById('card-vehiculos-registrados');
  const cardValor      = document.getElementById('card-valor');
  const cardKm         = document.getElementById('card-km');
  const cardGastos     = document.getElementById('card-gastos');
  const cardCalendario = document.getElementById('card-calendario');

  // Panel calendario grande
  const panelCalendario = document.getElementById('panel-calendario');

  let activeCardEl = null;

  // 🔹 Limpia modos y clases
  const clearModes = () => {
    section?.classList.remove('modo-valor', 'modo-km', 'modo-gastos', 'modo-calendario');
    const grid = section?.querySelector('.vehiculos-grid, .vehiculos-grid2');
    if (grid) {
      grid.classList.remove('vehiculos-grid', 'vehiculos-grid2');
      grid.classList.add('vehiculos-grid'); // por defecto
    }
  };

  // 🔹 Mostrar solo una tarjeta
  const showOnly = (cardEl) => {
    topCards.forEach(c => c === cardEl ? c.classList.remove('hidden') : c.classList.add('hidden'));
    activeCardEl = cardEl;
  };

  // 🔹 Mostrar todas
  const showAll = () => {
    topCards.forEach(c => c.classList.remove('hidden'));
    activeCardEl = null;
  };

  // 🔹 Abrir sección (mis vehículos)
  const openSection = (modeClass, gridType = 'vehiculos-grid') => {
    if (!section) return;
    section.classList.add('open');
    clearModes();
    if (modeClass) section.classList.add(modeClass);

    const grid = section.querySelector('.vehiculos-grid, .vehiculos-grid2');
    if (grid) {
      grid.classList.remove('vehiculos-grid', 'vehiculos-grid2');
      grid.classList.add(gridType);
    }
  };

  // 🔹 Mostrar calendario y ocultar mis vehículos
  const showCalendarPanel = () => {
    if (section) section.style.display = 'none';
    if (panelCalendario) {
      panelCalendario.style.display = 'block';
      panelCalendario.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
  };

  // 🔹 Mostrar mis vehículos y ocultar calendario
  const showVehiclesPanel = () => {
    if (panelCalendario) panelCalendario.style.display = 'none';
    if (section) section.style.display = 'block';
  };

  // 🔹 Registro de tarjetas
  const registerCard = ({ el, modeClass, gridType, onOpen }) => {
    if (!el) return;

    const handleToggle = () => {
      const isSameCard = activeCardEl === el;

      // Cerrar si se pulsa la misma tarjeta activa
      if (isSameCard && section?.classList.contains('open')) {
        section.classList.remove('open');
        clearModes();
        showAll();
        // Siempre volvemos a "Mis vehículos" y ocultamos el calendario
        showVehiclesPanel();
        return;
      }

      // Abrir modo correspondiente
      openSection(modeClass, gridType);
      showOnly(el);
      if (typeof onOpen === 'function') onOpen();
    };

    el.addEventListener('click', handleToggle);
    el.setAttribute('tabindex', '0');
    el.addEventListener('keydown', (e) => {
      if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        handleToggle();
      }
    });
  };

  // ======================================================
  // 🟦 TARJETAS
  // ======================================================

  // 🚗 Vehículos → usa vehiculos-grid
  registerCard({
    el: cardVehiculos,
    modeClass: null,
    gridType: 'vehiculos-grid',
    onOpen: () => {
      // Si venimos del calendario, ocultamos calendario y mostramos vehículos
      showVehiclesPanel();
    }
  });

  // 💰 Valor → usa vehiculos-grid2
  registerCard({
    el: cardValor,
    modeClass: 'modo-valor',
    gridType: 'vehiculos-grid2',
    onOpen: () => {
      showVehiclesPanel();
    }
  });

  // 📍 Kilómetros → usa vehiculos-grid2
  registerCard({
    el: cardKm,
    modeClass: 'modo-km',
    gridType: 'vehiculos-grid2',
    onOpen: () => {
      showVehiclesPanel();
    }
  });

  // 🧾 Gastos → usa vehiculos-grid2
  registerCard({
    el: cardGastos,
    modeClass: 'modo-gastos',
    gridType: 'vehiculos-grid2',
    onOpen: () => {
      showVehiclesPanel();
    }
  });

  // 📅 Calendario → muestra el panel calendario y oculta mis vehículos
  registerCard({
    el: cardCalendario,
    modeClass: 'modo-calendario',
    gridType: 'vehiculos-grid2', // realmente se oculta la sección, pero no molesta
    onOpen: () => {
      showCalendarPanel();
    }
  });

})();
