const header = document.querySelector("body > div.fixed");
if (header) {
  let lastScrollY = 0;
  const threshold = window.innerHeight / 2;

  window.addEventListener(
    "scroll",
    () => {
      const currentScrollY = window.scrollY;

      if (currentScrollY < threshold) {
        header.style.transform = "translateY(0)";
      } else if (currentScrollY > lastScrollY) {
        header.style.transform = "translateY(-100%)";
      } else {
        header.style.transform = "translateY(0)";
      }

      lastScrollY = currentScrollY;
    },
    { passive: true },
  );
}
