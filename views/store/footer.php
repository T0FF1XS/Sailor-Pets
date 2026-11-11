<footer class="bg-white border-top mt-5 pt-4" style="padding-bottom: 90px;">
  <div class="container">
    <div class="row g-4">
      <div class="col-12 col-md-5">
        <div class="d-flex align-items-center gap-2">
          <span class="icon-box rounded-3 bg-primary text-white d-flex align-items-center justify-content-center fs-5"><i class="bi bi-compass"></i></span>
          <span class="lh-1"><b class="brand d-block fs-6 text-brand">SAILOR PETS</b><small class="mono text-secondary" style="font-size: 9px;">Harborside Supply Co.</small></span>
        </div>
        <p class="small text-secondary mt-3" style="max-width: 34ch;">Modern pet care from the harbor. Vet-approved products, fast delivery and a whole lot of love — for every crew member.</p>
      </div>
      <div class="col-4 col-md-2 small">
        <h4 class="fs-6 fw-bold">Shop</h4>
        <a class="d-block text-secondary text-decoration-none py-1" href="#shop" onclick="filterPet('Dogs')">Dogs</a>
        <a class="d-block text-secondary text-decoration-none py-1" href="#shop" onclick="filterPet('Cats')">Cats</a>
        <a class="d-block text-secondary text-decoration-none py-1" href="#shop" onclick="filterPet('Birds')">Birds</a>
        <a class="d-block text-secondary text-decoration-none py-1" href="#catalogs">All catalogs</a>
      </div>
      <div class="col-4 col-md-2 small">
        <h4 class="fs-6 fw-bold">Company</h4>
        <a class="d-block text-secondary text-decoration-none py-1" href="#/about">About us</a>
        <a class="d-block text-secondary text-decoration-none py-1" href="#/contact">Contact us</a>
      </div>
      <div class="col-4 col-md-3 small">
        <h4 class="fs-6 fw-bold">Help</h4>
        <a class="d-block text-secondary text-decoration-none py-1" href="#/shipping">Shipping &amp; returns</a>
        <a class="d-block text-secondary text-decoration-none py-1" href="#/track">Track inquiry</a>
        <a class="d-block text-secondary text-decoration-none py-1" href="#/faq">FAQ</a>
      </div>
    </div>
    <div class="d-flex justify-content-between flex-wrap gap-2 border-top mt-4 pt-3 small text-secondary">
      <span>© 2026 Sailor Pets — Harborside Supply Co.</span>
      <span class="mono"><span class="border rounded-2 bg-mint text-brand px-2 py-1 ms-1">Cash</span><span class="border rounded-2 bg-mint text-brand px-2 py-1 ms-1">GCash</span></span>
    </div>
  </div>
</footer>

<button id="cartBar" class="btn btn-primary rounded-pill position-fixed start-50 translate-middle-x d-none d-flex justify-content-between fw-bold py-3 px-4 shadow" style="bottom: 14px; width: calc(100% - 28px); max-width: 480px; z-index: 1030;" onclick="showCart()"></button>

<div id="sheet" class="offcanvas offcanvas-bottom mx-auto rounded-top-5" tabindex="-1" style="max-width: 520px; height: auto; max-height: 88%; background: #F6F7F2;">
  <div id="sheetBody" class="offcanvas-body p-3"></div>
</div>

<div class="toast-container position-fixed start-50 translate-middle-x" style="bottom: 84px; z-index: 1090;">
  <div id="msgToast" class="toast text-bg-dark rounded-pill border-0" role="status" style="width: auto;">
    <div id="msgText" class="toast-body fw-bold px-4"></div>
  </div>
</div>

<script>
var baseUrl = "<?= BASE_URL ?>";
var csrf = "<?= csrf_token() ?>";
var products = <?= json_encode($products, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE | JSON_HEX_TAG | JSON_HEX_AMP) ?>;
var catalogs = <?= json_encode($catalogs, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE | JSON_HEX_TAG | JSON_HEX_AMP) ?>;
</script>
<script src="<?= BASE_URL ?>/assets/store.js"></script>
