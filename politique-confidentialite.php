
<?php
  include('admin/includes/db.php'); // Inclure la connexion à la base de données
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Découvrez la politique de confidentialité d'Agriforland, expliquant comment nous collectons, utilisons et protégeons vos données personnelles.">
  <title data-i18n="title">Politique de Confidentialité - Agriforland</title>
  <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@400;700&family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <!-- Phosphor Icons -->
  <link rel="stylesheet" href="https://unpkg.com/@phosphor-icons/web@2.0.3/src/bold/style.css">
  <link href="css/Style.css" rel="stylesheet">
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

<body class="bg-[#f6ffde] text-black font-roboto">
  <!-- Preloader -->
  <div id="preloader" class="fixed inset-0 bg-[#f6ffde] z-50 flex items-center justify-center">
    <div class="animate-triangle w-32 h-32">
      <img src="images/triangle-svgrepo-com.svg" loading="lazy" alt="Chargement..." class="w-full h-full object-contain triangle-img">
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
        alt="Logo Agriforland" 
        loading="lazy"
        class="h-8 sm:h-10"
      />
      <!-- Menu Burger pour mobile -->
      <button id="menu-toggle" class="md:hidden text-gray-700 focus:outline-none p-2" aria-label="Ouvrir le menu" aria-expanded="false">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
          <path d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
      </button>
      <!-- Boutons (desktop) -->
      <div class="hidden md:flex gap-3 items-center ml-auto">
        <!-- Language Selector -->
        <div class="relative inline-block text-left">
          <select id="language-selector" class="block appearance-none bg-white border border-gray-300 hover:border-gray-500 px-2 py-1 pr-8 rounded shadow leading-tight focus:outline-none focus:shadow-outline">
            <option value="fr" data-icon="images/fr.webp">Français</option>
            <option value="en" data-icon="images/en.webp">English</option>
          </select>
          <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2">
            <img id="language-icon" src="images/fr.webp" alt="Drapeau français" class="h-5 w-5">
          </div>
        </div>
        <a href="recrutement.html" class="bg-[#759916] text-white px-4 py-2 rounded-md hover:text-black hover:bg-[#ade126] transition text-sm whitespace-nowrap min-h-[48px] flex items-center" data-i18n="join_us" aria-label="Rejoindre Agriforland">
          Nous Rejoindre
        </a>
        <a href="contact.html" class="border border-gray-500 px-4 py-2 rounded-md hover:text-black hover:bg-[#f6ffde] transition text-sm whitespace-nowrap min-h-[48px] flex items-center" data-i18n="contact_us" aria-label="Contacter Agriforland">
          Nous Contacter
        </a>
      </div>
    </div>

    <!-- Navigation Desktop -->
    <div class="border-t border-gray-100 bg-[#f6ffde] hidden md:block">
      <nav class="max-w-7xl mx-auto px-4 py-3 flex justify-center gap-8 text-lg">
        <a href="index.php" class="nav-link hover:text-[#a9cf46] transition-colors" data-i18n="home" aria-label="Page d'accueil">Accueil</a>
        <a href="about.php" class="nav-link hover:text-[#a9cf46] transition-colors" data-i18n="about" aria-label="À propos d'Agriforland">À Propos</a>
        <a href="poles.html" class="nav-link hover:text-[#a9cf46] transition-colors" data-i18n="poles" aria-label="Nos pôles">Nos Pôles</a>
        <a href="projets.html" class="nav-link hover:text-[#a9cf46] transition-colors" data-i18n="projects" aria-label="Nos projets">Nos Projets</a>
        <a href="blog.php" class="nav-link hover:text-[#a9cf46] transition-colors" data-i18n="blog" aria-label="Blog d'Agriforland">Blog</a>
        <a href="portfolios.php" class="nav-link hover:text-[#a9cf46] transition-colors" data-i18n="portfolios" aria-label="Portfolios">Portfolios</a>
      </nav>
    </div>

    <!-- Menu Mobile -->
    <div id="mobile-menu" class="md:hidden hidden bg-[#f6ffde] px-4 pb-4">
      <nav class="flex flex-col gap-3 text-base">
        <a href="index.php" class="nav-link hover:text-[#a9cf46] transition py-2" data-i18n="home" aria-label="Page d'accueil">Accueil</a>
        <a href="about.php" class="nav-link hover:text-[#a9cf46] transition py-2" data-i18n="about" aria-label="À propos d'Agriforland">À Propos</a>
        <a href="poles.html" class="nav-link hover:text-[#a9cf46] transition py-2" data-i18n="poles" aria-label="Nos pôles">Nos Pôles</a>
        <a href="projets.html" class="nav-link hover:text-[#a9cf46] transition py-2" data-i18n="projects" aria-label="Nos projets">Nos Projets</a>
        <a href="blog.php" class="nav-link hover:text-[#a9cf46] transition py-2" data-i18n="blog" aria-label="Blog d'Agriforland">Blog</a>
        <a href="portfolios.php" class="nav-link hover:text-[#a9cf46] transition py-2" data-i18n="portfolios" aria-label="Portfolios">Portfolios</a>
      </nav>
      <div class="mt-4 flex flex-col gap-2">
        <!-- Language Selector for Mobile -->
        <div class="relative inline-block text-left">
          <select id="language-selector-mobile" class="block appearance-none bg-white border border-gray-300 hover:border-gray-500 px-2 py-1 pr-8 rounded shadow leading-tight focus:outline-none focus:shadow-outline w-full min-h-[48px]">
            <option value="fr" data-icon="images/fr.webp">Français</option>
            <option value="en" data-icon="images/en.webp">English</option>
          </select>
          <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2">
            <img id="language-icon-mobile" src="images/fr.webp" alt="Drapeau français" class="h-5 w-5">
          </div>
        </div>
        <a href="recrutement.html" class="bg-[#759916] text-white px-4 py-2 rounded-md text-center text-sm hover:bg-[#ade126] transition min-h-[48px] flex items-center justify-center" data-i18n="join_us" aria-label="Rejoindre Agriforland">Nous Rejoindre</a>
        <a href="contact.html" class="border border-gray-500 px-4 py-2 rounded-md text-center text-sm hover:bg-white transition min-h-[48px] flex items-center justify-center" data-i18n="contact_us" aria-label="Contacter Agriforland">Nous contacter</a>
      </div>
    </div>
  </header>

  <section class="relative">
    <img 
      src="cache/bgg-1-800.webp" 
      srcset="
        cache/bgg-1-480.webp 480w, 
        cache/bgg-1-800.webp 800w, 
        cache/bgg-1-1200.webp 1200w
      "
      sizes="(max-width: 600px) 480px, (max-width: 1000px) 800px, 1200px"
      alt="Fond de la bannière" 
      loading="lazy"
      class="w-full h-[200px] sm:h-[300px] md:h-[400px] object-cover"
    />
    <div class="absolute top-0 left-0 w-full h-full bg-black/50 flex flex-col justify-center items-center text-center text-white px-4">
      <h1 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-bold pb-4 sm:pb-6 font-kanit" data-i18n="privacy_policy_title">Politique de Confidentialité</h1>
      <p class="text-base sm:text-lg pb-4 sm:pb-6" data-i18n="privacy_subtitle">AGRIFORLAND s'engage à protéger vos données</p>
    </div>
  </section>

  <section class="bg-[#f5fad3] py-12 px-4 sm:px-6 md:px-16 text-black">
    <div class="max-w-4xl mx-auto bg-white p-4 sm:p-6 md:p-8 rounded-xl shadow-md">
      <div class="prose max-w-none">
        <h2 class="text-2xl font-bold text-green-700 mb-4" data-i18n="introduction_title">Introduction</h2>
        <p data-i18n="introduction_p1">Chez AGRIFORLAND, nous accordons une grande importance à la confidentialité de vos données personnelles. Cette politique de confidentialité explique comment nous collectons, utilisons, partageons et protégeons vos informations lorsque vous utilisez notre site web et nos services.</p>
        <p data-i18n="introduction_p2">En utilisant notre site ou en nous fournissant vos informations personnelles, vous acceptez les pratiques décrites dans cette politique.</p>

        <h2 class="text-2xl font-bold text-green-700 mb-4 mt-8" data-i18n="data_collected_title">Données collectées</h2>
        
        <h3 class="text-xl font-bold text-green-600 mb-2" data-i18n="voluntary_data_title">1. Données fournies volontairement</h3>
        <p data-i18n="voluntary_data_intro">Nous collectons les informations que vous nous fournissez directement, notamment lorsque vous :</p>
        <ul class="list-disc pl-6 mb-4">
          <li data-i18n="voluntary_data_1">Postulez à une offre d'emploi</li>
          <li data-i18n="voluntary_data_2">Vous inscrivez à notre newsletter</li>
          <li data-i18n="voluntary_data_3">Nous contactez via notre formulaire de contact</li>
        </ul>
        <p data-i18n="voluntary_data_info">Ces informations peuvent inclure :</p>
        <ul class="list-disc pl-6 mb-4">
          <li data-i18n="personal_info_1">Vos nom et prénoms</li>
          <li data-i18n="personal_info_2">Votre adresse e-mail</li>
          <li data-i18n="personal_info_3">Votre numéro de téléphone</li>
          <li data-i18n="personal_info_4">Votre curriculum vitae (CV)</li>
          <li data-i18n="personal_info_5">Votre lettre de motivation</li>
          <li data-i18n="personal_info_6">Vos diplômes et certifications</li>
          <li data-i18n="personal_info_7">Tout autre document que vous nous soumettez dans le cadre d'une candidature</li>
        </ul>

        <h3 class="text-xl font-bold text-green-600 mb-2" data-i18n="automatic_data_title">2. Données collectées automatiquement</h3>
        <p data-i18n="automatic_data_intro">Lorsque vous naviguez sur notre site, nous pouvons collecter automatiquement certaines informations techniques, notamment :</p>
        <ul class="list-disc pl-6 mb-4">
          <li data-i18n="technical_info_1">Votre adresse IP</li>
          <li data-i18n="technical_info_2">Le type et la version de votre navigateur</li>
          <li data-i18n="technical_info_3">Le type d'appareil utilisé</li>
          <li data-i18n="technical_info_4">Les pages que vous visitez</li>
          <li data-i18n="technical_info_5">La date et l'heure de vos visites</li>
          <li data-i18n="technical_info_6">Le temps passé sur nos pages</li>
        </ul>
        <p data-i18n="cookies_mention">Ces données sont collectées à l'aide de cookies et d'autres technologies similaires.</p>

        <h2 class="text-2xl font-bold text-green-700 mb-4 mt-8" data-i18n="data_usage_title">Utilisation des données</h2>
        <p data-i18n="data_usage_intro">Nous utilisons vos données personnelles pour :</p>
        <ul class="list-disc pl-6 mb-4">
          <li data-i18n="data_usage_1">Traiter et évaluer vos candidatures à nos offres d'emploi</li>
          <li data-i18n="data_usage_2">Vous contacter concernant votre candidature</li>
          <li data-i18n="data_usage_3">Vous envoyer notre newsletter si vous vous y êtes inscrit</li>
          <li data-i18n="data_usage_4">Répondre à vos demandes et questions</li>
          <li data-i18n="data_usage_5">Améliorer notre site web et nos services</li>
          <li data-i18n="data_usage_6">Se conformer à nos obligations légales</li>
        </ul>

        <h2 class="text-2xl font-bold text-green-700 mb-4 mt-8" data-i18n="legal_basis_title">Base légale du traitement</h2>
        <p data-i18n="legal_basis_intro">Nous traitons vos données personnelles sur les bases légales suivantes :</p>
        <ul class="list-disc pl-6 mb-4">
          <li><strong data-i18n="consent">Consentement</strong> : <span data-i18n="consent_desc">Lorsque vous vous inscrivez à notre newsletter ou que vous postulez à une offre d'emploi.</span></li>
          <li><strong data-i18n="legitimate_interest">Intérêt légitime</strong> : <span data-i18n="legitimate_interest_desc">Pour améliorer nos services et assurer la sécurité de notre site.</span></li>
          <li><strong data-i18n="legal_obligation">Obligation légale</strong> : <span data-i18n="legal_obligation_desc">Pour respecter les lois et réglementations applicables.</span></li>
        </ul>

        <h2 class="text-2xl font-bold text-green-700 mb-4 mt-8" data-i18n="data_retention_title">Conservation des données</h2>
        <p data-i18n="data_retention_intro">Nous conservons vos données personnelles aussi longtemps que nécessaire pour atteindre les objectifs pour lesquels elles ont été collectées, sauf si la loi exige ou permet une période de conservation plus longue.</p>
        <ul class="list-disc pl-6 mb-4">
          <li><strong data-i18n="application_data">Données de candidature</strong> : <span data-i18n="application_data_retention">Conservation pendant 2 ans après le dernier contact avec le candidat.</span></li>
          <li><strong data-i18n="newsletter_data">Données de newsletter</strong> : <span data-i18n="newsletter_data_retention">Conservation jusqu'à la désinscription.</span></li>
          <li><strong data-i18n="contact_data">Données de contact</strong> : <span data-i18n="contact_data_retention">Conservation pendant 3 ans après le dernier contact.</span></li>
        </ul>

        <h2 class="text-2xl font-bold text-green-700 mb-4 mt-8" data-i18n="data_sharing_title">Partage des données</h2>
        <p data-i18n="data_sharing_intro">Nous ne vendons pas vos données personnelles à des tiers. Nous pouvons partager vos informations dans les circonstances suivantes :</p>
        <ul class="list-disc pl-6 mb-4">
          <li data-i18n="data_sharing_1">Avec nos prestataires de services qui nous aident à exploiter notre site et nos services (hébergement, envoi d'emails, etc.)</li>
          <li data-i18n="data_sharing_2">Si la loi l'exige, en réponse à une procédure judiciaire ou pour protéger nos droits</li>
          <li data-i18n="data_sharing_3">Dans le cadre d'une fusion, acquisition ou vente d'actifs de notre entreprise</li>
        </ul>

        <h2 class="text-2xl font-bold text-green-700 mb-4 mt-8" data-i18n="data_security_title">Sécurité des données</h2>
        <p data-i18n="data_security_desc">Nous mettons en œuvre des mesures de sécurité techniques et organisationnelles appropriées pour protéger vos données personnelles contre la perte, l'accès non autorisé, la divulgation, l'altération et la destruction.</p>

        <h2 class="text-2xl font-bold text-green-700 mb-4 mt-8" data-i18n="your_rights_title">Vos droits</h2>
        <p data-i18n="your_rights_intro">Selon les lois applicables sur la protection des données, vous pouvez avoir les droits suivants :</p>
        <ul class="list-disc pl-6 mb-4">
          <li><strong data-i18n="right_access">Droit d'accès</strong> : <span data-i18n="right_access_desc">Demander une copie de vos données personnelles.</span></li>
          <li><strong data-i18n="right_rectification">Droit de rectification</strong> : <span data-i18n="right_rectification_desc">Corriger des informations inexactes ou incomplètes.</span></li>
          <li><strong data-i18n="right_erasure">Droit à l'effacement</strong> : <span data-i18n="right_erasure_desc">Demander la suppression de vos données personnelles.</span></li>
          <li><strong data-i18n="right_restriction">Droit à la limitation du traitement</strong> : <span data-i18n="right_restriction_desc">Demander la limitation du traitement de vos données.</span></li>
          <li><strong data-i18n="right_portability">Droit à la portabilité des données</strong> : <span data-i18n="right_portability_desc">Recevoir vos données dans un format structuré.</span></li>
          <li><strong data-i18n="right_object">Droit d'opposition</strong> : <span data-i18n="right_object_desc">S'opposer au traitement de vos données personnelles.</span></li>
          <li><strong data-i18n="right_withdraw">Droit de retirer votre consentement</strong> : <span data-i18n="right_withdraw_desc">à tout moment.</span></li>
        </ul>
        <p data-i18n="exercise_rights">Pour exercer ces droits, veuillez nous contacter à l'adresse indiquée ci-dessous.</p>

        <h2 class="text-2xl font-bold text-green-700 mb-4 mt-8" data-i18n="cookies_title">Cookies</h2>
        <p data-i18n="cookies_intro">Notre site utilise des cookies pour améliorer votre expérience de navigation. Un cookie est un petit fichier texte stocké sur votre appareil lorsque vous visitez notre site.</p>
        <p data-i18n="cookies_usage_intro">Nous utilisons des cookies pour :</p>
        <ul class="list-disc pl-6 mb-4">
          <li data-i18n="cookies_usage_1">Assurer le bon fonctionnement du site</li>
          <li data-i18n="cookies_usage_2">Mémoriser vos préférences</li>
          <li data-i18n="cookies_usage_3">Analyser la façon dont notre site est utilisé</li>
        </ul>
        <p data-i18n="cookies_control">Vous pouvez contrôler et gérer les cookies dans les paramètres de votre navigateur.</p>

        <h2 class="text-2xl font-bold text-green-700 mb-4 mt-8" data-i18n="policy_changes_title">Modifications de la politique de confidentialité</h2>
        <p data-i18n="policy_changes_desc">Nous pouvons mettre à jour cette politique de confidentialité périodiquement pour refléter les changements dans nos pratiques ou pour d'autres raisons opérationnelles, légales ou réglementaires. Nous vous encourageons à consulter régulièrement cette page pour vous tenir informé des changements.</p>

        <h2 class="text-2xl font-bold text-green-700 mb-4 mt-8" data-i18n="contact_title">Contact</h2>
        <p data-i18n="contact_desc">Si vous avez des questions, des préoccupations ou des demandes concernant cette politique de confidentialité ou le traitement de vos données personnelles, veuillez nous contacter à :</p>
        <div class="bg-gray-100 p-4 rounded-lg mb-4">
          <p class="font-bold">AGRIFORLAND</p>
          <p data-i18n="address">Adresse : Abidjan, Côte d'Ivoire</p>
          <p>Email : <a href="mailto:contact@agriforland.com" aria-label="Envoyer un email à Agriforland">contact@agriforland.com</a></p>
          <p data-i18n="phone_label">Téléphone : <a href="tel:+2252722332336" aria-label="Appeler Agriforland">+225 27 22 332 336</a></p>
        </div>

        <h2 class="text-2xl font-bold text-green-700 mb-4 mt-8" data-i18n="authority_title">Autorité de contrôle</h2>
        <p data-i18n="authority_desc">Vous avez le droit de déposer une plainte auprès de l'autorité de protection des données compétente si vous estimez que le traitement de vos données personnelles enfreint les lois applicables en matière de protection des données.</p>
      </div>
    </div>
  </section>

    <?php include __DIR__ . '/footer.php'; ?>


  <!-- Scripts -->
  <script>
    // Language translations
    const translations = {
      fr: {
        title: "Politique de Confidentialité - Agriforland",
        join_us: "Nous Rejoindre",
        contact_us: "Nous Contacter",
        home: "Accueil",
        about: "À Propos",
        poles: "Nos Pôles",
        projects: "Nos Projets",
        blog: "Blog",
        portfolios: "Portfolios",
        contact: "Contact",
        privacy_policy_title: "Politique de Confidentialité",
        privacy_subtitle: "AGRIFORLAND s'engage à protéger vos données",
        introduction_title: "Introduction",
        introduction_p1: "Chez AGRIFORLAND, nous accordons une grande importance à la confidentialité de vos données personnelles. Cette politique de confidentialité explique comment nous collectons, utilisons, partageons et protégeons vos informations lorsque vous utilisez notre site web et nos services.",
        introduction_p2: "En utilisant notre site ou en nous fournissant vos informations personnelles, vous acceptez les pratiques décrites dans cette politique.",
        data_collected_title: "Données collectées",
        voluntary_data_title: "1. Données fournies volontairement",
        voluntary_data_intro: "Nous collectons les informations que vous nous fournissez directement, notamment lorsque vous :",
        voluntary_data_1: "Postulez à une offre d'emploi",
        voluntary_data_2: "Vous inscrivez à notre newsletter",
        voluntary_data_3: "Nous contactez via notre formulaire de contact",
        voluntary_data_info: "Ces informations peuvent inclure :",
        personal_info_1: "Vos nom et prénoms",
        personal_info_2: "Votre adresse e-mail",
        personal_info_3: "Votre numéro de téléphone",
        personal_info_4: "Votre curriculum vitae (CV)",
        personal_info_5: "Votre lettre de motivation",
        personal_info_6: "Vos diplômes et certifications",
        personal_info_7: "Tout autre document que vous nous soumettez dans le cadre d'une candidature",
        automatic_data_title: "2. Données collectées automatiquement",
        automatic_data_intro: "Lorsque vous naviguez sur notre site, nous pouvons collecter automatiquement certaines informations techniques, notamment :",
        technical_info_1: "Votre adresse IP",
        technical_info_2: "Le type et la version de votre navigateur",
        technical_info_3: "Le type d'appareil utilisé",
        technical_info_4: "Les pages que vous visitez",
        technical_info_5: "La date et l'heure de vos visites",
        technical_info_6: "Le temps passé sur nos pages",
        cookies_mention: "Ces données sont collectées à l'aide de cookies et d'autres technologies similaires.",
        data_usage_title: "Utilisation des données",
        data_usage_intro: "Nous utilisons vos données personnelles pour :",
        data_usage_1: "Traiter et évaluer vos candidatures à nos offres d'emploi",
        data_usage_2: "Vous contacter concernant votre candidature",
        data_usage_3: "Vous envoyer notre newsletter si vous vous y êtes inscrit",
        data_usage_4: "Répondre à vos demandes et questions",
        data_usage_5: "Améliorer notre site web et nos services",
        data_usage_6: "Se conformer à nos obligations légales",
        legal_basis_title: "Base légale du traitement",
        legal_basis_intro: "Nous traitons vos données personnelles sur les bases légales suivantes :",
        consent: "Consentement",
        consent_desc: "Lorsque vous vous inscrivez à notre newsletter ou que vous postulez à une offre d'emploi.",
        legitimate_interest: "Intérêt légitime",
        legitimate_interest_desc: "Pour améliorer nos services et assurer la sécurité de notre site.",
        legal_obligation: "Obligation légale",
        legal_obligation_desc: "Pour respecter les lois et réglementations applicables.",
        data_retention_title: "Conservation des données",
        data_retention_intro: "Nous conservons vos données personnelles aussi longtemps que nécessaire pour atteindre les objectifs pour lesquels elles ont été collectées, sauf si la loi exige ou permet une période de conservation plus longue.",
        application_data: "Données de candidature",
        application_data_retention: "Conservation pendant 2 ans après le dernier contact avec le candidat.",
        newsletter_data: "Données de newsletter",
        newsletter_data_retention: "Conservation jusqu'à la désinscription.",
        contact_data: "Données de contact",
        contact_data_retention: "Conservation pendant 3 ans après le dernier contact.",
        data_sharing_title: "Partage des données",
        data_sharing_intro: "Nous ne vendons pas vos données personnelles à des tiers. Nous pouvons partager vos informations dans les circonstances suivantes :",
        data_sharing_1: "Avec nos prestataires de services qui nous aident à exploiter notre site et nos services (hébergement, envoi d'emails, etc.)",
        data_sharing_2: "Si la loi l'exige, en réponse à une procédure judiciaire ou pour protéger nos droits",
        data_sharing_3: "Dans le cadre d'une fusion, acquisition ou vente d'actifs de notre entreprise",
        data_security_title: "Sécurité des données",
        data_security_desc: "Nous mettons en œuvre des mesures de sécurité techniques et organisationnelles appropriées pour protéger vos données personnelles contre la perte, l'accès non autorisé, la divulgation, l'altération et la destruction.",
        your_rights_title: "Vos droits",
        your_rights_intro: "Selon les lois applicables sur la protection des données, vous pouvez avoir les droits suivants :",
        right_access: "Droit d'accès",
        right_access_desc: "Demander une copie de vos données personnelles.",
        right_rectification: "Droit de rectification",
        right_rectification_desc: "Corriger des informations inexactes ou incomplètes.",
        right_erasure: "Droit à l'effacement",
        right_erasure_desc: "Demander la suppression de vos données personnelles.",
        right_restriction: "Droit à la limitation du traitement",
        right_restriction_desc: "Demander la limitation du traitement de vos données.",
        right_portability: "Droit à la portabilité des données",
        right_portability_desc: "Recevoir vos données dans un format structuré.",
        right_object: "Droit d'opposition",
        right_object_desc: "S'opposer au traitement de vos données personnelles.",
        right_withdraw: "Droit de retirer votre consentement",
        right_withdraw_desc: "à tout moment.",
        exercise_rights: "Pour exercer ces droits, veuillez nous contacter à l'adresse indiquée ci-dessous.",
        cookies_title: "Cookies",
        cookies_intro: "Notre site utilise des cookies pour améliorer votre expérience de navigation. Un cookie est un petit fichier texte stocké sur votre appareil lorsque vous visitez notre site.",
        cookies_usage_intro: "Nous utilisons des cookies pour :",
        cookies_usage_1: "Assurer le bon fonctionnement du site",
        cookies_usage_2: "Mémoriser vos préférences",
        cookies_usage_3: "Analyser la façon dont notre site est utilisé",
        cookies_control: "Vous pouvez contrôler et gérer les cookies dans les paramètres de votre navigateur.",
        policy_changes_title: "Modifications de la politique de confidentialité",
        policy_changes_desc: "Nous pouvons mettre à jour cette politique de confidentialité périodiquement pour refléter les changements dans nos pratiques ou pour d'autres raisons opérationnelles, légales ou réglementaires. Nous vous encourageons à consulter régulièrement cette page pour vous tenir informé des changements.",
        contact_title: "Contact",
        contact_desc: "Si vous avez des questions, des préoccupations ou des demandes concernant cette politique de confidentialité ou le traitement de vos données personnelles, veuillez nous contacter à :",
        address: "Adresse : Abidjan, Côte d'Ivoire",
        phone_label: "Téléphone : +225 27 22 332 336",
        authority_title: "Autorité de contrôle",
        authority_desc: "Vous avez le droit de déposer une plainte auprès de l'autorité de protection des données compétente si vous estimez que le traitement de vos données personnelles enfreint les lois applicables en matière de protection des données.",
        follow_us: "SUIVEZ-NOUS",
        useful_links: "Liens Utiles",
        recruitment: "Recrutement",
        consultant_recruitment: "Recrutement consultant",
        our_group: "Notre Groupe",
        our_stories: "Nos Histoires",
        our_values: "Nos Valeurs",
        our_missions: "Notre Missions",
        our_teams: "Notre Equipe",
        our_ecofarms: "Notre Ecoferme",
        others: "Autres",
        agroforestry: "Agroforesterie",
        mapping: "Cartographie",
        our_partners: "Nos Partenaires",
        newsletter: "Newsletter",
        your_email: "Votre email",
        subscribe: "S'inscrire",
        newsletter_success: "Merci pour votre inscription !",
        copyright: "© 2025 Agriforland. Tous droits réservés."
      },
      en: {
        title: "Privacy Policy - Agriforland",
        join_us: "Join Us",
        contact_us: "Contact Us",
        home: "Home",
        about: "About",
        poles: "Our Divisions",
        projects: "Our Projects",
        blog: "Blog",
        portfolios: "Portfolios",
        contact: "Contact",
        privacy_policy_title: "Privacy Policy",
        privacy_subtitle: "AGRIFORLAND is committed to protecting your data",
        introduction_title: "Introduction",
        introduction_p1: "At AGRIFORLAND, we place great importance on the confidentiality of your personal data. This privacy policy explains how we collect, use, share and protect your information when you use our website and services.",
        introduction_p2: "By using our site or providing us with your personal information, you accept the practices described in this policy.",
        data_collected_title: "Data Collected",
        voluntary_data_title: "1. Data provided voluntarily",
        voluntary_data_intro: "We collect information that you provide to us directly, particularly when you:",
        voluntary_data_1: "Apply for a job offer",
        voluntary_data_2: "Subscribe to our newsletter",
        voluntary_data_3: "Contact us via our contact form",
        voluntary_data_info: "This information may include:",
        personal_info_1: "Your first and last names",
        personal_info_2: "Your email address",
        personal_info_3: "Your phone number",
        personal_info_4: "Your curriculum vitae (CV)",
        personal_info_5: "Your cover letter",
        personal_info_6: "Your diplomas and certifications",
        personal_info_7: "Any other document you submit to us as part of an application",
        automatic_data_title: "2. Automatically collected data",
        automatic_data_intro: "When you browse our site, we may automatically collect certain technical information, including:",
        technical_info_1: "Your IP address",
        technical_info_2: "Your browser type and version",
        technical_info_3: "The type of device used",
        technical_info_4: "The pages you visit",
        technical_info_5: "The date and time of your visits",
        technical_info_6: "Time spent on our pages",
        cookies_mention: "This data is collected using cookies and other similar technologies.",
        data_usage_title: "Use of Data",
        data_usage_intro: "We use your personal data to:",
        data_usage_1: "Process and evaluate your applications for our job offers",
        data_usage_2: "Contact you regarding your application",
        data_usage_3: "Send you our newsletter if you have subscribed to it",
        data_usage_4: "Respond to your requests and questions",
        data_usage_5: "Improve our website and services",
        data_usage_6: "Comply with our legal obligations",
        legal_basis_title: "Legal Basis for Processing",
        legal_basis_intro: "We process your personal data on the following legal bases:",
        consent: "Consent",
        consent_desc: "When you subscribe to our newsletter or apply for a job offer.",
        legitimate_interest: "Legitimate Interest",
        legitimate_interest_desc: "To improve our services and ensure the security of our site.",
        legal_obligation: "Legal Obligation",
        legal_obligation_desc: "To comply with applicable laws and regulations.",
        data_retention_title: "Data Retention",
        data_retention_intro: "We retain your personal data for as long as necessary to achieve the purposes for which it was collected, unless the law requires or permits a longer retention period.",
        application_data: "Application data",
        application_data_retention: "Retained for 2 years after the last contact with the candidate.",
        newsletter_data: "Newsletter data",
        newsletter_data_retention: "Retained until unsubscription.",
        contact_data: "Contact data",
        contact_data_retention: "Retained for 3 years after the last contact.",
        data_sharing_title: "Data Sharing",
        data_sharing_intro: "We do not sell your personal data to third parties. We may share your information in the following circumstances:",
        data_sharing_1: "With our service providers who help us operate our site and services (hosting, email sending, etc.)",
        data_sharing_2: "If required by law, in response to legal proceedings or to protect our rights",
        data_sharing_3: "In connection with a merger, acquisition or sale of assets of our company",
        data_security_title: "Data Security",
        data_security_desc: "We implement appropriate technical and organizational security measures to protect your personal data against loss, unauthorized access, disclosure, alteration and destruction.",
        your_rights_title: "Your Rights",
        your_rights_intro: "Under applicable data protection laws, you may have the following rights:",
        right_access: "Right of access",
        right_access_desc: "Request a copy of your personal data.",
        right_rectification: "Right of rectification",
        right_rectification_desc: "Correct inaccurate or incomplete information.",
        right_erasure: "Right to erasure",
        right_erasure_desc: "Request the deletion of your personal data.",
        right_restriction: "Right to restriction of processing",
        right_restriction_desc: "Request the restriction of processing of your data.",
        right_portability: "Right to data portability",
        right_portability_desc: "Receive your data in a structured format.",
        right_object: "Right to object",
        right_object_desc: "Object to the processing of your personal data.",
        right_withdraw: "Right to withdraw your consent",
        right_withdraw_desc: "at any time.",
        exercise_rights: "To exercise these rights, please contact us at the address indicated below.",
        cookies_title: "Cookies",
        cookies_intro: "Our site uses cookies to improve your browsing experience. A cookie is a small text file stored on your device when you visit our site.",
        cookies_usage_intro: "We use cookies to:",
        cookies_usage_1: "Ensure the proper functioning of the site",
        cookies_usage_2: "Remember your preferences",
        cookies_usage_3: "Analyze how our site is used",
        cookies_control: "You can control and manage cookies in your browser settings.",
        policy_changes_title: "Privacy Policy Changes",
        policy_changes_desc: "We may update this privacy policy periodically to reflect changes in our practices or for other operational, legal or regulatory reasons. We encourage you to regularly consult this page to stay informed of changes.",
        contact_title: "Contact",
        contact_desc: "If you have questions, concerns or requests regarding this privacy policy or the processing of your personal data, please contact us at:",
        address: "Address: Abidjan, Côte d'Ivoire",
        phone_label: "Phone: +225 27 22 332 336",
        authority_title: "Supervisory Authority",
        authority_desc: "You have the right to lodge a complaint with the competent data protection authority if you believe that the processing of your personal data violates applicable data protection laws.",
        follow_us: "FOLLOW US",
        useful_links: "Useful Links",
        recruitment: "Recruitment",
        consultant_recruitment: "Consultant Recruitment",
        our_group: "Our Group",
        our_stories: "Our Stories",
        our_values: "Our Values",
        our_missions: "Our Missions",
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
        copyright: "© 2025 Agriforland. All rights reserved."
      }
    };

    // Language switcher
    const languageSelectors = document.querySelectorAll('#language-selector, #language-selector-mobile');
    const languageIcons = document.querySelectorAll('#language-icon, #language-icon-mobile');

    function updateContent(lang) {
      document.querySelectorAll('[data-i18n]').forEach(element => {
        const key = element.getAttribute('data-i18n');
        element.textContent = translations[lang][key] || element.textContent;
      });
      document.querySelectorAll('[data-i18n-placeholder]').forEach(element => {
        const key = element.getAttribute('data-i18n-placeholder');
        element.placeholder = translations[lang][key] || element.placeholder;
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

    // Preloader
    window.addEventListener("load", function () {
      const preloader = document.getElementById('preloader');
      preloader.classList.add('opacity-0', 'pointer-events-none', 'transition-opacity', 'duration-500');
      setTimeout(() => preloader.remove(), 200);
    });

    // Menu mobile
    const toggle = document.getElementById('menu-toggle');
    const menu = document.getElementById('mobile-menu');
    toggle.addEventListener('click', () => {
      const isExpanded = menu.classList.toggle('hidden');
      toggle.setAttribute('aria-expanded', !isExpanded);
    });

    // Classe active pour la navigation
    const currentPage = window.location.pathname.split("/").pop();
    document.querySelectorAll('.nav-link').forEach(link => {
      const href = link.getAttribute('href');
      if (href === currentPage) {
        link.classList.add('text-[#a9cf46]', 'border-b-2', 'border-[#a9cf46]', 'font-semibold');
      }
    });

    // Newsletter form
    const newsletterForm = document.getElementById('newsletter-form');
    const newsletterMsg = document.getElementById('newsletter-msg');
    newsletterForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      const formData = new FormData(newsletterForm);
      const response = await fetch('back/newsletter.php', {
        method: 'POST',
        body: formData
      });
      if (response.ok) {
        newsletterMsg.classList.remove('hidden');
        newsletterMsg.classList.remove('text-red-600');
        newsletterMsg.classList.add('text-green-600');
        newsletterMsg.textContent = translations[languageSelectors[0].value].newsletter_success;
        newsletterForm.reset();
      } else {
        newsletterMsg.classList.remove('hidden');
        newsletterMsg.classList.remove('text-green-600');
        newsletterMsg.classList.add('text-red-600');
        newsletterMsg.textContent = "Erreur lors de l'inscription.";
      }
    });
  </script>
</body>
</html>
