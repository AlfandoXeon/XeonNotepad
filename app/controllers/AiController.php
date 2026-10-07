<?php
declare(strict_types=1);

/**
 * AiController — Handles Xeon AI generation requests via Groq API.
 */
class AiController extends Controller
{
    /**
     * AJAX endpoint: POST /ai/generate
     * Generates or refines note content using Groq LLM.
     */
    public function generate(): void
    {
        $this->requireAuth();

        // Verify CSRF
        $token = $_POST['csrf_token'] ?? '';
        if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
            $this->json(['success' => false, 'message' => 'Security token invalid. Please refresh the page.'], 403);
        }

        $prompt         = trim($_POST['prompt'] ?? '');
        $action         = trim($_POST['action'] ?? 'generate');
        $templateType   = trim($_POST['template_type'] ?? '');
        $tone           = trim($_POST['tone'] ?? 'balanced');
        $contextText    = trim($_POST['context_text'] ?? '');
        $existingTitle  = trim(strip_tags($_POST['existing_title'] ?? ''));
        $customApiKey   = trim($_POST['custom_api_key'] ?? '');

        if ($action !== 'template' && $prompt === '' && $contextText === '') {
            $this->json(['success' => false, 'message' => 'Please provide a topic or prompt for Xeon AI.'], 422);
        }

        $service = new GroqService();
        $result  = $service->generate($prompt, [
            'action'         => $action,
            'template_type'  => $templateType,
            'tone'           => $tone,
            'context_text'   => $contextText,
            'existing_title' => $existingTitle,
            'custom_api_key' => $customApiKey,
        ]);

        if (!$result['success']) {
            $this->json(['success' => false, 'message' => $result['error'] ?? 'Generation failed.'], 400);
        }

        $this->json([
            'success' => true,
            'title'   => $result['title'],
            'content' => $result['content'],
            'model'   => $result['model'],
        ]);
    }

    /**
     * AJAX endpoint: POST /ai/create-note
     * Generates content and automatically saves it as a new encrypted note.
     */
    public function createNote(): void
    {
        $this->requireAuth();

        $token = $_POST['csrf_token'] ?? '';
        if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
            $this->json(['success' => false, 'message' => 'Security token invalid. Please refresh the page.'], 403);
        }

        $user           = $this->currentUser();
        $prompt         = trim($_POST['prompt'] ?? '');
        $templateType   = trim($_POST['template_type'] ?? '');
        $tone           = trim($_POST['tone'] ?? 'balanced');
        $customApiKey   = trim($_POST['custom_api_key'] ?? '');

        if ($prompt === '' && $templateType === '') {
            $this->json(['success' => false, 'message' => 'Please provide a topic or choose a template.'], 422);
        }

        $service = new GroqService();
        $result  = $service->generate($prompt, [
            'action'         => $templateType !== '' ? 'template' : 'generate',
            'template_type'  => $templateType,
            'tone'           => $tone,
            'custom_api_key' => $customApiKey,
        ]);

        if (!$result['success']) {
            $this->json(['success' => false, 'message' => $result['error'] ?? 'Generation failed.'], 400);
        }

        $title   = $result['title'] ?: 'AI Generated Note';
        $content = $result['content'];

        try {
            $noteModel    = new Note();
            $historyModel = new NoteHistory();

            $newId = $noteModel->create($user['id'], $title, $content);
            $historyModel->log($newId, $user['id'], 'created', 'Note created with Xeon AI.');

            $this->json([
                'success' => true,
                'noteId'  => $newId,
                'title'   => $title,
                'message' => 'Note created successfully!',
            ]);
        } catch (Throwable $e) {
            error_log('[AiController::createNote] ' . $e->getMessage());
            $this->json(['success' => false, 'message' => 'Failed to save new note.'], 500);
        }
    }
}
