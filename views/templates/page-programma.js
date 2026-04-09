// Hide header when filters become sticky, show when they return to natural position
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

const italianDays = ['DOM', 'LUN', 'MAR', 'MER', 'GIO', 'VEN', 'SAB'];

function formatDayTab(text) {
    const raw = text.trim();

    let date;
    if (/^\d{8}$/.test(raw)) {
        // Ymd stored format: "20250618"
        date = new Date(`${raw.slice(0, 4)}-${raw.slice(4, 6)}-${raw.slice(6, 8)}`);
    } else {
        date = new Date(raw);
    }

    if (isNaN(date.getTime())) return raw;

    return `${italianDays[date.getDay()]} ${date.getDate()}`;
}

function getFirstDayValue() {
    const first = document.querySelector('.facetwp-facet-days .facetwp-radio');
    return first ? first.getAttribute('data-value') : null;
}

document.addEventListener('facetwp-loaded', function () {
    const facet = document.querySelector('.facetwp-facet-days');
    if (!facet) return;

    // Register reset hook once: restore days to its first value instead of clearing
    if (!FWP.loaded) {
        FWP.hooks.addAction('facetwp/reset', function () {
            const firstValue = getFirstDayValue();
            if (firstValue) {
                FWP.facets['days'] = [firstValue];
            }
        });
    }

    // Remove disabled state from day tabs (FacetWP disables tabs that would yield 0 results)
    facet.querySelectorAll('.facetwp-radio.disabled').forEach(radio => {
        radio.classList.remove('disabled');
    });

    // Reformat labels on every reload
    facet.querySelectorAll('.facetwp-radio').forEach(radio => {
        radio.textContent = formatDayTab(radio.textContent);
    });

    // Re-attach on every reload (FacetWP re-renders the facet HTML each time)
    // Use capture phase to intercept before FacetWP's own listener
    facet.querySelectorAll('.facetwp-radio').forEach(radio => {
        radio.addEventListener('click', function (e) {
            if (this.classList.contains('checked')) {
                e.stopImmediatePropagation();
            }
        }, { capture: true });
    });
});
