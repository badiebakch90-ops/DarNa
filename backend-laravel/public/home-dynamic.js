document.addEventListener('DOMContentLoaded', async () => {
  const { apiGet, escapeHtml, formatMoney, propertyLink, ratingStars } = window.DarNaSite;

  const collectionsGrid = document.getElementById('collectionsGrid');
  const categoriesGrid = document.getElementById('categoriesGrid');
  const staysGrid = document.getElementById('staysGrid');
  const searchForm = document.getElementById('searchForm');
  const destinationInput = document.getElementById('destinationInput');
  const typeInput = document.getElementById('typeInput');
  const guestsInput = document.getElementById('guestsInput');
  const listingFeedback = document.getElementById('listingFeedback');
  const mobileSearchForm = document.getElementById('mobileSearchForm');
  const mobileDestinationInput = document.getElementById('mobileDestinationInput');
  const mobileDestinationSuggestions = document.getElementById('mobileDestinationSuggestions');
  const mobileTypeSuggestions = document.getElementById('mobileTypeSuggestions');
  const mobileGuestsSummary = document.getElementById('mobileGuestsSummary');
  const mobileClearSearch = document.getElementById('mobileClearSearch');
  const mobileCitySections = document.getElementById('mobileCitySections');
  const mobileCollectionsRail = document.getElementById('mobileCollectionsRail');
  const mobileTabs = Array.from(document.querySelectorAll('[data-mobile-tab]'));
  const mobileSearchTrigger = document.getElementById('mobileSearchTrigger');
  const mobileSearchTriggerTitle = document.getElementById('mobileSearchTriggerTitle');
  const mobileSearchTriggerSummary = document.getElementById('mobileSearchTriggerSummary');
  const mobileSearchOverlay = document.getElementById('mobileSearchOverlay');
  const mobileSearchClose = document.getElementById('mobileSearchClose');
  const heroKicker = document.getElementById('heroKicker');
  const heroTitle = document.getElementById('heroTitle');
  const heroCopy = document.getElementById('heroCopy');
  const spotlightKicker = document.getElementById('spotlightKicker');
  const collectionsKicker = document.getElementById('collectionsKicker');
  const collectionsTitle = document.getElementById('collectionsTitle');
  const collectionsCopy = document.getElementById('collectionsCopy');
  const categoriesKicker = document.getElementById('categoriesKicker');
  const categoriesTitle = document.getElementById('categoriesTitle');
  const staysKicker = document.getElementById('staysKicker');
  const staysTitle = document.getElementById('staysTitle');
  const hostingKicker = document.getElementById('hostingKicker');
  const hostingTitle = document.getElementById('hostingTitle');
  const hostingCopy = document.getElementById('hostingCopy');
  const hostingTags = document.getElementById('hostingTags');
  const experienceKicker = document.getElementById('experienceKicker');
  const experienceTitle = document.getElementById('experienceTitle');
  const experienceCopy = document.getElementById('experienceCopy');
  const experienceStepOneCopy = document.getElementById('experienceStepOneCopy');
  const experienceStepTwoCopy = document.getElementById('experienceStepTwoCopy');
  const experienceStepThreeCopy = document.getElementById('experienceStepThreeCopy');
  const ctaKicker = document.getElementById('ctaKicker');
  const ctaTitle = document.getElementById('ctaTitle');
  const ctaCopy = document.getElementById('ctaCopy');

  let homePayload = null;
  let allProperties = [];
  let currentProperties = [];

  const arrowIcon = `
    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
      <path d="M5 12H19"></path>
      <path d="M13 6L19 12L13 18"></path>
    </svg>
  `;

  const heartIcon = `
    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
      <path d="M12 20.5C11.7 20.5 11.4 20.4 11.2 20.2L5.2 14.8C3.1 12.9 2 11.4 2 9.4C2 6.7 4 4.7 6.7 4.7C8.2 4.7 9.6 5.4 10.5 6.6C11.4 5.4 12.8 4.7 14.3 4.7C17 4.7 19 6.7 19 9.4C19 11.4 17.9 12.9 15.8 14.8L12.8 17.5"></path>
    </svg>
  `;

  const nearbyIcon = `
    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
      <path d="M21 3L10 14"></path>
      <path d="M21 3L14 21L10 14L3 10L21 3Z"></path>
    </svg>
  `;

  const cityIcon = `
    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
      <path d="M3 20H21"></path>
      <path d="M5 20V8L11 5V20"></path>
      <path d="M11 20V10L17 7V20"></path>
      <path d="M7.5 11.5H8.5"></path>
      <path d="M7.5 14.5H8.5"></path>
      <path d="M13.5 12.5H14.5"></path>
      <path d="M13.5 15.5H14.5"></path>
      <path d="M17 11H20V20"></path>
    </svg>
  `;

  const mobileCityDescriptions = {
    Marrakech: 'Pour les riads, patios et jardins',
    Rabat: 'Pour les adresses calmes et culturelles',
    Casablanca: 'Pour une escapade urbaine et design',
    Tanger: 'Ideal pour un week-end au bord de la mer',
    Agadir: 'Destination plage et soleil',
    Fes: 'Pour la medina et l architecture',
    Essaouira: 'Parfait pour une pause ocean et medina',
    Merzouga: 'Pour le desert, les dunes et le ciel ouvert',
  };

  const mobileCityVariants = ['is-indigo', 'is-sand', 'is-sea', 'is-rose', 'is-sage'];

  const typeMeta = {
    riad: { label: 'Riad', plural: 'riads', collection: 'patios et lanternes' },
    villa: { label: 'Villa', plural: 'villas', collection: 'terrasses, piscine et longues journees au soleil' },
    appartement: { label: 'Appartement', plural: 'appartements', collection: 'escapades urbaines bien situees' },
    maison: { label: 'Maison', plural: 'maisons', collection: 'sejours chaleureux a partager' },
    camp: { label: 'Camp', plural: 'camps', collection: 'nuits dans le desert et horizons ouverts' },
  };

  function capitalize(value) {
    if (!value) {
      return '';
    }

    return value.charAt(0).toUpperCase() + value.slice(1);
  }

  function normalizeType(value) {
    return String(value || '').trim().toLowerCase();
  }

  function formatHumanList(values) {
    const cleaned = values.filter(Boolean);

    if (!cleaned.length) {
      return '';
    }

    if (cleaned.length === 1) {
      return cleaned[0];
    }

    if (cleaned.length === 2) {
      return `${cleaned[0]} et ${cleaned[1]}`;
    }

    return `${cleaned.slice(0, -1).join(', ')} et ${cleaned[cleaned.length - 1]}`;
  }

  function getTypeMeta(type) {
    const normalized = normalizeType(type);

    return typeMeta[normalized] || {
      label: capitalize(normalized || 'Sejour'),
      plural: `${normalized || 'sejour'}s`,
      collection: 'adresses choisies selon les biens disponibles',
    };
  }

  function currentSearchQuery() {
    return destinationInput?.value.trim() || '';
  }

  function currentSearchType() {
    return normalizeType(typeInput?.value);
  }

  function currentGuests() {
    return Math.max(1, Number(guestsInput?.value || 2));
  }

  function getTypeEntries(properties) {
    const counts = properties.reduce((map, property) => {
      const type = normalizeType(property.type);
      if (!type) {
        return map;
      }

      map.set(type, (map.get(type) || 0) + 1);
      return map;
    }, new Map());

    return Array.from(counts.entries())
      .map(([type, count]) => ({
        type,
        count,
        meta: getTypeMeta(type),
      }))
      .sort((left, right) => {
        if (right.count !== left.count) {
          return right.count - left.count;
        }

        return left.meta.label.localeCompare(right.meta.label, 'fr');
      });
  }

  function getCityEntries(properties) {
    const counts = properties.reduce((map, property) => {
      const city = (property.city || property.location || '').trim();
      if (!city) {
        return map;
      }

      map.set(city, (map.get(city) || 0) + 1);
      return map;
    }, new Map());

    return Array.from(counts.entries())
      .map(([city, count]) => ({ city, count }))
      .sort((left, right) => {
        if (right.count !== left.count) {
          return right.count - left.count;
        }

        return left.city.localeCompare(right.city, 'fr');
      });
  }

  function averageRatingPercent(properties) {
    const ratings = properties
      .map(property => Number(property.rating || 0))
      .filter(rating => rating > 0);

    if (!ratings.length) {
      return homePayload?.stats?.satisfaction_rate || 98;
    }

    const average = ratings.reduce((sum, value) => sum + value, 0) / ratings.length;
    return Math.round((average / 5) * 100);
  }

  function buildNarrativeContext(properties) {
    const source = properties.length ? properties : allProperties;
    const typeEntries = getTypeEntries(source);
    const cityEntries = getCityEntries(source);

    return {
      source,
      typeEntries,
      cityEntries,
      topTypeEntry: typeEntries[0] || {
        type: '',
        count: source.length,
        meta: getTypeMeta('sejour'),
      },
      primaryCity: cityEntries[0]?.city || 'Maroc',
      cityList: cityEntries.slice(0, 3).map(entry => entry.city),
      query: currentSearchQuery(),
      selectedType: currentSearchType(),
    };
  }

  function syncSearchInputs(source = 'desktop') {
    if (!destinationInput || !mobileDestinationInput) {
      return;
    }

    if (source === 'mobile') {
      destinationInput.value = mobileDestinationInput.value;
    } else {
      mobileDestinationInput.value = destinationInput.value;
    }

    updateMobileSearchTrigger();
  }

  function formatRatingValue(value) {
    return Number(value || 0)
      .toFixed(2)
      .replace(/(\.\d)0$/, '$1')
      .replace(/\.00$/, '.0');
  }

  function updateMobileGuestsSummary() {
    if (!mobileGuestsSummary || !guestsInput) {
      return;
    }

    const guests = currentGuests();
    mobileGuestsSummary.textContent = `${guests} voyageur${guests > 1 ? 's' : ''}`;
  }

  function updateMobileSearchTrigger() {
    if (!mobileSearchTriggerTitle || !mobileSearchTriggerSummary) {
      return;
    }

    const query = currentSearchQuery();
    const selectedType = currentSearchType();
    const guests = currentGuests();
    const selectedTypeMeta = selectedType ? getTypeMeta(selectedType) : null;
    const summary = [];

    if (query) {
      summary.push(query);
    }

    if (selectedTypeMeta) {
      summary.push(selectedTypeMeta.label);
    }

    summary.push(`${guests} voyageur${guests > 1 ? 's' : ''}`);

    mobileSearchTriggerTitle.textContent = query || (selectedTypeMeta ? selectedTypeMeta.label : 'Rechercher');
    mobileSearchTriggerSummary.textContent = summary.length
      ? summary.join(' | ')
      : 'Destination, type de bien et voyageurs';
  }

  function openMobileSearch() {
    if (!mobileSearchOverlay) {
      return;
    }

    mobileSearchOverlay.hidden = false;
    document.body.classList.add('mobile-search-open');
    mobileSearchTrigger?.setAttribute('aria-expanded', 'true');

    window.setTimeout(() => {
      mobileDestinationInput?.focus();
    }, 50);
  }

  function closeMobileSearch() {
    if (!mobileSearchOverlay) {
      return;
    }

    mobileSearchOverlay.hidden = true;
    document.body.classList.remove('mobile-search-open');
    mobileSearchTrigger?.setAttribute('aria-expanded', 'false');
  }

  function stayCard(property) {
    const image = property.thumbnail_image || property.photos?.[0]?.image || '';
    const tags = Array.isArray(property.listing_tags) ? property.listing_tags.slice(0, 3) : [];

    return `
      <div class="col-lg-4 col-md-6">
        <article class="stay-card h-100">
          <div class="stay-media" style="background-image: linear-gradient(135deg, rgba(184,90,43,0.14), rgba(27,20,16,0.14)), url('${escapeHtml(image)}')"></div>
          <div class="stay-body d-flex flex-column h-100">
            <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
              <div>
                <div class="soft-copy small mb-1">${escapeHtml(property.location || property.city || '')}</div>
                <h3 class="font-display h2 mb-0">${escapeHtml(property.name)}</h3>
              </div>
              <span class="tiny-tag">${escapeHtml(property.type)}</span>
            </div>
            <p class="soft-copy mb-3">${escapeHtml(property.summary || '')}</p>
            <div class="d-flex flex-wrap gap-2 mb-3">
              ${tags.map(tag => `<span class="chip-tag data-pill">${escapeHtml(tag)}</span>`).join('')}
            </div>
            <div class="d-flex justify-content-between align-items-center mt-auto">
              <div>
                <div class="price-line">${escapeHtml(formatMoney(property.nightly_rate))} <span>/ nuit</span></div>
                <div class="soft-copy small">${escapeHtml(ratingStars(property.rating))} ${escapeHtml(String(property.rating || 0))} - ${escapeHtml(property.reviews_label || '')}</div>
              </div>
              <a class="primary-pill" href="${propertyLink(property.slug)}">Voir</a>
            </div>
          </div>
        </article>
      </div>
    `;
  }

  function mobileSectionHeader(title, copy, railId) {
    return `
      <div class="mobile-section-head">
        <div>
          <h2 class="mobile-section-title">${escapeHtml(title)}</h2>
          ${copy ? `<p class="mobile-section-copy">${escapeHtml(copy)}</p>` : ''}
        </div>
        <button class="mobile-section-arrow" type="button" data-rail-next="${escapeHtml(railId)}" aria-label="Faire defiler la section">
          ${arrowIcon}
        </button>
      </div>
    `;
  }

  function mobileStayCard(property) {
    const image = property.thumbnail_image || property.photos?.[0]?.image || '';
    const price = `${formatMoney(property.nightly_rate)} / nuit`;
    const rating = formatRatingValue(property.rating);
    const badge = property.featured ? 'Selection DarNa' : (property.type || 'Sejour');

    return `
      <article class="mobile-stay-card">
        <div class="mobile-stay-media" style="background-image: linear-gradient(180deg, rgba(20,14,11,0.05), rgba(20,14,11,0.18)), url('${escapeHtml(image)}')">
          <a class="mobile-stay-cover-link" href="${propertyLink(property.slug)}" aria-label="Voir ${escapeHtml(property.name)}"></a>
          <span class="mobile-stay-badge">${escapeHtml(badge)}</span>
          <button class="mobile-like-button" type="button" data-mobile-like aria-pressed="false" aria-label="Ajouter ${escapeHtml(property.name)} aux favoris">
            ${heartIcon}
          </button>
        </div>
        <div class="mobile-stay-copy">
          <h3 class="mobile-stay-name"><a href="${propertyLink(property.slug)}">${escapeHtml(property.name)}</a></h3>
          <div class="mobile-stay-place">${escapeHtml(property.location || property.city || 'Maroc')}</div>
          <div class="mobile-stay-meta">${escapeHtml(price)}</div>
          <div class="mobile-stay-rating"><strong>${escapeHtml(rating)}</strong> - ${escapeHtml(property.reviews_label || 'Aucun avis')}</div>
        </div>
      </article>
    `;
  }

  function collectionCard(item) {
    const property = item.property || {};

    return `
      <div class="col-lg-4">
        <article class="collection-card h-100">
          <div class="collection-media" style="background-image: linear-gradient(180deg, rgba(14,10,8,0.15), rgba(14,10,8,0.55)), url('${escapeHtml(item.image || property.thumbnail_image || '')}')"></div>
          <div class="p-4">
            <div class="d-flex flex-wrap gap-2 mb-3">
              ${(item.tags || []).map(tag => `<span class="tiny-tag">${escapeHtml(tag)}</span>`).join('')}
            </div>
            <div class="soft-copy small mb-2">${escapeHtml(item.city || property.city || '')}</div>
            <h3 class="font-display h1 mb-2">${escapeHtml(item.title || property.name || '')}</h3>
            <p class="soft-copy mb-4">${escapeHtml(item.copy || property.summary || '')}</p>
            <a class="primary-pill" href="${propertyLink(property.slug || item.slug)}">Ouvrir la collection</a>
          </div>
        </article>
      </div>
    `;
  }

  function mobileCollectionCard(item) {
    const property = item.property || {};
    const image = item.image || property.thumbnail_image || property.photos?.[0]?.image || '';
    const destination = property.slug || item.slug || '';

    return `
      <article class="mobile-collection-card">
        <a href="${propertyLink(destination)}">
          <div class="mobile-collection-media" style="background-image: linear-gradient(180deg, rgba(20,14,11,0.16), rgba(20,14,11,0.42)), url('${escapeHtml(image)}')">
            <span class="mobile-collection-badge">${escapeHtml(item.city || property.city || 'Maroc')}</span>
          </div>
        </a>
        <div class="mobile-collection-copy">
          <h3 class="mobile-collection-title"><a href="${propertyLink(destination)}">${escapeHtml(item.title || property.name || '')}</a></h3>
          <div class="mobile-collection-city">${escapeHtml(property.location || property.city || item.city || '')}</div>
          <p>${escapeHtml(item.copy || property.summary || '')}</p>
        </div>
      </article>
    `;
  }

  function renderMobileDestinationSuggestions(properties) {
    if (!mobileDestinationSuggestions) {
      return;
    }

    const source = properties.length ? properties : allProperties;
    const orderedCities = getCityEntries(source).map(entry => entry.city);
    const suggestions = [
      {
        query: '',
        title: 'A proximite',
        subtitle: 'Voir ce qui est disponible autour du Maroc',
        variant: 'is-blue',
        icon: nearbyIcon,
      },
      ...orderedCities.slice(0, 8).map((city, index) => ({
        query: city,
        title: `${city}, Maroc`,
        subtitle: mobileCityDescriptions[city] || 'Destination suggeree par les logements du projet',
        variant: mobileCityVariants[index % mobileCityVariants.length],
        icon: cityIcon,
      })),
    ];

    mobileDestinationSuggestions.innerHTML = suggestions.map(suggestion => `
      <button class="mobile-destination-button" type="button" data-mobile-destination="${escapeHtml(suggestion.query)}">
        <span class="mobile-destination-icon ${escapeHtml(suggestion.variant)}">
          ${suggestion.icon}
        </span>
        <span class="mobile-destination-copy">
          <strong>${escapeHtml(suggestion.title)}</strong>
          <span>${escapeHtml(suggestion.subtitle)}</span>
        </span>
      </button>
    `).join('');
  }

  function renderMobileTypeSuggestions(properties) {
    if (!mobileTypeSuggestions) {
      return;
    }

    const source = properties.length ? properties : allProperties;
    const entries = getTypeEntries(source);
    const selectedType = currentSearchType();

    mobileTypeSuggestions.innerHTML = entries.length
      ? entries.map(entry => `
        <button class="mobile-type-chip ${selectedType === entry.type ? 'is-active' : ''}" type="button" data-mobile-type="${escapeHtml(entry.type)}">
          <span>${escapeHtml(entry.meta.label)}</span>
          <small>${escapeHtml(String(entry.count))}</small>
        </button>
      `).join('')
      : '<div class="mobile-destination-empty">Aucun type de bien disponible.</div>';
  }

  function categoryCard(type, count) {
    const gradients = {
      riad: 'linear-gradient(135deg, rgba(184,90,43,0.18), rgba(141,61,23,0.22))',
      villa: 'linear-gradient(135deg, rgba(43,115,95,0.18), rgba(24,74,60,0.22))',
      appartement: 'linear-gradient(135deg, rgba(81,77,170,0.18), rgba(49,44,109,0.22))',
      maison: 'linear-gradient(135deg, rgba(165,128,64,0.18), rgba(129,88,35,0.22))',
      camp: 'linear-gradient(135deg, rgba(150,96,60,0.18), rgba(85,49,29,0.22))',
    };
    const meta = getTypeMeta(type);

    return `
      <div class="col-lg-3 col-md-6">
        <button class="category-card w-100 text-start ${currentSearchType() === type ? 'active' : ''}" data-category="${escapeHtml(type)}" style="background: ${gradients[type] || 'var(--panel)'}">
          <div class="soft-copy small mb-2 text-uppercase">${escapeHtml(meta.label)}</div>
          <div class="font-display display-6 mb-2">${escapeHtml(String(count))}</div>
          <div class="soft-copy mb-0">logements disponibles dans cette categorie</div>
        </button>
      </div>
    `;
  }

  function groupPropertiesByCity(properties) {
    const groups = properties.reduce((map, property) => {
      const key = (property.city || property.location || 'Maroc').trim() || 'Maroc';
      if (!map.has(key)) {
        map.set(key, []);
      }

      map.get(key).push(property);
      return map;
    }, new Map());

    return Array.from(groups.entries())
      .map(([city, items]) => ({
        city,
        items: [...items].sort((left, right) => {
          if (Boolean(right.featured) !== Boolean(left.featured)) {
            return Number(Boolean(right.featured)) - Number(Boolean(left.featured));
          }

          return Number(right.rating || 0) - Number(left.rating || 0);
        }),
      }))
      .sort((left, right) => {
        if (right.items.length !== left.items.length) {
          return right.items.length - left.items.length;
        }

        return left.city.localeCompare(right.city, 'fr');
      })
      .slice(0, 3);
  }

  function renderMobileCitySections(properties) {
    if (!mobileCitySections) {
      return;
    }

    if (!properties.length) {
      mobileCitySections.innerHTML = `
        <section class="mobile-browse-block">
          <div class="container">
            <div class="mobile-empty-card">Aucun logement ne correspond a cette recherche.</div>
          </div>
        </section>
      `;
      return;
    }

    const groups = groupPropertiesByCity(properties);
    const titles = [
      city => `Adresses populaires a ${city}`,
      city => `Escapades recommandees a ${city}`,
      city => `Sejours disponibles a ${city}`,
    ];
    const copies = [
      count => `${count} logement(s) a parcourir sur mobile.`,
      () => 'Une selection rapide a faire defiler avec un rendu plus proche d une application.',
      () => 'Des cartes reliees aux donnees du projet et accessibles en quelques gestes.',
    ];

    mobileCitySections.innerHTML = groups.map((group, index) => {
      const railId = `mobileCityRail${index + 1}`;
      const title = (titles[index] || titles[0])(group.city);
      const copy = (copies[index] || copies[0])(group.items.length);

      return `
        <section class="mobile-browse-block">
          <div class="container">
            ${mobileSectionHeader(title, copy, railId)}
            <div class="mobile-scroll-rail" id="${railId}">
              ${group.items.slice(0, 6).map(mobileStayCard).join('')}
            </div>
          </div>
        </section>
      `;
    }).join('');

    bindRailButtons();
  }

  function renderStats(properties) {
    const source = properties.length ? properties : allProperties;
    document.getElementById('statProperties').innerHTML = `${source.length}<span>+</span>`;
    document.getElementById('statCities').innerHTML = `${getCityEntries(source).length}<span>+</span>`;
    document.getElementById('statSatisfaction').innerHTML = `${averageRatingPercent(source)}<span>%</span>`;
  }

  function renderSpotlight(properties) {
    const source = properties.length ? properties : allProperties;
    const spotlight = source[0];

    if (!spotlight) {
      return;
    }

    const spotlightImage = spotlight.thumbnail_image || spotlight.photos?.[0]?.image || '';
    const spotlightMeta = getTypeMeta(spotlight.type);
    const spotlightTags = (spotlight.listing_tags || [])
      .slice(0, 2)
      .concat([spotlightMeta.label, spotlight.city || spotlight.location || 'Maroc'])
      .filter(Boolean)
      .slice(0, 4);

    document.getElementById('spotlightMedia').style.backgroundImage =
      `linear-gradient(180deg, rgba(14,10,8,0.12), rgba(14,10,8,0.55)), url('${spotlightImage}')`;
    document.getElementById('spotlightName').textContent = spotlight.name || 'DarNa';
    document.getElementById('spotlightCopy').textContent = spotlight.summary || '';
    document.getElementById('spotlightLocation').textContent = spotlight.location || spotlight.city || 'Maroc';
    document.getElementById('spotlightLink').href = propertyLink(spotlight.slug);
    document.getElementById('spotlightTags').innerHTML = spotlightTags
      .map(tag => `<span class="tiny-tag">${escapeHtml(tag)}</span>`)
      .join('');

    if (spotlightKicker) {
      spotlightKicker.textContent = `${spotlightMeta.label} en avant`;
    }
  }

  function renderTypeOptions(properties) {
    if (!typeInput) {
      return;
    }

    const source = properties.length ? properties : allProperties;
    const entries = getTypeEntries(source);
    const selected = currentSearchType();

    typeInput.innerHTML = [
      '<option value="">Tous</option>',
      ...entries.map(entry => `<option value="${escapeHtml(entry.type)}">${escapeHtml(entry.meta.label)} (${escapeHtml(String(entry.count))})</option>`)
    ].join('');

    if (selected && entries.some(entry => entry.type === selected)) {
      typeInput.value = selected;
    }
  }

  function buildCollectionEntries(properties) {
    const source = properties.length ? properties : allProperties;
    const sourceSlugs = new Set(source.map(property => property.slug));
    const blueprintCollections = Array.isArray(homePayload?.collections) ? homePayload.collections : [];
    const matchingCollections = blueprintCollections.filter(item => sourceSlugs.has(item.property?.slug || item.slug));
    const typeFallbacks = getTypeEntries(source)
      .map(entry => {
        const property = source.find(item => normalizeType(item.type) === entry.type);
        if (!property) {
          return null;
        }

        return {
          slug: property.slug,
          city: property.city || property.location || 'Maroc',
          title: capitalize(entry.meta.plural),
          copy: `${entry.count} ${entry.count > 1 ? entry.meta.plural : entry.meta.label.toLowerCase()} actuellement visibles, avec ${entry.meta.collection}.`,
          tags: [entry.meta.label, `${entry.count} disponible${entry.count > 1 ? 's' : ''}`],
          image: property.thumbnail_image || property.photos?.[0]?.image || '',
          property,
        };
      })
      .filter(Boolean);

    const merged = [];
    const seen = new Set();

    [...matchingCollections, ...typeFallbacks].forEach(item => {
      const slug = item?.property?.slug || item?.slug;
      if (!slug || seen.has(slug)) {
        return;
      }

      seen.add(slug);
      merged.push(item);
    });

    return merged.slice(0, 3);
  }

  function renderCollections(properties) {
    const entries = buildCollectionEntries(properties);

    collectionsGrid.innerHTML = entries.length
      ? entries.map(collectionCard).join('')
      : '<div class="col-12"><div class="empty-card p-4 rounded-4">Aucune collection disponible.</div></div>';

    if (mobileCollectionsRail) {
      mobileCollectionsRail.innerHTML = entries.length
        ? entries.map(mobileCollectionCard).join('')
        : '<div class="mobile-empty-card">Aucune inspiration disponible pour le moment.</div>';
    }

    bindRailButtons();
  }

  function renderCategories(properties) {
    const source = properties.length ? properties : allProperties;
    const entries = getTypeEntries(source);

    categoriesGrid.innerHTML = entries.length
      ? entries.map(entry => categoryCard(entry.type, entry.count)).join('')
      : '<div class="col-12"><div class="empty-card p-4 rounded-4">Aucune categorie disponible.</div></div>';
  }

  function renderNarrative(properties, resultCount) {
    const source = properties.length ? properties : allProperties;
    const context = buildNarrativeContext(source);
    const leadMeta = context.selectedType ? getTypeMeta(context.selectedType) : context.topTypeEntry.meta;
    const destinationLabel = context.query || context.primaryCity || 'le Maroc';
    const topTypes = formatHumanList(context.typeEntries.slice(0, 3).map(entry => entry.meta.plural));
    const topCities = formatHumanList(context.cityList);

    if (heroKicker) {
      heroKicker.textContent = context.query
        ? `Recherche: ${context.query}`
        : context.primaryCity !== 'Maroc'
          ? `Sejours a ${context.primaryCity}`
          : 'Sejours au Maroc';
    }

    if (heroTitle) {
      heroTitle.innerHTML = context.selectedType
        ? `Trouve des ${escapeHtml(leadMeta.plural)} pour <em>${escapeHtml(destinationLabel)}</em>`
        : `Trouve un sejour a <em>${escapeHtml(destinationLabel)}</em>`;
    }

    if (heroCopy) {
      heroCopy.textContent = `${source.length} logement(s) disponibles, avec une forte presence de ${topTypes || 'plusieurs styles de sejour'}${topCities ? ` a ${topCities}` : ''}.`;
    }

    if (collectionsKicker) {
      collectionsKicker.textContent = context.selectedType ? `${capitalize(leadMeta.plural)} a explorer` : 'Selections dynamiques';
    }

    if (collectionsTitle) {
      collectionsTitle.innerHTML = context.selectedType
        ? `Des idees de sejour autour des <em>${escapeHtml(leadMeta.plural)}</em>`
        : `Des selections inspirees des <em>${escapeHtml(context.topTypeEntry.meta.plural)}</em>`;
    }

    if (collectionsCopy) {
      collectionsCopy.textContent = 'Les cartes de cette section suivent les biens actuellement visibles sur la page.';
    }

    if (categoriesKicker) {
      categoriesKicker.textContent = 'Types disponibles';
    }

    if (categoriesTitle) {
      categoriesTitle.innerHTML = `Explore ${escapeHtml(String(context.typeEntries.length || 0))} styles de sejour <em>reellement presents</em>`;
    }

    if (staysKicker) {
      staysKicker.textContent = context.selectedType
        ? `${capitalize(leadMeta.plural)} disponibles`
        : context.query
          ? `Resultats pour ${context.query}`
          : 'Sejours disponibles';
    }

    if (staysTitle) {
      staysTitle.innerHTML = context.selectedType
        ? `Decouvre des ${escapeHtml(leadMeta.plural)} <em>adaptes a ton sejour</em>`
        : context.query
          ? `Decouvre des logements <em>autour de ${escapeHtml(context.query)}</em>`
          : 'Decouvre des logements <em>adaptes a ton sejour</em>';
    }

    if (hostingKicker) {
      hostingKicker.textContent = 'Espace hote';
    }

    if (hostingTitle) {
      hostingTitle.innerHTML = context.selectedType
        ? `Publie ton ${escapeHtml(leadMeta.label.toLowerCase())} et recois des demandes <em>de reservation</em>`
        : 'Publie ton bien et recois des demandes <em>de reservation</em>';
    }

    if (hostingCopy) {
      hostingCopy.textContent = `Les hotes peuvent publier ${topTypes || 'leurs logements'}, suivre leurs annonces et recevoir des notifications sur chaque nouvelle reservation.`;
    }

    if (hostingTags) {
      const tags = [
        ...context.typeEntries.slice(0, 3).map(entry => entry.meta.label),
        `${source.length} logements`,
        `${context.cityEntries.length} villes`,
        'Notifications'
      ];

      hostingTags.innerHTML = tags
        .map(tag => `<span class="tiny-tag">${escapeHtml(tag)}</span>`)
        .join('');
    }

    if (experienceKicker) {
      experienceKicker.textContent = context.primaryCity !== 'Maroc' ? `Sejourner a ${context.primaryCity}` : 'Pourquoi DarNa';
    }

    if (experienceTitle) {
      experienceTitle.innerHTML = `Un parcours adapte aux <em>${escapeHtml(context.topTypeEntry.meta.plural)}</em>`;
    }

    if (experienceCopy) {
      experienceCopy.textContent = `${source.length} biens consultables${topCities ? ` entre ${topCities}` : ''}, avec une navigation plus claire entre recherche, fiche et reservation.`;
    }

    if (experienceStepOneCopy) {
      experienceStepOneCopy.textContent = `Choisis parmi ${topTypes || 'plusieurs types de biens'}, puis ajuste la destination et la capacite selon ton voyage.`;
    }

    if (experienceStepTwoCopy) {
      experienceStepTwoCopy.textContent = `Chaque demande verifie les disponibilites et evite les chevauchements pour les ${leadMeta.plural}.`;
    }

    if (experienceStepThreeCopy) {
      experienceStepThreeCopy.textContent = 'Les hotes retrouvent leurs annonces, les reservations et les notifications depuis un espace de gestion dedie.';
    }

    if (ctaKicker) {
      ctaKicker.textContent = context.primaryCity !== 'Maroc' ? `Destinations: ${context.primaryCity}` : 'Plateforme DarNa';
    }

    if (ctaTitle) {
      ctaTitle.innerHTML = `Une plateforme claire pour reserver des <em>${escapeHtml(context.topTypeEntry.meta.plural)}</em>`;
    }

    if (ctaCopy) {
      ctaCopy.textContent = `${source.length} biens repartis${topCities ? ` entre ${topCities}` : ' sur plusieurs villes du Maroc'}, avec un espace hote et un backoffice admin pour gerer l activite.`;
    }

    if (!resultCount && currentSearchQuery()) {
      listingFeedback.textContent = '0 logement trouve pour cette recherche.';
    }
  }

  function renderHomeState(properties) {
    const source = properties.length ? properties : allProperties;

    renderStats(source);
    renderSpotlight(source);
    renderCollections(source);
    renderCategories(source);
    renderTypeOptions(source);
    renderMobileDestinationSuggestions(source);
    renderMobileTypeSuggestions(source);
    renderNarrative(source, properties.length);
  }

  function bindRailButtons() {
    document.querySelectorAll('[data-rail-next]').forEach(button => {
      if (button.dataset.bound === 'true') {
        return;
      }

      button.dataset.bound = 'true';
      button.addEventListener('click', () => {
        const rail = document.getElementById(button.dataset.railNext || '');
        if (!rail) {
          return;
        }

        rail.scrollBy({
          left: rail.clientWidth * 0.88,
          behavior: 'smooth',
        });
      });
    });
  }

  function initMobileTabs() {
    if (!mobileTabs.length) {
      return;
    }

    const setActiveTab = id => {
      mobileTabs.forEach(tab => {
        tab.classList.toggle('is-active', tab.getAttribute('href') === `#${id}`);
      });
    };

    mobileTabs.forEach(tab => {
      tab.addEventListener('click', event => {
        const target = document.querySelector(tab.getAttribute('href'));
        if (!target) {
          return;
        }

        event.preventDefault();
        setActiveTab(target.id);
        target.scrollIntoView({
          behavior: 'smooth',
          block: 'start',
        });
      });
    });

    if (!('IntersectionObserver' in window)) {
      return;
    }

    const observer = new IntersectionObserver(entries => {
      const visible = entries
        .filter(entry => entry.isIntersecting)
        .sort((left, right) => right.intersectionRatio - left.intersectionRatio)[0];

      if (visible) {
        setActiveTab(visible.target.id);
      }
    }, {
      threshold: [0.3, 0.6],
      rootMargin: '-30% 0px -45% 0px',
    });

    mobileTabs.forEach(tab => {
      const target = document.querySelector(tab.getAttribute('href'));
      if (target) {
        observer.observe(target);
      }
    });
  }

  async function loadHome() {
    try {
      const [homeResponse, propertiesResponse] = await Promise.all([
        apiGet('/home'),
        apiGet('/properties')
      ]);

      homePayload = homeResponse.data || {};
      allProperties = Array.isArray(propertiesResponse.data) ? propertiesResponse.data : [];
      currentProperties = [...allProperties];

      updateMobileGuestsSummary();
      updateMobileSearchTrigger();
      renderHomeState(currentProperties);
      renderProperties(currentProperties);
    } catch (error) {
      collectionsGrid.innerHTML = '<div class="col-12"><div class="danger-alert">Impossible de charger les donnees depuis Laravel.</div></div>';
      categoriesGrid.innerHTML = '';
      staysGrid.innerHTML = '';

      if (mobileDestinationSuggestions) {
        mobileDestinationSuggestions.innerHTML = '<div class="mobile-destination-empty">Impossible de charger les destinations suggerees.</div>';
      }

      if (mobileTypeSuggestions) {
        mobileTypeSuggestions.innerHTML = '<div class="mobile-destination-empty">Impossible de charger les types de biens.</div>';
      }

      if (mobileCollectionsRail) {
        mobileCollectionsRail.innerHTML = '<div class="mobile-empty-card">Impossible de charger les inspirations.</div>';
      }

      if (mobileCitySections) {
        mobileCitySections.innerHTML = `
          <section class="mobile-browse-block">
            <div class="container">
              <div class="mobile-empty-card">Impossible de charger les logements pour le moment.</div>
            </div>
          </section>
        `;
      }
    }
  }

  function renderProperties(properties) {
    currentProperties = properties;
    renderHomeState(properties);
    renderMobileCitySections(properties);

    if (!properties.length) {
      listingFeedback.textContent = '0 logement trouve pour cette recherche.';
      staysGrid.innerHTML = '<div class="col-12"><div class="empty-card p-4 rounded-4">Aucun logement ne correspond a cette recherche.</div></div>';
      return;
    }

    listingFeedback.textContent = `${properties.length} logement(s) disponible(s) pour cette recherche.`;
    staysGrid.innerHTML = properties.map(stayCard).join('');
  }

  async function performSearch(options = {}) {
    const { closeMobile = false } = options;

    try {
      updateMobileGuestsSummary();
      updateMobileSearchTrigger();
      staysGrid.innerHTML = '<div class="col-12"><div class="loading-state">Mise a jour des logements...</div></div>';

      if (mobileCitySections) {
        mobileCitySections.innerHTML = `
          <section class="mobile-browse-block">
            <div class="container">
              <div class="loading-state">Mise a jour des logements...</div>
            </div>
          </section>
        `;
      }

      const payload = await apiGet('/properties', {
        query: currentSearchQuery(),
        type: currentSearchType(),
        guests: currentGuests()
      });

      renderProperties(Array.isArray(payload.data) ? payload.data : []);

      if (closeMobile) {
        closeMobileSearch();
      }
    } catch (error) {
      listingFeedback.textContent = 'La recherche n a pas abouti. Reessaie dans un instant.';
      staysGrid.innerHTML = '<div class="col-12"><div class="danger-alert">La recherche n a pas abouti. Reessaie dans un instant.</div></div>';

      if (mobileCitySections) {
        mobileCitySections.innerHTML = `
          <section class="mobile-browse-block">
            <div class="container">
              <div class="mobile-empty-card">La recherche n a pas abouti. Reessaie dans un instant.</div>
            </div>
          </section>
        `;
      }
    }
  }

  document.addEventListener('click', event => {
    const closeTarget = event.target.closest('[data-mobile-search-close]');
    if (closeTarget) {
      closeMobileSearch();
      return;
    }

    const likeButton = event.target.closest('[data-mobile-like]');
    if (likeButton) {
      event.preventDefault();
      const pressed = likeButton.getAttribute('aria-pressed') === 'true';
      likeButton.setAttribute('aria-pressed', pressed ? 'false' : 'true');
      return;
    }

    const destinationButton = event.target.closest('[data-mobile-destination]');
    if (destinationButton) {
      const query = destinationButton.dataset.mobileDestination || '';
      destinationInput.value = query;
      mobileDestinationInput.value = query;
      updateMobileSearchTrigger();
      performSearch({ closeMobile: true });
      return;
    }

    const typeButton = event.target.closest('[data-mobile-type]');
    if (typeButton) {
      typeInput.value = typeButton.dataset.mobileType || '';
      updateMobileSearchTrigger();
      performSearch({ closeMobile: true });
      return;
    }

    const categoryButton = event.target.closest('[data-category]');
    if (categoryButton) {
      typeInput.value = categoryButton.dataset.category || '';
      updateMobileSearchTrigger();
      performSearch();
    }
  });

  searchForm?.addEventListener('submit', event => {
    event.preventDefault();
    syncSearchInputs('desktop');
    performSearch();
  });

  mobileSearchForm?.addEventListener('submit', event => {
    event.preventDefault();
    syncSearchInputs('mobile');
    performSearch({ closeMobile: true });
  });

  destinationInput?.addEventListener('input', () => {
    syncSearchInputs('desktop');
  });

  mobileDestinationInput?.addEventListener('input', () => {
    syncSearchInputs('mobile');
  });

  typeInput?.addEventListener('change', () => {
    updateMobileSearchTrigger();
  });

  guestsInput?.addEventListener('input', () => {
    updateMobileGuestsSummary();
    updateMobileSearchTrigger();
  });

  mobileClearSearch?.addEventListener('click', () => {
    destinationInput.value = '';
    mobileDestinationInput.value = '';
    typeInput.value = '';
    guestsInput.value = 2;
    updateMobileGuestsSummary();
    updateMobileSearchTrigger();
    performSearch();
  });

  mobileSearchTrigger?.addEventListener('click', openMobileSearch);
  mobileSearchClose?.addEventListener('click', closeMobileSearch);

  document.addEventListener('keydown', event => {
    if (event.key === 'Escape' && mobileSearchOverlay && !mobileSearchOverlay.hidden) {
      closeMobileSearch();
    }
  });

  initMobileTabs();
  await loadHome();
});
