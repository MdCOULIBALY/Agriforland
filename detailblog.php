<?php
require_once 'admin/includes/db.php';

if (!isset($_GET['slug'])) {
    echo "Aucun article spécifié.";
    exit;
}

$slug = $_GET['slug'];
$article = null;

// Récupération de l'article via le slug avec mysqli
$stmt = $conn->prepare("SELECT * FROM articles WHERE slug = ?");
$stmt->bind_param("s", $slug);
$stmt->execute();
$result = $stmt->get_result();
$article = $result->fetch_assoc();

if (!$article) {
    echo "Article introuvable.";
    exit;
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
  <title><?php echo ($article['title']); ?> | AGRIFORLAND SARL</title>
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
    }
    
    .tag {
        transition: all 0.3s ease;
    }
    
    .tag:hover {
        transform: translateY(-2px);
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    .triangle-img {
        animation: spin 2s linear infinite;
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
  <!-- Preloader optimisé -->
  <div id="preloader" class="fixed inset-0 bg-lightbg z-50 flex items-center justify-center preloader-optimized">
    <div class="animate-pulse w-24 h-24 sm:w-32 sm:h-32">
      <img src="images/triangle-svgrepo-com.svg" alt="Chargement..." loading="lazy" class="w-full h-full object-contain triangle-img">
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
        <a href="recrutement.html" class="bg-[#759916] text-white px-4 py-2 rounded-lg hover:bg-[#ade126] transition-colors text-sm font-semibold focus-visible touch-target">
          Nous Rejoindre
        </a>
        <a href="contact.html" class="border border-gray-500 px-4 py-2 rounded-lg hover:bg-[#f6ffde] transition-colors text-sm focus-visible touch-target">
          Nous Contacter
        </a>
      </div>
    </div>

    <!-- Navigation Desktop -->
    <div class="border-t border-gray-100 bg-[#f6ffde] hidden md:block">
      <nav class="max-w-7xl mx-auto px-4 sm:px-6 py-3 flex justify-center gap-6 text-lg">
        <a href="index.php" class="nav-link hover:text-[#a9cf46] transition-colors focus-visible touch-target">Accueil</a>
        <a href="about.php" class="nav-link hover:text-[#a9cf46] transition-colors focus-visible touch-target">À Propos</a>
        <a href="poles.html" class="nav-link hover:text-[#a9cf46] transition-colors focus-visible touch-target">Nos Pôles</a>
        <a href="projets.html" class="nav-link hover:text-[#a9cf46] transition-colors focus-visible touch-target">Nos Projets</a>
        <a href="blog.php" class="nav-link hover:text-[#a9cf46] transition-colors focus-visible touch-target">Blog</a>
        <a href="portfolios.php" class="nav-link hover:text-[#a9cf46] transition-colors focus-visible touch-target">Portfolios</a>
      </nav>
    </div>

    <!-- Menu Mobile amélioré -->
    <div id="mobile-menu" class="md:hidden hidden bg-[#f6ffde] mobile-padding mobile-menu-enter">
      <nav class="flex flex-col mobile-gap text-base">
        <a href="index.php" class="nav-link hover:text-[#a9cf46] transition touch-target py-3 focus-visible">Accueil</a>
        <a href="about.php" class="nav-link hover:text-[#a9cf46] transition touch-target py-3 focus-visible">À Propos</a>
        <a href="poles.html" class="nav-link hover:text-[#a9cf46] transition touch-target py-3 focus-visible">Nos Pôles</a>
        <a href="projets.html" class="nav-link hover:text-[#a9cf46] transition touch-target py-3 focus-visible">Nos Projets</a>
        <a href="blog.php" class="nav-link hover:text-[#a9cf46] transition touch-target py-3 focus-visible">Blog</a>
        <a href="portfolios.php" class="nav-link hover:text-[#a9cf46] transition-colors touch-target py-3 focus-visible">Portfolios</a>
      </nav>
      <div class="mt-6 flex flex-col gap-3">
        <a href="recrutement.html" class="bg-[#759916] text-white px-4 py-3 rounded-lg text-center font-semibold hover:bg-[#ade126] transition-colors touch-target focus-visible">Nous Rejoindre</a>
        <a href="contact.html" class="border border-gray-500 px-4 py-3 rounded-lg text-center hover:bg-white transition-colors touch-target focus-visible">Nous Contacter</a>
      </div>
    </div>
  </header>

  <!-- Hero amélioré -->
  <section class="relative">
    <div class="h-64 xs:h-80 sm:h-96 overflow-hidden">
      <img src="admin/<?php echo htmlspecialchars($article['image']); ?>" loading="lazy" alt="<?php echo ($article['title']); ?>" class="w-full h-full object-cover object-center">
    </div>
    <div class="absolute inset-0 flex items-center justify-center bg-black/40">
      <div class="text-center px-4 sm:px-6 max-w-4xl mx-auto">
        <h1 class="text-white text-xl xs:text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-bold font-kanit mb-4 leading-tight"><?php echo ($article['title']); ?></h1>
        <div class="flex items-center justify-center space-x-4 text-white/80 text-sm xs:text-base">
        </div>
      </div>
    </div>
  </section>

  <!-- Breadcrumb amélioré -->
  <div class="bg-white py-3 sm:py-4 shadow-sm">
    <div class="container mx-auto px-4 sm:px-6">
      <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-2 text-sm">
          <li class="inline-flex items-center">
            <a href="index.php" class="inline-flex items-center text-gray-700 hover:text-primary transition-colors focus-visible touch-target py-1">
              <i class="ph ph-house mr-2"></i>
              Accueil
            </a>
          </li>
          <li>
            <div class="flex items-center">
              <i class="ph ph-caret-right text-gray-400 mx-1"></i>
              <a href="blog.php" class="text-gray-700 hover:text-primary transition-colors focus-visible touch-target py-1">Blog</a>
            </div>
          </li>
          <li aria-current="page">
            <div class="flex items-center">
              <i class="ph ph-caret-right text-gray-400 mx-1"></i>
              <span class="text-gray-500 truncate max-w-32 xs:max-w-48 sm:max-w-none"><?php echo ($article['title']); ?></span>
            </div>
          </li>
        </ol>
      </nav>
    </div>
  </div>

  <!-- Détail du Post amélioré -->
  <section class="container mx-auto px-4 sm:px-6 py-8 sm:py-12">
    <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-lg overflow-hidden">
      <!-- Contenu principal -->
      <div class="p-4 sm:p-6 md:p-8">
        <!-- Résumé amélioré -->
        <?php if (!empty($article['resume'])): ?>
        <div class="mb-6 sm:mb-8 p-4 sm:p-6 bg-lightbg rounded-lg border-l-4 border-primary">
          <h3 class="font-bold text-base xs:text-lg text-darkbg mb-3">En résumé :</h3>
          <p class="text-gray-700 text-sm xs:text-base leading-relaxed"><?php echo ($article['resume']); ?></p>
        </div>
        <?php endif; ?>
        
        <!-- Contenu complet optimisé pour mobile -->
        <div class="article-content prose prose-sm xs:prose-base max-w-none">
          <?php echo $article['contenu'] ?? 'Contenu non disponible'; ?>
        </div>
        
        <!-- Métadonnées améliorées -->
        <div class="mt-8 sm:mt-12 pt-6 border-t border-gray-200">
          <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between text-gray-500 gap-3 text-sm">
            <div class="flex items-center">
              <i class="ph ph-calendar mr-2 flex-shrink-0"></i>
              <span>Publié le : <?php echo htmlspecialchars($article['created_at']); ?></span>
            </div>
            <div class="flex items-center">
              <i class="ph ph-user-circle mr-2 flex-shrink-0"></i>
              <span>Auteur : <?php echo htmlspecialchars($article['author']); ?></span>
            </div>
          </div>
        </div>
        
        <!-- Boutons d'action améliorés -->
        <div class="mt-6 sm:mt-8 pt-6 border-t border-gray-200">
          <div class="flex flex-col xs:flex-row gap-3 xs:gap-4">
            <a href="blog.php" class="inline-flex items-center justify-center px-4 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors touch-target focus-visible text-sm font-medium">
              <i class="ph ph-arrow-left mr-2"></i>
              Retour au blog
            </a>
            <button onclick="window.print()" class="inline-flex items-center justify-center px-4 py-3 bg-secondary text-white rounded-lg hover:bg-primary transition-colors touch-target focus-visible text-sm font-medium">
              <i class="ph ph-printer mr-2"></i>
              Imprimer l'article
            </button>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Footer amélioré -->
    <?php include __DIR__ . '/footer.php'; ?>


  <!-- Scripts optimisés -->
  <script>
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
        if (href === currentPage || (href === 'blog.php' && currentPage.includes('article'))) {
            link.classList.add('text-[#a9cf46]', 'border-b-2', 'border-[#a9cf46]', 'font-semibold');
        }
    });

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

    // Amélioration de la lecture sur mobile
    function adjustReadability() {
        const isMobile = window.innerWidth < 640;
        const articleContent = document.querySelector('.article-content');
        
        if (articleContent && isMobile) {
            // Ajuster l'espacement des paragraphes sur mobile
            const paragraphs = articleContent.querySelectorAll('p');
            paragraphs.forEach(p => {
                p.style.marginBottom = '1.25rem';
            });
            
            // Ajuster les images pour qu'elles soient plus grandes sur mobile
            const images = articleContent.querySelectorAll('img');
            images.forEach(img => {
                img.style.maxWidth = '100%';
                img.style.height = 'auto';
            });
        }
    }

    // Ajuster lors du chargement et du redimensionnement
    window.addEventListener('load', adjustReadability);
    window.addEventListener('resize', debounce(adjustReadability, 250));

    // Partage d'article (si souhaité)
    function shareArticle() {
        if (navigator.share) {
            navigator.share({
                title: document.title,
                url: window.location.href
            });
        } else {
            // Fallback : copier l'URL
            navigator.clipboard.writeText(window.location.href).then(() => {
                alert('Lien copié dans le presse-papiers !');
            });
        }
    }

    // Ajouter un bouton de partage si on le souhaite
    // document.addEventListener('DOMContentLoaded', () => {
    //     const actionsDiv = document.querySelector('.mt-6.sm\\:mt-8');
    //     if (actionsDiv && navigator.share) {
    //         const shareButton = document.createElement('button');
    //         shareButton.onclick = shareArticle;
    //         shareButton.className = 'inline-flex items-center justify-center px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors touch-target focus-visible text-sm font-medium';
    //         shareButton.innerHTML = '<i class="ph ph-share mr-2"></i>Partager';
    //         actionsDiv.querySelector('.flex').appendChild(shareButton);
    //     }
    // });
  </script>
</body>
</html>