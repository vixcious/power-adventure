const form = document.getElementById("form-paket");
const container = document.getElementById("paket-container");

let paketList = [];
let editIndex = null;

async function loadData() {
  const res = await fetch("paket-list.json");
  paketList = await res.json();
  renderList();
}

function renderList() {
  const keyword = document.getElementById("adminSearch").value.toLowerCase();
  const selectedCategory = document.getElementById("adminCategory").value;

  const filteredList = paketList.filter((item) => {
    const matchTitle = item.details.title.toLowerCase().includes(keyword);
    const matchCategory = selectedCategory === "all" || item.category === selectedCategory;
    return matchTitle && matchCategory;
  });

  container.innerHTML = "";
  filteredList.forEach((item, i) => {
    const div = document.createElement("div");
    div.className = "card";
    div.innerHTML = `
      <div class="card-content">
        <img src="${item.details.image}" alt="${item.details.title}" class="thumb" />
        <div class="card-text">
          <h3>${item.details.title}</h3>
          <p><i class="ri-map-pin-line"></i> ${item.details.location || '-'}</p>
          <p>${item.details.desc}</p>
          <p><i class="ri-money-dollar-circle-line"></i> 
            <strong>Rp ${item.details.price1}</strong> 
            ${item.details.price2 ? `→ <strong>Rp ${item.details.price2}</strong>` : ""}
          </p>
          <p><i class="ri-user-line"></i> Min: ${item.details.minimum || '-'}</p>
          <p><i class="ri-time-line"></i> Durasi: ${item.details.duration || '-'}</p>
          <p><i class="ri-heart-line"></i> Fasilitas: ${item.details.facilities || '-'}</p>
          <p><i class="ri-check-double-line"></i> Include: ${item.details.include || '-'}</p>
          <div class="card-buttons">
            <button onclick="editPaket(${i})">Edit</button>
            <button onclick="hapusPaket(${i})">Hapus</button>
          </div>
        </div>
      </div>
    `;
    container.appendChild(div);
  });
}


function editPaket(i) {
  const p = paketList[i];
  editIndex = i;
  document.getElementById("title").value = p.details.title;
  document.getElementById("desc").value = p.details.desc;
  document.getElementById("image").value = p.details.image;
  document.getElementById("price1").value = p.details.price1;
  document.getElementById("price2").value = p.details.price2;
  document.getElementById("minimum").value = p.details.minimum;
  document.getElementById("duration").value = p.details.duration;
  document.getElementById("facilities").value = p.details.facilities;
  document.getElementById("location").value = p.details.location || "";
  document.getElementById("include").value = p.details.include;
  document.getElementById("priceLabel").value = p.details.priceLabel || ' / Pax';
  document.getElementById("category").value = p.category;
}

function hapusPaket(i) {
  if (confirm("Yakin hapus paket ini?")) {
    paketList.splice(i, 1);
    simpanData();
  }
}

form.onsubmit = async (e) => {
  e.preventDefault();
  const item = {
    category: document.getElementById("category").value,
    price: parseInt(document.getElementById("price2").value || 0),
  details: {
  title: document.getElementById("title").value,
  desc: document.getElementById("desc").value,
  image: document.getElementById("image").value,
  price1: document.getElementById("price1").value,
  price2: document.getElementById("price2").value,
  priceLabel: document.getElementById("priceLabel").value || ' / Pax',
  minimum: document.getElementById("minimum").value,
  duration: document.getElementById("duration").value,
  facilities: document.getElementById("facilities").value,
  location: document.getElementById("location").value,
  include: document.getElementById("include").value
  
  }

  };

  if (editIndex !== null) {
    paketList[editIndex] = item;
    editIndex = null;
  } else {
    paketList.push(item);
  }

  form.reset();
  simpanData();
};

async function simpanData() {
  const res = await fetch("save-paket.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(paketList)
  });
  if ((await res.json()).status === "success") {
    loadData();
  } else {
    alert("Gagal menyimpan data!");
  }
}

loadData();
// di bawah loadData();
loadData().then(() => {
  document.getElementById("adminSearch").addEventListener("input", renderList);
  document.getElementById("adminCategory").addEventListener("change", renderList);
});