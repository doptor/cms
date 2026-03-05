<?php

namespace App\Http\Controllers;

use App\Services\AiService;
use App\Services\EncryptionService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function __construct(
        private EncryptionService $encryption,
        private AiService $ai
    ) {}

    public function index(): View
    {
        $user = auth()->user();
        return view('dashboard.settings', [
            'claude_key_masked'   => $user->claude_api_key_masked,
            'deepseek_key_masked' => $user->deepseek_api_key_masked,
            'ai_routing_mode'     => $user->ai_routing_mode ?? 'auto',
            'has_claude'          => $user->hasClaudeKey(),
            'has_deepseek'        => $user->hasDeepSeekKey(),
            'today_tokens'        => $user->todayTokenUsage(),
            'month_tokens'        => $user->monthTokenUsage(),
        ]);
    }

    /**
     * POST /settings/api-keys
     * Save & verify API keys (encrypted before storage).
     */
    public function saveApiKeys(Request $request): JsonResponse
    {
        $request->validate([
            'claude_api_key'   => 'nullable|string|starts_with:sk-ant',
            'deepseek_api_key' => 'nullable|string|starts_with:sk-',
            'ai_routing_mode'  => 'required|in:auto,claude,deepseek',
        ]);

        $user   = auth()->user();
        $errors = [];
        $saved  = [];

        // Verify and save Claude key
        if ($request->filled('claude_api_key')) {
            $verified = $this->verifyClaude($request->claude_api_key);
            if ($verified) {
                $user->claude_api_key = $this->encryption->encrypt($request->claude_api_key);
                $saved[] = 'Claude';
            } else {
                $errors[] = 'Claude API key is invalid or expired.';
            }
        }

        // Verify and save DeepSeek key
        if ($request->filled('deepseek_api_key')) {
            $verified = $this->verifyDeepSeek($request->deepseek_api_key);
            if ($verified) {
                $user->deepseek_api_key = $this->encryption->encrypt($request->deepseek_api_key);
                $saved[] = 'DeepSeek';
            } else {
                $errors[] = 'DeepSeek API key is invalid or expired.';
            }
        }

        $user->ai_routing_mode = $request->ai_routing_mode;
        $user->save();

        if (!empty($errors)) {
            return response()->json(['success' => false, 'errors' => $errors], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'API keys saved: ' . implode(' & ', $saved),
            'saved'   => $saved,
        ]);
    }

    /**
     * DELETE /settings/api-keys/{provider}
     * Remove a stored API key.
     */
    public function removeApiKey(string $provider): JsonResponse
    {
        $user = auth()->user();

        match ($provider) {
            'claude'   => $user->update(['claude_api_key' => null]),
            'deepseek' => $user->update(['deepseek_api_key' => null]),
        };

        return response()->json(['success' => true, 'message' => ucfirst($provider) . ' key removed.']);
    }

    /**
     * POST /settings/profile
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $request->validate([
            'name'     => 'required|string|max:100',
            'timezone' => 'nullable|timezone',
            'locale'   => 'nullable|string|max:10',
        ]);

        auth()->user()->update($request->only('name', 'timezone', 'locale'));

        return response()->json(['success' => true, 'message' => 'Profile updated.']);
    }

    // ─── PRIVATE VERIFICATION ─────────────────────────────────────

    private function verifyClaude(string $key): bool
    {
        try {
            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'x-api-key'         => $key,
                'anthropic-version' => '2023-06-01',
            ])->timeout(10)->post('https://api.anthropic.com/v1/messages', [
                'model'      => 'claude-haiku-4-5-20251001',
                'max_tokens' => 10,
                'messages'   => [['role' => 'user', 'content' => 'Hi']],
            ]);

            return $response->status() !== 401;
        } catch (\Exception) {
            return false;
        }
    }

    private function verifyDeepSeek(string $key): bool
    {
        try {
            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'Authorization' => 'Bearer ' . $key,
            ])->timeout(10)->get('https://api.deepseek.com/v1/models');

            return $response->status() !== 401;
        } catch (\Exception) {
            return false;
        }
    }
}
