<main id="page-home" class="page container">

  <section class="soft border rounded-5 mt-3 px-4 px-md-5 pt-4 pb-0 overflow-hidden">
    <div class="row align-items-center">
      <div class="col-md-6 pb-3">
        <span class="badge bg-white text-primary border rounded-pill mono fw-normal px-3 py-2"><i class="bi bi-circle-fill me-1" style="font-size: 6px;"></i> New — Harborside bundles · Save 30%</span>
        <h1 class="display-3 fw-bold lh-1 my-3">All paws<span class="d-block text-primary">on deck.</span><span class="d-block text-navy">Loved on shore.</span></h1>
        <p class="text-secondary" style="max-width: 46ch;">Premium food, playful gear and everyday care — curated by vets, packed at the harbor, delivered fast. With our 30-day tail-wag guarantee.</p>
        <div class="d-flex flex-wrap gap-2 my-3">
          <a class="btn btn-primary rounded-pill px-4 py-2 fw-bold" href="#shop">Shop best sellers <i class="bi bi-arrow-right"></i></a>
          <a class="btn btn-light border rounded-pill px-4 py-2 fw-bold" href="#catalogs">Browse catalogs</a>
        </div>
        <div class="d-flex gap-4 border-top pt-3">
          <div><b class="brand d-block fs-4">48k+</b><span class="mono text-secondary">Happy pets</span></div>
          <div><b class="brand d-block fs-4">4.9/5</b><span class="mono text-secondary">2,840 reviews</span></div>
          <div><b class="brand d-block fs-4">24h</b><span class="mono text-secondary">Avg. delivery</span></div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="position-relative" style="height: 380px;">
          <img src="<?= BASE_URL ?>/assets/images/hero-d.jpg" alt="Golden retriever" class="position-absolute object-fit-cover rounded-4 border border-4 border-white shadow" style="left: 6%; top: 40px; width: 50%; height: 300px; transform: rotate(-3deg);">
          <img src="<?= BASE_URL ?>/assets/images/hero-c.jpg" alt="Cat" class="position-absolute object-fit-cover rounded-4 border border-4 border-white shadow" style="right: 5%; top: 14px; width: 46%; height: 290px; transform: rotate(3deg);">
          <div class="position-absolute bg-white rounded-3 shadow-sm small fw-bold px-3 py-2" style="left: 4%; top: 8px;"><i class="bi bi-shield-check text-primary me-2"></i>Vet-approved products</div>
          <div class="position-absolute bg-white rounded-3 shadow-sm small fw-bold px-3 py-2" style="right: 6%; bottom: 8px;"><i class="bi bi-star text-primary me-2"></i>Trusted by 48,000+ crews</div>
        </div>
      </div>
    </div>
  </section>

  <div class="row row-cols-2 row-cols-md-4 g-2 mt-2">
    <div class="col"><div class="h-100 d-flex align-items-center gap-2 bg-white border rounded-4 p-2 small fw-bold lh-sm"><span class="icon-box rounded-3 d-flex align-items-center justify-content-center fs-5 tone-0"><i class="bi bi-truck"></i></span><div>Free fast delivery<small class="d-block fw-normal text-secondary">Over ₱1,500 · same-day</small></div></div></div>
    <div class="col"><div class="h-100 d-flex align-items-center gap-2 bg-white border rounded-4 p-2 small fw-bold lh-sm"><span class="icon-box rounded-3 d-flex align-items-center justify-content-center fs-5 tone-1"><i class="bi bi-shield-check"></i></span><div>Vet-approved<small class="d-block fw-normal text-secondary">Every product reviewed</small></div></div></div>
    <div class="col"><div class="h-100 d-flex align-items-center gap-2 bg-white border rounded-4 p-2 small fw-bold lh-sm"><span class="icon-box rounded-3 d-flex align-items-center justify-content-center fs-5 tone-2"><i class="bi bi-arrow-counterclockwise"></i></span><div>30-day returns<small class="d-block fw-normal text-secondary">Tail-wag guarantee</small></div></div></div>
    <div class="col"><div class="h-100 d-flex align-items-center gap-2 bg-white border rounded-4 p-2 small fw-bold lh-sm"><span class="icon-box rounded-3 d-flex align-items-center justify-content-center fs-5 tone-0"><i class="bi bi-lock"></i></span><div>Simple ordering<small class="d-block fw-normal text-secondary">Send an inquiry, we confirm</small></div></div></div>
  </div>

  <section class="mt-5">
    <h2 class="fw-bold">Shop by pet</h2>
    <p class="small text-secondary">Pick your companion — we'll filter everything.</p>
    <div id="petList" class="row row-cols-3 g-2"></div>
  </section>

  <section id="catalogs" class="mt-5">
    <h2 class="fw-bold">Shop by catalog</h2>
    <p id="catalogCount" class="small text-secondary"></p>
    <div id="catalogList" class="row row-cols-2 row-cols-md-4 g-2"></div>
    <button id="catalogToggle" class="btn btn-light border rounded-pill fw-bold d-block mx-auto mt-3 px-4" onclick="toggleCatalogs()"></button>
  </section>

  <section id="shop" class="mt-5">
    <div class="d-flex justify-content-between align-items-end mb-3">
      <div>
        <h2 id="shopTitle" class="fw-bold mb-0">Best sellers</h2>
        <p id="shopCount" class="small text-secondary mb-0"></p>
      </div>
      <button class="btn btn-link link-dark fw-bold small p-0" onclick="resetFilters()">Clear filters</button>
    </div>
    <div class="d-flex gap-2 overflow-auto pb-3">
      <select id="catFilter" class="form-select rounded-pill w-auto flex-shrink-0" aria-label="Catalog"></select>
      <select id="petFilter" class="form-select rounded-pill w-auto flex-shrink-0" aria-label="Pet">
        <option value="All">All pets</option><option>Dogs</option><option>Cats</option><option>Birds</option>
      </select>
      <select id="sortFilter" class="form-select rounded-pill w-auto flex-shrink-0" aria-label="Sort">
        <option value="pop">Featured</option><option value="lo">Price: low to high</option><option value="hi">Price: high to low</option>
      </select>
    </div>
    <div id="grid" class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-3"></div>
    <button id="moreBtn" class="btn btn-light border rounded-pill fw-bold d-block mx-auto mt-3 px-4" onclick="showMore()"></button>
  </section>

  <div class="banner grad-g text-white rounded-5 p-4 p-md-5 mt-4 position-relative overflow-hidden">
    <span class="mono">Bundle &amp; save</span>
    <h2 class="display-6 fw-bold my-2" style="max-width: 12ch;">Build your starter kit — save 30%</h2>
    <p class="opacity-75" style="max-width: 30ch;">Food, toy and bed tailored to your pet's age and size. Packed at the harbor, delivered together.</p>
    <button class="btn btn-light rounded-pill px-4 py-2 fw-bold mt-2" onclick="filterPet('Dogs')">Build my kit <i class="bi bi-arrow-right"></i></button>
    <img src="<?= BASE_URL ?>/assets/images/pet-dogs.jpg" alt="" class="position-absolute object-fit-cover rounded-4 border border-4 border-white d-none d-sm-block" style="right: -10px; bottom: -10px; width: 170px; height: 170px; transform: rotate(5deg);">
  </div>
  <div class="banner grad-n text-white rounded-5 p-4 p-md-5 mt-3">
    <span class="mono">New in</span>
    <h2 class="display-6 fw-bold my-2" style="max-width: 12ch;">Calm &amp; comfy beds</h2>
    <p class="opacity-75" style="max-width: 30ch;">Soft, washable and made for the best naps on deck.</p>
    <button class="btn btn-light rounded-pill px-4 py-2 fw-bold mt-2" onclick="filterCat('Bed & Carrier')">Shop beds <i class="bi bi-arrow-right"></i></button>
  </div>
</main>
