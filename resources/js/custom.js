document.addEventListener("scroll", function () {
    let navbar = document.getElementById("scroller");

    if (window.scrollY > 50) {
        // navbar.classList.remove("my-navbar");
        navbar.classList.add("fixed-navbar");
    } else {
        navbar.classList.add("my-navbar");
        navbar.classList.remove("fixed-navbar");
    }
});
