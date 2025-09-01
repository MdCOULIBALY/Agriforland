<?php
    // On récupère l'ID ou le slug du projet depuis l'URL
    $slug = isset($_GET['slug']) ? $_GET['slug'] : '';

    // Charger les projets depuis le fichier JSON
    $projects = json_decode(file_get_contents('data/projects.json'), true);

    // Trouver le projet sélectionné en utilisant le slug
    $selectedProject = null;
    foreach ($projects as $project) {
        if ($project['slug'] === $slug) {
            $selectedProject = $project;
            break;
        }
    }

    // Si le projet n'existe pas, afficher une erreur ou rediriger
    if (!$selectedProject) {
        echo "Projet non trouvé";
        exit;
    }

    // Maintenant on récupère les projets associés
    $relatedProjects = [];

    foreach ($projects as $project) {
        if ($project['slug'] !== $selectedProject['slug']) {
            // Vérifier si au moins une catégorie correspond
            $commonCategories = array_intersect($project['categorie'], $selectedProject['categorie']);
            if (!empty($commonCategories)) {
                $relatedProjects[] = $project;
            }
        }
    }
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
  <title data-i18n="page_title">Agriforland - <?php echo htmlspecialchars($selectedProject['titre']['fr']); ?></title>
  <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@400;700&family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/@phosphor-icons/web"></script>
  <link href="css/Style.css" rel="stylesheet">
<link rel="stylesheet" href="https://unpkg.com/@phosphor-icons/web@2.0.3/src/bold/style.css">
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

<body class="bg-[#f6ffde] text-black"> 
<!-- Preloader -->
<div id="preloader" class="fixed inset-0 bg-[#f6ffde] z-50 flex items-center justify-center">
  <div class="animate-triangle w-20 h-20 sm:w-32 sm:h-32">
    <img src="images/triangle-svgrepo-com.svg" loading="lazy" alt="" data-alt-i18n="loading" class="w-full h-full object-contain triangle-img">
  </div>
</div>

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
      alt="" 
      data-alt-i18n="agriforland_logo" 
      loading="lazy" 
      class="h-8 sm:h-10"
    />
    <!-- Menu Burger pour mobile -->
    <button id="menu-toggle" class="md:hidden text-gray-700 focus:outline-none p-1" aria-label="" data-aria-i18n="open_menu">
      <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2"
            viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
        <path d="M4 6h16M4 12h16M4 18h16"/>
      </svg>
    </button>
    <!-- Boutons (desktop) -->
    <div class="hidden md:flex gap-2 lg:gap-3 items-center ml-auto">
      <!-- Language Selector -->
      <div class="relative inline-block text-left">
        <select id="language-selector" class="block appearance-none bg-white border border-gray-300 hover:border-gray-500 px-2 py-1 pr-8 rounded shadow leading-tight focus:outline-none focus:shadow-outline text-sm">
          <option value="fr" data-icon="images/fr.webp">Français</option>
          <option value="en" data-icon="images/en.webp">English</option>
        </select>
        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2">
          <img id="language-icon" loading="lazy" src="images/fr.webp" alt="" data-alt-i18n="language" class="h-4 w-4 sm:h-5 sm:w-5">
        </div>
      </div>
      <a href="recrutement.html" class="bg-[#759916] text-white px-3 sm:px-4 py-2 rounded-md hover:text-black hover:bg-[#ade126] transition text-xs sm:text-sm whitespace-nowrap" data-i18n="join_us">
        Nous Rejoindre
      </a>
      <a href="contact.html" class="border border-gray-500 px-3 sm:px-4 py-2 rounded-md hover:text-black hover:bg-[#f6ffde] transition text-xs sm:text-sm whitespace-nowrap" data-i18n="contact_us">
        Nous Contacter
      </a>
    </div>
  </div>

  <!-- Navigation Desktop -->
  <div class="border-t border-gray-100 bg-[#f6ffde] hidden md:block">
    <nav class="max-w-7xl mx-auto px-4 py-3 flex justify-center gap-4 lg:gap-8 text-sm lg:text-lg">
      <a href="index.php" class="nav-link hover:text-[#a9cf46] transition-colors" data-i18n="home">Accueil</a>
      <a href="about.php" class="nav-link hover:text-[#a9cf46] transition-colors" data-i18n="about">À Propos</a>
      <a href="poles.html" class="nav-link hover:text-[#a9cf46] transition-colors" data-i18n="poles">Nos Pôles</a>
      <a href="projets.html" class="nav-link hover:text-[#a9cf46] transition-colors" data-i18n="projects">Nos Projets</a>
      <a href="blog.php" class="nav-link hover:text-[#a9cf46] transition-colors" data-i18n="blog">Blog</a>
      <a href="portfolios.php" class="nav-link hover:text-[#a9cf46] transition-colors" data-i18n="portfolios">Portfolio</a>
    </nav>
  </div>

  <!-- Menu Mobile -->
  <div id="mobile-menu" class="md:hidden hidden bg-[#f6ffde] px-3 sm:px-4 pb-4">
    <nav class="flex flex-col gap-2 sm:gap-3 text-base py-3">
      <a href="index.php" class="nav-link hover:text-[#a9cf46] transition py-2 border-b border-gray-200" data-i18n="home">Accueil</a>
      <a href="about.php" class="nav-link hover:text-[#a9cf46] transition py-2 border-b border-gray-200" data-i18n="about">À Propos</a>
      <a href="poles.html" class="nav-link hover:text-[#a9cf46] transition py-2 border-b border-gray-200" data-i18n="poles">Nos Pôles</a>
      <a href="projets.html" class="nav-link hover:text-[#a9cf46] transition py-2 border-b border-gray-200" data-i18n="projects">Nos Projets</a>
      <a href="blog.php" class="nav-link hover:text-[#a9cf46] transition py-2 border-b border-gray-200" data-i18n="blog">Blog</a>
      <a href="portfolios.php" class="nav-link hover:text-[#a9cf46] transition-colors py-2" data-i18n="portfolios">Portfolio</a>
    </nav>
    <div class="mt-4 flex flex-col gap-2">
      <!-- Language Selector for Mobile -->
      <div class="relative inline-block text-left">
        <select id="language-selector-mobile" class="block appearance-none bg-white border border-gray-300 hover:border-gray-500 px-2 py-1 pr-8 rounded shadow leading-tight focus:outline-none focus:shadow-outline w-full">
          <option value="fr" data-icon="images/fr.webp">Français</option>
          <option value="en" data-icon="images/en.webp">English</option>
        </select>
        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2">
          <img id="language-icon-mobile" src="images/fr.webp" alt="" data-alt-i18n="language" class="h-5 w-5">
        </div>
      </div>
      <a href="recrutement.html" class="bg-[#759916] text-white px-4 py-2 rounded-md text-center text-sm hover:bg-[#ade126] transition" data-i18n="join_us">Nous Rejoindre</a>
      <a href="contact.html" class="border border-gray-500 px-4 py-2 rounded-md text-center text-sm hover:bg-white transition" data-i18n="contact_us">Nous contacter</a>
    </div>
  </div>
</header>

<!-- Hero Section - Responsive -->
<section class="bg-[#fff]">
  <div class="relative h-[300px] sm:h-[400px] md:h-[500px] flex items-center justify-center">
    <img src="<?php echo htmlspecialchars($selectedProject['image']); ?>" alt="" data-alt-i18n="project_image" loading="lazy" class="w-full h-full object-cover">
    <div class="absolute inset-0 bg-black/20 flex items-center justify-center">
      <h1 id="project-title" class="text-white text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-bold text-center px-4 sm:px-6 py-4 bg-black/50 rounded backdrop-blur-sm"><?php echo htmlspecialchars($selectedProject['titre']['fr']); ?></h1>
    </div>
  </div>
</section>

<!-- Fiche projet - Responsive Grid -->
<div class="max-w-6xl mx-auto p-3 sm:p-4 md:p-8 my-6 sm:my-8 rounded-lg shadow-sm">
  <!-- Grid responsive : 2 colonnes sur mobile, 3 colonnes sur tablet+ -->
  <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 sm:gap-4 md:gap-6">
    
    <!-- Catégorie -->
    <div class="flex flex-col items-center text-center p-3 sm:p-4">
      <h2 class="text-xs sm:text-sm md:text-lg font-semibold text-[#6b8e23] mb-1 md:mb-2 flex items-center justify-center gap-1 sm:gap-2">
        <i class="ph ph-leaf text-sm sm:text-base md:text-xl"></i> 
        <span data-i18n="category" class="hidden sm:inline">Catégorie</span>
        <span data-i18n="category_short" class="sm:hidden">Cat.</span>
      </h2>
      <div id="project-categories">
        <?php foreach ($selectedProject['categorie'] as $cat): ?>
          <p class="text-gray-700 text-xs sm:text-sm"><?php echo htmlspecialchars($cat); ?></p>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- Zone -->
    <div class="flex flex-col items-center text-center p-3 sm:p-4">
      <h2 class="text-xs sm:text-sm md:text-lg font-semibold text-[#6b8e23] mb-1 md:mb-2 flex items-center justify-center gap-1 sm:gap-2">
        <i class="ph ph-map-pin text-sm sm:text-base md:text-xl"></i> 
        <span data-i18n="zone">Zone</span>
      </h2>
      <div id="project-zones">
        <?php foreach ($selectedProject['zones'] as $zone): ?>
          <p class="text-gray-700 text-xs sm:text-sm"><?php echo htmlspecialchars($zone); ?></p>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- Partenaire -->
    <div class="flex flex-col items-center text-center p-3 sm:p-4 col-span-2 sm:col-span-1">
      <h2 class="text-xs sm:text-sm md:text-lg font-semibold text-[#6b8e23] mb-1 md:mb-2 flex items-center justify-center gap-1 sm:gap-2">
        <i class="ph ph-handshake text-sm sm:text-base md:text-xl"></i> 
        <span data-i18n="partner" class="hidden sm:inline">Partenaire</span>
        <span data-i18n="partner_short" class="sm:hidden">Part.</span>
      </h2>
      <p id="project-partner" class="text-gray-700 text-xs sm:text-sm text-center"><?php echo htmlspecialchars($selectedProject['partenaire']); ?></p>
    </div>

    <!-- Durée -->
    <div class="flex flex-col items-center text-center p-3 sm:p-4">
      <h2 class="text-xs sm:text-sm md:text-lg font-semibold text-[#6b8e23] mb-1 md:mb-2 flex items-center justify-center gap-1 sm:gap-2">
        <i class="ph ph-clock text-sm sm:text-base md:text-xl"></i> 
        <span data-i18n="duration" class="hidden sm:inline">Durée</span>
        <span data-i18n="duration_short" class="sm:hidden">Dur.</span>
      </h2>
      <p id="project-duration" class="text-gray-700 text-xs sm:text-sm"><?php echo htmlspecialchars($selectedProject['duree']['fr']); ?></p>
    </div>

    <!-- Date -->
    <div class="flex flex-col items-center text-center p-3 sm:p-4">
      <h2 class="text-xs sm:text-sm md:text-lg font-semibold text-[#6b8e23] mb-1 md:mb-2 flex items-center justify-center gap-1 sm:gap-2">
        <i class="ph ph-calendar-blank text-sm sm:text-base md:text-xl"></i> 
        <span data-i18n="date">Date</span>
      </h2>
      <p id="project-date" class="text-gray-700 text-xs sm:text-sm"><?php echo htmlspecialchars($selectedProject['date_demarrage']); ?></p>
    </div>

    <!-- Mots-clés -->
    <div class="flex flex-col items-center text-center p-3 sm:p-4">
      <h2 class="text-xs sm:text-sm md:text-lg font-semibold text-[#6b8e23] mb-1 md:mb-2 flex items-center justify-center gap-1 sm:gap-2">
        <i class="ph ph-hash text-sm sm:text-base md:text-xl"></i> 
        <span data-i18n="keywords" class="hidden sm:inline">Mots-clés</span>
        <span data-i18n="keywords_short" class="sm:hidden">Tags</span>
      </h2>
      <p id="project-keywords" class="text-gray-700 text-xs sm:text-sm text-center">
        <?php echo htmlspecialchars(implode(', ', $selectedProject['mots_cles']['fr'])); ?>
      </p>
    </div>

  </div>
</div>

<!-- Conteneur global avec padding responsive -->
<div class="bg-white px-3 sm:px-4 md:px-16 py-4 sm:py-6 space-y-4 sm:space-y-6">

  <!-- Description du projet -->
  <div class="border border-black rounded-md overflow-hidden">
    <div class="bg-[#f6ffde] text-center py-2">
      <h2 class="text-base sm:text-lg font-bold text-[#4d4d4d]" data-i18n="project_description">Description du projet</h2>
    </div>
    <div class="p-3 sm:p-4">
      <div id="project-description">
        <?php foreach ($selectedProject['description']['fr'] as $p): ?>
          <p class="text-gray-800 text-xs sm:text-sm leading-relaxed mb-2"><?php echo htmlspecialchars($p); ?></p>
        <?php endforeach; ?>
      </div>
    </div>
  </div>

  <!-- Contexte -->
  <div class="border border-black rounded-md overflow-hidden">
    <div class="bg-[#f6ffde] text-center py-2">
      <h2 class="text-base sm:text-lg font-bold text-[#4d4d4d]" data-i18n="context">Contexte</h2>
    </div>
    <div class="p-3 sm:p-4">
      <div id="project-context">
        <?php foreach ($selectedProject['contexte']['fr'] as $c): ?>
          <p class="text-gray-800 text-xs sm:text-sm leading-relaxed mb-2"><?php echo htmlspecialchars($c); ?></p>
        <?php endforeach; ?>
      </div>
    </div>
  </div>

  <!-- Objectifs & Résultats attendus - Stack sur mobile -->
  <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
    <!-- Objectifs -->
    <div class="border border-black rounded-md overflow-hidden">
      <div class="bg-[#f6ffde] text-center py-2">
        <h2 class="text-base sm:text-lg font-bold text-[#4d4d4d]" data-i18n="objectives">Objectifs</h2>
      </div>
      <div class="p-3 sm:p-4">
        <ul id="project-objectives" class="list-disc pl-4 sm:pl-5 text-xs sm:text-sm text-gray-800 space-y-1">
          <?php foreach ($selectedProject['objectifs']['fr'] as $objectif): ?>
            <li><?php echo htmlspecialchars($objectif); ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>

    <!-- Résultats attendus -->
    <div class="border border-black rounded-md overflow-hidden">
      <div class="bg-[#f6ffde] text-center py-2">
        <h2 class="text-base sm:text-lg font-bold text-[#4d4d4d]" data-i18n="expected_results">Résultats attendus</h2>
      </div>
      <div class="p-3 sm:p-4">
        <ul id="project-results" class="list-disc pl-4 sm:pl-5 text-xs sm:text-sm text-gray-800 space-y-1">
          <?php foreach ($selectedProject['resultats']['fr'] as $resultat): ?>
            <li><?php echo htmlspecialchars($resultat); ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>
  </div>

</div>

<!-- Section des projets associés - Responsive -->
<div class="container mx-auto px-3 sm:px-4 py-12 sm:py-16">
    <h2 class="text-2xl sm:text-3xl font-bold text-gray-800 mb-6 sm:mb-8 text-center" data-i18n="related_projects">Projets Associés</h2>
    <div id="related-projects-container" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 lg:gap-8">
        <?php foreach ($relatedProjects as $index => $relatedProject): ?>
            <div class="bg-white rounded-lg shadow-lg hover:shadow-xl transition-shadow duration-300">
                <img src="<?php echo htmlspecialchars($relatedProject['image']); ?>" alt="" data-alt-i18n="related_project_<?php echo $index; ?>" loading="lazy" class="w-full h-40 sm:h-48 object-cover rounded-t-lg">
                <div class="p-3 sm:p-4">
                    <h3 class="related-project-title text-lg sm:text-xl font-semibold text-gray-800 mb-2" data-project-index="<?php echo $index; ?>"><?php echo htmlspecialchars($relatedProject['titre']['fr']); ?></h3>
                    <p class="related-project-description text-gray-600 text-sm mb-3" data-project-index="<?php echo $index; ?>"><?php echo htmlspecialchars($relatedProject['description']['fr'][0]); ?></p>
                    <a href="portfolio.php?slug=<?php echo htmlspecialchars($relatedProject['slug']); ?>" class="inline-block text-[#a9cf46] hover:underline text-sm font-medium" data-i18n="see_more">Voir plus</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Footer responsive -->
    <?php include __DIR__ . '/footer.php'; ?>


<script>
  // Project data from PHP
  const projectData = <?php echo json_encode($selectedProject); ?>;
  const relatedProjectsData = <?php echo json_encode($relatedProjects); ?>;

  // Language translations
  const translations = {
    fr: {
      page_title: "Agriforland - " + projectData.titre.fr,
      loading: "Chargement...",
      open_menu: "Ouvrir le menu",
      facebook: "Facebook",
      instagram: "Instagram",
      twitter: "Twitter",
      linkedin: "LinkedIn",
      language: "Langue",
      agriforland_logo: "Logo Agriforland",
      project_image: "Image du projet",
      join_us: "Nous Rejoindre",
      contact_us: "Nous Contacter",
      home: "Accueil",
      about: "À Propos",
      poles: "Nos Pôles",
      projects: "Nos Projets",
      blog: "Blog",
      portfolios: "Portfolios",
      category: "Catégorie",
      category_short: "Cat.",
      zone: "Zone",
      partner: "Partenaire",
      partner_short: "Part.",
      duration: "Durée",
      duration_short: "Dur.",
      date: "Date",
      keywords: "Mots-clés",
      keywords_short: "Tags",
      project_description: "Description du projet",
      context: "Contexte",
      objectives: "Objectifs",
      expected_results: "Résultats attendus",
      related_projects: "Projets Associés",
      see_more: "Voir plus",
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
      error_newsletter: "Erreur lors de l'inscription.",
      copyright: "© 2025 Agriforland. Tous droits réservés.",
      contact: "Contact"
    },
    en: {
      page_title: "Agriforland - " + (projectData.titre.en || projectData.titre.fr),
      loading: "Loading...",
      open_menu: "Open menu",
      facebook: "Facebook",
      instagram: "Instagram",
      twitter: "Twitter",
      linkedin: "LinkedIn",
      language: "Language",
      agriforland_logo: "Agriforland Logo",
      project_image: "Project image",
      join_us: "Join Us",
      contact_us: "Contact Us",
      home: "Home",
      about: "About",
      poles: "Our Divisions",
      projects: "Our Projects",
      blog: "Blog",
      portfolios: "Portfolios",
      category: "Category",
      category_short: "Cat.",
      zone: "Zone",
      partner: "Partner",
      partner_short: "Part.",
      duration: "Duration",
      duration_short: "Dur.",
      date: "Date",
      keywords: "Keywords",
      keywords_short: "Tags",
      project_description: "Project Description",
      context: "Context",
      objectives: "Objectives",
      expected_results: "Expected Results",
      related_projects: "Related Projects",
      see_more: "See more",
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
      if (translations[lang][key]) {
        element.textContent = translations[lang][key];
      }
    });
    
    // Update placeholders
    document.querySelectorAll('[data-i18n-placeholder]').forEach(element => {
      const key = element.getAttribute('data-i18n-placeholder');
      if (translations[lang][key]) {
        element.placeholder = translations[lang][key];
      }
    });

    // Update alt attributes
    document.querySelectorAll('[data-alt-i18n]').forEach(element => {
      const key = element.getAttribute('data-alt-i18n');
      if (translations[lang][key]) {
        element.alt = translations[lang][key];
      }
    });

    // Update aria-label attributes
    document.querySelectorAll('[data-aria-i18n]').forEach(element => {
      const key = element.getAttribute('data-aria-i18n');
      if (translations[lang][key]) {
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

    // Update dynamic project content
    updateProjectContent(lang);
    
    localStorage.setItem('language', lang);
  }

  function updateProjectContent(lang) {
    // Update project title
    const titleText = projectData.titre[lang] || projectData.titre.fr;
    document.getElementById('project-title').textContent = titleText;
    
    // Update project duration
    const durationText = projectData.duree[lang] || projectData.duree.fr;
    document.getElementById('project-duration').textContent = durationText;
    
    // Update project keywords
    const keywordsText = projectData.mots_cles[lang] || projectData.mots_cles.fr;
    document.getElementById('project-keywords').textContent = keywordsText.join(', ');
    
    // Update project description
    const description = projectData.description[lang] || projectData.description.fr;
    const descriptionContainer = document.getElementById('project-description');
    descriptionContainer.innerHTML = '';
    description.forEach(p => {
      const paragraph = document.createElement('p');
      paragraph.className = 'text-gray-800 text-xs sm:text-sm leading-relaxed mb-2';
      paragraph.textContent = p;
      descriptionContainer.appendChild(paragraph);
    });
    
    // Update project context
    const context = projectData.contexte[lang] || projectData.contexte.fr;
    const contextContainer = document.getElementById('project-context');
    contextContainer.innerHTML = '';
    context.forEach(c => {
      const paragraph = document.createElement('p');
      paragraph.className = 'text-gray-800 text-xs sm:text-sm leading-relaxed mb-2';
      paragraph.textContent = c;
      contextContainer.appendChild(paragraph);
    });
    
    // Update project objectives
    const objectives = projectData.objectifs[lang] || projectData.objectifs.fr;
    const objectivesContainer = document.getElementById('project-objectives');
    objectivesContainer.innerHTML = '';
    objectives.forEach(obj => {
      const listItem = document.createElement('li');
      listItem.textContent = obj;
      objectivesContainer.appendChild(listItem);
    });
    
    // Update project results
    const results = projectData.resultats[lang] || projectData.resultats.fr;
    const resultsContainer = document.getElementById('project-results');
    resultsContainer.innerHTML = '';
    results.forEach(res => {
      const listItem = document.createElement('li');
      listItem.textContent = res;
      resultsContainer.appendChild(listItem);
    });

    // Update related projects
    document.querySelectorAll('.related-project-title').forEach(element => {
      const index = element.getAttribute('data-project-index');
      const project = relatedProjectsData[index];
      if (project && project.titre) {
        element.textContent = project.titre[lang] || project.titre.fr;
      }
    });

    document.querySelectorAll('.related-project-description').forEach(element => {
      const index = element.getAttribute('data-project-index');
      const project = relatedProjectsData[index];
      if (project && project.description) {
        const desc = project.description[lang] || project.description.fr;
        element.textContent = desc[0] || '';
      }
    });
  }

  languageSelectors.forEach(selector => {
    selector.addEventListener('change', (e) => {
      const selectedLang = e.target.value;
      updateContent(selectedLang);
    });
  });

  // Set default language
  const savedLang = localStorage.getItem('language') || 'fr';
  updateContent(savedLang);

  // Newsletter form
  const newsletterForm = document.getElementById('newsletter-form');
  const newsletterMsg = document.getElementById('newsletter-msg');
  newsletterForm?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(newsletterForm);
    const currentLang = localStorage.getItem('language') || 'fr';
    try {
      const response = await fetch('back/newsletter.php', {
        method: 'POST',
        body: formData
      });
      if (response.ok) {
        newsletterMsg.classList.remove('hidden');
        newsletterMsg.classList.remove('text-red-600');
        newsletterMsg.classList.add('text-green-600');
        newsletterMsg.textContent = translations[currentLang].newsletter_success;
        newsletterForm.reset();
      } else {
        throw new Error('Network response was not ok');
      }
    } catch (error) {
      newsletterMsg.classList.remove('hidden');
      newsletterMsg.classList.remove('text-green-600');
      newsletterMsg.classList.add('text-red-600');
      newsletterMsg.textContent = translations[currentLang].error_newsletter;
    }
  });

  // Preloader
  window.addEventListener("load", function () {
    const preloader = document.getElementById('preloader');
    preloader.classList.add('opacity-0', 'pointer-events-none', 'transition-opacity', 'duration-500');
    setTimeout(() => preloader.remove(), 500);
  });

  // Menu mobile
  const toggle = document.getElementById('menu-toggle');
  const menu = document.getElementById('mobile-menu');
  toggle?.addEventListener('click', () => {
    menu.classList.toggle('hidden');
  });

  // Ajout de la classe active automatiquement
  const currentPage = window.location.pathname.split("/").pop();
  document.querySelectorAll('.nav-link').forEach(link => {
    const href = link.getAttribute('href');
    if (href === currentPage) {
      link.classList.add('text-[#a9cf46]', 'border-b-2', 'border-[#a9cf46]', 'font-semibold');
    }
  });
</script>
</body>
</html>