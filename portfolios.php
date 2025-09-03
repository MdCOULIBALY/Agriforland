<?php
// Charger les données du portfolio depuis le fichier JSON
$json_file = 'data/portfolio.json';
$portfolio_data = file_exists($json_file) ? json_decode(file_get_contents($json_file), true) : [];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="preconnect" href="https://cdn.tailwindcss.com">
  <link rel="preconnect" href="https://unpkg.com">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title data-i18n="title">AGRIFORLAND SARL - Portfolio</title>
  <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@400;700&family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
  <link rel="stylesheet" href="https://unpkg.com/@phosphor-icons/web@2.0.3/src/css/icons.css">
  <link rel="icon" href="images/favicon.ico" type="image/x-icon">
  <link href="css/Style.css" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/@phosphor-icons/web"></script>
  <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: {
            kanit: ['Kanit', 'sans-serif'],
            roboto: ['Roboto', 'sans-serif'],
          }
        }
      }
    }
  </script>
  <!-- Google tag (gtag.js) -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=G-ZKKVQJJCYG"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', 'G-ZKKVQJJCYG');
  </script>
</head>

<body class="bg-[#f6ffde] text-black">
   <!-- En tête -->
         <?php require __DIR__ . "/includes/header.php"; ?>

<!-- Bannière -->
<section class="relative">
  <img 
    src="cache/bgg-1-800.webp" 
    srcset="
      cache/bgg-1-480.webp 480w, 
      cache/bgg-1-800.webp 800w, 
      cache/bgg-1-1200.webp 1200w
    "
    sizes="(max-width: 600px) 480px, (max-width: 1000px) 800px, 1200px"
    alt="Portfolio background" 
    loading="lazy" 
    class="w-full h-[250px] sm:h-[300px] md:h-[400px] object-cover"
  >
  <div class="absolute top-0 left-0 w-full h-full bg-black/50 flex flex-col justify-center items-center text-center text-white px-3 sm:px-4">
    <h1 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-bold pb-3 sm:pb-4 md:pb-6" data-i18n="portfolio_title">Notre Portfolio</h1>
    <p class="text-base sm:text-lg md:text-xl font-roboto" data-i18n="portfolio_subtitle">Découvrez nos réalisations et projets marquants</p>
  </div>
</section>

<!-- Section Portfolio -->
<section class="py-8 sm:py-12 px-3 sm:px-4 max-w-7xl mx-auto bg-[#f6ffde]">
  <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold text-center mb-6 sm:mb-8 font-kanit" data-i18n="achievements">Nos Réalisations</h2>
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
    <?php if (!empty($portfolio_data)): ?>
      <?php foreach ($portfolio_data as $index => $item): ?>
        <div class="bg-white rounded-xl overflow-hidden shadow-md hover:shadow-lg transition-all duration-300 group">
          <div class="overflow-hidden">
            <img src="<?= htmlspecialchars($item['image'] ?: 'images/fallback.webp') ?>" loading="lazy" class="w-full h-48 sm:h-56 object-cover transition-transform duration-500 group-hover:scale-110 cursor-pointer portfolio-img" alt="<?= htmlspecialchars($item['title']) ?>" data-index="<?= $index ?>">
          </div>
          <div class="p-3 sm:p-4 group-hover:bg-gray-50 transition-colors duration-300">
            <h3 class="font-bold text-base sm:text-lg mb-2"><?= htmlspecialchars($item['title']) ?></h3>
            <p class="text-xs sm:text-sm text-gray-600 mb-2 line-clamp-3"><?= htmlspecialchars($item['description']) ?></p>
            <div class="space-y-1 text-xs sm:text-sm text-gray-500">
              <p><strong data-i18n="year">Année :</strong> <?= htmlspecialchars($item['year']) ?></p>
              <p><strong data-i18n="location">Lieu :</strong> <?= htmlspecialchars($item['location']) ?></p>
              <p><strong data-i18n="theme">Thème :</strong> <?= htmlspecialchars($item['theme']) ?></p>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p class="text-center text-gray-600 col-span-full" data-i18n="no_achievements">Aucun projet disponible pour le moment.</p>
    <?php endif; ?>
  </div>
</section>

<!-- Lightbox amélioré pour mobile -->
<div id="lightbox" class="fixed inset-0 bg-black/80 z-50 flex items-center justify-center hidden p-2 sm:p-4">
  <div class="relative max-w-4xl w-full h-full sm:h-auto flex flex-col">
    <div class="flex-1 flex items-center justify-center mb-2 sm:mb-4 overflow-hidden">
      <img id="lightbox-img" src="" alt="Image agrandie" loading="lazy" class="max-w-full max-h-[60vh] sm:max-h-[70vh] object-contain rounded-lg mx-auto">
    </div>
    
    <div id="lightbox-info" class="bg-white/95 p-3 sm:p-4 rounded-lg text-black">
      <h3 id="lightbox-title" class="font-bold text-base sm:text-lg mb-2"></h3>
      <p id="lightbox-description" class="text-xs sm:text-sm text-gray-600 mb-2"></p>
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-1 sm:gap-2 text-xs sm:text-sm text-gray-500">
        <p id="lightbox-year"></p>
        <p id="lightbox-location"></p>
        <p id="lightbox-theme"></p>
      </div>
    </div>
    
    <!-- Boutons de contrôle responsive -->
    <button id="lightbox-close" class="absolute top-2 sm:top-4 right-2 sm:right-4 bg-[#a9cf46] text-white p-2 sm:p-3 rounded-full hover:bg-[#759916] transition">
      <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
      </svg>
    </button>
    
    <button id="lightbox-prev" class="absolute left-2 sm:left-4 top-1/2 -translate-y-1/2 bg-[#a9cf46] text-white p-2 sm:p-3 rounded-full hover:bg-[#759916] transition">
      <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
      </svg>
    </button>
    
    <button id="lightbox-next" class="absolute right-2 sm:right-4 top-1/2 -translate-y-1/2 bg-[#a9cf46] text-white p-2 sm:p-3 rounded-full hover:bg-[#759916] transition">
      <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
      </svg>
    </button>
    
    <!-- Contrôles zoom - masqués sur mobile -->
    <button id="lightbox-zoom-in" class="absolute top-12 sm:top-16 right-2 sm:right-4 bg-[#a9cf46] text-white p-2 rounded-full hover:bg-[#759916] transition hidden sm:block">
      <svg class="w-4 h-4 sm:w-6 sm:h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v8m0 0v8m0-8h8m-8 0H4" />
      </svg>
    </button>
    
    <button id="lightbox-zoom-out" class="absolute top-20 sm:top-28 right-2 sm:right-4 bg-[#a9cf46] text-white p-2 rounded-full hover:bg-[#759916] transition hidden sm:block">
      <svg class="w-4 h-4 sm:w-6 sm:h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M4 12h16" />
      </svg>
    </button>
    
    <!-- Indicateur mobile -->
    <div class="absolute bottom-2 left-1/2 transform -translate-x-1/2 bg-black/50 text-white px-3 py-1 rounded-full text-xs sm:hidden" id="mobile-indicator">
      <span id="current-slide">1</span> / <span id="total-slides"><?= count($portfolio_data) ?></span>
    </div>
  </div>
</div>

<!-- Footer -->
    <?php require __DIR__ . "/includes/footer.php"; ?>


<!-- Scripts -->
<script>
  // Language translations
  const translations = {
    fr: {
      title: "AGRIFORLAND SARL - Portfolio",
      join_us: "Nous Rejoindre",
      contact_us: "Nous Contacter",
      home: "Accueil",
      about: "À Propos",
      poles: "Nos Pôles",
      projects: "Nos Projets",
      blog: "Blog",
      portfolios: "Portfolio",
      portfolio_title: "Notre Portfolio",
      portfolio_subtitle: "Découvrez nos réalisations et projets marquants",
      achievements: "Nos Réalisations",
      no_achievements: "Aucun projet disponible pour le moment.",
      year: "Année :",
      location: "Lieu :",
      theme: "Thème :",
      follow_us: "SUIVEZ-NOUS",
      useful_links: "Liens Utiles",
      recruitment: "Recrutement",
      consultant_recruitment: "Recrutement Consultant",
      our_group: "Notre Groupe",
      our_stories: "Nos Histoires",
      our_values: "Nos Valeurs",
      our_missions: "Nos Missions",
      our_teams: "Nos Équipes",
      our_ecofarms: "Nos Écofermes",
      others: "Autres",
      agroforestry: "Agroforesterie",
      mapping: "Cartographie",
      our_partners: "Nos Partenaires",
      newsletter: "Newsletter",
      your_email: "Votre email",
      subscribe: "S'inscrire",
      newsletter_success: "Merci pour votre inscription !",
      copyright: "© 2025 Agriforland. Tous droits réservés."
    },
    en: {
      title: "AGRIFORLAND SARL - Portfolio",
      join_us: "Join Us",
      contact_us: "Contact Us",
      home: "Home",
      about: "About",
      poles: "Our Divisions",
      projects: "Our Projects",
      blog: "Blog",
      portfolios: "Portfolio",
      portfolio_title: "Our Portfolio",
      portfolio_subtitle: "Discover our achievements and key projects",
      achievements: "Our Achievements",
      no_achievements: "No projects available at the moment.",
      year: "Year:",
      location: "Location:",
      theme: "Theme:",
      follow_us: "FOLLOW US",
      useful_links: "Useful Links",
      recruitment: "Recruitment",
      consultant_recruitment: "Consultant Recruitment",
      our_group: "Our Group",
      our_stories: "Our Stories",
      our_values: "Our Values",
      our_missions: "Our Missions",
      our_teams: "Our Teams",
      our_ecofarms: "Our Ecofarms",
      others: "Others",
      agroforestry: "Agroforestry",
      mapping: "Mapping",
      our_partners: "Our Partners",
      newsletter: "Newsletter",
      your_email: "Your email",
      subscribe: "Subscribe",
      newsletter_success: "Thank you for subscribing!",
      copyright: "© 2025 Agriforland. All rights reserved."
    }
  };

  // Lightbox variables (declared before updateContent)
  const lightbox = document.getElementById('lightbox');
  const lightboxImg = document.getElementById('lightbox-img');
  const lightboxTitle = document.getElementById('lightbox-title');
  const lightboxDescription = document.getElementById('lightbox-description');
  const lightboxYear = document.getElementById('lightbox-year');
  const lightboxLocation = document.getElementById('lightbox-location');
  const lightboxTheme = document.getElementById('lightbox-theme');
  const lightboxClose = document.getElementById('lightbox-close');
  const lightboxPrev = document.getElementById('lightbox-prev');
  const lightboxNext = document.getElementById('lightbox-next');
  const lightboxZoomIn = document.getElementById('lightbox-zoom-in');
  const lightboxZoomOut = document.getElementById('lightbox-zoom-out');
  const portfolioImages = document.querySelectorAll('.portfolio-img');
  const currentSlideEl = document.getElementById('current-slide');
  const totalSlidesEl = document.getElementById('total-slides');
  let currentIndex = 0;
  let zoomLevel = 1;

  const portfolioData = <?php echo json_encode($portfolio_data); ?>;

  // Language switcher
  const languageSelectors = document.querySelectorAll('#language-selector, #language-selector-mobile');
  const languageIcons = document.querySelectorAll('#language-icon, #language-icon-mobile');

  function updateContent(lang) {
    document.querySelectorAll('[data-i18n]').forEach(element => {
      const key = element.getAttribute('data-i18n');
      element.textContent = translations[lang][key] || element.textContent;
    });
    document.querySelectorAll('[data-i18n-placeholder]').forEach(element => {
      const key = element.getAttribute('data-i18n-placeholder');
      element.placeholder = translations[lang][key] || element.placeholder;
    });
    document.documentElement.lang = lang;
    languageIcons.forEach(icon => icon.src = `images/${lang}.webp`);
    languageSelectors.forEach(selector => selector.value = lang);
    // Update lightbox content if open
    if (lightbox && !lightbox.classList.contains('hidden')) {
      const item = portfolioData[currentIndex];
      lightboxYear.textContent = `${translations[lang].year} ${item.year}`;
      lightboxLocation.textContent = `${translations[lang].location} ${item.location}`;
      lightboxTheme.textContent = `${translations[lang].theme} ${item.theme}`;
    }
  }

  languageSelectors.forEach(selector => {
    selector.addEventListener('change', (e) => {
      const selectedLang = e.target.value;
      updateContent(selectedLang);
      localStorage.setItem('language', selectedLang);
    });
  });

  // Set default language
  const savedLang = localStorage.getItem('language') || 'fr';
  updateContent(savedLang);

  // Preloader
  window.addEventListener("load", function () {
    const preloader = document.getElementById('preloader');
    preloader.classList.add('opacity-0', 'pointer-events-none', 'transition-opacity', 'duration-500');
    setTimeout(() => preloader.remove(), 500);
  });

  // Toggle menu mobile
  const toggle = document.getElementById('menu-toggle');
  const menu = document.getElementById('mobile-menu');
  toggle.addEventListener('click', () => {
    menu.classList.toggle('hidden');
  });

  // Ajout de la classe active pour la page courante
  const currentPage = window.location.pathname.split("/").pop();
  document.querySelectorAll('.nav-link').forEach(link => {
    const href = link.getAttribute('href');
    if (href === currentPage) {
      link.classList.add('text-[#a9cf46]', 'border-b-2', 'border-[#a9cf46]', 'font-semibold');
    }
  });

  // Gestion du formulaire newsletter
  const newsletterForm = document.getElementById('newsletter-form');
  const newsletterMsg = document.getElementById('newsletter-msg');
  newsletterForm?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(newsletterForm);
    try {
      const response = await fetch('back/newsletter.php', {
        method: 'POST',
        body: formData
      });
      if (response.ok) {
        newsletterMsg.classList.remove('hidden');
        newsletterMsg.classList.remove('text-red-600');
        newsletterMsg.classList.add('text-green-600');
        newsletterMsg.textContent = translations[savedLang].newsletter_success;
        newsletterForm.reset();
      } else {
        throw new Error('Network response was not ok');
      }
    } catch (error) {
      newsletterMsg.classList.remove('hidden');
      newsletterMsg.classList.remove('text-green-600');
      newsletterMsg.classList.add('text-red-600');
      newsletterMsg.textContent = "Erreur lors de l'inscription.";
    }
  });

  // Gestion de la lightbox avec améliorations mobiles
  function updateMobileIndicator() {
    if (currentSlideEl && totalSlidesEl) {
      currentSlideEl.textContent = currentIndex + 1;
      totalSlidesEl.textContent = portfolioData.length;
    }
  }

  function openLightbox(index) {
    currentIndex = index;
    const item = portfolioData[currentIndex];
    lightboxImg.src = item.image || 'images/fallback.webp';
    lightboxImg.alt = item.title;
    lightboxImg.setAttribute('loading', 'lazy');
    lightboxTitle.textContent = item.title;
    lightboxDescription.textContent = item.description;
    lightboxYear.textContent = `${translations[savedLang].year} ${item.year}`;
    lightboxLocation.textContent = `${translations[savedLang].location} ${item.location}`;
    lightboxTheme.textContent = `${translations[savedLang].theme} ${item.theme}`;
    lightbox.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    zoomLevel = 1;
    lightboxImg.style.transform = `scale(${zoomLevel})`;
    updateMobileIndicator();
  }

  function closeLightbox() {
    lightbox.classList.add('hidden');
    document.body.style.overflow = '';
  }

  function showPrevImage() {
    currentIndex = (currentIndex - 1 + portfolioData.length) % portfolioData.length;
    const item = portfolioData[currentIndex];
    lightboxImg.src = item.image || 'images/fallback.webp';
    lightboxImg.alt = item.title;
    lightboxImg.setAttribute('loading', 'lazy');
    lightboxTitle.textContent = item.title;
    lightboxDescription.textContent = item.description;
    lightboxYear.textContent = `${translations[savedLang].year} ${item.year}`;
    lightboxLocation.textContent = `${translations[savedLang].location} ${item.location}`;
    lightboxTheme.textContent = `${translations[savedLang].theme} ${item.theme}`;
    zoomLevel = 1;
    lightboxImg.style.transform = `scale(${zoomLevel})`;
    updateMobileIndicator();
  }

  function showNextImage() {
    currentIndex = (currentIndex + 1) % portfolioData.length;
    const item = portfolioData[currentIndex];
    lightboxImg.src = item.image || 'images/fallback.webp';
    lightboxImg.alt = item.title;
    lightboxImg.setAttribute('loading', 'lazy');
    lightboxTitle.textContent = item.title;
    lightboxDescription.textContent = item.description;
    lightboxYear.textContent = `${translations[savedLang].year} ${item.year}`;
    lightboxLocation.textContent = `${translations[savedLang].location} ${item.location}`;
    lightboxTheme.textContent = `${translations[savedLang].theme} ${item.theme}`;
    zoomLevel = 1;
    lightboxImg.style.transform = `scale(${zoomLevel})`;
    updateMobileIndicator();
  }

  function zoomIn() {
    zoomLevel = Math.min(zoomLevel + 0.2, 3);
    lightboxImg.style.transform = `scale(${zoomLevel})`;
  }

  function zoomOut() {
    zoomLevel = Math.max(zoomLevel - 0.2, 0.5);
    lightboxImg.style.transform = `scale(${zoomLevel})`;
  }

  portfolioImages.forEach((img, index) => {
    img.addEventListener('click', () => openLightbox(index));
  });

  lightboxClose?.addEventListener('click', closeLightbox);
  lightboxPrev?.addEventListener('click', showPrevImage);
  lightboxNext?.addEventListener('click', showNextImage);
  lightboxZoomIn?.addEventListener('click', zoomIn);
  lightboxZoomOut?.addEventListener('click', zoomOut);

  // Fermer la lightbox en cliquant à l'extérieur
  lightbox?.addEventListener('click', (e) => {
    if (e.target === lightbox) {
      closeLightbox();
    }
  });

  // Support des gestes tactiles pour mobile
  let touchStartX = 0;
  let touchStartY = 0;

  lightbox?.addEventListener('touchstart', (e) => {
    touchStartX = e.touches[0].clientX;
    touchStartY = e.touches[0].clientY;
  });

  lightbox?.addEventListener('touchend', (e) => {
    if (!touchStartX || !touchStartY) return;
    
    const touchEndX = e.changedTouches[0].clientX;
    const touchEndY = e.changedTouches[0].clientY;
    const diffX = touchStartX - touchEndX;
    const diffY = touchStartY - touchEndY;
    
    if (Math.abs(diffX) > Math.abs(diffY) && Math.abs(diffX) > 50) {
      if (diffX > 0) {
        showNextImage();
      } else {
        showPrevImage();
      }
    }
    
    touchStartX = touchStartY = 0;
  });

  // Navigation avec les touches clavier
  document.addEventListener('keydown', (e) => {
    if (!lightbox.classList.contains('hidden')) {
      if (e.key === 'ArrowLeft') showPrevImage();
      if (e.key === 'ArrowRight') showNextImage();
      if (e.key === 'Escape') closeLightbox();
      if (e.key === '+') zoomIn();
      if (e.key === '-') zoomOut();
    }
  });
</script>
</body>
</html>