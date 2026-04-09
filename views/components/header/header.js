const header = document.querySelector("body > div.fixed");
const skipScroll =
  document.body.classList.contains("page-programma") ||
  document.body.classList.contains("post-type-archive-film");

if (header && !skipScroll) {
  const whiteBar = header.firstElementChild;
  const threshold = 150;
  let lastScrollY = 0;

  window.addEventListener(
    "scroll",
    () => {
      const currentScrollY = window.scrollY;
      const offset = whiteBar.offsetHeight;

      if (currentScrollY <= threshold) {
        header.style.transform = "translateY(0)";
      } else if (currentScrollY > lastScrollY) {
        header.style.transform = `translateY(-${offset}px)`;
      } else {
        header.style.transform = "translateY(0)";
      }

      lastScrollY = currentScrollY;
    },
    { passive: true },
  );
}
