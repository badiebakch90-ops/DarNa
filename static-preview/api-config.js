/**
 * ─────────────────────────────────────────────────────────────
 *   DarNa — Plateforme de location authentique au Maroc 🇲🇦
 *   Author    : Abdelbadie Abkhich (@badiebakch90-ops)
 *   Original  : https://github.com/badiebakch90-ops/DarNa
 *   Copyright : © 2026 Abdelbadie Abkhich — All rights reserved
 *   License   : See LICENSE file
 * ─────────────────────────────────────────────────────────────
 */

(() => {
  if (window.DARNA_API_BASE && window.DARNA_SITE_BASE) return;

  const localLaravelOrigin = 'http://127.0.0.1:8000';

  try {
    const currentOrigin = window.location.origin;
    const isLaravelOrigin = /^https?:\/\/(127\.0\.0\.1|localhost):8000$/i.test(currentOrigin);
    const siteBase = (isLaravelOrigin ? currentOrigin : localLaravelOrigin).replace(/\/$/, '');

    window.DARNA_SITE_BASE = siteBase;
    window.DARNA_API_BASE = `${siteBase}/api`;
  } catch {
    window.DARNA_SITE_BASE = localLaravelOrigin;
    window.DARNA_API_BASE = `${localLaravelOrigin}/api`;
  }
})();
