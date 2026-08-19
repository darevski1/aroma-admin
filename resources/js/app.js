import Toastify from "toastify-js";
import "toastify-js/src/toastify.css";
import "./main.js";
import "animate.css";
import "./users.js";
import "./custom.js";
import Swiper from "swiper";
import "swiper/css";
import "swiper/css/navigation";
import "swiper/css/pagination";
import { Autoplay, Navigation, Pagination } from "swiper/modules";
import "preline";
import "flowbite";

const swiper = new Swiper(".swiper:not(.garminSwiper)", {
    modules: [Autoplay],
    loop: true,
    autoplay: {
        delay: 3000,
    },
});

const garminSwiper = new Swiper(".garminSwiper", {
    modules: [Autoplay, Navigation, Pagination],
    loop: true,
    speed: 650,
    grabCursor: true,
    watchSlidesProgress: true,
    spaceBetween: 18,
    autoplay: {
        delay: 4200,
        disableOnInteraction: false,
        pauseOnMouseEnter: true,
    },
    pagination: {
        el: ".garmin-showcase__pagination",
        clickable: true,
    },
    navigation: {
        nextEl: ".garmin-showcase__arrow--next",
        prevEl: ".garmin-showcase__arrow--prev",
    },
    breakpoints: {
        0: {
            slidesPerView: 1,
            spaceBetween: 18,
        },
        640: {
            slidesPerView: 1,
            spaceBetween: 20,
        },
        900: {
            slidesPerView: 1,
            spaceBetween: 24,
        },
        1280: {
            slidesPerView: 1,
            spaceBetween: 28,
        },
    },
});

const portfolioTestimonials = new Swiper(".portfolioTestimonials", {
    modules: [Autoplay, Pagination],
    loop: true,
    speed: 650,
    spaceBetween: 20,
    autoplay: {
        delay: 4500,
        disableOnInteraction: false,
        pauseOnMouseEnter: true,
    },
    pagination: {
        el: ".portfolio-testimonials__pagination",
        clickable: true,
    },
    breakpoints: {
        0: {
            slidesPerView: 1,
        },
        900: {
            slidesPerView: 2,
        },
    },
});
