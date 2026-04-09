const sentinel = document.getElementById('filters-sentinel');
const header = document.querySelector('body > div.fixed');

if (sentinel && header) {
  header.style.transition = 'none';
  const headerHeight = header.offsetHeight;

  window.addEventListener('scroll', () => {
    const filtersTop = sentinel.getBoundingClientRect().top;
    const push = Math.min(Math.max(headerHeight - filtersTop, 0), headerHeight);
    header.style.transform = `translateY(-${push}px)`;
  }, { passive: true });
}
