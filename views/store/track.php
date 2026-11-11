<main id="page-track" class="page container d-none">
  <div class="soft border rounded-5 p-4 p-md-5 mt-3">
    <span class="badge bg-white text-primary border rounded-pill mono fw-normal px-3 py-2">Track inquiry</span>
    <h1 class="display-4 fw-bold lh-1 my-3">Where's my <span class="text-primary">inquiry?</span></h1>
    <p class="text-secondary">Enter your inquiry number and phone number to see its status.</p>
  </div>
  <section class="card rounded-4 p-3 p-md-4 mt-2">
    <form method="post" action="<?= url('track') ?>#/track">
<?= csrf_field() ?>
      <label class="form-label fw-bold small">Inquiry number</label>
      <input name="track_id" type="number" min="1" class="form-control rounded-3 mb-3" required placeholder="e.g. 12" value="<?= e($_POST['track_id'] ?? '') ?>">
      <label class="form-label fw-bold small">Phone number</label>
      <input name="track_phone" class="form-control rounded-3 mb-3" required value="<?= e($_POST['track_phone'] ?? '') ?>">
      <button class="btn btn-primary rounded-pill px-4 py-2 fw-bold">Check status</button>
    </form>

    <?php if ($trackError): ?>
      <div class="alert alert-danger rounded-4 mt-3 mb-0"><?= e($trackError) ?></div>
    <?php endif; ?>

    <?php if ($track): ?>
      <div class="mt-4">
        <h3 class="fs-5 mb-3">Inquiry #<?= $track['inquiry_id'] ?> · <?= e($track['inquiry_type']) ?></h3>
        <div class="d-grid gap-2">
          <div class="d-flex gap-3 bg-white border rounded-4 p-3"><span class="icon-box rounded-circle text-white fw-bold d-flex align-items-center justify-content-center" style="background: var(--gold);">1</span><div><b>Inquiry received</b><div class="small text-secondary">Sent on <?= e($track['date_sent']) ?>.</div></div></div>
          <?php if ($track['status'] == 'Pending'): ?>
            <div class="d-flex gap-3 bg-white border rounded-4 p-3"><span class="icon-box rounded-circle bg-primary text-white fw-bold d-flex align-items-center justify-content-center">2</span><div><b>Waiting for review</b><div class="small text-secondary">Our staff will check stock and prices soon.</div></div></div>
          <?php else: ?>
            <div class="d-flex gap-3 bg-white border rounded-4 p-3"><span class="icon-box rounded-circle text-white fw-bold d-flex align-items-center justify-content-center" style="background: var(--gold);">2</span><div><b>Reviewed by our staff</b><div class="small text-secondary">Your inquiry has been checked.</div></div></div>
            <div class="d-flex gap-3 bg-white border rounded-4 p-3"><span class="icon-box rounded-circle bg-primary text-white fw-bold d-flex align-items-center justify-content-center">3</span><div><b><?= e($track['status']) ?></b><div class="small text-secondary"><?= $track['status'] == 'Approved' ? "Good news! We'll contact you to arrange your order." : 'Sorry, we could not fulfil this one. Please contact us for details.' ?></div></div></div>
          <?php endif; ?>
        </div>
      </div>
    <?php endif; ?>
  </section>
</main>
