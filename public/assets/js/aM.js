document.querySelectorAll('.carousel-container').forEach(container => {
    let isDown = false;
    let startX;
    let scrollLeft;

    container.addEventListener('mousedown', (e) => {
        isDown = true;
        container.classList.add('active');
        startX = e.pageX - container.offsetLeft;
        scrollLeft = container.scrollLeft;
    });

    container.addEventListener('mouseleave', () => {
        isDown = false;
        container.classList.remove('active');
    });

    container.addEventListener('mouseup', () => {
        isDown = false;
        container.classList.remove('active');
    });

    container.addEventListener('mousemove', (e) => {
        if (!isDown) return;
        e.preventDefault();
        const x = e.pageX - container.offsetLeft;
        const walk = (x - startX) * 2;
        container.scrollLeft = scrollLeft - walk;
    });

    // Adiciona suporte a toque para mobile
    container.addEventListener('touchstart', (e) => {
        isDown = true;
        startX = e.touches[0].pageX - container.offsetLeft;
        scrollLeft = container.scrollLeft;
    });

    container.addEventListener('touchmove', (e) => {
        if (!isDown) return;
        const x = e.touches[0].pageX - container.offsetLeft;
        const walk = (x - startX) * 2;
        container.scrollLeft = scrollLeft - walk;
    });

    container.addEventListener('touchend', () => {
        isDown = false;
    });

    document.addEventListener("DOMContentLoaded", function () {
  const container = document.getElementById("carousel-container");

  if (!container) return;

  const cards = Array.from(container.children);

  // Duplicar os cards
  cards.forEach(card => {
    const clone = card.cloneNode(true);
    container.appendChild(clone);
  });

  let scrollAmount = 0;

  function animateCarousel() {
    scrollAmount += 1;
    container.scrollLeft = scrollAmount;

    if (scrollAmount >= container.scrollWidth / 2) {
      scrollAmount = 0;
      container.scrollLeft = 0;
    }

    requestAnimationFrame(animateCarousel);
  }

  animateCarousel();
});
});

