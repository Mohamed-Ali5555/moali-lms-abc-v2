<script>
    //let operatorBtn = document.querySelectorAll(".counter-operator");
    const header = document.querySelector("header");

    // Move Image When Mouse Move
    const moveImage = (e) => {
        let h = header.clientHeight;
        let w = header.clientWidth;
        let i = (10 * e.pageX) / h - 1;
        let o = (2 * e.pageY) / w - 1;
        let s = 20 * o;
        mainImage.style.transform = `translate(${i}px, ${s}px)`;
    };

    // header.addEventListener("mousemove", moveImage);

    // const swiper = new Swiper(".swiper", {
    //     loop: true,
    //     preventClicks: false,

    //     // autoplay: {
    //     //   delay: 4000,
    //     // },

    //     // If we need pagination
    //     pagination: {
    //         el: ".swiper-pagination",
    //     },

    //     // Navigation arrows
    //     navigation: {
    //         nextEl: ".swiper-button-next",
    //         prevEl: ".swiper-button-prev",
    //     },

    //     breakpoints: {
    //         300: {
    //             slidesPerView: 1.2,
    //             spaceBetween: 10,
    //         },
    //         767: {
    //             slidesPerView: 2.2,
    //             spaceBetween: 15,
    //         },
    //         992: {
    //             slidesPerView: 3.2,
    //             spaceBetween: 20,
    //         },
    //         1200: {
    //             slidesPerView: 4.2,
    //             spaceBetween: 25,
    //         },
    //     },
    // });

    // operatorBtn.forEach((btn) => {
    //     btn.addEventListener("click", () => {
    //         let counterContainer = btn.closest(".book-counter");
    //         let counterNum = counterContainer.querySelector(".counter-number");

    //         if (btn.classList.contains("operator-minus")) {
    //             counterNum.textContent--;
    //         } else if (btn.classList.contains("operator-plus")) {
    //             counterNum.textContent++;
    //         }

    //         if (counterNum.textContent > 0) {
    //             counterContainer.querySelector(".operator-minus").disabled = false;
    //         } else {
    //             counterContainer.querySelector(".operator-minus").disabled = true;
    //         }
    //     });
    // });
</script>
