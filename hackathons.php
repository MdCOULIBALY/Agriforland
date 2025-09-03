<?php
// Démarrer la session
session_start();

// Charger les données des hackathons depuis le fichier JSON
$hackathons_file = 'data/hackathons.json';
$hackathons = [];
$error_message = '';

if (!file_exists($hackathons_file)) {
  $error_message = 'Fichier de données non trouvé.';
} else {
  $hackathons_data = file_get_contents($hackathons_file);
  $hackathons_json = json_decode($hackathons_data, true);
  
  if (!$hackathons_json || !isset($hackathons_json['hackathons'])) {
    $error_message = 'Données de hackathons invalides.';
  } else {
    $hackathons = $hackathons_json['hackathons'];
  }
}

// Vérifier les dates d'expiration
$now = new DateTime('now', new DateTimeZone('GMT'));
// $deadline = new DateTime('2025-07-31T23:59:59Z', new DateTimeZone('GMT'));
$deadline = new DateTime('2025-08-03T23:59:59Z', new DateTimeZone('GMT'));

$is_expired = $now > $deadline;

// Calculer le temps restant
$time_remaining = $deadline->diff($now);
$days_remaining = $is_expired ? 0 : $time_remaining->days;

// Gestion des messages de succès
$success_message = isset($_GET['success']) ? $_GET['success'] : '';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title data-i18n="page_title">Hackathons - AGRIFORLAND</title>
  
  <!-- Meta description -->
  <meta name="description" content="Participez aux hackathons AGRIFORLAND et contribuez à l'innovation dans l'agriculture et la foresterie. Créez des designs impactants pour nos projets.">
  
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
    function gtag() { dataLayer.push(arguments); }
    gtag('js', new Date());
    gtag('config', 'G-ZKKVQJJCYG');
  </script>
  
  <style>
    .hackathon-card {
      transition: all 0.3s ease;
      background: linear-gradient(135deg, #ffffff 0%, #f8fff8 100%);
    }
    .hackathon-card:hover {
      transform: translateY(-8px);
      box-shadow: 0 20px 40px rgba(169, 207, 70, 0.15);
    }
    .status-badge {
      position: absolute;
      top: 0.5rem;
      right: 0.5rem;
      z-index: 10;
    }
    @media (min-width: 768px) {
      .status-badge {
        top: 1rem;
        right: 1rem;
      }
    }
    .card-overlay {
      background: linear-gradient(180deg, transparent 0%, rgba(0,0,0,0.7) 100%);
    }
    .countdown-global {
      background: linear-gradient(135deg, #a9cf46, #759916);
      animation: pulse 2s infinite;
    }
    @keyframes pulse {
      0%, 100% { opacity: 1; }
      50% { opacity: 0.8; }
    }
    .hero-pattern {
      background-image: 
        radial-gradient(circle at 25% 25%, rgba(169, 207, 70, 0.1) 0%, transparent 50%),
        radial-gradient(circle at 75% 75%, rgba(117, 153, 22, 0.1) 0%, transparent 50%);
    }
    .floating-element {
      animation: float 6s ease-in-out infinite;
    }
    @keyframes float {
      0%, 100% { transform: translateY(0px); }
      50% { transform: translateY(-20px); }
    }
    .filter-tab {
      transition: all 0.3s ease;
    }
    .filter-tab.active {
      background: linear-gradient(135deg, #a9cf46, #759916);
      color: white;
      transform: scale(1.05);
    }
    .stats-counter {
      background: linear-gradient(135deg, rgba(255,255,255,0.95) 0%, rgba(248,255,248,0.95) 100%);
      backdrop-filter: blur(10px);
    }
    
    /* Responsive improvements */
    @media (max-width: 640px) {
      .floating-element {
        display: none;
      }
      .hackathon-card:hover {
        transform: translateY(-4px);
      }
    }
    
    .animate-fade-in-up {
      animation: fadeInUp 0.6s ease-out forwards;
    }
    
    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(30px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
    
    /* Mobile optimizations */
    @media (max-width: 768px) {
      .hero-section {
        min-height: 70vh;
      }
    }
  </style>
</head>

<body class="bg-[#f6ffde] text-black font-roboto">
    <!-- En tête -->
         <?php require __DIR__ . "/includes/header.php"; ?>

  <!-- Hero Section -->
  <section class="hero-section relative min-h-[60vh] sm:min-h-[70vh] lg:min-h-[80vh] flex items-center justify-center hero-pattern overflow-hidden">
    <div class="absolute inset-0">
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
        data-alt-i18n="hackathons_hero"
        class="w-full h-full object-cover"
        style="filter: brightness(0.4);"
      >
    </div>
    
    <!-- Éléments flottants décoratifs (cachés sur mobile) -->
    <div class="floating-element absolute top-16 sm:top-20 left-4 sm:left-10 opacity-20 sm:opacity-30">
      <i class="ph ph-lightbulb text-[#a9cf46] text-4xl sm:text-5xl lg:text-6xl"></i>
    </div>
    <div class="floating-element absolute top-24 sm:top-32 right-8 sm:right-20 opacity-15 sm:opacity-20" style="animation-delay: -2s;">
      <i class="ph ph-code text-[#759916] text-5xl sm:text-6xl lg:text-8xl"></i>
    </div>
    <div class="floating-element absolute bottom-16 sm:bottom-20 left-8 sm:left-20 opacity-20 sm:opacity-25" style="animation-delay: -4s;">
      <i class="ph ph-rocket text-[#a9cf46] text-3xl sm:text-4xl lg:text-5xl"></i>
    </div>
    
    <div class="relative z-10 text-center px-4 sm:px-6 max-w-5xl mx-auto">
      <h1 class="text-white text-3xl sm:text-4xl md:text-5xl lg:text-6xl xl:text-7xl font-bold font-kanit mb-4 sm:mb-6 leading-tight">
        <span data-i18n="hero_title">Hackathons</span>
        <span class="block text-[#a9cf46] text-2xl sm:text-3xl md:text-4xl lg:text-5xl xl:text-6xl" data-i18n="hero_subtitle">AGRIFORLAND</span>
      </h1>
      <p class="text-white/90 text-base sm:text-lg md:text-xl lg:text-2xl mb-6 sm:mb-8 leading-relaxed max-w-3xl mx-auto" data-i18n="hero_description">
        Libérez votre créativité et participez à l'innovation agricole et forestière
      </p>
      
      <!-- Statistiques -->
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6 sm:mb-8">
        <div class="stats-counter rounded-lg p-3 sm:p-4 text-center">
          <div class="text-xl sm:text-2xl lg:text-3xl font-bold text-[#759916]" id="hackathon-count"><?php echo count($hackathons); ?></div>
          <div class="text-xs sm:text-sm text-gray-600" data-i18n="hackathons_available">Hackathons</div>
        </div>
        <div class="stats-counter rounded-lg p-3 sm:p-4 text-center">
          <div class="text-xl sm:text-2xl lg:text-3xl font-bold text-[#a9cf46]">10K+</div>
          <div class="text-xs sm:text-sm text-gray-600" data-i18n="prizes_total">Prix Total</div>
        </div>
        <div class="stats-counter rounded-lg p-3 sm:p-4 text-center">
          <div class="text-xl sm:text-2xl lg:text-3xl font-bold text-[#759916]" id="days-counter"><?php echo $days_remaining; ?></div>
          <div class="text-xs sm:text-sm text-gray-600" data-i18n="days_remaining">Jours restants</div>
        </div>
        <div class="stats-counter rounded-lg p-3 sm:p-4 text-center">
          <div class="text-xl sm:text-2xl lg:text-3xl font-bold text-[#a9cf46]">100%</div>
          <div class="text-xs sm:text-sm text-gray-600" data-i18n="remote_participation">Participation<br class="sm:hidden"> 100% en ligne</div>
        </div>
      </div>

      <?php if (!$is_expired) : ?>
        <div class="countdown-global text-white p-3 sm:p-4 rounded-lg inline-block mb-4 sm:mb-6">
          <div class="text-xs sm:text-sm font-semibold mb-2" data-i18n="registration_deadline">Date limite d'inscription</div>
          <div id="global-countdown" class="text-lg sm:text-xl lg:text-2xl font-bold">
            <span id="global-days"><?php echo $days_remaining; ?></span> <span data-i18n="days">jours</span>
            <span id="global-hours"><?php echo $time_remaining->h; ?></span> <span data-i18n="hours">h</span>
            <span id="global-minutes"><?php echo $time_remaining->i; ?></span> <span data-i18n="minutes">min</span>
            <span id="global-seconds"><?php echo $time_remaining->s; ?></span> <span data-i18n="seconds">s</span>
          </div>
        </div>
      <?php endif; ?>
      
      <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 justify-center">
        <a href="#hackathons-list" class="bg-[#a9cf46] hover:bg-[#93bc3d] text-black px-6 sm:px-8 py-3 sm:py-4 rounded-lg font-semibold transition-all duration-200 transform hover:scale-105 inline-flex items-center justify-center text-sm sm:text-base">
          <i class="ph ph-list mr-2"></i>
          <span data-i18n="view_hackathons">Voir les Hackathons</span>
        </a>
        <a href="#how-it-works" class="border-2 border-white text-white hover:bg-white hover:text-black px-6 sm:px-8 py-3 sm:py-4 rounded-lg font-semibold transition-all duration-200 transform hover:scale-105 inline-flex items-center justify-center text-sm sm:text-base">
          <i class="ph ph-question mr-2"></i>
          <span data-i18n="how_it_works">Comment ça marche</span>
        </a>
      </div>
    </div>
  </section>

  <!-- Message de succès -->
  <?php if ($success_message === '1') : ?>
    <div id="success-banner" class="bg-green-50 border-l-4 border-green-400 p-4 mx-3 sm:mx-4 rounded-r-lg mt-4">
      <div class="flex items-center">
        <i class="ph ph-check-circle text-green-400 text-xl mr-3"></i>
        <div class="flex-1">
          <p class="text-green-700 font-semibold text-sm sm:text-base" data-i18n="registration_success_global">
            Inscription réussie ! Vous recevrez une confirmation par e-mail.
          </p>
        </div>
        <button onclick="document.getElementById('success-banner').remove()" class="ml-auto text-green-400 hover:text-green-600 p-1">
          <i class="ph ph-x text-lg sm:text-xl"></i>
        </button>
      </div>
    </div>
  <?php endif; ?>

  <!-- Section Comment participer -->
  <section id="how-it-works" class="py-8 sm:py-12 lg:py-16 bg-white">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">
      <div class="text-center mb-8 sm:mb-12">
        <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold mb-3 sm:mb-4" data-i18n="how_it_works_title">Comment participer</h2>
        <p class="text-gray-600 text-base sm:text-lg max-w-2xl mx-auto" data-i18n="how_it_works_subtitle">
          Suivez ces étapes simples pour participer au Hackathon Visuel 2025
        </p>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 sm:gap-8">
        
        <!-- Étape 1 -->
        <div class="text-center group">
          <div class="w-12 h-12 sm:w-16 sm:h-16 bg-[#a9cf46] rounded-full flex items-center justify-center mx-auto mb-3 sm:mb-4 group-hover:scale-110 transition-transform">
            <i class="ph ph-lightbulb text-white text-lg sm:text-2xl"></i>
          </div>
          <h3 class="font-semibold text-base sm:text-lg mb-2">1. Choisissez un projet</h3>
          <p class="text-gray-600 text-sm">
            Sélectionnez 1 ou les 2 projets proposés dans le hackathon visuel.
          </p>
        </div>

        <!-- Étape 2 -->
        <div class="text-center group">
          <div class="w-12 h-12 sm:w-16 sm:h-16 bg-[#759916] rounded-full flex items-center justify-center mx-auto mb-3 sm:mb-4 group-hover:scale-110 transition-transform">
            <i class="ph ph-paint-brush-broad text-white text-lg sm:text-2xl"></i>
          </div>
          <h3 class="font-semibold text-base sm:text-lg mb-2">2. Créez vos visuels</h3>
          <p class="text-gray-600 text-sm">
            Réalisez librement vos créations : logo, affiche et identité visuelle.
          </p>
        </div>

        <!-- Étape 3 -->
        <div class="text-center group">
          <div class="w-12 h-12 sm:w-16 sm:h-16 bg-[#a9cf46] rounded-full flex items-center justify-center mx-auto mb-3 sm:mb-4 group-hover:scale-110 transition-transform">
            <i class="ph ph-upload-simple text-white text-lg sm:text-2xl"></i>
          </div>
          <h3 class="font-semibold text-base sm:text-lg mb-2">3. Soumettez vos créations</h3>
          <p class="text-gray-600 text-sm">
            Remplissez le formulaire uniquement lorsque vos visuels sont prêts.  
            Le lien soumis doit contenir uniquement les livrables du projet (Google Drive, Dropbox, etc.).  
            <strong>Pas besoin de portfolio ou d'inscription préalable.</strong>
          </p>
        </div>

        <!-- Étape 4 -->
        <div class="text-center group">
          <div class="w-12 h-12 sm:w-16 sm:h-16 bg-[#759916] rounded-full flex items-center justify-center mx-auto mb-3 sm:mb-4 group-hover:scale-110 transition-transform">
            <i class="ph ph-trophy text-white text-lg sm:text-2xl"></i>
          </div>
          <h3 class="font-semibold text-base sm:text-lg mb-2">4. Gagnez des récompenses</h3>
          <p class="text-gray-600 text-sm">
            Les meilleures propositions recevront 100.000 FCFA + 3 mois de stage chez AGRIFORLAND.
          </p>
        </div>

      </div>
    </div>
  </section>

  <!-- Section Liste des Hackathons -->
  <section id="hackathons-list" class="py-8 sm:py-12 lg:py-16 bg-[#f1ffcd]">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">
      <div class="text-center mb-8 sm:mb-12">
        <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold mb-3 sm:mb-4" data-i18n="available_hackathons">Hackathons Disponibles</h2>
        <p class="text-gray-600 text-base sm:text-lg max-w-2xl mx-auto" data-i18n="hackathons_description">
          Découvrez nos défis créatifs et participez à l'innovation
        </p>
      </div>

      <!-- Filtres -->
      <div class="flex flex-wrap justify-center gap-3 sm:gap-4 mb-6 sm:mb-8">
        <button class="filter-tab active px-4 sm:px-6 py-2 rounded-full border-2 border-[#a9cf46] text-sm font-medium transition-all" data-filter="all" data-i18n="filter_all">
          Tous
        </button>
        <button class="filter-tab px-4 sm:px-6 py-2 rounded-full border-2 border-gray-300 text-sm font-medium transition-all" data-filter="open" data-i18n="filter_open">
          Ouvert
        </button>
        <button class="filter-tab px-4 sm:px-6 py-2 rounded-full border-2 border-gray-300 text-sm font-medium transition-all" data-filter="closed" data-i18n="filter_closed">
          Fermé
        </button>
      </div>

      <?php if ($error_message) : ?>
        <div class="text-center py-8 sm:py-12">
          <i class="ph ph-warning-circle text-red-500 text-4xl sm:text-6xl mb-4"></i>
          <h3 class="text-lg sm:text-xl font-semibold text-red-600 mb-2" data-i18n="error_title">Erreur de chargement</h3>
          <p class="text-gray-600 text-sm sm:text-base" data-i18n="error_message"><?php echo htmlspecialchars($error_message); ?></p>
        </div>
      <?php elseif (empty($hackathons)) : ?>
        <div class="text-center py-8 sm:py-12">
          <i class="ph ph-calendar-x text-gray-400 text-4xl sm:text-6xl mb-4"></i>
          <h3 class="text-lg sm:text-xl font-semibold text-gray-600 mb-2" data-i18n="no_hackathons_title">Aucun hackathon disponible</h3>
          <p class="text-gray-500 text-sm sm:text-base" data-i18n="no_hackathons_message">Revenez bientôt pour découvrir nos prochains défis créatifs !</p>
        </div>
      <?php else : ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4 sm:gap-6 lg:gap-8" id="hackathons-grid">
          <?php foreach ($hackathons as $hackathon) : ?>
            <div class="hackathon-card bg-white rounded-xl shadow-lg overflow-hidden relative group" data-status="<?php echo $is_expired ? 'closed' : 'open'; ?>">
              <!-- Badge de statut -->
              <div class="status-badge">
                <?php if ($is_expired) : ?>
                  <span class="bg-red-500 text-white px-2 sm:px-3 py-1 rounded-full text-xs font-semibold">
                    <i class="ph ph-lock mr-1"></i>
                    <span data-i18n="status_closed">Fermé</span>
                  </span>
                <?php else : ?>
                  <span class="bg-green-500 text-white px-2 sm:px-3 py-1 rounded-full text-xs font-semibold">
                    <i class="ph ph-unlock mr-1"></i>
                    <span data-i18n="status_open">Ouvert</span>
                  </span>
                <?php endif; ?>
              </div>

              <!-- Image -->
              <div class="relative h-40 sm:h-48 overflow-hidden">
                <img 
                  src="<?php echo htmlspecialchars($hackathon['image']); ?>" 
                  srcset="
                    <?php echo htmlspecialchars(str_replace('800', '480', $hackathon['image'])); ?> 480w, 
                    <?php echo htmlspecialchars($hackathon['image']); ?> 800w
                  "
                  sizes="(max-width: 768px) 480px, 800px"
                  loading="lazy" 
                  alt="" 
                  data-alt-i18n="hackathon_image"
                  class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                >
                <div class="card-overlay absolute inset-0"></div>
                
                <!-- Informations overlay -->
                <div class="absolute bottom-3 sm:bottom-4 left-3 sm:left-4 right-3 sm:right-4 text-white">
                  <div class="flex items-center justify-between">
                    <div class="flex items-center text-xs sm:text-sm">
                      <i class="ph ph-calendar mr-1"></i>
                      <span><?php echo htmlspecialchars($hackathon['date']); ?></span>
                    </div>
                    <div class="flex items-center text-xs sm:text-sm">
                      <i class="ph ph-map-pin mr-1"></i>
                      <span class="truncate max-w-20 sm:max-w-none"><?php echo htmlspecialchars($hackathon['location']); ?></span>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Contenu -->
              <div class="p-4 sm:p-6">
                <h3 class="text-lg sm:text-xl font-bold mb-2 text-gray-800 line-clamp-2">
                  <?php echo htmlspecialchars($hackathon['title']); ?>
                </h3>
                
                <p class="text-gray-600 text-sm mb-4 line-clamp-3">
                  <?php echo htmlspecialchars(substr($hackathon['description'], 0, 120)) . '...'; ?>
                </p>
                
                <!-- Détails -->
                <div class="space-y-2 mb-4 sm:mb-6">
                  <div class="flex items-start text-sm text-gray-600">
                    <i class="ph ph-lightbulb text-[#a9cf46] mr-2 mt-0.5 flex-shrink-0"></i>
                    <div>
                      <span class="font-medium" data-i18n="theme">Thème :</span>
                      <span class="ml-1"><?php echo htmlspecialchars($hackathon['theme']); ?></span>
                    </div>
                  </div>
                  <div class="flex items-start text-sm text-gray-600">
                    <i class="ph ph-trophy text-[#a9cf46] mr-2 mt-0.5 flex-shrink-0"></i>
                    <div>
                      <span class="font-medium" data-i18n="prizes">Prix :</span>
                      <span class="ml-1"><?php echo htmlspecialchars($hackathon['prizes']); ?></span>
                    </div>
                  </div>
                </div>
                
                <!-- Actions -->
                <div class="flex flex-col sm:flex-row gap-2 sm:gap-3">
                  <a href="hackathon-detail.php?id=<?php echo $hackathon['id']; ?>" 
                     class="flex-1 bg-[#a9cf46] hover:bg-[#93bc3d] text-black px-3 sm:px-4 py-2 rounded-lg font-semibold transition-all duration-200 text-center text-sm inline-flex items-center justify-center">
                    <i class="ph ph-eye mr-2"></i>
                    <span data-i18n="view_details">Voir Détails</span>
                  </a>
                  
                  <?php if (!$is_expired) : ?>
                    <a href="hackathon-detail.php?id=<?php echo $hackathon['id']; ?>#hackathon-form" 
                       class="bg-[#759916] hover:bg-[#6a8515] text-white px-3 sm:px-4 py-2 rounded-lg font-semibold transition-all duration-200 text-center text-sm inline-flex items-center justify-center">
                      <i class="ph ph-paper-plane-tilt mr-2"></i>
                      <span data-i18n="register_now">S'inscrire</span>
                    </a>
                  <?php else : ?>
                    <button disabled class="bg-gray-400 text-white px-3 sm:px-4 py-2 rounded-lg font-semibold text-center text-sm inline-flex items-center justify-center cursor-not-allowed">
                      <i class="ph ph-lock mr-2"></i>
                      <span data-i18n="closed">Fermé</span>
                    </button>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </section>

  <!-- Section CTA -->
  <section class="py-8 sm:py-12 lg:py-16 bg-gradient-to-r from-[#a9cf46] to-[#759916] text-white">
    <div class="max-w-4xl mx-auto text-center px-4 sm:px-6">
      <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold mb-3 sm:mb-4" data-i18n="cta_title">Prêt à relever le défi ?</h2>
      <p class="text-base sm:text-lg lg:text-xl mb-6 sm:mb-8 opacity-90" data-i18n="cta_description">
        Participez à nos hackathons et contribuez à l'innovation dans l'agriculture et la foresterie
      </p>
      
      <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 justify-center">
        <a href="#hackathons-list" class="bg-white text-[#759916] hover:bg-gray-100 px-6 sm:px-8 py-3 sm:py-4 rounded-lg font-semibold transition-all duration-200 transform hover:scale-105 inline-flex items-center justify-center text-sm sm:text-base">
          <i class="ph ph-rocket-launch mr-2"></i>
          <span data-i18n="start_now">Commencer Maintenant</span>
        </a>
        <a href="contact.html" class="border-2 border-white text-white hover:bg-white hover:text-[#759916] px-6 sm:px-8 py-3 sm:py-4 rounded-lg font-semibold transition-all duration-200 transform hover:scale-105 inline-flex items-center justify-center text-sm sm:text-base">
          <i class="ph ph-chat-circle mr-2"></i>
          <span data-i18n="contact_us">Nous Contacter</span>
        </a>
      </div>
    </div>
  </section>

    <!-- Footer -->
     
    <?php require __DIR__ . "/includes/footer.php"; ?>

  <!-- Scripts -->
  <script>
    // Language translations (version étendue pour la page hackathons)
    const translations = {
      fr: {
        page_title: "Hackathons - AGRIFORLAND",
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
        hackathons: "Hackathons",
        hackathons_hero: "Bannière Hackathons",
        hero_title: "Hackathons",
        hero_subtitle: "AGRIFORLAND",
        hero_description: "Libérez votre créativité et participez à l'innovation agricole et forestière",
        hackathons_available: "Hackathons",
        prizes_total: "Prix Total",
        days_remaining: "Jours restants",
        remote_participation: "Participation 100% en ligne",
        registration_deadline: "Date limite d'inscription",
        days: "jours",
        hours: "h",
        minutes: "min",
        seconds: "s",
        view_hackathons: "Voir les Hackathons",
        how_it_works: "Comment ça marche",
        registration_success_global: "Inscription réussie ! Vous recevrez une confirmation par e-mail.",
        how_it_works_title: "Comment participer",
        how_it_works_subtitle: "Suivez ces étapes simples pour participer à nos hackathons",
        step_1_title: "1. Inscrivez-vous",
        step_1_desc: "Remplissez le formulaire d'inscription avec vos informations",
        step_2_title: "2. Créez",
        step_2_desc: "Développez vos idées créatives selon le brief du hackathon",
        step_3_title: "3. Soumettez",
        step_3_desc: "Envoyez vos créations avant la date limite",
        step_4_title: "4. Gagnez",
        step_4_desc: "Les meilleures créations remportent des prix attractifs",
        available_hackathons: "Hackathons Disponibles",
        hackathons_description: "Découvrez nos défis créatifs et participez à l'innovation",
        filter_all: "Tous",
        filter_open: "Ouvert",
        filter_closed: "Fermé",
        error_title: "Erreur de chargement",
        error_message: "Impossible de charger les données des hackathons",
        no_hackathons_title: "Aucun hackathon disponible",
        no_hackathons_message: "Revenez bientôt pour découvrir nos prochains défis créatifs !",
        status_closed: "Fermé",
        status_open: "Ouvert",
        hackathon_image: "Image du hackathon",
        theme: "Thème",
        prizes: "Prix",
        view_details: "Voir Détails",
        register_now: "S'inscrire",
        closed: "Fermé",
        cta_title: "Prêt à relever le défi ?",
        cta_description: "Participez à nos hackathons et contribuez à l'innovation dans l'agriculture et la foresterie",
        start_now: "Commencer Maintenant",
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
        subscribing: "Inscription...",
        newsletter_success: "Merci pour votre inscription !",
        newsletter_error: "Erreur lors de l'inscription.",
        copyright: "© 2025 Agriforland. Tous droits réservés."
      },
      en: {
        page_title: "Hackathons - AGRIFORLAND",
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
        hackathons: "Hackathons",
        hackathons_hero: "Hackathons Banner",
        hero_title: "Hackathons",
        hero_subtitle: "AGRIFORLAND",
        hero_description: "Unleash your creativity and participate in agricultural and forestry innovation",
        hackathons_available: "Hackathons",
        prizes_total: "Total Prizes",
        days_remaining: "Days remaining",
        remote_participation: "100% online participation",
        registration_deadline: "Registration deadline",
        days: "days",
        hours: "h",
        minutes: "min",
        seconds: "s",
        view_hackathons: "View Hackathons",
        how_it_works: "How it works",
        registration_success_global: "Registration successful! You will receive a confirmation email.",
        how_it_works_title: "How to participate",
        how_it_works_subtitle: "Follow these simple steps to participate in our hackathons",
        step_1_title: "1. Register",
        step_1_desc: "Fill out the registration form with your information",
        step_2_title: "2. Create",
        step_2_desc: "Develop your creative ideas according to the hackathon brief",
        step_3_title: "3. Submit",
        step_3_desc: "Send your creations before the deadline",
        step_4_title: "4. Win",
        step_4_desc: "The best creations win attractive prizes",
        available_hackathons: "Available Hackathons",
        hackathons_description: "Discover our creative challenges and participate in innovation",
        filter_all: "All",
        filter_open: "Open",
        filter_closed: "Closed",
        error_title: "Loading error",
        error_message: "Unable to load hackathon data",
        no_hackathons_title: "No hackathons available",
        no_hackathons_message: "Come back soon to discover our next creative challenges!",
        status_closed: "Closed",
        status_open: "Open",
        hackathon_image: "Hackathon image",
        theme: "Theme",
        prizes: "Prizes",
        view_details: "View Details",
        register_now: "Register",
        closed: "Closed",
        cta_title: "Ready to take on the challenge?",
        cta_description: "Participate in our hackathons and contribute to innovation in agriculture and forestry",
        start_now: "Start Now",
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
        subscribing: "Subscribing...",
        newsletter_success: "Thank you for subscribing!",
        newsletter_error: "Error during subscription.",
        copyright: "© 2025 Agriforland. All rights reserved."
      }
    };

    // Variables globales
    let currentLanguage = localStorage.getItem('language') || 'fr';

    // Sélecteurs DOM
    const languageSelectors = document.querySelectorAll('#language-selector, #language-selector-mobile');
    const languageIcons = document.querySelectorAll('#language-icon, #language-icon-mobile');

    // Fonctions utilitaires
    function updateContent(lang) {
      currentLanguage = lang;
      
      document.querySelectorAll('[data-i18n]').forEach(element => {
        const key = element.getAttribute('data-i18n');
        if (translations[lang] && translations[lang][key]) {
          element.textContent = translations[lang][key];
        }
      });
      
      document.querySelectorAll('[data-i18n-placeholder]').forEach(element => {
        const key = element.getAttribute('data-i18n-placeholder');
        if (translations[lang] && translations[lang][key]) {
          element.placeholder = translations[lang][key];
        }
      });
      
      document.querySelectorAll('[data-aria-i18n]').forEach(element => {
        const key = element.getAttribute('data-aria-i18n');
        if (translations[lang] && translations[lang][key]) {
          element.setAttribute('aria-label', translations[lang][key]);
        }
      });
      
      document.querySelectorAll('[data-alt-i18n]').forEach(element => {
        const key = element.getAttribute('data-alt-i18n');
        if (translations[lang] && translations[lang][key]) {
          element.setAttribute('alt', translations[lang][key]);
        }
      });
      
      document.documentElement.lang = lang;
      languageIcons.forEach(icon => icon.src = `images/${lang}.webp`);
      languageSelectors.forEach(selector => selector.value = lang);
    }

    // Event Listeners pour le changement de langue
    languageSelectors.forEach(selector => {
      selector.addEventListener('change', e => {
        const selectedLang = e.target.value;
        updateContent(selectedLang);
        localStorage.setItem('language', selectedLang);
      });
    });

    // Initialisation de la langue
    updateContent(currentLanguage);

    // Preloader
    window.addEventListener('load', () => {
      const preloader = document.getElementById('preloader');
      preloader.classList.add('opacity-0', 'pointer-events-none', 'transition-opacity', 'duration-500');
      setTimeout(() => preloader.remove(), 500);
    });

    // Menu mobile amélioré
    const toggle = document.getElementById('menu-toggle');
    const menu = document.getElementById('mobile-menu');
    
    if (toggle && menu) {
      toggle.addEventListener('click', (e) => {
        e.stopPropagation();
        menu.classList.toggle('hidden');
        
        // Animation du bouton burger
        if (!menu.classList.contains('hidden')) {
          toggle.innerHTML = `
            <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
              <path d="M18 6L6 18M6 6l12 12"/>
            </svg>
          `;
        } else {
          toggle.innerHTML = `
            <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
              <path d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
          `;
        }
      });

      // Fermer le menu en cliquant sur un lien
      const mobileLinks = menu.querySelectorAll('a');
      mobileLinks.forEach(link => {
        link.addEventListener('click', () => {
          menu.classList.add('hidden');
          toggle.innerHTML = `
            <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
              <path d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
          `;
        });
      });

      // Fermer le menu en cliquant en dehors
      document.addEventListener('click', e => {
        if (!toggle.contains(e.target) && !menu.contains(e.target)) {
          menu.classList.add('hidden');
          toggle.innerHTML = `
            <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
              <path d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
          `;
        }
      });
    }

    // Countdown timer global
    const globalCountdown = document.getElementById('global-countdown');
    if (globalCountdown) {
      // const deadline = new Date('2025-07-31T23:59:59Z');
     const deadline = new Date('2025-08-03T23:59:59Z');

      function updateGlobalCountdown() {
        const now = new Date();
        const diff = deadline - now;
        
        if (diff <= 0) {
          globalCountdown.innerHTML = '<span class="text-red-400">Expiré</span>';
          return;
        }
        
        const days = Math.floor(diff / (1000 * 60 * 60 * 24));
        const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((diff % (1000 * 60)) / 1000);
        
        const daysEl = document.getElementById('global-days');
        const hoursEl = document.getElementById('global-hours');
        const minutesEl = document.getElementById('global-minutes');
        const secondsEl = document.getElementById('global-seconds');
        
        if (daysEl) daysEl.textContent = days;
        if (hoursEl) hoursEl.textContent = hours;
        if (minutesEl) minutesEl.textContent = minutes;
        if (secondsEl) secondsEl.textContent = seconds;
        
        // Mettre à jour le compteur dans les stats
        const daysCounter = document.getElementById('days-counter');
        if (daysCounter) {
          daysCounter.textContent = days;
        }
      }
      
      updateGlobalCountdown();
      setInterval(updateGlobalCountdown, 1000);
    }

    // Système de filtrage des hackathons amélioré
    const filterTabs = document.querySelectorAll('.filter-tab');
    const hackathonCards = document.querySelectorAll('.hackathon-card');
    
    filterTabs.forEach(tab => {
      tab.addEventListener('click', () => {
        const filter = tab.getAttribute('data-filter');
        
        // Mettre à jour les onglets actifs
        filterTabs.forEach(t => t.classList.remove('active'));
        tab.classList.add('active');
        
        // Filtrer les cartes avec animation améliorée
        hackathonCards.forEach((card, index) => {
          const status = card.getAttribute('data-status');
          
          if (filter === 'all' || filter === status) {
            card.style.display = 'block';
            setTimeout(() => {
              card.style.opacity = '1';
              card.style.transform = 'translateY(0) scale(1)';
            }, index * 50); // Animation en cascade
          } else {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px) scale(0.95)';
            setTimeout(() => {
              card.style.display = 'none';
            }, 300);
          }
        });
      });
    });

    // Smooth scroll pour les liens d'ancrage avec offset pour header fixe
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
      anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
          const headerHeight = document.querySelector('header').offsetHeight;
          const targetPosition = target.offsetTop - headerHeight;
          
          window.scrollTo({
            top: targetPosition,
            behavior: 'smooth'
          });
        }
      });
    });

    // Animation des compteurs améliorée
    function animateCounters() {
      const counters = document.querySelectorAll('.stats-counter [class*="text-"]');
      
      counters.forEach(counter => {
        const text = counter.textContent;
        const target = parseInt(text.replace(/\D/g, ''));
        if (!target) return;
        
        let current = 0;
        const increment = target / 60; // Animation plus fluide
        const timer = setInterval(() => {
          current += increment;
          if (current >= target) {
            current = target;
            clearInterval(timer);
          }
          
          if (text.includes('K')) {
            counter.textContent = Math.floor(current / 1000) + 'K+';
          } else if (text.includes('%')) {
            counter.textContent = Math.floor(current) + '%';
          } else {
            counter.textContent = Math.floor(current);
          }
        }, 30);
      });
    }

    // Intersection Observer pour les animations
    const observerOptions = {
      threshold: 0.1,
      rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('animate-fade-in-up');
          
          // Animer les compteurs quand ils entrent dans la vue
          if (entry.target.querySelector('.stats-counter')) {
            setTimeout(animateCounters, 300);
          }
        }
      });
    }, observerOptions);

    // Observer les éléments à animer
    document.querySelectorAll('.hackathon-card, .stats-counter, .step').forEach(el => {
      observer.observe(el);
    });

    // Newsletter form avec amélioration UX
    const newsletterForm = document.getElementById('newsletter-form');
    const newsletterMsg = document.getElementById('newsletter-msg');
    const newsletterBtn = document.getElementById('newsletter-btn');
    
    if (newsletterForm) {
      newsletterForm.addEventListener('submit', async e => {
        e.preventDefault();
        
        newsletterBtn.disabled = true;
        newsletterBtn.textContent = translations[currentLanguage].subscribing;
        
        try {
          const formData = new FormData(newsletterForm);
          const response = await fetch('back/newsletter.php', {
            method: 'POST',
            body: formData
          });
          
          if (response.ok) {
            newsletterMsg.classList.remove('hidden', 'text-red-600');
            newsletterMsg.classList.add('text-green-600');
            newsletterMsg.textContent = translations[currentLanguage].newsletter_success;
            newsletterForm.reset();
          } else {
            throw new Error('Network response was not ok');
          }
        } catch (error) {
          newsletterMsg.classList.remove('hidden', 'text-green-600');
          newsletterMsg.classList.add('text-red-600');
          newsletterMsg.textContent = translations[currentLanguage].newsletter_error;
        } finally {
          newsletterBtn.disabled = false;
          newsletterBtn.textContent = translations[currentLanguage].subscribe;
          newsletterMsg.classList.remove('hidden');
          setTimeout(() => newsletterMsg.classList.add('hidden'), 5000);
        }
      });
    }

    // Auto-hide success banner amélioré
    const successBanner = document.getElementById('success-banner');
    if (successBanner) {
      setTimeout(() => {
        successBanner.style.opacity = '0';
        successBanner.style.transform = 'translateX(100%)';
        setTimeout(() => successBanner.remove(), 500);
      }, 5000);
    }

    // Effet parallax léger sur le hero (optimisé pour mobile)
    let ticking = false;
    
    function updateParallax() {
      const scrolled = window.pageYOffset;
      const parallax = document.querySelector('section .absolute img');
      const rate = scrolled * -0.3;
      
      if (parallax && window.innerWidth > 768) { // Seulement sur desktop
        parallax.style.transform = `translateY(${rate}px)`;
      }
      
      ticking = false;
    }

    function requestTick() {
      if (!ticking) {
        requestAnimationFrame(updateParallax);
        ticking = true;
      }
    }

    window.addEventListener('scroll', requestTick);

    // Amélioration performance : lazy loading des images
    if ('IntersectionObserver' in window) {
      const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            const img = entry.target;
            img.src = img.dataset.src || img.src;
            img.classList.remove('lazy');
            imageObserver.unobserve(img);
          }
        });
      });

      document.querySelectorAll('img[data-src]').forEach(img => {
        imageObserver.observe(img);
      });
    }

    // Gestion du focus pour l'accessibilité
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') {
        // Fermer le menu mobile avec Escape
        if (menu && !menu.classList.contains('hidden')) {
          menu.classList.add('hidden');
          toggle.focus();
        }
      }
    });

    // Gestion des erreurs globales
    window.addEventListener('error', (e) => {
      console.error('Erreur capturée:', e.error);
    });

    // Performance monitoring
    if ('PerformanceObserver' in window) {
      const observer = new PerformanceObserver((list) => {
        list.getEntries().forEach((entry) => {
          if (entry.entryType === 'largest-contentful-paint') {
            console.log('LCP:', entry.startTime);
          }
        });
      });
      
      observer.observe({type: 'largest-contentful-paint', buffered: true});
    }
  </script>
</body>
</html>