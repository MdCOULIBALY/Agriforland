<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
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
</body>
</html>