<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\Project;
use App\Services\AiService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AiController extends Controller
{
    public function __construct(private AiService $ai) {}

    /**
     * POST /ai/generate
     * Main endpoint — receives prompt, returns AI-generated code + files.
     */
    public function generate(Request $request): JsonResponse
    {
        $request->validate([
            'prompt'     => 'required|string|min:5|max:10000',
            'project_id' => 'required|exists:projects,id',
            'model'      => 'nullable|in:auto,claude,deepseek',
        ]);

        $project = Project::where('id', $request->project_id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        // Build context from project and chat history
        $context = $this->buildContext($project);

        // Call AI
        $result = $this->ai->generate(
            prompt: $request->prompt,
            model: $request->model ?? 'auto',
            context: $context
        );

        if (!$result['success']) {
            return response()->json(['error' => $result['content']], 422);
        }

        // Save chat messages
        ChatMessage::create([
            'project_id' => $project->id,
            'user_id'    => auth()->id(),
            'role'       => 'user',
            'content'    => $request->prompt,
        ]);

        ChatMessage::create([
            'project_id'     => $project->id,
            'user_id'        => auth()->id(),
            'role'           => 'assistant',
            'content'        => $result['content'],
            'model_used'     => $result['model'],
            'tokens_used'    => ($result['tokens']['input_tokens'] ?? 0) + ($result['tokens']['output_tokens'] ?? 0),
            'files_generated' => array_column($result['files'], 'path'),
        ]);

        // Auto-save generated files to project
        if (!empty($result['files'])) {
            $this->saveGeneratedFiles($project, $result['files']);
            $project->update(['status' => 'ready']);
        }

        return response()->json([
            'success'    => true,
            'content'    => $result['content'],
            'model'      => $result['model'],
            'files'      => $result['files'],
            'tokens'     => $result['tokens'],
            'file_count' => count($result['files']),
        ]);
    }

    /**
     * POST /ai/fix
     * Fix a bug or error — pass error message + file content.
     */
    public function fix(Request $request): JsonResponse
    {
        $request->validate([
            'error'      => 'required|string',
            'code'       => 'nullable|string',
            'project_id' => 'required|exists:projects,id',
        ]);

        $prompt = "Fix this error:\n\n**Error:**\n{$request->error}\n\n";
        if ($request->code) {
            $prompt .= "**Code:**\n```php\n{$request->code}\n```";
        }
        $prompt .= "\n\nProvide the corrected code with explanation.";

        $project = Project::findOrFail($request->project_id);
        $result  = $this->ai->generate($prompt, 'claude', $this->buildContext($project));

        return response()->json($result);
    }

    /**
     * POST /ai/explain
     * Explain code in plain English.
     */
    public function explain(Request $request): JsonResponse
    {
        $request->validate(['code' => 'required|string', 'project_id' => 'required']);

        $prompt = "Explain this code in simple, plain English. Be concise:\n\n```\n{$request->code}\n```";
        $result = $this->ai->generate($prompt, 'deepseek', []);

        return response()->json(['explanation' => $result['content']]);
    }

    /**
     * POST /ai/seo
     * Generate SEO meta tags, sitemap hints, schema markup for a page.
     */
    public function generateSeo(Request $request): JsonResponse
    {
        $request->validate([
            'page_title'   => 'required|string',
            'page_content' => 'required|string',
            'project_id'   => 'required',
        ]);

        $prompt = <<<PROMPT
Generate complete SEO optimization for a Laravel Blade page:

Page Title: {$request->page_title}
Content: {$request->page_content}

Provide:
1. Meta title and description (optimized)
2. Open Graph tags (og:title, og:description, og:image placeholder)
3. Twitter card tags
4. JSON-LD Schema markup (WebPage or Article)
5. Laravel Blade code to include all of the above

Format as a Blade partial file: resources/views/partials/seo.blade.php
PROMPT;

        $result = $this->ai->generate($prompt, 'deepseek', []);
        return response()->json(['content' => $result['content'], 'files' => $result['files']]);
    }

    /**
     * GET /ai/chat/{project}
     * Return full chat history for a project.
     */
    public function chatHistory(Project $project): JsonResponse
    {
        $this->authorize('view', $project);

        $messages = $project->chatHistory()
            ->select('id', 'role', 'content', 'model_used', 'tokens_used', 'created_at')
            ->get();

        return response()->json($messages);
    }

    /**
     * DELETE /ai/chat/{project}
     * Clear chat history.
     */
    public function clearChat(Project $project): JsonResponse
    {
        $this->authorize('update', $project);
        $project->chatHistory()->delete();
        return response()->json(['success' => true]);
    }

    // ─── PRIVATE ─────────────────────────────────────────────────

    private function buildContext(Project $project): array
    {
        // Last 10 messages as context
        $history = $project->chatHistory()
            ->latest()
            ->limit(10)
            ->get()
            ->reverse()
            ->map(fn($m) => ['role' => $m->role, 'content' => substr($m->content, 0, 500)])
            ->values()
            ->toArray();

        return [
            'project' => [
                'name'    => $project->name,
                'type'    => $project->type,
                'history' => $history,
            ],
        ];
    }

    private function saveGeneratedFiles(Project $project, array $files): void
    {
        foreach ($files as $file) {
            \App\Models\ProjectFile::updateOrCreate(
                ['project_id' => $project->id, 'path' => $file['path']],
                ['content' => $file['content'], 'generated_by_ai' => true]
            );
        }
    }
}
