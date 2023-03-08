<?php

// src/Form/DataTransformer/ImageUrlToFileTransformer.php

namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\HttpFoundation\File\File;

class ImageUrlToFileTransformer implements DataTransformerInterface
{
    /**
     * Transforms an image URL into a File object.
     *
     * @param  string|null $imageUrl The image URL
     * @return File|null   The transformed File object
     */
    public function transform($imageUrl)
    {
        if (!$imageUrl) {
            return null;
        }

        // If the image URL already points to a local file, return it as a File object
        if (strpos($imageUrl, 'http') !== 0) {
            return new File($imageUrl);
        }

        // Otherwise, download the image and return it as a temporary File object
        $tempFile = tempnam(sys_get_temp_dir(), 'image_');
        copy($imageUrl, $tempFile);

        return new File($tempFile);
    }

    /**
     * Transforms a File object back into an image URL.
     *
     * @param  File|null $file The File object
     * @return string|null    The URL of the image
     */
    public function reverseTransform($file)
    {
        if (!$file) {
            return null;
        }

        return $file->getPathname();
    }
}
