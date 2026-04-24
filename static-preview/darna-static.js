/**
 * ─────────────────────────────────────────────────────────────
 *   DarNa — Plateforme de location authentique au Maroc 🇲🇦
 *   Author    : Abdelbadie Abkhich (@badiebakch90-ops)
 *   Original  : https://github.com/badiebakch90-ops/DarNa
 *   Copyright : © 2026 Abdelbadie Abkhich — All rights reserved
 *   License   : See LICENSE file
 * ─────────────────────────────────────────────────────────────
 */

// SECURITE: Le mot de passe admin n'est plus hardcode ici.
// Pour utiliser l'admin en local, cree un fichier "admin-config.local.js"
// (ignore par .gitignore et non deploye sur Netlify) avec ce contenu :
//
//   window.ADMIN_CONFIG = { username: "ton_user", password: "ton_password_fort" };
//
// Sur la version deployee, l'admin sera desactive automatiquement.
const ADMIN_CONFIG = window.ADMIN_CONFIG || null;

(() => {
  const THEME_KEY = "darna-theme";
  const LANGUAGE_KEY = "darna-language";
  const SESSION_KEY = "darnaAdminSession";
  const PROPERTY_STORAGE_KEY = "darnaProperties";
  const REQUEST_STORAGE_KEY = "darnaReservationRequests";
  const DEFAULT_LANGUAGE = "fr";
  const MOROCCO_CENTER = [31.7917, -7.0926];

  const TYPE_LABELS = {
    riad: { fr: "Riad", en: "Riad", ar: "رياض" },
    villa: { fr: "Villa", en: "Villa", ar: "فيلا" },
    apartment: { fr: "Appartement", en: "Apartment", ar: "شقة" },
    house: { fr: "Maison", en: "House", ar: "منزل" },
    studio: { fr: "Studio", en: "Studio", ar: "استوديو" },
    room: { fr: "Chambre", en: "Room", ar: "غرفة" },
    camp: { fr: "Camp", en: "Camp", ar: "مخيم" }
  };

  const TYPE_GRADIENTS = {
    riad: "linear-gradient(135deg, rgba(196, 98, 45, 0.9), rgba(97, 42, 18, 0.92))",
    villa: "linear-gradient(135deg, rgba(29, 158, 117, 0.88), rgba(22, 64, 53, 0.92))",
    apartment: "linear-gradient(135deg, rgba(83, 74, 183, 0.88), rgba(43, 37, 93, 0.92))",
    house: "linear-gradient(135deg, rgba(158, 75, 31, 0.88), rgba(98, 57, 24, 0.94))",
    studio: "linear-gradient(135deg, rgba(72, 111, 179, 0.88), rgba(35, 60, 108, 0.94))",
    room: "linear-gradient(135deg, rgba(145, 92, 58, 0.88), rgba(85, 50, 29, 0.92))",
    camp: "linear-gradient(135deg, rgba(176, 96, 64, 0.9), rgba(58, 32, 16, 0.94))"
  };

  const TYPE_DEFAULT_IMAGES = {
    riad: "https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?auto=format&fit=crop&w=1200&q=80",
    villa: "https://images.unsplash.com/photo-1505692952047-1a78307da8f2?auto=format&fit=crop&w=1200&q=80",
    apartment: "https://images.unsplash.com/photo-1494526585095-c41746248156?auto=format&fit=crop&w=1200&q=80",
    house: "https://images.unsplash.com/photo-1502672023488-70e25813eb80?auto=format&fit=crop&w=1200&q=80",
    studio: "https://images.unsplash.com/photo-1484154218962-a197022b5858?auto=format&fit=crop&w=1200&q=80",
    room: "https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?auto=format&fit=crop&w=1200&q=80",
    camp: "https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=1200&q=80"
  };

  const PRICE_SUFFIXES = {
    night: { fr: "/ nuit", en: "/ night", ar: "/ ليلة" },
    month: { fr: "/ mois", en: "/ month", ar: "/ شهر" }
  };

  const ADMIN_TRANSLATIONS = {
    fr: {
      theme_night_short: "Nuit",
      theme_day_short: "Jour",
      theme_night_label: "Activer le mode nuit",
      theme_day_label: "Activer le mode jour",
      login_brand: "DarNa Admin",
      login_kicker: "Espace admin",
      login_title: "Connecte-toi pour gerer les annonces",
      login_copy: "Utilise les identifiants definis dans ADMIN_CONFIG pour ouvrir le tableau de bord local.",
      username_label: "Nom d utilisateur",
      username_placeholder: "admin",
      password_label: "Mot de passe",
      password_placeholder: "Ton mot de passe admin",
      login_submit: "Se connecter",
      login_error: "Identifiants invalides. Verifie ADMIN_CONFIG dans darna-static.js.",
      login_session_chip: "Session fermee avec l onglet",
      login_hint: "La session est gardee dans sessionStorage et se ferme automatiquement avec l onglet.",
      back_home: "Retour a l accueil",
      dashboard_brand: "DarNa Dashboard",
      dashboard_kicker: "Gestion locale",
      dashboard_title: "Ajoute, supprime et pilote les annonces",
      dashboard_copy: "Les logements ajoutes ici sont enregistres dans localStorage sous la cle darnaProperties.",
      logout: "Deconnexion",
      add_panel_kicker: "Nouvelle annonce",
      add_panel_title: "Ajouter un bien",
      add_panel_copy: "Remplis les champs ci-dessous pour publier un logement sur la version statique de DarNa.",
      field_title: "Titre",
      field_title_placeholder: "Appartement lumineux Maarif",
      field_price: "Prix (MAD/mois)",
      field_price_placeholder: "6500",
      field_city: "Ville",
      field_city_placeholder: "Casablanca",
      field_neighborhood: "Quartier",
      field_neighborhood_placeholder: "Maarif",
      field_type: "Type",
      field_rooms: "Nombre de pieces",
      field_rooms_placeholder: "3",
      field_surface: "Surface (m2)",
      field_surface_placeholder: "96",
      field_image: "URL de l image",
      field_image_placeholder: "https://...",
      field_description: "Description",
      field_description_placeholder: "Appartement meuble, proche tramway, lumineux et calme.",
      add_submit: "Ajouter le bien",
      add_success: "{name} a ete ajoute a DarNa.",
      delete_success: "{name} a ete supprime.",
      list_kicker: "Biens ajoutes",
      list_title: "Catalogue admin DarNa",
      list_copy: "Le dashboard affiche le catalogue statique DarNa et les annonces ajoutees localement. Les annonces locales peuvent etre modifiees ou supprimees sans backend.",
      list_empty: "Le catalogue DarNa est vide pour le moment.",
      list_delete: "Supprimer",
      list_edit: "Editer",
      list_preview: "Apercu",
      list_price_suffix: "/ mois",
      list_rooms: "{count} piece(s)",
      list_surface: "{count} m2",
      list_location_fallback: "Maroc",
      list_source_base: "Catalogue DarNa",
      list_source_local: "Ajoute localement",
      list_source_demo: "UI de demo",
      add_cancel: "Annuler",
      update_submit: "Mettre a jour",
      add_editing: "Mode edition",
      add_editing_copy: "Le formulaire est pre-rempli. Modifie les champs puis enregistre pour mettre a jour l annonce locale.",
      add_editing_base_copy: "Cette annonce fait partie du catalogue statique. Tu peux reutiliser ses donnees pour creer une copie locale.",
      type_apartment: "Appartement",
      type_villa: "Villa",
      type_studio: "Studio",
      type_room: "Chambre",
      status_storage: "Stockage local actif",
      status_session: "Session admin active",
      edit_success: "{name} a ete mis a jour.",
      edit_notice_base: "Catalogue DarNa: tu peux dupliquer cette annonce dans ton espace local.",
      edit_notice_local: "{name} a ete charge dans le formulaire.",
      delete_blocked: "Seules les annonces locales peuvent etre supprimees depuis ce dashboard.",
      link_dashboard: "Ouvrir le dashboard",
      link_login: "Connexion admin"
    },
    en: {
      theme_night_short: "Night",
      theme_day_short: "Day",
      theme_night_label: "Enable dark mode",
      theme_day_label: "Enable light mode",
      login_brand: "DarNa Admin",
      login_kicker: "Admin area",
      login_title: "Sign in to manage listings",
      login_copy: "Use the credentials defined in ADMIN_CONFIG to open the local dashboard.",
      username_label: "Username",
      username_placeholder: "admin",
      password_label: "Password",
      password_placeholder: "Your admin password",
      login_submit: "Sign in",
      login_error: "Invalid credentials. Check ADMIN_CONFIG in darna-static.js.",
      login_session_chip: "Session closes with the tab",
      login_hint: "The session is kept in sessionStorage and closes automatically with the tab.",
      back_home: "Back to home",
      dashboard_brand: "DarNa Dashboard",
      dashboard_kicker: "Local management",
      dashboard_title: "Add, delete, and manage listings",
      dashboard_copy: "Listings created here are saved in localStorage under the darnaProperties key.",
      logout: "Logout",
      add_panel_kicker: "New listing",
      add_panel_title: "Add a property",
      add_panel_copy: "Fill in the fields below to publish a property on the static DarNa site.",
      field_title: "Title",
      field_title_placeholder: "Bright apartment in Maarif",
      field_price: "Price (MAD/month)",
      field_price_placeholder: "6500",
      field_city: "City",
      field_city_placeholder: "Casablanca",
      field_neighborhood: "Neighborhood",
      field_neighborhood_placeholder: "Maarif",
      field_type: "Type",
      field_rooms: "Number of rooms",
      field_rooms_placeholder: "3",
      field_surface: "Surface (m2)",
      field_surface_placeholder: "96",
      field_image: "Image URL",
      field_image_placeholder: "https://...",
      field_description: "Description",
      field_description_placeholder: "Furnished apartment, close to the tramway, bright and quiet.",
      add_submit: "Add property",
      add_success: "{name} has been added to DarNa.",
      delete_success: "{name} has been deleted.",
      list_kicker: "Added properties",
      list_title: "DarNa admin catalog",
      list_copy: "The dashboard shows the static DarNa catalog and the listings added locally. Local listings can be edited or deleted without a backend.",
      list_empty: "The DarNa catalog is empty right now.",
      list_delete: "Delete",
      list_edit: "Edit",
      list_preview: "Preview",
      list_price_suffix: "/ month",
      list_rooms: "{count} room(s)",
      list_surface: "{count} m2",
      list_location_fallback: "Morocco",
      list_source_base: "DarNa catalog",
      list_source_local: "Added locally",
      list_source_demo: "Demo UI",
      add_cancel: "Cancel",
      update_submit: "Update",
      add_editing: "Editing mode",
      add_editing_copy: "The form has been pre-filled. Update the fields and save to edit the local listing.",
      add_editing_base_copy: "This property belongs to the static catalog. You can reuse its data to create a local copy.",
      type_apartment: "Apartment",
      type_villa: "Villa",
      type_studio: "Studio",
      type_room: "Room",
      status_storage: "Local storage enabled",
      status_session: "Admin session active",
      edit_success: "{name} has been updated.",
      edit_notice_base: "DarNa catalog item: you can duplicate it into your local space.",
      edit_notice_local: "{name} has been loaded into the form.",
      delete_blocked: "Only locally added listings can be deleted from this dashboard.",
      link_dashboard: "Open dashboard",
      link_login: "Admin login"
    },
    ar: {
      theme_night_short: "ليل",
      theme_day_short: "نهار",
      theme_night_label: "تفعيل الوضع الليلي",
      theme_day_label: "تفعيل الوضع النهاري",
      login_brand: "DarNa Admin",
      login_kicker: "مساحة الادارة",
      login_title: "سجل الدخول لتدبير الاعلانات",
      login_copy: "استعمل بيانات الدخول الموجودة في ADMIN_CONFIG لفتح لوحة التحكم المحلية.",
      username_label: "اسم المستخدم",
      username_placeholder: "admin",
      password_label: "كلمة المرور",
      password_placeholder: "كلمة مرور الادارة",
      login_submit: "تسجيل الدخول",
      login_error: "بيانات الدخول غير صحيحة. تحقق من ADMIN_CONFIG داخل darna-static.js.",
      login_session_chip: "تنتهي الجلسة مع اغلاق التبويب",
      login_hint: "الجلسة محفوظة في sessionStorage وتغلق تلقائيا عند اغلاق التبويب.",
      back_home: "العودة الى الرئيسية",
      dashboard_brand: "DarNa Dashboard",
      dashboard_kicker: "تدبير محلي",
      dashboard_title: "اضف واحذف وادِر الاعلانات",
      dashboard_copy: "يتم حفظ العقارات المضافة هنا داخل localStorage تحت المفتاح darnaProperties.",
      logout: "تسجيل الخروج",
      add_panel_kicker: "اعلان جديد",
      add_panel_title: "اضافة عقار",
      add_panel_copy: "املأ الحقول التالية لنشر عقار في النسخة الثابتة من DarNa.",
      field_title: "العنوان",
      field_title_placeholder: "شقة مضيئة في المعاريف",
      field_price: "السعر (درهم/شهر)",
      field_price_placeholder: "6500",
      field_city: "المدينة",
      field_city_placeholder: "الدار البيضاء",
      field_neighborhood: "الحي",
      field_neighborhood_placeholder: "المعاريف",
      field_type: "النوع",
      field_rooms: "عدد الغرف",
      field_rooms_placeholder: "3",
      field_surface: "المساحة (م2)",
      field_surface_placeholder: "96",
      field_image: "رابط الصورة",
      field_image_placeholder: "https://...",
      field_description: "الوصف",
      field_description_placeholder: "شقة مفروشة وقريبة من الترام ومضيئة وهادئة.",
      add_submit: "اضافة العقار",
      add_success: "تمت اضافة {name} الى DarNa.",
      delete_success: "تم حذف {name}.",
      list_kicker: "العقارات المضافة",
      list_title: "فهرس DarNa الاداري",
      list_copy: "اللوحة تعرض الفهرس الثابت لـ DarNa والاعلانات المضافة محليا. يمكن تعديل او حذف الاعلانات المحلية بدون backend.",
      list_empty: "فهرس DarNa فارغ حاليا.",
      list_delete: "حذف",
      list_edit: "تعديل",
      list_preview: "معاينة",
      list_price_suffix: "/ شهر",
      list_rooms: "{count} غرفة",
      list_surface: "{count} م2",
      list_location_fallback: "المغرب",
      list_source_base: "فهرس DarNa",
      list_source_local: "مضاف محليا",
      list_source_demo: "واجهة تجريبية",
      add_cancel: "الغاء",
      update_submit: "تحديث",
      add_editing: "وضع التعديل",
      add_editing_copy: "تمت تعبئة النموذج. عدل الحقول ثم احفظ لتحديث الاعلان المحلي.",
      add_editing_base_copy: "هذا العقار من الفهرس الثابت. يمكنك اعادة استعمال معطياته لإنشاء نسخة محلية.",
      type_apartment: "شقة",
      type_villa: "فيلا",
      type_studio: "استوديو",
      type_room: "غرفة",
      status_storage: "التخزين المحلي مفعل",
      status_session: "جلسة الادارة مفعلة",
      edit_success: "تم تحديث {name}.",
      edit_notice_base: "عقار من فهرس DarNa: يمكنك نسخه في مساحتك المحلية.",
      edit_notice_local: "تم تحميل {name} في النموذج.",
      delete_blocked: "يمكن حذف الاعلانات المحلية فقط من هذه اللوحة.",
      link_dashboard: "فتح لوحة التحكم",
      link_login: "دخول الادارة"
    }
  };

  const BASE_PROPERTIES = [
    {
      slug: "riad-al-baraka",
      typeKey: "riad",
      type: "Riad",
      eyebrow: "Riad au coeur de la Medina",
      name: "Riad Al Baraka",
      city: "Marrakech",
      neighborhood: "Medina",
      location: "Marrakech, Medina",
      summary: "Un riad chaleureux organise autour d un patio vegetal, pense pour les voyageurs qui veulent vivre la Medina dans une ambiance calme et raffinee.",
      description: "Le Riad Al Baraka melange architecture traditionnelle, tadelakt, bois sculpte et coins lecture baignes de lumiere. La circulation s organise autour d un patio central avec bassin et salon exterieur.",
      story: "Les chambres s ouvrent sur des terrasses privees, et les espaces communs alternent entre ombre, fraicheur et vues sur les toits ocre de Marrakech. C est une adresse ideale pour un sejour en couple, en famille ou pour un petit groupe.",
      price: 850,
      priceUnit: "night",
      rating: 4.9,
      reviews: "128 avis",
      capacity: 6,
      facts: [
        ["Voyageurs", "6 personnes"],
        ["Chambres", "3 suites"],
        ["Salles de bain", "3 bains"],
        ["Surface", "210 m2"]
      ],
      listingTags: ["Patio fleuri", "Piscine chauffee", "Toit terrasse"],
      amenities: ["Patio fleuri", "Piscine chauffee", "Petit-dejeuner maison", "Toit terrasse", "Wi-Fi fibre", "Climatisation"],
      localSpots: [
        "A 6 minutes a pied de Jemaa el-Fna et des souks principaux.",
        "Quartier ideal pour decouvrir les artisans, les cafes discrets et les rooftops de la vieille ville.",
        "Acces facile en voiture jusqu au point de depose le plus proche de la ruelle."
      ],
      coords: [31.6258, -7.9892],
      mapLabel: "Ruelle calme, a quelques minutes des souks et de la place Jemaa el-Fna.",
      gradient: TYPE_GRADIENTS.riad,
      photos: [
        { label: "Patio", image: "https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?auto=format&fit=crop&w=1200&q=80" },
        { label: "Salon", image: "https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?auto=format&fit=crop&w=1200&q=80" },
        { label: "Suite", image: "https://images.unsplash.com/photo-1494526585095-c41746248156?auto=format&fit=crop&w=1200&q=80" },
        { label: "Terrasse", image: "https://images.unsplash.com/photo-1484154218962-a197022b5858?auto=format&fit=crop&w=1200&q=80" }
      ]
    },
    {
      slug: "villa-oasis-bleue",
      typeKey: "villa",
      type: "Villa",
      eyebrow: "Villa ouverte sur l ocean",
      name: "Villa Oasis Bleue",
      city: "Agadir",
      neighborhood: "Bord de mer",
      location: "Agadir, Bord de mer",
      summary: "Une villa lumineuse avec jardin, piscine et grandes baies vitrees pour vivre la cote d Agadir en toute tranquillite.",
      description: "La Villa Oasis Bleue s organise autour d une grande piece de vie qui relie salon, salle a manger et terrasse exterieure. Le jardin plante d essences mediterraneennes prolonge naturellement les espaces interieurs.",
      story: "Chaque chambre a ete pensee pour le repos avec des materiaux naturels, des tonalites sable et une circulation fluide vers l exterieur. La maison est parfaite pour un sejour au soleil en famille ou entre amis.",
      price: 2400,
      priceUnit: "night",
      rating: 4.8,
      reviews: "94 avis",
      capacity: 8,
      facts: [
        ["Voyageurs", "8 personnes"],
        ["Chambres", "4 chambres"],
        ["Salles de bain", "4 bains"],
        ["Surface", "340 m2"]
      ],
      listingTags: ["Piscine privee", "Vue mer", "Grand jardin"],
      amenities: ["Piscine privee", "Jardin tropical", "Cuisine equipee", "Coin barbecue", "Stationnement", "Vue mer"],
      localSpots: [
        "A quelques minutes de la corniche et des plages surveillees.",
        "Un secteur calme avec acces rapide aux restaurants et clubs de surf.",
        "Ideal pour alterner journees mer, golf et sorties en ville."
      ],
      coords: [30.4106, -9.6049],
      mapLabel: "Secteur residentiel a proximite de la plage, avec acces direct a la promenade d Agadir.",
      gradient: TYPE_GRADIENTS.villa,
      photos: [
        { label: "Piscine", image: "https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?auto=format&fit=crop&w=1200&q=80" },
        { label: "Terrasse", image: "https://images.unsplash.com/photo-1505692952047-1a78307da8f2?auto=format&fit=crop&w=1200&q=80" },
        { label: "Sejour", image: "https://images.unsplash.com/photo-1494526585095-c41746248156?auto=format&fit=crop&w=1200&q=80" },
        { label: "Suite", image: "https://images.unsplash.com/photo-1502672023488-70e25813eb80?auto=format&fit=crop&w=1200&q=80" }
      ]
    },
    {
      slug: "appartement-vue-mer",
      typeKey: "apartment",
      type: "Appartement",
      eyebrow: "Adresse urbaine et lumineuse",
      name: "Appartement Vue Mer",
      city: "Casablanca",
      neighborhood: "Maarif",
      location: "Casablanca, Maarif",
      summary: "Un appartement contemporain pour sejourner au centre de Casablanca, entre vie urbaine, balcon et horizon marin.",
      description: "L appartement offre un plan fluide, une belle luminosite et un balcon pour prendre l air en fin de journee. Les finitions sont sobres, confortables et pensees pour les courts comme les longs sejours.",
      story: "Le quartier du Maarif te place entre commerces, bureaux, cafes et acces rapides vers le front de mer. C est une base pratique pour explorer Casablanca sans sacrifier le confort.",
      price: 620,
      priceUnit: "night",
      rating: 4.7,
      reviews: "62 avis",
      capacity: 4,
      facts: [
        ["Voyageurs", "4 personnes"],
        ["Chambres", "2 chambres"],
        ["Salles de bain", "2 bains"],
        ["Surface", "118 m2"]
      ],
      listingTags: ["Balcon", "Parking", "Espace de travail"],
      amenities: ["Balcon", "Ascenseur", "Cuisine ouverte", "Wi-Fi rapide", "Parking", "Espace de travail"],
      localSpots: [
        "A quelques minutes des boutiques, restaurants et cafes du Maarif.",
        "Connexion rapide vers la corniche, Anfa et le tramway.",
        "Parfait pour un sejour professionnel ou un city break."
      ],
      coords: [33.5791, -7.6401],
      mapLabel: "Appartement situe dans le quartier du Maarif, proche des grands axes et des bonnes adresses de Casablanca.",
      gradient: TYPE_GRADIENTS.apartment,
      photos: [
        { label: "Salon", image: "https://images.unsplash.com/photo-1494526585095-c41746248156?auto=format&fit=crop&w=1200&q=80" },
        { label: "Balcon", image: "https://images.unsplash.com/photo-1505692952047-1a78307da8f2?auto=format&fit=crop&w=1200&q=80" },
        { label: "Chambre", image: "https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?auto=format&fit=crop&w=1200&q=80" },
        { label: "Cuisine", image: "https://images.unsplash.com/photo-1484154218962-a197022b5858?auto=format&fit=crop&w=1200&q=80" }
      ]
    },
    {
      slug: "dar-bleu-des-arts",
      typeKey: "house",
      type: "Maison",
      eyebrow: "Maison d artiste a Essaouira",
      name: "Dar Bleu des Arts",
      city: "Essaouira",
      neighborhood: "Medina",
      location: "Essaouira, Medina",
      summary: "Une maison de charme avec terrasse, hammam et details artisanaux, parfaite pour vivre Essaouira dans une ambiance douce et creative.",
      description: "Dar Bleu des Arts marie murs clairs, bois naturel, textiles sobres et touches d artisanat local. La circulation verticale mene vers une terrasse panoramique pensee pour les repas et les fins de journee.",
      story: "Le hammam prive et les salons intimistes donnent a cette maison une atmosphere contemplative. Elle conviendra aux voyageurs qui cherchent le calme tout en restant proches du port, des galeries et de la plage.",
      price: 980,
      priceUnit: "night",
      rating: 4.9,
      reviews: "211 avis",
      capacity: 5,
      facts: [
        ["Voyageurs", "5 personnes"],
        ["Chambres", "3 chambres"],
        ["Salles de bain", "2 bains"],
        ["Surface", "190 m2"]
      ],
      listingTags: ["Terrasse sur les toits", "Hammam", "Coin lecture"],
      amenities: ["Terrasse sur les toits", "Hammam", "Coin lecture", "Cuisine equipee", "Wi-Fi", "Cheminee decorative"],
      localSpots: [
        "A 4 minutes du port historique et des remparts d Essaouira.",
        "Environnement vivant avec artisans, galeries et coffee shops accessibles a pied.",
        "Un excellent point de depart pour les balades sur la plage et dans la Medina."
      ],
      coords: [31.5116, -9.7706],
      mapLabel: "Maison cachee dans les ruelles de la Medina, tout pres du port et des remparts.",
      gradient: TYPE_GRADIENTS.house,
      photos: [
        { label: "Salon", image: "https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?auto=format&fit=crop&w=1200&q=80" },
        { label: "Terrasse", image: "https://images.unsplash.com/photo-1505692952047-1a78307da8f2?auto=format&fit=crop&w=1200&q=80" },
        { label: "Chambre", image: "https://images.unsplash.com/photo-1502672023488-70e25813eb80?auto=format&fit=crop&w=1200&q=80" },
        { label: "Hammam", image: "https://images.unsplash.com/photo-1484154218962-a197022b5858?auto=format&fit=crop&w=1200&q=80" }
      ]
    },
    {
      slug: "riad-laaroussa",
      typeKey: "riad",
      type: "Riad",
      eyebrow: "Escapade patrimoniale a Fes",
      name: "Riad Laaroussa",
      city: "Fes",
      neighborhood: "Medina",
      location: "Fes, Medina",
      summary: "Un grand riad au decor zellige et aux volumes genereux, ideal pour vivre le patrimoine de Fes dans un cadre serein.",
      description: "Le riad met en avant les details architecturaux de Fes: fontaine centrale, stucs, menuiseries traditionnelles et salons a la fois ouverts et enveloppants.",
      story: "Chaque espace a ete imagine pour accueillir aussi bien un sejour familial qu un voyage plus contemplatif. Les terrasses offrent des vues superbes sur les toits de la Medina et la lumiere du matin.",
      price: 1100,
      priceUnit: "night",
      rating: 5.0,
      reviews: "47 avis",
      capacity: 10,
      facts: [
        ["Voyageurs", "10 personnes"],
        ["Chambres", "5 chambres"],
        ["Salles de bain", "5 bains"],
        ["Surface", "360 m2"]
      ],
      listingTags: ["Fontaine", "Terrasses", "Conciergerie"],
      amenities: ["Fontaine", "Terrasses", "Petit-dejeuner", "Suite familiale", "Wi-Fi", "Conciergerie"],
      localSpots: [
        "A proximite des medersas, fondouks et tanneries historiques.",
        "Le quartier permet de vivre Fes a pied, sans perdre la sensation de refuge au retour.",
        "Service de guide ou transfert facilement organisable depuis la Medina."
      ],
      coords: [34.0637, -4.9765],
      mapLabel: "Riad situe dans la Medina de Fes, proche des grands points d interet historiques.",
      gradient: TYPE_GRADIENTS.riad,
      photos: [
        { label: "Patio", image: "https://images.unsplash.com/photo-1484154218962-a197022b5858?auto=format&fit=crop&w=1200&q=80" },
        { label: "Salon", image: "https://images.unsplash.com/photo-1494526585095-c41746248156?auto=format&fit=crop&w=1200&q=80" },
        { label: "Suite", image: "https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?auto=format&fit=crop&w=1200&q=80" },
        { label: "Terrasse", image: "https://images.unsplash.com/photo-1505692952047-1a78307da8f2?auto=format&fit=crop&w=1200&q=80" }
      ]
    },
    {
      slug: "villa-cap-spartel",
      typeKey: "villa",
      type: "Villa",
      eyebrow: "Villa panoramique au nord",
      name: "Villa Cap Spartel",
      city: "Tanger",
      neighborhood: "Montagne",
      location: "Tanger, Montagne",
      summary: "Une villa de grand confort entre pins, lumiere atlantique et vues ouvertes sur le detroit, pour un sejour spacieux et elegant.",
      description: "La villa combine architecture ample, ouvertures vers l exterieur et materiaux doux. Les espaces de reception donnent directement sur la terrasse, la piscine et les vues lointaines.",
      story: "Le secteur du Cap Spartel permet de rester proche de la nature tout en rejoignant rapidement Tanger, les plages et les bonnes tables. La maison se prete aussi bien aux vacances familiales qu aux retraites creatives.",
      price: 1800,
      priceUnit: "night",
      rating: 4.8,
      reviews: "76 avis",
      capacity: 12,
      facts: [
        ["Voyageurs", "12 personnes"],
        ["Chambres", "6 chambres"],
        ["Salles de bain", "5 bains"],
        ["Surface", "420 m2"]
      ],
      listingTags: ["Piscine", "Vue detroit", "Grand jardin"],
      amenities: ["Piscine", "Vue detroit", "Grand jardin", "Salon exterieur", "Parking", "Cuisine familiale"],
      localSpots: [
        "A quelques minutes du phare du Cap Spartel et des plages sauvages.",
        "Vue ideale pour profiter des couchers de soleil sur l Atlantique.",
        "Bon equilibre entre intimite, air marin et acces rapide vers la ville."
      ],
      coords: [35.7855, -5.9368],
      mapLabel: "Villa nichee dans les hauteurs de Tanger, a proximite du Cap Spartel et des plages.",
      gradient: TYPE_GRADIENTS.villa,
      photos: [
        { label: "Piscine", image: "https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?auto=format&fit=crop&w=1200&q=80" },
        { label: "Terrasse", image: "https://images.unsplash.com/photo-1505692952047-1a78307da8f2?auto=format&fit=crop&w=1200&q=80" },
        { label: "Sejour", image: "https://images.unsplash.com/photo-1494526585095-c41746248156?auto=format&fit=crop&w=1200&q=80" },
        { label: "Suite", image: "https://images.unsplash.com/photo-1502672023488-70e25813eb80?auto=format&fit=crop&w=1200&q=80" }
      ]
    },
    {
      slug: "camp-merzouga-etoiles",
      typeKey: "camp",
      type: "Camp",
      eyebrow: "Nuit saharienne sous les etoiles",
      name: "Camp Merzouga Etoiles",
      city: "Merzouga",
      neighborhood: "Sahara",
      location: "Merzouga, Sahara",
      summary: "Un camp elegant au bord des dunes pour vivre Merzouga entre silence, ciel immense et hospitalite desertique.",
      description: "Le camp propose de grandes tentes amenagees avec literie confortable, coin salon, salle de bain privative et details artisanaux inspires du Sud marocain. Les espaces communs s ouvrent sur les dunes pour les repas, le the et les soirees autour du feu.",
      story: "Au lever du soleil, les couleurs changent sur l erg et le camp devient un point de depart ideal pour une balade en dromadaire, une excursion en 4x4 ou simplement une parenthese calme loin de la ville. C est une adresse pensee pour une experience immersive sans renoncer au confort.",
      price: 1350,
      priceUnit: "night",
      rating: 4.9,
      reviews: "83 avis",
      capacity: 4,
      facts: [
        ["Voyageurs", "4 personnes"],
        ["Tentes", "2 suites nomades"],
        ["Salles de bain", "2 bains"],
        ["Experience", "Feu de camp"]
      ],
      listingTags: ["Dunes", "Feu de camp", "Ciel ouvert"],
      amenities: ["Diner sous les etoiles", "Petit-dejeuner berbere", "Salle de bain privee", "Balade a dos de dromadaire", "Coin feu", "Transfert en 4x4"],
      localSpots: [
        "Installe au pied des dunes de l Erg Chebbi, a quelques minutes du village de Merzouga.",
        "Zone ideale pour admirer le coucher et le lever du soleil sans pollution lumineuse.",
        "Depart facile pour les excursions desert, musique gnawa et bivouacs prives."
      ],
      coords: [31.0994, -4.0121],
      mapLabel: "Camp situe pres des grandes dunes de Merzouga, avec acces direct a l Erg Chebbi.",
      gradient: TYPE_GRADIENTS.camp,
      photos: [
        { label: "Tente principale", image: "https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=1200&q=80" },
        { label: "Salon nomade", image: "https://images.unsplash.com/photo-1518509562904-e7ef99cdcc86?auto=format&fit=crop&w=1200&q=80" },
        { label: "Dunes", image: "https://images.unsplash.com/photo-1472396961693-142e6e269027?auto=format&fit=crop&w=1200&q=80" },
        { label: "Soiree au camp", image: "https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=1200&q=80" }
      ]
    }
  ];

  function clone(value) {
    return JSON.parse(JSON.stringify(value));
  }

  function escapeHtml(value) {
    return String(value ?? "")
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;")
      .replace(/'/g, "&#39;");
  }

  function formatMoney(value) {
    return `${new Intl.NumberFormat("fr-MA").format(Math.round(Number(value || 0)))} MAD`;
  }

  function slugify(value) {
    return String(value || "")
      .normalize("NFD")
      .replace(/[\u0300-\u036f]/g, "")
      .toLowerCase()
      .trim()
      .replace(/[^a-z0-9]+/g, "-")
      .replace(/^-+|-+$/g, "");
  }

  function normalizeType(value) {
    const cleaned = slugify(value);

    switch (cleaned) {
      case "appartement":
      case "apartment":
        return "apartment";
      case "villa":
        return "villa";
      case "studio":
        return "studio";
      case "chambre":
      case "room":
        return "room";
      case "maison":
      case "house":
        return "house";
      case "camp":
        return "camp";
      case "riad":
        return "riad";
      default:
        return cleaned || "apartment";
    }
  }

  function buildLocation(city, neighborhood) {
    const items = [city, neighborhood].filter(Boolean);
    return items.length ? items.join(", ") : "Maroc";
  }

  function summarizeText(text, maxLength = 150) {
    const cleaned = String(text || "").trim();
    if (cleaned.length <= maxLength) {
      return cleaned;
    }

    return `${cleaned.slice(0, maxLength).trim()}...`;
  }

  function getStorage(type) {
    try {
      return window[type] || null;
    } catch (error) {
      return null;
    }
  }

  function readJson(key, fallback) {
    const storage = getStorage("localStorage");
    if (!storage) {
      return fallback;
    }

    try {
      const raw = storage.getItem(key);
      return raw ? JSON.parse(raw) : fallback;
    } catch (error) {
      return fallback;
    }
  }

  function writeJson(key, value) {
    const storage = getStorage("localStorage");
    if (!storage) {
      return;
    }

    storage.setItem(key, JSON.stringify(value));
  }

  function getTheme() {
    const storage = getStorage("localStorage");
    const savedTheme = storage ? storage.getItem(THEME_KEY) : null;

    if (savedTheme === "dark" || savedTheme === "light") {
      return savedTheme;
    }

    try {
      return window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light";
    } catch (error) {
      return "light";
    }
  }

  function setTheme(theme) {
    const nextTheme = theme === "dark" ? "dark" : "light";
    const storage = getStorage("localStorage");

    if (storage) {
      storage.setItem(THEME_KEY, nextTheme);
    }

    document.documentElement.classList.toggle("dark", nextTheme === "dark");
    return nextTheme;
  }

  function getLanguage() {
    const storage = getStorage("localStorage");
    const savedLanguage = storage ? storage.getItem(LANGUAGE_KEY) : null;

    if (savedLanguage && ADMIN_TRANSLATIONS[savedLanguage]) {
      return savedLanguage;
    }

    return DEFAULT_LANGUAGE;
  }

  function setLanguage(language) {
    const nextLanguage = ADMIN_TRANSLATIONS[language] ? language : DEFAULT_LANGUAGE;
    const storage = getStorage("localStorage");

    if (storage) {
      storage.setItem(LANGUAGE_KEY, nextLanguage);
    }

    document.documentElement.lang = nextLanguage;
    document.documentElement.dir = nextLanguage === "ar" ? "rtl" : "ltr";
    return nextLanguage;
  }

  function applySavedPreferences() {
    setLanguage(getLanguage());
    setTheme(getTheme());
  }

  function formatTemplate(message, values = {}) {
    return Object.entries(values).reduce((result, [key, value]) => {
      return result.replaceAll(`{${key}}`, String(value));
    }, message);
  }

  function t(key, values = {}, language = getLanguage()) {
    const dictionary = ADMIN_TRANSLATIONS[language] || ADMIN_TRANSLATIONS[DEFAULT_LANGUAGE];
    const fallback = ADMIN_TRANSLATIONS[DEFAULT_LANGUAGE][key] || key;
    return formatTemplate(dictionary[key] || fallback, values);
  }

  function syncThemeButtons(root = document) {
    const isDark = document.documentElement.classList.contains("dark");

    root.querySelectorAll("[data-theme-toggle]").forEach(button => {
      const mode = button.getAttribute("data-theme-toggle") || "icon";

      if (mode === "text") {
        button.textContent = t(isDark ? "theme_day_short" : "theme_night_short");
      } else {
        button.innerHTML = isDark ? "&#9728;" : "&#9789;";
      }

      button.setAttribute("aria-label", t(isDark ? "theme_day_label" : "theme_night_label"));
      button.setAttribute("title", t(isDark ? "theme_day_label" : "theme_night_label"));
    });
  }

  function bindThemeButtons(root = document, onChange) {
    root.querySelectorAll("[data-theme-toggle]").forEach(button => {
      if (button.dataset.themeBound === "true") {
        return;
      }

      button.dataset.themeBound = "true";
      button.addEventListener("click", () => {
        setTheme(document.documentElement.classList.contains("dark") ? "light" : "dark");
        syncThemeButtons(root);

        if (typeof onChange === "function") {
          onChange();
        }
      });
    });

    syncThemeButtons(root);
  }

  function syncLanguageButtons(root = document) {
    const language = getLanguage();

    root.querySelectorAll("[data-language-option]").forEach(button => {
      const isActive = button.dataset.languageOption === language;
      button.classList.toggle("is-active", isActive);
      button.setAttribute("aria-pressed", isActive ? "true" : "false");
    });
  }

  function bindLanguageButtons(root = document, onChange) {
    root.querySelectorAll("[data-language-option]").forEach(button => {
      if (button.dataset.languageBound === "true") {
        return;
      }

      button.dataset.languageBound = "true";
      button.addEventListener("click", () => {
        setLanguage(button.dataset.languageOption || DEFAULT_LANGUAGE);
        syncLanguageButtons(root);
        syncThemeButtons(root);

        if (typeof onChange === "function") {
          onChange(getLanguage());
        }
      });
    });

    syncLanguageButtons(root);
  }

  function applyAdminTranslations(root = document) {
    const language = getLanguage();

    root.querySelectorAll("[data-i18n]").forEach(element => {
      element.textContent = t(element.dataset.i18n || "", {}, language);
    });

    root.querySelectorAll("[data-i18n-placeholder]").forEach(element => {
      element.setAttribute("placeholder", t(element.dataset.i18nPlaceholder || "", {}, language));
    });

    root.querySelectorAll("[data-i18n-title]").forEach(element => {
      element.setAttribute("title", t(element.dataset.i18nTitle || "", {}, language));
    });

    root.querySelectorAll("[data-i18n-aria-label]").forEach(element => {
      element.setAttribute("aria-label", t(element.dataset.i18nAriaLabel || "", {}, language));
    });
  }

  function ratingStars(value) {
    const rounded = Math.max(0, Math.min(5, Math.round(Number(value || 0))));
    return `${"\u2605".repeat(rounded)}${"\u2606".repeat(5 - rounded)}`;
  }

  function getTypeLabel(typeKey, language = DEFAULT_LANGUAGE) {
    const normalized = normalizeType(typeKey);
    const dictionary = TYPE_LABELS[normalized] || TYPE_LABELS.apartment;
    return dictionary[language] || dictionary.fr;
  }

  function getTypeGradient(typeKey) {
    return TYPE_GRADIENTS[normalizeType(typeKey)] || TYPE_GRADIENTS.apartment;
  }

  function getTypeDefaultImage(typeKey) {
    return TYPE_DEFAULT_IMAGES[normalizeType(typeKey)] || TYPE_DEFAULT_IMAGES.apartment;
  }

  function prepareCatalogProperty(property) {
    const nextProperty = clone(property);
    nextProperty.thumbnailImage = nextProperty.thumbnailImage || nextProperty.photos?.[0]?.image || "";
    nextProperty.typeKey = normalizeType(nextProperty.typeKey || nextProperty.type);
    nextProperty.priceUnit = nextProperty.priceUnit || "night";
    nextProperty.capacity = Number(nextProperty.capacity || 1);
    nextProperty.source = nextProperty.source || "base";
    return nextProperty;
  }

  function getBaseProperties() {
    return BASE_PROPERTIES.map(prepareCatalogProperty);
  }

  function getStoredProperties() {
    const items = readJson(PROPERTY_STORAGE_KEY, []);
    return Array.isArray(items) ? items : [];
  }

  function buildUniqueSlug(name, existingSlugs) {
    const baseSlug = slugify(name) || "annonce-darna";
    let nextSlug = baseSlug;
    let suffix = 2;

    while (existingSlugs.has(nextSlug)) {
      nextSlug = `${baseSlug}-${suffix}`;
      suffix += 1;
    }

    return nextSlug;
  }

  function normalizeStoredProperty(entry) {
    const typeKey = normalizeType(entry.type);
    const city = String(entry.city || "").trim();
    const neighborhood = String(entry.neighborhood || "").trim();
    const price = Math.max(0, Number(entry.price || 0));
    const rooms = Math.max(1, Number(entry.rooms || 1));
    const surface = Math.max(1, Number(entry.surface || 1));
    const name = String(entry.name || entry.title || "Annonce DarNa").trim();
    const description = String(entry.description || "").trim() || "Annonce ajoutee depuis le dashboard DarNa.";
    const location = buildLocation(city, neighborhood);
    const image = String(entry.imageUrl || "").trim() || getTypeDefaultImage(typeKey);
    const capacity = Math.max(1, Number(entry.capacity || rooms * 2));

    return {
      slug: entry.slug,
      source: "local",
      typeKey,
      type: getTypeLabel(typeKey, "fr"),
      eyebrow: "Annonce admin",
      name,
      city,
      neighborhood,
      location,
      summary: summarizeText(description, 155),
      description,
      story: `${name} est propose a partir de ${formatMoney(price)} par mois. Cette annonce a ete ajoutee depuis le dashboard DarNa pour ${location.toLowerCase()}.`,
      price,
      priceUnit: "month",
      rating: 0,
      reviews: "Nouvelle annonce",
      capacity,
      facts: [
        ["Pieces", `${rooms} piece${rooms > 1 ? "s" : ""}`],
        ["Surface", `${surface} m2`],
        ["Ville", city || "Maroc"],
        ["Quartier", neighborhood || "A definir"]
      ],
      listingTags: [getTypeLabel(typeKey, "fr"), city, neighborhood].filter(Boolean),
      amenities: ["Annonce admin", "Visite sur demande", "Contact direct"],
      localSpots: [
        neighborhood ? `Quartier: ${neighborhood}.` : "Quartier a definir.",
        city ? `Ville: ${city}.` : "Ville a definir.",
        `Surface disponible: ${surface} m2.`
      ],
      coords: clone(MOROCCO_CENTER),
      mapLabel: neighborhood ? `${neighborhood}, ${city || "Maroc"}` : location,
      gradient: getTypeGradient(typeKey),
      photos: [
        { label: "Photo principale", image }
      ],
      thumbnailImage: image,
      createdAt: entry.createdAt || null,
      rooms,
      surface
    };
  }

  function getDashboardProperties() {
    const baseItems = getBaseProperties().map(item => ({
      ...item,
      source: "base",
      imageUrl: item.thumbnailImage || item.photos?.[0]?.image || getTypeDefaultImage(item.typeKey),
      rooms: item.rooms || Number(String(item.facts?.[0]?.[1] || "").match(/\d+/)?.[0] || 1),
      surface: item.surface || Number(String(item.facts?.find(fact => /surface/i.test(String(fact?.[0] || "")))?.[1] || "").match(/\d+/)?.[0] || 1)
    }));

    const localItems = getStoredProperties().map(entry => {
      const normalized = normalizeStoredProperty(entry);
      return {
        ...normalized,
        source: "local",
        imageUrl: normalized.thumbnailImage || getTypeDefaultImage(normalized.typeKey),
        rooms: normalized.rooms || 1,
        surface: normalized.surface || 1
      };
    });

    return [...baseItems, ...localItems];
  }

  function getMergedProperties() {
    return [
      ...getBaseProperties(),
      ...getStoredProperties().map(normalizeStoredProperty)
    ];
  }

  function findPropertyBySlug(slug) {
    const properties = getMergedProperties();
    return properties.find(property => property.slug === slug) || null;
  }

  function getPropertyOrDefault(slug, fallbackSlug = "riad-al-baraka") {
    return findPropertyBySlug(slug) || findPropertyBySlug(fallbackSlug) || getMergedProperties()[0] || null;
  }

  function addProperty(payload) {
    const currentItems = getStoredProperties();
    const existingSlugs = new Set(getMergedProperties().map(property => property.slug));
    const title = String(payload.title || "").trim();
    const slug = buildUniqueSlug(title, existingSlugs);
    const nextEntry = {
      slug,
      name: title,
      price: Math.max(0, Number(payload.price || 0)),
      city: String(payload.city || "").trim(),
      neighborhood: String(payload.neighborhood || "").trim(),
      type: normalizeType(payload.type),
      rooms: Math.max(1, Number(payload.rooms || 1)),
      surface: Math.max(1, Number(payload.surface || 1)),
      imageUrl: String(payload.imageUrl || "").trim(),
      description: String(payload.description || "").trim(),
      createdAt: new Date().toISOString()
    };

    currentItems.unshift(nextEntry);
    writeJson(PROPERTY_STORAGE_KEY, currentItems);
    return normalizeStoredProperty(nextEntry);
  }

  function updateProperty(slug, payload) {
    const currentItems = getStoredProperties();
    const index = currentItems.findIndex(item => item.slug === slug);

    if (index === -1) {
      return null;
    }

    const currentItem = currentItems[index];
    const nextEntry = {
      ...currentItem,
      name: String(payload.title || currentItem.name || "").trim(),
      price: Math.max(0, Number(payload.price || currentItem.price || 0)),
      city: String(payload.city || currentItem.city || "").trim(),
      neighborhood: String(payload.neighborhood || currentItem.neighborhood || "").trim(),
      type: normalizeType(payload.type || currentItem.type),
      rooms: Math.max(1, Number(payload.rooms || currentItem.rooms || 1)),
      surface: Math.max(1, Number(payload.surface || currentItem.surface || 1)),
      imageUrl: String(payload.imageUrl || currentItem.imageUrl || "").trim(),
      description: String(payload.description || currentItem.description || "").trim(),
      updatedAt: new Date().toISOString()
    };

    currentItems[index] = nextEntry;
    writeJson(PROPERTY_STORAGE_KEY, currentItems);
    return normalizeStoredProperty(nextEntry);
  }

  function deleteProperty(slug) {
    const currentItems = getStoredProperties();
    const nextItems = currentItems.filter(item => item.slug !== slug);
    writeJson(PROPERTY_STORAGE_KEY, nextItems);
    return nextItems.length !== currentItems.length;
  }

  function saveReservationRequest(payload) {
    const currentItems = readJson(REQUEST_STORAGE_KEY, []);
    const nextItem = {
      id: `DRN-${String(Date.now()).slice(-8)}`,
      createdAt: new Date().toISOString(),
      ...payload
    };

    currentItems.unshift(nextItem);
    writeJson(REQUEST_STORAGE_KEY, currentItems);
    return nextItem;
  }

  function loginAdmin(username, password) {
    // Si ADMIN_CONFIG n'est pas charge (cas de la version deployee), on bloque tout
    if (!ADMIN_CONFIG || !ADMIN_CONFIG.username || !ADMIN_CONFIG.password) {
      return false;
    }
    const isValid = username === ADMIN_CONFIG.username && password === ADMIN_CONFIG.password;
    const storage = getStorage("sessionStorage");

    if (!isValid || !storage) {
      return false;
    }

    storage.setItem(SESSION_KEY, JSON.stringify({
      username,
      loggedAt: new Date().toISOString()
    }));

    return true;
  }

  function isAdminAuthenticated() {
    const storage = getStorage("sessionStorage");
    if (!storage) {
      return false;
    }

    return Boolean(storage.getItem(SESSION_KEY));
  }

  function logoutAdmin() {
    const storage = getStorage("sessionStorage");
    if (!storage) {
      return;
    }

    storage.removeItem(SESSION_KEY);
  }

  function propertyLink(slug) {
    return `property.html?slug=${encodeURIComponent(slug)}`;
  }

  function reservationLink(slug) {
    return `reservation.html?slug=${encodeURIComponent(slug)}`;
  }

  function getPriceSuffix(priceUnit, language = getLanguage()) {
    const key = priceUnit === "month" ? "month" : "night";
    return PRICE_SUFFIXES[key]?.[language] || PRICE_SUFFIXES[key].fr;
  }

  function getAdminTypeOptions(language = getLanguage()) {
    return ["apartment", "villa", "studio", "room"].map(value => ({
      value,
      label: getTypeLabel(value, language)
    }));
  }

  applySavedPreferences();

  window.DarNaStatic = {
    ADMIN_CONFIG,
    PROPERTY_STORAGE_KEY,
    REQUEST_STORAGE_KEY,
    SESSION_KEY,
    addProperty,
    applyAdminTranslations,
    applySavedPreferences,
    bindLanguageButtons,
    bindThemeButtons,
    deleteProperty,
    escapeHtml,
    findPropertyBySlug,
    formatMoney,
    getAdminTypeOptions,
    getBaseProperties,
    getDashboardProperties,
    getLanguage,
    getMergedProperties,
    getPriceSuffix,
    getPropertyOrDefault,
    getStoredProperties,
    getTheme,
    getTypeGradient,
    getTypeLabel,
    isAdminAuthenticated,
    loginAdmin,
    logoutAdmin,
    normalizeType,
    propertyLink,
    ratingStars,
    reservationLink,
    saveReservationRequest,
    setLanguage,
    setTheme,
    slugify,
    syncLanguageButtons,
    syncThemeButtons,
    t,
    updateProperty
  };
})();
