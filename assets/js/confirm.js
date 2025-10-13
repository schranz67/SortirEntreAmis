// Empêchement de la redirection vers la route de suppression si l'administrateur ne confirme pas la suppression de l'évènement
const delEvent = document.querySelectorAll(".delete_event");
delEvent.forEach(link => {
    link.addEventListener('click', e => {
        $confirm_delete=confirm("Voulez-vous vraiment supprimer cet enregistrement ?");
        if (!$confirm_delete) { e.preventDefault(); }
    })
});

const confirmAdmin = document.querySelectorAll(".confirm_admin");
confirmAdmin.forEach(link => {
    link.addEventListener('click', e => {
        $confirm_admin=confirm("Voulez-vous vraiment faire passer cet utilisateur en administrateur ?");
        if (!$confirm_admin) { e.preventDefault(); }
    })
});

const confReg = document.querySelectorAll(".confirm_register");
confReg.forEach(link => {
    link.addEventListener('click', e => {
        $confirm_register=confirm("Voulez-vous vraiment vous inscrire à cette sortie ?");
        if (!$confirm_register) { e.preventDefault(); }
    })
});

const confUnReg = document.querySelectorAll(".confirm_unregister");
confUnReg.forEach(link => {
    link.addEventListener('click', e => {
        $confirm_unregister=confirm("Voulez-vous vraiment vous déinscrire de cette sortie ?");
        if (!$confirm_unregister) { e.preventDefault(); }
    })
});
