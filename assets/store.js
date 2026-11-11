var pets = ["Dogs", "Cats", "Birds"];

var faqs = [
  ["How fast is delivery?", "Most orders go out the same day from the harbor. Delivery time depends on your location."],
  ["When is delivery free?", "Orders over ₱1,500 ship free."],
  ["What is your return policy?", "You have 30 days to return an item. If your pet doesn't love it, let us know and we'll make it right."],
  ["Are your products vet-approved?", "Yes. Every product is reviewed for quality and safety before we stock it."],
  ["How do I order?", "Add products to your inquiry list and send it with your contact details. Our staff will review it and contact you to confirm."],
  ["How do I pay?", "We accept cash and GCash."],
  ["How do I check my inquiry?", "Use the Track page with your inquiry number and phone number."],
  ["Which pets do you cater to?", "Mostly dogs, cats and birds, with food, health, grooming, toys, beds and more."]
];

var titles = {home: "Sailor Pets", about: "About us · Sailor Pets", contact: "Contact · Sailor Pets", faq: "FAQ · Sailor Pets", shipping: "Shipping & returns · Sailor Pets", track: "Track inquiry · Sailor Pets"};

var cart = {};
var favs = {};
var orderType = "Retail";
var filters = {pet: "All", cat: "All", sort: "pop", search: "", shown: 12};
var showAllCatalogs = false;
var page = "home";
var current = null;
var detailQty = 1;

var sheet = new bootstrap.Offcanvas(document.getElementById("sheet"));

function $(id) {
  return document.getElementById(id);
}

function esc(text) {
  return String(text).replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;");
}

function money(n) {
  return "₱" + Number(n).toLocaleString("en-US", {minimumFractionDigits: 0, maximumFractionDigits: 2});
}

function priceOf(p) {
  return orderType == "Wholesale" ? p.wholesale : p.price;
}

function findProduct(id) {
  return products.find(function (p) { return p.id == id; });
}

function toast(text) {
  $("msgText").textContent = text;
  bootstrap.Toast.getOrCreateInstance($("msgToast"), {delay: 1600}).show();
}

function goTo(id) {
  if (page != "home") {
    location.hash = "#" + id;
  } else {
    $(id).scrollIntoView({behavior: "smooth"});
  }
}

function saveCart() {
  try { sessionStorage.setItem("sp_cart", JSON.stringify(cart)); } catch (e) {}
}

function loadCart() {
  try { cart = JSON.parse(sessionStorage.getItem("sp_cart")) || {}; } catch (e) { cart = {}; }
  for (var id in cart) {
    var p = findProduct(id);
    if (!p || p.stock < 1) delete cart[id];
    else if (cart[id] > p.stock) cart[id] = p.stock;
  }
}

function filteredProducts() {
  var list = products.filter(function (p) {
    var catOk = filters.cat == "All" || p.cat == filters.cat;
    var petOk = filters.pet == "All" || p.pets.includes(filters.pet) || p.pets == "All pets";
    var searchOk = (p.name + p.cat + p.pets).toLowerCase().includes(filters.search);
    return catOk && petOk && searchOk;
  });
  if (filters.sort == "lo") list.sort(function (a, b) { return a.price - b.price; });
  if (filters.sort == "hi") list.sort(function (a, b) { return b.price - a.price; });
  return list;
}

function productImage(p, ratio) {
  if (p.img) return `<div class="ratio ${ratio}"><img src="${esc(p.img)}" class="object-fit-cover" alt="${esc(p.name)}"></div>`;
  return `<div class="ratio ${ratio} bg-mint"><div class="d-flex align-items-center justify-content-center text-secondary small">No image</div></div>`;
}

function productCard(p) {
  var tag = p.tag ? `<span class="badge rounded-pill bg-navy position-absolute top-0 start-0 m-2">${esc(p.tag)}</span>` : "";
  var heart = favs[p.id] ? "bi-heart-fill text-danger" : "bi-heart";
  var stock = p.stock > 0
    ? `<small class="text-secondary">${p.stock} left</small>`
    : '<span class="badge bg-danger">Out of stock</span>';
  var addButton = p.stock > 0
    ? `<button class="btn btn-sm bg-mint text-brand rounded-pill fw-bold px-3" onclick="addToCart(event, ${p.id})" aria-label="Add ${esc(p.name)}">+ Add</button>`
    : "";
  return `
    <div class="col">
      <div class="card h-100 rounded-4 overflow-hidden" role="button" onclick="openProduct(${p.id})">
        <div class="position-relative">
          ${productImage(p, "ratio-1x1")}
          ${tag}
          <button class="btn btn-light rounded-circle position-absolute top-0 end-0 m-2 p-0" style="width: 34px; height: 34px;" onclick="toggleFav(event, ${p.id})" aria-label="Save ${esc(p.name)}"><i class="bi ${heart}"></i></button>
        </div>
        <div class="card-body d-flex flex-column p-3">
          <span class="mono text-secondary" style="font-size: 9.5px;">${esc(p.cat)} · ${esc(p.pets)}</span>
          <h3 class="fs-6 fw-bold">${esc(p.name)}</h3>
          <div class="small text-secondary">Wholesale ${money(p.wholesale)}</div>
          <div class="d-flex justify-content-between align-items-center mt-auto pt-2">
            <span class="brand fw-bold fs-5">${money(p.price)}</span>
            ${addButton}
          </div>
          <div class="mt-1">${stock}</div>
        </div>
      </div>
    </div>`;
}

function renderShop() {
  var list = filteredProducts();
  var visible = list.slice(0, filters.shown);
  $("shopTitle").textContent = filters.cat == "All" ? "Best sellers" : filters.cat;
  $("shopCount").textContent = `Showing ${visible.length} of ${list.length} products`;
  $("grid").innerHTML = visible.length
    ? visible.map(productCard).join("")
    : '<div class="col-12 text-center text-secondary py-5">No matches. Try another pet or catalog.</div>';
  $("moreBtn").classList.toggle("d-none", list.length <= filters.shown);
  $("moreBtn").textContent = `Show more (${list.length - filters.shown} left)`;
  $("catFilter").value = filters.cat;
  $("petFilter").value = filters.pet;
  $("sortFilter").value = filters.sort;
}

function renderPets() {
  $("petList").innerHTML = pets.map(function (pet) {
    var count = products.filter(function (p) { return p.pets.includes(pet) || p.pets == "All pets"; }).length;
    return `
      <div class="col">
        <button class="btn bg-white border rounded-4 w-100 py-3" onclick="filterPet('${pet}')">
          <img src="${baseUrl}/assets/images/pet-${pet.toLowerCase()}.jpg" alt="" class="rounded-circle object-fit-cover border border-3" style="width: 64px; height: 64px; border-color: var(--mint) !important;">
          <b class="brand d-block mt-2">${pet}</b>
          <small class="text-secondary">${count} items</small>
        </button>
      </div>`;
  }).join("");
}

function renderCatalogs() {
  var list = showAllCatalogs ? catalogs : catalogs.slice(0, 8);
  $("catalogCount").textContent = catalogs.length + " catalogs for every need";
  $("catalogToggle").textContent = showAllCatalogs ? "Show fewer" : "Show all " + catalogs.length + " catalogs";
  $("catalogToggle").classList.toggle("d-none", catalogs.length <= 8);
  $("catalogList").innerHTML = list.map(function (c, i) {
    var count = products.filter(function (p) { return p.cat == c[0]; }).length;
    return `
      <div class="col">
        <button class="btn bg-white border rounded-4 w-100 d-flex align-items-center gap-2 p-2 text-start fw-bold small lh-sm" data-cat="${esc(c[0])}" onclick="filterCat(this.dataset.cat)">
          ${c[2] ? `<img src="${esc(c[2])}" alt="" class="rounded-3 object-fit-cover flex-shrink-0" style="width: 44px; height: 44px;">` : `<span class="icon-box rounded-3 d-flex align-items-center justify-content-center fs-5 tone-${i % 3}"><i class="bi bi-${esc(c[1])}"></i></span>`}
          <span>${esc(c[0])}<small class="d-block fw-normal text-secondary">${count} ${count == 1 ? "item" : "items"}</small></span>
        </button>
      </div>`;
  }).join("");
  $("catFilter").innerHTML = '<option value="All">All catalogs</option>' + catalogs.map(function (c) {
    return `<option>${esc(c[0])}</option>`;
  }).join("");
}

function renderFaq() {
  $("faqList").innerHTML = faqs.map(function (f, i) {
    return `
      <div class="accordion-item rounded-4 overflow-hidden mb-2">
        <h3 class="accordion-header"><button class="accordion-button collapsed fw-bold fs-6 bg-white" data-bs-toggle="collapse" data-bs-target="#faq${i}">${f[0]}</button></h3>
        <div id="faq${i}" class="accordion-collapse collapse" data-bs-parent="#faqList"><div class="accordion-body text-secondary">${f[1]}</div></div>
      </div>`;
  }).join("");
}

function filterPet(pet) {
  filters.pet = pet;
  filters.cat = "All";
  filters.search = "";
  filters.shown = 12;
  $("searchBox").value = "";
  renderShop();
  goTo("shop");
}

function filterCat(cat) {
  filters.cat = cat;
  filters.shown = 12;
  renderShop();
  goTo("shop");
}

function resetFilters() {
  filters = {pet: "All", cat: "All", sort: "pop", search: "", shown: 12};
  $("searchBox").value = "";
  renderShop();
}

function showMore() {
  filters.shown += 12;
  renderShop();
}

function toggleCatalogs() {
  showAllCatalogs = !showAllCatalogs;
  renderCatalogs();
  renderShop();
}

function toggleFav(event, id) {
  event.stopPropagation();
  favs[id] = !favs[id];
  renderShop();
}

function showFavs() {
  var n = Object.keys(favs).filter(function (id) { return favs[id]; }).length;
  toast(n ? n + " saved item" + (n > 1 ? "s" : "") : "No saved items yet");
}

function cartSum() {
  var sum = 0;
  for (var id in cart) sum += cart[id] * priceOf(findProduct(id));
  return sum;
}

function updateCartBar() {
  var n = 0;
  for (var id in cart) n += cart[id];
  $("count").textContent = n;
  $("cartBar").classList.toggle("d-none", n == 0);
  $("cartBar").innerHTML = `<span>View inquiry · ${n} ${n > 1 ? "items" : "item"}</span><span>${money(cartSum())}</span>`;
  saveCart();
}

function addToCart(event, id) {
  event.stopPropagation();
  var p = findProduct(id);
  if ((cart[id] || 0) + 1 > p.stock) {
    toast("Only " + p.stock + " in stock.");
    return;
  }
  cart[id] = (cart[id] || 0) + 1;
  updateCartBar();
  toast("Added to your inquiry");
}

function changeQty(id, change) {
  var p = findProduct(id);
  if (cart[id] + change > p.stock) {
    toast("Only " + p.stock + " in stock.");
    return;
  }
  cart[id] += change;
  if (cart[id] < 1) delete cart[id];
  updateCartBar();
  if (Object.keys(cart).length) {
    renderCartLines();
  } else {
    showCart();
  }
}

function setType(type) {
  orderType = type;
  updateCartBar();
  renderCartLines();
}

function renderCartLines() {
  var html = "";
  var inputs = "";
  for (var id in cart) {
    var p = findProduct(id);
    html += `
      <div class="d-flex align-items-center gap-3 py-2 border-bottom">
        ${p.img ? `<img src="${esc(p.img)}" alt="" class="rounded-3 object-fit-cover flex-shrink-0" style="width: 56px; height: 56px;">` : '<div class="rounded-3 bg-mint flex-shrink-0" style="width: 56px; height: 56px;"></div>'}
        <div class="flex-grow-1 fw-bold small lh-sm">${esc(p.name)}<small class="d-block fw-normal text-secondary">${money(priceOf(p))}</small></div>
        <div class="d-flex align-items-center gap-2 bg-mint rounded-pill p-1">
          <button type="button" class="btn btn-light btn-sm rounded-circle fw-bold" style="width: 34px; height: 34px;" onclick="changeQty(${id}, -1)" aria-label="Remove one">−</button>
          <b>${cart[id]}</b>
          <button type="button" class="btn btn-light btn-sm rounded-circle fw-bold" style="width: 34px; height: 34px;" onclick="changeQty(${id}, 1)" aria-label="Add one">+</button>
        </div>
      </div>`;
    inputs += `<input type="hidden" name="qty[${id}]" value="${cart[id]}">`;
  }
  $("cartLines").innerHTML = html;
  $("cartInputs").innerHTML = inputs;
  $("cartTotal").textContent = money(cartSum());
}

function cartHtml() {
  var head = `<div class="d-flex justify-content-between align-items-center mb-2"><h2 class="fs-4 fw-bold mb-0">Your inquiry</h2><button class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button></div>`;
  if (!Object.keys(cart).length) {
    return head + '<p class="text-center text-secondary py-4">Your list is empty. Add something for your pet.</p>';
  }
  return head + `
    <div id="cartLines"></div>
    <div class="d-flex justify-content-between fw-bold fs-5 my-3"><span>Estimated total</span><span id="cartTotal"></span></div>
    <form method="post" action="${baseUrl}/index.php?page=inquiry_send">
      <input type="hidden" name="csrf" value="${csrf}">
      <div id="cartInputs"></div>
      <div class="row g-2">
        <div class="col-12"><input name="customer_name" class="form-control rounded-3" placeholder="Your name *" required></div>
        <div class="col-6"><input name="customer_phone" class="form-control rounded-3" placeholder="Phone *" required></div>
        <div class="col-6"><input type="email" name="customer_email" class="form-control rounded-3" placeholder="Email"></div>
        <div class="col-12"><input name="customer_address" class="form-control rounded-3" placeholder="Address"></div>
        <div class="col-12">
          <select id="orderType" name="inquiry_type" class="form-select rounded-3" onchange="setType(this.value)"><option>Retail</option><option>Wholesale</option></select>
        </div>
        <div class="col-12"><textarea name="message" class="form-control rounded-3" rows="2" maxlength="255" placeholder="Message (optional)"></textarea></div>
      </div>
      <p class="small text-secondary mt-2 mb-0">This sends an inquiry, not a payment. Our staff will review it and contact you.</p>
      <button class="btn btn-primary rounded-pill w-100 py-3 fw-bold mt-3">Send inquiry</button>
    </form>`;
}

function showCart() {
  $("sheetBody").innerHTML = cartHtml();
  if ($("cartLines")) {
    $("orderType").value = orderType;
    renderCartLines();
  }
  sheet.show();
}

function detailHtml() {
  var p = current;
  var inStock = p.stock > 0;
  return `
    <div class="rounded-4 overflow-hidden mb-3">${productImage(p, "ratio-4x3")}</div>
    <div class="d-flex justify-content-between align-items-start gap-3">
      <div>
        <h2 class="fs-4 fw-bold mb-1">${esc(p.name)}</h2>
        <div class="small text-secondary">${esc(p.pets)} · ${esc(p.cat)}</div>
        <div class="brand fw-bold fs-3 text-brand mt-1">${money(p.price)}</div>
        <div class="small text-secondary">Wholesale ${money(p.wholesale)}</div>
      </div>
      <button class="btn bg-white border rounded-circle round-btn flex-shrink-0" data-bs-dismiss="offcanvas" aria-label="Close"><i class="bi bi-x-lg"></i></button>
    </div>
    <p class="mt-2 ${inStock ? "text-secondary" : "text-danger fw-bold"}">${inStock ? p.stock + " in stock" : "Out of stock"}</p>
    <div class="bg-mint rounded-3 p-3 my-3 small d-grid gap-1">
      <div>✓ Free delivery over ₱1,500</div><div>✓ Vet-approved</div><div>✓ 30-day easy returns</div>
    </div>
    ${inStock ? `
    <div class="d-flex align-items-center gap-3">
      <div class="d-flex align-items-center gap-2 bg-mint rounded-pill p-1">
        <button class="btn btn-light rounded-circle fw-bold" style="width: 34px; height: 34px; padding: 0;" onclick="changeDetailQty(-1)" aria-label="Less">−</button>
        <b>${detailQty}</b>
        <button class="btn btn-light rounded-circle fw-bold" style="width: 34px; height: 34px; padding: 0;" onclick="changeDetailQty(1)" aria-label="More">+</button>
      </div>
      <button class="btn btn-primary rounded-pill flex-grow-1 py-3 fw-bold" onclick="addCurrent()">Add to inquiry · ${money(p.price * detailQty)}</button>
    </div>` : ""}`;
}

function openProduct(id) {
  current = findProduct(id);
  detailQty = 1;
  $("sheetBody").innerHTML = detailHtml();
  sheet.show();
}

function changeDetailQty(change) {
  detailQty = Math.min(current.stock, Math.max(1, detailQty + change));
  $("sheetBody").innerHTML = detailHtml();
}

function addCurrent() {
  var total = (cart[current.id] || 0) + detailQty;
  if (total > current.stock) {
    toast("Only " + current.stock + " in stock.");
    return;
  }
  cart[current.id] = total;
  sheet.hide();
  updateCartBar();
  toast("Added to your inquiry");
}

function route() {
  var hash = location.hash;
  page = hash.startsWith("#/") ? hash.slice(2) : "home";
  if (!titles[page]) page = "home";
  document.querySelectorAll(".page").forEach(function (el) {
    el.classList.toggle("d-none", el.id != "page-" + page);
  });
  document.querySelectorAll("[data-nav]").forEach(function (a) {
    a.classList.toggle("active", a.dataset.nav == page);
  });
  document.title = titles[page];
  if (!hash || hash == "#top" || hash.startsWith("#/")) {
    scrollTo(0, 0);
  } else {
    setTimeout(function () {
      var el = $(hash.slice(1));
      if (el) el.scrollIntoView();
    }, 30);
  }
}

$("searchBox").addEventListener("input", function (e) {
  filters.search = e.target.value.trim().toLowerCase();
  filters.shown = 12;
  renderShop();
  if (filters.search) goTo("shop");
});

$("catFilter").addEventListener("change", function (e) { filters.cat = e.target.value; filters.shown = 12; renderShop(); });
$("petFilter").addEventListener("change", function (e) { filters.pet = e.target.value; filters.shown = 12; renderShop(); });
$("sortFilter").addEventListener("change", function (e) { filters.sort = e.target.value; filters.shown = 12; renderShop(); });

window.addEventListener("hashchange", route);

loadCart();
renderPets();
renderCatalogs();
renderShop();
renderFaq();
updateCartBar();
route();
