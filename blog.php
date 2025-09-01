<?php
include('admin/includes/db.php'); // Inclure la connexion à la base de données

// Nombre d'articles par page
$articles_per_page = 6;

// Récupérer le numéro de la page actuelle, sinon la page 1 par défaut
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$current_page = max(1, $current_page); // Empêcher les pages négatives

// Calculer la position de départ pour la requête SQL
$start_from = ($current_page - 1) * $articles_per_page;

// Récupérer les paramètres de recherche
$search_term = isset($_GET['search']) ? trim($_GET['search']) : '';
$category = isset($_GET['category']) ? trim($_GET['category']) : '';

// Construire la requête SQL avec filtrage
$sql = "SELECT * FROM articles WHERE 1=1";
if (!empty($search_term)) {
    $sql .= " AND title LIKE '%" . $conn->real_escape_string($search_term) . "%'";
}
if (!empty($category)) {
    $sql .= " AND categorie LIKE '%" . $conn->real_escape_string($category) . "%'";
}
$sql .= " ORDER BY created_at DESC LIMIT $start_from, $articles_per_page";
$result = $conn->query($sql);

// Requête pour calculer le nombre total d'articles
$sql_count = "SELECT COUNT(*) AS total_articles FROM articles WHERE 1=1";
if (!empty($search_term)) {
    $sql_count .= " AND title LIKE '%" . $conn->real_escape_string($search_term) . "%'";
}
if (!empty($category)) {
    $sql_count .= " AND categorie LIKE '%" . $conn->real_escape_string($category) . "%'";
}
$result_count = $conn->query($sql_count);
$row = $result_count->fetch_assoc();
$total_articles = $row['total_articles'];

// Calculer le nombre total de pages
$total_pages = ceil($total_articles / $articles_per_page);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title data-i18n="page_title">AGRIFORLAND SARL - Blog</title>
  
  <!-- Preconnect optimisé -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="preconnect" href="https://cdn.tailwindcss.com">
  <link rel="preconnect" href="https://unpkg.com">
  
  <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@400;700&family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/@phosphor-icons/web"></script>
  <link href="css/Style.css" rel="stylesheet">
  <link rel="icon" href="images/favicon.ico" type="image/x-icon">
  
  <!-- Google tag (gtag.js) -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=G-ZKKVQJJCYG"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', 'G-ZKKVQJJCYG');
  </script>
</head>

<body class="bg-[#f6ffde] text-black font-roboto">
  <!-- Preloader -->
  <div id="preloader" class="fixed inset-0 bg-[#f6ffde] z-50 flex items-center justify-center">
    <div class="animate-triangle w-24 h-24 sm:w-32 sm:h-32">
      <img src="images/triangle-svgrepo-com.svg" loading="lazy" alt="" data-alt-i18n="loading" class="w-full h-full object-contain triangle-img">
    </div>
  </div>

  <!-- Header -->
  <header class="bg-white shadow-md sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-3 sm:px-4 py-3 flex items-center justify-between">
      <img 
        src="cache/logo-198x66-800.webp" 
        srcset="
          cache/logo-198x66-480.webp 480w, 
          cache/logo-198x66-800.webp 800w, 
          cache/logo-198x66-1200.webp 1200w
        "
        sizes="(max-width: 600px) 480px, (max-width: 1000px) 800px, 1200px"
        loading="lazy" 
        alt="" 
        data-alt-i18n="agriforland_logo"
        class="h-8 sm:h-10 md:h-12"
      >
      <!-- Menu Burger pour mobile -->
      <button id="menu-toggle" class="md:hidden text-gray-700 focus:outline-none p-1" aria-label="" data-aria-i18n="open_menu">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
          <path d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
      </button>
      <!-- Boutons (desktop) -->
      <div class="hidden md:flex gap-3 items-center ml-auto">
        <!-- Language Selector -->
        <div class="relative inline-block text-left">
          <select id="language-selector" class="block appearance-none bg-white border border-gray-300 hover:border-gray-500 px-2 py-1 pr-8 rounded shadow leading-tight focus:outline-none focus:shadow-outline text-sm">
            <option value="fr" data-icon="images/fr.webp">Français</option>
            <option value="en" data-icon="images/en.webp">English</option>
          </select>
          <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2">
            <img id="language-icon" src="images/fr.webp" loading="lazy" alt="" data-alt-i18n="language" class="h-4 w-4">
          </div>
        </div>
        <a href="recrutement.html" class="bg-[#759916] text-white px-4 py-2 rounded-md hover:text-black hover:bg-[#ade126] transition text-sm whitespace-nowrap" data-i18n="join_us">
          Nous Rejoindre
        </a>
        <a href="contact.html" class="border border-gray-500 px-4 py-2 rounded-md hover:text-black hover:bg-[#f6ffde] transition text-sm whitespace-nowrap" data-i18n="contact_us">
          Nous Contacter
        </a>
      </div>
    </div>
    <!-- Navigation Desktop -->
    <div class="border-t border-gray-100 bg-[#f6ffde] hidden md:block">
      <nav class="max-w-7xl mx-auto px-4 py-3 flex justify-center gap-6 lg:gap-8 text-lg">
        <a href="index.php" class="nav-link hover:text-[#a9cf46] transition-colors" data-i18n="home">Accueil</a>
        <a href="about.php" class="nav-link hover:text-[#a9cf46] transition-colors" data-i18n="about">À Propos</a>
        <a href="poles.html" class="nav-link hover:text-[#a9cf46] transition-colors" data-i18n="poles">Nos Pôles</a>
        <a href="projets.html" class="nav-link hover:text-[#a9cf46] transition-colors" data-i18n="projects">Nos Projets</a>
        <a href="blog.php" class="nav-link hover:text-[#a9cf46] transition-colors" data-i18n="blog">Blog</a>
        <a href="portfolios.php" class="nav-link hover:text-[#a9cf46] transition-colors" data-i18n="portfolios">Portfolios</a>
      </nav>
    </div>
    <!-- Menu Mobile -->
    <div id="mobile-menu" class="md:hidden hidden bg-[#f6ffde] px-4 pb-4">
      <nav class="flex flex-col gap-2 text-base">
        <a href="index.php" class="nav-link hover:text-[#a9cf46] transition py-2" data-i18n="home">Accueil</a>
        <a href="about.php" class="nav-link hover:text-[#a9cf46] transition py-2" data-i18n="about">À Propos</a>
        <a href="poles.html" class="nav-link hover:text-[#a9cf46] transition py-2" data-i18n="poles">Nos Pôles</a>
        <a href="projets.html" class="nav-link hover:text-[#a9cf46] transition py-2" data-i18n="projects">Nos Projets</a>
        <a href="blog.php" class="nav-link hover:text-[#a9cf46] transition py-2" data-i18n="blog">Blog</a>
        <a href="portfolios.php" class="nav-link hover:text-[#a9cf46] transition py-2" data-i18n="portfolios">Portfolios</a>
      </nav>
      <div class="mt-4 flex flex-col gap-3">
        <!-- Language Selector for Mobile -->
        <div class="relative">
          <select id="language-selector-mobile" class="block appearance-none bg-white border border-gray-300 hover:border-gray-500 px-3 py-2 pr-8 rounded shadow leading-tight focus:outline-none focus:shadow-outline w-full text-sm">
            <option value="fr" data-icon="images/fr.webp">Français</option>
            <option value="en" data-icon="images/en.webp">English</option>
          </select>
          <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2">
            <img id="language-icon-mobile" src="images/fr.webp" loading="lazy" alt="" data-alt-i18n="language" class="h-4 w-4">
          </div>
        </div>
        <a href="recrutement.html" class="bg-[#759916] text-white px-4 py-3 rounded-md text-center text-sm hover:bg-[#ade126] transition font-medium" data-i18n="join_us">Nous Rejoindre</a>
        <a href="contact.html" class="border border-gray-500 px-4 py-3 rounded-md text-center text-sm hover:bg-white transition font-medium" data-i18n="contact_us">Nous Contacter</a>
      </div>
    </div>
  </header>

  <!-- Hero du Blog -->
  <section class="relative">
    <img 
      src="cache/sil-1-800.webp" 
      srcset="
        cache/sil-1-480.webp 480w, 
        cache/sil-1-800.webp 800w, 
        cache/sil-1-1200.webp 1200w
      "
      sizes="(max-width: 600px) 480px, (max-width: 1000px) 800px, 1200px"
      loading="lazy" 
      alt="" 
      data-alt-i18n="blog_banner"
      class="w-full h-48 sm:h-60 md:h-72 lg:h-80 object-cover"
    >
    <div class="absolute inset-0 flex items-center justify-center bg-black/50">
      <h1 class="text-white text-3xl sm:text-4xl md:text-5xl font-bold font-kanit px-4 text-center" data-i18n="blog_title">Blog</h1>
    </div>
  </section>

  <!-- Bienvenue et barre de recherche -->
  <section class="text-center py-8 sm:py-10 px-4">
    <div class="max-w-4xl mx-auto">
      <h2 class="text-xl sm:text-2xl md:text-3xl font-semibold mb-2" data-i18n="welcome_blog">Bienvenue sur notre blog</h2>
      <p class="text-[#4CAF50] text-base sm:text-lg font-semibold mb-6 sm:mb-8" data-i18n="start_exploring">Commencez à explorer !</p>
      
      <!-- Formulaire de recherche amélioré -->
      <form id="search-form" class="flex flex-col gap-4 max-w-2xl mx-auto">
        <div class="flex flex-col sm:flex-row gap-4">
          <input 
            type="text" 
            name="category" 
            placeholder="" 
            data-i18n-placeholder="category" 
            value="<?php echo htmlspecialchars($category); ?>" 
            class="flex-1 px-4 py-3 rounded-lg border-2 border-gray-300 focus:border-[#a9cf46] focus:outline-none focus:ring-2 focus:ring-[#a9cf46]/20 transition-all text-sm sm:text-base"
          >
          <input 
            type="text" 
            name="search" 
            placeholder="" 
            data-i18n-placeholder="search_title" 
            value="<?php echo htmlspecialchars($search_term); ?>" 
            class="flex-1 px-4 py-3 rounded-lg border-2 border-gray-300 focus:border-[#a9cf46] focus:outline-none focus:ring-2 focus:ring-[#a9cf46]/20 transition-all text-sm sm:text-base"
          >
        </div>
        <button 
          type="submit" 
          class="bg-[#a9cf46] px-6 py-3 rounded-lg hover:bg-[#93bc3d] transition-all text-white font-medium text-sm sm:text-base shadow-md hover:shadow-lg transform hover:-translate-y-0.5"
          data-i18n="search"
        >
          Rechercher
        </button>
      </form>
    </div>
  </section>

  <!-- Grid des articles de blog -->
  <section class="max-w-7xl mx-auto px-4 pb-8 sm:pb-12">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6" id="blog-grid">
      <?php if ($result->num_rows > 0) : ?>
        <?php while ($article = $result->fetch_assoc()) : ?>
          <div class="bg-white rounded-xl shadow-md hover:shadow-lg transition-all duration-300 overflow-hidden">
            <img 
              src="admin/<?php echo htmlspecialchars($article['image']); ?>" 
              loading="lazy" 
              class="w-full h-40 sm:h-48 object-cover" 
              alt="" 
              data-alt-i18n="article_image"
            >
            <div class="p-4 sm:p-6">
              <h3 class="font-bold text-base sm:text-lg mb-2 leading-tight line-clamp-2">
                <?php echo ($article['title']); ?>
              </h3>
              <p class="text-sm text-gray-600 mb-4 leading-relaxed line-clamp-3">
                <?php echo ($article['resume']); ?>
              </p>
              <a 
                href="detailblog.php?slug=<?php echo urlencode($article['slug']); ?>" 
                class="inline-flex items-center text-[#759916] font-semibold hover:text-[#a9cf46] transition-colors text-sm group"
                data-i18n="read_more"
              >
                Voir plus
                <i class="ph ph-arrow-right ml-1 group-hover:translate-x-1 transition-transform"></i>
              </a>
            </div>
          </div>
        <?php endwhile; ?>
      <?php else : ?>
        <div class="col-span-full text-center py-12">
          <div class="text-gray-500">
            <i class="ph ph-article text-4xl mb-4"></i>
            <p class="text-lg" data-i18n="no_articles">Aucun article trouvé.</p>
          </div>
        </div>
      <?php endif; ?>
    </div>

    <!-- Pagination améliorée -->
    <?php if ($total_pages > 1) : ?>
      <div class="flex flex-wrap justify-center items-center mt-8 sm:mt-12 gap-2 sm:gap-4">
        <?php if ($current_page > 1) : ?>
          <a 
            href="blog.php?page=<?php echo $current_page - 1; ?>&search=<?php echo urlencode($search_term); ?>&category=<?php echo urlencode($category); ?>" 
            class="px-3 py-2 rounded-lg border border-gray-300 hover:bg-[#a9cf46] hover:text-white hover:border-[#a9cf46] transition-all text-sm font-medium"
            data-i18n="previous"
          >
            Précédent
          </a>
        <?php endif; ?>
        
        <!-- Pagination avec points de suspension pour mobile -->
        <?php 
        $start_page = max(1, $current_page - 2);
        $end_page = min($total_pages, $current_page + 2);
        
        if ($start_page > 1): ?>
          <a href="blog.php?page=1&search=<?php echo urlencode($search_term); ?>&category=<?php echo urlencode($category); ?>" 
             class="px-3 py-2 rounded-lg border border-gray-300 hover:bg-[#a9cf46] hover:text-white hover:border-[#a9cf46] transition-all text-sm font-medium">1</a>
          <?php if ($start_page > 2): ?>
            <span class="px-2 text-gray-500">...</span>
          <?php endif; ?>
        <?php endif; ?>
        
        <?php for ($page = $start_page; $page <= $end_page; $page++) : ?>
          <a 
            href="blog.php?page=<?php echo $page; ?>&search=<?php echo urlencode($search_term); ?>&category=<?php echo urlencode($category); ?>" 
            class="px-3 py-2 rounded-lg border text-sm font-medium transition-all <?php echo ($page == $current_page) ? 'bg-[#a9cf46] text-white border-[#a9cf46]' : 'border-gray-300 hover:bg-[#a9cf46] hover:text-white hover:border-[#a9cf46]'; ?>"
          >
            <?php echo $page; ?>
          </a>
        <?php endfor; ?>
        
        <?php if ($end_page < $total_pages): ?>
          <?php if ($end_page < $total_pages - 1): ?>
            <span class="px-2 text-gray-500">...</span>
          <?php endif; ?>
          <a href="blog.php?page=<?php echo $total_pages; ?>&search=<?php echo urlencode($search_term); ?>&category=<?php echo urlencode($category); ?>" 
             class="px-3 py-2 rounded-lg border border-gray-300 hover:bg-[#a9cf46] hover:text-white hover:border-[#a9cf46] transition-all text-sm font-medium"><?php echo $total_pages; ?></a>
        <?php endif; ?>
        
        <?php if ($current_page < $total_pages) : ?>
          <a 
            href="blog.php?page=<?php echo $current_page + 1; ?>&search=<?php echo urlencode($search_term); ?>&category=<?php echo urlencode($category); ?>" 
            class="px-3 py-2 rounded-lg border border-gray-300 hover:bg-[#a9cf46] hover:text-white hover:border-[#a9cf46] transition-all text-sm font-medium"
            data-i18n="next"
          >
            Suivant
          </a>
        <?php endif; ?>
      </div>
    <?php endif; ?>
  </section>

    <?php include __DIR__ . '/footer.php'; ?>


  <!-- Scripts -->
  <script>
    // Language translations (complètes)
    const translations = {
      fr: {
        page_title: "AGRIFORLAND SARL - Blog",
        loading: "Chargement...",
        agriforland_logo: "Logo Agriforland",
        open_menu: "Ouvrir le menu",
        language: "Langue",
        join_us: "Nous Rejoindre",
        contact_us: "Nous Contacter",
        home: "Accueil",
        about: "À Propos",
        poles: "Nos Pôles",
        projects: "Nos Projets",
        blog: "Blog",
        portfolios: "Portfolios",
        blog_banner: "Bannière Blog",
        blog_title: "Blog",
        welcome_blog: "Bienvenue sur notre blog",
        start_exploring: "Commencez à explorer !",
        category: "Catégorie",
        search_title: "Rechercher un titre",
        search: "Rechercher",
        article_image: "Image de l'article",
        read_more: "Voir plus",
        no_articles: "Aucun article trouvé.",
        previous: "Précédent",
        next: "Suivant",
        follow_us: "SUIVEZ-NOUS",
        facebook: "Facebook",
        instagram: "Instagram",
        twitter: "Twitter",
        linkedin: "LinkedIn",
        useful_links: "Liens Utiles",
        recruitment: "Recrutement",
        consultant_recruitment: "Recrutement Consultant",
        our_group: "Notre Groupe",
        our_stories: "Nos Histoires",
        our_values_link: "Nos Valeurs",
        our_missions_link: "Nos Missions",
        our_teams: "Nos Équipes",
        our_ecofarms: "Nos Écofermes",
        others: "Autres",
        agroforestry: "Agroforesterie",
        mapping: "Cartographie",
        our_partners: "Nos Partenaires",
        newsletter: "Newsletter",
        your_email: "Votre email",
        subscribe: "S'inscrire",
        subscribing: "Inscription...",
        newsletter_success: "Merci pour votre inscription !",
        error_newsletter: "Erreur lors de l'inscription.",
        copyright: "© 2025 Agriforland. Tous droits réservés.",
        contact: "Contact"
      },
      en: {
        page_title: "AGRIFORLAND SARL - Blog",
        loading: "Loading...",
        agriforland_logo: "Agriforland Logo",
        open_menu: "Open menu",
        language: "Language",
        join_us: "Join Us",
        contact_us: "Contact Us",
        home: "Home",
        about: "About",
        poles: "Our Divisions",
        projects: "Our Projects",
        blog: "Blog",
        portfolios: "Portfolios",
        blog_banner: "Blog Banner",
        blog_title: "Blog",
        welcome_blog: "Welcome to our blog",
        start_exploring: "Start exploring!",
        category: "Category",
        search_title: "Search for a title",
        search: "Search",
        article_image: "Article image",
        read_more: "Read more",
        no_articles: "No articles found.",
        previous: "Previous",
        next: "Next",
        follow_us: "FOLLOW US",
        facebook: "Facebook",
        instagram: "Instagram",
        twitter: "Twitter",
        linkedin: "LinkedIn",
        useful_links: "Useful Links",
        recruitment: "Recruitment",
        consultant_recruitment: "Consultant Recruitment",
        our_group: "Our Group",
        our_stories: "Our Stories",
        our_values_link: "Our Values",
        our_missions_link: "Our Missions",
        our_teams: "Our Teams",
        our_ecofarms: "Our Ecofarms",
        others: "Others",
        agroforestry: "Agroforestry",
        mapping: "Mapping",
        our_partners: "Our Partners",
        newsletter: "Newsletter",
        your_email: "Your email",
        subscribe: "Subscribe",
        subscribing: "Subscribing...",
        newsletter_success: "Thank you for subscribing!",
        error_newsletter: "Subscription error.",
        copyright: "© 2025 Agriforland. All rights reserved.",
        contact: "Contact"
      }
    };

    // Language switcher
    const languageSelectors = document.querySelectorAll('#language-selector, #language-selector-mobile');
    const languageIcons = document.querySelectorAll('#language-icon, #language-icon-mobile');

    function updateContent(lang) {
      // Update static translations
      document.querySelectorAll('[data-i18n]').forEach(element => {
        const key = element.getAttribute('data-i18n');
        if (translations[lang] && translations[lang][key]) {
          element.textContent = translations[lang][key];
        }
      });
      
      // Update placeholders
      document.querySelectorAll('[data-i18n-placeholder]').forEach(element => {
        const key = element.getAttribute('data-i18n-placeholder');
        if (translations[lang] && translations[lang][key]) {
          element.placeholder = translations[lang][key];
        }
      });

      // Update alt attributes
      document.querySelectorAll('[data-alt-i18n]').forEach(element => {
        const key = element.getAttribute('data-alt-i18n');
        if (translations[lang] && translations[lang][key]) {
          element.alt = translations[lang][key];
        }
      });

      // Update aria-label attributes
      document.querySelectorAll('[data-aria-i18n]').forEach(element => {
        const key = element.getAttribute('data-aria-i18n');
        if (translations[lang] && translations[lang][key]) {
          element.setAttribute('aria-label', translations[lang][key]);
        }
      });

      // Update document title
      document.title = translations[lang].page_title;

      // Update language icons and selectors
      document.documentElement.lang = lang;
      languageIcons.forEach(icon => {
        icon.src = `images/${lang}.webp`;
        icon.alt = translations[lang].language;
      });
      languageSelectors.forEach(selector => selector.value = lang);
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

    // Menu mobile amélioré
    const toggle = document.getElementById('menu-toggle');
    const menu = document.getElementById('mobile-menu');
    
    toggle.addEventListener('click', () => {
      menu.classList.toggle('hidden');
    });

    // Fermer le menu en cliquant sur un lien
    const mobileLinks = menu.querySelectorAll('a');
    mobileLinks.forEach(link => {
      link.addEventListener('click', () => {
        menu.classList.add('hidden');
      });
    });

    // Fermer le menu en cliquant en dehors
    document.addEventListener('click', (e) => {
      if (!toggle.contains(e.target) && !menu.contains(e.target)) {
        menu.classList.add('hidden');
      }
    });

    // Classe active pour la navigation
    document.querySelectorAll('.nav-link').forEach(link => {
      const href = link.getAttribute('href');
      if (href === 'blog.php') {
        link.classList.add('text-[#a9cf46]', 'border-b-2', 'border-[#a9cf46]', 'font-semibold');
      }
    });

    // Recherche améliorée
    const searchForm = document.getElementById('search-form');
    searchForm.addEventListener('submit', function (e) {
      e.preventDefault();
      const searchInput = document.querySelector('input[name="search"]').value;
      const categoryInput = document.querySelector('input[name="category"]').value;
      
      // Construire l'URL avec les paramètres de recherche
      const params = new URLSearchParams();
      params.set('page', '1');
      if (searchInput.trim()) params.set('search', searchInput.trim());
      if (categoryInput.trim()) params.set('category', categoryInput.trim());
      
      window.location.href = `blog.php?${params.toString()}`;
    });

    // Newsletter form améliorée avec états de chargement
    const newsletterForm = document.getElementById('newsletter-form');
    const newsletterMsg = document.getElementById('newsletter-msg');
    const newsletterBtn = document.getElementById('newsletter-btn');
    
    newsletterForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      
      const currentLang = localStorage.getItem('language') || 'fr';
      
      // État de chargement
      newsletterBtn.disabled = true;
      newsletterBtn.textContent = translations[currentLang].subscribing;
      
      try {
        const formData = new FormData(newsletterForm);
        const response = await fetch('back/newsletter.php', {
          method: 'POST',
          body: formData
        });
        
        if (response.ok) {
          newsletterMsg.classList.remove('hidden', 'text-red-600');
          newsletterMsg.classList.add('text-green-600');
          newsletterMsg.textContent = translations[currentLang].newsletter_success;
          newsletterForm.reset();
        } else {
          newsletterMsg.classList.remove('hidden', 'text-green-600');
          newsletterMsg.classList.add('text-red-600');
          newsletterMsg.textContent = translations[currentLang].error_newsletter;
        }
      } catch (error) {
        newsletterMsg.classList.remove('hidden', 'text-green-600');
        newsletterMsg.classList.add('text-red-600');
        newsletterMsg.textContent = translations[currentLang].error_newsletter;
      } finally {
        // Restaurer le bouton
        newsletterBtn.disabled = false;
        newsletterBtn.textContent = translations[currentLang].subscribe;
        
        // Masquer le message après 5 secondes
        setTimeout(() => {
          newsletterMsg.classList.add('hidden');
        }, 5000);
      }
    });
  </script>
</body>
</html>