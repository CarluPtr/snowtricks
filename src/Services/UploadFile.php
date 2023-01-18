<?php

namespace App\Services;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class UploadFile
{
    const POST_IMAGE_DIR = 'images/figures/';

    private $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    /**
     * @param UploadedFile $file
     * @return string
     * @throws \Exception
     */
    public function uploadImage(UploadedFile $file, string $dir): string
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

        $safeFilename = $this->slugger->slug($originalFilename);
        $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

        try {
            $file->move(
                $dir,
                $newFilename
            );
        } catch (FileException $e) {
            throw new \Exception('Error lors de l’upload');
        }

        return $newFilename;
    }
}
