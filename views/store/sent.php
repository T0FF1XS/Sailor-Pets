<main class="container">
  <div class="row justify-content-center mt-5">
    <div class="col-md-6">
      <div class="card rounded-5 text-center">
        <div class="card-body py-5">
          <span class="icon-box rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center fs-4 mb-3" style="width: 56px; height: 56px;"><i class="bi bi-check-lg"></i></span>
          <h1 class="h3 fw-bold">Inquiry Sent</h1>
          <p class="mb-1">Your inquiry number is <strong>#<?= $sentId ?></strong>.</p>
          <p class="text-secondary">Our staff will review it and contact you using the phone number or email you provided. You can check its status on the Track page.</p>
          <a href="<?= url('home') ?>" class="btn btn-primary rounded-pill px-4 py-2 fw-bold">Back to shop</a>
        </div>
      </div>
    </div>
  </div>
</main>

<script>
try { sessionStorage.removeItem("sp_cart"); } catch (e) {}
function showCart() {}
function showFavs() {}
</script>
