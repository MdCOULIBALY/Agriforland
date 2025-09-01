<?php
session_start();
$message = isset($_GET['message']) ? htmlspecialchars(urldecode($_GET['message'])) : 'Une erreur inconnue est survenue.';
$slug = isset($_GET['slug']) ? htmlspecialchars(urldecode($_GET['slug'])) : '';

// Vérifier si le slug est valide en consultant le fichier JSON
$return_to_form = false;
if ($slug) {
    $file_path = 'data/recrutement.json';
    if (file_exists($file_path)) {
        $posts = json_decode(file_get_contents($file_path), true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($posts)) {
            foreach ($posts as $post) {
                if ($post['slug'] === $slug) {
                    $return_to_form = true;
                    break;
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AGRIFORLAND - Erreur</title>
    
    <!-- Preconnect optimisé (suppression des doublons) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdn.tailwindcss.com">
    <link rel="preconnect" href="https://unpkg.com">
    
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@400;700&family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="icon" href="images/favicon.ico" type="image/x-icon">
    <link href="css/Style.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-ZKKVQJJCYG"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'G-ZKKVQJJCYG');
    </script>
</head>
<body class="bg-[#f6ffde] text-black">
    <!-- Preloader -->
    <div id="preloader" class="fixed inset-0 bg-[#f6ffde] z-50 flex items-center justify-center">
        <div class="animate-triangle w-24 h-24 sm:w-32 sm:h-32">
            <img src="images/triangle-svgrepo-com.svg" loading="lazy" alt="Chargement..." class="w-full h-full object-contain triangle-img">
        </div>
    </div>

    <!-- Header -->
    <header class="bg-white shadow-md sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-3 sm:px-4 py-3 flex items-center justify-between">
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
                class="h-8 sm:h-10 md:h-12"
            />
            <button id="menu-toggle" class="md:hidden text-gray-700 focus:outline-none p-1" aria-label="Ouvrir le menu">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
            <div class="hidden md:flex gap-3 items-center ml-auto">
                <a href="recrutement.html" class="bg-[#759916] text-white px-4 py-2 rounded-md hover:text-black hover:bg-[#ade126] transition text-sm whitespace-nowrap">
                    Nous Rejoindre
                </a>
                <a href="contact.html" class="border border-gray-500 px-4 py-2 rounded-md hover:text-black hover:bg-[#f6ffde] transition text-sm whitespace-nowrap">
                    Nous Contacter
                </a>
            </div>
        </div>
        <div class="border-t border-gray-100 bg-[#f6ffde] hidden md:block">
            <nav class="max-w-7xl mx-auto px-4 py-3 flex justify-center gap-6 lg:gap-8 text-lg">
                <a href="index.php" class="nav-link hover:text-[#a9cf46] transition-colors">Accueil</a>
                <a href="about.php" class="nav-link hover:text-[#a9cf46] transition-colors">À Propos</a>
                <a href="poles.html" class="nav-link hover:text-[#a9cf46] transition-colors">Nos Pôles</a>
                <a href="projets.html" class="nav-link hover:text-[#a9cf46] transition-colors">Nos Projets</a>
                <a href="blog.php" class="nav-link hover:text-[#a9cf46] transition-colors">Blog</a>
            </nav>
        </div>
        <div id="mobile-menu" class="md:hidden hidden bg-[#f6ffde] px-4 pb-4">
            <nav class="flex flex-col gap-3 text-base">
                <a href="index.php" class="nav-link hover:text-[#a9cf46] transition py-2">Accueil</a>
                <a href="about.php" class="nav-link hover:text-[#a9cf46] transition py-2">À Propos</a>
                <a href="poles.html" class="nav-link hover:text-[#a9cf46] transition py-2">Nos Pôles</a>
                <a href="projets.html" class="nav-link hover:text-[#a9cf46] transition py-2">Nos Projets</a>
                <a href="blog.php" class="nav-link hover:text-[#a9cf46] transition py-2">Blog</a>
            </nav>
            <div class="mt-4 flex flex-col gap-3">
                <a href="recrutement.html" class="bg-[#759916] text-white px-4 py-3 rounded-md text-center text-sm hover:bg-[#ade126] transition">Nous Rejoindre</a>
                <a href="contact.html" class="border border-gray-500 px-4 py-3 rounded-md text-center text-sm hover:bg-white transition">Nous contacter</a>
            </div>
        </div>
    </header>

    <!-- Section Erreur -->
    <section class="bg-[#f5fad3] py-8 sm:py-12 px-4 sm:px-6 md:px-16 text-black min-h-screen flex items-center">
        <div class="max-w-4xl mx-auto bg-white shadow-lg p-4 sm:p-6 md:p-8 rounded-xl border border-red-500 w-full">
            <div class="flex items-center gap-3 sm:gap-4 mb-4 sm:mb-6">
                <span class="text-red-600 text-3xl sm:text-4xl">❌</span>
                <h1 class="text-2xl sm:text-3xl font-bold text-red-600">Erreur</h1>
            </div>
            <div class="text-gray-700 mb-6 sm:mb-8 text-sm sm:text-base leading-relaxed">
                <?php echo nl2br(htmlspecialchars($message)); ?>
            </div>
            <div class="flex flex-col sm:flex-row justify-center gap-3 sm:gap-4">
                <?php if ($return_to_form): ?>
                    <a href="description-du-poste.php?slug=<?= htmlspecialchars($slug) ?>" 
                       class="bg-[#a9cf46] px-4 sm:px-6 py-3 rounded-md text-white hover:bg-[#93bc3d] transition text-center text-sm sm:text-base font-medium">
                        Retour au formulaire
                    </a>
                <?php endif; ?>
                <a href="recrutements.html" 
                   class="border border-gray-500 px-4 sm:px-6 py-3 rounded-md text-black hover:bg-[#f6ffde] transition text-center text-sm sm:text-base font-medium">
                    Retour aux offres
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php include __DIR__ . '/footer.php'; ?>


    <!-- Scripts -->
    <script>
        // Preloader
        window.addEventListener("load", function () {
            const preloader = document.getElementById('preloader');
            preloader.classList.add('opacity-0', 'pointer-events-none', 'transition-opacity', 'duration-500');
            setTimeout(() => preloader.remove(), 500);
        });

        // Menu mobile avec fermeture automatique
        const toggle = document.getElementById('menu-toggle');
        const menu = document.getElementById('mobile-menu');
        
        toggle.addEventListener('click', () => {
            menu.classList.toggle('hidden');
        });

        // Fermer le menu en cliquant sur un lien
        const mobileLinks = menu.querySelectorAll('a');
        mobileLinks.forEach(link => {
            link.addEventListener('click', () => {
                menu.classList.add('hidden');
            });
        });

        // Fermer le menu en cliquant en dehors
        document.addEventListener('click', (e) => {
            if (!toggle.contains(e.target) && !menu.contains(e.target)) {
                menu.classList.add('hidden');
            }
        });

        // Newsletter améliorée
        const newsletterForm = document.getElementById('newsletter-form');
        const newsletterMsg = document.getElementById('newsletter-msg');
        const submitButton = newsletterForm.querySelector('button[type="submit"]');
        
        newsletterForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            // État de chargement
            submitButton.disabled = true;
            submitButton.textContent = 'Inscription...';
            
            try {
                const formData = new FormData(newsletterForm);
                const response = await fetch('back/newsletter.php', {
                    method: 'POST',
                    body: formData
                });
                
                if (response.ok) {
                    newsletterMsg.classList.remove('hidden');
                    newsletterMsg.classList.remove('text-red-600');
                    newsletterMsg.classList.add('text-green-600');
                    newsletterMsg.textContent = "Merci pour votre inscription !";
                    newsletterForm.reset();
                } else {
                    newsletterMsg.classList.remove('hidden');
                    newsletterMsg.classList.remove('text-green-600');
                    newsletterMsg.classList.add('text-red-600');
                    newsletterMsg.textContent = "Erreur lors de l'inscription.";
                }
            } catch (error) {
                newsletterMsg.classList.remove('hidden');
                newsletterMsg.classList.remove('text-green-600');
                newsletterMsg.classList.add('text-red-600');
                newsletterMsg.textContent = "Erreur de connexion.";
            } finally {
                // Restaurer le bouton
                submitButton.disabled = false;
                submitButton.textContent = "S'inscrire";
                
                // Masquer le message après 5 secondes
                setTimeout(() => {
                    newsletterMsg.classList.add('hidden');
                }, 5000);
            }
        });
    </script>
</body>
</html>