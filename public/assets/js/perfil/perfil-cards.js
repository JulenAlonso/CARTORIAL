// ======================================================
// PERFIL-CARDS.JS — Top cards behavior + modes + calendar toggle
// ======================================================

(function () {
  // Main collapsible section with vehicle cards
  const section = document.getElementById('seccion-mis-vehiculos');

  // Container that holds the top summary cards
  const cardsContainer = document.querySelector('.cards');
  const topCards = cardsContainer ? Array.from(cardsContainer.querySelectorAll('.card')) : [];

  // Individual top cards
  const cardVehiculos  = document.getElementById('card-vehiculos-registrados');
  const cardValor      = document.getElementById('card-valor');
  const cardKm         = document.getElementById('card-km');
  const cardGastos     = document.getElementById('card-gastos');
  const cardCalendario = document.getElementById('card-calendario');

  // Big calendar panel
  const panelCalendario = document.getElementById('panel-calendario');

  // Currently active top card (the one that is "opened")
  let activeCardEl = null;

  // 🔹 Reset modes and grid layout on the main section
  const clearModes = () => {
    // Remove all mode classes from the section
    section?.classList.remove('modo-valor', 'modo-km', 'modo-gastos', 'modo-calendario');

    // Reset grid layout of vehicle cards
    const grid = section?.querySelector('.vehiculos-grid, .vehiculos-grid2');
    if (grid) {
      grid.classList.remove('vehiculos-grid', 'vehiculos-grid2');
      grid.classList.add('vehiculos-grid'); // default layout
    }
  };

  // 🔹 Show only one top card (hide the rest)
  const showOnly = (cardEl) => {
    topCards.forEach(c =>
      c === cardEl ? c.classList.remove('hidden') : c.classList.add('hidden')
    );
    activeCardEl = cardEl;
  };

  // 🔹 Show all top cards again
  const showAll = () => {
    topCards.forEach(c => c.classList.remove('hidden'));
    activeCardEl = null;
  };

  // 🔹 Open the main "Mis vehículos" section in a specific mode
  const openSection = (modeClass, gridType = 'vehiculos-grid') => {
    if (!section) return;

    section.classList.add('open');
    clearModes();

    // Apply mode class (valor / km / gastos / calendario)
    if (modeClass) section.classList.add(modeClass);

    // Switch between grid layouts (1-column vs 2-column, etc.)
    const grid = section.querySelector('.vehiculos-grid, .vehiculos-grid2');
    if (grid) {
      grid.classList.remove('vehiculos-grid', 'vehiculos-grid2');
      grid.classList.add(gridType);
    }
  };

  // 🔹 Show the big calendar panel and hide the vehicle section
  const showCalendarPanel = () => {
    if (section) section.style.display = 'none';

    if (panelCalendario) {
      panelCalendario.style.display = 'block';
      // Smooth scroll so the calendar is fully visible
      panelCalendario.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
  };

  // 🔹 Show the vehicle section and hide the big calendar panel
  const showVehiclesPanel = () => {
    if (panelCalendario) panelCalendario.style.display = 'none';
    if (section) section.style.display = 'block';
  };

  // 🔹 Register click / keyboard behavior for one top card
  const registerCard = ({ el, modeClass, gridType, onOpen }) => {
    if (!el) return;

    // Handles card toggle behavior when clicked / activated
    const handleToggle = () => {
      const isSameCard = activeCardEl === el;

      // If clicking the same active card → close section and reset everything
      if (isSameCard && section?.classList.contains('open')) {
        section.classList.remove('open');
        clearModes();
        showAll();
        // Always return to "Mis vehículos" view and hide calendar
        showVehiclesPanel();
        return;
      }

      // Open the section in the mode for this card
      openSection(modeClass, gridType);
      showOnly(el);

      // Optional callback when this card is opened (e.g., show calendar)
      if (typeof onOpen === 'function') onOpen();
    };

    // Mouse click
    el.addEventListener('click', handleToggle);

    // Make card focusable for keyboard users
    el.setAttribute('tabindex', '0');

    // Keyboard accessibility: Enter / Space also toggle the card
    el.addEventListener('keydown', (e) => {
      if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        handleToggle();
      }
    });
  };

  // ======================================================
  // 🟦 REGISTER CARDS
  // ======================================================

  // 🚗 Vehicles → keeps default grid (vehiculos-grid)
  registerCard({
    el: cardVehiculos,
    modeClass: null,
    gridType: 'vehiculos-grid',
    onOpen: () => {
      // If coming from calendar, hide calendar and show vehicle section again
      showVehiclesPanel();
    }
  });

  // 💰 Value → uses alternate grid (vehiculos-grid2)
  registerCard({
    el: cardValor,
    modeClass: 'modo-valor',
    gridType: 'vehiculos-grid2',
    onOpen: () => {
      showVehiclesPanel();
    }
  });

  // 📍 Kilometers → uses alternate grid (vehiculos-grid2)
  registerCard({
    el: cardKm,
    modeClass: 'modo-km',
    gridType: 'vehiculos-grid2',
    onOpen: () => {
      showVehiclesPanel();
    }
  });

  // 🧾 Expenses → uses alternate grid (vehiculos-grid2)
  registerCard({
    el: cardGastos,
    modeClass: 'modo-gastos',
    gridType: 'vehiculos-grid2',
    onOpen: () => {
      showVehiclesPanel();
    }
  });

  // 📅 Calendar → hide vehicle section and show big calendar panel
  registerCard({
    el: cardCalendario,
    modeClass: 'modo-calendario',
    gridType: 'vehiculos-grid2', // section is hidden anyway, so this is harmless
    onOpen: () => {
      showCalendarPanel();
    }
  });

})();
