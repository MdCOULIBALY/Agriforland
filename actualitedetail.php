<?php
include 'admin/includes/db.php';

if (!isset($_GET['slug'])) {
    echo "Aucune actualité sélectionnée.";
    exit;
}

$slug = $_GET['slug'];
$stmt = $conn->prepare("SELECT * FROM a_la_une WHERE slug = ?");
$stmt->bind_param("s", $slug);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Actualité introuvable.";
    exit;
}

$actu = $result->fetch_assoc();

/**
 * Nettoie le contenu HTML généré par Microsoft Word
 * @param string $content Le contenu HTML sale
 * @return string Le contenu HTML nettoyé
 */
function cleanWordContent($content) {
    if (empty($content)) return '';
    
    // 1. Supprimer les balises et attributs spécifiques à Word
    $content = preg_replace('/<o:p\s*\/?>/', '', $content);
    $content = preg_replace('/<\/o:p>/', '', $content);
    $content = preg_replace('/<w:[^>]*>/', '', $content);
    $content = preg_replace('/<\/w:[^>]*>/', '', $content);
    
    // 2. Supprimer les classes MsoXXX
    $content = preg_replace('/class="Mso[^"]*"/', '', $content);
    $content = preg_replace('/class=""/', '', $content);
    
    // 3. Nettoyer les styles inline complexes de Word
    $content = preg_replace('/style="[^"]*mso[^"]*"/', '', $content);
    $content = preg_replace('/style="[^"]*margin[^"]*"/', '', $content);
    $content = preg_replace('/style="[^"]*line-height[^"]*"/', '', $content);
    $content = preg_replace('/style="[^"]*background[^"]*"/', '', $content);
    $content = preg_replace('/style=""/', '', $content);
    
    // 4. Supprimer les spans vides ou inutiles
    $content = preg_replace('/<span[^>]*><\/span>/', '', $content);
    $content = preg_replace('/<span[^>]*>([^<]+)<\/span>/', '$1', $content);
    
    // 5. Supprimer les images locales (msohtmlclip)
    $content = preg_replace('/<img[^>]*src="file:\/\/\/[^"]*"[^>]*>/i', '', $content);
    $content = preg_replace('/<img[^>]*src="[^"]*msohtmlclip[^"]*"[^>]*>/i', '', $content);
    $content = preg_replace('/<img[^>]*src="[^"]*clip_image[^"]*"[^>]*>/i', '', $content);
    
    // 6. Nettoyer les paragraphes vides
    $content = preg_replace('/<p[^>]*><\/p>/', '', $content);
    $content = preg_replace('/<p[^>]*>\s*<\/p>/', '', $content);
    $content = preg_replace('/<p[^>]*>\s*&nbsp;\s*<\/p>/', '', $content);
    
    // 7. Simplifier les styles de paragraphe et garder seulement les essentiels
    $content = preg_replace('/<p[^>]*class="MsoNormal"[^>]*>/', '<p>', $content);
    $content = preg_replace('/<p[^>]*style="[^"]*"[^>]*>/', '<p>', $content);
    
    // 8. Convertir les entités HTML en caractères normaux
    $content = html_entity_decode($content, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    
    // 9. Nettoyer les attributs Word spécifiques
    $content = preg_replace('/mso-[^;]*;?/', '', $content);
    $content = preg_replace('/font-family:[^;]*;?/', '', $content);
    $content = preg_replace('/font-size:[^;]*;?/', '', $content);
    
    // 10. Supprimer les espaces et retours à la ligne excessifs
    $content = preg_replace('/\s+/', ' ', $content);
    $content = preg_replace('/>\s+</', '><', $content);
    
    // 11. Nettoyer les balises autorisées seulement
    $allowedTags = '<p><br><strong><b><em><i><u><ul><ol><li><h1><h2><h3><h4><h5><h6><a><img>';
    $content = strip_tags($content, $allowedTags);
    
    // 12. Ajouter des retours à la ligne pour la lisibilité
    $content = str_replace('</p>', "</p>\n", $content);
    $content = str_replace('<br>', "<br>\n", $content);
    
    // 13. Si le contenu n'a pas de paragraphes, en créer
    if (!preg_match('/<p[^>]*>/', $content) && !empty(trim(strip_tags($content)))) {
        $content = '<p>' . trim($content) . '</p>';
    }
    
    return trim($content);
}

/**
 * Version alternative plus simple pour le résumé
 * @param string $content Le contenu HTML sale
 * @return string Le contenu texte nettoyé
 */
function cleanWordContentSimple($content) {
    if (empty($content)) return '';
    
    // Supprimer tout le HTML de Word et garder seulement le texte
    $content = strip_tags($content);
    
    // Convertir les entités HTML
    $content = html_entity_decode($content, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    
    // Nettoyer les espaces
    $content = preg_replace('/\s+/', ' ', $content);
    $content = trim($content);
    
    return $content;
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
    <title data-i18n="page_title"><?= htmlspecialchars($actu['titre']) ?> | AGRIFORLAND SARL</title>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@400;700&family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#258f1b',
                        secondary: '#a9cf46',
                        lightbg: '#f6ffde',
                        darkbg: '#3a3a3a',
                    },
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
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
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
        
        /* Optimisations spécifiques pour la lecture d'articles */
        .article-content {
            line-height: 1.8;
        }
        
        .article-content p {
            margin-bottom: 1.5rem;
            line-height: 1.8;
        }
        
        .article-content img {
            border-radius: 0.5rem;
            margin: 2rem 0;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            max-width: 100%;
            width: 100%;
            height: auto;
        }
        
        .article-content h1,
        .article-content h2,
        .article-content h3,
        .article-content h4,
        .article-content h5,
        .article-content h6 {
            margin-top: 2rem;
            margin-bottom: 1rem;
            font-weight: 600;
            line-height: 1.3;
        }
        
        .article-content h1 { font-size: 2rem; }
        .article-content h2 { font-size: 1.75rem; }
        .article-content h3 { font-size: 1.5rem; }
        .article-content h4 { font-size: 1.25rem; }
        
        /* Amélioration du rendu des listes */
        .article-content ul,
        .article-content ol {
            margin: 1.5rem 0;
            padding-left: 2rem;
        }
        
        .article-content li {
            margin-bottom: 0.5rem;
        }
        
        @media (max-width: 640px) {
            .article-content h1 { font-size: 1.5rem; }
            .article-content h2 { font-size: 1.375rem; }
            .article-content h3 { font-size: 1.25rem; }
            .article-content h4 { font-size: 1.125rem; }
            
            .article-content {
                font-size: 16px;
                line-height: 1.7;
            }
            
            .article-content img {
                margin: 1.5rem 0;
            }
            
            .article-content ul,
            .article-content ol {
                padding-left: 1.5rem;
            }
        }
        
        .tag {
            transition: all 0.3s ease;
        }
        
        .tag:hover {
            transform: translateY(-2px);
        }
    </style>
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

<body class="bg-lightbg text-gray-800 font-roboto">

  <!-- En tête -->
         <?php require __DIR__ . "/includes/header.php"; ?>


<!-- Hero Section amélioré -->
<section class="relative">
    <?php if (!empty($actu['image'])): ?>
    <div class="h-64 xs:h-80 sm:h-96 overflow-hidden">
        <img src="admin/<?= htmlspecialchars($actu['image']) ?>" 
             alt="<?= htmlspecialchars($actu['titre']) ?>" 
             loading="lazy" 
             class="w-full h-full object-cover object-center">
    </div>
    <div class="absolute inset-0 flex items-center justify-center bg-black/40">
        <div class="text-center px-4 sm:px-6 max-w-4xl mx-auto">
            <h1 class="text-white text-xl xs:text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-bold font-kanit mb-4 leading-tight"><?= htmlspecialchars($actu['titre']) ?></h1>
            <div class="flex items-center justify-center space-x-4 text-white/80 text-sm xs:text-base">
                <span><?= date("d/m/Y", strtotime($actu['date_publication'])) ?></span>
            </div>
        </div>
    </div>
    <?php endif; ?>
</section>

<!-- Breadcrumb amélioré -->
<div class="bg-white py-3 sm:py-4 shadow-sm">
    <div class="container mx-auto px-4 sm:px-6">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2 text-sm">
                <li class="inline-flex items-center">
                    <a href="index.php" class="inline-flex items-center text-gray-700 hover:text-primary transition-colors focus-visible touch-target py-1">
                        <i class="ph ph-house mr-2"></i>
                        <span data-i18n="home">Accueil</span>
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="ph ph-caret-right text-gray-400 mx-1"></i>
                        <a href="index.php" class="text-gray-700 hover:text-primary transition-colors focus-visible touch-target py-1" data-i18n="news">Actualités</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <i class="ph ph-caret-right text-gray-400 mx-1"></i>
                        <span class="text-primary font-medium truncate max-w-32 xs:max-w-48 sm:max-w-none"><?= htmlspecialchars($actu['titre']) ?></span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>
</div>

<!-- Article Content amélioré -->
<section class="container mx-auto px-4 sm:px-6 py-8 sm:py-12">
    <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="p-4 sm:p-6 md:p-8">
            <!-- Résumé amélioré et nettoyé -->
            <?php if (!empty($actu['resume'])): ?>
            <div class="mb-6 sm:mb-8 p-4 sm:p-6 bg-lightbg rounded-lg border-l-4 border-primary">
                <h3 class="font-bold text-base xs:text-lg text-darkbg mb-3" data-i18n="summary">Resumé :</h3>
                <p class="text-gray-700 text-sm xs:text-base leading-relaxed"><?= cleanWordContentSimple($actu['resume']) ?></p>
            </div>
            <?php endif; ?>
            
            <!-- Contenu complet optimisé et nettoyé -->
            <div class="article-content prose prose-sm xs:prose-base max-w-none">
                <?= cleanWordContent($actu['contenu']) ?>
            </div>
            
            <!-- Lien associé amélioré -->
            <?php if (!empty($actu['lien'])): ?>
            <div class="mt-6 sm:mt-8 pt-6 border-t border-gray-200">
                <a href="<?= htmlspecialchars($actu['lien']) ?>" 
                   target="_blank" 
                   rel="noopener noreferrer"
                   class="inline-flex items-center bg-primary text-white px-4 py-3 rounded-lg hover:bg-green-700 transition-colors touch-target focus-visible text-sm font-medium">
                    <span data-i18n="view_related_link">Voir le lien associé</span>
                    <i class="ph ph-arrow-square-out ml-2"></i>
                </a>
            </div>
            <?php endif; ?>
            
            <!-- Métadonnées améliorées -->
            <div class="mt-6 sm:mt-8 pt-6 border-t border-gray-200">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between text-gray-500 gap-3 text-sm">
                    <div class="flex items-center">
                        <i class="ph ph-calendar mr-2 flex-shrink-0"></i>
                        <span data-i18n="published_on">Publié le :</span> 
                        <span class="ml-1"><?= date("d/m/Y", strtotime($actu['date_publication'])) ?></span>
                    </div>
                </div>
            </div>
            
            <!-- Boutons d'action améliorés -->
            <div class="mt-6 sm:mt-8 pt-6 border-t border-gray-200">
                <div class="flex flex-col xs:flex-row gap-3 xs:gap-4">
                    <a href="index.php" class="inline-flex items-center justify-center px-4 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors touch-target focus-visible text-sm font-medium">
                        <i class="ph ph-arrow-left mr-2"></i>
                        <span data-i18n="back_to_news">Retour aux actualités</span>
                    </a>
                    <button onclick="window.print()" class="inline-flex items-center justify-center px-4 py-3 bg-secondary text-white rounded-lg hover:bg-primary transition-colors touch-target focus-visible text-sm font-medium">
                        <i class="ph ph-printer mr-2"></i>
                        <span data-i18n="print_article">Imprimer l'article</span>
                    </button>
                    <button onclick="shareArticle()" class="inline-flex items-center justify-center px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors touch-target focus-visible text-sm font-medium">
                        <i class="ph ph-share mr-2"></i>
                        <span data-i18n="share_article">Partager</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>

    <!-- Footer -->
     
   <?php require __DIR__ . "/includes/footer.php"; ?>


<!-- Scripts optimisés -->
<script>
    // Language translations améliorées
    const translations = {
        fr: {
            page_title: "<?= htmlspecialchars($actu['titre']) ?> | AGRIFORLAND SARL",
            agriforland_logo: "Logo Agriforland",
            open_menu: "Ouvrir le menu",
            language: "Langue",
            join_us: "Nous Rejoindre",
            contact_us: "Nous Contacter",
            home: "Accueil",
            about: "À Propos",
            poles: "Nos Pôles",
            projects: "Nos Projets",
            news: "Actualités",
            portfolios: "Portfolios",
            summary: "Resumé :",
            view_related_link: "Voir le lien associé",
            published_on: "Publié le :",
            back_to_news: "Retour aux actualités",
            print_article: "Imprimer l'article",
            share_article: "Partager",
            footer_description: "Experts en solutions intégrées pour l'agriculture, la foresterie et le développement durable.",
            facebook: "Facebook",
            twitter: "Twitter",
            instagram: "Instagram",
            linkedin: "LinkedIn",
            useful_links: "Liens utiles",
            about_us: "À propos de nous",
            our_expertise: "Nos pôles d'expertise",
            our_projects: "Nos projets",
            our_services: "Nos services",
            agroforestry_consulting: "Conseil en agroforesterie",
            environmental_studies: "Études environnementales",
            technical_training: "Formations techniques",
            gis_mapping: "Cartographie SIG",
            rural_development: "Développement rural",
            contact: "Contact",
            address: "Cocody Riviera Faya ATCI, Abidjan, Côte d'Ivoire",
            business_hours: "Lun-Ven: 8h-17h",
            copyright: "© 2025 AGRIFORLAND SARL. Tous droits réservés.",
            legal_notices: "Mentions légales",
            privacy_policy: "Politique de confidentialité",
            terms_of_use: "Conditions d'utilisation"
        },
        en: {
            page_title: "<?= htmlspecialchars($actu['titre']) ?> | AGRIFORLAND SARL",
            agriforland_logo: "Agriforland Logo",
            open_menu: "Open menu",
            language: "Language",
            join_us: "Join Us",
            contact_us: "Contact Us",
            home: "Home",
            about: "About",
            poles: "Our Divisions",
            projects: "Our Projects",
            news: "News",
            portfolios: "Portfolios",
            summary: "Summary:",
            view_related_link: "View related link",
            published_on: "Published on:",
            back_to_news: "Back to news",
            print_article: "Print article",
            share_article: "Share",
            footer_description: "Experts in integrated solutions for agriculture, forestry and sustainable development.",
            facebook: "Facebook",
            twitter: "Twitter",
            instagram: "Instagram",
            linkedin: "LinkedIn",
            useful_links: "Useful Links",
            about_us: "About us",
            our_expertise: "Our expertise divisions",
            our_projects: "Our projects",
            our_services: "Our Services",
            agroforestry_consulting: "Agroforestry consulting",
            environmental_studies: "Environmental studies",
            technical_training: "Technical training",
            gis_mapping: "GIS mapping",
            rural_development: "Rural development",
            contact: "Contact",
            address: "Cocody Riviera Faya ATCI, Abidjan, Côte d'Ivoire",
            business_hours: "Mon-Fri: 8am-5pm",
            copyright: "© 2025 AGRIFORLAND SARL. All rights reserved.",
            legal_notices: "Legal notices",
            privacy_policy: "Privacy policy",
            terms_of_use: "Terms of use"
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
        // Update static translations
        document.querySelectorAll('[data-i18n]').forEach(element => {
            const key = element.getAttribute('data-i18n');
            if (translations[lang][key]) {
                element.textContent = translations[lang][key];
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

    // Menu mobile amélioré avec animations
    const toggle = document.getElementById('menu-toggle');
    const mobileMenu = document.getElementById('mobile-menu');
    let isMenuOpen = false;

    const toggleMenu = () => {
        isMenuOpen = !isMenuOpen;
        
        if (isMenuOpen) {
            mobileMenu.classList.remove('hidden');
            mobileMenu.classList.add('mobile-menu-enter-active');
            toggle.setAttribute('aria-expanded', 'true');
            // Animation du bouton burger
            toggle.querySelector('.menu-line-1').style.transform = 'rotate(45deg) translate(6px, 6px)';
            toggle.querySelector('.menu-line-2').style.opacity = '0';
            toggle.querySelector('.menu-line-3').style.transform = 'rotate(-45deg) translate(6px, -6px)';
        } else {
            mobileMenu.classList.remove('mobile-menu-enter-active');
            toggle.setAttribute('aria-expanded', 'false');
            // Reset animation du bouton burger
            toggle.querySelector('.menu-line-1').style.transform = '';
            toggle.querySelector('.menu-line-2').style.opacity = '';
            toggle.querySelector('.menu-line-3').style.transform = '';
            setTimeout(() => mobileMenu.classList.add('hidden'), 300);
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
    mobileMenu.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', () => {
            if (isMenuOpen) toggleMenu();
        });
    });

    // Ajout de la classe active pour la navigation
    const currentPage = window.location.pathname.split("/").pop();
    document.querySelectorAll('.nav-link').forEach(link => {
        const href = link.getAttribute('href');
        if (href === currentPage || (href === 'blog.php' && (currentPage.includes('actualite') || currentPage.includes('news')))) {
            link.classList.add('text-secondary', 'border-b-2', 'border-secondary', 'font-semibold');
        }
    });

    // Gestion optimisée du preloader
    const hidePreloader = () => {
        const preloader = document.getElementById('preloader');
        if (preloader && !preloader.classList.contains('hidden')) {
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

    // Fonction de partage d'article
    function shareArticle() {
        const title = document.querySelector('h1').textContent;
        const url = window.location.href;
        
        if (navigator.share) {
            navigator.share({
                title: title,
                url: url
            }).catch(err => console.log('Erreur lors du partage:', err));
        } else {
            // Fallback : copier l'URL
            navigator.clipboard.writeText(url).then(() => {
                alert('Lien copié dans le presse-papiers !');
            }).catch(() => {
                // Fallback pour les navigateurs plus anciens
                const textArea = document.createElement('textarea');
                textArea.value = url;
                document.body.appendChild(textArea);
                textArea.select();
                document.execCommand('copy');
                document.body.removeChild(textArea);
                alert('Lien copié dans le presse-papiers !');
            });
        }
    }

    // Amélioration de l'accessibilité au clavier
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && isMenuOpen) {
            toggleMenu();
        }
    });

    // Optimisation des images dans le contenu d'article
    document.addEventListener('DOMContentLoaded', () => {
        const articleImages = document.querySelectorAll('.article-content img');
        articleImages.forEach(img => {
            img.addEventListener('click', () => {
                // Ajouter un comportement de zoom ou modal si souhaité
                if (img.requestFullscreen) {
                    img.requestFullscreen();
                } else if (img.webkitRequestFullscreen) {
                    img.webkitRequestFullscreen();
                } else if (img.msRequestFullscreen) {
                    img.msRequestFullscreen();
                }
            });
        });
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

    // Rendre shareArticle disponible globalement
    window.shareArticle = shareArticle;
</script>
</body>
</html>