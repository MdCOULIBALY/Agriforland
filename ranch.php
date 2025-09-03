<?php
  // Charger les données du ranch depuis le fichier JSON
  $json_file = 'data/ranch.json';
  $ranch_data = file_exists($json_file) ? json_decode(file_get_contents($json_file), true) : [];
  $items = isset($ranch_data['ranch_data']) ? $ranch_data['ranch_data'] : [];

  // Pagination
  $items_per_page = 100;
  $current_page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
  $total_items = count($items);
  $total_pages = ceil($total_items / $items_per_page);
  $offset = ($current_page - 1) * $items_per_page;
  $current_items = array_slice($items, $offset, $items_per_page);
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
  <title data-i18n="title">AGRIFORLAND SARL - Notre Ranch</title>
  <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@400;700&family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/@phosphor-icons/web"></script>
  <link rel="stylesheet" href="https://unpkg.com/@phosphor-icons/web@2.0.3/src/bold/style.css">
  <link rel="icon" href="images/favicon.ico" type="image/x-icon">
  <link href="css/Style.css" rel="stylesheet">
  
  <!-- Google tag (gtag.js) -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=G-ZKKVQJJCYG"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', 'G-ZKKVQJJCYG');
  </script>

  <style>
    /* Animations personnalisées */
    @keyframes fadeInUp {
      from { opacity: 0; transform: translateY(30px); }
      to { opacity: 1; transform: translateY(0); }
    }
    
    @keyframes shimmer {
      0% { background-position: -200px 0; }
      100% { background-position: calc(200px + 100%) 0; }
    }
    
    .animate-fadeInUp { animation: fadeInUp 0.6s ease-out; }
    
    .skeleton {
      background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
      background-size: 200px 100%;
      animation: shimmer 1.5s infinite;
    }
    
    .card-3d {
      transform-style: preserve-3d;
      transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
      perspective: 1000px;
    }
    
    .card-3d:hover {
      transform: translateY(-12px) rotateX(8deg) rotateY(8deg) scale(1.02);
      box-shadow: 0 25px 60px rgba(0,0,0,0.2);
    }
    
    .card-glow {
      position: relative;
      overflow: hidden;
    }
    
    .card-glow::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
      transition: left 0.6s;
      z-index: 1;
    }
    
    .card-glow:hover::before {
      left: 100%;
    }
    
    .description-overlay {
      position: absolute;
      bottom: 0;
      left: 0;
      right: 0;
      background: linear-gradient(to top, rgba(0,0,0,0.9), rgba(0,0,0,0.7), transparent);
      color: white;
      padding: 20px;
      transform: translateY(100%);
      transition: transform 0.4s ease-in-out;
      z-index: 2;
    }
    
    .card-3d:hover .description-overlay {
      transform: translateY(0);
    }
    
    .modal-backdrop {
      backdrop-filter: blur(15px);
      background: rgba(0,0,0,0.8);
    }
    
    .search-focus {
      box-shadow: 0 0 0 3px rgba(169, 207, 70, 0.3);
    }
    
    .pagination-btn {
      @apply px-4 py-2 mx-1 border-2 border-[#a9cf46] rounded-lg hover:bg-[#a9cf46] hover:text-white transition-all duration-300 font-medium text-[#759916];
    }
    
    .pagination-btn.active {
      @apply bg-[#a9cf46] text-white border-[#a9cf46] shadow-lg transform scale-105;
    }
    
    .pagination-btn:disabled {
      @apply opacity-50 cursor-not-allowed;
    }
    
    /* Contrôles d'image avancés */
    .image-viewer {
      position: relative;
      overflow: hidden;
      width: 100%;
      height: 500px;
      border-radius: 12px;
      background: #f8f9fa;
    }
    
    .image-viewer img {
      transition: all 0.3s ease;
      cursor: grab;
      max-width: 100%;
      max-height: 100%;
      object-fit: contain;
    }
    
    .image-viewer img:active {
      cursor: grabbing;
    }
    
    .image-controls {
      position: absolute;
      top: 10px;
      right: 10px;
      display: flex;
      gap: 8px;
      background: rgba(0,0,0,0.7);
      padding: 8px;
      border-radius: 8px;
    }
    
    .image-controls button {
      background: rgba(255,255,255,0.2);
      border: none;
      color: white;
      padding: 8px;
      border-radius: 4px;
      cursor: pointer;
      transition: background 0.2s;
    }
    
    .image-controls button:hover {
      background: rgba(255,255,255,0.3);
    }
    
    .image-nav {
      position: absolute;
      top: 50%;
      transform: translateY(-50%);
      background: rgba(0,0,0,0.7);
      color: white;
      border: none;
      padding: 15px 12px;
      cursor: pointer;
      border-radius: 50%;
      transition: all 0.2s;
      z-index: 10;
    }
    
    .image-nav:hover {
      background: rgba(0,0,0,0.9);
      transform: translateY(-50%) scale(1.1);
    }
    
    .image-nav.prev {
      left: 15px;
    }
    
    .image-nav.next {
      right: 15px;
    }
    
    .fullscreen-viewer {
      position: fixed;
      top: 0;
      left: 0;
      width: 100vw;
      height: 100vh;
      background: rgba(0,0,0,0.95);
      z-index: 1000;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    
    .fullscreen-viewer img {
      max-width: 90vw;
      max-height: 90vh;
      object-fit: contain;
    }
    
    .fullscreen-controls {
      position: absolute;
      bottom: 20px;
      left: 50%;
      transform: translateX(-50%);
      display: flex;
      gap: 10px;
      background: rgba(0,0,0,0.8);
      padding: 12px;
      border-radius: 10px;
    }
    
    .fullscreen-controls button {
      background: rgba(255,255,255,0.2);
      border: none;
      color: white;
      padding: 10px;
      border-radius: 6px;
      cursor: pointer;
      transition: background 0.2s;
    }
    
    .fullscreen-controls button:hover {
      background: rgba(255,255,255,0.3);
    }
  </style>
</head>

<body class="bg-[#f6ffde] text-black">
  <!-- Preloader -->
  <div id="preloader" class="fixed inset-0 bg-[#f6ffde] z-50 flex items-center justify-center">
    <div class="animate-triangle w-32 h-32">
      <img src="images/triangle-svgrepo-com.svg" loading="lazy" alt="" data-alt-i18n="loading" class="w-full h-full object-contain triangle-img">
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
        alt="" 
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
            <img id="language-icon" loading="lazy" src="images/fr.webp" alt="" data-alt-i18n="language" class="h-5 w-5">
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
        <a href="ranch.php" class="nav-link text-[#a9cf46] border-b-2 border-[#a9cf46] font-semibold" data-i18n="ranch">Notre Ranch</a>
      </nav>
    </div>

    <!-- Menu Mobile -->
    <div id="mobile-menu" class="md:hidden hidden bg-[#f6ffde] px-4 pb-4">
      <nav class="flex flex-col gap-3 text-base">
        <a href="index.php" class="nav-link hover:text-[#a9cf46] transition" data-i18n="home">Accueil</a>
        <a href="about.php" class="nav-link hover:text-[#a9cf46] transition" data-i18n="about">À Propos</a>
        <a href="poles.html" class="nav-link hover:text-[#a9cf46] transition" data-i18n="poles">Nos Pôles</a>
        <a href="projets.html" class="nav-link hover:text-[#a9cf46] transition" data-i18n="projects">Nos Projets</a>
        <a href="ranch.php" class="nav-link text-[#a9cf46] border-b-2 border-[#a9cf46] font-semibold" data-i18n="ranch">Notre Ranch</a>
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
        <a href="contact.html" class="border border-gray-500 px-4 py-2 rounded-md text-center text-sm hover:bg-white transition" data-i18n="contact_us">Nous Contacter</a>
      </div>
    </div>
  </header>

  <!-- Bannière -->
  <section class="relative">
    <img 
      src="cache/banniereranch.webp" 
      srcset="
        cache/banniereranch.webp 480w, 
        cache/banniereranch.webp 800w, 
        cache/banniereranch.webp 1200w
      "
      sizes="(max-width: 600px) 480px, (max-width: 1000px) 800px, 1200px"
      loading="lazy" 
      alt="" 
      data-alt-i18n="hero_background"
      class="w-full h-[300px] md:h-[400px] object-cover" 
    />
    <div class="absolute top-0 left-0 w-full h-full bg-black/50 flex flex-col justify-center items-center text-center text-white px-4">
      <h1 class="text-3xl sm:text-4xl md:text-5xl font-bold pb-4 sm:pb-6" data-i18n="ranch_title">Notre Ranch AGRIFORLAND 🐄</h1>
      <p class="text-center px-4 sm:px-8 md:px-16" data-i18n="ranch_subtitle">Découvrez nos Animaux d'ornements, animaux de consommations, animaux de parcs, matériels d'élevages, formations</p>
    </div>
  </section>

  <!-- Section principale -->
  <section class="py-8 sm:py-12 px-4 max-w-7xl mx-auto bg-[#f6ffde]">
    <!-- Statistiques rapides simplifiées -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
      <div class="bg-white rounded-xl p-4 text-center shadow-md">
        <div class="text-2xl md:text-3xl font-bold text-[#759916]" id="total-count"><?= count(array_filter($items, fn($item) => $item['type'] === 'animal')) ?></div>
        <div class="text-sm text-gray-600" data-i18n="total_animals">Animaux</div>
      </div>
      <div class="bg-white rounded-xl p-4 text-center shadow-md">
        <div class="text-2xl md:text-3xl font-bold text-[#759916]" id="farms-count"><?= count(array_filter($items, fn($item) => $item['type'] === 'farm')) ?></div>
        <div class="text-sm text-gray-600" data-i18n="total_farms">Fermes</div>
      </div>
      <div class="bg-white rounded-xl p-4 text-center shadow-md">
        <div class="text-2xl md:text-3xl font-bold text-[#759916]" id="categories-count"><?= count(array_unique(array_column($items, 'category'))) ?></div>
        <div class="text-sm text-gray-600" data-i18n="categories">Catégories</div>
      </div>
      <div class="bg-white rounded-xl p-4 text-center shadow-md">
        <div class="text-2xl md:text-3xl font-bold text-[#a9cf46]"><?= $items_per_page ?></div>
        <div class="text-sm text-gray-600" data-i18n="items_per_page">Éléments/Page</div>
      </div>
    </div>

    <!-- Contrôles et filtres simplifiés -->
    <div class="mb-8 space-y-6">
      <!-- Barre de recherche -->
      <div class="relative max-w-2xl mx-auto">
        <input 
          type="text" 
          id="search-input" 
          placeholder="Rechercher un animal, une ferme..." 
          data-i18n-placeholder="search_placeholder"
          class="w-full px-6 py-4 pl-12 pr-20 text-lg border-2 border-gray-200 rounded-full focus:border-[#a9cf46] focus:search-focus outline-none transition-all duration-300"
        >
        <i class="ph ph-magnifying-glass absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400 text-xl"></i>
        <button id="search-clear" class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-red-500 transition-colors hidden">
          <i class="ph ph-x text-xl"></i>
        </button>
      </div>

      <!-- Filtres avancés -->
      <div class="bg-white rounded-xl p-6 shadow-md">
        <div class="flex flex-wrap items-center gap-4 mb-4">
          <h3 class="font-bold text-lg" data-i18n="filters">Filtres :</h3>
          <button id="reset-filters" class="px-4 py-2 text-sm text-red-600 hover:bg-red-50 rounded-lg transition-colors" data-i18n="reset_filters">Réinitialiser</button>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          <!-- Filtre par type -->
          <div>
            <label class="block text-sm font-medium mb-2" data-i18n="type_filter">Type :</label>
            <select id="type-filter" class="w-full p-2 border border-gray-300 rounded-lg focus:border-[#a9cf46] outline-none">
              <option value="" data-i18n="all_types">Tous</option>
              <option value="animal" data-i18n="animals_only">Animaux</option>
              <option value="farm" data-i18n="farms_only">Fermes</option>
            </select>
          </div>
          
          <!-- Filtre par catégorie -->
          <div>
            <label class="block text-sm font-medium mb-2" data-i18n="category_filter">Catégorie :</label>
            <select id="category-filter" class="w-full p-2 border border-gray-300 rounded-lg focus:border-[#a9cf46] outline-none">
              <option value="" data-i18n="all_categories">Toutes les catégories</option>
            </select>
          </div>
          
          <!-- Filtre par localisation -->
          <div>
            <label class="block text-sm font-medium mb-2" data-i18n="location_filter">Localisation :</label>
            <select id="location-filter" class="w-full p-2 border border-gray-300 rounded-lg focus:border-[#a9cf46] outline-none">
              <option value="" data-i18n="all_locations">Toutes les zones</option>
            </select>
          </div>
          
          <!-- Tri -->
          <div>
            <label class="block text-sm font-medium mb-2" data-i18n="sort_by">Trier par :</label>
            <select id="sort-select" class="w-full p-2 border border-gray-300 rounded-lg focus:border-[#a9cf46] outline-none">
              <option value="name" data-i18n="sort_name">Nom (A-Z)</option>
              <option value="name_desc" data-i18n="sort_name_desc">Nom (Z-A)</option>
              <option value="date" data-i18n="sort_date">Plus récents</option>
              <option value="type" data-i18n="sort_type">Type</option>
            </select>
          </div>
        </div>
      </div>
    </div>

    <!-- Résultats et pagination -->
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4 bg-white rounded-xl p-4 shadow-md">
      <div class="flex items-center gap-4">
        <span id="results-info" class="text-gray-600 font-medium">
          <span data-i18n="showing">Affichage</span> <?= $offset + 1 ?>-<?= min($offset + $items_per_page, $total_items) ?> <span data-i18n="of">sur</span> <span id="total-results"><?= $total_items ?></span> <span data-i18n="elements">éléments</span>
        </span>
        <span class="text-sm text-gray-500">
          (<?= $items_per_page ?> <span data-i18n="per_page">par page</span>)
        </span>
      </div>
      
      <!-- Pagination -->
      <div class="flex items-center gap-2">
        <?php if ($total_pages > 1): ?>
          <a href="?page=<?= max(1, $current_page - 1) ?>" class="pagination-btn <?= $current_page <= 1 ? 'opacity-50 pointer-events-none' : '' ?>">
            <i class="ph ph-arrow-left"></i>
          </a>
          
          <?php
          $start_page = max(1, $current_page - 2);
          $end_page = min($total_pages, $current_page + 2);
          
          if ($start_page > 1): ?>
            <a href="?page=1" class="pagination-btn">1</a>
            <?php if ($start_page > 2): ?>
              <span class="px-2">...</span>
            <?php endif;
          endif;
          
          for ($i = $start_page; $i <= $end_page; $i++): ?>
            <a href="?page=<?= $i ?>" class="pagination-btn <?= $i === $current_page ? 'active' : '' ?>"><?= $i ?></a>
          <?php endfor;
          
          if ($end_page < $total_pages): ?>
            <?php if ($end_page < $total_pages - 1): ?>
              <span class="px-2">...</span>
            <?php endif; ?>
            <a href="?page=<?= $total_pages ?>" class="pagination-btn"><?= $total_pages ?></a>
          <?php endif; ?>
          
          <a href="?page=<?= min($total_pages, $current_page + 1) ?>" class="pagination-btn <?= $current_page >= $total_pages ? 'opacity-50 pointer-events-none' : '' ?>">
            <i class="ph ph-arrow-right"></i>
          </a>
        <?php else: ?>
          <span class="text-gray-500 text-sm" data-i18n="single_page">Page unique</span>
        <?php endif; ?>
      </div>
    </div>

    <!-- Grille des éléments (2 colonnes sur TOUS écrans) -->
    <div id="ranch-grid" class="grid grid-cols-2 gap-4 md:gap-8">
      <?php if (!empty($current_items)): ?>
        <?php foreach ($current_items as $index => $item): ?>
          <div class="ranch-item card-3d card-glow bg-white rounded-xl overflow-hidden shadow-lg hover:shadow-xl transition-all duration-500 animate-fadeInUp cursor-pointer" 
               style="animation-delay: <?= $index * 0.1 ?>s"
               data-type="<?= htmlspecialchars($item['type']) ?>"
               data-category="<?= htmlspecialchars($item['category']) ?>"
               data-location="<?= htmlspecialchars($item['location']) ?>"
               data-name="<?= htmlspecialchars($item['name']) ?>"
               onclick="openModal(<?= htmlspecialchars(json_encode($item)) ?>)">
            
            <div class="relative overflow-hidden aspect-square">
              <img src="<?= htmlspecialchars($item['images'][0] ?? 'images/fallback.webp') ?>" 
                   loading="lazy" 
                   class="w-full h-full object-cover transition-transform duration-700 hover:scale-110" 
                   alt="<?= htmlspecialchars($item['name']) ?>">
              
              <!-- Badge type -->
              <div class="absolute top-4 left-4">
                <span class="px-3 py-1 rounded-full text-xs font-medium <?= $item['type'] === 'animal' ? 'bg-blue-500 text-white' : 'bg-green-500 text-white' ?>">
                  <?= $item['type'] === 'animal' ? '🐄 Animal' : '🌾 Ferme' ?>
                </span>
              </div>
              
              <!-- Titre sur l'image -->
              <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/80 to-transparent p-4">
                <h3 class="font-bold text-xl text-white mb-1"><?= htmlspecialchars($item['name']) ?></h3>
                <p class="text-white/80 text-sm"><?= htmlspecialchars($item['category']) ?></p>
              </div>
              
              <!-- Description au survol -->
              <div class="description-overlay">
                <h3 class="font-bold text-lg mb-2"><?= htmlspecialchars($item['name']) ?></h3>
                <p class="text-sm mb-3 leading-relaxed"><?= htmlspecialchars(substr($item['description'], 0, 150)) ?>...</p>
                <div class="flex items-center justify-between text-white/80 text-sm">
                  <div class="flex items-center gap-1">
                    <i class="ph ph-map-pin"></i>
                    <span><?= htmlspecialchars(explode(' - ', $item['location'])[0]) ?></span>
                  </div>
                  <span class="text-white font-medium" data-i18n="click_details">Cliquez pour plus</span>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="col-span-2 text-center py-16">
          <div class="text-6xl mb-4">📭</div>
          <h3 class="text-xl font-bold mb-2" data-i18n="no_results">Aucun élément trouvé</h3>
          <p class="text-gray-600" data-i18n="try_different_search">Essayez une recherche différente</p>
        </div>
      <?php endif; ?>
    </div>

    <!-- Message si aucun résultat (pour le filtrage JS) -->
    <div id="no-results" class="text-center py-16 hidden">
      <div class="text-6xl mb-4">🔍</div>
      <h3 class="text-xl font-bold mb-2" data-i18n="no_results">Aucun élément trouvé</h3>
      <p class="text-gray-600" data-i18n="try_different_filters">Essayez des filtres différents</p>
    </div>

    <!-- Pagination en bas -->
    <?php if ($total_pages > 1): ?>
      <div class="flex flex-col md:flex-row justify-between items-center mt-8 gap-4 bg-white rounded-xl p-4 shadow-md">
        <div class="flex items-center gap-4">
          <span class="text-gray-600 font-medium">
            <span data-i18n="page">Page</span> <?= $current_page ?> <span data-i18n="of">sur</span> <?= $total_pages ?>
          </span>
          <span class="text-sm text-gray-500">
            (<?= $total_items ?> <span data-i18n="total_elements">éléments au total</span>)
          </span>
        </div>
        
        <!-- Pagination -->
        <div class="flex items-center gap-2">
          <a href="?page=<?= max(1, $current_page - 1) ?>" class="pagination-btn <?= $current_page <= 1 ? 'opacity-50 pointer-events-none' : '' ?>" title="Page précédente">
            <i class="ph ph-arrow-left"></i>
          </a>
          
          <?php
          $start_page = max(1, $current_page - 2);
          $end_page = min($total_pages, $current_page + 2);
          
          if ($start_page > 1): ?>
            <a href="?page=1" class="pagination-btn">1</a>
            <?php if ($start_page > 2): ?>
              <span class="px-2 text-gray-400">...</span>
            <?php endif;
          endif;
          
          for ($i = $start_page; $i <= $end_page; $i++): ?>
            <a href="?page=<?= $i ?>" class="pagination-btn <?= $i === $current_page ? 'active' : '' ?>"><?= $i ?></a>
          <?php endfor;
          
          if ($end_page < $total_pages): ?>
            <?php if ($end_page < $total_pages - 1): ?>
              <span class="px-2 text-gray-400">...</span>
            <?php endif; ?>
            <a href="?page=<?= $total_pages ?>" class="pagination-btn"><?= $total_pages ?></a>
          <?php endif; ?>
          
          <a href="?page=<?= min($total_pages, $current_page + 1) ?>" class="pagination-btn <?= $current_page >= $total_pages ? 'opacity-50 pointer-events-none' : '' ?>" title="Page suivante">
            <i class="ph ph-arrow-right"></i>
          </a>
        </div>
      </div>
    <?php endif; ?>
  </section>

  <!-- Modal de visualisation -->
  <div id="ranch-modal" class="fixed inset-0 z-50 hidden modal-backdrop">
    <div class="flex items-center justify-center min-h-screen p-4">
      <div class="bg-white rounded-2xl shadow-2xl max-w-6xl w-full max-h-[90vh] overflow-y-auto">
        <!-- Header du modal -->
        <div class="flex justify-between items-center p-6 border-b border-gray-200">
          <div>
            <h2 id="modal-title" class="text-2xl font-bold"></h2>
            <div class="flex items-center gap-2 mt-1">
              <span id="modal-type-badge" class="px-3 py-1 rounded-full text-xs font-medium"></span>
              <span id="modal-category" class="text-gray-600"></span>
            </div>
          </div>
          <div class="flex items-center gap-4">
            <button id="modal-share" class="p-2 rounded-full hover:bg-gray-100 transition-colors" title="Partager">
              <i class="ph ph-share-network text-2xl"></i>
            </button>
            <button id="modal-close" class="p-2 rounded-full hover:bg-gray-100 transition-colors" title="Fermer">
              <i class="ph ph-x text-2xl"></i>
            </button>
          </div>
        </div>
        
        <!-- Contenu du modal -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 p-6">
          <!-- Visionneuse d'images avancée -->
          <div class="space-y-4">
            <div class="image-viewer" id="image-viewer">
              <img id="modal-main-image" src="" alt="" style="transform-origin: center;">
              
              <!-- Navigation entre images -->
              <button class="image-nav prev" id="prev-image" onclick="changeImage(-1)">
                <i class="ph ph-caret-left text-xl"></i>
              </button>
              <button class="image-nav next" id="next-image" onclick="changeImage(1)">
                <i class="ph ph-caret-right text-xl"></i>
              </button>
              
              <!-- Contrôles d'image -->
              <div class="image-controls">
                <button onclick="zoomIn()" title="Zoom +">
                  <i class="ph ph-plus"></i>
                </button>
                <button onclick="zoomOut()" title="Zoom -">
                  <i class="ph ph-minus"></i>
                </button>
                <button onclick="rotateLeft()" title="Rotation gauche">
                  <i class="ph ph-arrow-counter-clockwise"></i>
                </button>
                <button onclick="rotateRight()" title="Rotation droite">
                  <i class="ph ph-arrow-clockwise"></i>
                </button>
                <button onclick="flipHorizontal()" title="Symétrie horizontale">
                  <i class="ph ph-arrows-horizontal"></i>
                </button>
                <button onclick="flipVertical()" title="Symétrie verticale">
                  <i class="ph ph-arrows-vertical"></i>
                </button>
                <button onclick="resetTransform()" title="Reset">
                  <i class="ph ph-arrow-clockwise"></i>
                </button>
                <button onclick="fullscreen()" title="Plein écran">
                  <i class="ph ph-arrows-out"></i>
                </button>
              </div>
              
              <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 bg-black/50 text-white px-3 py-1 rounded-full text-sm">
                <span id="image-counter">1/1</span>
              </div>
            </div>
            
            <!-- Miniatures -->
            <div id="modal-thumbnails" class="flex gap-2 overflow-x-auto">
              <!-- Miniatures générées dynamiquement -->
            </div>
          </div>
          
          <!-- Informations détaillées -->
          <div class="space-y-6">
            <!-- Stats rapides -->
            <div id="modal-stats" class="grid grid-cols-2 gap-4">
              <!-- Stats générées dynamiquement -->
            </div>
            
            <!-- Description -->
            <div>
              <h3 class="font-bold mb-2" data-i18n="description">Description</h3>
              <p id="modal-description" class="text-gray-700 leading-relaxed"></p>
            </div>
            
            <!-- Caractéristiques -->
            <div>
              <h3 class="font-bold mb-3" data-i18n="characteristics">Caractéristiques</h3>
              <div id="modal-characteristics" class="space-y-2">
                <!-- Caractéristiques générées dynamiquement -->
              </div>
            </div>
            
            <!-- Localisation -->
            <div>
              <h3 class="font-bold mb-2" data-i18n="location">Localisation</h3>
              <div id="modal-location" class="bg-gray-100 rounded-lg p-4 flex items-center gap-3">
                <i class="ph ph-map-pin text-2xl text-[#759916]"></i>
                <span id="modal-location-text">-</span>
              </div>
            </div>
            
            <!-- Produits/Cultures (pour les fermes) -->
            <div id="modal-products" class="hidden">
              <h3 class="font-bold mb-3" data-i18n="products">Produits/Cultures</h3>
              <div id="modal-products-list" class="flex flex-wrap gap-2">
                <!-- Produits générés dynamiquement -->
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Visionneuse plein écran -->
  <div id="fullscreen-viewer" class="fullscreen-viewer hidden">
    <img id="fullscreen-image" src="" alt="">
    <button onclick="closeFullscreenViewer()" style="position: absolute; top: 20px; right: 20px; background: rgba(0,0,0,0.7); color: white; border: none; padding: 10px; border-radius: 50%; cursor: pointer;">
      <i class="ph ph-x text-xl"></i>
    </button>
    
    <div class="fullscreen-controls">
      <button onclick="fsZoomIn()" title="Zoom +">
        <i class="ph ph-plus"></i>
      </button>
      <button onclick="fsZoomOut()" title="Zoom -">
        <i class="ph ph-minus"></i>
      </button>
      <button onclick="fsRotateLeft()" title="Rotation gauche">
        <i class="ph ph-arrow-counter-clockwise"></i>
      </button>
      <button onclick="fsRotateRight()" title="Rotation droite">
        <i class="ph ph-arrow-clockwise"></i>
      </button>
      <button onclick="fsFlipH()" title="Symétrie H">
        <i class="ph ph-arrows-horizontal"></i>
      </button>
      <button onclick="fsFlipV()" title="Symétrie V">
        <i class="ph ph-arrows-vertical"></i>
      </button>
      <button onclick="fsReset()" title="Reset">
        <i class="ph ph-arrow-clockwise"></i>
      </button>
    </div>
  </div>

    <!-- Footer -->
    <?php require __DIR__ . "/includes/footer.php"; ?>

  <script>
    // Données du ranch (chargées depuis PHP)
    const ranchData = <?php echo json_encode($items); ?>;
    let filteredData = [...ranchData];
    let currentModalItem = null;
    let currentImageIndex = 0;
    let imageTransform = {
      zoom: 1,
      rotation: 0,
      flipH: 1,
      flipV: 1,
      translateX: 0,
      translateY: 0
    };

    // Traductions
    const translations = {
      fr: {
        title: "AGRIFORLAND SARL - Notre Ranch",
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
        ranch: "Notre Ranch",
        blog: "Blog",
        portfolios: "Portfolio",
        hero_background: "Arrière-plan héro",
        ranch_title: "AGRIFORLAND RANCH 🐄",
        ranch_subtitle: "Découvrez nos Animaux d'ornements, animaux de consommations, animaux de parcs, matériels d'élevages, formations",
        total_animals: "Animaux",
        total_farms: "Fermes",
        categories: "Catégories",
        items_per_page: "Éléments/Page",
        search_placeholder: "Rechercher un animal, une ferme...",
        filters: "Filtres :",
        reset_filters: "Réinitialiser",
        type_filter: "Type :",
        all_types: "Tous",
        animals_only: "Animaux",
        farms_only: "Fermes",
        category_filter: "Catégorie :",
        all_categories: "Toutes les catégories",
        location_filter: "Localisation :",
        all_locations: "Toutes les zones",
        sort_by: "Trier par :",
        sort_name: "Nom (A-Z)",
        sort_name_desc: "Nom (Z-A)",
        sort_date: "Plus récents",
        sort_type: "Type",
        showing: "Affichage",
        of: "sur",
        elements: "éléments",
        per_page: "par page",
        single_page: "Page unique",
        page: "Page",
        total_elements: "éléments au total",
        click_details: "Cliquez pour plus",
        no_results: "Aucun élément trouvé",
        try_different_search: "Essayez une recherche différente",
        try_different_filters: "Essayez des filtres différents",
        description: "Description",
        characteristics: "Caractéristiques",
        location: "Localisation",
        products: "Produits/Cultures",
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
        newsletter_success: "Merci pour votre inscription !",
        copyright: "© 2025 Agriforland. Tous droits réservés.",
        contact: "Contact"
      },
      en: {
        title: "AGRIFORLAND SARL - Our Ranch",
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
        ranch: "Our Ranch",
        blog: "Blog",
        portfolios: "Portfolio",
        hero_background: "Hero background",
        ranch_title: "AGRIFORLAND RANCH 🐄",
        ranch_subtitle: "Discover our ornamental animals, food animals, park animals, breeding equipment, training",
        total_animals: "Animals",
        total_farms: "Farms",
        categories: "Categories",
        items_per_page: "Items/Page",
        search_placeholder: "Search for an animal, a farm...",
        filters: "Filters:",
        reset_filters: "Reset",
        type_filter: "Type:",
        all_types: "All",
        animals_only: "Animals",
        farms_only: "Farms",
        category_filter: "Category:",
        all_categories: "All categories",
        location_filter: "Location:",
        all_locations: "All areas",
        sort_by: "Sort by:",
        sort_name: "Name (A-Z)",
        sort_name_desc: "Name (Z-A)",
        sort_date: "Most recent",
        sort_type: "Type",
        showing: "Showing",
        of: "of",
        elements: "elements",
        per_page: "per page",
        single_page: "Single page",
        page: "Page",
        total_elements: "total elements",
        click_details: "Click for more",
        no_results: "No elements found",
        try_different_search: "Try a different search",
        try_different_filters: "Try different filters",
        description: "Description",
        characteristics: "Characteristics",
        location: "Location",
        products: "Products/Crops",
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
        newsletter_success: "Thank you for subscribing!",
        copyright: "© 2025 Agriforland. All rights reserved.",
        contact: "Contact"
      }
    };

    // Initialisation
    document.addEventListener('DOMContentLoaded', function() {
      initializeLanguage();
      populateFilters();
      setupEventListeners();
      
      // Masquer le preloader
      setTimeout(() => {
        document.getElementById('preloader').style.display = 'none';
      }, 1000);
    });

    // Fonction pour initialiser la langue
    function initializeLanguage() {
      const savedLang = localStorage.getItem('language') || 'fr';
      updateContent(savedLang);
    }

    // Fonction pour mettre à jour le contenu selon la langue
    function updateContent(lang) {
      document.querySelectorAll('[data-i18n]').forEach(element => {
        const key = element.getAttribute('data-i18n');
        if (translations[lang][key]) {
          element.textContent = translations[lang][key];
        }
      });

      document.querySelectorAll('[data-i18n-placeholder]').forEach(element => {
        const key = element.getAttribute('data-i18n-placeholder');
        if (translations[lang][key]) {
          element.placeholder = translations[lang][key];
        }
      });

      document.querySelectorAll('[data-alt-i18n]').forEach(element => {
        const key = element.getAttribute('data-alt-i18n');
        if (translations[lang][key]) {
          element.alt = translations[lang][key];
        }
      });

      document.querySelectorAll('[data-aria-i18n]').forEach(element => {
        const key = element.getAttribute('data-aria-i18n');
        if (translations[lang][key]) {
          element.setAttribute('aria-label', translations[lang][key]);
        }
      });

      document.documentElement.lang = lang;
      document.getElementById('language-selector').value = lang;
      document.getElementById('language-selector-mobile').value = lang;
      document.title = translations[lang].title;
      
      // Mettre à jour les icônes de langue
      document.getElementById('language-icon').src = `images/${lang}.webp`;
      document.getElementById('language-icon-mobile').src = `images/${lang}.webp`;
    }

    // Fonction pour peupler les filtres
    function populateFilters() {
      const categories = [...new Set(ranchData.map(item => item.category))];
      const locations = [...new Set(ranchData.map(item => item.location.split(' - ')[0]))];
      
      // Peupler les select
      const categorySelect = document.getElementById('category-filter');
      categories.forEach(cat => {
        const option = document.createElement('option');
        option.value = cat;
        option.textContent = cat;
        categorySelect.appendChild(option);
      });

      const locationSelect = document.getElementById('location-filter');
      locations.forEach(loc => {
        const option = document.createElement('option');
        option.value = loc;
        option.textContent = loc;
        locationSelect.appendChild(option);
      });
    }

    // Fonction pour configurer les event listeners
    function setupEventListeners() {
      // Language selectors
      document.getElementById('language-selector').addEventListener('change', (e) => {
        updateContent(e.target.value);
        localStorage.setItem('language', e.target.value);
      });

      document.getElementById('language-selector-mobile').addEventListener('change', (e) => {
        updateContent(e.target.value);
        localStorage.setItem('language', e.target.value);
      });

      // Recherche
      const searchInput = document.getElementById('search-input');
      searchInput.addEventListener('input', handleSearch);

      // Clear search
      document.getElementById('search-clear').addEventListener('click', clearSearch);

      // Filtres
      document.getElementById('type-filter').addEventListener('change', applyFilters);
      document.getElementById('category-filter').addEventListener('change', applyFilters);
      document.getElementById('location-filter').addEventListener('change', applyFilters);
      document.getElementById('sort-select').addEventListener('change', applyFilters);

      // Reset filters
      document.getElementById('reset-filters').addEventListener('click', resetFilters);

      // Menu mobile
      document.getElementById('menu-toggle').addEventListener('click', () => {
        document.getElementById('mobile-menu').classList.toggle('hidden');
      });

      // Modal
      document.getElementById('modal-close').addEventListener('click', closeModal);
      document.getElementById('ranch-modal').addEventListener('click', (e) => {
        if (e.target.id === 'ranch-modal') {
          closeModal();
        }
      });

      // Fullscreen viewer
      document.getElementById('fullscreen-viewer').addEventListener('click', (e) => {
        if (e.target.id === 'fullscreen-viewer') {
          closeFullscreenViewer();
        }
      });

      // Double-clic pour fermer le fullscreen
      document.getElementById('fullscreen-image').addEventListener('dblclick', closeFullscreenViewer);

      // Newsletter
      document.getElementById('newsletter-form').addEventListener('submit', handleNewsletter);

      // Glisser pour les images
      setupImageDragging();
    }

    // Variables globales
    let searchTimeout;

    // Fonction pour gérer la recherche améliorée avec debounce
    function handleSearch(e) {
      const query = e.target.value.toLowerCase().trim();
      const clearBtn = document.getElementById('search-clear');
      
      if (query) {
        clearBtn.classList.remove('hidden');
      } else {
        clearBtn.classList.add('hidden');
      }

      // Debounce pour améliorer les performances
      clearTimeout(searchTimeout);
      searchTimeout = setTimeout(() => {
        applyFilters(); // Utiliser la nouvelle fonction de filtrage
      }, 300);
    }

    // Fonction pour appliquer les filtres courants sans recherche
    function applyCurrentFilters() {
      const type = document.getElementById('type-filter').value;
      const category = document.getElementById('category-filter').value;
      const location = document.getElementById('location-filter').value;
      const sort = document.getElementById('sort-select').value;

      // Appliquer les filtres sur les données déjà filtrées par la recherche
      if (type || category || location) {
        filteredData = filteredData.filter(item => {
          const matchesType = !type || item.type === type;
          const matchesCategory = !category || item.category === category;
          const matchesLocation = !location || item.location.includes(location);

          return matchesType && matchesCategory && matchesLocation;
        });
      }

      // Tri
      filteredData.sort((a, b) => {
        switch (sort) {
          case 'name':
            return a.name.localeCompare(b.name);
          case 'name_desc':
            return b.name.localeCompare(a.name);
          case 'date':
            return new Date(b.created_date || '2020-01-01') - new Date(a.created_date || '2020-01-01');
          case 'type':
            return a.type.localeCompare(b.type);
          default:
            return 0;
        }
      });
    }

    // Fonction pour effacer la recherche
    function clearSearch() {
      document.getElementById('search-input').value = '';
      document.getElementById('search-clear').classList.add('hidden');
      clearTimeout(searchTimeout);
      applyFilters();
    }

    // Fonction pour appliquer les filtres - cacher/montrer les cartes existantes
    function applyFilters() {
      const search = document.getElementById('search-input').value.toLowerCase().trim();
      const type = document.getElementById('type-filter').value;
      const category = document.getElementById('category-filter').value;
      const location = document.getElementById('location-filter').value;
      const sort = document.getElementById('sort-select').value;

      // Récupérer toutes les cartes dans le DOM
      const allCards = document.querySelectorAll('.ranch-item');
      let visibleCount = 0;

      allCards.forEach(card => {
        const cardData = {
          name: card.getAttribute('data-name').toLowerCase(),
          type: card.getAttribute('data-type'),
          category: card.getAttribute('data-category'),
          location: card.getAttribute('data-location')
        };

        // Vérifier tous les critères de filtrage
        const matchesSearch = !search || cardData.name.includes(search);
        const matchesType = !type || cardData.type === type;
        const matchesCategory = !category || cardData.category === category;
        const matchesLocation = !location || cardData.location.includes(location);

        const shouldShow = matchesSearch && matchesType && matchesCategory && matchesLocation;

        if (shouldShow) {
          card.style.display = 'block';
          card.style.opacity = '1';
          visibleCount++;
        } else {
          card.style.display = 'none';
          card.style.opacity = '0';
        }
      });

      // Mettre à jour le compteur
      updateResultsDisplay(visibleCount);

      // Appliquer le tri si nécessaire
      if (sort && sort !== 'name') {
        applySortingToVisibleCards(sort);
      }
    }

    // Fonction pour mettre à jour l'affichage des résultats
    function updateResultsDisplay(visibleCount) {
      const currentLang = localStorage.getItem('language') || 'fr';
      document.getElementById('total-results').textContent = visibleCount;
      
      if (visibleCount === 0) {
        document.getElementById('no-results').classList.remove('hidden');
      } else {
        document.getElementById('no-results').classList.add('hidden');
      }

      // Mettre à jour les infos de pagination
      const resultsInfo = document.getElementById('results-info');
      if (resultsInfo && visibleCount > 0) {
        const showing = Math.min(visibleCount, 100);
        resultsInfo.innerHTML = `
          <span data-i18n="showing">${translations[currentLang].showing}</span> 1-${showing} 
          <span data-i18n="of">${translations[currentLang].of}</span> 
          <span id="total-results">${visibleCount}</span> 
          <span data-i18n="elements">${translations[currentLang].elements}</span>
          ${visibleCount > 100 ? `<span class="text-xs text-gray-500">(${translations[currentLang].per_page})</span>` : ''}
        `;
      }
    }

    // Fonction pour appliquer le tri aux cartes visibles
    function applySortingToVisibleCards(sortType) {
      const container = document.getElementById('ranch-grid');
      const visibleCards = Array.from(container.children).filter(card => 
        card.style.display !== 'none' && card.classList.contains('ranch-item')
      );

      visibleCards.sort((a, b) => {
        const nameA = a.getAttribute('data-name');
        const nameB = b.getAttribute('data-name');

        switch (sortType) {
          case 'name_desc':
            return nameB.localeCompare(nameA);
          case 'type':
            const typeA = a.getAttribute('data-type');
            const typeB = b.getAttribute('data-type');
            return typeA.localeCompare(typeB);
          default:
            return nameA.localeCompare(nameB);
        }
      });

      // Réorganiser les cartes dans le DOM
      visibleCards.forEach(card => {
        container.appendChild(card);
      });
    }

    // Fonction pour rendre les résultats filtrés
    function renderFilteredResults() {
      const container = document.getElementById('ranch-grid');
      const noResults = document.getElementById('no-results');

      if (filteredData.length === 0) {
        container.innerHTML = '';
        noResults.classList.remove('hidden');
        return;
      }

      noResults.classList.add('hidden');

      // ⚠️ IMPORTANT: Respecter la pagination PHP - ne pas tout recréer
      // Seulement mettre à jour le compteur, pas les cartes
      const totalFiltered = filteredData.length;
      const currentLang = localStorage.getItem('language') || 'fr';
      
      document.getElementById('total-results').textContent = totalFiltered;
      
      // Mettre à jour l'info de résultats
      const resultsInfo = document.getElementById('results-info');
      if (resultsInfo) {
        // Garder l'affichage PHP original, juste mettre à jour le total
        const showingText = resultsInfo.innerHTML;
        const updatedText = showingText.replace(/\d+(?=\s+éléments)/, totalFiltered);
        resultsInfo.innerHTML = updatedText;
      }
    }

    // Fonction pour réinitialiser les filtres
    function resetFilters() {
      document.getElementById('search-input').value = '';
      document.getElementById('type-filter').value = '';
      document.getElementById('category-filter').value = '';
      document.getElementById('location-filter').value = '';
      document.getElementById('sort-select').value = 'name';
      document.getElementById('search-clear').classList.add('hidden');
      
      // Montrer toutes les cartes
      const allCards = document.querySelectorAll('.ranch-item');
      allCards.forEach(card => {
        card.style.display = 'block';
        card.style.opacity = '1';
      });
      
      // Remettre le compteur original
      const currentLang = localStorage.getItem('language') || 'fr';
      const totalCards = allCards.length;
      updateResultsDisplay(totalCards);
      
      document.getElementById('no-results').classList.add('hidden');
    }

    // Fonction pour ouvrir le modal
    function openModal(item) {
      currentModalItem = item;
      currentImageIndex = 0;
      resetTransform();
      
      const modal = document.getElementById('ranch-modal');
      
      // Mettre à jour le contenu
      document.getElementById('modal-title').textContent = item.name;
      document.getElementById('modal-category').textContent = item.category;
      document.getElementById('modal-description').textContent = item.description;
      document.getElementById('modal-location-text').textContent = item.location;

      // Badge de type
      const typeBadge = document.getElementById('modal-type-badge');
      if (item.type === 'animal') {
        typeBadge.className = 'px-3 py-1 rounded-full text-xs font-medium bg-blue-500 text-white';
        typeBadge.textContent = '🐄 Animal';
      } else {
        typeBadge.className = 'px-3 py-1 rounded-full text-xs font-medium bg-green-500 text-white';
        typeBadge.textContent = '🌾 Ferme';
      }

      // Stats
      const statsContainer = document.getElementById('modal-stats');
      if (item.type === 'animal') {
        statsContainer.innerHTML = `
          <div class="bg-[#f6ffde] rounded-lg p-4 text-center">
            <div class="text-2xl font-bold text-[#759916]">${item.age || 'N/A'}</div>
            <div class="text-sm text-gray-600">ans</div>
          </div>
          <div class="bg-[#f6ffde] rounded-lg p-4 text-center">
            <div class="text-2xl font-bold text-[#759916]">${item.weight || 'N/A'}</div>
            <div class="text-sm text-gray-600">kg</div>
          </div>
        `;
      } else {
        statsContainer.innerHTML = `
          <div class="bg-[#f6ffde] rounded-lg p-4 text-center">
            <div class="text-2xl font-bold text-[#759916]">${item.surface || 'N/A'}</div>
            <div class="text-sm text-gray-600">ha</div>
          </div>
          <div class="bg-[#f6ffde] rounded-lg p-4 text-center">
            <div class="text-2xl font-bold text-[#759916]">${item.established || 'N/A'}</div>
            <div class="text-sm text-gray-600">Créée</div>
          </div>
        `;
      }

      // Caractéristiques
      const charContainer = document.getElementById('modal-characteristics');
      charContainer.innerHTML = '';
      if (item.characteristics) {
        Object.entries(item.characteristics).forEach(([key, value]) => {
          const div = document.createElement('div');
          div.className = 'flex justify-between items-center py-1 border-b border-gray-100';
          div.innerHTML = `
            <span class="font-medium">${key.replace('_', ' ')}:</span>
            <span class="text-gray-600">${value}</span>
          `;
          charContainer.appendChild(div);
        });
      }

      // Produits/Cultures (pour les fermes)
      if (item.crops || item.products || item.animals || item.species) {
        document.getElementById('modal-products').classList.remove('hidden');
        const productsList = document.getElementById('modal-products-list');
        productsList.innerHTML = '';
        
        const items = item.crops || item.products || item.animals || item.species || [];
        items.forEach(product => {
          const span = document.createElement('span');
          span.className = 'px-3 py-1 bg-[#a9cf46] text-white text-sm rounded-full';
          span.textContent = product;
          productsList.appendChild(span);
        });
      } else {
        document.getElementById('modal-products').classList.add('hidden');
      }

      // Miniatures
      const thumbnailsContainer = document.getElementById('modal-thumbnails');
      thumbnailsContainer.innerHTML = '';
      item.images.forEach((img, index) => {
        const thumb = document.createElement('img');
        thumb.src = img;
        thumb.className = 'w-16 h-16 object-cover rounded-lg cursor-pointer border-2 border-transparent hover:border-[#a9cf46] transition-colors';
        thumb.onclick = () => setCurrentImage(index);
        thumbnailsContainer.appendChild(thumb);
      });

      // Configurer l'image principale
      setCurrentImage(0);

      // Afficher/masquer les flèches de navigation
      updateNavigationButtons();

      modal.classList.remove('hidden');
      document.body.style.overflow = 'hidden';
    }

    // Fonction pour fermer le modal
    function closeModal() {
      document.getElementById('ranch-modal').classList.add('hidden');
      document.body.style.overflow = '';
      currentModalItem = null;
    }

    // Fonctions de navigation d'images
    function setCurrentImage(index) {
      if (!currentModalItem || !currentModalItem.images) return;
      
      currentImageIndex = index;
      const img = document.getElementById('modal-main-image');
      img.src = currentModalItem.images[index];
      
      // Reset transform
      resetTransform();
      
      // Mettre à jour le compteur
      document.getElementById('image-counter').textContent = `${index + 1}/${currentModalItem.images.length}`;
      
      // Mettre à jour les miniatures
      updateThumbnailsSelection();
      
      // Mettre à jour les boutons de navigation
      updateNavigationButtons();
    }

    function changeImage(direction) {
      if (!currentModalItem || !currentModalItem.images) return;
      
      const newIndex = currentImageIndex + direction;
      if (newIndex >= 0 && newIndex < currentModalItem.images.length) {
        setCurrentImage(newIndex);
      }
    }

    function updateThumbnailsSelection() {
      const thumbnails = document.querySelectorAll('#modal-thumbnails img');
      thumbnails.forEach((thumb, index) => {
        if (index === currentImageIndex) {
          thumb.classList.add('border-[#a9cf46]');
          thumb.classList.remove('border-transparent');
        } else {
          thumb.classList.remove('border-[#a9cf46]');
          thumb.classList.add('border-transparent');
        }
      });
    }

    function updateNavigationButtons() {
      if (!currentModalItem || !currentModalItem.images) return;
      
      const prevBtn = document.getElementById('prev-image');
      const nextBtn = document.getElementById('next-image');
      
      if (currentModalItem.images.length <= 1) {
        prevBtn.style.display = 'none';
        nextBtn.style.display = 'none';
      } else {
        prevBtn.style.display = 'block';
        nextBtn.style.display = 'block';
        
        prevBtn.style.opacity = currentImageIndex === 0 ? '0.5' : '1';
        nextBtn.style.opacity = currentImageIndex === currentModalItem.images.length - 1 ? '0.5' : '1';
      }
    }

    // Fonctions de transformation d'image
    function updateImageTransform() {
      const img = document.getElementById('modal-main-image');
      const transform = `
        scale(${imageTransform.zoom}) 
        rotate(${imageTransform.rotation}deg) 
        scaleX(${imageTransform.flipH}) 
        scaleY(${imageTransform.flipV})
        translate(${imageTransform.translateX}px, ${imageTransform.translateY}px)
      `;
      img.style.transform = transform;
    }

    function zoomIn() {
      imageTransform.zoom = Math.min(imageTransform.zoom * 1.2, 5);
      updateImageTransform();
    }

    function zoomOut() {
      imageTransform.zoom = Math.max(imageTransform.zoom / 1.2, 0.1);
      updateImageTransform();
    }

    function rotateLeft() {
      imageTransform.rotation -= 90;
      updateImageTransform();
    }

    function rotateRight() {
      imageTransform.rotation += 90;
      updateImageTransform();
    }

    function flipHorizontal() {
      imageTransform.flipH *= -1;
      updateImageTransform();
    }

    function flipVertical() {
      imageTransform.flipV *= -1;
      updateImageTransform();
    }

    function resetTransform() {
      imageTransform = {
        zoom: 1,
        rotation: 0,
        flipH: 1,
        flipV: 1,
        translateX: 0,
        translateY: 0
      };
      updateImageTransform();
    }

    // Fonction plein écran
    function fullscreen() {
      const viewer = document.getElementById('fullscreen-viewer');
      const img = document.getElementById('fullscreen-image');
      const currentImg = document.getElementById('modal-main-image');
      
      img.src = currentImg.src;
      img.style.transform = currentImg.style.transform;
      viewer.classList.remove('hidden');
      document.body.style.overflow = 'hidden';
    }

    function closeFullscreenViewer() {
      const viewer = document.getElementById('fullscreen-viewer');
      viewer.classList.add('hidden');
      document.body.style.overflow = '';
    }

    // Fonctions plein écran
    function fsZoomIn() {
      const img = document.getElementById('fullscreen-image');
      let currentTransform = img.style.transform || 'scale(1) rotate(0deg) scaleX(1) scaleY(1)';
      let scaleMatch = currentTransform.match(/scale\(([^)]+)\)/);
      let currentScale = scaleMatch ? parseFloat(scaleMatch[1]) : 1;
      img.style.transform = currentTransform.replace(/scale\([^)]+\)/, `scale(${Math.min(currentScale * 1.2, 5)})`);
    }

    function fsZoomOut() {
      const img = document.getElementById('fullscreen-image');
      let currentTransform = img.style.transform || 'scale(1) rotate(0deg) scaleX(1) scaleY(1)';
      let scaleMatch = currentTransform.match(/scale\(([^)]+)\)/);
      let currentScale = scaleMatch ? parseFloat(scaleMatch[1]) : 1;
      img.style.transform = currentTransform.replace(/scale\([^)]+\)/, `scale(${Math.max(currentScale / 1.2, 0.1)})`);
    }

    function fsRotateLeft() {
      const img = document.getElementById('fullscreen-image');
      let currentTransform = img.style.transform || 'scale(1) rotate(0deg) scaleX(1) scaleY(1)';
      let rotateMatch = currentTransform.match(/rotate\(([^)]+)deg\)/);
      let currentRotation = rotateMatch ? parseFloat(rotateMatch[1]) : 0;
      img.style.transform = currentTransform.replace(/rotate\([^)]+deg\)/, `rotate(${currentRotation - 90}deg)`);
    }

    function fsRotateRight() {
      const img = document.getElementById('fullscreen-image');
      let currentTransform = img.style.transform || 'scale(1) rotate(0deg) scaleX(1) scaleY(1)';
      let rotateMatch = currentTransform.match(/rotate\(([^)]+)deg\)/);
      let currentRotation = rotateMatch ? parseFloat(rotateMatch[1]) : 0;
      img.style.transform = currentTransform.replace(/rotate\([^)]+deg\)/, `rotate(${currentRotation + 90}deg)`);
    }

    function fsFlipH() {
      const img = document.getElementById('fullscreen-image');
      let currentTransform = img.style.transform || 'scale(1) rotate(0deg) scaleX(1) scaleY(1)';
      let scaleXMatch = currentTransform.match(/scaleX\(([^)]+)\)/);
      let currentScaleX = scaleXMatch ? parseFloat(scaleXMatch[1]) : 1;
      if (currentTransform.includes('scaleX(')) {
        img.style.transform = currentTransform.replace(/scaleX\([^)]+\)/, `scaleX(${currentScaleX * -1})`);
      } else {
        img.style.transform = currentTransform + ` scaleX(${currentScaleX * -1})`;
      }
    }

    function fsFlipV() {
      const img = document.getElementById('fullscreen-image');
      let currentTransform = img.style.transform || 'scale(1) rotate(0deg) scaleX(1) scaleY(1)';
      let scaleYMatch = currentTransform.match(/scaleY\(([^)]+)\)/);
      let currentScaleY = scaleYMatch ? parseFloat(scaleYMatch[1]) : 1;
      if (currentTransform.includes('scaleY(')) {
        img.style.transform = currentTransform.replace(/scaleY\([^)]+\)/, `scaleY(${currentScaleY * -1})`);
      } else {
        img.style.transform = currentTransform + ` scaleY(${currentScaleY * -1})`;
      }
    }

    function fsReset() {
      document.getElementById('fullscreen-image').style.transform = 'scale(1) rotate(0deg) scaleX(1) scaleY(1)';
    }

    // Fonction pour configurer le glisser-déposer des images
    function setupImageDragging() {
      let isDragging = false;
      let startX, startY;

      const img = document.getElementById('modal-main-image');
      
      img.addEventListener('mousedown', (e) => {
        isDragging = true;
        startX = e.clientX - imageTransform.translateX;
        startY = e.clientY - imageTransform.translateY;
        img.style.cursor = 'grabbing';
      });

      document.addEventListener('mousemove', (e) => {
        if (!isDragging) return;
        
        imageTransform.translateX = e.clientX - startX;
        imageTransform.translateY = e.clientY - startY;
        updateImageTransform();
      });

      document.addEventListener('mouseup', () => {
        isDragging = false;
        img.style.cursor = 'grab';
      });
    }

    // Fonction pour gérer la newsletter
    function handleNewsletter(e) {
      e.preventDefault();
      const msg = document.getElementById('newsletter-msg');
      msg.classList.remove('hidden');
      setTimeout(() => {
        msg.classList.add('hidden');
        e.target.reset();
      }, 3000);
    }

    // Gestion des touches clavier
    document.addEventListener('keydown', (e) => {
      if (document.getElementById('ranch-modal').classList.contains('hidden')) return;
      
      switch(e.key) {
        case 'ArrowLeft':
          changeImage(-1);
          break;
        case 'ArrowRight':
          changeImage(1);
          break;
        case 'Escape':
          if (!document.getElementById('fullscreen-viewer').classList.contains('hidden')) {
            closeFullscreenViewer();
          } else {
            closeModal();
          }
          break;
        case '+':
        case '=':
          zoomIn();
          break;
        case '-':
          zoomOut();
          break;
        case 'r':
          rotateRight();
          break;
        case 'R':
          rotateLeft();
          break;
        case 'h':
          flipHorizontal();
          break;
        case 'v':
          flipVertical();
          break;
        case '0':
          resetTransform();
          break;
        case 'f':
          fullscreen();
          break;
      }
    });
  </script>
</body>
</html>