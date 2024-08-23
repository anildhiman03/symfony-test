<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class CsvUploadController extends AbstractController
{
    #[Route('/csv/upload', name: 'app_csv_upload')]
    public function uploadCsv(Request $request): JsonResponse
    {
        // Check if a file is uploaded
        $file = $request->files->get('file');
        if (!$file || $file->getClientOriginalExtension() !== 'csv') {
            return $this->json(['error' => 'Invalid or missing CSV file'], Response::HTTP_BAD_REQUEST);
        }

        // Define the directory to save uploaded files
        $uploadsDirectory = $this->getParameter('kernel.project_dir') . '/public/uploads';

        // Ensure the directory exists
        if (!is_dir($uploadsDirectory)) {
            mkdir($uploadsDirectory, 0777, true);
        }

        // Generate a unique filename and move the file
        $filename = uniqid() . '.' . $file->getClientOriginalExtension();

        try {
            $file->move($uploadsDirectory, $filename);
        } catch (FileException $e) {
            return $this->json(['error' => 'Failed to upload file'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        // Process the CSV file if needed
        // $csvData = array_map('str_getcsv', file($uploadsDirectory . '/' . $filename));

        return $this->json([
            'message' => 'File uploaded successfully',
            'filename' => $filename
        ], Response::HTTP_OK);
    }
}
