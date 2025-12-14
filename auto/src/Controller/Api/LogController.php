<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/logs')]
class LogController extends AbstractController
{
    #[Route('', methods: ['GET'])]
    public function index(Request $request): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_DISPATCHER');

        $file = $request->query->get('file', 'app');
        $env = $this->getParameter('kernel.environment');

        $map = [
            'app' => sprintf('%s/%s.log', $this->getParameter('kernel.logs_dir'), $env),
            'assignments' => sprintf('%s/assignments.log', $this->getParameter('kernel.logs_dir')),
            'maintenance' => sprintf('%s/maintenance.log', $this->getParameter('kernel.logs_dir')),
        ];

        if (!isset($map[$file]) || !is_readable($map[$file])) {
            return $this->json(['error' => 'Log not found'], 404);
        }

        $lines = $request->query->getInt('lines', 200);
        $data = $this->tailFile($map[$file], $lines);

        return $this->json(['file' => $file, 'lines' => $lines, 'data' => $data]);
    }

    private function tailFile(string $path, int $lines = 200): array
    {
        $fp = fopen($path, 'r');
        if (!$fp) return [];

        $pos = -1;
        $currentLines = [];
        $buffer = '';
        fseek($fp, 0, SEEK_END);
        $fileSize = ftell($fp);

        while (count($currentLines) <= $lines && abs($pos) < $fileSize) {
            fseek($fp, $pos, SEEK_END);
            $char = fgetc($fp);
            if ($char === "\n") {
                array_unshift($currentLines, $buffer);
                $buffer = '';
            } else {
                $buffer = $char . $buffer;
            }
            $pos--;
        }

        if ($buffer !== '') array_unshift($currentLines, $buffer);
        fclose($fp);

        return array_slice($currentLines, -$lines);
    }
}
