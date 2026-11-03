import Toastify from "toastify-js";
import "toastify-js/src/toastify.css";
import "./main.js";
import "animate.css";
import "./users.js";
import "./custom.js";
import "swiper/css";
import "swiper/css/navigation";
import "swiper/css/pagination";
import { Autoplay, Navigation, Pagination } from "swiper/modules";
import "preline";
import "flowbite";

document.querySelectorAll("[data-garmin-carousel]").forEach((carousel) => {
    const track = carousel.querySelector("[data-garmin-track]");
    const cards = Array.from(carousel.querySelectorAll("[data-garmin-slide]"));
    const previous = carousel.querySelector("[data-garmin-prev]");
    const next = carousel.querySelector("[data-garmin-next]");
    const count = carousel.querySelector("[data-garmin-count]");
    const progress = carousel.querySelector("[data-garmin-progress]");

    if (!track || cards.length === 0 || !previous || !next) return;

    let activeIndex = 0;
    let scrollTimer;

    const update = () => {
        const maxScroll = Math.max(1, track.scrollWidth - track.clientWidth);
        const currentScroll = track.scrollLeft;
        const nearest = cards.reduce((bestIndex, card, index) => {
            const distance = Math.abs(card.offsetLeft - track.offsetLeft - currentScroll);
            const bestDistance = Math.abs(cards[bestIndex].offsetLeft - track.offsetLeft - currentScroll);
            return distance < bestDistance ? index : bestIndex;
        }, 0);

        activeIndex = nearest;
        previous.disabled = currentScroll <= 2;
        next.disabled = currentScroll >= maxScroll - 2;
        if (count) {
            const current = String(Math.min(cards.length, activeIndex + 1)).padStart(2, "0");
            count.innerHTML = `${current} <span>/ ${String(cards.length).padStart(2, "0")}</span>`;
        }
        if (progress) {
            const availableSteps = Math.max(1, cards.length - 1);
            const progressIndex = Math.round((currentScroll / maxScroll) * availableSteps);
            progress.style.transform = `translateX(${(progressIndex / availableSteps) * (cards.length - 1) * 100}%)`;
        }
    };

    const move = (direction) => {
        const destination = Math.max(0, Math.min(cards.length - 1, activeIndex + direction));
        cards[destination].scrollIntoView({
            behavior: window.matchMedia("(prefers-reduced-motion: reduce)").matches ? "auto" : "smooth",
            block: "nearest",
            inline: "start",
        });
    };

    previous.addEventListener("click", () => move(-1));
    next.addEventListener("click", () => move(1));
    track.addEventListener("scroll", () => {
        window.clearTimeout(scrollTimer);
        scrollTimer = window.setTimeout(update, 70);
    }, { passive: true });
    track.addEventListener("keydown", (event) => {
        if (event.key === "ArrowLeft") {
            event.preventDefault();
            move(-1);
        } else if (event.key === "ArrowRight") {
            event.preventDefault();
            move(1);
        }
    });
    window.addEventListener("resize", update, { passive: true });
    update();
});

const swiper = new Swiper(".swiper:not(.garminSwiper)", {
    modules: [Autoplay],
    loop: true,
    autoplay: {
        delay: 3000,
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
