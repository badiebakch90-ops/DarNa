window.DarNaSite = (() => {
  const config = window.darnaConfig || { apiBase: '/api' };

  function escapeHtml(value) {
    return String(value ?? '')
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#39;');
  }

  function formatMoney(value) {
    return `${new Intl.NumberFormat('fr-MA').format(Math.round(Number(value || 0)))} MAD`;
  }

  function ratingStars(value) {
    const rounded = Math.max(0, Math.min(5, Math.round(Number(value || 0))));
    return `${'\u2605'.repeat(rounded)}${'\u2606'.repeat(5 - rounded)}`;
  }

  async function apiGet(path, params = {}) {
    const url = new URL(`${config.apiBase}${path}`, window.location.origin);
    Object.entries(params).forEach(([key, value]) => {
      if (value !== undefined && value !== null && value !== '') {
        url.searchParams.set(key, value);
      }
    });

    const response = await fetch(url.toString(), {
      headers: {
        Accept: 'application/json',
      },
      credentials: 'same-origin',
    });

    const payload = await response.json();
    if (!response.ok) {
      throw new Error(payload?.message || `API ${response.status}`);
    }

    return payload;
  }

  async function apiPost(path, body) {
    const response = await fetch(`${config.apiBase}${path}`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        Accept: 'application/json',
      },
      credentials: 'same-origin',
      body: JSON.stringify(body),
    });

    const payload = await response.json();
    if (!response.ok) {
      throw new Error(payload?.message || `API ${response.status}`);
    }

    return payload;
  }

  function setTheme(theme) {
    const isDark = theme === 'dark';
    document.documentElement.classList.toggle('dark', isDark);
    localStorage.setItem('darna-theme', isDark ? 'dark' : 'light');

    document.querySelectorAll('[data-theme-toggle]').forEach(button => {
      button.innerHTML = isDark ? '&#9728;' : '&#9789;';
      button.setAttribute('aria-label', isDark ? 'Activer le mode jour' : 'Activer le mode nuit');
      button.setAttribute('title', isDark ? 'Activer le mode jour' : 'Activer le mode nuit');
    });
  }

  function initTheme() {
    const savedTheme = localStorage.getItem('darna-theme');
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    setTheme(savedTheme || (prefersDark ? 'dark' : 'light'));

    document.querySelectorAll('[data-theme-toggle]').forEach(button => {
      button.addEventListener('click', () => {
        const nextTheme = document.documentElement.classList.contains('dark') ? 'light' : 'dark';
        setTheme(nextTheme);
      });
    });
  }

  function openAuthWindow(url) {
    const width = 980;
    const height = 760;
    const left = Math.max(0, Math.round((window.screen.width - width) / 2));
    const top = Math.max(0, Math.round((window.screen.height - height) / 2));
    const features = [
      `width=${width}`,
      `height=${height}`,
      `left=${left}`,
      `top=${top}`,
      'resizable=yes',
      'scrollbars=yes',
    ].join(',');

    const authWindow = window.open(url, 'darna-auth-window', features);

    if (authWindow) {
      authWindow.focus();
      return;
    }

    window.open(url, '_blank', 'noopener');
  }

  function initAuthPrompts() {
    const modalElement = document.getElementById('guestAuthModal');

    if (!modalElement || !window.bootstrap) {
      return;
    }

    const modal = new window.bootstrap.Modal(modalElement);
    const titleElement = document.getElementById('guestAuthModalLabel');
    const copyElement = document.getElementById('guestAuthModalCopy');
    const loginButton = modalElement.querySelector('[data-auth-open="login"]');
    const registerButton = modalElement.querySelector('[data-auth-open="register"]');
    const defaultTitle = titleElement?.textContent || '';
    const defaultCopy = copyElement?.textContent || '';

    function setButtonUrl(button, url) {
      if (!button) {
        return;
      }

      const nextUrl = url || button.dataset.authUrl || '';
      button.dataset.authUrl = nextUrl;
      button.disabled = nextUrl === '';
      button.classList.toggle('d-none', nextUrl === '');
    }

    document.querySelectorAll('[data-auth-prompt]').forEach(trigger => {
      trigger.addEventListener('click', event => {
        event.preventDefault();

        if (titleElement) {
          titleElement.textContent = trigger.dataset.authTitle || defaultTitle;
        }

        if (copyElement) {
          copyElement.textContent = trigger.dataset.authCopy || defaultCopy;
        }

        setButtonUrl(loginButton, trigger.dataset.authLoginUrl || loginButton?.dataset.authUrl || '');
        setButtonUrl(registerButton, trigger.dataset.authRegisterUrl || registerButton?.dataset.authUrl || '');

        modal.show();
      });
    });

    modalElement.querySelectorAll('[data-auth-open]').forEach(button => {
      button.addEventListener('click', () => {
        const url = button.dataset.authUrl;

        if (!url) {
          return;
        }

        modal.hide();
        openAuthWindow(url);
      });
    });
  }

  function initHostingPhotoPreviews() {
    const previewGrid = document.querySelector('[data-hosting-photo-preview]');
    const coverInput = document.getElementById('cover_photo');
    const galleryInput = document.getElementById('gallery_photos');

    if (!previewGrid || !coverInput || !galleryInput) {
      return;
    }

    let objectUrls = [];

    function cleanupObjectUrls() {
      objectUrls.forEach(url => URL.revokeObjectURL(url));
      objectUrls = [];
    }

    function buildPreviewCard(file, label, isPrimary) {
      const objectUrl = URL.createObjectURL(file);
      objectUrls.push(objectUrl);

      const card = document.createElement('div');
      card.className = 'photo-preview-card';
      card.innerHTML = `
        <div class="photo-preview-media" style="background-image: linear-gradient(180deg, rgba(14,10,8,0.08), rgba(14,10,8,0.3)), url('${objectUrl}')">
          <span class="photo-preview-badge">${label}</span>
        </div>
        <div class="photo-preview-copy">
          <strong>${window.DarNaSite.escapeHtml(file.name)}</strong>
          <span>${isPrimary ? 'Photo principale' : 'Photo de galerie'}</span>
        </div>
      `;

      return card;
    }

    function renderPreview() {
      cleanupObjectUrls();

      const files = [];
      const coverFile = coverInput.files?.[0] || null;
      const galleryFiles = Array.from(galleryInput.files || []);

      if (coverFile) {
        files.push({
          file: coverFile,
          label: 'Principale',
          isPrimary: true,
        });
      }

      galleryFiles.forEach((file, index) => {
        files.push({
          file,
          label: `Galerie ${index + 1}`,
          isPrimary: false,
        });
      });

      previewGrid.innerHTML = '';

      if (files.length === 0) {
        previewGrid.innerHTML = `
          <div class="photo-preview-empty">
            Les photos choisies apparaitront ici avant la publication.
          </div>
        `;
        return;
      }

      files.slice(0, 9).forEach(({ file, label, isPrimary }) => {
        previewGrid.appendChild(buildPreviewCard(file, label, isPrimary));
      });
    }

    coverInput.addEventListener('change', renderPreview);
    galleryInput.addEventListener('change', renderPreview);
    window.addEventListener('beforeunload', cleanupObjectUrls);
    renderPreview();
  }

  function propertyLink(slug) {
    return `/stays/${encodeURIComponent(slug)}`;
  }

  function reservationLink(slug) {
    return `/reservation/${encodeURIComponent(slug)}`;
  }

  return {
    apiGet,
    apiPost,
    escapeHtml,
    formatMoney,
    initAuthPrompts,
    initHostingPhotoPreviews,
    initTheme,
    openAuthWindow,
    propertyLink,
    ratingStars,
    reservationLink,
  };
})();

document.addEventListener('DOMContentLoaded', () => {
  window.DarNaSite.initTheme();
  window.DarNaSite.initAuthPrompts();
  window.DarNaSite.initHostingPhotoPreviews();
});
