<?php
  header('Content-Type: text/html; charset=UTF-8');
  include('admin/includes/db.php'); // Inclure la connexion à la base de données
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
  <meta name="description" content="Découvrez AGRIFORLAND SARL : cabinet conseil multidisciplinaire fondé en 2023 en Côte d'Ivoire. Notre équipe d'experts en agriculture durable.">
  <meta name="keywords" content="AGRIFORLAND, à propos, équipe, vision, mission, cabinet conseil, agriculture, Côte d'Ivoire">
  <meta name="author" content="AGRIFORLAND SARL">
  <meta property="og:title" content="À Propos AGRIFORLAND | Notre Histoire et Équipe">
  <meta property="og:description" content="Découvrez AGRIFORLAND SARL : cabinet conseil multidisciplinaire fondé en 2023 en Côte d'Ivoire. Notre équipe d'experts en agriculture durable.">
  <meta property="og:image" content="https://www.agriforland.com/cache/logo-198x66-1200.webp">
  <meta property="og:url" content="https://www.agriforland.com/about.php">
  <meta name="twitter:card" content="summary_large_image">
  <link rel="canonical" href="https://www.agriforland.com/about.php">
<title data-i18n="page_title">À Propos AGRIFORLAND | Notre Histoire, Vision et Équipe</title>
  <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@400;700&family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <!-- Charger la police Phosphor Icons -->
  <script src="https://unpkg.com/@phosphor-icons/web"></script>
  <link href="css/Style.css" rel="stylesheet">
  <!-- Phosphor Icons -->
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
  <!-- AJOUTEZ avant </head> -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "AboutPage",
  "mainEntity": {
    "@type": "Organization",
    "name": "AGRIFORLAND SARL",
    "foundingDate": "2023-05",
    "description": "Cabinet conseil multidisciplinaire en agriculture, environnement et développement durable en Côte d'Ivoire"
  }
}
</script>
<style>
  /* Animations personnalisées */
  @keyframes fadeInUp {
      from {
          opacity: 0;
          transform: translateY(20px);
      }
      to {
          opacity: 1;
          transform: translateY(0);
      }
  }
  .animate-fadeInUp {
      animation: fadeInUp 0.6s ease-out;
  }
</style>
</head>

<body class="bg-[#f6ffde] text-black">
  <!-- Preloader -->
  <div id="preloader" class="fixed inset-0 bg-[#f6ffde] z-50 flex items-center justify-center">
    <div class="animate-triangle w-32 h-32">
      <img src="images/triangle-svgrepo-com.svg" loading="lazy" alt="AGRIFORLAND - BARRE DE RECHARGEMENT " data-alt-i18n="loading" class="w-full h-full object-contain triangle-img">
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
        alt="AGRIFORLAND - banniere" 
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
            <img id="language-icon" loading="lazy" src="images/fr.webp" alt="AGRIFORLAND - drapeau francais" data-alt-i18n="language" class="h-5 w-5">
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
            <img id="language-icon-mobile" src="images/fr.webp" alt="AGRIFORLAND - drapeau fr" data-alt-i18n="language" class="h-5 w-5">
          </div>
        </div>
        <a href="recrutement.html" class="bg-[#759916] text-white px-4 py-2 rounded-md text-center text-sm hover:bg-[#ade126] transition" data-i18n="join_us">Nous Rejoindre</a>
        <a href="contact.html" class="border border-gray-500 px-4 py-2 rounded-md text-center text-sm hover:bg-white transition" data-i18n="contact_us">Nous Contacter</a>
      </div>
    </div>
  </header>

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
      loading="lazy" 
      alt="AGRIFORLAND - Banniere " 
      data-alt-i18n="hero_background"
      class="w-full h-[300px] md:h-[400px] object-cover" 
    />
    <div class="absolute top-0 left-0 w-full h-full bg-black/50 flex flex-col justify-center items-center text-center text-white px-4">
      <h1 class="text-3xl sm:text-4xl md:text-5xl font-bold pb-4 sm:pb-6" data-i18n="welcome_title">À Propos AGRIFORLAND : Notre Histoire et Expertise</h1>
      <p class="text-center px-4 sm:px-8 md:px-16" data-i18n="welcome_description">Votre partenaire d'excellence dans les secteurs de l'agriculture, de la foresterie, de l'environnement, du développement durable, du foncier, de l'immobilier, de l'industrie et de la technologie</p>
    </div>
  </section>

  <!-- A la une -->
<!-- REMPLACEZ la section complète par ceci -->
<section class="py-8 sm:py-12 px-4 max-w-7xl mx-auto bg-[#f6ffde]">
  <h2 class="text-3xl sm:text-3xl font-bold text-center mb-6 sm:mb-8 font-kanit" data-i18n="who_we_are">QUI SOMMES-NOUS ?</h2>
  <p class="relative text-justify mx-4 sm:mx-8 md:mx-16" data-i18n="company_description">
    Depuis sa création en mai 2023, AGRIFORLAND SARL accompagne avec succès les acteurs du développement durable en Côte d'Ivoire. Notre cabinet AGRIFORLAND s'appuie sur une équipe de 15 experts multidisciplinaires dirigée par Abdoul-Kader Ouattara. Les solutions AGRIFORLAND ont déjà transformé plus de 50 projets dans 25 zones d'intervention. L'approche AGRIFORLAND privilégie l'innovation tout en respectant les réalités locales ivoiriennes. Choisir AGRIFORLAND, c'est opter pour un partenaire qui transforme les défis en opportunités durables.
  </p>
  
  <p class="mt-4 relative text-justify mx-4 sm:mx-8 md:mx-16" data-i18n="company_links">
    Découvrez nos <a href="poles.html" class="text-[#759916] hover:text-[#ade126] underline">10 pôles d'expertise AGRIFORLAND</a> et consultez nos <a href="projets.html" class="text-[#759916] hover:text-[#ade126] underline">projets réalisés AGRIFORLAND</a> qui témoignent de notre savoir-faire reconnu.
  </p>
</section>

  <!-- Notre vision -->
  <section class="py-16 px-4 sm:px-6 max-w-7xl mx-auto grid md:grid-cols-2 gap-12 items-center bg-[#fbfff0]">
    <div class="order-2 md:order-1">
      <img 
        src="cache/nvision-800.webp" 
        srcset="
          cache/nvision-480.webp 480w, 
          cache/nvision-800.webp 800w, 
          cache/nvision-1200.webp 1200w
        "
        sizes="(max-width: 600px) 480px, (max-width: 1000px) 800px, 1200px"
        loading="lazy" 
        alt="AGRIFORLAND - image vision" 
        data-alt-i18n="vision_illustration"
        class="rounded-xl w-full h-auto object-cover shadow-lg transition-transform duration-300 hover:scale-[1.02]"
      />
    </div>
    <div class="order-1 md:order-2">
      <h2 class="text-3xl md:text-4xl font-bold mb-8 relative pb-3 uppercase text-[#2c541d]">
        <span data-i18n="our_vision">NOTRE VISION</span>
        <span class="absolute bottom-0 left-0 w-20 h-1 bg-[#a9cf46]"></span>
      </h2>
      <p class="text-lg mb-8 leading-relaxed text-gray-700" data-i18n="vision_description">
        AGRIFORLAND aspire à devenir LE leader africain dans la conception et la mise en œuvre de solutions durables. L'équipe AGRIFORLAND place systématiquement les communautés locales au cœur de ses actions. Choisir AGRIFORLAND, c'est opter pour un partenaire qui allie prospérité économique et régénération des écosystèmes.
      </p>
      <p class="font-semibold italic text-[#2c541d] text-xl border-l-4 border-[#a9cf46] pl-4 py-2" data-i18n="vision_quote">
        "Votre partenaire pour un développement harmonieux entre économie et écologie"
      </p>
    </div>
  </section>

  <!-- Notre Mission -->
  <section class="py-12 bg-[#f6ffde] text-center">
    <h2 class="text-3xl sm:text-3xl font-bold text-center mb-6 sm:mb-8 font-kanit" data-i18n="our_mission">Notre Mission</h2>
    <div class="max-w-5xl mx-auto grid md:grid-cols-3 gap-6 px-4">
      <div>
        <i class="ph ph-gear text-4xl text-[#759916] mb-4 hover:text-white transition-colors duration-300"></i>
        <h3 class="font-bold text-lg hover:text-[#759916] transition-colors duration-300" data-i18n="mission_1_title">Innover pour répondre aux défis agricoles</h3>
        <p class="text-sm" data-i18n="mission_1_desc">environnementaux et sociaux grâce à des solutions durables et inclusives</p>
      </div>
      <div>
        <i class="ph ph-spinner-gap text-4xl text-[#759916] mb-4 hover:text-white transition-colors duration-300"></i>
        <h3 class="font-bold text-lg hover:text-[#759916] transition-colors duration-300" data-i18n="mission_2_title">Restaurer les écosystèmes</h3>
        <p class="text-sm" data-i18n="mission_2_desc">Naturels tout en valorisant les ressources locales</p>
      </div>
      <div>
        <i class="ph ph-handshake text-4xl text-[#759916] mb-4 hover:text-white transition-colors duration-300"></i>
        <h3 class="font-bold text-lg hover:text-[#759916] transition-colors duration-300" data-i18n="mission_3_title">Transformer positivement</h3>
        <p class="text-sm" data-i18n="mission_3_desc">La qualité de vie des communautés grâce à des approches participatives</p>
      </div>
    </div>
  </section>

  <!-- Section Notre Engagement -->
  <section class="py-12 px-4 max-w-7xl mx-auto bg-[#fbfff0]">
    <div class="text-center mb-12">
      <h2 class="text-3xl font-bold" data-i18n="our_commitment">Notre Engagement</h2>
      <span class="absolute ml-5 left-0 w-20 h-2 rounded-xl bg-[#a9cf46]"></span>
    </div>
    <div class="grid md:grid-cols-2 gap-6 items-center">
      <div>
        <p class="font-semibold text-xl mb-4" data-i18n="commitment_description">AGRIFORLAND aspire à devenir un acteur de référence dans la conception et la mise en œuvre de solutions durables et innovantes, en plaçant systématiquement les communautés locales et la préservation de l'environnement au cœur de ses actions. Nous sommes convaincus que la prospérité économique doit aller de pair avec la régénération des écosystèmes et l'équité sociale.</p>
      </div>
      <img 
        src="cache/md-800.webp" 
        srcset="
          cache/md-480.webp 480w, 
          cache/md-800.webp 800w, 
          cache/md-1200.webp 1200w
        "
        sizes="(max-width: 600px) 480px, (max-width: 1000px) 800px, 1200px"
        loading="lazy" 
        alt="AGRIFORLAND - image engagement" 
        data-alt-i18n="commitment_illustration"
        class="rounded-xl"
      />
    </div>
  </section>

  <!-- Valeurs -->
  <section class="py-12 bg-[#f6ffde] text-center">
    <h2 class="text-3xl sm:text-3xl font-bold text-center mb-6 sm:mb-8 font-kanit" data-i18n="our_values">Nos Valeurs</h2>
    <div class="max-w-5xl mx-auto grid md:grid-cols-3 gap-6 px-4">
      <div>
        <i class="ph ph-lamp-pendant text-4xl text-[#759916] mb-4 hover:text-white transition-colors duration-300"></i>
        <h3 class="font-semibold text-lg hover:text-[#759916] transition-colors duration-300" data-i18n="value_1">Fournir des prestations techniques de haute qualité</h3>
      </div>
      <div>
        <i class="ph ph-perspective text-4xl text-[#759916] mb-4 hover:text-white transition-colors duration-300"></i>
        <h3 class="font-semibold text-lg hover:text-[#759916] transition-colors duration-300" data-i18n="value_2">Respecter les délais et les budgets</h3>
      </div>
      <div>
        <i class="ph ph-signpost text-4xl text-[#759916] mb-4 hover:text-white transition-colors duration-300"></i>
        <h3 class="font-semibold text-lg hover:text-[#759916] transition-colors duration-300" data-i18n="value_3">Transférer nos méthodes de savoir-faire à nos clients</h3>
      </div>
    </div>
    <div class="max-w-5xl mx-auto grid md:grid-cols-3 gap-6 px-4">
      <div>
        <i class="ph ph-globe-simple-x text-4xl text-[#759916] mb-4 hover:text-white transition-colors duration-300"></i>
        <h3 class="font-semibold text-lg hover:text-[#759916] transition-colors duration-300" data-i18n="value_4">Contribuer positivement au développement local</h3>
      </div>
      <div>
        <i class="ph ph-apple-podcasts-logo text-4xl text-[#759916] mb-4 hover:text-white transition-colors duration-300"></i>
        <h3 class="font-semibold text-lg hover:text-[#759916] transition-colors duration-300" data-i18n="value_5">Maintenir les normes éthiques et professionnelles</h3>
      </div>
      <div>
        <i class="ph ph-cube text-4xl text-[#759916] mb-4 hover:text-white transition-colors duration-300"></i>
        <h3 class="font-semibold text-lg hover:text-[#759916] transition-colors duration-300" data-i18n="value_6">Promouvoir l'innovation, l'excellence dans tous nos projets</h3>
      </div>
    </div>
  </section>

  <!-- NOS ÉQUIPES DYNAMIQUES -->
  <section class="py-16 px-4 bg-[#f8fafc]">
    <!-- Titre principal -->
    <div class="max-w-7xl mx-auto text-center mb-16">
      <h2 class="text-4xl font-bold text-[#2d3748] mb-4" data-i18n="our_dynamic_teams">NOS ÉQUIPES DYNAMIQUES</h2>
      <div class="w-24 h-1 bg-[#a9cf46] mx-auto"></div>
    </div>

    <!-- Directeur Général -->
    <div class="max-w-7xl mx-auto text-center mb-16">
      <div class="flex justify-center">
        <div class="text-center group relative">
          <!-- Photo avec effet de survol -->
          <div class="w-48 h-48 rounded-full border-4 border-[#759916] shadow-lg mx-auto overflow-hidden mb-4 relative">
            <img 
              src="cache/teams_dg1-1-800.webp" 
              srcset="
                cache/teams_dg1-1-480.webp 480w, 
                cache/teams_dg1-1-800.webp 800w, 
                cache/teams_dg1-1-1200.webp 1200w
              "
              sizes="(max-width: 600px) 480px, (max-width: 1000px) 800px, 1200px"
              loading="lazy" 
              alt="AGRIFORLAND - directeur general" 
              data-alt-i18n="general_director"
              class="w-full h-full object-cover"
            />
            <!-- Overlay et icônes sociales -->
            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40 transition-all duration-300 flex items-center justify-center space-x-4 opacity-0 group-hover:opacity-100">
              <a href="https://www.facebook.com/abdoulkader.ouattara.925" class="text-white bg-[#a9cf46] rounded-full p-3 hover:bg-[#759916] transition-colors duration-300" aria-label="" data-aria-i18n="facebook">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                  <path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"/>
                </svg>
              </a>
              <a href="https://www.linkedin.com/in/abdoul-kader-ouattara-95bab21a2/" class="text-white bg-[#a9cf46] rounded-full p-3 hover:bg-[#759916] transition-colors duration-300" aria-label="" data-aria-i18n="linkedin">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/>
                </svg>
              </a>
              <a href="mailto:ak.ouattara@agriforland.com" class="text-white bg-[#a9cf46] rounded-full p-3 hover:bg-[#759916] transition-colors duration-300" aria-label="" data-aria-i18n="email">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M22 4H2v16h20V4zm-2 4l-8 5-8-5V6l8 5 8-5v2z"/>
                </svg>
              </a>
            </div>
          </div>
          <h4 class="text-xl font-bold">ABDOUL-KADER OUATTARA</h4>
          <p class="text-[#a9cf46] font-medium" data-i18n="ceo_title">Directeur Général</p>
        </div>
      </div>
    </div>

    <div class="flex flex-col md:flex-row justify-between items-center border-b-2 border-black"></div>

    <!-- Grille des équipes -->
    <div class="max-w-7xl mx-auto pt-[50px]">
      <div class="grid grid-cols-2 lg:grid-cols-3 gap-8 lg:gap-12 justify-items-center">
        <!-- Membre 1 -->
        <div class="text-center group relative min-w-[100px] max-w-[200px]">
          <div class="w-32 h-32 sm:w-36 sm:h-36 rounded-full border-4 border-[#759916] shadow-lg mx-auto overflow-hidden mb-4 relative">
            <img 
              src="cache/teams_jl-800.webp" 
              srcset="
                cache/teams_jl-480.webp 480w, 
                cache/teams_jl-800.webp 800w, 
                cache/teams_jl-1200.webp 1200w
              "
              sizes="(max-width: 600px) 480px, (max-width: 1000px) 800px, 1200px"
              loading="lazy" 
              alt="AGRIFORLAND - membre jl" 
              data-alt-i18n="team_member"
              class="w-full h-full object-cover"
            />
            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40 transition-all duration-300 flex items-center justify-center space-x-2 opacity-0 group-hover:opacity-100">
              <a href="https://www.facebook.com/jl4kouassi" class="text-white bg-[#a9cf46] rounded-full p-2 hover:bg-[#759916]" aria-label="" data-aria-i18n="facebook">
                <svg class="w-5 h-5 md:w-6 md:h-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                  <path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"/>
                </svg>
              </a>
              <a href="https://www.linkedin.com/in/luckouassi/" class="text-white bg-[#a9cf46] rounded-full p-2 hover:bg-[#759916]" aria-label="" data-aria-i18n="linkedin">
                <svg class="w-5 h-5 md:w-6 md:h-6" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/>
                </svg>
              </a>
              <a href="mailto:jl.kouassi@agriforland.com" class="text-white bg-[#a9cf46] rounded-full p-2 hover:bg-[#759916]" aria-label="" data-aria-i18n="email">
                <svg class="w-5 h-5 md:w-6 md:h-6" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M22 4H2v16h20V4zm-2 4l-8 5-8-5V6l8 5 8-5v2z"/>
                </svg>
              </a>
            </div>
          </div>
          <h4 class="text-lg md:text-xl lg:text-2xl font-bold leading-tight mb-2">Dr Jean-Luc KOUASSI</h4>
          <p class="text-[#a9cf46] font-medium text-sm md:text-base leading-tight" data-i18n="tech_coordinator_title">Coordonnateur Technique Des Etudes <br> Et De la Recherche</p>
        </div>

        <!-- Membre 2 -->
        <div class="text-center group relative min-w-[100px] max-w-[200px]">
          <div class="w-32 h-32 sm:w-36 sm:h-36 rounded-full border-4 border-[#759916] shadow-lg mx-auto overflow-hidden mb-4 relative">
            <img 
              src="cache/teams_dona-1-800.webp" 
              srcset="
                cache/teams_dona-1-480.webp 480w, 
                cache/teams_dona-1-800.webp 800w, 
                cache/teams_dona-1-1200.webp 1200w
              "
              sizes="(max-width: 600px) 480px, (max-width: 1000px) 800px, 1200px"
              loading="lazy" 
              alt="AGRIFORLAND - membre donation" 
              data-alt-i18n="team_member"
              class="w-full h-full object-cover"
            />
            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40 transition-all duration-300 flex items-center justify-center space-x-2 opacity-0 group-hover:opacity-100">
              <a href="https://www.facebook.com/dgueable/friends?locale=fr_FR" class="text-white bg-[#a9cf46] rounded-full p-2 hover:bg-[#759916]" aria-label="" data-aria-i18n="facebook">
                <svg class="w-5 h-5 md:w-6 md:h-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                  <path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"/>
                </svg>
              </a>
              <a href="https://www.linkedin.com/in/donatien-gueable-ph-d-a3b08a106" class="text-white bg-[#a9cf46] rounded-full p-2 hover:bg-[#759916]" aria-label="" data-aria-i18n="linkedin">
                <svg class="w-5 h-5 md:w-6 md:h-6" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/>
                </svg>
              </a>
              <a href="mailto:d.gueable@agriforland.com" class="text-white bg-[#a9cf46] rounded-full p-2 hover:bg-[#759916]" aria-label="" data-aria-i18n="email">
                <svg class="w-5 h-5 md:w-6 md:h-6" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M22 4H2v16h20V4zm-2 4l-8 5-8-5V6l8 5 8-5v2z"/>
                </svg>
              </a>
            </div>
          </div>
          <h4 class="text-lg md:text-xl lg:text-2xl font-bold leading-tight mb-2">Dr Donatien GUEABLE</h4>
          <p class="text-[#a9cf46] font-medium text-sm md:text-base leading-tight" data-i18n="program_coordinator_title">Coordonnateur De Programme</p>
        </div>

        <!-- Membre 3 -->
        <div class="text-center group relative min-w-[100px] max-w-[200px]">
          <div class="w-32 h-32 sm:w-36 sm:h-36 rounded-full border-4 border-[#759916] shadow-lg mx-auto overflow-hidden mb-4 relative">
            <img 
              src="cache/teams_som-1-800.webp" 
              srcset="
                cache/teams_som-1-480.webp 480w, 
                cache/teams_som-1-800.webp 800w, 
                cache/teams_som-1-1200.webp 1200w
              "
              sizes="(max-width: 600px) 480px, (max-width: 1000px) 800px, 1200px"
              loading="lazy" 
              alt="AGRIFORLAND - membre som" 
              data-alt-i18n="team_member"
              class="w-full h-full object-cover"
            />
            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40 transition-all duration-300 flex items-center justify-center space-x-2 opacity-0 group-hover:opacity-100">
              <a href="https://www.facebook.com/" class="text-white bg-[#a9cf46] rounded-full p-2 hover:bg-[#759916]" aria-label="" data-aria-i18n="facebook">
                <svg class="w-5 h-5 md:w-6 md:h-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                  <path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"/>
                </svg>
              </a>
              <a href="https://www.linkedin.com/" class="text-white bg-[#a9cf46] rounded-full p-2 hover:bg-[#759916]" aria-label="" data-aria-i18n="linkedin">
                <svg class="w-5 h-5 md:w-6 md:h-6" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/>
                </svg>
              </a>
              <a href="mailto:j.som@agriforland.com" class="text-white bg-[#a9cf46] rounded-full p-2 hover:bg-[#759916]" aria-label="" data-aria-i18n="email">
                <svg class="w-5 h-5 md:w-6 md:h-6" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M22 4H2v16h20V4zm-2 4l-8 5-8-5V6l8 5 8-5v2z"/>
                </svg>
              </a>
            </div>
          </div>
          <h4 class="text-lg md:text-xl lg:text-2xl font-bold leading-tight mb-2">Sié Justin SOM</h4>
          <p class="text-[#a9cf46] font-medium text-sm md:text-base leading-tight" data-i18n="project_manager_title">Chargé De Projets</p>
        </div>

        <!-- Membre 4 -->
        <div class="text-center group relative min-w-[100px] max-w-[200px]">
          <div class="w-32 h-32 sm:w-36 sm:h-36 rounded-full border-4 border-[#759916] shadow-lg mx-auto overflow-hidden mb-4 relative">
            <img 
              src="cache/teams_coulibaly-1-800.webp" 
              srcset="
                cache/teams_coulibaly-1-480.webp 480w, 
                cache/teams_coulibaly-1-800.webp 800w, 
                cache/teams_coulibaly-1-1200.webp 1200w
              "
              sizes="(max-width: 600px) 480px, (max-width: 1000px) 800px, 1200px"
              loading="lazy" 
              alt="AGRIFORLAND - membre coulibaly" 
              data-alt-i18n="team_member"
              class="w-full h-full object-cover"
            />
            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40 transition-all duration-300 flex items-center justify-center space-x-2 opacity-0 group-hover:opacity-100">
              <a href="#" class="text-white bg-[#a9cf46] rounded-full p-2 hover:bg-[#759916]" aria-label="" data-aria-i18n="facebook">
                <svg class="w-5 h-5 md:w-6 md:h-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                  <path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"/>
                </svg>
              </a>
              <a href="" class="text-white bg-[#a9cf46] rounded-full p-2 hover:bg-[#759916]" aria-label="" data-aria-i18n="linkedin">
                <svg class="w-5 h-5 md:w-6 md:h-6" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/>
                </svg>
              </a>
              <a href="mailto:h.coulibaly@agriforland.com" class="text-white bg-[#a9cf46] rounded-full p-2 hover:bg-[#759916]" aria-label="" data-aria-i18n="email">
                <svg class="w-5 h-5 md:w-6 md:h-6" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M22 4H2v16h20V4zm-2 4l-8 5-8-5V6l8 5 8-5v2z"/>
                </svg>
              </a>
            </div>
          </div>
          <h4 class="text-lg md:text-xl lg:text-2xl font-bold leading-tight mb-2">Dr Hobonan COULIBALY</h4>
          <p class="text-[#a9cf46] font-medium text-sm md:text-base leading-tight" data-i18n="training_gender_manager_title">Chargé de Mission Formation, Genre et Développement Social</p>
        </div>

        <!-- Membre 5 -->
        <div class="text-center group relative min-w-[100px] max-w-[200px]">
          <div class="w-32 h-32 sm:w-36 sm:h-36 rounded-full border-4 border-[#759916] shadow-lg mx-auto overflow-hidden mb-4 relative">
            <img 
              src="cache/teams_md-1-800.webp" 
              srcset="
                cache/teams_md-1-480.webp 480w, 
                cache/teams_md-1-800.webp 800w, 
                cache/teams_md-1-1200.webp 1200w
              "
              sizes="(max-width: 600px) 480px, (max-width: 1000px) 800px, 1200px"
              loading="lazy" 
              alt="AGRIFORLAND - membre mohamed" 
              data-alt-i18n="team_member"
              class="w-full h-full object-cover"
            />
            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40 transition-all duration-300 flex items-center justify-center space-x-2 opacity-0 group-hover:opacity-100">
              <a href="https://www.facebook.com/mohamedtenena.coulibaly" class="text-white bg-[#a9cf46] rounded-full p-2 hover:bg-[#759916]" aria-label="" data-aria-i18n="facebook">
                <svg class="w-5 h-5 md:w-6 md:h-6" svg class="w-5 h-5 md:w-6 md:h-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                  <path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"/>
                </svg>
              </a>
              <a href="https://www.linkedin.com/in/tenena-mohamed-coulibaly-21232927b/" class="text-white bg-[#a9cf46] rounded-full p-2 hover:bg-[#759916]" aria-label="" data-aria-i18n="linkedin">
                <svg class="w-5 h-5 md:w-6 md:h-6" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/>
                </svg>
              </a>
              <a href="mailto:tm.coulibaly@agriforland.com" class="text-white bg-[#a9cf46] rounded-full p-2 hover:bg-[#759916]" aria-label="" data-aria-i18n="email">
                <svg class="w-5 h-5 md:w-6 md:h-6" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M22 4H2v16h20V4zm-2 4l-8 5-8-5V6l8 5 8-5v2z"/>
                </svg>
              </a>
            </div>
          </div>
          <h4 class="text-lg md:text-xl lg:text-2xl font-bold leading-tight mb-2">Mohamed COULIBALY</h4>
          <p class="text-[#a9cf46] font-medium text-sm md:text-base leading-tight" data-i18n="it_manager_title">Responsable IT</p>
        </div>

        <!-- Membre 6 -->
        <div class="text-center group relative min-w-[100px] max-w-[200px]">
          <div class="w-32 h-32 sm:w-36 sm:h-36 rounded-full border-4 border-[#759916] shadow-lg mx-auto overflow-hidden mb-4 relative">
            <img 
              src="cache/teams_jp-1-800.webp" 
              srcset="
                cache/teams_jp-1-480.webp 480w, 
                cache/teams_jp-1-800.webp 800w, 
                cache/teams_jp-1-1200.webp 1200w
              "
              sizes="(max-width: 600px) 480px, (max-width: 1000px) 800px, 1200px"
              loading="lazy" 
              alt="AGRIFORLAND - membre jp" 
              data-alt-i18n="team_member"
              class="w-full h-full object-cover"
            />
            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40 transition-all duration-300 flex items-center justify-center space-x-2 opacity-0 group-hover:opacity-100">
              <a href="#" class="text-white bg-[#a9cf46] rounded-full p-2 hover:bg-[#759916]" aria-label="" data-aria-i18n="facebook">
                <svg class="w-5 h-5 md:w-6 md:h-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                  <path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"/>
                </svg>
              </a>
              <a href="" class="text-white bg-[#a9cf46] rounded-full p-2 hover:bg-[#759916]" aria-label="" data-aria-i18n="linkedin">
                <svg class="w-5 h-5 md:w-6 md:h-6" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/>
                </svg>
              </a>
              <a href="mailto:jp.yao@agriforland.com" class="text-white bg-[#a9cf46] rounded-full p-2 hover:bg-[#759916]" aria-label="" data-aria-i18n="email">
                <svg class="w-5 h-5 md:w-6 md:h-6" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M22 4H2v16h20V4zm-2 4l-8 5-8-5V6l8 5 8-5v2z"/>
                </svg>
              </a>
            </div>
          </div>
          <h4 class="text-lg md:text-xl lg:text-2xl font-bold leading-tight mb-2">Jean Pierre YAO</h4>
          <p class="text-[#a9cf46] font-medium text-sm md:text-base leading-tight" data-i18n="sustainability_assistant_title">Assistant Technique en Durabilité et Formation</p>
        </div>

        <!-- Membre 7 -->
        <div class="text-center group relative min-w-[100px] max-w-[200px]">
          <div class="w-32 h-32 sm:w-36 sm:h-36 rounded-full border-4 border-[#759916] shadow-lg mx-auto overflow-hidden mb-4 relative">
            <img 
              src="cache/teams_diloman-1-800.webp" 
              srcset="
                cache/teams_diloman-1-480.webp 480w, 
                cache/teams_diloman-1-800.webp 800w, 
                cache/teams_diloman-1-1200.webp 1200w
              "
              sizes="(max-width: 600px) 480px, (max-width: 1000px) 800px, 1200px"
              loading="lazy" 
              alt="AGRIFORLAND - membre diloman" 
              data-alt-i18n="team_member"
              class="w-full h-full object-cover"
            />
            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40 transition-all duration-300 flex items-center justify-center space-x-2 opacity-0 group-hover:opacity-100">
              <a href="" class="text-white bg-[#a9cf46] rounded-full p-2 hover:bg-[#759916]" aria-label="" data-aria-i18n="facebook">
                <svg class="w-5 h-5 md:w-6 md:h-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                  <path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"/>
                </svg>
              </a>
              <a href="" class="text-white bg-[#a9cf46] rounded-full p-2 hover:bg-[#759916]" aria-label="" data-aria-i18n="linkedin">
                <svg class="w-5 h-5 md:w-6 md:h-6" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/>
                </svg>
              </a>
              <a href="mailto:a.coulibaly@agriforland.com" class="text-white bg-[#a9cf46] rounded-full p-2 hover:bg-[#759916]" aria-label="" data-aria-i18n="email">
                <svg class="w-5 h-5 md:w-6 md:h-6" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M22 4H2v16h20V4zm-2 4l-8 5-8-5V6l8 5 8-5v2z"/>
                </svg>
              </a>
            </div>
          </div>
          <h4 class="text-lg md:text-xl lg:text-2xl font-bold leading-tight mb-2">Abdoul Karim COULIBALY</h4>
          <p class="text-[#a9cf46] font-medium text-sm md:text-base leading-tight" data-i18n="rdue_assistant_title">Assistant Technique en Durabilité et RDUE</p>
        </div>

        <!-- Membres dynamiques depuis la base de données -->
        <?php
          $sql = "SELECT * FROM team_members";
          $result = $conn->query($sql);
          if ($result->num_rows > 0):
            while ($row = $result->fetch_assoc()):
        ?>
          <div class="text-center group relative min-w-[100px] max-w-[200px]">
            <div class="w-32 h-32 sm:w-36 sm:h-36 rounded-full border-4 border-[#759916] shadow-lg mx-auto overflow-hidden mb-4 relative">
              <img src="admin/assets/images/<?= htmlspecialchars($row['image'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?= htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8'); ?>" loading="lazy" class="w-full h-full object-cover">
              <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40 transition-all duration-300 flex items-center justify-center space-x-2 opacity-0 group-hover:opacity-100">
                <?php if (!empty($row['facebook'])): ?>
                  <a href="<?= htmlspecialchars($row['facebook'], ENT_QUOTES, 'UTF-8'); ?>" class="text-white bg-[#a9cf46] rounded-full p-2 hover:bg-[#759916]" aria-label="" data-aria-i18n="facebook">
                    <svg class="w-5 h-5 md:w-6 md:h-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                      <path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"/>
                    </svg>
                  </a>
                <?php endif; ?>
                <?php if (!empty($row['linkedin'])): ?>
                  <a href="<?= htmlspecialchars($row['linkedin'], ENT_QUOTES, 'UTF-8'); ?>" class="text-white bg-[#a9cf46] rounded-full p-2 hover:bg-[#759916]" aria-label="" data-aria-i18n="linkedin">
                    <svg class="w-5 h-5 md:w-6 md:h-6" fill="currentColor" viewBox="0 0 24 24">
                      <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/>
                    </svg>
                  </a>
                <?php endif; ?>
                <?php if (!empty($row['google_plus'])): ?>
                  <a href="mailto:<?= htmlspecialchars($row['google_plus'], ENT_QUOTES, 'UTF-8'); ?>" class="text-white bg-[#a9cf46] rounded-full p-2 hover:bg-[#759916]" aria-label="" data-aria-i18n="email">
                    <svg class="w-5 h-5 md:w-6 md:h-6" fill="currentColor" viewBox="0 0 24 24">
                      <path d="M22 4H2v16h20V4zm-2 4l-8 5-8-5V6l8 5 8-5v2z"/>
                    </svg>
                  </a>
                <?php endif; ?>
              </div>
            </div>
            <h4 class="text-lg md:text-xl lg:text-2xl font-bold leading-tight mb-2"> <?= htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8'); ?></h4>
            <p class="text-[#a9cf46] font-medium text-sm md:text-base leading-tight"><?= htmlspecialchars($row['position'], ENT_QUOTES, 'UTF-8'); ?></p>
          </div>
        <?php 
            endwhile; 
          endif; 
        ?>
      </div>
    </div>
  </section>

    <?php include __DIR__ . '/footer.php'; ?>


  <script>
    // Language translations
    const translations = {
      fr: {
        page_title: "A propos de nous",
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
        welcome_title: "À Propos AGRIFORLAND : Notre Histoire et Expertise",
        welcome_description: "Votre partenaire d'excellence dans les secteurs de l'agriculture, de la foresterie, de l'environnement, du développement durable, du foncier, de l'immobilier, de l'industrie et de la technologie",
        who_we_are: "QUI SOMMES-NOUS ?",
        company_description: "Depuis sa création en mai 2023, AGRIFORLAND SARL accompagne avec succès les acteurs du développement durable en Côte d'Ivoire. Notre cabinet AGRIFORLAND s'appuie sur une équipe de 15 experts multidisciplinaires dirigée par Abdoul-Kader Ouattara. Les solutions AGRIFORLAND ont déjà transformé plus de 50 projets dans 25 zones d'intervention. L'approche AGRIFORLAND privilégie l'innovation tout en respectant les réalités locales ivoiriennes. Choisir AGRIFORLAND, c'est opter pour un partenaire qui transforme les défis en opportunités durables.",
        company_links: "Découvrez nos 10 pôles d'expertise AGRIFORLAND et consultez nos projets réalisés AGRIFORLAND qui témoignent de notre savoir-faire reconnu.",
        vision_illustration: "Illustration de la vision durable d'AGRIFORLAND",
        our_vision: "NOTRE VISION",
        vision_description: "AGRIFORLAND aspire à devenir un acteur de référence dans la conception et la mise en œuvre de solutions durables et innovantes, en plaçant systématiquement les communautés locales et la préservation de l'environnement au cœur de ses actions. Nous sommes convaincus que la prospérité économique doit aller de pair avec la régénération des écosystèmes et l'équité sociale.",
        vision_quote: '"Votre partenaire pour un développement harmonieux entre économie et écologie"',
        our_mission: "Notre Mission",
        mission_1_title: "Innover pour répondre aux défis agricoles",
        mission_1_desc: "environnementaux et sociaux grâce à des solutions durables et inclusives",
        mission_2_title: "Restaurer les écosystèmes",
        mission_2_desc: "Naturels tout en valorisant les ressources locales",
        mission_3_title: "Transformer positivement",
        mission_3_desc: "La qualité de vie des communautés grâce à des approches participatives",
        our_commitment: "Notre Engagement",
        commitment_description: "AGRIFORLAND aspire à devenir un acteur de référence dans la conception et la mise en œuvre de solutions durables et innovantes, en plaçant systématiquement les communautés locales et la préservation de l'environnement au cœur de ses actions. Nous sommes convaincus que la prospérité économique doit aller de pair avec la régénération des écosystèmes et l'équité sociale.",
        commitment_illustration: "Illustration de l'engagement d'AGRIFORLAND",
        our_values: "Nos Valeurs",
        value_1: "Fournir des prestations techniques de haute qualité",
        value_2: "Respecter les délais et les budgets",
        value_3: "Transférer nos méthodes de savoir-faire à nos clients",
        value_4: "Contribuer positivement au développement local",
        value_5: "Maintenir les normes éthiques et professionnelles",
        value_6: "Promouvoir l'innovation, l'excellence dans tous nos projets",
        our_dynamic_teams: "NOS ÉQUIPES DYNAMIQUES",
        general_director: "Directeur Général",
        team_member: "Membre d'équipe",
        facebook: "Facebook",
        instagram: "Instagram",
        twitter: "Twitter",
        linkedin: "LinkedIn",
        email: "Email",
        ceo_title: "Directeur Général",
        tech_coordinator_title: "Coordonnateur Technique Des Etudes Et De la Recherche",
        program_coordinator_title: "Coordonnateur De Programme",
        project_manager_title: "Chargé De Projets",
        training_gender_manager_title: "Chargé de Mission Formation, Genre et Développement Social",
        it_manager_title: "Responsable IT",
        sustainability_assistant_title: "Assistant Technique en Durabilité et Formation",
        rdue_assistant_title: "Assistant Technique en Durabilité et RDUE",
        follow_us: "SUIVEZ-NOUS",
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
        newsletter_success: "Merci pour votre inscription !",
        error_newsletter: "Erreur lors de l'inscription.",
        copyright: "© 2025 Agriforland. Tous droits réservés.",
        contact: "Contact"
      },
      en: {
        page_title: "About us",
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
        welcome_title: "About AGRIFORLAND: Our History and Expertise",
        welcome_description: "Your partner of excellence in the sectors of agriculture, forestry, environment, sustainable development, land, real estate, industry and technology",
        who_we_are: "WHO ARE WE?",
        company_description: "Since its creation in May 2023, AGRIFORLAND SARL has successfully supported sustainable development actors in Côte d'Ivoire. Our AGRIFORLAND firm relies on a team of 15 multidisciplinary experts led by Abdoul-Kader Ouattara. AGRIFORLAND solutions have already transformed over 50 projects in 25 intervention zones. The AGRIFORLAND approach favors innovation while respecting Ivorian local realities. Choosing AGRIFORLAND means opting for a partner that transforms challenges into sustainable opportunities.",
        company_links: "Discover our 10 AGRIFORLAND expertise areas and consult our completed AGRIFORLAND projects that demonstrate our recognized know-how.",
        vision_illustration: "Illustration of AGRIFORLAND's sustainable vision",
        our_vision: "OUR VISION",
        vision_description: "AGRIFORLAND aspires to become a reference player in the design and implementation of sustainable and innovative solutions, by systematically placing local communities and environmental preservation at the heart of its actions. We are convinced that economic prosperity must go hand in hand with ecosystem regeneration and social equity.",
        vision_quote: '"Your partner for harmonious development between economy and ecology"',
        our_mission: "Our Mission",
        mission_1_title: "Innovate to meet agricultural challenges",
        mission_1_desc: "environmental and social through sustainable and inclusive solutions",
        mission_2_title: "Restore ecosystems",
        mission_2_desc: "Natural while enhancing local resources",
        mission_3_title: "Transform positively",
        mission_3_desc: "The quality of life of communities through participatory approaches",
        our_commitment: "Our Commitment",
        commitment_description: "AGRIFORLAND aspires to become a reference player in the design and implementation of sustainable and innovative solutions, by systematically placing local communities and environmental preservation at the heart of its actions. We are convinced that economic prosperity must go hand in hand with ecosystem regeneration and social equity.",
        commitment_illustration: "Illustration of AGRIFORLAND's commitment",
        our_values: "Our Values",
        value_1: "Provide high-quality technical services",
        value_2: "Respect deadlines and budgets",
        value_3: "Transfer our know-how methods to our clients",
        value_4: "Contribute positively to local development",
        value_5: "Maintain ethical and professional standards",
        value_6: "Promote innovation, excellence in all our projects",
        our_dynamic_teams: "OUR DYNAMIC TEAMS",
        general_director: "General Director",
        team_member: "Team member",
        facebook: "Facebook",
        instagram: "Instagram",
        twitter: "Twitter",
        linkedin: "LinkedIn",
        email: "Email",
        ceo_title: "General Director",
        tech_coordinator_title: "Technical Coordinator of Studies and Research",
        program_coordinator_title: "Program Coordinator",
        project_manager_title: "Project Manager",
        training_gender_manager_title: "Mission Manager Training, Gender and Social Development",
        it_manager_title: "IT Manager",
        sustainability_assistant_title: "Technical Assistant in Sustainability and Training",
        rdue_assistant_title: "Technical Assistant in Sustainability and RDUE",
        follow_us: "FOLLOW US",
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
      document.title = translations[lang].page_title + " - Agriforland";

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

    // JS pour activer le menu mobile + lien actif
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
  </script>
  
</body>
</html>