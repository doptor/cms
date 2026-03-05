<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\AiUsageLog;

class AiService
{
    // Complexity keywords that route to Claude (premium model)
    private array $complexKeywords = [
        'architect', 'optimize', 'security', 'authentication',
        'payment', 'encrypt', 'complex', 'advanced', 'debug',
        'performance', 'scale', 'refactor', 'api design',
    ];

    /**
     * Main entry point — auto-routes or uses specified model.
     */
    public function generate(string $prompt, string $model = 'auto', array $context = []): array
    {
        $resolvedModel = $model === 'auto' ? $this->resolveModel($prompt) : $model;

        return match ($resolvedModel) {
            'claude'   => $this->callClaude($prompt, $context),
            'deepseek' => $this->callDeepSeek($prompt, $context),
            default    => $this->callDeepSeek($prompt, $context),
        };
    }

    /**
     * Smart routing: DeepSeek for most tasks, Claude for complex ones.
     */
    private function resolveModel(string $prompt): string
    {
        $promptLower = strtolower($prompt);
        $promptLength = strlen($prompt);

        // Long prompts or complex keywords → Claude
        if ($promptLength > 800) return 'claude';

        foreach ($this->complexKeywords as $keyword) {
            if (str_contains($promptLower, $keyword)) return 'claude';
        }

        return 'deepseek'; // Default: economical
    }

    /**
     * Call Anthropic Claude API.
     */
    public function callClaude(string $prompt, array $context = [], ?string $apiKey = null): array
    {
        $key = $apiKey ?? auth()->user()?->claude_api_key_decrypted ?? config('services.anthropic.key');

        if (empty($key)) {
            return $this->errorResponse('Claude API key not configured. Please add your key in Settings → API Keys.');
        }

        $systemPrompt = $this->buildSystemPrompt($context);

        try {
            $response = Http::withHeaders([
                'x-api-key'         => $key,
                'anthropic-version' => '2023-06-01',
                'Content-Type'      => 'application/json',
            ])->timeout(120)->post('https://api.anthropic.com/v1/messages', [
                'model'      => 'claude-sonnet-4-20250514',
                'max_tokens' => 8000,
                'system'     => $systemPrompt,
                'messages'   => [
                    ['role' => 'user', 'content' => $prompt],
                ],
            ]);

            if ($response->failed()) {
                return $this->errorResponse('Claude API error: ' . $response->body());
            }

            $data = $response->json();
            $content = $data['content'][0]['text'] ?? '';
            $tokens = $data['usage'] ?? [];

            $this->logUsage('claude', $tokens['input_tokens'] ?? 0, $tokens['output_tokens'] ?? 0);

            return [
                'success' => true,
                'model'   => 'claude-sonnet-4',
                'content' => $content,
                'tokens'  => $tokens,
                'files'   => $this->extractFiles($content),
            ];

        } catch (\Exception $e) {
            Log::error('Claude API Exception: ' . $e->getMessage());
            return $this->errorResponse('Claude connection failed: ' . $e->getMessage());
        }
    }

    /**
     * Call DeepSeek API.
     */
    public function callDeepSeek(string $prompt, array $context = [], ?string $apiKey = null): array
    {
        $key = $apiKey ?? auth()->user()?->deepseek_api_key_decrypted ?? config('services.deepseek.key');

        if (empty($key)) {
            return $this->errorResponse('DeepSeek API key not configured. Please add your key in Settings → API Keys.');
        }

        $systemPrompt = $this->buildSystemPrompt($context);

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $key,
                'Content-Type'  => 'application/json',
            ])->timeout(120)->post('https://api.deepseek.com/v1/chat/completions', [
                'model'       => 'deepseek-chat',
                'max_tokens'  => 8000,
                'temperature' => 0.2,
                'messages'    => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user',   'content' => $prompt],
                ],
            ]);

            if ($response->failed()) {
                return $this->errorResponse('DeepSeek API error: ' . $response->body());
            }

            $data    = $response->json();
            $content = $data['choices'][0]['message']['content'] ?? '';
            $tokens  = $data['usage'] ?? [];

            $this->logUsage('deepseek', $tokens['prompt_tokens'] ?? 0, $tokens['completion_tokens'] ?? 0);

            return [
                'success' => true,
                'model'   => 'deepseek-v3',
                'content' => $content,
                'tokens'  => $tokens,
                'files'   => $this->extractFiles($content),
            ];

        } catch (\Exception $e) {
            Log::error('DeepSeek API Exception: ' . $e->getMessage());
            return $this->errorResponse('DeepSeek connection failed: ' . $e->getMessage());
        }
    }

    /**
     * Build the system prompt for the AI — makes it a Laravel expert.
     */
    private function buildSystemPrompt(array $context = []): string
    {
        $projectContext = '';
        if (!empty($context['project'])) {
            $projectContext = "\n\nCurrent project context:\n" . json_encode($context['project'], JSON_PRETTY_PRINT);
        }

        return <<<PROMPT
You are RyaanCMS AI Developer — an expert full-stack Laravel developer.

## Your Role
You build complete, production-ready Laravel 11 applications for users on shared hosting (cPanel + MySQL + PHP 8.2).

## Your Capabilities
- Generate complete Laravel controllers, models, migrations, routes, and Blade views
- Write Tailwind CSS + Alpine.js frontend code (NO separate build step required)
- Create MySQL schemas optimized for shared hosting
- Fix bugs and explain errors in plain English
- Generate SEO-friendly HTML with meta tags, Open Graph, and Schema markup
- Write secure code: CSRF protection, input validation, XSS prevention, SQL injection prevention
- Optimize for shared hosting: file-based caching, minimal memory usage, no Redis/Octane required

## Output Format
When generating files, ALWAYS use this format so files can be auto-saved:
```filename:path/to/file.ext
[file content here]
```

Example:
```filename:app/Http/Controllers/HomeController.php
<?php
// code here
```

## Rules
1. Always use Laravel 11 syntax
2. Always include CSRF tokens in forms
3. Always validate all inputs with Form Requests
4. Use file-based cache (not Redis) for shared hosting compatibility
5. Use Tailwind CSS via CDN (no npm build required)
6. Write complete, working code — never use placeholders like "// add code here"
7. Always include error handling
8. Make UI modern, responsive, and mobile-first
{$projectContext}
PROMPT;
    }

    /**
     * Extract file blocks from AI response.
     * Format: ```filename:path/to/file.php ... ```
     */
    public function extractFiles(string $content): array
    {
        $files = [];
        preg_match_all('/```filename:([^\n]+)\n(.*?)```/s', $content, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $files[] = [
                'path'    => trim($match[1]),
                'content' => trim($match[2]),
            ];
        }

        return $files;
    }

    /**
     * Log AI token usage for the current user.
     */
    private function logUsage(string $model, int $inputTokens, int $outputTokens): void
    {
        try {
            AiUsageLog::create([
                'user_id'       => auth()->id(),
                'model'         => $model,
                'input_tokens'  => $inputTokens,
                'output_tokens' => $outputTokens,
                'total_tokens'  => $inputTokens + $outputTokens,
            ]);
        } catch (\Exception $e) {
            Log::warning('Failed to log AI usage: ' . $e->getMessage());
        }
    }

    private function errorResponse(string $message): array
    {
        return ['success' => false, 'content' => $message, 'files' => [], 'model' => null];
    }
}
