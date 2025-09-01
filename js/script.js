// script.js
document.addEventListener("DOMContentLoaded", function() {
    chargerProjets();
    ajouterEvenementsFiltres();
    ajouterEvenementsRecherche();
    ajouterEvenementsLangue();
});

let projets = [];
let projetsParPage = 9; // 9 projets par page comme demandé
let pageActuelle = 1;
let projetsFiltres = [];

// Fonction pour obtenir la langue actuelle
function getCurrentLanguage() {
    const languageSelector = document.querySelector('#language-selector, #language-selector-mobile');
    return languageSelector ? languageSelector.value : 'fr';
}

// Fonction pour obtenir le texte traduit
function getTranslatedText(textObject, lang = null) {
    if (!lang) lang = getCurrentLanguage();
    if (typeof textObject === 'object' && textObject !== null) {
        return textObject[lang] || textObject['fr'] || '';
    }
    return textObject || '';
}

// Fonction pour obtenir un tableau traduit
function getTranslatedArray(arrayObject, lang = null) {
    if (!lang) lang = getCurrentLanguage();
    if (typeof arrayObject === 'object' && arrayObject !== null && !Array.isArray(arrayObject)) {
        return arrayObject[lang] || arrayObject['fr'] || [];
    }
    return Array.isArray(arrayObject) ? arrayObject : [];
}

async function chargerProjets() {
    try {
        const response = await fetch("data/projects.json");
        projets = await response.json();
        projetsFiltres = [...projets];
        afficherProjets();
        genererPagination();
    } catch (error) {
        console.error("Erreur lors du chargement des projets :", error);
        const lang = getCurrentLanguage();
        const errorMessage = lang === 'fr' 
            ? "Erreur lors du chargement des projets. Veuillez réessayer."
            : "Error loading projects. Please try again.";
        document.getElementById("projects-container").innerHTML = 
            `<p class='text-center text-red-500'>${errorMessage}</p>`;
    }
}

function afficherProjets() {
    let container = document.getElementById("projects-container");
    container.innerHTML = "";

    let debut = (pageActuelle - 1) * projetsParPage;
    let fin = debut + projetsParPage;
    let projetsAffiches = projetsFiltres.slice(debut, fin);

    const lang = getCurrentLanguage();

    if (projetsAffiches.length === 0) {
        const noResultsMessage = lang === 'fr' 
            ? "Aucun projet trouvé. Essayez de modifier vos critères de recherche."
            : "No projects found. Try modifying your search criteria.";
        container.innerHTML = `
            <div class="col-span-full text-center py-10">
                <p class="text-gray-500">${noResultsMessage}</p>
            </div>
        `;
        return;
    }

    projetsAffiches.forEach(projet => {
        // Obtenir les textes traduits
        const titre = getTranslatedText(projet.titre, lang);
        const description = getTranslatedArray(projet.description, lang);
        const duree = getTranslatedText(projet.duree, lang);
        const motsCles = getTranslatedArray(projet.mots_cles, lang);
        
        const descriptionLimitee = description[0] ? description[0].substring(0, 120) + '...' : '';
        
        // Labels traduits
        const partnerLabel = lang === 'fr' ? 'Partenaire' : 'Partner';
        const startDateLabel = lang === 'fr' ? 'Date de démarrage' : 'Start Date';
        const zonesLabel = lang === 'fr' ? 'Zones' : 'Zones';
        const durationLabel = lang === 'fr' ? 'Durée' : 'Duration';
        const keywordsLabel = lang === 'fr' ? 'Mots-clés' : 'Keywords';
        const readMoreLabel = lang === 'fr' ? 'Lire plus' : 'Read more';
        
        let projetHTML = `
            <div class="project-card bg-white rounded-lg overflow-hidden shadow-md hover:shadow-lg transition-all duration-300 hover:-translate-y-1">
                <div class="relative">
                    <img src="${projet.image}" alt="${titre}" class="w-full h-48 object-cover">
                    <div class="absolute top-2 right-2">
                        ${projet.categorie.map(cat => 
                            `<span class="inline-block bg-[#a9cf46] text-white text-xs px-2 py-1 rounded-full mb-1 mr-1">${cat}</span>`
                        ).join('')}
                    </div>
                </div>
                
                <div class="p-4">
                    <h3 class="text-lg font-bold text-gray-800 mb-2 line-clamp-2">${titre}</h3>
                    <p class="text-gray-600 text-sm mb-3 line-clamp-3">${descriptionLimitee}</p>
                    
                    <div class="space-y-2 mb-4">
                        <div class="flex items-start gap-2">
                            <span class="text-xs font-semibold text-[#759916] min-w-max">${partnerLabel}:</span>
                            <span class="text-xs text-gray-600 flex-1">${projet.partenaire}</span>
                        </div>
                        
                        <div class="flex items-start gap-2">
                            <span class="text-xs font-semibold text-[#759916] min-w-max">${startDateLabel}:</span>
                            <span class="text-xs text-gray-600 flex-1">${projet.date_demarrage}</span>
                        </div>
                        
                        ${projet.zones && projet.zones.length > 0 ? `
                        <div class="flex items-start gap-2">
                            <span class="text-xs font-semibold text-[#759916] min-w-max">${zonesLabel}:</span>
                            <span class="text-xs text-gray-600 flex-1">${projet.zones.join(', ')}</span>
                        </div>
                        ` : ''}
                        
                        <div class="flex items-start gap-2">
                            <span class="text-xs font-semibold text-[#759916] min-w-max">${durationLabel}:</span>
                            <span class="text-xs text-gray-600 flex-1">${duree}</span>
                        </div>
                    </div>
                    
                    ${motsCles.length > 0 ? `
                    <div class="mb-4">
                        <span class="text-xs font-semibold text-[#759916] block mb-1">${keywordsLabel}:</span>
                        <div class="flex flex-wrap gap-1">
                            ${motsCles.slice(0, 4).map(mot => `
                                <span class="text-xs bg-gray-100 text-gray-700 px-2 py-1 rounded">
                                    ${mot}
                                </span>
                            `).join('')}
                            ${motsCles.length > 4 ? `
                                <span class="text-xs text-gray-500">+${motsCles.length - 4}</span>
                            ` : ''}
                        </div>
                    </div>
                    ` : ''}
                    
                    <!-- Bouton Voir plus qui redirige vers portfolio.php avec le paramètre slug -->
                    <div class="flex justify-between items-center">
                        <a href="portfolio.php?slug=${projet.slug}" 
                           class="inline-flex items-center gap-2 bg-[#a9cf46] hover:bg-[#93bc3d] text-white px-4 py-2 rounded text-sm font-medium transition-colors duration-200">
                            <span>${readMoreLabel}</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        `;
        container.innerHTML += projetHTML;
    });
}

function genererPagination() {
    const paginationContainer = document.getElementById("pagination");
    const totalPages = Math.ceil(projetsFiltres.length / projetsParPage);

    paginationContainer.innerHTML = ""; 

    if (totalPages <= 1) return; 

    // Bouton Précédent
    if (pageActuelle > 1) {
        const prevButton = document.createElement("button");
        prevButton.innerHTML = '<i class="fas fa-chevron-left"></i>';
        prevButton.classList.add("page-btn", "px-3", "py-1", "mx-1", "rounded-full", "border", "border-[#a9cf46]", "hover:bg-[#a9cf46]", "hover:text-white", "transition-colors");
        prevButton.addEventListener("click", function() {
            pageActuelle--;
            afficherProjets();
            genererPagination();
            document.getElementById('mes-projets').scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
        paginationContainer.appendChild(prevButton);
    }

    // Boutons de page
    for (let i = 1; i <= totalPages; i++) {
        const pageButton = document.createElement("button");
        pageButton.textContent = i;
        pageButton.classList.add("page-btn", "px-4", "py-1", "mx-1", "rounded-full", "transition-colors");

        if (i === pageActuelle) {
            pageButton.classList.add("bg-[#a9cf46]", "text-white");
        } else {
            pageButton.classList.add("border", "border-[#a9cf46]", "text-[#a9cf46]", "hover:bg-[#a9cf46]", "hover:text-white");
        }

        pageButton.addEventListener("click", function() {
            pageActuelle = i;
            afficherProjets();
            genererPagination();
            document.getElementById('mes-projets').scrollIntoView({ behavior: 'smooth', block: 'start' });
        });

        paginationContainer.appendChild(pageButton);
    }

    // Bouton Suivant
    if (pageActuelle < totalPages) {
        const nextButton = document.createElement("button");
        nextButton.innerHTML = '<i class="fas fa-chevron-right"></i>';
        nextButton.classList.add("page-btn", "px-3", "py-1", "mx-1", "rounded-full", "border", "border-[#a9cf46]", "hover:bg-[#a9cf46]", "hover:text-white", "transition-colors");
        nextButton.addEventListener("click", function() {
            pageActuelle++;
            afficherProjets();
            genererPagination();
            document.getElementById('mes-projets').scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
        paginationContainer.appendChild(nextButton);
    }
}

function filtrerCategorie(categorie) {
    const filterBtns = document.querySelectorAll('.filter-btn');
    filterBtns.forEach(btn => btn.classList.remove('active', 'bg-[#a9cf46]', 'text-white'));
    filterBtns.forEach(btn => btn.classList.add('bg-white', 'text-[#759916]'));

    const activeBtn = document.querySelector(`.filter-btn[data-category="${categorie}"]`);
    if (activeBtn) {
        activeBtn.classList.add('active', 'bg-[#a9cf46]', 'text-white');
        activeBtn.classList.remove('bg-white', 'text-[#759916]');
    }

    appliquerFiltres();
}

function filtrerProjets() {
    appliquerFiltres();
}

function appliquerFiltres() {
    const searchInput = document.getElementById('searchInput').value.toLowerCase().trim();
    const categorieActive = document.querySelector('.filter-btn.active')?.getAttribute('data-category') || 'all';
    const lang = getCurrentLanguage();

    projetsFiltres = projets.filter(projet => {
        // Filtre par catégorie
        const correspondCategorie = categorieActive === 'all' || projet.categorie.includes(categorieActive);
        
        // Filtre par recherche
        let correspondRecherche = true;
        if (searchInput) {
            const titre = getTranslatedText(projet.titre, lang).toLowerCase();
            const description = getTranslatedArray(projet.description, lang);
            const motsCles = getTranslatedArray(projet.mots_cles, lang);
            const partenaire = (projet.partenaire || '').toLowerCase();
            const zones = Array.isArray(projet.zones) ? projet.zones.join(' ').toLowerCase() : '';
            
            const descriptionText = description.join(' ').toLowerCase();
            const motsClesText = motsCles.join(' ').toLowerCase();
            
            correspondRecherche = titre.includes(searchInput) ||
                                descriptionText.includes(searchInput) ||
                                motsClesText.includes(searchInput) ||
                                partenaire.includes(searchInput) ||
                                zones.includes(searchInput);
        }
        
        return correspondCategorie && correspondRecherche;
    });

    pageActuelle = 1;
    afficherProjets();
    genererPagination();
}

function ajouterEvenementsFiltres() {
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const categorie = this.getAttribute('data-category');
            filtrerCategorie(categorie);
        });
    });
}

function ajouterEvenementsRecherche() {
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        // Recherche en temps réel avec délai
        let timeoutId;
        searchInput.addEventListener('input', function() {
            clearTimeout(timeoutId);
            timeoutId = setTimeout(filtrerProjets, 300);
        });
        
        // Recherche sur Entrée
        searchInput.addEventListener('keyup', function(e) {
            if (e.key === 'Enter') {
                clearTimeout(timeoutId);
                filtrerProjets();
            }
        });
    }
}

function ajouterEvenementsLangue() {
    const languageSelectors = document.querySelectorAll('#language-selector, #language-selector-mobile');
    languageSelectors.forEach(selector => {
        selector.addEventListener('change', function() {
            // Délai pour laisser le temps à la traduction de s'appliquer
            setTimeout(() => {
                afficherProjets();
                genererPagination();
            }, 100);
        });
    });
}

// Fonction pour ouvrir popup (si vous l'utilisez encore)
function ouvrirPopup(projetId) {
    // Votre code existant pour le popup
    const projet = projets.find(p => p.id === projetId);
    if (projet) {
        // Redirection vers la page portfolio avec le slug
        window.location.href = `portfolio.php?slug=${projet.slug}`;
    }
}

// CSS additionnels pour l'affichage
const additionalCSS = `
  .line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }
  
  .line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }
  
  .project-card {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
  }
  
  .project-card:hover {
    transform: translateY(-2px);
  }
  
  .min-w-max {
    min-width: max-content;
  }
  
  .page-btn {
    transition: all 0.2s ease-in-out;
  }
`;

// Ajouter le CSS à la page
if (!document.querySelector('#additional-project-styles')) {
    const styleSheet = document.createElement('style');
    styleSheet.id = 'additional-project-styles';
    styleSheet.textContent = additionalCSS;
    document.head.appendChild(styleSheet);
}