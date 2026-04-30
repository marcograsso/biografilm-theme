import Swiper from "swiper";
import { Navigation, Pagination, A11y } from "swiper/modules";

import "swiper/css";
import "swiper/css/navigation";
import "swiper/css/pagination";
import "swiper/css/a11y";
import "./contents-displayer.css";

document.addEventListener("DOMContentLoaded", () => {
  Array.from(document.querySelectorAll(".contents-displayer-swiper")).forEach(
    (el) => {
      const cols = parseInt(el.dataset.cols) || 4;
      const breakpoints =
        cols === 3
          ? { 640: { slidesPerView: 2 }, 1024: { slidesPerView: 3 } }
          : {
              640: { slidesPerView: 2 },
              1024: { slidesPerView: 3 },
              1280: { slidesPerView: 4 },
            };
      new Swiper(el, {
        modules: [Navigation, Pagination, A11y],
        slidesPerView: 1.1,
        speed: 600,
        breakpoints,
        a11y: { enabled: true },
        pagination: {
          el: el.querySelector(".swiper-pagination"),
          clickable: true,
        },
        navigation: {
          nextEl: el.querySelector(".swiper-button-next"),
          prevEl: el.querySelector(".swiper-button-prev"),
        },
      });
    },
  );
});
