
import Alpine from "alpinejs";

// Re-initialize Alpine on elements injected by FacetWP AJAX refreshes
document.addEventListener("facetwp-loaded", () => {
  document.querySelectorAll(".facetwp-facet [x-data]").forEach((el) => {
    Alpine.initTree(el);
  });

  syncFSelectsFromFWPState();
  lockFsLabels();
});

// After each AJAX refresh, FWP.facets holds the values that were sent to the server.
// We force-sync the native (hidden) <select> elements to match, so the next
// parseFacets() call reads the correct values for ALL facets — not just the one
// the user just interacted with.
function syncFSelectsFromFWPState() {
  if (typeof FWP === "undefined" || !FWP.facets) return;

  document.querySelectorAll(".facetwp-type-fselect").forEach((facetEl) => {
    const facetName = facetEl.getAttribute("data-name");
    const selected = FWP.facets[facetName];
    const select = facetEl.querySelector("select");
    if (!select) return;

    if (!selected || selected.length === 0) return;

    // Set the matching option(s) as selected on the native element
    Array.from(select.options).forEach((opt) => {
      opt.selected = selected.includes(opt.value);
    });
  });
}

// Lock fs-label to always show the facet label from FWP settings
function getFacetLabel(facetEl) {
  const facetName = facetEl.getAttribute("data-name");
  if (typeof FWP !== "undefined" && FWP.settings && FWP.settings.labels) {
    return FWP.settings.labels[facetName] || facetName;
  }
  return facetName;
}

function lockFsLabels() {
  document.querySelectorAll(".facetwp-type-fselect").forEach((facetEl) => {
    const wrap = facetEl.querySelector(".fs-wrap");
    const label = facetEl.querySelector(".fs-label");
    if (!wrap || !label) return;

    if (!wrap.dataset.facetLabel) {
      wrap.dataset.facetLabel = getFacetLabel(facetEl);
    }

    label.textContent = wrap.dataset.facetLabel;
  });
}

function updateSelectionDots() {
  document.querySelectorAll(".facetwp-type-fselect").forEach((facetEl) => {
    const wrap = facetEl.querySelector(".fs-wrap");
    if (!wrap) return;
    const hasSelection = wrap.querySelectorAll(".fs-option.selected").length > 0;
    wrap.classList.toggle("has-selection", hasSelection);
  });
}

// Delegate clicks on any .fs-option so the dot updates immediately on interaction
document.addEventListener("click", (e) => {
  if (e.target.closest(".fs-option")) {
    requestAnimationFrame(updateSelectionDots);
  }
});

function formatDayLabels() {
  const italianDays = ["DOM", "LUN", "MAR", "MER", "GIO", "VEN", "SAB"];
  document
    .querySelectorAll(".facetwp-type-radio .facetwp-radio[data-value]")
    .forEach((el) => {
      const val = el.getAttribute("data-value");
      if (!/^\d{4}-\d{2}-\d{2}$/.test(val)) return;
      const [y, m, d] = val.split("-").map(Number);
      const date = new Date(y, m - 1, d); // local constructor avoids UTC midnight offset
      el.textContent = italianDays[date.getDay()] + " " + d;
    });
}

document.addEventListener("DOMContentLoaded", () => { lockFsLabels(); updateSelectionDots(); formatDayLabels(); });
document.addEventListener("facetwp-loaded", () => { lockFsLabels(); updateSelectionDots(); formatDayLabels(); });
document.addEventListener("fs:changed", () => { lockFsLabels(); updateSelectionDots(); });
