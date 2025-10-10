// Empêchement de la redirection vers la route de suppression si l'administrateur ne confirme pas la suppression de l'évènement
const delEvent = document.querySelectorAll(".delete_event");
delEvent.forEach(link => {
    link.addEventListener('click', e => {
        if (!confirm("Voulez-vous vraiment supprimer cet enregistrement ?")) {
            e.preventDefault();
        }
    })
});
