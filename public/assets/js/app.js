(() => {
  const root = document.documentElement;
  const toggle = document.getElementById('themeToggle');
  const label = document.getElementById('themeLabel');
  const icon = document.getElementById('themeIcon');

  function applyTheme(theme) {
    root.dataset.theme = theme;
    if (label) label.textContent = theme === 'dark' ? 'Dark Mode' : 'Light Mode';
    if (icon) icon.textContent = theme === 'dark' ? '☾' : '☼';
    if (toggle) toggle.setAttribute('aria-label', `Current theme: ${theme} mode. Switch theme`);
  }
  applyTheme(root.dataset.theme === 'dark' ? 'dark' : 'light');
  toggle?.addEventListener('click', () => {
    const next = root.dataset.theme === 'dark' ? 'light' : 'dark';
    applyTheme(next);
    try { localStorage.setItem('studyflow-theme', next); } catch (_) {}
  });

  const menu = document.getElementById('menuToggle');
  const sidebar = document.getElementById('sidebar');
  const scrim = document.getElementById('mobileScrim');
  const closeMenu = () => { sidebar?.classList.remove('sidebar-open'); scrim?.classList.remove('scrim-visible'); };
  menu?.addEventListener('click', () => { sidebar?.classList.add('sidebar-open'); scrim?.classList.add('scrim-visible'); });
  scrim?.addEventListener('click', closeMenu);

  document.querySelectorAll('.toast-close').forEach(btn => btn.addEventListener('click', () => btn.closest('.toast')?.remove()));
  document.querySelectorAll('[data-confirm]').forEach(el => el.addEventListener('click', event => {
    if (!window.confirm(el.dataset.confirm || 'Are you sure?')) event.preventDefault();
  }));

  document.querySelectorAll('[data-progress]').forEach(el => {
    const target = Math.max(0, Math.min(100, Number(el.dataset.progress) || 0));
    requestAnimationFrame(() => { el.style.width = `${target}%`; });
  });

  document.querySelectorAll('form[data-validate]').forEach(form => {
    form.addEventListener('submit', event => {
      let valid = true;
      form.querySelectorAll('[required]').forEach(input => {
        const empty = !String(input.value || '').trim();
        input.classList.toggle('input-invalid', empty);
        if (empty) valid = false;
      });
      if (!valid) {
        event.preventDefault();
        form.querySelector('.input-invalid')?.focus();
      }
    });
    form.querySelectorAll('input, select, textarea').forEach(input => input.addEventListener('input', () => input.classList.remove('input-invalid')));
  });

  const search = document.querySelector('[data-task-search]');
  const filter = document.querySelector('[data-task-filter]');
  const taskRows = [...document.querySelectorAll('[data-task-row]')];
  function filterTasks() {
    const query = (search?.value || '').toLowerCase().trim();
    const status = filter?.value || 'all';
    taskRows.forEach(row => {
      const matchesText = row.textContent.toLowerCase().includes(query);
      const matchesStatus = status === 'all' || row.dataset.status === status;
      row.hidden = !(matchesText && matchesStatus);
    });
    const empty = document.getElementById('filterEmpty');
    if (empty) empty.hidden = taskRows.some(row => !row.hidden);
  }
  search?.addEventListener('input', filterTasks);
  filter?.addEventListener('change', filterTasks);

  document.querySelectorAll('.reveal').forEach((el, i) => {
    el.style.animationDelay = `${Math.min(i * 45, 240)}ms`;
  });
})();
