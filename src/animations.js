import gsap from "gsap";
import { ScrollTrigger } from "gsap/ScrollTrigger";
import { ScrollSmoother } from "gsap/ScrollSmoother";

gsap.registerPlugin(ScrollTrigger, ScrollSmoother);

// Prevent mobile address-bar resize events from recalculating trigger positions
// (causes visual jumps on iOS/Android when the browser chrome hides/shows).
ScrollTrigger.config({ ignoreMobileResize: true });

document.addEventListener("DOMContentLoaded", () => {
  // normalizeScroll forces JS-driven scroll — great on desktop but fights native
  // momentum on touch-only devices and causes sluggish/unstable behaviour.
  const isTouch = ScrollTrigger.isTouch === 1;

  // Let native scroll work inside FaceWP option lists (and any overflow container
  // that needs its own scroll). Must be registered before ScrollSmoother.create()
  // so it runs first in the capture phase and stops GSAP from seeing the event.
  document.addEventListener(
    "wheel",
    (e) => {
      if (e.target.closest(".fs-options, .scroll-x")) e.stopImmediatePropagation();
    },
    { capture: true, passive: false },
  );

  const smoother = ScrollSmoother.create({
    wrapper: "#smooth-wrapper",
    content: "#smooth-content",
    smooth: 1,
    effects: true,
    normalizeScroll: !isTouch,
  });


  // Smooth-scroll to anchor links via ScrollSmoother instead of native jump.
  document.addEventListener("click", (e) => {
    const link = e.target.closest('a[href^="#"]');
    if (!link) return;
    const id = link.getAttribute("href").slice(1);
    if (!id) return;
    const target = document.getElementById(id);
    if (!target) return;
    e.preventDefault();
    const offset = headerEl ? headerEl.offsetHeight : 0;
    smoother.scrollTo(target, true, `top ${offset}px`);
  });

  // Date/location badge slides in from the left on load — homepage only.
  const dateline = document.querySelector(".header-dateline");
  if (dateline && document.body.classList.contains("home")) {
    gsap.fromTo(
      dateline.children,
      { x: -24, opacity: 0 },
      { x: 0, opacity: 1, duration: 0.7, delay: 0.2, ease: "power3.out", stagger: 0.15 },
    );
  }

  // Header pushed up by filter bar (archive-film and programma pages only).
  // Replaces the native window.scroll + getBoundingClientRect() approach, which
  // lags behind ScrollSmoother's RAF loop and produces jitter.
  const filterSentinel = document.getElementById("filters-sentinel");
  const headerEl = document.querySelector("body > div.fixed");
  if (headerEl) headerEl.style.transition = "none";

  if (filterSentinel && headerEl) {
    // Filter pages: header is pushed up by the sticky filter bar
    const headerHeight = headerEl.offsetHeight;
    gsap.to(headerEl, {
      y: -headerHeight,
      ease: "none",
      scrollTrigger: {
        trigger: filterSentinel,
        start: `top ${headerHeight}px`,
        end: "top top",
        scrub: true,
      },
    });
  } else if (headerEl) {
    // All other pages: hide on scroll down, show on scroll up.
    // Uses smoother.scrollTop() so the position is always in sync with
    // ScrollSmoother's virtual scroll, not the lagged native scrollY.
    const whiteBar = headerEl.firstElementChild;
    const hideOffset = whiteBar ? whiteBar.offsetHeight : headerEl.offsetHeight;
    const threshold = 150;
    let lastScrollY = 0;
    let headerVisible = true;

    gsap.ticker.add(() => {
      const currentScrollY = smoother.scrollTop();
      if (currentScrollY <= threshold) {
        if (!headerVisible) {
          headerVisible = true;
          gsap.to(headerEl, { y: 0, duration: 0.35, ease: "power3.out", overwrite: "auto" });
        }
      } else if (currentScrollY > lastScrollY && headerVisible) {
        headerVisible = false;
        gsap.to(headerEl, { y: -hideOffset, duration: 0.25, ease: "power3.in", overwrite: "auto" });
      } else if (currentScrollY < lastScrollY && !headerVisible) {
        headerVisible = true;
        gsap.to(headerEl, { y: 0, duration: 0.35, ease: "power3.out", overwrite: "auto" });
      }
      lastScrollY = currentScrollY;
    });
  }

  // Replace CSS sticky with ScrollTrigger.pin — required inside ScrollSmoother
  // because transforms on #smooth-content break position:sticky.
  // On touch devices normalizeScroll is off (native scroll), so CSS sticky works
  // and is preferable — JS pin lags behind native momentum causing jitter.
  gsap.utils.toArray("[data-sticky-top]").forEach((el) => {
    if (isTouch) {
      el.style.position = "sticky";
      el.style.top = "0";
      return;
    }
    ScrollTrigger.create({
      trigger: el,
      start: "top top",
      pin: true,
      pinSpacing: false,
      end: () => `+=${document.body.scrollHeight}`,
    });
  });

  // Hour labels in the programma grid — pin each to top:48px (below the filter bar)
  // within its own row container, mirroring sticky top-12
  function pinHourLabels() {
    if (isTouch) {
      gsap.utils.toArray("[data-sticky-hour]").forEach((el) => {
        el.style.position = "sticky";
        el.style.top = "48px";
      });
      return;
    }

    ScrollTrigger.getAll()
      .filter((st) => st.vars.id === "hour-label")
      .forEach((st) => st.kill());

    gsap.utils.toArray("[data-sticky-hour]").forEach((el) => {
      if (!el.offsetParent) return; // skip elements inside display:none sections
      const row = el.parentElement.parentElement; // column → flex row
      ScrollTrigger.create({
        id: "hour-label",
        trigger: el,
        start: "top 48px",
        endTrigger: row,
        // Unpin when the row's bottom reaches (48px + label height) from the top,
        // so the label never visually exits the bottom of its container
        end: () => `bottom ${48 + el.offsetHeight}px`,
        pin: true,
        pinSpacing: false,
      });
    });
  }

  pinHourLabels();

  // Re-pin after every FacetWP refresh (results are re-rendered)
  document.addEventListener("facetwp-loaded", () => {
    ScrollTrigger.refresh();
    pinHourLabels();
    initBlurEffects(true);
  });

  // Re-pin when the doc-program day tab changes (hidden sections have no dimensions at load time)
  window.addEventListener("doc-day-changed", () => {
    ScrollTrigger.refresh();
    pinHourLabels();
  });

  function initBlurEffects(staggerInView = false) {
    if (isTouch) {
      // On mobile, skip blur effects entirely — clear initial state immediately.
      gsap.utils.toArray(".blur-in").forEach((el) => {
        el.classList.remove("blur-in");
        gsap.set(el, { clearProps: "filter" });
      });
      gsap.utils.toArray(".overlay-blur").forEach((el) => el.remove());
      return;
    }

    // Kill any stale blur ScrollTriggers left over from a previous FacetWP render.
    ScrollTrigger.getAll()
      .filter((st) => st.vars._blurEffect)
      .forEach((st) => st.kill());

    gsap.utils.toArray(".overlay-blur").forEach((el) => {
      ScrollTrigger.create({
        _blurEffect: true,
        trigger: el,
        start: "top 99%",
        once: true,
        onEnter: () => {
          gsap.fromTo(
            el,
            { backdropFilter: "blur(50px)" },
            {
              backdropFilter: "blur(0px)",
              duration: 1,
              ease: "power3.out",
              onComplete: () => el.remove(),
            },
          );
        },
      });
    });

    const blurEls = gsap.utils.toArray(".blur-in");
    const inView = [];
    const belowFold = [];

    blurEls.forEach((el) => {
      if (el.getBoundingClientRect().top < window.innerHeight * 0.97) {
        inView.push(el);
      } else {
        belowFold.push(el);
      }
    });

    // Already-visible elements: animate in together, or with stagger for dynamically loaded cards.
    if (inView.length) {
      gsap.to(inView, {
        filter: "blur(0px)",
        duration: 1,
        ease: "power4.out",
        ...(staggerInView && { stagger: 0.05 }),
        onComplete() {
          this.targets().forEach((el) => {
            el.classList.remove("blur-in");
            gsap.set(el, { clearProps: "filter" });
          });
        },
      });
    }

    // Below-fold elements: trigger individually on scroll.
    belowFold.forEach((el) => {
      ScrollTrigger.create({
        _blurEffect: true,
        trigger: el,
        start: "top 99%",
        once: true,
        onEnter: () => {
          gsap.to(el, {
            filter: "blur(0px)",
            duration: 1,
            ease: "power4.out",
            onComplete: () => {
              el.classList.remove("blur-in");
              gsap.set(el, { clearProps: "filter" });
            },
          });
        },
      });
    });
  }

  initBlurEffects();

  // Images (e.g. footer image) load after DOMContentLoaded and make the page
  // taller than GSAP's initial measurement. Refresh once everything is loaded
  // so the scroll distance includes the full content height.
  window.addEventListener("load", () => ScrollTrigger.refresh());
});
