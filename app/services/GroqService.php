<?php
declare(strict_types=1);

/**
 * GroqService — Client for Groq Cloud API
 * Powers Xeon AI intelligent text and note generation.
 */
class GroqService
{
    private const GROQ_ENDPOINT = 'https://api.groq.com/openai/v1/chat/completions';

    /**
     * Generate content via Groq LLM.
     *
     * @param string $prompt
     * @param array  $options
     * @return array{success: bool, title?: string, content?: string, error?: string, model?: string}
     */
    public function generate(string $prompt, array $options = []): array
    {
        $customKey = trim($options['custom_api_key'] ?? '');
        $apiKey = $customKey !== '' ? $customKey : (defined('GROQ_API_KEY') ? GROQ_API_KEY : '');

        if ($apiKey === '') {
            return [
                'success' => false,
                'error'   => 'Groq API Key is not configured. Please add GROQ_API_KEY in your .env file or provide your personal key in Settings.',
            ];
        }

        $model = !empty($options['model'])
            ? trim($options['model'])
            : (defined('GROQ_MODEL') && GROQ_MODEL !== '' ? GROQ_MODEL : 'llama-3.3-70b-versatile');

        $action       = $options['action'] ?? 'generate';
        $templateType = $options['template_type'] ?? '';
        $tone         = $options['tone'] ?? 'balanced';
        $contextText  = $options['context_text'] ?? '';
        $existingTitle= $options['existing_title'] ?? '';

        $messages = $this->buildMessages($prompt, $action, $templateType, $tone, $contextText, $existingTitle);

        $payload = [
            'model'       => $model,
            'messages'    => $messages,
            'temperature' => 0.65,
            'max_tokens'  => 3072,
        ];

        $ch = curl_init(self::GROQ_ENDPOINT);
        if ($ch === false) {
            return ['success' => false, 'error' => 'cURL failed to initialize on this server.'];
        }

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode($payload, JSON_UNESCAPED_UNICODE),
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $apiKey,
            ],
            CURLOPT_TIMEOUT        => 45,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);

        $response   = curl_exec($ch);
        $httpCode   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError  = curl_error($ch);
        curl_close($ch);

        if ($response === false) {
            return [
                'success' => false,
                'error'   => 'Connection to Groq API failed: ' . ($curlError ?: 'Unknown network error'),
            ];
        }

        $decoded = json_decode((string)$response, true);

        if ($httpCode !== 200) {
            $msg = $decoded['error']['message'] ?? ('Groq API returned HTTP error ' . $httpCode);
            if ($httpCode === 401) {
                $msg = 'Invalid Groq API Key. Please verify your GROQ_API_KEY in .env or Settings.';
            } elseif ($httpCode === 429) {
                $msg = 'Groq rate limit exceeded. Please wait a few moments and try again.';
            }
            return ['success' => false, 'error' => $msg];
        }

        $rawReply = $decoded['choices'][0]['message']['content'] ?? '';
        if (trim($rawReply) === '') {
            return ['success' => false, 'error' => 'Received an empty response from Xeon AI.'];
        }

        // Post-process response: separate title if provided as first H1 line, strip emojis
        $parsed = $this->parseResponse($rawReply, $existingTitle);

        return [
            'success' => true,
            'title'   => $parsed['title'],
            'content' => $parsed['content'],
            'model'   => $model,
        ];
    }

    /**
     * Construct prompt messages according to user intent.
     */
    private function buildMessages(
        string $prompt,
        string $action,
        string $templateType,
        string $tone,
        string $contextText,
        string $existingTitle
    ): array {
        $toneInstructions = [
            'casual'       => 'Keep the tone friendly, accessible, conversational, and encouraging.',
            'professional' => 'Maintain a polished, authoritative, structured, and business-ready tone.',
            'academic'     => 'Use rigorous, objective, explanatory, and academically sound tone with precise terminology.',
            'concise'      => 'Be extremely concise, direct, bulleted, and actionable. Omit fluff, greetings, and repetitive preamble.',
            'balanced'     => 'Clear, natural, helpful, engaging, and well-structured.',
        ];
        $selectedTone = $toneInstructions[$tone] ?? $toneInstructions['balanced'];

        $systemPrompt = <<<SYS
You are "Xeon AI", the intelligent note-taking and document generation assistant inside Xeon Notepad (created by @AlfandoXeon).
Your task is to produce high-value, logically organized, clear, and comprehensive notes.

IMPORTANT FORMAT RULES:
1. OUTPUT FORMAT: Respond in clean, elegant GitHub Flavored Markdown (GFM). Use headings (#, ##, ###), bullet lists, bold key terms, tables, blockquotes, and code fences where relevant.
2. ABSOLUTELY ZERO EMOJIS: Never use unicode emojis (such as 📝, 💡, 🚀, ✨, 📌). Use textual labels, ASCII bullets, or clear typography instead.
3. TITLE CONVENTION: Begin your output with a single top-level heading for the note title: "# Note Title". Immediately follow with the body content.
4. TONE: {$selectedTone}
5. LANGUAGE: Respond in the language used by the user's prompt (support Indonesian, English, or any requested language naturally).
SYS;

        $userPrompt = '';

        if ($action === 'template') {
            $templateDirectives = $this->getTemplateDirectives($templateType);
            $userPrompt = "Please generate a comprehensive note based on the following template requirements:\n\n"
                . "Template Type: {$templateType}\n"
                . "Directives:\n{$templateDirectives}\n\n";
            if (trim($prompt) !== '') {
                $userPrompt .= "User Context / Topic: {$prompt}\n";
            }
        } elseif ($action === 'summarize') {
            $userPrompt = "Please summarize the following note content concisely. Retain key takeaways, decisions, and essential details in clean bullet points.\n\n"
                . "Existing Note Content:\n" . ($contextText ?: $prompt);
        } elseif ($action === 'expand') {
            $userPrompt = "Please expand and elaborate on the following note. Add deeper explanations, examples, structured sections, and practical points while preserving the original intent.\n\n"
                . "Existing Note Content:\n" . ($contextText ?: $prompt);
        } elseif ($action === 'fix_grammar') {
            $userPrompt = "Please review, fix grammar/spelling, polish clarity, and refine the readability of the following text while keeping its meaning intact.\n\n"
                . "Original Content:\n" . ($contextText ?: $prompt);
        } elseif ($action === 'change_tone') {
            $userPrompt = "Rewrite the following note to strictly match the '{$tone}' tone: {$selectedTone}\n\n"
                . "Original Content:\n" . ($contextText ?: $prompt);
        } else {
            // General generation
            $userPrompt = $prompt;
            if (trim($contextText) !== '') {
                $userPrompt .= "\n\nReference / Existing Context:\n" . $contextText;
            }
        }

        return [
            ['role' => 'system', 'content' => $systemPrompt],
            ['role' => 'user',   'content' => $userPrompt],
        ];
    }

    /**
     * Get specific guidelines for built-in templates.
     */
    private function getTemplateDirectives(string $type): string
    {
        return match ($type) {
            'meeting_notes' => "Create structured Meeting Notes with: # Meeting Title\n- **Date & Time**, **Attendees**, **Meeting Goal**\n- ## Agenda & Discussion Topics\n- ## Key Decisions Made\n- ## Action Items (use `- [ ] Task name (@Assignee, Due Date)`)\n- ## Next Meeting Schedule",
            'study_summary' => "Create a Study / Lecture Summary note with: # Topic Summary\n- ## Executive Overview\n- ## Core Concepts & Key Definitions\n- ## In-Depth Breakdown\n- ## Practical Examples / Applications\n- ## Quick Review Questions / Flashcard Points",
            'project_plan'  => "Create a Project Plan & Roadmap with: # Project Name - Implementation Plan\n- ## Objective & High-level Scope\n- ## Deliverables & Success Metrics\n- ## Milestone Phases (Phase 1, Phase 2, etc.)\n- ## Resource Allocation & Timeline\n- ## Risk Mitigation & Dependencies",
            'technical_doc' => "Create Technical Documentation with: # System / Feature Documentation\n- ## Architecture & Overview\n- ## Prerequisites & Setup\n- ## Core Components & Workflow\n- ## Code Snippets / API Examples\n- ## Error Handling & Troubleshooting",
            'brainstorming' => "Create a Brainstorming & Ideation Outline with: # Brainstorming: [Topic]\n- ## Core Problem / Challenge Statement\n- ## Creative Idea Clusters (Idea A, Idea B, Idea C)\n- ## Pros & Cons Analysis\n- ## High-Impact Quick Wins\n- ## Recommended Immediate Next Steps",
            'todo_list'     => "Create a Structured To-Do & Task Checklist with: # Action Plan & Tasks\n- ## High Priority (Do Today)\n  - [ ] Task 1\n  - [ ] Task 2\n- ## Medium Priority (This Week)\n  - [ ] Task 3\n- ## Backlog / Future Tasks\n- ## Notes & Blockers",
            'formal_email'  => "Create a Professional Email / Business Letter with: # Subject: [Clear Subject Line]\n- Formal Greeting\n- Clear Opening & Purpose\n- Core Details / Value Proposition\n- Call to Action / Next Steps\n- Professional Sign-off",
            'creative_draft'=> "Create a Structured Article / Blog Draft with: # Engaging Headline\n- ## Hook & Introduction\n- ## Background & Context\n- ## Main Arguments / Subsections (with practical subheadings)\n- ## Key Takeaways\n- ## Concluding Thought",
            default         => "Structure as an organized document with clear headings, bold highlights, and clean paragraphs.",
        };
    }

    /**
     * Parse raw LLM output into title and markdown content, ensuring no emojis remain.
     */
    private function parseResponse(string $raw, string $existingTitle = ''): array
    {
        // Strip common unicode emojis if the LLM outputted any
        $cleaned = preg_replace('/[\x{1F300}-\x{1F64F}\x{1F680}-\x{1F6FF}\x{2600}-\x{26FF}\x{2700}-\x{27BF}\x{1F900}-\x{1F9FF}\x{1F1E0}-\x{1F1FF}]/u', '', $raw);

        $lines = explode("\n", trim($cleaned));
        $title = $existingTitle;
        $content = $cleaned;

        // Check if first line starts with "# "
        if (!empty($lines) && str_starts_with(trim($lines[0]), '# ')) {
            $extractedTitle = trim(substr(trim($lines[0]), 2));
            if ($extractedTitle !== '') {
                $title = $extractedTitle;
                // Remove the first H1 line from content so it isn't duplicated with note title
                array_shift($lines);
                // Also remove leading blank lines
                while (!empty($lines) && trim($lines[0]) === '') {
                    array_shift($lines);
                }
                $content = implode("\n", $lines);
            }
        }

        if ($title === '') {
            $title = 'AI Generated Note';
        }

        return [
            'title'   => $title,
            'content' => trim($content),
        ];
    }
}
