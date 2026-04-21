import Swiper from "swiper";
import { Navigation, Pagination, Keyboard, A11y, Autoplay } from "swiper/modules";
import "swiper/css";
import "swiper/css/navigation";
import "swiper/css/pagination";
import "swiper/css/a11y";
import "../hero-carousel/hero-carousel.css";
import "../giurie/giurie.css";
import "./carousel.css";

document.addEventListener("DOMContentLoaded", () => {
  Array.from(document.querySelectorAll(".carousel-wrapper")).forEach((wrapper) => {
    const carousel = wrapper.querySelector(".carousel");
    const prevEl = wrapper.querySelector(".swiper-button-prev");
    const nextEl = wrapper.querySelector(".swiper-button-next");
    const paginationEl = wrapper.querySelector(".swiper-pagination");

    new Swiper(carousel, {
      modules: [Navigation, Pagination, Keyboard, A11y, Autoplay],
      loop: true,
      autoplay: {
        delay: 5000,
        disableOnInteraction: false,
      },
      speed: 800,
      keyboard: { enabled: true },
      a11y: { enabled: true },
      ...(paginationEl && {
        pagination: { el: paginationEl, clickable: true },
      }),
      ...(prevEl && nextEl && {
        navigation: { prevEl, nextEl },
      }),
    });
  });
});
