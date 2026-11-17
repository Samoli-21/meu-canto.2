(() => {
  const button = document.querySelector('[data-menu-toggle]');
  const nav = document.querySelector('[data-navigation]');
  if (button && nav) {
    button.addEventListener('click', () => {
      const open = button.getAttribute('aria-expanded') === 'true';
      button.setAttribute('aria-expanded', String(!open));
      nav.classList.toggle('is-open', !open);
      const label = button.querySelector('.sr-only');
      if (label) label.textContent = open ? 'Abrir menu' : 'Fechar menu';
    });
    nav.addEventListener('click', event => {
      if (event.target.closest('a')) {
        button.setAttribute('aria-expanded', 'false');
        nav.classList.remove('is-open');
        const label = button.querySelector('.sr-only');
        if (label) label.textContent = 'Abrir menu';
      }
    });
    document.addEventListener('keydown', event => {
      if (event.key === 'Escape') {
        button.setAttribute('aria-expanded', 'false');
        nav.classList.remove('is-open');
        button.focus();
      }
    });
  }
  const search = document.querySelector('.search-popover');
  if (search) document.addEventListener('click', event => { if (!search.contains(event.target)) search.removeAttribute('open'); });
})();

/* Refinamentos: sombra do cabeçalho ao rolar e entrada suave dos blocos. Respeita prefers-reduced-motion. */
(() => {
  const header = document.querySelector('.site-header');
  if (header) {
    const onScroll = () => header.classList.toggle('is-scrolled', window.scrollY > 8);
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
  }
  const reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  if (reduce || !('IntersectionObserver' in window)) return;
  document.documentElement.classList.add('js');
  const targets = document.querySelectorAll(
    '.section-heading, .feeling-card, .area-card, .resource-card, .assistant-panel, .support-grid > *, ' +
    '.work-feature, .about-layout > *, .language-inner > *, .directory-layout > *');
  const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      if (!entry.isIntersecting) return;
      const el = entry.target;
      observer.unobserve(el);
      el.classList.add('is-visible');
      // Devolve o elemento ao estilo original depois da animação (não interfere no hover).
      setTimeout(() => { el.classList.remove('reveal', 'is-visible'); el.style.transitionDelay = ''; }, 900);
    });
  }, { threshold: 0.12, rootMargin: '0px 0px -6% 0px' });
  targets.forEach((el) => {
    const siblings = el.parentElement ? Array.from(el.parentElement.children) : [el];
    el.style.transitionDelay = Math.min(siblings.indexOf(el), 5) * 70 + 'ms';
    el.classList.add('reveal');
    observer.observe(el);
  });
})();

/* Transição curta entre páginas internas; desactivada para quem prefere movimento reduzido. */
(() => {
  const reduced = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  const curtain = document.querySelector('.page-transition-curtain');
  if (reduced || !curtain) return;

  window.addEventListener('pageshow', () => {
    document.body.classList.add('page-is-ready');
    document.body.classList.remove('page-is-leaving');
  });

  document.addEventListener('click', (event) => {
    const link = event.target.closest('a[href]');
    if (!link || event.defaultPrevented || event.button !== 0) return;
    if (event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return;
    if (link.target === '_blank' || link.hasAttribute('download')) return;
    const destination = new URL(link.href, window.location.href);
    if (destination.origin !== window.location.origin) return;
    if (destination.pathname === window.location.pathname && destination.search === window.location.search) return;
    if (destination.protocol !== 'http:' && destination.protocol !== 'https:') return;
    event.preventDefault();
    document.body.classList.add('page-is-leaving');
    window.setTimeout(() => { window.location.href = destination.href; }, 170);
  });
})();
