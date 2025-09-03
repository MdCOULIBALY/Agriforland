  <!-- Footer -->
  <footer class="bg-[#3a3a3a] text-white py-12">
    <div class="max-w-7xl mx-auto px-4">
      <div class="flex flex-col md:flex-row justify-between items-center border-b border-white/20 pb-6">
        <!-- Logo aligné à gauche -->
        <div class="mb-4 md:mb-0">
          <img 
            src="cache/logo-inverse-198x66-800.webp" 
            srcset="
              cache/logo-inverse-198x66-480.webp 480w, 
              cache/logo-inverse-198x66-800.webp 800w, 
              cache/logo-inverse-198x66-1200.webp 1200w
            "
            sizes="(max-width: 600px) 480px, (max-width: 1000px) 800px, 1200px"
            loading="lazy" 
            alt="" 
            data-alt-i18n="agriforland_logo"
            class="h-12"
          />
        </div>
        <!-- Réseaux sociaux alignés à droite -->
        <div class="text-center md:text-right">
          <p class="font-bold mb-2" data-i18n="follow_us">SUIVEZ-NOUS</p>
          <div class="flex justify-center md:justify-end gap-4 text-2xl">
            <a class="hover:text-[#a9cf46] transition-colors" href="#" aria-label="" data-aria-i18n="facebook"><i class="ph ph-facebook-logo"></i></a>
            <a class="hover:text-[#a9cf46] transition-colors" href="#" aria-label="" data-aria-i18n="instagram"><i class="ph ph-instagram-logo"></i></a>
            <a class="hover:text-[#a9cf46] transition-colors" href="#" aria-label="" data-aria-i18n="twitter"><i class="ph ph-twitter-logo"></i></a>
            <a class="hover:text-[#a9cf46] transition-colors" href="#" aria-label="" data-aria-i18n="linkedin"><i class="ph ph-linkedin-logo"></i></a>
          </div>
        </div>
      </div>
      <div class="grid md:grid-cols-4 gap-8 mt-6">
        <div class="border-b md:border-none pb-4 md:pb-0">
          <h4 class="font-bold mb-2" data-i18n="useful_links">Liens Utiles</h4>
          <ul class="text-sm space-y-1">
            <li class="hover:text-[#a9cf46] transition-colors"><a href="about.php" data-i18n="about">À Propos</a></li>
            <li class="hover:text-[#a9cf46] transition-colors"><a href="poles.html" data-i18n="poles">Nos Pôles</a></li>
            <li class="hover:text-[#a9cf46] transition-colors"><a href="projets.html" data-i18n="projects">Nos Projets</a></li>
        </ul>
        </div>
        <div class="border-b md:border-none pb-4 md:pb-0">
          <h4 class="font-bold mb-2" data-i18n="useful_links">Liens Utiles</h4> 
          <ul class="text-sm space-y-1">
            <li class="hover:text-[#a9cf46] transition-colors"><a href="blog.php" data-i18n="blog">Blog</a></li>
            <li class="hover:text-[#a9cf46] transition-colors"><a href="portfolios.php" data-i18n="portfolios">Portfolio</a></li>
            <li class="hover:text-[#a9cf46] transition-colors"><a href="contact.html" data-i18n="contact">Contact</a></li>
          </ul>
        </div>
        <div class="border-b md:border-none pb-4 md:pb-0">
          <h4 class="font-bold mb-2" data-i18n="others">Autres</h4>
          <ul class="text-sm space-y-1">
            <li class="hover:text-[#a9cf46] transition-colors"><a href="recrutement.html" data-i18n="join_us">Nous Rejoindre</a></li>
            <li class="hover:text-[#a9cf46] transition-colors"><a href="consultants.php" data-i18n="consultant_recruitment">Recrutement Consultant</a></li>
            <li class="hover:text-[#a9cf46] transition-colors"><a href="ranch.php" data-i18n="ranch">Notre Ranch</a></li>
          </ul>
        </div>
        <div>
          <h4 class="font-bold mb-2" data-i18n="newsletter">Newsletter</h4>
          <form id="newsletter-form" class="mt-6 px-4">
            <input type="email" name="newsletter_email" placeholder="Votre email" data-i18n-placeholder="your_email" required class="w-full px-3 py-2 rounded mb-2 text-black">
            <button type="submit" class="bg-[#a9cf46] w-full py-2 rounded" data-i18n="subscribe">S'inscrire</button>
            <p id="newsletter-msg" class="text-green-600 mt-2 hidden" data-i18n="newsletter_success">Merci pour votre inscription !</p>
          </form>
        </div>
      </div>
      <div class="flex flex-col md:flex-row justify-between items-center border-t border-b border-white/20 py-6 mt-6 text-xs text-white/60">
        <!-- Numéro de téléphone à gauche -->
        <div class="mb-2 md:mb-0">
          <a href="tel:+2252722332336" class="text-white font-bold hover:text-[#a9cf46] transition-colors">
            +225 27 22 332 336
          </a>
        </div>
        <!-- Copyright à droite -->
        <div data-i18n="copyright">
          © 2025 Agriforland. Tous droits réservés.
        </div>
      </div>
    </div>
  </footer>