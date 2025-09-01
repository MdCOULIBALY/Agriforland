<?php
// Démarrer la session pour le token CSRF
session_start();

// Générer un token CSRF si non existant
if (empty($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Charger les données des hackathons depuis le fichier JSON
$hackathons_file = 'data/hackathons.json';
if (!file_exists($hackathons_file)) {
  header('Location: hackathons.php?error=data_not_found');
  exit;
}

$hackathons_data = file_get_contents($hackathons_file);
$hackathons_json = json_decode($hackathons_data, true);

if (!$hackathons_json || !isset($hackathons_json['hackathons'])) {
  header('Location: hackathons.php?error=invalid_data');
  exit;
}

$hackathons = $hackathons_json['hackathons'];

// Récupérer l'ID du hackathon depuis l'URL
$hackathon_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$hackathon = null;
foreach ($hackathons as $h) {
  if ($h['id'] === $hackathon_id) {
    $hackathon = $h;
    break;
  }
}

// Si aucun hackathon trouvé, rediriger
if (!$hackathon) {
  header('Location: hackathons.php?error=not_found');
  exit;
}

// Vérifier si la date limite est dépassée
// $deadline = new DateTime('2025-07-31T23:59:59Z', new DateTimeZone('GMT'));
$deadline = new DateTime('2025-08-03T23:59:59Z', new DateTimeZone('GMT'));
$now = new DateTime('now', new DateTimeZone('GMT'));
$is_expired = $now > $deadline;

// Calculer le temps restant
$time_remaining = $deadline->diff($now);
$days_remaining = $is_expired ? 0 : $time_remaining->days;
$hours_remaining = $is_expired ? 0 : $time_remaining->h;
$minutes_remaining = $is_expired ? 0 : $time_remaining->i;
$seconds_remaining = $is_expired ? 0 : $time_remaining->s;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title data-i18n="page_title"><?php echo htmlspecialchars($hackathon['title']); ?> - AGRIFORLAND</title>
  
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
    .smart-input {
      transition: all 0.3s ease;
    }
    .smart-input:focus {
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(169, 207, 70, 0.3);
    }
    .smart-input.valid {
      border-color: #4CAF50;
      background-color: #f8fff8;
    }
    .smart-input.invalid {
      border-color: #F44336;
      background-color: #fff8f8;
    }
    .countdown-timer {
      background: linear-gradient(135deg, #a9cf46, #759916);
      color: white;
      padding: 0.75rem 1rem;
      border-radius: 8px;
      text-align: center;
      margin: 1rem 0;
    }
    @media (min-width: 640px) {
      .countdown-timer {
        padding: 1rem 1.5rem;
      }
    }
    .form-progress {
      height: 4px;
      background: #e0e0e0;
      border-radius: 2px;
      overflow: hidden;
      margin-bottom: 1rem;
    }
    .form-progress-bar {
      height: 100%;
      background: linear-gradient(90deg, #a9cf46, #759916);
      transition: width 0.5s ease;
      border-radius: 2px;
    }
    .floating-label {
      position: absolute;
      top: 50%;
      left: 12px;
      transform: translateY(-50%);
      color: #999;
      font-size: 14px;
      pointer-events: none;
      transition: all 0.3s ease;
      background: white;
      padding: 0 4px;
      z-index: 10;
    }
    @media (min-width: 640px) {
      .floating-label {
        font-size: 16px;
      }
    }
    .floating-label.active {
      top: -8px;
      left: 8px;
      font-size: 12px;
      color: #a9cf46;
    }
    .input-wrapper {
      position: relative;
    }
    .step-indicator {
      display: flex;
      justify-content: center;
      margin: 1rem 0;
      gap: 0.5rem;
    }
    .step {
      width: 24px;
      height: 24px;
      border-radius: 50%;
      background: #e0e0e0;
      color: #999;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: bold;
      font-size: 12px;
      transition: all 0.3s ease;
    }
    @media (min-width: 640px) {
      .step {
        width: 30px;
        height: 30px;
        font-size: 14px;
      }
    }
    .step.active {
      background: #a9cf46;
      color: white;
    }
    .step.completed {
      background: #4CAF50;
      color: white;
    }
    
    /* Responsive improvements */
    .hero-section {
      min-height: 40vh;
    }
    @media (min-width: 640px) {
      .hero-section {
        min-height: 50vh;
      }
    }
    @media (min-width: 1024px) {
      .hero-section {
        min-height: 60vh;
      }
    }
    
    .tooltip {
      position: absolute;
      background: #2d3748;
      color: white;
      padding: 0.5rem;
      border-radius: 0.375rem;
      font-size: 0.75rem;
      white-space: nowrap;
      z-index: 50;
      opacity: 0;
      transform: translateY(-100%);
      transition: opacity 0.3s ease;
      bottom: 100%;
      left: 50%;
      transform: translateX(-50%) translateY(-0.5rem);
      margin-bottom: 0.25rem;
    }
    .tooltip::after {
      content: '';
      position: absolute;
      top: 100%;
      left: 50%;
      margin-left: -5px;
      border-width: 5px;
      border-style: solid;
      border-color: #2d3748 transparent transparent transparent;
    }
    .tooltip.show {
      opacity: 1;
    }
    
    @media (max-width: 640px) {
      .tooltip {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: rgba(45, 55, 72, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 0.5rem;
        padding: 1rem;
        max-width: 80vw;
        white-space: normal;
        text-align: center;
      }
      .tooltip::after {
        display: none;
      }
    }
  </style>
</head>

<body class="bg-[#f6ffde] text-black font-roboto">
  <!-- Preloader -->
  <div id="preloader" class="fixed inset-0 bg-[#f6ffde] z-50 flex items-center justify-center">
    <div class="animate-triangle w-16 h-16 sm:w-24 sm:h-24 md:w-32 md:h-32">
      <img src="images/triangle-svgrepo-com.svg" loading="lazy" alt="" data-alt-i18n="loading" class="w-full h-full object-contain triangle-img">
    </div>
  </div>

  <!-- Header -->
  <header class="bg-white shadow-md sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-6 py-2 sm:py-3 flex items-center justify-between">
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
        class="h-6 sm:h-8 md:h-10 lg:h-12"
      >
      
      <!-- Menu Burger pour mobile -->
      <button id="menu-toggle" class="lg:hidden text-gray-700 focus:outline-none p-2" aria-label="" data-aria-i18n="open_menu">
        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
          <path d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
      </button>
      
      <!-- Boutons (desktop) -->
      <div class="hidden lg:flex gap-2 xl:gap-3 items-center ml-auto">
        <div class="relative inline-block text-left">
          <select id="language-selector" class="block appearance-none bg-white border border-gray-300 hover:border-gray-500 px-2 py-1 pr-8 rounded shadow leading-tight focus:outline-none focus:shadow-outline text-sm">
            <option value="fr" data-icon="images/fr.webp">Français</option>
            <option value="en" data-icon="images/en.webp">English</option>
          </select>
          <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2">
            <img id="language-icon" src="images/fr.webp" loading="lazy" alt="" data-alt-i18n="language" class="h-4 w-4">
          </div>
        </div>
        <a href="recrutement.html" class="bg-[#759916] text-white px-3 xl:px-4 py-2 rounded-md hover:text-black hover:bg-[#ade126] transition text-sm whitespace-nowrap" data-i18n="join_us">
          Nous Rejoindre
        </a>
        <a href="contact.html" class="border border-gray-500 px-3 xl:px-4 py-2 rounded-md hover:text-black hover:bg-[#f6ffde] transition text-sm whitespace-nowrap" data-i18n="contact_us">
          Nous Contacter
        </a>
      </div>
    </div>
    
    <!-- Navigation Desktop -->
    <div class="border-t border-gray-100 bg-[#f6ffde] hidden lg:block">
      <nav class="max-w-7xl mx-auto px-4 lg:px-6 py-3 flex justify-center gap-4 lg:gap-6 xl:gap-8 text-base lg:text-lg">
        <a href="index.php" class="nav-link hover:text-[#a9cf46] transition-colors" data-i18n="home">Accueil</a>
        <a href="about.php" class="nav-link hover:text-[#a9cf46] transition-colors" data-i18n="about">À Propos</a>
        <a href="poles.html" class="nav-link hover:text-[#a9cf46] transition-colors" data-i18n="poles">Nos Pôles</a>
        <a href="projets.html" class="nav-link hover:text-[#a9cf46] transition-colors" data-i18n="projects">Nos Projets</a>
        <a href="blog.php" class="nav-link hover:text-[#a9cf46] transition-colors" data-i18n="blog">Blog</a>
        <a href="portfolios.php" class="nav-link hover:text-[#a9cf46] transition-colors" data-i18n="portfolios">Portfolios</a>
        <a href="hackathons.php" class="nav-link text-[#a9cf46] border-b-2 border-[#a9cf46] font-semibold" data-i18n="hackathons">Hackathons</a>
      </nav>
    </div>
    
    <!-- Menu Mobile -->
    <div id="mobile-menu" class="lg:hidden hidden bg-[#f6ffde] px-4 pb-4">
      <nav class="flex flex-col gap-1 text-base">
        <a href="index.php" class="nav-link hover:text-[#a9cf46] transition py-3 border-b border-gray-200" data-i18n="home">Accueil</a>
        <a href="about.php" class="nav-link hover:text-[#a9cf46] transition py-3 border-b border-gray-200" data-i18n="about">À Propos</a>
        <a href="poles.html" class="nav-link hover:text-[#a9cf46] transition py-3 border-b border-gray-200" data-i18n="poles">Nos Pôles</a>
        <a href="projets.html" class="nav-link hover:text-[#a9cf46] transition py-3 border-b border-gray-200" data-i18n="projects">Nos Projets</a>
        <a href="blog.php" class="nav-link hover:text-[#a9cf46] transition py-3 border-b border-gray-200" data-i18n="blog">Blog</a>
        <a href="portfolios.php" class="nav-link hover:text-[#a9cf46] transition py-3 border-b border-gray-200" data-i18n="portfolios">Portfolios</a>
        <a href="hackathons.php" class="nav-link text-[#a9cf46] font-semibold py-3" data-i18n="hackathons">Hackathons</a>
      </nav>
      
      <div class="mt-4 flex flex-col gap-3">
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

  <!-- Hero du Hackathon -->
  <section class="hero-section relative">
    <img 
      src="<?php echo htmlspecialchars($hackathon['image']); ?>" 
      srcset="
        <?php echo htmlspecialchars(str_replace('800', '480', $hackathon['image'])); ?> 480w, 
        <?php echo htmlspecialchars($hackathon['image']); ?> 800w, 
        <?php echo htmlspecialchars(str_replace('800', '1200', $hackathon['image'])); ?> 1200w
      "
      sizes="(max-width: 600px) 480px, (max-width: 1000px) 800px, 1200px"
      loading="lazy" 
      alt="" 
      data-alt-i18n="hackathon_image"
      class="w-full h-full object-cover"
    >
    <div class="absolute inset-0 flex items-center justify-center bg-black/60">
      <div class="text-center px-4 sm:px-6 max-w-4xl mx-auto">
        <h1 class="text-white text-2xl sm:text-3xl md:text-4xl lg:text-5xl xl:text-6xl font-bold font-kanit mb-3 sm:mb-4 leading-tight">
          <?php echo htmlspecialchars($hackathon['title']); ?>
        </h1>
        <?php if (!$is_expired) : ?>
          <div class="countdown-timer inline-block">
            <div class="text-xs sm:text-sm font-semibold mb-1" data-i18n="time_remaining">Temps restant</div>
            <div id="countdown" class="text-base sm:text-lg lg:text-xl font-bold">
              <span id="days"><?php echo $days_remaining; ?></span> <span data-i18n="days">jours</span>
              <span id="hours"><?php echo $hours_remaining; ?></span> <span data-i18n="hours">h</span>
              <span id="minutes"><?php echo $minutes_remaining; ?></span> <span data-i18n="minutes">min</span>
              <span id="seconds"><?php echo $seconds_remaining; ?></span> <span data-i18n="seconds">s</span>
            </div>
          </div>
        <?php else : ?>
          <div class="bg-red-600 text-white px-3 sm:px-4 py-2 rounded-md inline-block text-sm sm:text-base">
            <i class="ph ph-clock-countdown mr-2"></i>
            <span data-i18n="registration_expired">Inscriptions fermées</span>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </section>

  <!-- Détails du Hackathon -->
  <section class="bg-[#f1ffcd] py-6 sm:py-8 lg:py-12">
    <div class="max-w-5xl mx-auto px-4 sm:px-6">
      <h2 class="text-xl sm:text-2xl lg:text-3xl font-bold mb-4 sm:mb-6" data-i18n="hackathon_details">Détails du Hackathon</h2>
      <div class="prose prose-sm sm:prose-base max-w-none mb-6 sm:mb-8">
        <p class="text-gray-700 text-sm sm:text-base leading-relaxed"><?php echo nl2br(htmlspecialchars($hackathon['description'])); ?></p>
      </div>
      
      <!-- Informations du hackathon -->
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4 lg:gap-6 mb-6 sm:mb-8">
        <div class="bg-white p-3 sm:p-4 lg:p-6 rounded-lg shadow-sm">
          <div class="flex items-center mb-2">
            <i class="ph ph-calendar text-[#4CAF50] text-lg sm:text-xl mr-2"></i>
            <span class="font-semibold text-[#4CAF50] text-sm sm:text-base" data-i18n="date">Date :</span>
          </div>
          <span class="ml-6 text-sm sm:text-base"><?php echo htmlspecialchars($hackathon['date']); ?></span>
        </div>
        <div class="bg-white p-3 sm:p-4 lg:p-6 rounded-lg shadow-sm">
          <div class="flex items-center mb-2">
            <i class="ph ph-map-pin text-[#4CAF50] text-lg sm:text-xl mr-2"></i>
            <span class="font-semibold text-[#4CAF50] text-sm sm:text-base" data-i18n="location">Lieu :</span>
          </div>
          <span class="ml-6 text-sm sm:text-base"><?php echo htmlspecialchars($hackathon['location']); ?></span>
        </div>
        <div class="bg-white p-3 sm:p-4 lg:p-6 rounded-lg shadow-sm">
          <div class="flex items-center mb-2">
            <i class="ph ph-lightbulb text-[#4CAF50] text-lg sm:text-xl mr-2"></i>
            <span class="font-semibold text-[#4CAF50] text-sm sm:text-base" data-i18n="theme">Thème :</span>
          </div>
          <span class="ml-6 text-sm sm:text-base"><?php echo htmlspecialchars($hackathon['theme']); ?></span>
        </div>
        <div class="bg-white p-3 sm:p-4 lg:p-6 rounded-lg shadow-sm">
          <div class="flex items-center mb-2">
            <i class="ph ph-trophy text-[#4CAF50] text-lg sm:text-xl mr-2"></i>
            <span class="font-semibold text-[#4CAF50] text-sm sm:text-base" data-i18n="prizes">Prix :</span>
          </div>
          <span class="ml-6 text-sm sm:text-base"><?php echo htmlspecialchars($hackathon['prizes']); ?></span>
        </div>
      </div>
      
      <!-- Livrables -->
      <div class="bg-white p-4 sm:p-6 lg:p-8 rounded-lg shadow-sm mb-6 sm:mb-8">
        <h3 class="text-base sm:text-lg lg:text-xl font-semibold mb-3 sm:mb-4 flex items-center">
          <i class="ph ph-list-checks text-[#4CAF50] text-lg sm:text-xl mr-2"></i>
          <span data-i18n="deliverables">Livrables attendus</span>
        </h3>
        <ul class="space-y-2 sm:space-y-3 text-sm sm:text-base text-gray-700">
          <?php if ($hackathon['id'] === 1) : ?>
            <li class="flex items-start">
              <i class="ph ph-check-circle text-[#4CAF50] mt-1 mr-2 flex-shrink-0"></i>
              <span data-i18n="deliverable_logo">Un logo pour le Programme Karah.</span>
            </li>
            <li class="flex items-start">
              <i class="ph ph-check-circle text-[#4CAF50] mt-1 mr-2 flex-shrink-0"></i>
              <span data-i18n="deliverable_posters">Trois affiches de recrutement mettant en avant la formation professionnelle et l'impact sur le développement territorial.</span>
            </li>
          <?php else : ?>
            <li class="flex items-start">
              <i class="ph ph-check-circle text-[#4CAF50] mt-1 mr-2 flex-shrink-0"></i>
              <span data-i18n="deliverable_logo_academy">Un logo pour la plateforme Agriforland Academy.</span>
            </li>
            <li class="flex items-start">
              <i class="ph ph-check-circle text-[#4CAF50] mt-1 mr-2 flex-shrink-0"></i>
              <span data-i18n="deliverable_launch_posters">Deux affiches générales pour le lancement de la plateforme.</span>
            </li>
            <li class="flex items-start">
              <i class="ph ph-check-circle text-[#4CAF50] mt-1 mr-2 flex-shrink-0"></i>
              <span data-i18n="deliverable_training_posters">Une affiche par formation : ISO 9001 (Gestion de la qualité), ISO 14001 (Gestion environnementale), ISO 21502 (Gestion de projet), ISO 22000 (Sécurité alimentaire), ISO 45001 (Santé et sécurité).</span>
            </li>
            <li class="flex items-start">
              <i class="ph ph-check-circle text-[#4CAF50] mt-1 mr-2 flex-shrink-0"></i>
              <span data-i18n="deliverable_promo_poster">Une affiche pour une promotion de -50 % sur les formations.</span>
            </li>
          <?php endif; ?>
        </ul>
      </div>

      <!-- Formulaire d'inscription -->
      <div class="bg-white p-4 sm:p-6 lg:p-8 rounded-lg shadow-sm" id="hackathon-form">
        <div class="text-center mb-4 sm:mb-6">
          <h2 class="text-xl sm:text-2xl lg:text-3xl font-bold mb-2" data-i18n="register_title">Inscrivez-vous maintenant</h2>
          <p class="text-[#4CAF50] font-semibold text-sm sm:text-base" data-i18n="register_subtitle">Rejoignez l'innovation !</p>
        </div>
        
        <?php if ($is_expired) : ?>
          <div class="text-center p-4 sm:p-6 bg-red-50 border border-red-200 rounded-lg">
            <i class="ph ph-clock-countdown text-red-600 text-2xl sm:text-3xl mb-2"></i>
            <p class="text-red-600 font-semibold text-sm sm:text-base" data-i18n="registration_closed">Inscriptions fermées. La date limite est dépassée.</p>
          </div>
        <?php else : ?>
          <!-- Indicateur de progression -->
          <div class="form-progress">
            <div class="form-progress-bar" id="progress-bar" style="width: 0%"></div>
          </div>
          
          <!-- Indicateur d'étapes -->
          <div class="step-indicator">
            <div class="step active" id="step-1">1</div>
            <div class="step" id="step-2">2</div>
            <div class="step" id="step-3">3</div>
          </div>
          
          <form id="registration-form" class="space-y-4 sm:space-y-6" action="back/hackathonmail.php" method="POST" novalidate>
            <input type="hidden" name="hackathon_id" value="<?php echo htmlspecialchars($hackathon['id']); ?>">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
            
            <!-- Section 1: Informations personnelles -->
            <div class="form-section" id="section-1">
              <h3 class="text-base sm:text-lg font-semibold mb-3 sm:mb-4 flex items-center">
                <i class="ph ph-user text-[#4CAF50] mr-2"></i>
                <span data-i18n="personal_info">Informations personnelles</span>
              </h3>
              <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
                <div class="input-wrapper">
                  <input 
                    type="text" 
                    name="nom" 
                    id="nom" 
                    class="smart-input px-3 sm:px-4 py-3 sm:py-4 w-full rounded-lg border-2 border-gray-300 focus:border-[#a9cf46] focus:outline-none text-sm sm:text-base transition-all" 
                    required 
                    aria-required="true"
                    aria-describedby="nom-error"
                    pattern="[A-Za-zÀ-ÖØ-öø-ÿ\s\-]{2,50}"
                    data-validate="name"
                  >
                  <label for="nom" class="floating-label" data-i18n="last_name">Nom</label>
                  <div id="nom-error" class="text-red-600 text-xs sm:text-sm mt-1 hidden flex items-center">
                    <i class="ph ph-warning-circle mr-1"></i>
                    <span data-i18n="invalid_name">Le nom doit contenir uniquement des lettres, espaces ou tirets.</span>
                  </div>
                  <div class="text-green-600 text-xs sm:text-sm mt-1 hidden" id="nom-success">
                    <i class="ph ph-check-circle mr-1"></i>
                    <span data-i18n="valid_name">Nom valide</span>
                  </div>
                </div>
                
                <div class="input-wrapper">
                  <input 
                    type="text" 
                    name="prenom" 
                    id="prenom" 
                    class="smart-input px-3 sm:px-4 py-3 sm:py-4 w-full rounded-lg border-2 border-gray-300 focus:border-[#a9cf46] focus:outline-none text-sm sm:text-base transition-all" 
                    required 
                    aria-required="true"
                    aria-describedby="prenom-error"
                    pattern="[A-Za-zÀ-ÖØ-öø-ÿ\s\-]{2,50}"
                    data-validate="name"
                  >
                  <label for="prenom" class="floating-label" data-i18n="first_name">Prénoms</label>
                  <div id="prenom-error" class="text-red-600 text-xs sm:text-sm mt-1 hidden flex items-center">
                    <i class="ph ph-warning-circle mr-1"></i>
                    <span data-i18n="invalid_first_name">Le prénom doit contenir uniquement des lettres, espaces ou tirets.</span>
                  </div>
                  <div class="text-green-600 text-xs sm:text-sm mt-1 hidden" id="prenom-success">
                    <i class="ph ph-check-circle mr-1"></i>
                    <span data-i18n="valid_first_name">Prénom valide</span>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Section 2: Contact -->
            <div class="form-section" id="section-2">
              <h3 class="text-base sm:text-lg font-semibold mb-3 sm:mb-4 flex items-center">
                <i class="ph ph-envelope text-[#4CAF50] mr-2"></i>
                <span data-i18n="contact_info">Informations de contact</span>
              </h3>
              <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
                <div class="input-wrapper">
                  <input 
                    type="email" 
                    name="email" 
                    id="email" 
                    class="smart-input px-3 sm:px-4 py-3 sm:py-4 w-full rounded-lg border-2 border-gray-300 focus:border-[#a9cf46] focus:outline-none text-sm sm:text-base transition-all" 
                    required 
                    aria-required="true"
                    aria-describedby="email-error"
                    data-validate="email"
                  >
                  <label for="email" class="floating-label" data-i18n="email">Adresse email</label>
                  <div id="email-error" class="text-red-600 text-xs sm:text-sm mt-1 hidden flex items-center">
                    <i class="ph ph-warning-circle mr-1"></i>
                    <span data-i18n="invalid_email">Veuillez entrer une adresse e-mail valide.</span>
                  </div>
                  <div class="text-green-600 text-xs sm:text-sm mt-1 hidden" id="email-success">
                    <i class="ph ph-check-circle mr-1"></i>
                    <span data-i18n="valid_email">Email valide</span>
                  </div>
                </div>
                
                <div class="input-wrapper">
                  <input 
                    type="tel" 
                    name="telephone" 
                    id="telephone" 
                    class="smart-input px-3 sm:px-4 py-3 sm:py-4 w-full rounded-lg border-2 border-gray-300 focus:border-[#a9cf46] focus:outline-none text-sm sm:text-base transition-all pr-12" 
                    required 
                    aria-required="true"
                    aria-describedby="telephone-error telephone-help"
                    pattern="[\+]?[0-9\s\-\(\)]{8,20}"
                    data-validate="phone"
                    placeholder="+225 07 12 34 56 78"
                  >
                  <label for="telephone" class="floating-label" data-i18n="phone">Numéro de téléphone</label>
                  <button type="button" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-[#4CAF50] text-lg cursor-help p-1 z-20" data-tooltip="phone_format">
                    <i class="ph ph-info"></i>
                  </button>
                  <div id="telephone-error" class="text-red-600 text-xs sm:text-sm mt-1 hidden flex items-center">
                    <i class="ph ph-warning-circle mr-1"></i>
                    <span data-i18n="invalid_phone">Numéro invalide. Utilisez uniquement des chiffres (min. 8).</span>
                  </div>
                  <div class="text-green-600 text-xs sm:text-sm mt-1 hidden" id="telephone-success">
                    <i class="ph ph-check-circle mr-1"></i>
                    <span data-i18n="valid_phone">Numéro valide</span>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Section 3: Portfolio -->
            <div class="form-section" id="section-3">
              <h3 class="text-base sm:text-lg font-semibold mb-3 sm:mb-4 flex items-center">
                <i class="ph ph-globe text-[#4CAF50] mr-2"></i>
                <span data-i18n="portfolio_info">Portfolio</span>
              </h3>
              <div class="input-wrapper">
                <input 
                  type="url" 
                  name="portfolio" 
                  id="portfolio" 
                  class="smart-input px-3 sm:px-4 py-3 sm:py-4 w-full rounded-lg border-2 border-gray-300 focus:border-[#a9cf46] focus:outline-none text-sm sm:text-base transition-all" 
                  required 
                  aria-required="true"
                  aria-describedby="portfolio-error"
                  pattern="https?://.+"
                  data-validate="url"
                  placeholder="https://monportfolio.com"
                >
                <label for="portfolio" class="floating-label" data-i18n="portfolio">Lien du portfolio</label>
                <div id="portfolio-error" class="text-red-600 text-xs sm:text-sm mt-1 hidden flex items-center">
                  <i class="ph ph-warning-circle mr-1"></i>
                  <span data-i18n="invalid_portfolio">Veuillez entrer une URL valide.</span>
                </div>
                <div class="text-green-600 text-xs sm:text-sm mt-1 hidden" id="portfolio-success">
                  <i class="ph ph-check-circle mr-1"></i>
                  <span data-i18n="valid_portfolio">URL valide</span>
                </div>
              </div>
            </div>
            
            <!-- Message de statut -->
            <div id="form-status" class="hidden p-3 sm:p-4 rounded-lg">
              <p id="registration-message" class="font-semibold text-sm sm:text-base text-center flex items-center justify-center">
                <i id="status-icon" class="mr-2 text-lg"></i>
                <span id="status-text"></span>
              </p>
            </div>
            
            <!-- Bouton d'envoi -->
            <div class="text-center pt-2 sm:pt-4">
              <button 
                type="submit" 
                id="submit-btn" 
                class="bg-[#a9cf46] hover:bg-[#93bc3d] disabled:bg-gray-400 disabled:cursor-not-allowed text-black px-6 sm:px-8 py-3 sm:py-4 rounded-lg font-semibold transition-all duration-200 text-sm sm:text-base min-w-[140px] sm:min-w-[160px] transform hover:scale-105 flex items-center justify-center mx-auto" 
                data-i18n="register"
              >
                <i class="ph ph-paper-plane-tilt mr-2"></i>
                <span>S'inscrire</span>
              </button>
            </div>
          </form>
        <?php endif; ?>
      </div>
    </div>
  </section>

  <!-- Footer -->
    <?php include __DIR__ . '/footer.php'; ?>


  <!-- Tooltip element -->
  <div id="tooltip" class="tooltip"></div>

  <!-- Scripts -->
  <script>
    // Language translations (version complète et étendue)
    const translations = {
      fr: {
        page_title: "<?php echo htmlspecialchars($hackathon['title']); ?> - AGRIFORLAND",
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
        time_remaining: "Temps restant",
        days: "jours",
        hours: "h",
        minutes: "min",
        seconds: "s",
        registration_expired: "Inscriptions fermées",
        hackathon_details: "Détails du Hackathon",
        hackathon_image: "Image du hackathon",
        date: "Date",
        location: "Lieu",
        theme: "Thème",
        prizes: "Prix",
        deliverables: "Livrables attendus",
        deliverable_logo: "Un logo pour le Programme Karah",
        deliverable_posters: "Trois affiches de recrutement mettant en avant la formation professionnelle et l'impact sur le développement territorial",
        deliverable_logo_academy: "Un logo pour la plateforme Agriforland Academy",
        deliverable_launch_posters: "Deux affiches générales pour le lancement de la plateforme",
        deliverable_training_posters: "Une affiche par formation : ISO 9001 (Gestion de la qualité), ISO 14001 (Gestion environnementale), ISO 21502 (Gestion de projet), ISO 22000 (Sécurité alimentaire), ISO 45001 (Santé et sécurité)",
        deliverable_promo_poster: "Une affiche pour une promotion de -50 % sur les formations",
        register_title: "Inscrivez-vous maintenant",
        register_subtitle: "Rejoignez l'innovation !",
        personal_info: "Informations personnelles",
        contact_info: "Informations de contact",
        portfolio_info: "Portfolio",
        last_name: "Nom",
        first_name: "Prénoms",
        email: "Adresse email",
        phone: "Numéro de téléphone",
        portfolio: "Lien du portfolio",
        register: "S'inscrire",
        registering: "Inscription en cours...",
        registration_success: "Inscription réussie ! Vous recevrez une confirmation par e-mail.",
        registration_error: "Erreur lors de l'inscription. Veuillez réessayer.",
        registration_closed: "Inscriptions fermées. La date limite est dépassée.",
        invalid_name: "Le nom doit contenir uniquement des lettres, espaces ou tirets (2-50 caractères).",
        invalid_first_name: "Le prénom doit contenir uniquement des lettres, espaces ou tirets (2-50 caractères).",
        invalid_email: "Veuillez entrer une adresse e-mail valide.",
        invalid_phone: "Numéro invalide. Utilisez uniquement des chiffres (min. 8).",
        invalid_portfolio: "Veuillez entrer une URL valide (ex. : https://monportfolio.com).",
        valid_name: "Nom valide",
        valid_first_name: "Prénom valide",
        valid_email: "Email valide",
        valid_phone: "Numéro valide",
        valid_portfolio: "URL valide",
        phone_format: "Accepte tous les formats internationaux. Minimum 8 chiffres, pas de lettres.",
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
        page_title: "<?php echo htmlspecialchars($hackathon['title']); ?> - AGRIFORLAND",
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
        time_remaining: "Time remaining",
        days: "days",
        hours: "h",
        minutes: "min",
        seconds: "s",
        registration_expired: "Registrations closed",
        hackathon_details: "Hackathon Details",
        hackathon_image: "Hackathon image",
        date: "Date",
        location: "Location",
        theme: "Theme",
        prizes: "Prizes",
        deliverables: "Expected Deliverables",
        deliverable_logo: "A logo for the Karah Program",
        deliverable_posters: "Three recruitment posters highlighting professional training and territorial development impact",
        deliverable_logo_academy: "A logo for the Agriforland Academy platform",
        deliverable_launch_posters: "Two general posters for the platform launch",
        deliverable_training_posters: "One poster per training: ISO 9001 (Quality Management), ISO 14001 (Environmental Management), ISO 21502 (Project Management), ISO 22000 (Food Safety), ISO 45001 (Health and Safety)",
        deliverable_promo_poster: "One poster for a 50% discount promotion on trainings",
        register_title: "Register Now",
        register_subtitle: "Join the innovation!",
        personal_info: "Personal Information",
        contact_info: "Contact Information",
        portfolio_info: "Portfolio",
        last_name: "Last Name",
        first_name: "First Name",
        email: "Email Address",
        phone: "Phone Number",
        portfolio: "Portfolio Link",
        register: "Register",
        registering: "Registering...",
        registration_success: "Registration successful! You will receive a confirmation email.",
        registration_error: "Error during registration. Please try again.",
        registration_closed: "Registrations closed. The deadline has passed.",
        invalid_name: "The last name must contain only letters, spaces, or hyphens (2-50 characters).",
        invalid_first_name: "The first name must contain only letters, spaces, or hyphens (2-50 characters).",
        invalid_email: "Please enter a valid email address.",
        invalid_phone: "Invalid number. Use only digits (min. 8).",
        invalid_portfolio: "Please enter a valid URL (e.g., https://myportfolio.com).",
        valid_name: "Valid name",
        valid_first_name: "Valid first name",
        valid_email: "Valid email",
        valid_phone: "Valid phone number",
        valid_portfolio: "Valid URL",
        phone_format: "Accepts all international formats. Minimum 8 digits, no letters.",
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
    let formProgress = 0;
    let validFields = {};
    const totalFields = 5; // nom, prenom, email, telephone, portfolio

    // Sélecteurs DOM
    const languageSelectors = document.querySelectorAll('#language-selector, #language-selector-mobile');
    const languageIcons = document.querySelectorAll('#language-icon, #language-icon-mobile');
    const form = document.getElementById('registration-form');
    const submitBtn = document.getElementById('submit-btn');
    const formStatus = document.getElementById('form-status');
    const statusIcon = document.getElementById('status-icon');
    const statusText = document.getElementById('status-text');
    const progressBar = document.getElementById('progress-bar');
    const tooltip = document.getElementById('tooltip');

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
      
      // Mettre à jour les labels flottants
      document.querySelectorAll('.floating-label').forEach(label => {
        const key = label.getAttribute('data-i18n');
        if (translations[lang] && translations[lang][key]) {
          label.textContent = translations[lang][key];
        }
      });
      
      document.documentElement.lang = lang;
      languageIcons.forEach(icon => icon.src = `images/${lang}.webp`);
      languageSelectors.forEach(selector => selector.value = lang);
    }

    function updateFormProgress() {
      const validCount = Object.keys(validFields).filter(key => validFields[key]).length;
      formProgress = (validCount / totalFields) * 100;
      
      if (progressBar) {
        progressBar.style.width = formProgress + '%';
      }
      
      // Mise à jour des indicateurs d'étapes
      updateStepIndicators();
    }

    function updateStepIndicators() {
      const steps = document.querySelectorAll('.step');
      const progress = formProgress;
      
      steps.forEach((step, index) => {
        const stepProgress = ((index + 1) / steps.length) * 100;
        step.classList.remove('active', 'completed');
        
        if (progress >= stepProgress) {
          step.classList.add('completed');
        } else if (progress >= stepProgress - (100 / steps.length / 2)) {
          step.classList.add('active');
        }
      });
    }

    function validateField(input) {
      const value = input.value.trim();
      const fieldName = input.name;
      const validateType = input.getAttribute('data-validate');
      const errorElement = document.getElementById(`${input.id}-error`);
      const successElement = document.getElementById(`${input.id}-success`);
      let isValid = false;

      // Réinitialiser l'affichage
      input.classList.remove('valid', 'invalid');
      errorElement?.classList.add('hidden');
      successElement?.classList.add('hidden');

      if (!value) {
        validFields[fieldName] = false;
        updateFormProgress();
        return false;
      }

      switch (validateType) {
        case 'name':
          isValid = /^[A-Za-zÀ-ÖØ-öø-ÿ\s\-]{2,50}$/.test(value);
          break;
        case 'email':
          isValid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
          break;
        case 'phone':
          const cleanedPhone = value.replace(/[\s\-\(\)]/g, '');
          isValid = /^[\+]?[0-9]{8,20}$/.test(cleanedPhone);
          break;
        case 'url':
          isValid = /^https?:\/\/.+/.test(value) && isValidUrl(value);
          break;
        default:
          isValid = value.length > 0;
      }

      if (isValid) {
        input.classList.add('valid');
        successElement?.classList.remove('hidden');
        validFields[fieldName] = true;
      } else {
        input.classList.add('invalid');
        errorElement?.classList.remove('hidden');
        validFields[fieldName] = false;
      }

      updateFormProgress();
      return isValid;
    }

    function isValidUrl(string) {
      try {
        new URL(string);
        return true;
      } catch (_) {
        return false;
      }
    }

    function showFormStatus(type, message, icon) {
      formStatus.classList.remove('hidden', 'bg-green-50', 'bg-red-50', 'border-green-200', 'border-red-200');
      statusIcon.className = `mr-2 text-lg ${icon}`;
      statusText.textContent = message;
      
      if (type === 'success') {
        formStatus.classList.add('bg-green-50', 'border-green-200', 'border');
        statusIcon.classList.add('text-green-600');
        statusText.classList.add('text-green-700');
      } else {
        formStatus.classList.add('bg-red-50', 'border-red-200', 'border');
        statusIcon.classList.add('text-red-600');
        statusText.classList.add('text-red-700');
      }
    }

    function hideFormStatus() {
      setTimeout(() => {
        formStatus.classList.add('hidden');
      }, 5000);
    }

    function showTooltip(element, key) {
      const message = translations[currentLanguage][key];
      if (!message) return;
      
      tooltip.textContent = message;
      tooltip.classList.add('show');
      
      if (window.innerWidth <= 640) {
        // Mobile: tooltip centered
        tooltip.style.position = 'fixed';
        tooltip.style.top = '50%';
        tooltip.style.left = '50%';
        tooltip.style.transform = 'translate(-50%, -50%)';
      } else {
        // Desktop: tooltip près de l'élément
        const rect = element.getBoundingClientRect();
        tooltip.style.position = 'absolute';
        tooltip.style.top = (rect.top - tooltip.offsetHeight - 10) + 'px';
        tooltip.style.left = (rect.left + rect.width / 2 - tooltip.offsetWidth / 2) + 'px';
        tooltip.style.transform = 'none';
      }
    }

    function hideTooltip() {
      tooltip.classList.remove('show');
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

    // Countdown timer
    const countdown = document.getElementById('countdown');
    if (countdown) {
      // const deadline = new Date('2025-07-31T23:59:59Z');
      const deadline = new Date('2025-08-03T23:59:59Z');
      function updateCountdown() {
        const now = new Date();
        const diff = deadline - now;
        
        if (diff <= 0) {
          countdown.innerHTML = '<span class="text-red-400">Expiré</span>';
          return;
        }
        
        const days = Math.floor(diff / (1000 * 60 * 60 * 24));
        const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((diff % (1000 * 60)) / 1000);
        
        const daysEl = document.getElementById('days');
        const hoursEl = document.getElementById('hours');
        const minutesEl = document.getElementById('minutes');
        const secondsEl = document.getElementById('seconds');
        
        if (daysEl) daysEl.textContent = days;
        if (hoursEl) hoursEl.textContent = hours;
        if (minutesEl) minutesEl.textContent = minutes;
        if (secondsEl) secondsEl.textContent = seconds;
      }
      
      updateCountdown();
      setInterval(updateCountdown, 1000);
    }

    // Gestion des tooltips
    document.querySelectorAll('[data-tooltip]').forEach(element => {
      element.addEventListener('mouseenter', () => {
        const key = element.getAttribute('data-tooltip');
        showTooltip(element, key);
      });
      
      element.addEventListener('mouseleave', hideTooltip);
      
      element.addEventListener('click', (e) => {
        if (window.innerWidth <= 640) {
          e.preventDefault();
          const key = element.getAttribute('data-tooltip');
          showTooltip(element, key);
          setTimeout(hideTooltip, 3000);
        }
      });
    });

    // Fermer tooltip en cliquant ailleurs
    document.addEventListener('click', (e) => {
      if (!e.target.closest('[data-tooltip]') && !tooltip.contains(e.target)) {
        hideTooltip();
      }
    });

    // Gestion des labels flottants améliorée
    document.querySelectorAll('.smart-input').forEach(input => {
      const label = input.parentElement.querySelector('.floating-label');
      
      function handleLabelFloat() {
        if (input.value || input === document.activeElement) {
          label?.classList.add('active');
        } else {
          label?.classList.remove('active');
        }
      }
      
      input.addEventListener('focus', handleLabelFloat);
      input.addEventListener('blur', handleLabelFloat);
      input.addEventListener('input', handleLabelFloat);
      
      // Vérification initiale
      handleLabelFloat();
    });

    // Validation en temps réel du formulaire
    if (form) {
      const inputs = form.querySelectorAll('.smart-input');
      
      inputs.forEach(input => {
        // Validation en temps réel
        input.addEventListener('input', () => {
          if (input.value.length > 0) {
            validateField(input);
          }
        });
        
        // Validation à la perte de focus
        input.addEventListener('blur', () => {
          validateField(input);
        });
        
        // Filtrage des caractères pour le téléphone
        if (input.name === 'telephone') {
          input.addEventListener('input', (e) => {
            let value = e.target.value.replace(/[^0-9\s\-\(\)\+]/g, '');
            e.target.value = value;
          });
        }
        
        // Auto-complétion pour les URLs
        if (input.name === 'portfolio') {
          input.addEventListener('blur', (e) => {
            let value = e.target.value.trim();
            if (value && !value.startsWith('http://') && !value.startsWith('https://')) {
              e.target.value = 'https://' + value;
              validateField(input);
            }
          });
        }
      });

      // Soumission du formulaire
      form.addEventListener('submit', async e => {
        e.preventDefault();

        // Vérifier tous les champs
        let allValid = true;
        inputs.forEach(input => {
          if (!validateField(input)) {
            allValid = false;
          }
        });

        if (!allValid) {
          showFormStatus('error', translations[currentLanguage].registration_error, 'ph ph-warning-circle');
          hideFormStatus();
          
          // Scroll vers le premier champ invalide
          const firstInvalid = form.querySelector('.invalid');
          if (firstInvalid) {
            firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
            firstInvalid.focus();
          }
          return;
        }

        // État de chargement
        submitBtn.disabled = true;
        const originalContent = submitBtn.innerHTML;
        submitBtn.innerHTML = `
          <i class="ph ph-spinner-gap animate-spin mr-2"></i>
          <span>${translations[currentLanguage].registering}</span>
        `;
        
        try {
          const formData = new FormData(form);
          const response = await fetch(form.action, {
            method: 'POST',
            body: formData
          });
          
          const text = await response.text();
          try {
            const result = JSON.parse(text);
            if (result.success) {
              showFormStatus('success', result.message, 'ph ph-check-circle');
              form.reset();
              
              // Réinitialiser les états visuels
              inputs.forEach(input => {
                input.classList.remove('valid', 'invalid');
                const label = input.parentElement.querySelector('.floating-label');
                label?.classList.remove('active');
                const errorElement = document.getElementById(`${input.id}-error`);
                const successElement = document.getElementById(`${input.id}-success`);
                errorElement?.classList.add('hidden');
                successElement?.classList.add('hidden');
              });
              
              validFields = {};
              updateFormProgress();
              
              // Redirection après 3 secondes
              setTimeout(() => {
                window.location.href = 'hackathons.php?success=1';
              }, 3000);
            } else {
              showFormStatus('error', result.message, 'ph ph-warning-circle');
            }
          } catch (jsonError) {
            console.error('Invalid JSON:', text);
            showFormStatus('error', translations[currentLanguage].registration_error, 'ph ph-warning-circle');
          }
        } catch (error) {
          console.error('Erreur:', error);
          showFormStatus('error', translations[currentLanguage].registration_error, 'ph ph-warning-circle');
        } finally {
          submitBtn.disabled = false;
          submitBtn.innerHTML = originalContent;
          hideFormStatus();
        }
      });
    }

    // Newsletter form
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

    // Gestion des paramètres URL
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('success') === '1') {
      showFormStatus('success', translations[currentLanguage].registration_success, 'ph ph-check-circle');
      setTimeout(() => {
        window.history.replaceState({}, '', window.location.pathname);
        hideFormStatus();
      }, 5000);
    }

    // Gestion du focus pour l'accessibilité
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') {
        // Fermer le menu mobile avec Escape
        if (menu && !menu.classList.contains('hidden')) {
          menu.classList.add('hidden');
          toggle.focus();
        }
        // Fermer tooltip avec Escape
        hideTooltip();
      }
    });
  </script>
</body>
</html>