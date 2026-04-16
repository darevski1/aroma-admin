import Toastify from "toastify-js";
import "toastify-js/src/toastify.css";
import "./main.js";
import "animate.css";
import "./users.js";
import "./custom.js";
import Swiper from "swiper";
import "swiper/css";

const swiper = new Swiper(".swiper", {
    loop: true,
    autoplay: {
        delay: 3000,
    },
});
