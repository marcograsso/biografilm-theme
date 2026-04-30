import Swiper from "swiper";
import { Navigation, Pagination, A11y } from "swiper/modules";

import "swiper/css";
import "swiper/css/navigation";
import "swiper/css/pagination";
import "swiper/css/a11y";
import "./whoscoming-displayer.css";

document.addEventListener("DOMContentLoaded", () => {
  Array.from(
    document.querySelectorAll(".whoscoming-displayer-swiper"),
  ).forEach((el) => {
    new Swiper(el, {
      modules: [Navigation, Pagination, A11y],
      slidesPerView: 1.1,
      speed: 600,
      breakpoints: {
        640: { slidesPerView: 2 },
        1024: { slidesPerView: 3 },
        1280: { slidesPerView: 4 },
      },
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
  });
});
