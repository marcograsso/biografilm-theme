
import Alpine from "alpinejs";

// Auto-click .facetwp-load-more when it enters the viewport, with stagger animation on new cards
let loadMoreBusy = false;

function cloneSkeleton(id) {
  const tpl = document.getElementById(id);
  if (!tpl) return null;
  return tpl.content.firstElementChild.cloneNode(true);
}

const loadingIndicator = document.createElement("div");
loadingIndicator.className = "hidden";

document.addEventListener("DOMContentLoaded", () => {
  const template = document.querySelector(".facetwp-template");
  if (!template) return;
  if (document.querySelector("[data-search-card]")) {
    for (let i = 0; i < 6; i++) {
      const card = cloneSkeleton("tpl-skeleton-search-card");
      if (card) loadingIndicator.appendChild(card);
    }
    template.after(loadingIndicator);
  }
});

function showFilmSkeletons() {
  const grid = document.querySelector(".facetwp-template > div");
  if (!grid) return;
  grid.querySelectorAll("[data-film-filler]").forEach((el) => el.remove());
  const compact = grid.hasAttribute("data-compact-cards");
  const tplId = compact ? "tpl-skeleton-film-card-compact" : "tpl-skeleton-film-card";
  for (let i = 0; i < 6; i++) {
    const card = cloneSkeleton(tplId);
    if (!card) return;
    card.dataset.filmSkeleton = "";
    if (i === 5) card.style.borderRight = "1px solid var(--color-stroke)";
    grid.appendChild(card);
  }
}

function checkLoadMore() {
  if (loadMoreBusy) return;
  if (document.querySelector(".fs-wrap.fs-open")) return;
  const btn = document.querySelector(".facetwp-load-more");
  if (!btn || btn.classList.contains("facetwp-hidden")) return;

  const rect = btn.getBoundingClientRect();
  if (rect.top < window.innerHeight) {
    loadMoreBusy = true;
    if (document.querySelector("[data-search-card]")) {
      loadingIndicator.classList.remove("hidden");
    } else if (document.querySelector("[data-programma-grid]")) {
      showProgrammaSkeletons();
    } else if (document.querySelector("[data-film-card]")) {
      showFilmSkeletons();
    }
    setTimeout(() => {
      if (document.querySelector(".fs-wrap.fs-open")) {
        loadMoreBusy = false;
        return;
      }
      btn.click();
    }, 1800);
  }
}

function makeFilmFillers(count) {
  const f = (cls) => `<div data-film-filler class="${cls}"></div>`;
  let html = "";
  if (count % 2 === 1) html += f("border-stroke col-span-1 border-l xl:hidden");
  if (count % 3 === 1) {
    html += f("border-stroke 3xl:hidden col-span-1 hidden border-l xl:block");
    html += f("3xl:hidden col-span-1 hidden xl:block");
  } else if (count % 3 === 2) {
    html += f("border-stroke 3xl:hidden col-span-1 hidden border-l xl:block");
  }
  if (count % 4 === 1) {
    html += f("border-stroke 3xl:block col-span-1 hidden border-l");
    html += f("3xl:block col-span-1 hidden");
    html += f("3xl:block col-span-1 hidden");
  } else if (count % 4 === 2) {
    html += f("border-stroke 3xl:block col-span-1 hidden border-l");
    html += f("3xl:block col-span-1 hidden");
  } else if (count % 4 === 3) {
    html += f("border-stroke 3xl:block col-span-1 hidden border-l");
  }
  return html;
}

function showProgrammaSkeletons() {
  const grid = document.querySelector("[data-programma-grid]");
  if (!grid) return;

  const group = document.createElement("div");
  group.dataset.programmaSkeletons = "";
  group.className = "skeleton-program-group";

  const hourCol = document.createElement("div");
  hourCol.className = "skeleton-hour";
  group.appendChild(hourCol);

  const itemsCol = document.createElement("div");
  itemsCol.className = "min-w-0 flex-1";

  for (let i = 0; i < 6; i++) {
    const card = cloneSkeleton("tpl-skeleton-program-card");
    if (card) itemsCol.appendChild(card);
  }

  group.appendChild(itemsCol);
  grid.appendChild(group);
}

function mergeProgrammaGrid() {
  const template = document.querySelector(".facetwp-template");
  if (!template) return null;
  const containers = template.querySelectorAll(":scope > [data-programma-grid]");
  if (containers.length < 2) return null;

  const original = containers[0];
  const skeletonGroup = original.querySelector("[data-programma-skeletons]");

  containers.forEach((container, i) => {
    if (i === 0) return;
    container.querySelectorAll(":scope > div").forEach((newGroup) => {
      newGroup.classList.remove("border-t", "border-t-stroke");

      const hourEl = newGroup.querySelector("[data-sticky-hour] h3");
      const hourLabel = hourEl ? hourEl.textContent.trim() : null;

      let matched = false;
      if (hourLabel) {
        original.querySelectorAll(":scope > div").forEach((origGroup) => {
          if (matched) return;
          const origHourEl = origGroup.querySelector("[data-sticky-hour] h3");
          if (origHourEl && origHourEl.textContent.trim() === hourLabel) {
            const origItems = origGroup.querySelector(".min-w-0.flex-1");
            const newItemsCol = newGroup.querySelector(".min-w-0.flex-1");
            if (origItems && newItemsCol) {
              Array.from(newItemsCol.children).forEach((item) => origItems.appendChild(item));
            }
            matched = true;
          }
        });
      }

      if (!matched) original.appendChild(newGroup);
    });
    container.remove();
  });

  return { skeletonGroup };
}

function mergeFilmGrid() {
  if (!document.querySelector("[data-film-card]")) return null;
  const template = document.querySelector(".facetwp-template");
  if (!template) return null;
  const grids = template.querySelectorAll(":scope > div");
  if (grids.length < 2) return null;

  const original = grids[0];
  const skeletons = Array.from(original.querySelectorAll("[data-film-skeleton]"));
  original.querySelectorAll("[data-film-filler]").forEach((el) => el.remove());

  const newCards = [];
  grids.forEach((grid, i) => {
    if (i === 0) return;
    grid.querySelectorAll("[data-film-card]").forEach((card) => newCards.push(card));
    grid.remove();
  });

  // Put each new card exactly where its skeleton was — blur handles the visual reveal
  newCards.forEach((card, i) => {
    if (skeletons[i]) {
      skeletons[i].replaceWith(card);
    } else {
      original.appendChild(card);
    }
  });
  // Remove any leftover skeletons (more skeletons than new cards)
  skeletons.slice(newCards.length).forEach((sk) => sk.remove());

  return { original, newCards };
}

document.addEventListener("scroll", checkLoadMore, { passive: true });
// Block checkLoadMore for the entire FacetWP AJAX cycle (initial load, filters,
// sort). ScrollTrigger.refresh() in animations.js fires scroll events inside the
// facetwp-loaded handlers; without this guard those events would spuriously
// trigger load-more. loadMoreBusy is reset via setTimeout(0) in facetwp-loaded
// so the reset happens after all synchronous handlers (including refresh) finish.
document.addEventListener("facetwp-refresh", () => { loadMoreBusy = true; });
document.addEventListener("facetwp-loaded", () => {
  const filmResult = mergeFilmGrid();
  const programmaResult = mergeProgrammaGrid();
  loadingIndicator.classList.add("hidden");

  // Film / compact cards: add fillers — blur-in cascade handles the visual reveal
  if (filmResult) {
    const { original } = filmResult;
    original.querySelectorAll("[data-film-filler]").forEach((el) => el.remove());
    const total = original.querySelectorAll("[data-film-card]").length;
    original.insertAdjacentHTML("beforeend", makeFilmFillers(total));
  }

  // Programma: fade out skeleton group — blur-in cascade handles new items
  if (programmaResult) {
    const { skeletonGroup } = programmaResult;
    if (skeletonGroup) {
      skeletonGroup.style.transition = "opacity 0.4s ease";
      skeletonGroup.style.opacity = "0";
      setTimeout(() => skeletonGroup.remove(), 400);
    }
  }

  // Delay so ScrollTrigger.refresh() in animations.js (also on facetwp-loaded)
  // fires its internal scroll events while loadMoreBusy is still true, preventing
  // checkLoadMore from auto-triggering after every FacetWP AJAX refresh.
  setTimeout(() => { loadMoreBusy = false; }, 0);
});

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
