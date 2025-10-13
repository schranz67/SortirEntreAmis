/**
 * Fonction de confirmation avant action sur tous les éléments correspondants au sélecteur.
 *
 * @param {string} selector - Sélecteur CSS des éléments à confirmer
 * @param {string} message - Message de confirmation à afficher
 *
 */
function confirmation(selector, message) {
    const elements = document.querySelectorAll(selector);
    elements.forEach(el => {
        el.addEventListener('click', e => {
            if (!confirm(message)) {
                e.preventDefault();
            }
        });
    });
}

// Empêchement de la redirection vers la route de suppression si l'administrateur ne confirme pas la suppression de l'évènement
confirmation(".delete_event", "Voulez-vous vraiment supprimer cet enregistrement ?");

// Confirmation du passage d'un utilisateur en administrateur
confirmation(".confirm_admin", "Voulez-vous vraiment faire passer cet utilisateur en administrateur ?");

// Confirmation d'inscription à un évènement
confirmation(".confirm_register", "Voulez-vous vraiment vous inscrire à cette sortie ?");

// Confirmation de désinscription à un évènement
confirmation(".confirm_unregister", "Voulez-vous vraiment vous déinscrire de cette sortie ?");
