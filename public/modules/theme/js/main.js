"use strict";
// ============ All Variables ============
const player = document.querySelector("#cart_icon_player");
const menuToggleBtn = document.querySelector("#menu_toggle_btn");
const menuBtn = document.querySelector("#menu__btns");
const mainNavbar = document.querySelector("#main__navbar");
const myBarIndicator = document.querySelector("#myBar");
const themeButton = document.querySelector("#theme__button");
const localTheme = window.localStorage.getItem("mrs-eman-theme") || "light";
const FeaturesCardsTitle = document.querySelectorAll(".features_card_title");
const coursesCard = document.querySelectorAll(".classroom-card");
const sectionTitle = document.querySelectorAll(".section-title");
const featuresCardTitle = document.querySelectorAll(".features_card_title");
const allInputs = document.querySelectorAll(".custom_input input");

// ============ All Functions ============
// Toggle Navbar Menu For Mobile
const toggleNavbarMenu = (e) => {
    const thisBtn = e.target.closest("button");

    if (thisBtn) {
        thisBtn.classList.toggle("opened");
        thisBtn.setAttribute(
            "aria-expanded",
            thisBtn.classList.contains("opened")
        );
        menuBtn.classList.toggle("opened");
    }
};

// Scroll Window
const scrollWindow = (e) => {
    let winScroll =
        document.body.scrollTop || document.documentElement.scrollTop;
    let height = document.body.scrollHeight - document.body.clientHeight;
    let scrolled = (winScroll / height) * 100;

    if (myBarIndicator) {
        myBarIndicator.style.width = scrolled + "%";
    }

    if (window.pageYOffset >= 100) {
        mainNavbar.classList.add("scrolling");
    } else {
        mainNavbar.classList.remove("scrolling");
    }
};

// Toggle Theme
const toggleTheme = (e) => {
    let myButton = e.target.closest("button");

    if (myButton) {
        myButton.dataset.theme =
            myButton.dataset.theme === "dark" ? "light" : "dark";
        document.body.dataset.theme = myButton.dataset.theme;
        window.localStorage.setItem("mrs-eman-theme", myButton.dataset.theme);
    }
};

const changeIConsColor = () => {
    document
        .querySelectorAll(
            ".userDropDown li:not(:first-child),  .couses-tab-list li"
        )
        .forEach((li) => {
            const icon = li.querySelector("dotlottie-player");
            if (!icon) return;

            const observer = new MutationObserver((mutations) => {
                const svg = icon.shadowRoot?.querySelector("svg");
                if (svg) {
                    const paths = svg.querySelectorAll("path");
                    const hiddenGroups = svg.querySelectorAll(
                        'g[style*="display: none"]'
                    );
                    hiddenGroups.forEach((g) => (g.style.display = "block"));
                    paths.forEach((path) => {
                        path.setAttribute(
                            "stroke",
                            li.classList.contains("active")
                                ? "rgb(var(--c-accent-rgb))"
                                : "var(--theme-color)"
                        );
                        path.setAttribute(
                            "stroke-width",
                            li.classList.contains("active") ? "20" : "10"
                        );
                    });
                    hiddenGroups.forEach((g) => (g.style.display = "none"));
                    observer.disconnect();
                }
            });

            observer.observe(icon.shadowRoot, {
                childList: true,
                subtree: true,
            });
        });
};

const checkTheme = () => {
    if (themeButton) {
        themeButton.dataset.theme = localTheme;
    }
    document.body.dataset.theme = localTheme;
    changeIConsColor();
};

// Animation Section Title
const sectionTitleAnimation = () => {
    sectionTitle.forEach((title) => {
        const titleOffsetTop = title.offsetTop;
        const scrollPosition = window.scrollY + window.innerHeight / 2.4;

        let newRight = -500 + (scrollPosition - titleOffsetTop);

        // تأكد من أن العنصر لا يتحرك بعد الوصول إلى موضعه النهائي
        if (newRight > 0) {
            newRight = 0;
        }

        title.style.setProperty("--title-after-right", `${newRight}px`);
    });
};

// Animation Features Title
const featuresTitleAnimation = () => {
    featuresCardTitle.forEach((title) => {
        const titleOffsetTop = title.offsetTop;
        const scrollPosition = window.scrollY + window.innerHeight / 10.3;
        const color = title.dataset.background;

        let newY = -400 + (scrollPosition - titleOffsetTop);
        let newRotate =
            30 -
            ((scrollPosition - titleOffsetTop) / (window.innerHeight / 2.3)) *
                20;

        // تأكد من أن العنصر لا يتحرك بعد الوصول إلى موضعه النهائي
        if (newY > 0) {
            newY = 0;
        } else if (newY < -350) {
            newY = -350;
        }

        // تأكد من أن الدوران لا يتجاوز 0 درجة
        if (newRotate < 0) {
            newRotate = 0;
        }

        title.style.setProperty("--title-translate-y", `${newY}px`);
        title.style.setProperty("--title-rotate", `${newRotate}deg`);
        title.style.backgroundColor = color;
    });
};

// ============ Handel All Function ============
// == Cart Animation Icon ==
if (player) {
    player.addEventListener("ready", () => {
        const svg = player.shadowRoot.querySelector("svg");
        if (svg) {
            const bbox = svg.getBBox();

            const padding = 20;
            const adjustedX = bbox.x - padding + 100;
            const adjustedWidth = bbox.width + padding * 2;

            svg.setAttribute(
                "viewBox",
                `${adjustedX} ${bbox.y} ${adjustedWidth} ${bbox.height}`
            );

            const paths = svg.querySelectorAll("path");
            paths.forEach((path) => {
                path.setAttribute("stroke", "#777");
            });
        }
    });
}

document
    .querySelectorAll(
        ".userDropDown li:not(:first-child), .couses-tab-list li:not(.active)"
    )
    .forEach((li) => {
        li.addEventListener("mouseenter", () => {
            const icon = li.querySelector("dotlottie-player");
            if (icon) {
                icon.play();
            }
        });

        li.addEventListener("mouseleave", () => {
            const icon = li.querySelector("dotlottie-player");
            if (icon) {
                icon.stop();
            }
        });
    });

if (menuToggleBtn) {
    menuToggleBtn.addEventListener("click", toggleNavbarMenu);
}

if (themeButton) {
    themeButton.addEventListener("click", toggleTheme);
}

window.addEventListener("scroll", () => {
    scrollWindow();
    sectionTitleAnimation();
    featuresTitleAnimation();
});

checkTheme();
scrollWindow();
sectionTitleAnimation();
featuresTitleAnimation();

// ============ Make Animation With Mouse Move In Course Card ============
coursesCard.forEach((card) => {
    card.addEventListener("mousemove", function (e) {
        let limits = 15;
        let rect = card.getBoundingClientRect();
        let x = e.clientX - rect.left;
        let y = e.clientY - rect.top;
        let offsetX = x / rect.width;
        let offsetY = y / rect.height;

        let rotateY = offsetX * (limits * 2) - limits;
        let rotateX = offsetY * (limits * 2) - limits;

        card.style.transform = `rotateY(${rotateY}deg) rotateX(${-rotateX}deg)`;
    });

    card.addEventListener("mouseleave", function (e) {
        card.style.transform = `rotateY(0deg) rotateX(0deg)`;
    });
});

// Start Animation Input
allInputs.forEach((input) => {
    input.addEventListener("focus", function () {
        input.closest(".custom_input").classList.add("focused");
    });
    input.addEventListener("blur", function () {
        if (input.value == "") {
            input.closest(".custom_input").classList.remove("focused");
        }
    });
});

function openVideosModal(ele, url) {
    const modal = document.querySelector("#videosModal");
    const iframe = modal.querySelector("iframe");
    const modalTitle = modal.querySelector(".modal-title");

    iframe.src = url;
    modalTitle.textContent = ele.textContent;

    const bootstrapModal = new bootstrap.Modal(modal);
    bootstrapModal.show();
}

document
    .querySelector("#videosModal")
    ?.addEventListener("hidden.bs.modal", function () {
        this.querySelector("iframe").src = "";
    });

window.openVideosModal = openVideosModal;
