(function () {
  const csrf = window.FOOD_BLOG?.csrf || document.querySelector('meta[name="csrf-token"]')?.content || '';
  const baseUrl = String(window.FOOD_BLOG?.baseUrl || '').replace(/\/$/, '');
  const toastZone = document.querySelector('#toastZone');

  function appUrl(path) {
    return `${baseUrl}/${String(path).replace(/^\/+/, '')}`;
  }

  function toast(message, type = 'success') {
    if (!toastZone) {
      return;
    }
    const note = document.createElement('div');
    note.className = `toast ${type}`;
    note.textContent = message;
    toastZone.appendChild(note);
    setTimeout(() => note.remove(), 3500);
  }

  function escapeHtml(value) {
    return String(value ?? '').replace(/[&<>"']/g, (char) => ({
      '&': '&amp;',
      '<': '&lt;',
      '>': '&gt;',
      '"': '&quot;',
      "'": '&#039;',
    }[char]));
  }

  function money(value) {
    return Number(value || 0).toFixed(2);
  }

  function numberedImage(prefix, id, count) {
    const number = (((Number(id) || 1) - 1) % count) + 1;
    return appUrl(`uploads/site/${prefix}-${number}.jpg`);
  }

  document.querySelectorAll('form[data-confirm]').forEach((form) => {
    form.addEventListener('submit', (event) => {
      if (!window.confirm(form.dataset.confirm)) {
        event.preventDefault();
      }
    });
  });

  document.querySelectorAll('[data-toggle-password]').forEach((button) => {
    button.addEventListener('click', () => {
      const field = button.closest('.password-field')?.querySelector('input');
      if (!field) {
        return;
      }

      const showing = field.type === 'text';
      field.type = showing ? 'password' : 'text';
      button.textContent = showing ? 'Show' : 'Hide';
      button.setAttribute('aria-label', showing ? 'Show password' : 'Hide password');
    });
  });

  document.querySelectorAll('form[data-validate]').forEach((form) => {
    form.addEventListener('submit', (event) => {
      const required = [...form.querySelectorAll('[required]')];
      const missing = required.find((field) => !String(field.value || '').trim());
      if (missing) {
        event.preventDefault();
        missing.focus();
        toast('Please complete all required fields.', 'error');
        return;
      }

      const email = form.querySelector('input[type="email"]');
      if (email && email.value && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value)) {
        event.preventDefault();
        email.focus();
        toast('Enter a valid email address.', 'error');
        return;
      }

      const matchField = form.querySelector('[data-match]');
      if (matchField) {
        const source = form.querySelector(`[name="${matchField.dataset.match}"]`);
        if (source && matchField.value !== source.value) {
          event.preventDefault();
          matchField.focus();
          toast('Passwords do not match.', 'error');
          return;
        }
      }

      const price = form.querySelector('input[name="price"]');
      if (price && Number(price.value) <= 0) {
        event.preventDefault();
        price.focus();
        toast('Price must be greater than zero.', 'error');
      }
    });
  });

  document.querySelectorAll('[data-search-form]').forEach((form) => {
    const results = form.closest('.panel')?.querySelector('[data-search-results]') || document.querySelector('[data-search-results]');
    let timer = null;

    async function runSearch() {
      const params = new URLSearchParams(new FormData(form));
      const response = await fetch(`${appUrl('api/search')}?${params.toString()}`);
      const data = await response.json();
      if (!response.ok || !data.ok) {
        toast(data.message || 'Search failed.', 'error');
        return;
      }
      if (!results) {
        return;
      }

      const restaurants = data.restaurants.map((restaurant) => {
        const href = `${appUrl('restaurants')}/${encodeURIComponent(restaurant.id)}`;
        return `
        <a class="search-result with-image" href="${href}">
          <img src="${numberedImage('restaurant', restaurant.id, 4)}" alt="">
          <span>
            <strong>${escapeHtml(restaurant.name)}</strong>
            <span>${escapeHtml(restaurant.location)} &middot; ${escapeHtml(restaurant.area)} &middot; ${escapeHtml(restaurant.menu_count)} items</span>
            <small>${escapeHtml(restaurant.short_background)}</small>
          </span>
        </a>
      `;
      }).join('');

      const items = data.items.map((item) => {
        const href = `${appUrl('menu-items')}/${encodeURIComponent(item.id)}`;
        const image = item.image_path ? appUrl(item.image_path) : numberedImage('menu', item.id, 8);
        return `
        <a class="search-result with-image" href="${href}">
          <img src="${image}" alt="">
          <span>
            <strong>${escapeHtml(item.name)} &middot; ${money(item.price)} BDT</strong>
            <span>${escapeHtml(item.restaurant_name)} &middot; ${escapeHtml(item.location)} &middot; ${escapeHtml(item.area)}</span>
            <small>${escapeHtml(item.description)}</small>
          </span>
        </a>
      `;
      }).join('');

      results.innerHTML = `
        <div class="search-section">
          <h3>Restaurants</h3>
          ${restaurants || '<p class="muted">No matching restaurants.</p>'}
        </div>
        <div class="search-section">
          <h3>Menu items</h3>
          ${items || '<p class="muted">No matching menu items.</p>'}
        </div>
      `;
    }

    form.addEventListener('input', () => {
      clearTimeout(timer);
      timer = setTimeout(runSearch, 250);
    });
    form.addEventListener('change', runSearch);
    runSearch();
  });

  document.querySelectorAll('[data-ajax-form]').forEach((form) => {
    form.addEventListener('submit', async (event) => {
      event.preventDefault();
      const submit = form.querySelector('button[type="submit"]');
      submit && (submit.disabled = true);
      try {
        const response = await fetch(form.action, {
          method: form.method || 'POST',
          body: new FormData(form),
          headers: { 'X-CSRF-Token': csrf },
        });
        const data = await response.json();
        if (!response.ok || !data.ok) {
          toast(data.message || 'Request failed.', 'error');
          return;
        }
        toast(data.message || 'Saved.');
        if (form.hasAttribute('data-reload')) {
          window.location.reload();
        } else {
          form.reset();
        }
      } catch (error) {
        toast('Network error. Try again.', 'error');
      } finally {
        submit && (submit.disabled = false);
      }
    });
  });

  document.addEventListener('click', async (event) => {
    const button = event.target.closest('[data-delete-url]');
    if (!button) {
      return;
    }
    event.preventDefault();
    if (!window.confirm('Delete this item?')) {
      return;
    }

    try {
      const response = await fetch(button.dataset.deleteUrl, {
        method: 'DELETE',
        headers: { 'X-CSRF-Token': csrf },
      });
      const data = await response.json();
      if (!response.ok || !data.ok) {
        toast(data.message || 'Delete failed.', 'error');
        return;
      }
      const target = document.querySelector(button.dataset.removeTarget || '');
      target && target.remove();
      toast(data.message || 'Deleted.');
    } catch (error) {
      toast('Network error. Try again.', 'error');
    }
  });
}());
