<!-- CONTACT -->
<section id="contact" class="tan">
  <div class="container text-center">
    <div class="row justify-content-center">
      <div class="col-lg-7">
        <h2 class="display-6 mb-3">Contact us</h2>
        <p class="mb-4">Ask about a vehicle, request a quote or book a viewing.</p>
        <button type="button" class="btn btn-rust btn-lg" data-bs-toggle="modal" data-bs-target="#contactModal">Contact us</button>
      </div>
    </div>
  </div>
</section>

<!-- CONTACT POPUP -->
<div class="modal fade" id="contactModal" tabindex="-1" aria-labelledby="contactModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content" style="border-radius:0">
      <div class="modal-header">
        <h2 class="modal-title h4" id="contactModalLabel">Contact us</h2>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="contactForm" class="d-flex flex-column overflow-hidden" novalidate>
        <div class="modal-body">
          <div id="formStatus" role="status" aria-live="polite"></div>
          <div class="row g-3">
            <div class="col-sm-6"><label for="f_name" class="form-label">First name</label>
              <input type="text" class="form-control" id="f_name" name="f_name" required maxlength="100"></div>
            <div class="col-sm-6"><label for="l_name" class="form-label">Last name</label>
              <input type="text" class="form-control" id="l_name" name="l_name" required maxlength="100"></div>
            <div class="col-12"><label for="email" class="form-label">Email</label>
              <input type="email" class="form-control" id="email" name="email" required maxlength="150"></div>
            <div class="col-12"><label for="contact_no" class="form-label">Contact number</label>
              <input type="tel" class="form-control" id="contact_no" name="contact_no" required maxlength="20"></div>
            <div class="col-12"><label for="subject" class="form-label">Subject</label>
              <input type="text" class="form-control" id="subject" name="subject" required maxlength="150"></div>
            <div class="col-12"><label for="message" class="form-label">Message</label>
              <textarea class="form-control" id="message" name="message" rows="4" required></textarea></div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-rust" id="sendBtn">Send message</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
const form = document.getElementById('contactForm');
const statusEl = document.getElementById('formStatus');
const sendBtn = document.getElementById('sendBtn');
const modalEl = document.getElementById('contactModal');

function showStatus(type, html) {
  statusEl.innerHTML = `<div class="alert alert-${type} mb-3">${html}</div>`;
  statusEl.scrollIntoView({ block: 'nearest' });
}

form.addEventListener('submit', async (e) => {
  e.preventDefault();
  form.classList.add('was-validated');

  const missing = [...form.elements]
    .filter(el => el.name && !el.checkValidity())
    .map(el => form.querySelector(`label[for="${el.id}"]`).textContent);

  if (missing.length) {
    showStatus('warning', `<strong>Missing or invalid:</strong> ${missing.join(', ')}.`);
    return;
  }

  sendBtn.disabled = true; sendBtn.textContent = 'Sending…';
  try {
    const res = await fetch('process_contact.php', { method: 'POST', body: new FormData(form) });
    const raw = await res.text();
    console.log('Server replied:', res.status, raw);
    const data = JSON.parse(raw);
    if (data.ok) {
      showStatus('success', '<strong>Message sent!</strong> Thank you, we will get back to you soon.');
      form.reset(); form.classList.remove('was-validated');
      setTimeout(() => bootstrap.Modal.getInstance(modalEl).hide(), 2500);
    } else {
      showStatus('danger', data.message);
      if (data.detail) console.error(data.detail);
    }
  } catch (err) {
    showStatus('danger', 'Could not reach the server. Make sure Apache is running and <code>process_contact.php</code> exists.');
    console.error(err);
  } finally {
    sendBtn.disabled = false; sendBtn.textContent = 'Send message';
  }
});

modalEl.addEventListener('show.bs.modal', () => {
  statusEl.innerHTML = '';
  form.classList.remove('was-validated');
});
</script>