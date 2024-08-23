<?php

// src/Controller/FileUploadController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileUploadController extends AbstractController
{
    #[Route('/api/upload', name: 'upload_csv', methods: ['POST'])]
    public function upload(Request $request, SluggerInterface $slugger): JsonResponse
    {
        $file = $request->files->get('file');

        if (!$file || $file->getClientOriginalExtension() !== 'csv') {
            return new JsonResponse(['error' => 'Invalid file format. Only CSV is allowed.'], Response::HTTP_BAD_REQUEST);
        }

        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $slugger->slug($originalFilename);
        $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

        try {
            $file->move(
                $this->getParameter('csv_directory'),
                $newFilename
            );
        } catch (FileException $e) {
            return new JsonResponse(['error' => 'File upload error.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        // Parse the CSV
        $csvData = [];
        if (($handle = fopen($this->getParameter('csv_directory').'/'.$newFilename, 'r')) !== false) {
            while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                $csvData[] = $data;
            }
            fclose($handle);
        }

        return new JsonResponse(['data' => $csvData]);
    }
}
