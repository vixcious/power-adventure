document.addEventListener("DOMContentLoaded", () => {
  // Menu toggle functionality
  const menuBtn = document.getElementById("menu-btn");
  const navLinks = document.getElementById("nav-links");

  menuBtn.addEventListener("click", () => {
    navLinks.classList.toggle("open");
  });

  // Close menu when clicking a link
  navLinks.querySelectorAll("a").forEach((link) => {
    link.addEventListener("click", () => {
      navLinks.classList.remove("open");
    });
  });

  // Package filtering and rendering
  const container = document.querySelector(".daftar-harga__grid");
  const buttons = document.querySelectorAll(".filter-btn");
  let allData = [];

  function formatRupiah(angka, label = " / Pax") {
    return "Rp " + parseInt(angka).toLocaleString("id-ID") + label;
  }

  fetch("paket-list.json")
    .then((res) => res.json())
    .then((data) => {
      allData = data;
      renderPackages(data);
    });

  function renderPackages(data) {
    container.innerHTML = "";
    data.forEach((pkg) => {
      const card = document.createElement("div");
      card.className = "daftar-harga__card";
      card.setAttribute("data-category", pkg.category);
      card.setAttribute("data-price", pkg.price);
      card.setAttribute("data-details", JSON.stringify(pkg.details));
      card.innerHTML = `
        <img src="${pkg.details.image}" alt="${pkg.details.title}" />
        <div class="daftar-harga__card__content">
          <h4>${pkg.details.title}</h4>
          <div class="location">
            <i class="ri-map-pin-2-fill"></i>
            <p>${pkg.details.location || "Pangalengan"}</p>
          </div>
          <div class="daftar-harga__card__footer">
            <p>${formatRupiah(pkg.details.price1, pkg.details.priceLabel)}</p>
          </div>
          <button class="btn detail-btn">Lihat Detail</button>
        </div>
      `;
      container.appendChild(card);
    });
    bindModalEvents();
  }

  buttons.forEach((btn) => {
    btn.addEventListener("click", () => {
      document.querySelector(".filter__btn.active")?.classList.remove("active");
      btn.classList.add("active");
      const category = btn.dataset.filter;
      const filtered = category === "all"
        ? allData
        : allData.filter((pkg) => (pkg.category || "").toLowerCase() === category.toLowerCase());
      renderPackages(filtered);
    });
  });

  function bindModalEvents() {
    const modal = document.getElementById("packageModal");
    const modalImage = document.querySelector(".modal-image");
    const modalTitle = document.querySelector(".modal-title");
    const modalDesc = document.querySelector(".modal-desc");
    const modalPrice1 = document.querySelector(".modal-price1");
    const modalPrice2 = document.querySelector(".modal-price2");
    const modalMinimum = document.querySelector(".modal-minimum");
    const modalDuration = document.querySelector(".modal-duration");
    const modalFacilities = document.querySelector(".modal-facilities");
    const modalInclude = document.querySelector(".modal-include");
    const modalBooking = document.querySelector(".modal-booking");
    const closeBtn = document.querySelector(".close-btn");

    document.querySelectorAll(".detail-btn").forEach((button) => {
      button.addEventListener("click", () => {
        const card = button.closest(".daftar-harga__card");
        const details = JSON.parse(card.getAttribute("data-details"));

        modalImage.innerHTML = `<img src="${details.image}" alt="${details.title}">`;
        modalTitle.textContent = details.title;
        modalDesc.textContent = details.desc;
        modalPrice1.textContent = formatRupiah(details.price1, details.priceLabel);
        if (details.price2 && !isNaN(details.price2) && parseInt(details.price2) > 0) {
          modalPrice2.textContent = formatRupiah(details.price2, details.priceLabel);
          modalPrice2.parentElement.style.display = "flex";
        } else {
          modalPrice2.textContent = "";
          modalPrice2.parentElement.style.display = "none";
        }
        modalMinimum.textContent = details.minimum;
        modalDuration.textContent = details.duration;
        modalFacilities.textContent = details.facilities;
        modalInclude.textContent = details.include;
        modalBooking.setAttribute("data-paket", details.title);
        modalBooking.setAttribute("data-harga", card.getAttribute("data-price"));

        modal.style.display = "flex";
      });
    });

    closeBtn.addEventListener("click", () => {
      modal.style.display = "none";
    });

    modal.addEventListener("click", (e) => {
      if (e.target === modal) {
        modal.style.display = "none";
      }
    });

    document.querySelectorAll(".booking-btn").forEach((button) => {
      button.addEventListener("click", function () {
        const paket = this.getAttribute("data-paket");
        const harga = this.getAttribute("data-harga");
        const details = JSON.parse(button.closest(".daftar-harga__card")?.getAttribute("data-details") || "{}");

        const message = `Halo, saya ingin booking:\nPaket: ${paket}\nHarga: ${formatRupiah(harga, details.priceLabel || " / Pax")}\nTanggal: -\nJumlah Orang: -`;
        const phoneNumber = "6281234567890";
        const whatsappURL = `https://api.whatsapp.com/send?phone=${phoneNumber}&text=${encodeURIComponent(message)}`;

        window.open(whatsappURL, "_blank");
        if (modal) modal.style.display = "none";
      });
    });
  }
});