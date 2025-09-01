<?php
include 'admin/includes/db.php';
header('Content-Type: text/html; charset=UTF-8');
// Récupération des actualités À la une
$sql = "SELECT titre, resume, image, slug, date_publication FROM a_la_une ORDER BY date_publication DESC";
$result = $conn->query($sql);

// Récupération sécurisée des données
$actualites = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $actualites[] = $row;
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
<meta name="description" content="AGRIFORLAND : Cabinet conseil multidisciplinaire en agriculture, environnement et développement durable en Côte d'Ivoire.">
<meta name="keywords" content="AGRIFORLAND, cabinet conseil, agriculture, environnement, Côte d'Ivoire, BTP, technologies, développement durable">
<meta name="author" content="AGRIFORLAND SARL">
<meta property="og:title" content="AGRIFORLAND | Cabinet de Conseil Agriculture Environnement Côte d'Ivoire">
<meta property="og:description" content="AGRIFORLAND SARL : Cabinet conseil multidisciplinaire en agriculture, environnement et développement durable en Côte d'Ivoire. Expertise reconnue, projets réalisés.">
<meta property="og:image" content="https://www.agriforland.com/cache/logo-198x66-1200.webp">
<meta property="og:url" content="https://www.agriforland.com/">
<meta name="twitter:card" content="summary_large_image">

<title data-i18n="page_title">AGRIFORLAND | Cabinet de Conseil Agriculture Environnement Côte d'Ivoire</title>
<link href="https://fonts.googleapis.com/css2?family=Kanit:wght@400;700&family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/@phosphor-icons/web@2.0.3/src/bold/style.css">
<link rel="icon" href="images/favicon.ico" type="image/x-icon">
<link href="css/Style.css" rel="stylesheet">
<link rel="canonical" href="https://www.agriforland.com/">
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://unpkg.com/@phosphor-icons/web"></script>
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js" defer></script>
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
  <style>
    .scrollbar-hide {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
    .scrollbar-hide::-webkit-scrollbar {
        display: none;
    }
    
    .slow-scroll {
        scroll-behavior: smooth;
    }
    
    /* Animation pour les cartes */
    @keyframes slideInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    .animate-slideIn {
        animation: slideInUp 0.6s ease-out;
    }

    /* Logo carousel animation */
    .logo-carousel-wrapper {
        animation: scrollLeft 30s linear infinite;
    }
    .logo-carousel-wrapper.paused {
        animation-play-state: paused;
    }
    @keyframes scrollLeft {
        0% { transform: translateX(0); }
        100% { transform: translateX(-50%); }
    }

    /* Classes pour tronquer le texte */
    .line-clamp-2 {
      display: -webkit-box;
      -webkit-line-clamp: 2;
      -webkit-box-orient: vertical;
      overflow: hidden;
    }

    .line-clamp-3 {
      display: -webkit-box;
      -webkit-line-clamp: 3;
      -webkit-box-orient: vertical;
      overflow: hidden;
    }
  </style>

<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-ZKKVQJJCYG"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', 'G-ZKKVQJJCYG');
</script>
<script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "Organization",
    "name": "AGRIFORLAND SARL",
    "legalName": "AGRIFORLAND SARL",
    "url": "https://www.agriforland.com",
    "logo": "https://www.agriforland.com/cache/logo-198x66-1200.webp",
    "description": "AGRIFORLAND SARL - Cabinet conseil multidisciplinaire en agriculture, environnement et développement durable en Côte d'Ivoire. Fondée en 2023.",
    "foundingDate": "2023-05",
    "address": {
      "@type": "PostalAddress",
      "streetAddress": "Abidjan – Cocody Riviera Faya cité ATCI en face du nouveau camp d'Akouedo",
      "addressLocality": "Abidjan",
      "addressRegion": "Abidjan",
      "postalCode": "05 BP 1908",
      "addressCountry": "CI"
    },
    "contactPoint": {
      "@type": "ContactPoint",
      "contactType": "customer service",
      "areaServed": "CI"
    },
    "sameAs": [
      "https://www.facebook.com/agriforland/",
      "https://ci.linkedin.com/company/agriforland",
      "https://www.instagram.com/agriforland/",
      "https://x.com/agriforland"
    ]
  }
</script>
</head>
<body class="bg-[#f6ffde] text-black">
  <!-- Preloader -->
  <div id="preloader" class="fixed inset-0 bg-[#f6ffde] z-50 flex items-center justify-center">
    <div class="animate-triangle w-32 h-32">
      <img src="images/triangle-svgrepo-com.svg" loading="lazy" alt="Cabinet AGRIFORLAND - barre de rechargement" data-alt-i18n="loading" loading="lazy" class="w-full h-full object-contain triangle-img">
    </div>
  </div>

  <header class="bg-white shadow-md sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 py-3 flex items-center justify-between">
      <img 
        src="cache/logo-198x66-800.webp" 
        srcset="
          cache/logo-198x66-480.webp 480w, 
          cache/logo-198x66-800.webp 800w, 
          cache/logo-198x66-1200.webp 1200w
        "
        sizes="(max-width: 600px) 480px, (max-width: 1000px) 800px, 1200px"
        loading="lazy" 
        alt="Cabinet AGRIFORLAND - logo" 
        data-alt-i18n="agriforland_logo"
        class="h-10"
      />
      <!-- Menu Burger pour mobile -->
<button id="menu-toggle" class="md:hidden text-gray-700 focus:outline-none" aria-label="" data-aria-i18n="open_menu">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
          <path d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
      </button>
      <!-- Boutons (desktop) -->
      <div class="hidden md:flex gap-3 items-center ml-auto">
        <!-- Language Selector -->
        <div class="relative inline-block text-left">
          <select id="language-selector" class="block appearance-none bg-white border border-gray-300 hover:border-gray-500 px-2 py-1 pr-8 rounded shadow leading-tight focus:outline-none focus:shadow-outline">
            <option value="fr" loading="lazy" data-icon="images/fr.webp">Français</option>
            <option value="en" loading="lazy" data-icon="images/en.webp">English</option>
          </select>
          <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2">
            <img id="language-icon" loading="lazy" src="images/fr.webp" alt="Cabinet AGRIFORLAND -traduction francaise" data-alt-i18n="language" class="h-5 w-5">
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
      <nav class="max-w-7xl mx-auto px-4 py-3 flex justify-center gap-8 text-lg">
        <a href="index.php" class="nav-link hover:text-[#a9cf46] transition-colors" data-i18n="home">Accueil</a>
        <a href="about.php" class="nav-link hover:text-[#a9cf46] transition-colors" data-i18n="about">À Propos</a>
        <a href="poles.html" class="nav-link hover:text-[#a9cf46] transition-colors" data-i18n="poles">Nos Pôles</a>
        <a href="projets.html" class="nav-link hover:text-[#a9cf46] transition-colors" data-i18n="projects">Nos Projets</a>
        <a href="blog.php" class="nav-link hover:text-[#a9cf46] transition-colors" data-i18n="blog">Blog</a>
        <a href="portfolios.php" class="nav-link hover:text-[#a9cf46] transition-colors" data-i18n="portfolios">Portfolio</a>
      </nav>
    </div>

    <!-- Menu Mobile -->
    <div id="mobile-menu" class="md:hidden hidden bg-[#f6ffde] px-4 pb-4">
      <nav class="flex flex-col gap-3 text-base">
        <a href="index.php" class="nav-link hover:text-[#a9cf46] transition" data-i18n="home">Accueil</a>
        <a href="about.php" class="nav-link hover:text-[#a9cf46] transition" data-i18n="about">À Propos</a>
        <a href="poles.html" class="nav-link hover:text-[#a9cf46] transition" data-i18n="poles">Nos Pôles</a>
        <a href="projets.html" class="nav-link hover:text-[#a9cf46] transition" data-i18n="projects">Nos Projets</a>
        <a href="blog.php" class="nav-link hover:text-[#a9cf46] transition" data-i18n="blog">Blog</a>
        <a href="portfolios.php" class="nav-link hover:text-[#a9cf46] transition-colors" data-i18n="portfolios">Portfolio</a>
      </nav>
      <div class="mt-4 flex flex-col gap-2">
        <!-- Language Selector for Mobile -->
        <div class="relative inline-block text-left">
          <select id="language-selector-mobile" class="block appearance-none bg-white border border-gray-300 hover:border-gray-500 px-2 py-1 pr-8 rounded shadow leading-tight focus:outline-none focus:shadow-outline w-full">
            <option value="fr" data-icon="images/fr.webp">Français</option>
            <option value="en" data-icon="images/en.webp">English</option>
          </select>
          <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2">
            <img id="language-icon-mobile" src="images/fr.webp" alt="Cabinet AGRIFORLAND -traduction francaise mobile" data-alt-i18n="language" class="h-5 w-5">
          </div>
        </div>
        <a href="recrutement.html" class="bg-[#759916] text-white px-4 py-2 rounded-md text-center text-sm hover:bg-[#ade126] transition" data-i18n="join_us">Nous Rejoindre</a>
        <a href="contact.html" class="border border-gray-500 px-4 py-2 rounded-md text-center text-sm hover:bg-white transition" data-i18n="contact_us">Nous Contacter</a>
      </div>
    </div>
  </header>

  <!-- Hero -->
  <section class="relative">
    <img 
      src="cache/slide-1-1920x753-800.webp" 
      srcset="
        cache/slide-1-1920x753-480.webp 480w, 
        cache/slide-1-1920x753-800.webp 800w, 
        cache/slide-1-1920x753-1200.webp 1200w
      "
      sizes="(max-width: 600px) 480px, (max-width: 1000px) 800px, 1200px"
      loading="lazy" 
      alt="Cabinet AGRIFORLAND -banniere" 
      data-alt-i18n="hero_background"
      class="w-full h-[400px] object-cover"
    />
    <div class="absolute top-0 left-0 w-full h-full bg-black/50 flex flex-col justify-center items-center text-center text-white px-4">
      <h1 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-bold leading-tight" data-i18n="hero_title">AGRIFORLAND - Cabinet de Conseil Multidisciplinaire</h1>      <div class="mt-6 flex gap-4">
        <a href="recrutement.html" class="bg-[#a9cf46] px-6 py-2 rounded hover:text-black hover:bg-[#ade126] transition-colors duration-300" data-i18n="join_us">Nous Rejoindre</a>
        <a href="contact.html" class="bg-white text-black px-6 py-2 rounded hover:text-white hover:bg-[#ade126] transition-colors duration-300" data-i18n="contact_us">Nous Contacter</a>
      </div>
    </div>
  </section>

<!-- session À la une -->
<section class="py-6 sm:py-12 px-4 max-w-7xl mx-auto bg-[#f6ffde]">
  <header>
    <h2 class="text-xl sm:text-3xl font-bold text-center mb-4 sm:mb-8 font-kanit" data-i18n="featured_news">À la une</h2>
  </header>
  
  <!-- Version mobile : Scroll horizontal avec navigation -->
  <div class="block sm:hidden relative">
    <?php if (!empty($actualites)): ?>
      <!-- Boutons de navigation -->
      <button id="actualitesLeft" class="absolute left-0 top-1/2 -translate-y-1/2 z-30 bg-white shadow-lg p-2 rounded-full hover:bg-[#ade126] transition-all duration-300" aria-label="Actualité précédente">
        <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
        </svg>
      </button>
      
      <button id="actualitesRight" class="absolute right-0 top-1/2 -translate-y-1/2 z-30 bg-white shadow-lg p-2 rounded-full hover:bg-[#ade126] transition-all duration-300" aria-label="Actualité suivante">
        <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
        </svg>
      </button>

      <!-- Container scroll -->
      <div id="actualitesContainer" class="flex gap-4 overflow-x-auto scrollbar-hide pb-2 snap-x snap-mandatory scroll-smooth px-8">
        <?php foreach ($actualites as $row): ?>
          <article class="min-w-[85%] flex-shrink-0 snap-center bg-white rounded-xl overflow-hidden shadow-lg hover:shadow-xl transition-all duration-300">
            <!-- Image en haut -->
            <div class="relative h-48 overflow-hidden">
              <?php 
              $image_src = 'images/fallback.webp';
              if (!empty($row['image'])) {
                  $image_src = 'admin/' . htmlspecialchars($row['image']);
              }
              ?>
              <img src="<?php echo $image_src; ?>" 
                   loading="lazy" 
                   alt="<?php echo ($row['titre']); ?>" 
                   class="w-full h-full object-cover hover:scale-105 transition-transform duration-300">
              <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent"></div>
            </div>
            
            <!-- Contenu en bas -->
            <div class="p-4">
              <header>
                <h3 class="font-bold text-base leading-tight mb-2">
                  <?php echo ($row['titre']); ?>
                </h3>
              </header>
              
              <div class="text-sm text-gray-600 mb-3 line-clamp-2">
                <?php echo (strip_tags($row['resume'])); ?>
              </div>
              
              <footer class="flex items-center justify-between">
                <a href="actualitedetail.php?slug=<?php echo urlencode($row['slug']); ?>" 
                   class="inline-flex items-center gap-2 text-sm font-medium text-[#759916] hover:text-[#ade126] transition-colors duration-300" 
                   data-i18n="learn_more">
                  Lire plus
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                  </svg>
                </a>
              </footer>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <div class="text-center py-8">
        <h3 class="text-lg font-medium text-gray-500 mb-2">Aucune actualité disponible</h3>
        <p class="text-gray-400" data-i18n="no_news">Revenez bientôt pour découvrir nos dernières nouvelles.</p>
      </div>
    <?php endif; ?>
  </div>

  <!-- Version desktop : Scroll horizontal -->
  <div class="hidden sm:block relative">
    <div class="flex gap-4 sm:gap-6 overflow-x-auto scrollbar-hide pb-2 snap-x snap-mandatory scroll-smooth slow-scroll" id="a-la-une-scroll">
      <?php if (!empty($actualites)): ?>
        <?php foreach ($actualites as $row): ?>
          <article class="min-w-[65%] md:min-w-[45%] lg:min-w-[32%] xl:min-w-[30%] flex-shrink-0 snap-center">
            <div class="relative rounded-xl overflow-hidden mx-2 shadow-lg h-80 lg:h-96 flex flex-col group hover:scale-105 transition-transform duration-300">
              <div class="absolute inset-0 bg-black/40 z-10 rounded-xl group-hover:bg-black/30 transition-colors duration-300"></div>
              <?php 
              $image_src = 'images/fallback.webp';
              if (!empty($row['image'])) {
                  $image_src = 'admin/' . htmlspecialchars($row['image']);
              }
              ?>
              <img src="<?php echo $image_src; ?>" 
                   loading="lazy" 
                   alt="<?php echo ($row['titre']); ?>" 
                   class="absolute inset-0 w-full h-full object-cover z-0">
              <div class="relative z-20 p-4 sm:p-6 text-white mt-auto">
                <header>
                  <h3 class="text-lg sm:text-xl font-bold leading-tight mb-2">
                    <?php echo ($row['titre']); ?>
                  </h3>
                </header>
                <div class="text-sm mt-2 leading-snug line-clamp-3">
                  <?php echo (strip_tags($row['resume'])); ?>
                </div>
                <a href="actualitedetail.php?slug=<?php echo urlencode($row['slug']); ?>" 
                   class="inline-block mt-3 sm:mt-4 text-sm border border-white text-white px-4 py-2 rounded hover:text-black hover:bg-[#ade126] transition-colors duration-300" 
                   data-i18n="learn_more">
                  En savoir plus
                </a>
              </div>
            </div>
          </article>
        <?php endforeach; ?>
      <?php else: ?>
        <p class="text-center text-gray-600 w-full" data-i18n="no_news">Aucune actualité disponible pour le moment.</p>
      <?php endif; ?>
    </div>
  </div>
</section>

  <!-- Qui sommes-nous -->
  <section class="py-12 px-4 max-w-7xl mx-auto grid md:grid-cols-2 gap-6 items-center bg-[#fbfff0]">
    <img 
      src="cache/offres-800.webp" 
      srcset="
        cache/offres-480.webp 480w, 
        cache/offres-800.webp 800w, 
        cache/offres-1200.webp 1200w
      "
      sizes="(max-width: 600px) 480px, (max-width: 1000px) 800px, 1200px"
      loading="lazy" 
      alt="Cabinet AGRIFORLAND - image qui sommes nous" 
      data-alt-i18n="who_we_are_image"
      class="rounded-xl"
    />
      <div>
        <h2 class="text-3xl font-bold mb-4" data-i18n="who_we_are_title">Qui est AGRIFORLAND ?</h2>
        <p class="mb-4" data-i18n="who_we_are_description">AGRIFORLAND est LE cabinet de conseil multidisciplinaire de référence en Côte d'Ivoire. Depuis 2023, AGRIFORLAND SARL accompagne les entreprises dans leurs projets stratégiques. L'équipe AGRIFORLAND couvre une gamme complète de services : agriculture, environnement, BTP, technologies.</p>
        
        <p class="mb-4" data-i18n="who_we_are_description_2">Pourquoi choisir AGRIFORLAND ? Nos experts AGRIFORLAND maîtrisent tous les secteurs clés du développement ivoirien. Contactez AGRIFORLAND maintenant pour transformer vos projets en succès.</p>
        
        <p class="font-semibold pb-4" data-i18n="who_we_are_tagline">AGRIFORLAND SARL : 10 pôles d'expertise, une seule ambition - votre réussite.</p>
        <a href="about.php" class="mt-4 bg-[#a9cf46] px-6 py-2 rounded hover:text-black hover:bg-[#759916] transition-colors duration-300" data-i18n="discover_agriforland">Découvrez AGRIFORLAND</a>
      
      </div>
  </section>

  <!-- Valeurs -->
  <section class="py-12 bg-[#f6ffde] text-center">
    <div class="max-w-5xl mx-auto grid md:grid-cols-3 gap-6 px-4">
      <div>
        <i class="ph ph-gear text-4xl text-[#a9cf46] mb-4 hover:text-white transition-colors duration-300"></i>
        <h3 class="font-bold text-lg hover:text-[#759916] transition-colors duration-300" data-i18n="value_1">Expertise polyvalente</h3>
      </div>
      <div>
        <i class="ph ph-leaf text-4xl text-[#a9cf46] mb-4 hover:text-white transition-colors duration-300"></i>
        <h3 class="font-bold text-lg hover:text-[#759916] transition-colors duration-300" data-i18n="value_2">Solutions vertes</h3>
      </div>
      <div>
        <i class="ph ph-handshake text-4xl text-[#a9cf46] mb-4 hover:text-white transition-colors duration-300"></i>
        <h3 class="font-bold text-lg hover:text-[#759916] transition-colors duration-300" data-i18n="value_3">Partenariat solide</h3>
      </div>
    </div>
  </section>

  <!-- Nos pôles -->
  <section class="py-12 px-4 max-w-7xl mx-auto bg-[#fbfff0] relative">
    <h2 class="text-2xl sm:text-3xl font-bold text-center mb-8" data-i18n="our_poles">Nos pôles</h2>
    
    <!-- Flèches -->
    <button id="polesLeft" class="absolute left-0 top-1/2 -translate-y-1/2 z-30 nav-btn p-3 rounded-full shadow-lg" aria-label="Pôle précédent">
      <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
      </svg>
    </button>
    <button id="polesRight" class="absolute right-0 top-1/2 -translate-y-1/2 z-30 nav-btn p-3 rounded-full shadow-lg" aria-label="Pôle suivant">
      <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
      </svg>
    </button>
    
    <!-- Scrollable content -->
    <div id="polesContainer" class="flex gap-6 overflow-x-auto scroll-smooth snap-x snap-mandatory scrollbar-hide pb-4 px-12">
      <!-- Pôle 1 -->
      <div class="min-w-[70%] sm:min-w-[55%] md:min-w-[40%] lg:min-w-[30%] snap-center bg-white rounded-xl overflow-hidden group transition-all duration-300 hover:shadow-xl">
        <div class="overflow-hidden">
          <a href="poles.html#farming">
            <picture>
              <source media="(max-width: 600px)" srcset="cache/poles_farming-nature-based-solutions-nbs.webp">
              <source media="(max-width: 1000px)" srcset="cache/poles_farming-nature-based-solutions-nbs.webp">
              <img 
                src="cache/poles_farming-nature-based-solutions-nbs.webp" 
                alt="Pôle Farming & Solutions basées sur la nature - AGRIFORLAND"
                class="w-full h-56 object-cover transition-transform duration-500 group-hover:scale-110"
                width="400"
                height="200"
              />
            </picture>
          </a>
        </div>
        <div class="p-4">
          <h3 class="font-semibold text-center" data-i18n="pole_farming_nbs">Farming & NBS</h3>
        </div>
      </div>
      
      <!-- Pôle 2 -->
      <div class="min-w-[70%] sm:min-w-[55%] md:min-w-[40%] lg:min-w-[30%] snap-center bg-white rounded-xl overflow-hidden group transition-all duration-300 hover:shadow-xl">
        <div class="overflow-hidden">
          <a href="poles.html#technologie">
            <picture>
              <source media="(max-width: 600px)" srcset="cache/poles_academy.png">
              <source media="(max-width: 1000px)" srcset="cache/poles_academy.png">
              <img 
                src="cache/poles_academy.png" 
                alt="Pôle Technologies - AGRIFORLAND"
                class="w-full h-56 object-cover transition-transform duration-500 group-hover:scale-110"
                width="400"
                height="200"
              />
            </picture>
          </a>
        </div>
        <div class="p-4">
          <h3 class="font-semibold text-center" data-i18n="pole_technologies">Technologies</h3>
        </div>
      </div>
      
      <!-- Pôle 3 -->
      <div class="min-w-[70%] sm:min-w-[55%] md:min-w-[40%] lg:min-w-[30%] snap-center bg-white rounded-xl overflow-hidden group transition-all duration-300 hover:shadow-xl">
        <div class="overflow-hidden">
          <a href="poles.html#batiment">
            <picture>
              <source media="(max-width: 600px)" srcset="cache/poles_btp-immobilie-480.webp">
              <source media="(max-width: 1000px)" srcset="cache/poles_btp-immobilie-800.webp">
              <img 
                src="cache/poles_btp-immobilie-1200.webp" 
                alt="Pôle BTP & Immobilier - AGRIFORLAND"
                class="w-full h-56 object-cover transition-transform duration-500 group-hover:scale-110"
                width="400"
                height="200"
              />
            </picture>
          </a>
        </div>
        <div class="p-4">
          <h3 class="font-semibold text-center" data-i18n="pole_btp_real_estate">BTP & Immobilier</h3>
        </div>
      </div>
      
      <!-- Pôle 4 -->
      <div class="min-w-[70%] sm:min-w-[55%] md:min-w-[40%] lg:min-w-[30%] snap-center bg-white rounded-xl overflow-hidden group transition-all duration-300 hover:shadow-xl">
        <div class="overflow-hidden">
          <a href="poles.html#eco">
            <picture>
              <source media="(max-width: 600px)" srcset="cache/poles_eco-expertise-1-480.webp">
              <source media="(max-width: 1000px)" srcset="cache/poles_eco-expertise-1-800.webp">
              <img 
                src="cache/poles_eco-expertise-1-1200.webp" 
                alt="Pôle Eco-expertise - AGRIFORLAND"
                class="w-full h-56 object-cover transition-transform duration-500 group-hover:scale-110"
                width="400"
                height="200"
              />
            </picture>
          </a>
        </div>
        <div class="p-4">
          <h3 class="font-semibold text-center" data-i18n="pole_eco_expertise">Eco-expertise</h3>
        </div>
      </div>
      
      <!-- Pôle 5 -->
      <div class="min-w-[70%] sm:min-w-[55%] md:min-w-[40%] lg:min-w-[30%] snap-center bg-white rounded-xl overflow-hidden group transition-all duration-300 hover:shadow-xl">
        <div class="overflow-hidden">
          <a href="poles.html#logistique">
            <picture>
              <source media="(max-width: 600px)" srcset="cache/poles_logistique-1-480.webp">
              <source media="(max-width: 1000px)" srcset="cache/poles_logistique-1-800.webp">
              <img 
                src="cache/poles_logistique-1-1200.webp" 
                alt="Pôle Logistics - AGRIFORLAND"
                class="w-full h-56 object-cover transition-transform duration-500 group-hover:scale-110"
                width="400"
                height="200"
              />
            </picture>
          </a>
        </div>
        <div class="p-4">
          <h3 class="font-semibold text-center" data-i18n="pole_logistics">Logistics</h3>
        </div>
      </div>
      
      <!-- Pôle 6 -->
      <div class="min-w-[70%] sm:min-w-[55%] md:min-w-[40%] lg:min-w-[30%] snap-center bg-white rounded-xl overflow-hidden group transition-all duration-300 hover:shadow-xl">
        <div class="overflow-hidden">
          <a href="poles.html#energie">
            <picture>
              <source media="(max-width: 600px)" srcset="cache/poles_energies-1-480.webp">
              <source media="(max-width: 1000px)" srcset="cache/poles_energies-1-800.webp">
              <img 
                src="cache/poles_energies-1-1200.webp" 
                alt="Pôle Energies - AGRIFORLAND"
                class="w-full h-56 object-cover transition-transform duration-500 group-hover:scale-110"
                width="400"
                height="200"
              />
            </picture>
          </a>
        </div>
        <div class="p-4">
          <h3 class="font-semibold text-center" data-i18n="pole_energies">Energies</h3>
        </div>
      </div>
      
      <!-- Pôle 7 -->
      <div class="min-w-[70%] sm:min-w-[55%] md:min-w-[40%] lg:min-w-[30%] snap-center bg-white rounded-xl overflow-hidden group transition-all duration-300 hover:shadow-xl">
        <div class="overflow-hidden">
          <a href="poles.html#industrie">
            <picture>
              <source media="(max-width: 600px)" srcset="cache/poles_industries-1-480.webp">
              <source media="(max-width: 1000px)" srcset="cache/poles_industries-1-800.webp">
              <img 
                src="cache/poles_industries-1-1200.webp" 
                alt="Pôle Industries - AGRIFORLAND"
                class="w-full h-56 object-cover transition-transform duration-500 group-hover:scale-110"
                width="400"
                height="200"
              />
            </picture>
          </a>
        </div>
        <div class="p-4">
          <h3 class="font-semibold text-center" data-i18n="pole_industries">Industries</h3>
        </div>
      </div>
      
      <!-- Pôle 8 -->
      <div class="min-w-[70%] sm:min-w-[55%] md:min-w-[40%] lg:min-w-[30%] snap-center bg-white rounded-xl overflow-hidden group transition-all duration-300 hover:shadow-xl">
        <div class="overflow-hidden">
          <a href="poles.html#communication">
            <picture>
              <source media="(max-width: 600px)" srcset="cache/poles_communication-1-480.webp">
              <source media="(max-width: 1000px)" srcset="cache/poles_communication-1-800.webp">
              <img 
                src="cache/poles_communication-1-1200.webp" 
                alt="Pôle Communication - AGRIFORLAND"
                class="w-full h-56 object-cover transition-transform duration-500 group-hover:scale-110"
                width="400"
                height="200"
              />
            </picture>
          </a>
        </div>
        <div class="p-4">
          <h3 class="font-semibold text-center" data-i18n="pole_academy">Communication</h3>
        </div>
      </div>

      <!-- Pôle 9 -->
      <div class="min-w-[70%] sm:min-w-[55%] md:min-w-[40%] lg:min-w-[30%] snap-center bg-white rounded-xl overflow-hidden group transition-all duration-300 hover:shadow-xl">
        <div class="overflow-hidden">
          <a href="poles.html#ranch">
            <picture>
              <source media="(max-width: 600px)" srcset="cache/poles_ranch.webp">
              <source media="(max-width: 1000px)" srcset="cache/poles_ranch.webp">
              <img 
                src="cache/poles_ranch.webp" 
                alt="Pôle Ranch - AGRIFORLAND"
                class="w-full h-56 object-cover transition-transform duration-500 group-hover:scale-110"
                width="400"
                height="200"
              />
            </picture>
          </a>
        </div>
        <div class="p-4">
          <h3 class="font-semibold text-center" data-i18n="pole_ranch">Ranch</h3>
        </div>
      </div>

      <!-- Pôle 10 -->
      <div class="min-w-[70%] sm:min-w-[55%] md:min-w-[40%] lg:min-w-[30%] snap-center bg-white rounded-xl overflow-hidden group transition-all duration-300 hover:shadow-xl">
        <div class="overflow-hidden">
          <a href="poles.html#academy">
            <picture>
              <source media="(max-width: 600px)" srcset="cache/poles_academy.png">
              <source media="(max-width: 1000px)" srcset="cache/poles_academy.png">
              <img 
                src="cache/poles_academy.png" 
                alt="Pôle ACADEMY - AGRIFORLAND"
                class="w-full h-56 object-cover transition-transform duration-500 group-hover:scale-110"
                width="400"
                height="200"
              />
            </picture>
          </a>
        </div>
        <div class="p-4">
          <h3 class="font-semibold text-center" data-i18n="pole_academy">AGRIFORLAND ACADEMY</h3>
        </div>
      </div>

    </div>

    <div class="text-center mt-8">
      <a href="poles.html" class="bg-[#a9cf46] px-6 py-3 rounded hover:text-white hover:bg-[#759916] transition-colors duration-300 transform hover:scale-105 font-semibold" data-i18n="see_more">
        Voir Plus
      </a>
    </div>
  </section>

  <!-- Nos projets -->
  <section class="py-12 px-4 max-w-7xl mx-auto">
    <h2 class="text-3xl font-bold text-center mb-8" data-i18n="our_completed_projects">Nos projets réalisés</h2>
    <div class="grid md:grid-cols-3 gap-6">
      <!-- Projet 1 -->
      <div class="bg-white rounded-xl overflow-hidden shadow-md hover:shadow-lg transition-all duration-300 group">
        <div class="overflow-hidden">
          <img 
            src="cache/projet_realise_realiser-1-800.webp" 
            srcset="
              cache/projet_realise_realiser-1-480.webp 480w, 
              cache/projet_realise_realiser-1-800.webp 800w, 
              cache/projet_realise_realiser-1-1200.webp 1200w
            "
            sizes="(max-width: 600px) 480px, (max-width: 1000px) 800px, 1200px"
            loading="lazy" 
            alt="Cabinet AGRIFORLAND - image projet 1" 
            data-alt-i18n="project_1_image"
            class="w-full h-56 object-cover transition-transform duration-500 group-hover:scale-110"
          />
        </div>
        <div class="p-4 group-hover:bg-gray-50 transition-colors duration-300">
          <h3 class="font-bold text-lg mb-2" data-i18n="project_1_title">ÉTUDE DE BASE DANS LES COMMUNAUTÉS RIVERAINES DE MABI-YAYA</h3>
          <p class="text-sm text-gray-600" data-i18n="project_1_description">L'étude de base du projet 'Sustainable rubber for communities (SR4C)' vise à mieux comprendre les dy...</p>
        </div>
      </div>
      <!-- Projet 2 -->
      <div class="bg-white rounded-xl overflow-hidden shadow-md hover:shadow-lg transition-all duration-300 group">
        <div class="overflow-hidden">
          <img 
            src="cache/projet_realise_e-laboration-800.webp" 
            srcset="
              cache/projet_realise_e-laboration-480.webp 480w, 
              cache/projet_realise_e-laboration-800.webp 800w, 
              cache/projet_realise_e-laboration-1200.webp 1200w
            "
            sizes="(max-width: 600px) 480px, (max-width: 1000px) 800px, 1200px"
            loading="lazy" 
            alt="Cabinet AGRIFORLAND - image projet 2" 
            data-alt-i18n="project_2_image"
            class="w-full h-56 object-cover transition-transform duration-500 group-hover:scale-110"
          />
        </div>
        <div class="p-4 group-hover:bg-gray-50 transition-colors duration-300">
          <h3 class="font-bold text-lg mb-2" data-i18n="project_2_title">STRATÉGIE NATIONALE POUR LES RÉSERVES NATURELLES VOLONTAIRES</h3>
          <p class="text-sm text-gray-600" data-i18n="project_2_description">Dans le cadre de l'amélioration de la gestion des aires protégées du Complexe forestier de Taï-Grebo...</p>
        </div>
      </div>
      <!-- Projet 3 -->
      <div class="bg-white rounded-xl overflow-hidden shadow-md hover:shadow-lg transition-all duration-300 group">
        <div class="overflow-hidden">
          <img 
            src="cache/projet_realise_cartographie-1-800.webp" 
            srcset="
              cache/projet_realise_cartographie-1-480.webp 480w, 
              cache/projet_realise_cartographie-1-800.webp 800w, 
              cache/projet_realise_cartographie-1-1200.webp 1200w
            "
            sizes="(max-width: 600px) 480px, (max-width: 1000px) 800px, 1200px"
            loading="lazy" 
            alt="Cabinet AGRIFORLAND - image projet 3" 
            data-alt-i18n="project_3_image"
            class="w-full h-56 object-cover transition-transform duration-500 group-hover:scale-110"
          />
        </div>
        <div class="p-4 group-hover:bg-gray-50 transition-colors duration-300">
          <h3 class="font-bold text-lg mb-2" data-i18n="project_3_title">CARTOGRAPHIE DES PRODUCTEURS DE CAOUTCHOUC EN CÔTE D'IVOIRE</h3>
          <p class="text-sm text-gray-600" data-i18n="project_3_description">OLAM AGRI Rubber a confié à AGRIFORLAND la mission de cartographier les plantations d'hévéas de 400 ...</p>
        </div>
      </div>
    </div>
    <div class="text-center mt-6">
      <a href="projets.html" class="bg-[#a9cf46] px-6 py-2 rounded hover:text-white hover:bg-[#759916] transition-all duration-300 transform hover:scale-105 shadow hover:shadow-md" data-i18n="see_more">
        Voir Plus
      </a>
    </div>
  </section>

  <!-- Section Zones d'Intervention et Bilan -->
  <section class="relative py-16">
    <img 
      src="cache/mdd-800.webp" 
      srcset="
        cache/mdd-480.webp 480w, 
        cache/mdd-800.webp 800w, 
        cache/mdd-1200.webp 1200w
      "
      sizes="(max-width: 600px) 480px, (max-width: 1000px) 800px, 1200px"
      loading="lazy" 
      alt="Cabinet AGRIFORLAND - ZONE INTERVENTION" 
      data-alt-i18n="intervention_zones_background"
      class="absolute inset-0 w-full h-full object-cover"
    />
    <div class="absolute inset-0 bg-black bg-opacity-50"></div>
    <div class="relative container mx-auto px-4 z-10">
      <div class="grid md:grid-cols-2 gap-8 items-start">
        <!-- Zones d'Intervention -->
        <div>
          <h3 class="text-white text-2xl font-bold text-center mb-4" data-i18n="intervention_zones">ZONES D'INTERVENTION</h3>
          <div id="map" class="rounded-lg h-60 sm:h-72 md:h-80 shadow-lg"></div>
        </div>
        <!-- Bilan d'Activités -->
        <div>
          <h3 class="text-white text-2xl font-bold text-center mb-4" data-i18n="activity_report">BILAN D'ACTIVITÉS</h3>
          <!-- Ligne 1 : 2 compteurs -->
          <div class="flex flex-wrap justify-center gap-6 text-center text-white">
            <div>
              <h2 class="text-3xl sm:text-4xl font-bold counter text-[#a9cf46]" data-target="50">0</h2>
              <p class="text-sm sm:text-lg mt-2" data-i18n="completed_projects">Projets réalisés</p>
            </div>
            <div>
              <h2 class="text-3xl sm:text-4xl font-bold counter text-[#a9cf46]" data-target="25">0</h2>
              <p class="text-lg" data-i18n="intervention_areas">Zones d'interventions</p>
            </div>
          </div>
          <!-- Ligne 2 : 1 compteur -->
          <div class="mt-6 flex justify-center text-white">
            <div class="text-center">
              <h2 class="text-3xl sm:text-4xl font-bold counter text-[#a9cf46]" data-target="3000">0</h2>
              <p class="text-lg" data-i18n="people_sensitized">Personnes sensibilisées</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Logos partenaires -->
  <section class="pb-12 pt-5 bg-[#fbfff0] text-center">
    <h2 class="text-4xl pb-12 font-bold mb-6" data-i18n="they_trust_us">Ils nous font confiance</h2>
    <div class="overflow-hidden relative px-4">
      <div id="logo-carousel-wrapper" class="flex w-max gap-4 logo-carousel-wrapper">
        <div class="flex flex-nowrap items-center gap-4 logo-carousel">
          <img 
            src="cache/partenaires_partenaire1-800.webp" 
            srcset="
              cache/partenaires_partenaire1-480.webp 480w, 
              cache/partenaires_partenaire1-800.webp 800w, 
              cache/partenaires_partenaire1-1200.webp 1200w
            "
            sizes="(max-width: 600px) 480px, (max-width: 1000px) 800px, 1200px"
            loading="lazy" 
            alt="Cabinet AGRIFORLAND - Partenaire 1" 
            data-alt-i18n="partner_1"
            class="h-12 max-w-none"
          />
          <img 
            src="cache/partenaires_partenaire2-800.webp" 
            srcset="
              cache/partenaires_partenaire2-480.webp 480w, 
              cache/partenaires_partenaire2-800.webp 800w, 
              cache/partenaires_partenaire2-1200.webp 1200w
            "
            sizes="(max-width: 600px) 480px, (max-width: 1000px) 800px, 1200px"
            loading="lazy" 
            alt="Cabinet AGRIFORLAND - Partenaire 2" 
            data-alt-i18n="partner_2"
            class="h-12 max-w-none"
          />
          <img 
            src="cache/partenaires_partenaire3-800.webp" 
            srcset="
              cache/partenaires_partenaire3-480.webp 480w, 
              cache/partenaires_partenaire3-800.webp 800w, 
              cache/partenaires_partenaire3-1200.webp 1200w
            "
            sizes="(max-width: 600px) 480px, (max-width: 1000px) 800px, 1200px"
            loading="lazy" 
            alt="Cabinet AGRIFORLAND - Partenaire 3" 
            data-alt-i18n="partner_3"
            class="h-12 max-w-none"
          />
          <img 
            src="cache/partenaires_partenaire5-800.webp" 
            srcset="
              cache/partenaires_partenaire5-480.webp 480w, 
              cache/partenaires_partenaire5-800.webp 800w, 
              cache/partenaires_partenaire5-1200.webp 1200w
            "
            sizes="(max-width: 600px) 480px, (max-width: 1000px) 800px, 1200px"
            loading="lazy" 
            alt="Cabinet AGRIFORLAND - Partenaire 5" 
            data-alt-i18n="partner_5"
            class="h-12 max-w-none"
          />
          <img 
            src="cache/partenaires_partenaire6-800.webp" 
            srcset="
              cache/partenaires_partenaire6-480.webp 480w, 
              cache/partenaires_partenaire6-800.webp 800w, 
              cache/partenaires_partenaire6-1200.webp 1200w
            "
            sizes="(max-width: 600px) 480px, (max-width: 1000px) 800px, 1200px"
            loading="lazy" 
            alt="Cabinet AGRIFORLAND - Partenaire 6" 
            data-alt-i18n="partner_6"
            class="h-12 max-w-none"
          />
          <img 
            src="cache/partenaires_partenaire7-800.webp" 
            srcset="
              cache/partenaires_partenaire7-480.webp 480w, 
              cache/partenaires_partenaire7-800.webp 800w, 
              cache/partenaires_partenaire7-1200.webp 1200w
            "
            sizes="(max-width: 600px) 480px, (max-width: 1000px) 800px, 1200px"
            loading="lazy" 
            alt="Cabinet AGRIFORLAND - Partenaire 7" 
            data-alt-i18n="partner_7"
            class="h-12 max-w-none"
          />
          <img 
            src="cache/partenaires_partenaire8-800.webp" 
            srcset="
              cache/partenaires_partenaire8-480.webp 480w, 
              cache/partenaires_partenaire8-800.webp 800w, 
              cache/partenaires_partenaire8-1200.webp 1200w
            "
            sizes="(max-width: 600px) 480px, (max-width: 1000px) 800px, 1200px"
            loading="lazy" 
            alt="Cabinet AGRIFORLAND - Partenaire 8" 
            data-alt-i18n="partner_8"
            class="h-12 max-w-none"
          />
          <img 
            src="cache/partenaires_partenaire9-800.webp" 
            srcset="
              cache/partenaires_partenaire9-480.webp 480w, 
              cache/partenaires_partenaire9-800.webp 800w, 
              cache/partenaires_partenaire9-1200.webp 1200w
            "
            sizes="(max-width: 600px) 480px, (max-width: 1000px) 800px, 1200px"
            loading="lazy" 
            alt="Cabinet AGRIFORLAND - Partenaire 9" 
            data-alt-i18n="partner_9"
            class="h-12 max-w-none"
          />
          <img 
            src="cache/partenaires_olam-agri-800.webp" 
            srcset="
              cache/partenaires_olam-agri-480.webp 480w, 
              cache/partenaires_olam-agri-800.webp 800w, 
              cache/partenaires_olam-agri-1200.webp 1200w
            "
            sizes="(max-width: 600px) 480px, (max-width: 1000px) 800px, 1200px"
            loading="lazy" 
            alt="Cabinet AGRIFORLAND - Partenaire olam" 
            data-alt-i18n="olam_agri"
            class="h-12 max-w-none"
          />
          <img 
            src="cache/partenaires_sucaf-800.webp" 
            srcset="
              cache/partenaires_sucaf-480.webp 480w, 
              cache/partenaires_sucaf-800.webp 800w, 
              cache/partenaires_sucaf-1200.webp 1200w
            "
            sizes="(max-width: 600px) 480px, (max-width: 1000px) 800px, 1200px"
            loading="lazy" 
            alt="Cabinet AGRIFORLAND - Partenaire sucaf" 
            data-alt-i18n="sucaf"
            class="h-12 max-w-none"
          />
          <img 
            src="cache/partenaires_solidaridad-800.webp" 
            srcset="
              cache/partenaires_solidaridad-480.webp 480w, 
              cache/partenaires_solidaridad-800.webp 800w, 
              cache/partenaires_solidaridad-1200.webp 1200w
            "
            sizes="(max-width: 600px) 480px, (max-width: 1000px) 800px, 1200px"
            loading="lazy" 
            alt="Cabinet AGRIFORLAND - Partenaire solidaridad" 
            data-alt-i18n="solidaridad"
            class="h-12 max-w-none"
          />
        </div>
      </div>
    </div>
  </section>

<!-- Section SEO AGRIFORLAND -->
<section class="py-8 bg-gray-50">
  <div class="max-w-4xl mx-auto px-4 text-center">
    <h2 class="text-2xl font-bold mb-4">AGRIFORLAND SARL - Votre partenaire expert en Côte d'Ivoire</h2>
    <div class="text-gray-700 leading-relaxed space-y-3">
      <p>
        <strong>AGRIFORLAND SARL</strong> est votre cabinet de conseil de référence en Côte d'Ivoire depuis 2023. 
        Notre équipe <strong>AGRIFORLAND SARL</strong> intervient dans toute la Côte d'Ivoire : 
        Abidjan, Bouaké, Yamoussoukro, San Pedro, Korhogo, Man, Daloa.
      </p>
      <p>
        Société <strong>AGRIFORLAND SARL</strong> - Expertise <strong>AGRIFORLAND SARL</strong> - 
        Projets <strong>AGRIFORLAND SARL</strong> - Consulting <strong>AGRIFORLAND SARL</strong> - 
        Agriculture Côte d'Ivoire - Environnement Côte d'Ivoire - BTP Côte d'Ivoire - 
        Développement durable Côte d'Ivoire.
      </p>
      <p class="font-medium text-agri-green">
        Contactez <strong>AGRIFORLAND SARL</strong> dès aujourd'hui pour vos projets en agriculture, 
        environnement, BTP, technologies et développement durable en Côte d'Ivoire.
      </p>
    </div>
  </div>
</section>

<!-- Footer -->
    <?php include __DIR__ . '/footer.php'; ?>

  <script>
    // Language translations
    const translations = {
      fr: {
        page_title: "AGRIFORLAND | Cabinet de Conseil Agriculture Environnement Côte d'Ivoire",
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
        portfolios: "Portfolio",
        hero_background: "Arrière-plan héro",
        hero_title: "AGRIFORLAND, le partenaire de confiance pour les solutions vertes",
        featured_news: "À la une",
        learn_more: "En savoir plus",
        no_news: "Aucune actualité disponible pour le moment.",
        who_we_are_image: "Qui sommes-nous",
        who_we_are_title: "Qui est AGRIFORLAND ?",
        who_we_are_description: "AGRIFORLAND est LE cabinet de conseil multidisciplinaire de référence en Côte d'Ivoire. Depuis 2023, AGRIFORLAND SARL accompagne les entreprises dans leurs projets stratégiques. L'équipe AGRIFORLAND couvre une gamme complète de services : agriculture, environnement, BTP, technologies.",
        who_we_are_description_2: "Pourquoi choisir AGRIFORLAND ? Nos experts AGRIFORLAND maîtrisent tous les secteurs clés du développement ivoirien. Contactez AGRIFORLAND maintenant pour transformer vos projets en succès.",
        who_we_are_tagline: "AGRIFORLAND SARL : 10 pôles d'expertise, une seule ambition - votre réussite.",
        discover_agriforland: "Découvrez AGRIFORLAND",
        agriforland_brief: "AGRIFORLAND en bref",
        agriforland_keywords: "Cabinet AGRIFORLAND - Société AGRIFORLAND - Équipe AGRIFORLAND - Projets AGRIFORLAND - Expertise AGRIFORLAND - Services AGRIFORLAND - Côte d'Ivoire",        value_1: "Expertise polyvalente",
        value_2: "Solutions vertes",
        value_3: "Partenariat solide",
        our_poles: "Nos pôles",
        previous_pole: "Pôle précédent",
        next_pole: "Pôle suivant",
        farming_nbs: "Farming & NBS",
        technologies: "Technologies",
        btp_real_estate: "BTP & Immobilier",
        eco_expertise: "Eco-expertise",
        logistics: "Logistics",
        energies: "Energies",
        industries: "Industries",
        communication: "Communication",
        pole_farming_nbs: "Farming & NBS",
        pole_technologies: "Technologies",
        pole_btp_real_estate: "BTP & Immobilier",
        pole_eco_expertise: "Eco-expertise",
        pole_logistics: "Logistics",
        pole_energies: "Energies",
        pole_industries: "Industries",
        pole_communication: "Communication",
        see_more: "Voir Plus",
        our_completed_projects: "Nos projets réalisés",
        project_1_image: "Étude de base dans les communautés riveraines de Mabi-Yaya",
        project_1_title: "ÉTUDE DE BASE DANS LES COMMUNAUTÉS RIVERAINES DE MABI-YAYA",
        project_1_description: "L'étude de base du projet 'Sustainable rubber for communities (SR4C)' vise à mieux comprendre les dy...",
        project_2_image: "Stratégie nationale pour les réserves naturelles volontaires",
        project_2_title: "STRATÉGIE NATIONALE POUR LES RÉSERVES NATURELLES VOLONTAIRES",
        project_2_description: "Dans le cadre de l'amélioration de la gestion des aires protégées du Complexe forestier de Taï-Grebo...",
        project_3_image: "Cartographie des producteurs de caoutchouc en Côte d'Ivoire",
        project_3_title: "CARTOGRAPHIE DES PRODUCTEURS DE CAOUTCHOUC EN CÔTE D'IVOIRE",
        project_3_description: "OLAM AGRI Rubber a confié à AGRIFORLAND la mission de cartographier les plantations d'hévéas de 400 ...",
        intervention_zones_background: "Zones d'intervention et bilan arrière-plan",
        intervention_zones: "ZONES D'INTERVENTION",
        activity_report: "BILAN D'ACTIVITÉS",
        completed_projects: "Projets réalisés",
        intervention_areas: "Zones d'interventions",
        people_sensitized: "Personnes sensibilisées",
        they_trust_us: "Ils nous font confiance",
        partner_1: "Partenaire 1",
        partner_2: "Partenaire 2",
        partner_3: "Partenaire 3",
        partner_5: "Partenaire 5",
        partner_6: "Partenaire 6",
        partner_7: "Partenaire 7",
        partner_8: "Partenaire 8",
        partner_9: "Partenaire 9",
        olam_agri: "OLAM AGRI",
        sucaf: "Sucaf",
        solidaridad: "Solidaridad",
        follow_us: "SUIVEZ-NOUS",
        facebook: "Facebook",
        instagram: "Instagram",
        twitter: "Twitter",
        linkedin: "LinkedIn",
        useful_links: "Liens Utiles",
        contact: "Contact",
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
        copyright: "© 2025 Agriforland. Tous droits réservés."
      },
      en: {
        page_title: "AGRIFORLAND – Expert in Agroforestry and Sustainable Reforestation in Côte d'Ivoire",
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
        portfolios: "Portfolio",
        hero_background: "Hero background",
        hero_title: "AGRIFORLAND, the trusted partner for green solutions",
        featured_news: "Featured News",
        learn_more: "Learn more",
        no_news: "No news available at the moment.",
        who_we_are_image: "Who we are",
    who_we_are_title: "Who is AGRIFORLAND?",
    who_we_are_description: "AGRIFORLAND is THE reference multidisciplinary consulting firm in Côte d'Ivoire. Since 2023, AGRIFORLAND SARL has been supporting companies in their strategic projects. The AGRIFORLAND team covers a complete range of services: agriculture, environment, construction, technology.",
    who_we_are_description_2: "Why choose AGRIFORLAND? Our AGRIFORLAND experts master all key sectors of Ivorian development. Contact AGRIFORLAND now to transform your projects into success.",
    who_we_are_tagline: "AGRIFORLAND SARL: 10 areas of expertise, one ambition - your success.",
    discover_agriforland: "Discover AGRIFORLAND",
    agriforland_brief: "AGRIFORLAND at a glance",
    agriforland_keywords: "AGRIFORLAND Firm - AGRIFORLAND Company - AGRIFORLAND Team - AGRIFORLAND Projects - AGRIFORLAND Expertise - AGRIFORLAND Services - Côte d'Ivoire",        value_1: "Versatile expertise",
        value_2: "Green solutions",
        value_3: "Solid partnership",
        our_poles: "Our Divisions",
        previous_pole: "Previous division",
        next_pole: "Next division",
        farming_nbs: "Farming & NBS",
        technologies: "Technologies",
        btp_real_estate: "Construction & Real Estate",
        eco_expertise: "Eco-expertise",
        logistics: "Logistics",
        energies: "Energies",
        industries: "Industries",
        communication: "Communication",
        pole_farming_nbs: "Farming & NBS",
        pole_technologies: "Technologies",
        pole_btp_real_estate: "Construction & Real Estate",
        pole_eco_expertise: "Eco-expertise",
        pole_logistics: "Logistics",
        pole_energies: "Energies",
        pole_industries: "Industries",
        pole_communication: "Communication",
        see_more: "See More",
        our_completed_projects: "Our Completed Projects",
        project_1_image: "Baseline study in Mabi-Yaya riparian communities",
        project_1_title: "BASELINE STUDY IN MABI-YAYA RIPARIAN COMMUNITIES",
        project_1_description: "The baseline study of the 'Sustainable rubber for communities (SR4C)' project aims to better understand the dy...",
        project_2_image: "National strategy for voluntary natural reserves",
        project_2_title: "NATIONAL STRATEGY FOR VOLUNTARY NATURAL RESERVES",
        project_2_description: "Within the framework of improving the management of protected areas of the Taï-Grebo forest complex...",
        project_3_image: "Mapping of rubber producers in Côte d'Ivoire",
        project_3_title: "MAPPING OF RUBBER PRODUCERS IN CÔTE D'IVOIRE",
        project_3_description: "OLAM AGRI Rubber entrusted AGRIFORLAND with the mission of mapping the rubber plantations of 400...",
        intervention_zones_background: "Intervention zones and report background",
        intervention_zones: "INTERVENTION ZONES",
        activity_report: "ACTIVITY REPORT",
        completed_projects: "Completed projects",
        intervention_areas: "Intervention areas",
        people_sensitized: "People sensitized",
        they_trust_us: "They trust us",
        partner_1: "Partner 1",
        partner_2: "Partner 2",
        partner_3: "Partner 3",
        partner_5: "Partner 5",
        partner_6: "Partner 6",
        partner_7: "Partner 7",
        partner_8: "Partner 8",
        partner_9: "Partner 9",
        olam_agri: "OLAM AGRI",
        sucaf: "Sucaf",
        solidaridad: "Solidaridad",
        follow_us: "FOLLOW US",
        facebook: "Facebook",
        instagram: "Instagram",
        twitter: "Twitter",
        linkedin: "LinkedIn",
        useful_links: "Useful Links",
        contact: "Contact",
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
        copyright: "© 2025 Agriforland. All rights reserved."
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

    // Newsletter form
    const newsletterForm = document.getElementById('newsletter-form');
    const newsletterMsg = document.getElementById('newsletter-msg');
    newsletterForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      const formData = new FormData(newsletterForm);
      const currentLang = localStorage.getItem('language') || 'fr';
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
        newsletterMsg.classList.remove('hidden');
        newsletterMsg.classList.remove('text-green-600');
        newsletterMsg.classList.add('text-red-600');
        newsletterMsg.textContent = translations[currentLang].error_newsletter;
      }
    });

    // Map and counters
    document.addEventListener("DOMContentLoaded", function () {
      if (document.getElementById("map")) {
        var map = L.map("map").setView([7.539989, -5.54708], 7);
        L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
          attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(map);
        var locations = [
          { name: "Abidjan", coords: [5.3363, -4.0277] },
          { name: "Bouaké", coords: [7.6898, -5.0303] },
          { name: "Tiassalé", coords: [5.8977, -4.8221] },
          { name: "Taï", coords: [5.8667, -7.3833] },
          { name: "Aboisso", coords: [5.4671, -3.2074] },
          { name: "Bongouanou", coords: [6.6517, -4.2044] },
          { name: "Neagui", coords: [8.0000, -6.0000] },
          { name: "Soubré", coords: [5.7856, -6.6080] },
          { name: "Gagnoa", coords: [6.1319, -5.9506] },
          { name: "Divo", coords: [5.8378, -5.3572] },
          { name: "Adzopé", coords: [6.1069, -3.8602] },
          { name: "Guiglo", coords: [6.5670, -7.5000] },
          { name: "Man", coords: [7.4125, -7.5538] },
          { name: "Bangolo", coords: [7.0126, -7.4846] },
          { name: "Mahapleu", coords: [7.0833, -7.7000] },
          { name: "Abengourou", coords: [6.7297, -3.4964] },
          { name: "Zagné", coords: [6.2500, -7.5000] },
          { name: "Agboville", coords: [5.9280, -4.2136] },
          { name: "Bouna", coords: [9.2699, -2.9951] },
          { name: "Dabakala", coords: [8.3655, -4.3147] },
          { name: "Abatta", coords: [5.3874, -3.9252] },
          { name: "Bonon", coords: [7.0667, -6.3667] },
          { name: "Oumé", coords: [6.3833, -5.4167] },
          { name: "Sassandra", coords: [4.9539, -6.0865] },
          { name: "San Pedro", coords: [4.7485, -6.6363] },
          { name: "Dimbokro", coords: [6.6500, -4.7000] }
        ];
        locations.forEach(function (loc) {
          L.marker(loc.coords).addTo(map).bindPopup(loc.name);
        });
      }
      const counters = document.querySelectorAll(".counter");
      counters.forEach(counter => {
        counter.innerText = "0";
        const updateCounter = () => {
          const target = +counter.getAttribute("data-target");
          const count = +counter.innerText;
          const increment = target / 100;
          if (count < target) {
            counter.innerText = "+" + Math.ceil(count + increment);
            setTimeout(updateCounter, 50);
          } else {
            counter.innerText = "+" + target;
          }
        };
        updateCounter();
      });

      // Navigation mobile pour actualités
      const actualitesContainer = document.getElementById('actualitesContainer');
      const actualitesLeft = document.getElementById('actualitesLeft');
      const actualitesRight = document.getElementById('actualitesRight');
      
      if (actualitesContainer && actualitesLeft && actualitesRight) {
        actualitesLeft.addEventListener('click', () => {
          actualitesContainer.scrollBy({ left: -300, behavior: 'smooth' });
        });
        
        actualitesRight.addEventListener('click', () => {
          actualitesContainer.scrollBy({ left: 300, behavior: 'smooth' });
        });
        
        // Auto-hide/show buttons based on scroll position
        function updateButtons() {
          const { scrollLeft, scrollWidth, clientWidth } = actualitesContainer;
          actualitesLeft.style.opacity = scrollLeft > 0 ? '1' : '0.5';
          actualitesRight.style.opacity = scrollLeft < scrollWidth - clientWidth - 10 ? '1' : '0.5';
        }
        
        actualitesContainer.addEventListener('scroll', updateButtons);
        updateButtons(); // Initial call
      }
    });

    // Toggle menu mobile
    const toggle = document.getElementById('menu-toggle');
    const menu = document.getElementById('mobile-menu');
    toggle.addEventListener('click', () => {
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

    // Preloader
    window.addEventListener("load", function () {
      const preloader = document.getElementById('preloader');
      preloader.classList.add('opacity-0', 'pointer-events-none', 'transition-opacity', 'duration-500');
      setTimeout(() => preloader.remove(), 500);
    });

    // Poles carousel
    const polesContainer = document.getElementById('polesContainer');
    const polesLeft = document.getElementById('polesLeft');
    const polesRight = document.getElementById('polesRight');
    polesLeft.addEventListener('click', () => {
      polesContainer.scrollBy({ left: -300, behavior: 'smooth' });
    });
    polesRight.addEventListener('click', () => {
      polesContainer.scrollBy({ left: 300, behavior: 'smooth' });
    });

    // À la une carousel
    const container = document.getElementById('a-la-une-scroll');
    if (container) {
      let isPaused = false;
      let scrollAmount = 0;
      function autoScroll() {
        if (!isPaused) {
          scrollAmount += 9;
          if (scrollAmount >= container.scrollWidth - container.clientWidth) {
            container.scrollTo({ left: 0, behavior: 'smooth' });
            scrollAmount = 0;
          } else {
            container.scrollTo({ left: scrollAmount, behavior: 'smooth' });
          }
        }
      }
      const interval = setInterval(autoScroll, 100);
      container.addEventListener('mouseenter', () => isPaused = true);
      container.addEventListener('mouseleave', () => isPaused = false);
      container.addEventListener('touchstart', () => isPaused = true);
      container.addEventListener('touchend', () => isPaused = false);
    }

    // Logo carousel
    const wrapper = document.getElementById('logo-carousel-wrapper');
    if (wrapper) {
      const carousel = wrapper.querySelector('.logo-carousel');
      const clone = carousel.cloneNode(true);
      wrapper.appendChild(clone);
      const pause = () => wrapper.classList.add('paused');
      const resume = () => wrapper.classList.remove('paused');
      wrapper.addEventListener('mouseenter', pause);
      wrapper.addEventListener('mouseleave', resume);
      wrapper.addEventListener('touchstart', pause);
      wrapper.addEventListener('touchend', resume);
    }
  </script>
</body>
</html>