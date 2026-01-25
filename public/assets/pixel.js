document.addEventListener('DOMContentLoaded', () => {
    const toggle = document.querySelector('[data-dropdown-toggle="pixel-user-menu"]');
    const menu = document.getElementById('pixel-user-menu');

    if (!toggle || !menu) return;

    const close = () => {
        menu.classList.add('hidden');
        menu.setAttribute('aria-hidden', 'true');
        toggle.setAttribute('aria-expanded', 'false');
    };

    const open = () => {
        menu.classList.remove('hidden');
        menu.setAttribute('aria-hidden', 'false');
        toggle.setAttribute('aria-expanded', 'true');
    };

    toggle.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        if (menu.classList.contains('hidden')) open();
        else close();
    });

    document.addEventListener('click', () => close());
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') close();
    });
});

(function () {
  const root = document.getElementById('brand365');
  const current = document.getElementById('pxcalCurrent');
  const next = document.getElementById('pxcalNext');
  const curMonthEl = document.getElementById('pxcalCurrentMonth');
  const nextMonthEl = document.getElementById('pxcalNextMonth');

  if (!root || !current || !next || !curMonthEl || !nextMonthEl) return;

  const months = ['JAN','FEB','MAR','APR','MAY','JUN','JUL','AUG','SEP','OCT','NOV','DEC'];
  let idx = new Date().getMonth();

  function render() {
    curMonthEl.textContent = months[idx];
    nextMonthEl.textContent = months[(idx + 1) % 12];
  }

  let timer = null;
  const periodMs = 10000;
  const flipMs = 520;

  function flip() {
    if (current.classList.contains('is-flipping')) return;

    current.classList.add('is-flipping');

    // nach Ende: idx++ und "current" sofort wieder auf 0deg resetten (ohne sichtbare animation)
    window.setTimeout(() => {
      idx = (idx + 1) % 12;
      render();

      current.classList.remove('is-flipping');
      current.style.transition = 'none';
      current.style.transform = 'rotateX(0deg)';
      void current.offsetWidth; // reflow
      current.style.transition = '';
      current.style.transform = '';
    }, flipMs + 20);
  }

  function startTimer() {
    if (timer) clearInterval(timer);
    timer = setInterval(flip, periodMs);
  }

  root.addEventListener('mouseenter', () => {
    flip();
    startTimer();
  });

  render();
  startTimer();
})();
