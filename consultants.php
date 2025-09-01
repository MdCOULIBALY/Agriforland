<?php
// Activer le débogage en développement (désactiver en production)
ini_set('display_errors', 0);           // ✅ Sécurisé
ini_set('display_startup_errors', 0);   // ✅ Sécurisé
error_reporting(0);                      // ✅ Sécurisé

// Démarrer la session
session_start();

// Générer un jeton CSRF si non défini
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
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
    <title data-i18n="title">AGRIFORLAND SARL - Recrutement de Consultants</title>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@400;700&family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/@phosphor-icons/web@2.0.3/src/icons.css">
    <link rel="icon" href="images/favicon.ico" type="image/x-icon">
    <link href="css/Style.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        kanit: ['Kanit', 'sans-serif'],
                        roboto: ['Roboto', 'sans-serif'],
                    },
                    screens: {
                        'xs': '475px',
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
    <style>
        /* Améliorations CSS pour mobile */
        .touch-target {
            min-height: 44px;
            min-width: 44px;
        }
        
        .smooth-transform {
            transition: transform 0.3s ease-in-out;
        }
        
        @media (max-width: 640px) {
            .mobile-text-adjust {
                font-size: 16px !important;
                line-height: 1.5 !important;
            }
            
            .mobile-padding {
                padding: 1rem !important;
            }
            
            .mobile-gap {
                gap: 1rem !important;
            }
        }
        
        /* Animation améliorée pour le menu mobile */
        .mobile-menu-enter {
            transform: translateY(-100%);
            opacity: 0;
        }
        
        .mobile-menu-enter-active {
            transform: translateY(0);
            opacity: 1;
            transition: transform 0.3s ease-out, opacity 0.3s ease-out;
        }
        
        /* Optimisation du preloader */
        .preloader-optimized {
            backdrop-filter: blur(4px);
        }
        
        /* Amélioration focus pour accessibilité */
        .focus-visible:focus {
            outline: 2px solid #a9cf46;
            outline-offset: 2px;
        }
    </style>
</head>

<body class="bg-[#f6ffde] text-black">
<!-- Preloader optimisé -->
<div id="preloader" class="fixed inset-0 bg-[#f6ffde] z-50 flex items-center justify-center preloader-optimized">
    <div class="animate-pulse w-24 h-24 sm:w-32 sm:h-32">
        <img src="images/triangle-svgrepo-com.svg" loading="lazy" alt="Icône de chargement" class="w-full h-full object-contain">
    </div>
</div>

<!-- Header amélioré -->
<header class="bg-white shadow-md sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-3 flex items-center justify-between">
        <img 
            src="cache/logo-198x66-800.webp" 
            srcset="
                cache/logo-198x66-480.webp 480w, 
                cache/logo-198x66-800.webp 800w, 
                cache/logo-198x66-1200.webp 1200w
            "
            sizes="(max-width: 600px) 480px, (max-width: 1000px) 800px, 1200px"
            loading="lazy" 
            alt="Logo AGRIFORLAND" 
            class="h-8 sm:h-10"
        />
        <!-- Menu Burger amélioré pour mobile -->
        <button id="menu-toggle" class="md:hidden text-gray-700 focus:outline-none touch-target focus-visible p-2" aria-label="Ouvrir le menu" aria-expanded="false">
            <svg class="w-6 h-6 transition-transform duration-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                <path class="menu-line-1" d="M4 6h16"/>
                <path class="menu-line-2" d="M4 12h16"/>
                <path class="menu-line-3" d="M4 18h16"/>
            </svg>
        </button>
        <!-- Boutons desktop améliorés -->
        <div class="hidden md:flex gap-3 items-center ml-auto">
            <!-- Language Selector -->
            <div class="relative inline-block text-left">
                <select id="language-selector" class="block appearance-none bg-white border border-gray-300 hover:border-gray-500 px-3 py-2 pr-8 rounded shadow leading-tight focus:outline-none focus:shadow-outline focus-visible touch-target">
                    <option value="fr" data-icon="images/fr.webp">Français</option>
                    <option value="en" data-icon="images/en.webp">English</option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2">
                    <img id="language-icon" loading="lazy" src="images/fr.webp" alt="Language" class="h-5 w-5">
                </div>
            </div>
            <a href="recrutement.html" class="bg-[#759916] text-white px-4 py-2 rounded-lg hover:bg-[#ade126] transition-colors text-sm font-semibold focus-visible touch-target" data-i18n="join_us">Nous rejoindre</a>
            <a href="contact.html" class="border border-gray-500 px-4 py-2 rounded-lg hover:bg-[#f6ffde] transition-colors text-sm focus-visible touch-target" data-i18n="contact_us">Nous contacter</a>
        </div>
    </div>

    <!-- Navigation Desktop -->
    <div class="border-t border-gray-100 bg-[#f6ffde] hidden md:block">
        <nav class="max-w-7xl mx-auto px-4 sm:px-6 py-3 flex justify-center gap-6 text-lg">
            <a href="index.php" class="nav-link hover:text-[#a9cf46] transition-colors focus-visible touch-target" data-i18n="home">Accueil</a>
            <a href="about.php" class="nav-link hover:text-[#a9cf46] transition-colors focus-visible touch-target" data-i18n="about">À Propos</a>
            <a href="poles.html" class="nav-link hover:text-[#a9cf46] transition-colors focus-visible touch-target" data-i18n="poles">Nos Pôles</a>
            <a href="projets.html" class="nav-link hover:text-[#a9cf46] transition-colors focus-visible touch-target" data-i18n="projects">Nos Projets</a>
            <a href="blog.php" class="nav-link hover:text-[#a9cf46] transition-colors focus-visible touch-target" data-i18n="blog">Blog</a>
            <a href="portfolios.php" class="nav-link hover:text-[#a9cf46] transition-colors focus-visible touch-target" data-i18n="portfolios">Portfolio</a>
        </nav>
    </div>

    <!-- Menu Mobile amélioré -->
    <div id="mobile-menu" class="md:hidden hidden bg-[#f6ffde] mobile-padding mobile-menu-enter">
        <nav class="flex flex-col mobile-gap text-base">
            <a href="index.php" class="nav-link hover:text-[#a9cf46] transition-colors touch-target py-3 focus-visible" data-i18n="home">Accueil</a>
            <a href="about.php" class="nav-link hover:text-[#a9cf46] transition-colors touch-target py-3 focus-visible" data-i18n="about">À Propos</a>
            <a href="poles.html" class="nav-link hover:text-[#a9cf46] transition-colors touch-target py-3 focus-visible" data-i18n="poles">Nos Pôles</a>
            <a href="projets.html" class="nav-link hover:text-[#a9cf46] transition-colors touch-target py-3 focus-visible" data-i18n="projects">Nos Projets</a>
            <a href="blog.php" class="nav-link hover:text-[#a9cf46] transition-colors touch-target py-3 focus-visible" data-i18n="blog">Blog</a>
            <a href="portfolios.php" class="nav-link hover:text-[#a9cf46] transition-colors touch-target py-3 focus-visible" data-i18n="portfolios">Portfolio</a>
            <a href="consultants.php" class="nav-link hover:text-[#a9cf46] transition-colors touch-target py-3 focus-visible" data-i18n="consultants">Consultants</a>
        </nav>
        <div class="mt-6 flex flex-col gap-3">
            <!-- Language Selector for Mobile -->
            <div class="relative inline-block text-left">
                <select id="language-selector-mobile" class="block appearance-none bg-white border border-gray-300 hover:border-gray-500 px-3 py-3 pr-8 rounded shadow leading-tight focus:outline-none focus:shadow-outline w-full touch-target focus-visible">
                    <option value="fr" data-icon="images/fr.webp">Français</option>
                    <option value="en" data-icon="images/en.webp">English</option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2">
                    <img id="language-icon-mobile" loading="lazy" src="images/fr.webp" alt="Language" class="h-5 w-5">
                </div>
            </div>
            <a href="recrutement.html" class="bg-[#759916] text-white px-4 py-3 rounded-lg text-center font-semibold hover:bg-[#ade126] transition-colors touch-target focus-visible" data-i18n="join_us">Nous rejoindre</a>
            <a href="contact.html" class="border border-gray-500 px-4 py-3 rounded-lg text-center hover:bg-white transition-colors touch-target focus-visible" data-i18n="contact_us">Nous contacter</a>
        </div>
    </div>
</header>

<!-- Bannière améliorée -->
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
        alt="Image de fond pour le recrutement de consultants" 
        class="w-full h-[250px] xs:h-[300px] md:h-[400px] object-cover"
    />
    <div class="absolute inset-0 bg-black/50 flex flex-col justify-center items-center text-center text-white px-4 sm:px-6">
        <h1 class="text-2xl xs:text-3xl sm:text-4xl md:text-5xl font-bold pb-4 sm:pb-6 font-kanit leading-tight" data-i18n="banner_title">Rejoignez nos Consultants Associés</h1>
        <p class="text-base xs:text-lg sm:text-xl font-roboto max-w-2xl leading-relaxed" data-i18n="banner_subtitle">Contribuez à des projets innovants et durables en Côte d'Ivoire</p>
    </div>
</section>

<!-- Section Consultants améliorée -->
<section class="py-8 sm:py-12 px-4 sm:px-6 max-w-7xl mx-auto bg-[#f6ffde]">
    <h2 class="text-2xl xs:text-3xl md:text-4xl font-bold text-center mb-6 sm:mb-8 font-kanit" data-i18n="section_title">Devenez Consultant Associé</h2>
    <div class="bg-white rounded-xl shadow-md p-4 sm:p-6 md:p-8">
        <!-- Section descriptive améliorée -->
        <div class="mb-6 sm:mb-8">
            <h3 class="text-lg xs:text-xl font-bold mb-4 font-kanit text-[#a9cf46]" data-i18n="why_join_title">Pourquoi rejoindre AGRIFORLAND ?</h3>
            <ul class="space-y-3 sm:space-y-4 text-sm xs:text-base font-roboto text-gray-700">
                <li class="flex items-start gap-3">
                    <span class="ph ph-check-circle text-[#a9cf46] text-xl flex-shrink-0 mt-0.5"></span>
                    <span><strong data-i18n="excellence_company_bold">Entreprise ivoirienne d'excellence</strong> : <span data-i18n="excellence_company_text">AGRIFORLAND est spécialisée dans</span> <a class="text-[#a9cf46] focus-visible" href="poles.html" data-i18n="eight_poles">8 pôles</a> <span data-i18n="excellence_company_end">offrant des solutions innovantes et durables adaptées aux besoins de nos clients.</span></span>
                </li>
                <li class="flex items-start gap-3">
                    <span class="ph ph-check-circle text-[#a9cf46] text-xl flex-shrink-0 mt-0.5"></span>
                    <span><strong data-i18n="concrete_impact_bold">Impact concret</strong> : <span data-i18n="concrete_impact_text">Contribuez à des projets qui soutiennent la croissance durable en Côte d'Ivoire et répondent aux enjeux environnementaux et agricoles.</span></span>
                </li>
                <li class="flex items-start gap-3">
                    <span class="ph ph-check-circle text-[#a9cf46] text-xl flex-shrink-0 mt-0.5"></span>
                    <span><strong data-i18n="opportunities_bold">Opportunités pour tous les profils</strong> : <span data-i18n="opportunities_text">Nous recrutons des consultants associés experts (Bac +5/8, 5+ ans d'expérience, chercheurs ou enseignants-chercheurs), juniors (Bac +3/5, 5+ ans d'expérience).</span></span>
                </li>
                <li class="flex items-start gap-3">
                    <span class="ph ph-check-circle text-[#a9cf46] text-xl flex-shrink-0 mt-0.5"></span>
                    <span><strong data-i18n="dynamic_team_bold">Équipe dynamique</strong> : <span data-i18n="dynamic_team_text">Rejoignez une équipe technique passionnée, dédiée à l'excellence et à la qualité de service.</span></span>
                </li>
            </ul>
        </div>
        
        <!-- Formulaire amélioré -->
        <form id="consultant-form" action="back/consultant_submit.php" method="POST" enctype="multipart/form-data" class="grid gap-4 sm:gap-6 lg:grid-cols-2">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
            
            <div class="relative">
                <input type="text" id="name" name="name" required class="peer w-full px-4 py-3 sm:py-4 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#a9cf46] focus:border-[#a9cf46] placeholder-transparent touch-target focus-visible mobile-text-adjust" data-i18n-placeholder="name_placeholder" placeholder="Nom et Prénoms">
                <label for="name" class="absolute left-4 -top-2.5 bg-white px-1 text-sm text-gray-600 peer-placeholder-shown:top-3 sm:peer-placeholder-shown:top-4 peer-placeholder-shown:text-base peer-placeholder-shown:text-gray-400 transition-all" data-i18n="name_label">Nom et Prénoms *</label>
                <span class="ph ph-user absolute right-3 top-3 sm:top-4 text-gray-400"></span>
                <p class="error text-red-600 text-sm mt-1 hidden"></p>
            </div>
            
            <div class="relative">
                <select id="specialty" name="specialty" required class="peer w-full px-4 py-3 sm:py-4 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#a9cf46] focus:border-[#a9cf46] touch-target focus-visible mobile-text-adjust">
                    <option value="" data-i18n="select_specialty">Sélectionnez une spécialité</option>
                    <option value="Production Végétale" data-i18n="plant_production">Production Végétale</option>
                    <option value="Production Animale" data-i18n="animal_production">Production Animale</option>
                    <option value="Défense des Cultures" data-i18n="crop_protection">Défense des Cultures</option>
                    <option value="Agro-Industrie" data-i18n="agro_industry">Agro-Industrie</option>
                    <option value="Agroéconomie" data-i18n="agroeconomics">Agroéconomie</option>
                    <option value="Machinisme Agricole" data-i18n="agricultural_machinery">Machinisme Agricole</option>
                    <option value="Irrigation" data-i18n="irrigation">Irrigation</option>
                    <option value="Agriculture de Précision" data-i18n="precision_agriculture">Agriculture de Précision</option>
                    <option value="Agriculture Biologique" data-i18n="organic_agriculture">Agriculture Biologique</option>
                    <option value="Transformation Alimentaire" data-i18n="food_processing">Transformation Alimentaire</option>
                    <option value="Foresterie" data-i18n="forestry">Foresterie</option>
                    <option value="Changement Climatique" data-i18n="climate_change">Changement Climatique</option>
                    <option value="Environnement" data-i18n="environment">Environnement</option>
                    <option value="Biodiversité/Biomonitoring" data-i18n="biodiversity">Biodiversité/Biomonitoring</option>
                    <option value="Botanique" data-i18n="botany">Botanique</option>
                    <option value="Gestion des Ressources Naturelles" data-i18n="natural_resources">Gestion des Ressources Naturelles</option>
                    <option value="Restauration Écologique" data-i18n="ecological_restoration">Restauration Écologique</option>
                    <option value="Carbone et Crédits Carbone" data-i18n="carbon_credits">Carbone et Crédits Carbone</option>
                    <option value="Statistique/Data Science" data-i18n="data_science">Statistique/Data Science</option>
                    <option value="Informatique" data-i18n="computer_science">Informatique</option>
                    <option value="Géomatique" data-i18n="geomatics">Géomatique</option>
                    <option value="Hydraulique" data-i18n="hydraulics">Hydraulique</option>
                    <option value="Électronique" data-i18n="electronics">Électronique</option>
                    <option value="Électromécanique" data-i18n="electromechanics">Électromécanique</option>
                    <option value="Intelligence Artificielle en Agriculture" data-i18n="ai_agriculture">Intelligence Artificielle en Agriculture</option>
                    <option value="Blockchain pour la Traçabilité" data-i18n="blockchain_traceability">Blockchain pour la Traçabilité</option>
                    <option value="BTP" data-i18n="construction">BTP</option>
                    <option value="Géomètre-Topographe" data-i18n="surveying">Géomètre-Topographe</option>
                    <option value="Électricité" data-i18n="electricity">Électricité</option>
                    <option value="Génie Civil" data-i18n="civil_engineering">Génie Civil</option>
                    <option value="Urbanisme Rural" data-i18n="rural_planning">Urbanisme Rural</option>
                    <option value="Sociologie/Socioanthropologie" data-i18n="sociology">Sociologie/Socioanthropologie</option>
                    <option value="Socioéconomie/Enquête" data-i18n="socioeconomics">Socioéconomie/Enquête</option>
                    <option value="Droit" data-i18n="law">Droit</option>
                    <option value="Foncier" data-i18n="land_tenure">Foncier</option>
                    <option value="Finance" data-i18n="finance">Finance</option>
                    <option value="Entrepreneuriat" data-i18n="entrepreneurship">Entrepreneuriat</option>
                    <option value="Économie Circulaire" data-i18n="circular_economy">Économie Circulaire</option>
                    <option value="Communication" data-i18n="communication">Communication</option>
                    <option value="Graphisme/Infographie/Montage vidéo" data-i18n="graphic_design">Graphisme/Infographie/Montage vidéo</option>
                    <option value="Marketing Digital" data-i18n="digital_marketing">Marketing Digital</option>
                    <option value="Gestion de Contenu" data-i18n="content_management">Gestion de Contenu</option>
                    <option value="Autre" data-i18n="other">Autre</option>
                </select>
                <input type="text" id="specialty-other" name="specialty_other" data-i18n-placeholder="specify_specialty" placeholder="Précisez votre spécialité" class="mt-3 w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#a9cf46] focus:border-[#a9cf46] hidden touch-target focus-visible">
                <p class="error text-red-600 text-sm mt-1 hidden"></p>
            </div>
            
            <div class="relative">
                <select id="degree" name="degree" required class="peer w-full px-4 py-3 sm:py-4 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#a9cf46] focus:border-[#a9cf46] touch-target focus-visible mobile-text-adjust">
                    <option value="" data-i18n="select_degree">Sélectionnez un diplôme</option>
                    <option value="Doctorat (BAC +8)" data-i18n="doctorate">Doctorat (BAC +8)</option>
                    <option value="Master/Ingénieur (BAC +5)" data-i18n="master_engineer">Master/Ingénieur (BAC +5)</option>
                    <option value="Ingénieur des Techniques (BAC +4)" data-i18n="technical_engineer">Maitrise IT (BAC +4)</option>
                    <option value="Licence (BAC +3)" data-i18n="bachelor">Licence (BAC +3)</option>
                </select>
                <p class="error text-red-600 text-sm mt-1 hidden"></p>
            </div>
            
            <div class="relative">
                <input type="text" id="degree-institution" name="degree_institution" required class="peer w-full px-4 py-3 sm:py-4 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#a9cf46] focus:border-[#a9cf46] placeholder-transparent touch-target focus-visible mobile-text-adjust" data-i18n-placeholder="degree_title_placeholder" placeholder="Intitulé du Dernier Diplôme">
                <label for="degree-institution" class="absolute left-4 -top-2.5 bg-white px-1 text-sm text-gray-600 peer-placeholder-shown:top-3 sm:peer-placeholder-shown:top-4 peer-placeholder-shown:text-base peer-placeholder-shown:text-gray-400 transition-all" data-i18n="degree_title_label">Intitulé du Dernier Diplôme *</label>
                <span class="ph ph-graduation-cap absolute right-3 top-3 sm:top-4 text-gray-400"></span>
                <p class="error text-red-600 text-sm mt-1 hidden"></p>
            </div>
            
            <div class="relative">
                <select id="experience" name="experience" required class="peer w-full px-4 py-3 sm:py-4 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#a9cf46] focus:border-[#a9cf46] touch-target focus-visible mobile-text-adjust">
                    <option value="" data-i18n="select_experience">Sélectionnez une expérience</option>
                    <option value="5">5</option>
                    <option value="6">6</option>
                    <option value="7">7</option>
                    <option value="8">8</option>
                    <option value="9">9</option>
                    <option value="10">10</option>
                    <option value="+10" data-i18n="more_than_10">+10 années</option>
                </select>
                <p class="error text-red-600 text-sm mt-1 hidden"></p>
            </div>
            
            <div class="relative">
                <select id="contract-type" name="contract_type" required class="peer w-full px-4 py-3 sm:py-4 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#a9cf46] focus:border-[#a9cf46] touch-target focus-visible mobile-text-adjust">
                    <option value="" data-i18n="select_contract">Sélectionnez un type de contrat</option>
                    <option value="Consultant Associé" data-i18n="associate_consultant">Consultant Associé</option>
                    <option value="Freelance" data-i18n="freelance">Freelance</option>
                    <option value="Temps Partiel" data-i18n="part_time">Temps Partiel</option>
                    <option value="Temps Plein" data-i18n="full_time">Temps Plein</option>
                </select>
                <p class="error text-red-600 text-sm mt-1 hidden"></p>
            </div>
            
            <div class="relative">
                <input type="date" id="availability" name="availability" required class="peer w-full px-4 py-3 sm:py-4 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#a9cf46] focus:border-[#a9cf46] placeholder-transparent touch-target focus-visible">
                <label for="availability" class="absolute left-4 -top-2.5 bg-white px-1 text-sm text-gray-600 peer-placeholder-shown:top-3 sm:peer-placeholder-shown:top-4 peer-placeholder-shown:text-base peer-placeholder-shown:text-gray-400 transition-all" data-i18n="availability_label">Date de Disponibilité *</label>
                <span class="ph ph-calendar absolute right-3 top-3 sm:top-4 text-gray-400"></span>
                <p class="error text-red-600 text-sm mt-1 hidden"></p>
            </div>
            
            <div class="relative">
                <input type="text" id="languages" name="languages" required class="peer w-full px-4 py-3 sm:py-4 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#a9cf46] focus:border-[#a9cf46] placeholder-transparent touch-target focus-visible mobile-text-adjust" data-i18n-placeholder="languages_placeholder" placeholder="Langues parlées">
                <label for="languages" class="absolute left-4 -top-2.5 bg-white px-1 text-sm text-gray-600 peer-placeholder-shown:top-3 sm:peer-placeholder-shown:top-4 peer-placeholder-shown:text-base peer-placeholder-shown:text-gray-400 transition-all" data-i18n="languages_label">Langues parlées (ex. Français, Anglais) *</label>
                <span class="ph ph-translate absolute right-3 top-3 sm:top-4 text-gray-400"></span>
                <p class="error text-red-600 text-sm mt-1 hidden"></p>
            </div>
            
            <div class="relative">
                <input type="file" id="cv" name="cv" accept=".pdf,.doc,.docx" required class="peer w-full px-4 py-3 border border-gray-200 rounded-lg text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-[#a9cf46] file:text-white hover:file:bg-[#759916] touch-target focus-visible">
                <label for="cv" class="absolute left-4 -top-2.5 bg-white px-1 text-sm text-gray-600" data-i18n="cv_label">CV * (PDF, DOC, DOCX, max 10 MB)</label>
                <span class="ph ph-file absolute right-3 top-3 text-gray-400"></span>
                <p class="error text-red-600 text-sm mt-1 hidden"></p>
            </div>
            
            <div class="relative">
                <input type="file" id="diploma" name="diploma" accept=".pdf,.jpg,.png" required class="peer w-full px-4 py-3 border border-gray-200 rounded-lg text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-[#a9cf46] file:text-white hover:file:bg-[#759916] touch-target focus-visible">
                <label for="diploma" class="absolute left-4 -top-2.5 bg-white px-1 text-sm text-gray-600" data-i18n="diploma_label">Copie du Dernier Diplôme * (PDF, JPG, PNG, max 10 MB)</label>
                <span class="ph ph-file absolute right-3 top-3 text-gray-400"></span>
                <p class="error text-red-600 text-sm mt-1 hidden"></p>
            </div>
            
            <div class="relative">
                <input type="tel" id="phone" name="phone" required class="peer w-full px-4 py-3 sm:py-4 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#a9cf46] focus:border-[#a9cf46] placeholder-transparent touch-target focus-visible mobile-text-adjust" data-i18n-placeholder="phone_placeholder" placeholder="+225 123 456 789">
                <label for="phone" class="absolute left-4 -top-2.5 bg-white px-1 text-sm text-gray-600 peer-placeholder-shown:top-3 sm:peer-placeholder-shown:top-4 peer-placeholder-shown:text-base peer-placeholder-shown:text-gray-400 transition-all" data-i18n="phone_label">Numéro de Téléphone *</label>
                <span class="ph ph-phone absolute right-3 top-3 sm:top-4 text-gray-400"></span>
                <p class="error text-red-600 text-sm mt-1 hidden"></p>
            </div>
            
            <div class="relative">
                <input type="email" id="email" name="email" required class="peer w-full px-4 py-3 sm:py-4 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#a9cf46] placeholder-transparent touch-target focus-visible mobile-text-adjust" data-i18n-placeholder="email_placeholder" placeholder="Email">
                <label for="email" class="absolute left-4 -top-2 bg-white px-1 text-sm text-gray-600 peer-placeholder-shown:top-3 sm:peer-placeholder-shown:top-4 peer-placeholder-shown:text-base peer-placeholder-shown:text-gray-400 transition-all" data-i18n="email_label">Email *</label>
                <span class="ph ph-envelope absolute right-3 top-3 sm:top-4 text-gray-400"></span>
                <p class="error text-red-600 text-sm mt-1 hidden"></p>
            </div>
            
            <!-- Case à cocher pour la politique de confidentialité -->
            <div class="col-span-full">
                <label class="flex items-start text-sm font-medium text-gray-700 gap-3">
                    <input type="checkbox" id="accept_conditions" name="accept_conditions" required class="mt-1 flex-shrink-0 w-5 h-5">
                    <span class="text-justify leading-relaxed">
                        <span data-i18n="privacy_policy_text">En envoyant ma candidature, je déclare avoir lu la</span>
                        <a href="politique-confidentialite.php" class="text-[#a9cf46] hover:underline focus-visible" data-i18n="privacy_policy_link">Politique de confidentialité</a> 
                        <span data-i18n="privacy_policy_consent">et je consens à ce que AGRIFORLAND stocke mes données personnelles pour traiter ma candidature. *</span>
                    </span>
                </label>
                <p class="error text-red-600 text-sm mt-1 hidden"></p>
            </div>
            
            <!-- Boutons radio pour donner des formations -->
            <div class="col-span-full">
                <label class="block text-sm font-medium text-gray-700 mb-3" data-i18n="training_question">Êtes-vous disponible pour dispenser des formations ? *</label>
                <div class="flex items-center gap-6">
                    <label class="flex items-center gap-2 touch-target">
                        <input type="radio" name="give_trainings" value="yes" required class="w-5 h-5">
                        <span data-i18n="yes">Oui</span>
                    </label>
                    <label class="flex items-center gap-2 touch-target">
                        <input type="radio" name="give_trainings" value="no" class="w-5 h-5">
                        <span data-i18n="no">Non</span>
                    </label>
                </div>
                <p class="error text-red-600 text-sm mt-1 hidden"></p>
            </div>
            
            <!-- Champ conditionnel pour les modules de formation -->
            <div class="col-span-full relative hidden" id="training-modules-container">
                <textarea id="training_modules" name="training_modules" class="peer w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#a9cf46] focus:border-[#a9cf46] placeholder-transparent touch-target focus-visible mobile-text-adjust" data-i18n-placeholder="training_modules_placeholder" placeholder="Modules de formation" rows="4"></textarea>
                <label for="training_modules" class="absolute left-4 -top-2.5 bg-white px-1 text-sm text-gray-600 peer-placeholder-shown:top-3 peer-placeholder-shown:text-base peer-placeholder-shown:text-gray-400 transition-all" data-i18n="training_modules_label">Modules de formation (ex. Gestion de projet, Agroforesterie) *</label>
                <span class="ph ph-chalkboard-teacher absolute right-3 top-3 text-gray-400"></span>
                <p class="error text-red-600 text-sm mt-1 hidden"></p>
            </div>
            
            <div class="col-span-full text-center mt-4">
                <button type="submit" class="bg-[#a9cf46] px-6 sm:px-8 py-3 sm:py-4 rounded-full text-white font-semibold hover:bg-[#759916] transition-all duration-300 transform hover:scale-105 shadow-lg touch-target focus-visible text-base" data-i18n="submit_application">
                    Soumettre ma candidature
                </button>
            </div>
            <p id="form-message" class="col-span-full text-center text-sm mt-4 hidden"></p>
        </form>
    </div>
</section>

    <?php include __DIR__ . '/footer.php'; ?>


<!-- Scripts optimisés -->
<script defer>
    // Language translations (conservé tel quel)
    const translations = {
        fr: {
            title: "AGRIFORLAND SARL - Recrutement de Consultants",
            join_us: "Nous rejoindre",
            contact_us: "Nous contacter",
            home: "Accueil",
            about: "À Propos",
            poles: "Nos Pôles",
            projects: "Nos Projets",
            blog: "Blog",
            portfolios: "Portfolio",
            consultants: "Consultants",
            banner_title: "Rejoignez nos Consultants Associés",
            banner_subtitle: "Contribuez à des projets innovants et durables en Côte d'Ivoire",
            section_title: "Devenez Consultant Associé",
            why_join_title: "Pourquoi rejoindre AGRIFORLAND ?",
            excellence_company_bold: "Entreprise ivoirienne d'excellence",
            excellence_company_text: "AGRIFORLAND est spécialisée dans",
            eight_poles: "8 pôles",
            excellence_company_end: "offrant des solutions innovantes et durables adaptées aux besoins de nos clients.",
            concrete_impact_bold: "Impact concret",
            concrete_impact_text: "Contribuez à des projets qui soutiennent la croissance durable en Côte d'Ivoire et répondent aux enjeux environnementaux et agricoles.",
            opportunities_bold: "Opportunités pour tous les profils",
            opportunities_text: "Nous recrutons des consultants associés experts (Bac +5/8, 5+ ans d'expérience, chercheurs ou enseignants-chercheurs), juniors (Bac +3/5, 5+ ans d'expérience).",
            dynamic_team_bold: "Équipe dynamique",
            dynamic_team_text: "Rejoignez une équipe technique passionnée, dédiée à l'excellence et à la qualité de service.",
            name_placeholder: "Nom et Prénoms",
            name_label: "Nom et Prénoms *",
            select_specialty: "Sélectionnez une spécialité",
            plant_production: "Production Végétale",
            animal_production: "Production Animale",
            crop_protection: "Défense des Cultures",
            agro_industry: "Agro-Industrie",
            agroeconomics: "Agroéconomie",
            agricultural_machinery: "Machinisme Agricole",
            irrigation: "Irrigation",
            precision_agriculture: "Agriculture de Précision",
            organic_agriculture: "Agriculture Biologique",
            food_processing: "Transformation Alimentaire",
            forestry: "Foresterie",
            climate_change: "Changement Climatique",
            environment: "Environnement",
            biodiversity: "Biodiversité/Biomonitoring",
            botany: "Botanique",
            natural_resources: "Gestion des Ressources Naturelles",
            ecological_restoration: "Restauration Écologique",
            carbon_credits: "Carbone et Crédits Carbone",
            data_science: "Statistique/Data Science",
            computer_science: "Informatique",
            geomatics: "Géomatique",
            hydraulics: "Hydraulique",
            electronics: "Électronique",
            electromechanics: "Électromécanique",
            ai_agriculture: "Intelligence Artificielle en Agriculture",
            blockchain_traceability: "Blockchain pour la Traçabilité",
            construction: "BTP",
            surveying: "Géomètre-Topographe",
            electricity: "Électricité",
            civil_engineering: "Génie Civil",
            rural_planning: "Urbanisme Rural",
            sociology: "Sociologie/Socioanthropologie",
            socioeconomics: "Socioéconomie/Enquête",
            law: "Droit",
            land_tenure: "Foncier",
            finance: "Finance",
            entrepreneurship: "Entrepreneuriat",
            circular_economy: "Économie Circulaire",
            communication: "Communication",
            graphic_design: "Graphisme/Infographie/Montage vidéo",
            digital_marketing: "Marketing Digital",
            content_management: "Gestion de Contenu",
            other: "Autre",
            specify_specialty: "Précisez votre spécialité",
            select_degree: "Sélectionnez un diplôme",
            doctorate: "Doctorat (BAC +8)",
            master_engineer: "Master/Ingénieur (BAC +5)",
            technical_engineer: "Maitrise IT (BAC +4)",
            bachelor: "Licence (BAC +3)",
            degree_title_placeholder: "Intitulé du Dernier Diplôme",
            degree_title_label: "Intitulé du Dernier Diplôme *",
            select_experience: "Sélectionnez une expérience",
            more_than_10: "+10 années",
            select_contract: "Sélectionnez un type de contrat",
            associate_consultant: "Consultant Associé",
            freelance: "Freelance",
            part_time: "Temps Partiel",
            full_time: "Temps Plein",
            availability_label: "Date de Disponibilité *",
            languages_placeholder: "Langues parlées",
            languages_label: "Langues parlées (ex. Français, Anglais) *",
            cv_label: "CV * (PDF, DOC, DOCX, max 10 MB)",
            diploma_label: "Copie du Dernier Diplôme * (PDF, JPG, PNG, max 10 MB)",
            phone_placeholder: "+225 123 456 789",
            phone_label: "Numéro de Téléphone *",
            email_placeholder: "Email",
            email_label: "Email *",
            privacy_policy_text: "En envoyant ma candidature, je déclare avoir lu la",
            privacy_policy_link: "Politique de confidentialité",
            privacy_policy_consent: "et je consens à ce que AGRIFORLAND stocke mes données personnelles pour traiter ma candidature. *",
            training_question: "Êtes-vous disponible pour dispenser des formations ? *",
            yes: "Oui",
            no: "Non",
            training_modules_placeholder: "Modules de formation",
            training_modules_label: "Modules de formation (ex. Gestion de projet, Agroforesterie) *",
            submit_application: "Soumettre ma candidature",
            follow_us: "SUIVEZ-NOUS",
            useful_links: "Liens Utiles",
            contact: "Contact",
            recruitment: "Recrutement",
            our_group: "Notre Groupe",
            our_stories: "Nos Histoires",
            our_values: "Nos Valeurs",
            our_missions: "Notre Mission",
            our_teams: "Notre Équipe",
            our_ecofarms: "Notre Écoferme",
            others: "Autres",
            agroforestry: "Agroforesterie",
            mapping: "Cartographie",
            our_partners: "Nos Partenaires",
            newsletter: "Newsletter",
            your_email: "Votre email",
            subscribe: "S'inscrire",
            newsletter_success: "Merci pour votre inscription !",
            copyright: "© 2025 Agriforland. Tous droits réservés.",
            application_success: "Candidature soumise avec succès !",
            application_error: "Erreur lors de la soumission. Veuillez réessayer.",
            field_required: "Ce champ est requis.",
            email_invalid: "Veuillez entrer un email valide.",
            phone_invalid: "Veuillez entrer un numéro de téléphone valide.",
            file_required: "Veuillez uploader un fichier.",
            file_too_large: "Le fichier dépasse 10 Mo.",
            file_invalid_cv: "Format de fichier non autorisé pour le CV (PDF, DOC, DOCX).",
            file_invalid_diploma: "Format de fichier non autorisé pour le diplôme (PDF, JPG, PNG).",
            specify_specialty_required: "Veuillez préciser votre spécialité.",
            privacy_required: "Vous devez accepter la politique de confidentialité.",
            training_modules_required: "Veuillez préciser les modules de formation.",
            form_errors: "Veuillez corriger les erreurs dans le formulaire.",
            sending: "Envoi en cours...",
            newsletter_error: "Erreur lors de l'inscription."
        },
        en: {
            title: "AGRIFORLAND SARL - Consultant Recruitment",
            join_us: "Join Us",
            contact_us: "Contact Us",
            home: "Home",
            about: "About",
            poles: "Our Divisions",
            projects: "Our Projects",
            blog: "Blog",
            portfolios: "Portfolio",
            consultants: "Consultants",
            banner_title: "Join Our Associate Consultants",
            banner_subtitle: "Contribute to innovative and sustainable projects in Côte d'Ivoire",
            section_title: "Become an Associate Consultant",
            why_join_title: "Why join AGRIFORLAND?",
            excellence_company_bold: "Ivorian company of excellence",
            excellence_company_text: "AGRIFORLAND specializes in",
            eight_poles: "8 divisions",
            excellence_company_end: "offering innovative and sustainable solutions tailored to our clients' needs.",
            concrete_impact_bold: "Concrete impact",
            concrete_impact_text: "Contribute to projects that support sustainable growth in Côte d'Ivoire and address environmental and agricultural challenges.",
            opportunities_bold: "Opportunities for all profiles",
            opportunities_text: "We recruit expert associate consultants (Bachelor +5/8, 5+ years experience, researchers or teacher-researchers), juniors (Bachelor +3/5, 5+ years experience).",
            dynamic_team_bold: "Dynamic team",
            dynamic_team_text: "Join a passionate technical team dedicated to excellence and quality service.",
            name_placeholder: "First and Last Name",
            name_label: "First and Last Name *",
            select_specialty: "Select a specialty",
            plant_production: "Plant Production",
            animal_production: "Animal Production",
            crop_protection: "Crop Protection",
            agro_industry: "Agro-Industry",
            agroeconomics: "Agroeconomics",
            agricultural_machinery: "Agricultural Machinery",
            irrigation: "Irrigation",
            precision_agriculture: "Precision Agriculture",
            organic_agriculture: "Organic Agriculture",
            food_processing: "Food Processing",
            forestry: "Forestry",
            climate_change: "Climate Change",
            environment: "Environment",
            biodiversity: "Biodiversity/Biomonitoring",
            botany: "Botany",
            natural_resources: "Natural Resources Management",
            ecological_restoration: "Ecological Restoration",
            carbon_credits: "Carbon and Carbon Credits",
            data_science: "Statistics/Data Science",
            computer_science: "Computer Science",
            geomatics: "Geomatics",
            hydraulics: "Hydraulics",
            electronics: "Electronics",
            electromechanics: "Electromechanics",
            ai_agriculture: "Artificial Intelligence in Agriculture",
            blockchain_traceability: "Blockchain for Traceability",
            construction: "Construction",
            surveying: "Surveyor-Topographer",
            electricity: "Electricity",
            civil_engineering: "Civil Engineering",
            rural_planning: "Rural Planning",
            sociology: "Sociology/Socioanthropology",
            socioeconomics: "Socioeconomics/Survey",
            law: "Law",
            land_tenure: "Land Tenure",
            finance: "Finance",
            entrepreneurship: "Entrepreneurship",
            circular_economy: "Circular Economy",
            communication: "Communication",
            graphic_design: "Graphic Design/Computer Graphics/Video Editing",
            digital_marketing: "Digital Marketing",
            content_management: "Content Management",
            other: "Other",
            specify_specialty: "Specify your specialty",
            select_degree: "Select a degree",
            doctorate: "Doctorate (Bachelor +8)",
            master_engineer: "Master/Engineer (Bachelor +5)",
            technical_engineer: "Technical Master (Bachelor +4)",
            bachelor: "Bachelor's (Bachelor +3)",
            degree_title_placeholder: "Latest Degree Title",
            degree_title_label: "Latest Degree Title *",
            select_experience: "Select experience",
            more_than_10: "+10 years",
            select_contract: "Select a contract type",
            associate_consultant: "Associate Consultant",
            freelance: "Freelance",
            part_time: "Part Time",
            full_time: "Full Time",
            availability_label: "Availability Date *",
            languages_placeholder: "Spoken languages",
            languages_label: "Spoken languages (e.g. French, English) *",
            cv_label: "CV * (PDF, DOC, DOCX, max 10 MB)",
            diploma_label: "Copy of Latest Degree * (PDF, JPG, PNG, max 10 MB)",
            phone_placeholder: "+225 123 456 789",
            phone_label: "Phone Number *",
            email_placeholder: "Email",
            email_label: "Email *",
            privacy_policy_text: "By submitting my application, I declare that I have read the",
            privacy_policy_link: "Privacy Policy",
            privacy_policy_consent: "and I consent to AGRIFORLAND storing my personal data to process my application. *",
            training_question: "Are you available to provide training? *",
            yes: "Yes",
            no: "No",
            training_modules_placeholder: "Training modules",
            training_modules_label: "Training modules (e.g. Project Management, Agroforestry) *",
            submit_application: "Submit my application",
            follow_us: "FOLLOW US",
            useful_links: "Useful Links",
            contact: "Contact",
            recruitment: "Recruitment",
            our_group: "Our Group",
            our_stories: "Our Stories",
            our_values: "Our Values",
            our_missions: "Our Mission",
            our_teams: "Our Team",
            our_ecofarms: "Our Ecofarm",
            others: "Others",
            agroforestry: "Agroforestry",
            mapping: "Mapping",
            our_partners: "Our Partners",
            newsletter: "Newsletter",
            your_email: "Your email",
            subscribe: "Subscribe",
            newsletter_success: "Thank you for subscribing!",
            copyright: "© 2025 Agriforland. All rights reserved.",
            application_success: "Application submitted successfully!",
            application_error: "Error during submission. Please try again.",
            field_required: "This field is required.",
            email_invalid: "Please enter a valid email.",
            phone_invalid: "Please enter a valid phone number.",
            file_required: "Please upload a file.",
            file_too_large: "File exceeds 10 MB.",
            file_invalid_cv: "File format not allowed for CV (PDF, DOC, DOCX).",
            file_invalid_diploma: "File format not allowed for diploma (PDF, JPG, PNG).",
            specify_specialty_required: "Please specify your specialty.",
            privacy_required: "You must accept the privacy policy.",
            training_modules_required: "Please specify the training modules.",
            form_errors: "Please correct the errors in the form.",
            sending: "Sending...",
            newsletter_error: "Error during subscription."
        }
    };

    // Fonctions utilitaires
    const debounce = (func, wait) => {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    };

    // Améliorations du sélecteur de langue
    const languageSelectors = document.querySelectorAll('#language-selector, #language-selector-mobile');
    const languageIcons = document.querySelectorAll('#language-icon, #language-icon-mobile');

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
        document.documentElement.lang = lang;
        languageIcons.forEach(icon => icon.src = `images/${lang}.webp`);
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

    // Gestion optimisée du preloader
    const hidePreloader = () => {
        const preloader = document.getElementById('preloader');
        if (preloader) {
            preloader.style.opacity = '0';
            preloader.style.pointerEvents = 'none';
            setTimeout(() => preloader.remove(), 300);
        }
    };

    if (document.readyState === 'loading') {
        window.addEventListener('load', hidePreloader);
    } else {
        hidePreloader();
    }

    // Améliorations du menu mobile avec animations
    const toggle = document.getElementById('menu-toggle');
    const menu = document.getElementById('mobile-menu');
    let isMenuOpen = false;

    const toggleMenu = () => {
        isMenuOpen = !isMenuOpen;
        
        if (isMenuOpen) {
            menu.classList.remove('hidden');
            menu.classList.add('mobile-menu-enter-active');
            toggle.setAttribute('aria-expanded', 'true');
            // Animation du bouton burger
            toggle.querySelector('.menu-line-1').style.transform = 'rotate(45deg) translate(6px, 6px)';
            toggle.querySelector('.menu-line-2').style.opacity = '0';
            toggle.querySelector('.menu-line-3').style.transform = 'rotate(-45deg) translate(6px, -6px)';
        } else {
            menu.classList.remove('mobile-menu-enter-active');
            toggle.setAttribute('aria-expanded', 'false');
            // Reset animation du bouton burger
            toggle.querySelector('.menu-line-1').style.transform = '';
            toggle.querySelector('.menu-line-2').style.opacity = '';
            toggle.querySelector('.menu-line-3').style.transform = '';
            setTimeout(() => menu.classList.add('hidden'), 300);
        }
    };

    toggle.addEventListener('click', toggleMenu);

    // Fermer le menu mobile lors du scroll
    let lastScrollY = window.scrollY;
    const handleScroll = debounce(() => {
        if (isMenuOpen && window.scrollY > lastScrollY + 100) {
            toggleMenu();
        }
        lastScrollY = window.scrollY;
    }, 100);

    window.addEventListener('scroll', handleScroll, { passive: true });

    // Fermer le menu quand on clique sur un lien
    menu.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', () => {
            if (isMenuOpen) toggleMenu();
        });
    });

    // Ajout de la classe active pour la page courante
    const currentPage = window.location.pathname.split('/').pop();
    document.querySelectorAll('.nav-link').forEach(link => {
        if (link.getAttribute('href') === currentPage) {
            link.classList.add('text-[#a9cf46]', 'border-b-2', 'border-[#a9cf46]', 'font-semibold');
        }
    });

    // Gestion améliorée du formulaire newsletter
    const newsletterForm = document.getElementById('newsletter-form');
    const newsletterMsg = document.getElementById('newsletter-msg');
    
    newsletterForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const submitBtn = newsletterForm.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;
        
        try {
            submitBtn.disabled = true;
            submitBtn.textContent = 'Envoi...';
            
            const formData = new FormData(newsletterForm);
            const response = await fetch('back/newsletter.php', { 
                method: 'POST', 
                body: formData 
            });
            
            if (!response.ok) throw new Error('Erreur réseau');
            
            newsletterMsg.classList.remove('hidden', 'text-red-600');
            newsletterMsg.classList.add('text-green-600');
            newsletterMsg.textContent = translations[savedLang].newsletter_success;
            newsletterForm.reset();
        } catch (error) {
            newsletterMsg.classList.remove('hidden', 'text-green-600');
            newsletterMsg.classList.add('text-red-600');
            newsletterMsg.textContent = translations[savedLang].newsletter_error;
        } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
        }
    });

    // Gestion améliorée du formulaire consultant
    const consultantForm = document.getElementById('consultant-form');
    const specialtySelect = document.getElementById('specialty');
    const specialtyOther = document.getElementById('specialty-other');
    const formMessage = document.getElementById('form-message');
    const submitButton = consultantForm.querySelector('button[type="submit"]');
    const trainingModulesContainer = document.getElementById('training-modules-container');
    const trainingModulesInput = document.getElementById('training_modules');
    const giveTrainingsRadios = document.querySelectorAll('input[name="give_trainings"]');

    // Gestion du champ "Autre" pour la spécialité
    specialtySelect.addEventListener('change', () => {
        const isOther = specialtySelect.value === 'Autre';
        if (isOther) {
            specialtyOther.classList.remove('hidden');
            specialtyOther.focus();
        } else {
            specialtyOther.classList.add('hidden');
        }
        specialtyOther.required = isOther;
    });

    // Gestion des boutons radio pour les formations avec animation
    giveTrainingsRadios.forEach(radio => {
        radio.addEventListener('change', () => {
            const isYes = radio.value === 'yes';
            if (isYes) {
                trainingModulesContainer.classList.remove('hidden');
                setTimeout(() => trainingModulesInput.focus(), 100);
            } else {
                trainingModulesContainer.classList.add('hidden');
            }
            trainingModulesInput.required = isYes;
        });
    });

    // Validation améliorée des fichiers
    const validateFile = (file, field, maxSize, allowedTypes) => {
        const currentLang = savedLang;
        if (!file) return { valid: false, message: translations[currentLang].file_required };
        if (file.size > maxSize) return { valid: false, message: translations[currentLang].file_too_large };
        if (!allowedTypes.includes(file.type)) {
            const message = field === 'cv' ? translations[currentLang].file_invalid_cv : translations[currentLang].file_invalid_diploma;
            return { valid: false, message };
        }
        return { valid: true };
    };

    // Affichage amélioré des erreurs
    const showError = (fieldName, message) => {
        const field = document.getElementById(fieldName.replace('_', '-'));
        if (field) {
            const errorElement = field.closest('.relative').querySelector('.error');
            if (errorElement) {
                errorElement.classList.remove('hidden');
                errorElement.textContent = message;
                field.classList.add('border-red-500');
                field.focus();
            }
        }
    };

    const clearErrors = () => {
        document.querySelectorAll('.error').forEach(error => {
            error.classList.add('hidden');
            error.textContent = '';
        });
        document.querySelectorAll('.border-red-500').forEach(field => {
            field.classList.remove('border-red-500');
        });
        formMessage.classList.add('hidden');
    };

    // Soumission optimisée du formulaire
    consultantForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        let isValid = true;
        const formData = new FormData(consultantForm);
        const maxSize = 10 * 1024 * 1024;
        const allowedCvTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
        const allowedDiplomaTypes = ['application/pdf', 'image/jpeg', 'image/png'];
        const currentLang = savedLang;

        // Vérification de candidature récente
        const candidatureData = JSON.parse(localStorage.getItem('candidatureSoumise') || '{}');
        const expirationMinutes = 10;
        if (candidatureData.timestamp) {
            const now = new Date().getTime();
            const expirationTime = candidatureData.timestamp + (expirationMinutes * 60 * 1000);
            if (now < expirationTime) {
                formMessage.classList.remove('hidden', 'text-green-600');
                formMessage.classList.add('text-red-600');
                formMessage.textContent = `Vous avez déjà soumis une candidature. Réessayez dans ${Math.ceil((expirationTime - now) / (60 * 1000))} minutes.`;
                return;
            } else {
                localStorage.removeItem('candidatureSoumise');
            }
        }

        clearErrors();

        // Nettoyage et validation du téléphone
        const phone = formData.get('phone');
        if (phone) formData.set('phone', phone.replace(/[^0-9+]/g, ''));

        // Validation des champs requis
        const requiredFields = ['name', 'specialty', 'degree', 'degree_institution', 'experience', 'contract_type', 'availability', 'languages', 'phone', 'email', 'give_trainings'];
        requiredFields.forEach(field => {
            if (!formData.get(field)) {
                isValid = false;
                showError(field, translations[currentLang].field_required);
            }
        });

        // Validation de l'email
        const email = formData.get('email');
        if (email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            isValid = false;
            showError('email', translations[currentLang].email_invalid);
        }

        // Validation du téléphone
        const cleanedPhone = formData.get('phone');
        if (cleanedPhone && !/^\+?[1-9]\d{1,14}$/.test(cleanedPhone)) {
            isValid = false;
            showError('phone', translations[currentLang].phone_invalid);
        }

        // Validation des fichiers
        const cvFile = document.getElementById('cv').files[0];
        const diplomaFile = document.getElementById('diploma').files[0];
        const cvValidation = validateFile(cvFile, 'cv', maxSize, allowedCvTypes);
        const diplomaValidation = validateFile(diplomaFile, 'diploma', maxSize, allowedDiplomaTypes);

        if (!cvValidation.valid) {
            isValid = false;
            showError('cv', cvValidation.message);
        }
        if (!diplomaValidation.valid) {
            isValid = false;
            showError('diploma', diplomaValidation.message);
        }

        // Validation de la spécialité "Autre"
        if (specialtySelect.value === 'Autre' && !formData.get('specialty_other')) {
            isValid = false;
            showError('specialty', translations[currentLang].specify_specialty_required);
        }

        // Validation de la politique de confidentialité
        if (!formData.get('accept_conditions')) {
            isValid = false;
            showError('accept_conditions', translations[currentLang].privacy_required);
        }

        // Validation des modules de formation
        const giveTrainings = formData.get('give_trainings');
        if (giveTrainings === 'yes' && !formData.get('training_modules')) {
            isValid = false;
            showError('training_modules', translations[currentLang].training_modules_required);
        }

        if (!isValid) {
            formMessage.classList.remove('hidden', 'text-green-600');
            formMessage.classList.add('text-red-600');
            formMessage.textContent = translations[currentLang].form_errors;
            return;
        }

        // Interface de chargement améliorée
        const originalText = submitButton.textContent;
        submitButton.disabled = true;
        submitButton.innerHTML = `
            <svg class="animate-spin h-5 w-5 mr-2 inline-block" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none" opacity="0.25"/>
                <path fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
            </svg>
            ${translations[currentLang].sending}
        `;

        try {
            const response = await fetch('back/consultant_submit.php', {
                method: 'POST',
                body: formData,
            });
            
            const rawResponse = await response.text();
            const result = JSON.parse(rawResponse);
            
            if (result.success) {
                formMessage.classList.remove('hidden', 'text-red-600');
                formMessage.classList.add('text-green-600');
                formMessage.textContent = result.message || translations[currentLang].application_success;
                
                // Reset complet du formulaire
                consultantForm.reset();
                specialtyOther.classList.add('hidden');
                trainingModulesContainer.classList.add('hidden');
                giveTrainingsRadios.forEach(radio => radio.checked = false);
                
                // Stockage de la candidature soumise
                localStorage.setItem('candidatureSoumise', JSON.stringify({
                    submitted: true,
                    timestamp: new Date().getTime()
                }));
                
                // Scroll vers le message
                formMessage.scrollIntoView({ behavior: 'smooth', block: 'center' });
            } else {
                formMessage.classList.remove('hidden', 'text-green-600');
                formMessage.classList.add('text-red-600');
                const errorMessage = result.errors?.join(', ') || result.message || translations[currentLang].application_error;
                formMessage.innerHTML = errorMessage;
                
                if (result.errors?.some(err => err.includes('email existe déjà') || err.includes('numéro de téléphone existe déjà'))) {
                    formMessage.innerHTML += ' <a href="mailto:support@agriforland.com" class="underline text-blue-600 hover:text-blue-800 focus-visible">Contactez le support</a> pour modifier votre candidature.';
                }
            }
        } catch (error) {
            console.error('Erreur de soumission :', error);
            formMessage.classList.remove('hidden', 'text-green-600');
            formMessage.classList.add('text-red-600');
            formMessage.textContent = translations[currentLang].application_error;
        } finally {
            submitButton.disabled = false;
            submitButton.textContent = originalText;
        }
    });

    // Amélioration de l'accessibilité au clavier
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && isMenuOpen) {
            toggleMenu();
        }
    });

    // Lazy loading amélioré pour les images
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    if (img.dataset.src) {
                        img.src = img.dataset.src;
                        img.removeAttribute('data-src');
                    }
                    observer.unobserve(img);
                }
            });
        });

        document.querySelectorAll('img[data-src]').forEach(img => {
            imageObserver.observe(img);
        });
    }
</script>
</body>
</html>