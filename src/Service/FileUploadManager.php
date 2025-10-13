<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileUploadManager
{
    private SluggerInterface $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    /**
     * Chargement d'une image et renvoi du chemin
     *
     * @param UploadedFile $uploadedFile
     * @param string $destination
     *
     * @return string $newFilename
     *
     */
    public function upload(UploadedFile $uploadedFile, string $destination): string
    {
        # Création d'un nouveau nom de fichier
        $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
        $newFilename = $this->slugger->slug($originalFilename) . '-' . uniqid() . '.' . $uploadedFile->guessExtension();

        # Déplacement du fichier vers le dossier défini
        $uploadedFile->move($destination, $newFilename);

        # Renvoi du nouveau nom du fichier
        return $newFilename;
    }

}
