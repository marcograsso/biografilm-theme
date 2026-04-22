import Swiper from "swiper";
import { Navigation, Pagination, Keyboard, A11y, Autoplay } from "swiper/modules";
import gsap from "gsap";

import "swiper/css";
import "swiper/css/navigation";
import "swiper/css/pagination";
import "swiper/css/a11y";
import "./hero-carousel.css";

document.addEventListener("DOMContentLoaded", () => {
  Array.from(document.querySelectorAll(".hero-carousel")).forEach(
    (carousel) => {
      new Swiper(carousel, {
        modules: [Navigation, Pagination, Keyboard, A11y, Autoplay],
        loop: true,
        autoplay: {
          delay: 5000,
          disableOnInteraction: false,
        },
        speed: 800,
        keyboard: {
          enabled: true,
        },
        a11y: {
          enabled: true,
        },
        pagination: {
          el: ".swiper-pagination",
          clickable: true,
        },
        navigation: {
          nextEl: ".swiper-button-next",
          prevEl: ".swiper-button-prev",
        },
        on: {
          init(swiper) {
            const activeSlide = swiper.slides[swiper.activeIndex];
            const textPanel = activeSlide.querySelector(".glass-effect");

            // Slide text panel in from left
            if (textPanel) {
              gsap.fromTo(
                textPanel,
                { x: -80 },
                { x: 0, duration: 1.2, delay: 0.2, ease: "power3.out" },
              );
            }
          },
        },
      });

    },
  );
});
