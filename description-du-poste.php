<?php
session_start(); // Démarrer la session pour stocker les données du formulaire en cas d'erreur

// Fonction pour détecter la langue courante
function getCurrentLanguage() {
    // Vérifier dans l'URL d'abord
    if (isset($_GET['lang']) && in_array($_GET['lang'], ['fr', 'en'])) {
        return $_GET['lang'];
    }
    // Sinon, français par défaut
    return 'fr';
}

// Fonction pour obtenir le contenu dans la bonne langue
function getLocalizedContent($data, $field, $lang = 'fr') {
    $key = $field . '_' . $lang;
    return isset($data[$key]) ? $data[$key] : (isset($data[$field]) ? $data[$field] : '');
}

$lang = getCurrentLanguage();

// Charger les données du fichier JSON
$file_path = 'data/recrutement.json';
if (!file_exists($file_path)) {
    http_response_code(500);
    header("Location: error.php?message=" . urlencode("Le fichier des offres d'emploi est manquant."));
    exit;
}

$posts = json_decode(file_get_contents($file_path), true);
if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(500);
    header("Location: error.php?message=" . urlencode("Erreur de lecture du fichier des offres d'emploi : " . json_last_error_msg()));
    exit;
}

// Récupérer et sanitiser le "slug" du poste
$slug = isset($_GET['slug']) ? htmlspecialchars(trim($_GET['slug'])) : '';
if (empty($slug)) {
    http_response_code(404);
    header("Location: error.php?message=" . urlencode("Le poste demandé est introuvable (slug vide)."));
    exit;
}

// Trouver le poste correspondant au "slug"
$selected_post = null;
foreach ($posts as $post) {
    if ($post['slug'] === $slug) {
        $selected_post = $post;
        break;
    }
}

if (!$selected_post) {
    http_response_code(404);
    header("Location: error.php?message=" . urlencode("Poste non trouvé pour le slug '$slug'."));
    exit;
}

// Vérifier si l'offre est résiliée
$is_resiliee = $selected_post['statut'] === 'resiliee';
if (!$is_resiliee && !empty($selected_post['date_resiliation'])) {
    $current_date = new DateTime();
    $resiliation_date = DateTime::createFromFormat('Y-m-d', $selected_post['date_resiliation']);
    if ($resiliation_date && $current_date > $resiliation_date) {
        $is_resiliee = true;
    }
}

// Formater la date de résiliation pour l'affichage
$date_resiliation_formatted = '';
if (!empty($selected_post['date_resiliation'])) {
    $date = DateTime::createFromFormat('Y-m-d', $selected_post['date_resiliation']);
    $date_resiliation_formatted = $date ? $date->format('d/m/Y') : '';
}

// Récupérer les données localisées
$titre = getLocalizedContent($selected_post, 'titre', $lang);
$intro = getLocalizedContent($selected_post, 'intro', $lang);
$avantages = getLocalizedContent($selected_post, 'avantages', $lang);
$missions = getLocalizedContent($selected_post, 'missions', $lang);
$profil = getLocalizedContent($selected_post, 'profil', $lang);

// Récupérer les données du formulaire depuis la session (si erreur précédente)
$form_data = isset($_SESSION['form_data']) ? $_SESSION['form_data'] : [];
$nom = htmlspecialchars($form_data['nom'] ?? '');
$prenom = htmlspecialchars($form_data['prenom'] ?? '');
$email = htmlspecialchars($form_data['email'] ?? '');
$telephone = htmlspecialchars($form_data['telephone'] ?? '');

// Supprimer les données de la session après utilisation
unset($_SESSION['form_data']);
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdn.tailwindcss.com">
    <link rel="preconnect" href="https://unpkg.com">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AGRIFORLAND - <?php echo htmlspecialchars($titre); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@400;700&family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="icon" href="images/favicon.ico" type="image/x-icon">
    <link href="css/Style.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
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
      
      // Transmettre la langue PHP au JavaScript
      window.initialLanguage = '<?php echo $lang; ?>';
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
        
        /* Amélioration du formulaire mobile */
        @media (max-width: 640px) {
            .file-input-mobile {
                padding: 1rem !important;
                font-size: 14px !important;
            }
        }
    </style>
</head>
<body class="bg-[#f6ffde] text-black">
    <!-- Preloader optimisé -->
    <div id="preloader" class="fixed inset-0 bg-[#f6ffde] z-50 flex items-center justify-center preloader-optimized">
        <div class="animate-pulse w-24 h-24 sm:w-32 sm:h-32">
            <img src="images/triangle-svgrepo-com.svg" loading="lazy" alt="Chargement..." class="w-full h-full object-contain">
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
                alt="Logo Agriforland" 
                class="h-8 sm:h-10"
            />
            <button id="menu-toggle" class="md:hidden text-gray-700 focus:outline-none touch-target focus-visible p-2" aria-label="Ouvrir le menu" aria-expanded="false">
                <svg class="w-6 h-6 transition-transform duration-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                    <path class="menu-line-1" d="M4 6h16"/>
                    <path class="menu-line-2" d="M4 12h16"/>
                    <path class="menu-line-3" d="M4 18h16"/>
                </svg>
            </button>
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
                <a href="recrutement.html" class="bg-[#759916] text-white px-4 py-2 rounded-lg hover:bg-[#ade126] transition-colors text-sm font-semibold focus-visible touch-target" data-i18n="join_us">
                    Nous Rejoindre
                </a>
                <a href="contact.html" class="border border-gray-500 px-4 py-2 rounded-lg hover:bg-[#f6ffde] transition-colors text-sm focus-visible touch-target" data-i18n="contact_us">
                    Nous Contacter
                </a>
            </div>
        </div>
        <div class="border-t border-gray-100 bg-[#f6ffde] hidden md:block">
            <nav class="max-w-7xl mx-auto px-4 sm:px-6 py-3 flex justify-center gap-6 text-lg">
                <a href="index.php" class="nav-link hover:text-[#a9cf46] transition-colors focus-visible touch-target" data-i18n="home">Accueil</a>
                <a href="about.php" class="nav-link hover:text-[#a9cf46] transition-colors focus-visible touch-target" data-i18n="about">À Propos</a>
                <a href="poles.html" class="nav-link hover:text-[#a9cf46] transition-colors focus-visible touch-target" data-i18n="poles">Nos Pôles</a>
                <a href="projets.html" class="nav-link hover:text-[#a9cf46] transition-colors focus-visible touch-target" data-i18n="projects">Nos Projets</a>
                <a href="blog.php" class="nav-link hover:text-[#a9cf46] transition-colors focus-visible touch-target" data-i18n="blog">Blog</a>
                <a href="portfolios.php" class="nav-link hover:text-[#a9cf46] transition-colors focus-visible touch-target" data-i18n="portfolios">Portfolios</a>
            </nav>
        </div>
        <div id="mobile-menu" class="md:hidden hidden bg-[#f6ffde] mobile-padding mobile-menu-enter">
            <nav class="flex flex-col mobile-gap text-base">
                <a href="index.php" class="nav-link hover:text-[#a9cf46] transition touch-target py-3 focus-visible" data-i18n="home">Accueil</a>
                <a href="about.php" class="nav-link hover:text-[#a9cf46] transition touch-target py-3 focus-visible" data-i18n="about">À Propos</a>
                <a href="poles.html" class="nav-link hover:text-[#a9cf46] transition touch-target py-3 focus-visible" data-i18n="poles">Nos Pôles</a>
                <a href="projets.html" class="nav-link hover:text-[#a9cf46] transition touch-target py-3 focus-visible" data-i18n="projects">Nos Projets</a>
                <a href="blog.php" class="nav-link hover:text-[#a9cf46] transition touch-target py-3 focus-visible" data-i18n="blog">Blog</a>
                <a href="portfolios.php" class="nav-link hover:text-[#a9cf46] transition touch-target py-3 focus-visible" data-i18n="portfolios">Portfolios</a>
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
                <a href="recrutement.html" class="bg-[#759916] text-white px-4 py-3 rounded-lg text-center font-semibold hover:bg-[#ade126] transition-colors touch-target focus-visible" data-i18n="join_us">Nous Rejoindre</a>
                <a href="contact.html" class="border border-gray-500 px-4 py-3 rounded-lg text-center hover:bg-white transition-colors touch-target focus-visible" data-i18n="contact_us">Nous Contacter</a>
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
            alt="Bannière offre d'emploi" 
            class="w-full h-[250px] xs:h-[300px] md:h-[400px] object-cover"
        />
        <div class="absolute top-0 left-0 w-full h-full bg-black/50 flex flex-col justify-center items-center text-center text-white px-4 sm:px-6">
            <p class="pb-3 sm:pb-4 text-sm xs:text-base" data-i18n="job_offer_label">offre d'emploi/</p>
            <h1 class="text-2xl xs:text-3xl sm:text-4xl md:text-5xl font-bold pb-3 sm:pb-4 leading-tight"><?php echo htmlspecialchars($titre); ?></h1>
            <p class="pb-3 sm:pb-4 text-sm xs:text-base" data-i18n="location">Abidjan, Côte d'Ivoire</p>
        </div>
    </section>

    <!-- Description du poste améliorée -->
    <section class="bg-[#f5fad3] py-8 sm:py-12 px-4 sm:px-6 md:px-8 text-black">
        <div class="max-w-4xl mx-auto">
            <h2 class="text-2xl xs:text-3xl font-bold text-center mb-1" data-i18n="job_description">Description du poste</h2>
            <p class="text-center text-green-600 mb-6 sm:mb-8 font-semibold text-sm xs:text-base" data-i18n="apply_now">Postulez maintenant</p>
            
            <?php if (is_array($avantages) && !empty($avantages)) : ?>
            <ul class="list-disc space-y-2 sm:space-y-3 mb-6 sm:mb-8 px-4 sm:px-6 text-sm xs:text-base">
                <?php foreach ($avantages as $avantage) : ?>
                    <li><strong><?php echo htmlspecialchars($avantage); ?></strong></li>
                <?php endforeach; ?>
            </ul>
            <?php endif; ?>
            
            <h3 class="text-green-700 font-bold text-center mb-3 sm:mb-4 text-lg xs:text-xl" data-i18n="main_missions">Vos principales missions</h3>
            <?php if (is_array($missions) && !empty($missions)) : ?>
            <ul class="list-disc space-y-2 sm:space-y-3 mb-6 sm:mb-8 px-4 sm:px-6 text-sm xs:text-base">
                <?php foreach ($missions as $mission) : ?>
                    <li><strong><?php echo htmlspecialchars($mission); ?></strong></li>
                <?php endforeach; ?>
            </ul>
            <?php endif; ?>
            
            <h3 class="text-green-700 font-bold text-center mb-3 sm:mb-4 text-lg xs:text-xl" data-i18n="profile_sought">Profil recherché</h3>
            <?php if (is_array($profil) && !empty($profil)) : ?>
            <ul class="list-disc space-y-2 sm:space-y-3 mb-6 sm:mb-8 px-4 sm:px-6 text-sm xs:text-base">
                <?php foreach ($profil as $profil_item) : ?>
                    <li><?php echo htmlspecialchars($profil_item); ?></li>
                <?php endforeach; ?>
            </ul>
            <?php endif; ?>
        </div>
    </section>

    <!-- Vérification du statut de l'offre -->
    <?php if ($is_resiliee) : ?>
        <section class="bg-[#fff] py-8 sm:py-12 px-4 sm:px-6 md:px-8 text-black">
            <div class="max-w-4xl mx-auto shadow-lg p-6 sm:p-8 rounded-xl bg-white border border-[#a9cf46] text-center">
                <h3 class="text-lg xs:text-xl font-semibold text-red-600 mb-4" data-i18n="offer_expired">Offre résiliée</h3>
                <p class="text-gray-700 mb-6 text-sm xs:text-base leading-relaxed">
                    <span data-i18n="offer_expired_text">Cette offre d'emploi a été résiliée et n'accepte plus de candidatures.</span>
                    <?php if ($date_resiliation_formatted) : ?>
                        <span data-i18n="expired_since">(Résiliée depuis le</span> <?php echo htmlspecialchars($date_resiliation_formatted); ?>)
                    <?php endif; ?>
                    <span data-i18n="discover_other_jobs">Découvrez nos autres opportunités d'emploi en cliquant ci-dessous.</span>
                </p>
                <a href="recrutement.html" class="inline-block bg-[#a9cf46] hover:bg-[#93bc3d] py-3 px-6 rounded-lg font-semibold transition-colors duration-200 touch-target focus-visible" data-i18n="view_available_offers">
                    Voir les offres disponibles
                </a>
            </div>
        </section>
    <?php else : ?>
        <!-- Formulaire amélioré -->
        <section class="bg-[#fff] py-8 sm:py-12 px-4 sm:px-6 md:px-8 text-black">
            <div class="max-w-4xl mx-auto shadow-lg p-4 sm:p-6 md:p-8 rounded-xl bg-white border border-[#a9cf46]">
                <h3 class="text-center font-semibold text-green-700 text-lg xs:text-xl mb-6" data-i18n="personal_data">Données personnelles</h3>
                <form id="formCandidature" class="space-y-4 sm:space-y-6" method="POST" action="back/confirmation.php" enctype="multipart/form-data">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
                        <div>
                            <label class="block text-sm font-medium mb-2" data-i18n="last_name_label">Nom <span class="text-red-500">*</span></label>
                            <input type="text" name="nom" required aria-required="true" data-i18n-placeholder="last_name_placeholder" placeholder="Nom" value="<?php echo $nom; ?>" class="w-full px-4 py-3 sm:py-4 text-base rounded-lg border border-gray-300 focus:border-[#a9cf46] focus:outline-none touch-target focus-visible mobile-text-adjust" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2" data-i18n="first_names_label">Prénoms <span class="text-red-500">*</span></label>
                            <input type="text" name="prenom" required aria-required="true" data-i18n-placeholder="first_names_placeholder" placeholder="Prénoms" value="<?php echo $prenom; ?>" class="w-full px-4 py-3 sm:py-4 text-base rounded-lg border border-gray-300 focus:border-[#a9cf46] focus:outline-none touch-target focus-visible mobile-text-adjust" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2" data-i18n="email_label">Email <span class="text-red-500">*</span></label>
                            <input type="email" name="email" required aria-required="true" data-i18n-placeholder="email_placeholder" placeholder="Adresse email" value="<?php echo $email; ?>" class="w-full px-4 py-3 sm:py-4 text-base rounded-lg border border-gray-300 focus:border-[#a9cf46] focus:outline-none touch-target focus-visible mobile-text-adjust" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2" data-i18n="phone_label">Téléphone <span class="text-red-500">*</span></label>
                            <input type="tel" name="telephone" required aria-required="true" data-i18n-placeholder="phone_placeholder" placeholder="Numéro de téléphone" value="<?php echo $telephone; ?>" pattern="^[\d\s+-]{10,}$" class="w-full px-4 py-3 sm:py-4 text-base rounded-lg border border-gray-300 focus:border-[#a9cf46] focus:outline-none touch-target focus-visible mobile-text-adjust" />
                        </div>
                    </div>
                    
                    <!-- Upload de fichiers amélioré -->
                    <div class="space-y-4">
                        <div class="relative">
                            <label for="cv" class="flex items-center justify-center w-full px-4 py-4 sm:py-5 text-center rounded-lg border-2 border-dashed border-gray-300 cursor-pointer bg-white text-gray-600 hover:bg-gray-50 transition touch-target focus-visible file-input-mobile" data-i18n="cv_upload_label">
                                <span class="mr-2">📄</span> Importer votre CV (.pdf, max 2 Mo)<span class="text-red-500 ml-1">*</span>
                            </label>
                            <input type="file" name="cv" id="cv" accept=".pdf" required aria-required="true" class="hidden" onchange="handleFileChange(this, 'cv-nom', 2)" />
                            <p id="cv-nom" class="text-sm text-center text-gray-500 mt-2" data-i18n="no_file">Aucun fichier</p>
                        </div>
                        
                        <div class="relative">
                            <label for="lettre" class="flex items-center justify-center w-full px-4 py-4 sm:py-5 text-center rounded-lg border-2 border-dashed border-gray-300 cursor-pointer bg-white text-gray-600 hover:bg-gray-50 transition touch-target focus-visible file-input-mobile" data-i18n="cover_letter_upload_label">
                                <span class="mr-2">✉</span> Importer votre lettre de motivation (.pdf, max 2 Mo)<span class="text-red-500 ml-1">*</span>
                            </label>
                            <input type="file" name="lettre" id="lettre" accept=".pdf" required aria-required="true" class="hidden" onchange="handleFileChange(this, 'lettre-nom', 2)" />
                            <p id="lettre-nom" class="text-sm text-center text-gray-500 mt-2" data-i18n="no_file">Aucun fichier</p>
                        </div>
                        
                        <div class="relative">
                            <label for="diplomes" class="flex items-center justify-center w-full px-4 py-4 sm:py-5 text-center rounded-lg border-2 border-dashed border-gray-300 cursor-pointer bg-white text-gray-600 hover:bg-gray-50 transition touch-target focus-visible file-input-mobile" data-i18n="diploma_upload_label">
                                <span class="mr-2">🎓</span> Importer votre dernier diplôme (.pdf, max 2 Mo)<span class="text-red-500 ml-1">*</span>
                            </label>
                            <input type="file" name="diplomes" id="diplomes" accept=".pdf" required aria-required="true" class="hidden" onchange="handleFileChange(this, 'diplomes-nom', 2)" />
                            <p id="diplomes-nom" class="text-sm text-center text-gray-500 mt-2" data-i18n="no_file">Aucun fichier</p>
                        </div>
                        
                        <div class="relative">
                            <label for="certification" class="flex items-center justify-center w-full px-4 py-4 sm:py-5 text-center rounded-lg border-2 border-dashed border-gray-300 cursor-pointer bg-white text-gray-600 hover:bg-gray-50 transition touch-target focus-visible file-input-mobile" data-i18n="certification_upload_label">
                                <span class="mr-2">📄</span> Importer une certification (facultatif, .pdf, max 2 Mo)
                            </label>
                            <input type="file" name="certification" id="certification" accept=".pdf" class="hidden" onchange="handleFileChange(this, 'certification-nom', 2)" />
                            <p id="certification-nom" class="text-sm text-center text-gray-500 mt-2" data-i18n="no_file">Aucun fichier</p>
                        </div>
                        
                        <div class="relative">
                            <label for="autre_document" class="flex items-center justify-center w-full px-4 py-4 sm:py-5 text-center rounded-lg border-2 border-dashed border-gray-300 cursor-pointer bg-white text-gray-600 hover:bg-gray-50 transition touch-target focus-visible file-input-mobile" data-i18n="other_docs_upload_label">
                                <span class="mr-2">📎</span> Ajouter jusqu'à 5 autres documents (facultatif, .pdf, max 2 Mo chacun)
                            </label>
                            <input type="file" name="autre_document[]" id="autre_document" accept=".pdf" multiple class="hidden" onchange="handleMultipleFiles(this, 'autre-documents-nom', 2, 5)" />
                            <p id="autre-documents-nom" class="text-sm text-center text-gray-500 mt-2" data-i18n="no_file">Aucun fichier</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start gap-3 text-sm">
                        <input type="checkbox" name="consentement" id="consentement" required aria-required="true" class="mt-1 w-5 h-5 flex-shrink-0" />
                        <label for="consentement" class="leading-relaxed">
                            <span data-i18n="consent_text">En envoyant ma candidature, je déclare avoir lu la</span> <a href="politique-confidentialite.php" class="text-[#a9cf46] hover:underline focus-visible" data-i18n="privacy_policy">Politique de confidentialité</a> <span data-i18n="consent_agreement">et je consens à ce que AGRIFORLAND stocke mes données personnelles pour traiter ma candidature.</span>
                        </label>
                    </div>
                    
                    <input type="hidden" name="poste_slug" value="<?php echo htmlspecialchars($slug); ?>">
                    <input type="hidden" name="poste_titre" value="<?php echo htmlspecialchars($titre); ?>">
                    
                    <button type="submit" class="w-full bg-[#a9cf46] hover:bg-[#93bc3d] py-3 sm:py-4 rounded-lg font-semibold transition-colors duration-200 touch-target focus-visible text-base" data-i18n="apply_button">
                        🚀 Postuler
                    </button>
                    
                    <p id="message" class="text-center text-sm text-green-600 mt-4 hidden" data-i18n="application_sent">Candidature envoyée avec succès !</p>
                </form>
                
                <!-- Modale de confirmation améliorée -->
                <div id="confirmationModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden z-50 p-4">
                    <div class="bg-white rounded-lg p-6 max-w-sm w-full mx-auto shadow-lg">
                        <h2 class="text-lg font-semibold mb-4" data-i18n="confirmation_title">Confirmation</h2>
                        <p class="mb-6 text-sm" data-i18n="confirmation_message">Vos informations sont correctes ?</p>
                        <div class="flex justify-end gap-3">
                            <button id="cancelBtn" class="px-4 py-2 rounded-lg bg-gray-300 hover:bg-gray-400 transition-colors touch-target focus-visible" data-i18n="cancel">Annuler</button>
                            <button id="confirmBtn" class="px-4 py-2 rounded-lg bg-green-600 text-white hover:bg-green-700 transition-colors touch-target focus-visible" data-i18n="ok">OK</button>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <!-- Footer amélioré -->
    <?php include __DIR__ . '/footer.php'; ?>


    <!-- Scripts optimisés -->
    <script>
        // Language translations (conservé identique à l'original avec quelques ajouts)
        const translations = {
            fr: {
                join_us: "Nous Rejoindre",
                contact_us: "Nous Contacter",
                home: "Accueil",
                about: "À Propos",
                poles: "Nos Pôles",
                projects: "Nos Projets",
                blog: "Blog",
                portfolios: "Portfolios",
                job_offer_label: "offre d'emploi/",
                location: "Abidjan, Côte d'Ivoire",
                job_description: "Description du poste",
                apply_now: "Postulez maintenant",
                main_missions: "Vos principales missions",
                profile_sought: "Profil recherché",
                offer_expired: "Offre résiliée",
                offer_expired_text: "Cette offre d'emploi a été résiliée et n'accepte plus de candidatures.",
                expired_since: "(Résiliée depuis le",
                discover_other_jobs: "Découvrez nos autres opportunités d'emploi en cliquant ci-dessous.",
                view_available_offers: "Voir les offres disponibles",
                personal_data: "Données personnelles",
                last_name_label: "Nom *",
                last_name_placeholder: "Nom",
                first_names_label: "Prénoms *",
                first_names_placeholder: "Prénoms",
                email_label: "Email *",
                email_placeholder: "Adresse email",
                phone_label: "Téléphone *",
                phone_placeholder: "Numéro de téléphone",
                cv_upload_label: "📄 Importer votre CV (.pdf, max 2 Mo)*",
                cover_letter_upload_label: "✉ Importer votre lettre de motivation (.pdf, max 2 Mo)*",
                diploma_upload_label: "🎓 Importer votre dernier diplôme (.pdf, max 2 Mo)*",
                certification_upload_label: "📄 Importer une certification (facultatif, .pdf, max 2 Mo)",
                other_docs_upload_label: "📎 Ajouter jusqu'à 5 autres documents (facultatif, .pdf, max 2 Mo chacun)",
                no_file: "Aucun fichier",
                consent_text: "En envoyant ma candidature, je déclare avoir lu la",
                privacy_policy: "Politique de confidentialité",
                consent_agreement: "et je consens à ce que AGRIFORLAND stocke mes données personnelles pour traiter ma candidature.",
                apply_button: "🚀 Postuler",
                application_sent: "Candidature envoyée avec succès !",
                confirmation_title: "Confirmation",
                confirmation_message: "Vos informations sont correctes ?",
                cancel: "Annuler",
                ok: "OK",
                follow_us: "SUIVEZ-NOUS",
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
                newsletter_error: "Erreur lors de l'inscription.",
                copyright: "© 2025 Agriforland. Tous droits réservés.",
                // File validation messages
                pdf_only: "Seuls les fichiers PDF sont autorisés.",
                file_too_large: "Le fichier est trop volumineux (max {size} Mo).",
                max_files: "Vous ne pouvez uploader que {max} fichiers maximum.",
                invalid_files: "Fichiers invalides : {files}",
                file_selected: "✅ {filename}",
                non_pdf: "non PDF",
                too_large: "trop volumineux, max {size} Mo"
            },
            en: {
                join_us: "Join Us",
                contact_us: "Contact Us",
                home: "Home",
                about: "About",
                poles: "Our Divisions",
                projects: "Our Projects",
                blog: "Blog",
                portfolios: "Portfolios",
                job_offer_label: "job offer/",
                location: "Abidjan, Côte d'Ivoire",
                job_description: "Job Description",
                apply_now: "Apply now",
                main_missions: "Your main missions",
                profile_sought: "Profile sought",
                offer_expired: "Expired offer",
                offer_expired_text: "This job offer has been terminated and no longer accepts applications.",
                expired_since: "(Expired since",
                discover_other_jobs: "Discover our other job opportunities by clicking below.",
                view_available_offers: "View available offers",
                personal_data: "Personal Data",
                last_name_label: "Last Name *",
                last_name_placeholder: "Last Name",
                first_names_label: "First Names *",
                first_names_placeholder: "First Names",
                email_label: "Email *",
                email_placeholder: "Email address",
                phone_label: "Phone *",
                phone_placeholder: "Phone number",
                cv_upload_label: "📄 Upload your CV (.pdf, max 2 MB)*",
                cover_letter_upload_label: "✉ Upload your cover letter (.pdf, max 2 MB)*",
                diploma_upload_label: "🎓 Upload your latest diploma (.pdf, max 2 MB)*",
                certification_upload_label: "📄 Upload a certification (optional, .pdf, max 2 MB)",
                other_docs_upload_label: "📎 Add up to 5 other documents (optional, .pdf, max 2 MB each)",
                no_file: "No file",
                consent_text: "By submitting my application, I declare that I have read the",
                privacy_policy: "Privacy Policy",
                consent_agreement: "and I consent to AGRIFORLAND storing my personal data to process my application.",
                apply_button: "🚀 Apply",
                application_sent: "Application sent successfully!",
                confirmation_title: "Confirmation",
                confirmation_message: "Is your information correct?",
                cancel: "Cancel",
                ok: "OK",
                follow_us: "FOLLOW US",
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
                newsletter_error: "Error during subscription.",
                copyright: "© 2025 Agriforland. All rights reserved.",
                // File validation messages
                pdf_only: "Only PDF files are allowed.",
                file_too_large: "File is too large (max {size} MB).",
                max_files: "You can only upload {max} files maximum.",
                invalid_files: "Invalid files: {files}",
                file_selected: "✅ {filename}",
                non_pdf: "not PDF",
                too_large: "too large, max {size} MB"
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

        // Language switcher amélioré
        const languageSelectors = document.querySelectorAll('#language-selector, #language-selector-mobile');
        const languageIcons = document.querySelectorAll('#language-icon, #language-icon-mobile');

        function updateContent(lang) {
            // Mettre à jour l'interface statique
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
            
            // IMPORTANT: Recharger la page avec la nouvelle langue pour le contenu dynamique
            if (window.initialLanguage !== lang) {
                const url = new URL(window.location);
                url.searchParams.set('lang', lang);
                window.location.href = url.toString();
            }
        }

        languageSelectors.forEach(selector => {
            selector.addEventListener('change', (e) => {
                const selectedLang = e.target.value;
                updateContent(selectedLang);
                localStorage.setItem('language', selectedLang);
            });
        });

        // Initialisation avec la langue depuis PHP ou localStorage
        const savedLang = localStorage.getItem('language') || window.initialLanguage || 'fr';
        if (savedLang !== window.initialLanguage) {
            // Si la langue sauvegardée diffère de celle du PHP, rediriger
            const url = new URL(window.location);
            url.searchParams.set('lang', savedLang);
            window.location.href = url.toString();
        } else {
            updateContent(savedLang);
        }

        // Validation de fichiers améliorée
        function handleFileChange(input, displayId, maxSizeMB) {
            const file = input.files[0];
            const display = document.getElementById(displayId);
            const currentLang = languageSelectors[0].value;
            
            if (file) {
                if (file.type !== 'application/pdf') {
                    display.innerHTML = `<span class="text-red-600">❌ ${translations[currentLang].pdf_only}</span>`;
                    input.value = '';
                    input.classList.add('border-red-500');
                } else if (file.size > maxSizeMB * 1024 * 1024) {
                    display.innerHTML = `<span class="text-red-600">❌ ${translations[currentLang].file_too_large.replace('{size}', maxSizeMB)}</span>`;
                    input.value = '';
                    input.classList.add('border-red-500');
                } else {
                    display.innerHTML = `<span class="text-green-600">${translations[currentLang].file_selected.replace('{filename}', file.name)}</span>`;
                    input.classList.remove('border-red-500');
                }
            } else {
                display.innerText = translations[currentLang].no_file;
                input.classList.remove('border-red-500');
            }
        }

        function handleMultipleFiles(input, displayId, maxSizeMB, maxFiles) {
            const display = document.getElementById(displayId);
            const files = input.files;
            const currentLang = languageSelectors[0].value;
            
            if (files.length === 0) {
                display.innerText = translations[currentLang].no_file;
                return;
            }
            
            if (files.length > maxFiles) {
                display.innerHTML = `<span class="text-red-600">❌ ${translations[currentLang].max_files.replace('{max}', maxFiles)}</span>`;
                input.value = '';
                return;
            }
            
            let fileNames = [];
            let invalidFiles = [];
            
            for (let i = 0; i < files.length; i++) {
                if (files[i].type !== 'application/pdf') {
                    invalidFiles.push(`${files[i].name} (${translations[currentLang].non_pdf})`);
                } else if (files[i].size > maxSizeMB * 1024 * 1024) {
                    invalidFiles.push(`${files[i].name} (${translations[currentLang].too_large.replace('{size}', maxSizeMB)})`);
                } else {
                    fileNames.push(files[i].name);
                }
            }
            
            if (invalidFiles.length > 0) {
                display.innerHTML = `<span class="text-red-600">❌ ${translations[currentLang].invalid_files.replace('{files}', invalidFiles.join(', '))}</span>`;
                if (fileNames.length === 0) {
                    input.value = '';
                }
            }
            
            if (fileNames.length > 0) {
                display.innerHTML = `<span class="text-green-600">✅ ${fileNames.join(', ')}</span>`;
            } else if (invalidFiles.length === 0) {
                display.innerText = translations[currentLang].no_file;
            }
        }

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

        // Menu mobile amélioré avec animations
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

        // Gestion de la modale améliorée
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('formCandidature');
            if (form) {
                const modal = document.getElementById('confirmationModal');
                const cancelBtn = document.getElementById('cancelBtn');
                const confirmBtn = document.getElementById('confirmBtn');

                form.addEventListener('submit', function (e) {
                    e.preventDefault();
                    modal.classList.remove('hidden');
                    // Focus sur le bouton Confirmer pour l'accessibilité
                    setTimeout(() => confirmBtn.focus(), 100);
                });

                cancelBtn.addEventListener('click', function () {
                    modal.classList.add('hidden');
                });

                confirmBtn.addEventListener('click', function () {
                    modal.classList.add('hidden');
                    form.submit();
                });

                // Fermer la modale avec Escape
                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
                        modal.classList.add('hidden');
                    }
                });
            }
        });

        // Newsletter améliorée
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
                
                const currentLang = languageSelectors[0].value;
                
                if (response.ok) {
                    newsletterMsg.classList.remove('hidden', 'text-red-600');
                    newsletterMsg.classList.add('text-green-600');
                    newsletterMsg.textContent = translations[currentLang].newsletter_success;
                    newsletterForm.reset();
                } else {
                    newsletterMsg.classList.remove('hidden', 'text-green-600');
                    newsletterMsg.classList.add('text-red-600');
                    newsletterMsg.textContent = translations[currentLang].newsletter_error;
                }
            } catch (error) {
                newsletterMsg.classList.remove('hidden', 'text-green-600');
                newsletterMsg.classList.add('text-red-600');
                newsletterMsg.textContent = translations[currentLang].newsletter_error;
            } finally {
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
            }
        });

        // Classe active pour la navigation
        const currentPage = window.location.pathname.split("/").pop();
        document.querySelectorAll('.nav-link').forEach(link => {
            const href = link.getAttribute('href');
            if (href === 'recrutement.html') {
                link.classList.add('text-[#a9cf46]', 'border-b-2', 'border-[#a9cf46]', 'font-semibold');
            }
        });

        // Amélioration de l'accessibilité au clavier
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && isMenuOpen) {
                toggleMenu();
            }
        });

        // Optimisation des images lazy loading
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