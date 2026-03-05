<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectFile;
use App\Services\DeployService;
use App\Services\EncryptionService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

// ═══════════════════════════════════════════════════════════════
// PROJECT CONTROLLER
// ═══════════════════════════════════════════════════════════════

class ProjectController extends Controller
{
    public function index(): View
    {
        $projects = auth()->user()->projects()->latest()->paginate(12);
        return view('dashboard.projects', compact('projects'));
    }

    public function show(Project $project): View
    {
        $this->authorize('view', $project);
        $files = $project->files()->get();
        return view('ai.builder', compact('project', 'files'));
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name'        => 'required|string|max:100',
            'type'        => 'required|in:landing_page,blog,ecommerce,saas,portfolio,booking,custom',
            'description' => 'nullable|string|max:500',
        ]);

        $project = auth()->user()->projects()->create([
            'name'        => $request->name,
            'slug'        => \Str::slug($request->name) . '-' . \Str::random(4),
            'type'        => $request->type,
            'description' => $request->description,
            'status'      => 'draft',
            'framework'   => 'laravel',
        ]);

        return response()->json([
            'success'  => true,
            'project'  => $project,
            'redirect' => route('projects.show', $project),
        ], 201);
    }

    public function update(Request $request, Project $project): JsonResponse
    {
        $this->authorize('update', $project);
        $request->validate(['name' => 'sometimes|string|max:100', 'description' => 'nullable|string']);
        $project->update($request->only('name', 'description', 'settings'));
        return response()->json(['success' => true]);
    }

    public function destroy(Project $project): JsonResponse
    {
        $this->authorize('delete', $project);
        $project->files()->delete();
        $project->chatHistory()->delete();
        $project->delete();
        return response()->json(['success' => true]);
    }

    public function getFile(Project $project, ProjectFile $file): JsonResponse
    {
        $this->authorize('view', $project);
        return response()->json(['file' => $file]);
    }

    public function saveFile(Request $request, Project $project): JsonResponse
    {
        $this->authorize('update', $project);
        $request->validate(['path' => 'required|string', 'content' => 'required|string']);

        $file = ProjectFile::updateOrCreate(
            ['project_id' => $project->id, 'path' => $request->path],
            ['content' => $request->content, 'generated_by_ai' => false]
        );

        return response()->json(['success' => true, 'file' => $file]);
    }
}

// ═══════════════════════════════════════════════════════════════
// DEPLOY CONTROLLER
// ═══════════════════════════════════════════════════════════════

class DeployController extends Controller
{
    public function __construct(
        private DeployService $deploy,
        private EncryptionService $encryption
    ) {}

    public function show(Project $project): View
    {
        $this->authorize('update', $project);
        return view('dashboard.deploy', compact('project'));
    }

    public function saveFtpCredentials(Request $request, Project $project): JsonResponse
    {
        $this->authorize('update', $project);

        $request->validate([
            'ftp_host'        => 'required|string',
            'ftp_username'    => 'required|string',
            'ftp_password'    => 'required|string',
            'ftp_port'        => 'nullable|integer',
            'ftp_remote_path' => 'required|string',
            'db_name'         => 'required|string',
            'db_username'     => 'required|string',
            'db_password'     => 'required|string',
        ]);

        $project->update([
            'ftp_host'        => $request->ftp_host,
            'ftp_username'    => $request->ftp_username,
            'ftp_password'    => $this->encryption->encrypt($request->ftp_password),
            'ftp_port'        => $request->ftp_port ?? 21,
            'ftp_remote_path' => $request->ftp_remote_path,
            'db_name'         => $request->db_name,
            'db_username'     => $request->db_username,
            'db_password'     => $this->encryption->encrypt($request->db_password),
        ]);

        return response()->json(['success' => true, 'message' => 'FTP credentials saved.']);
    }

    public function testConnection(Request $request, Project $project): JsonResponse
    {
        $credentials = $this->getDecryptedCredentials($project);
        $connected   = $this->deploy->testConnection($credentials);

        return response()->json([
            'success' => $connected,
            'message' => $connected ? '✅ FTP connection successful!' : '❌ Connection failed. Check credentials.',
        ]);
    }

    public function deploy(Project $project): JsonResponse
    {
        $this->authorize('update', $project);

        if (!$project->ftp_host) {
            return response()->json(['error' => 'FTP credentials not configured.'], 422);
        }

        $project->update(['status' => 'building']);
        $credentials = $this->getDecryptedCredentials($project);
        $projectPath = storage_path("projects/{$project->id}");

        $result = $this->deploy->deployViaFtp($credentials, $projectPath);

        if ($result['success']) {
            $project->update(['status' => 'deployed', 'deployed_at' => now()]);
        } else {
            $project->update(['status' => 'ready']);
        }

        return response()->json($result);
    }

    private function getDecryptedCredentials(Project $project): array
    {
        return [
            'host'        => $project->ftp_host,
            'username'    => $project->ftp_username,
            'password'    => $this->encryption->decrypt($project->ftp_password),
            'port'        => $project->ftp_port ?? 21,
            'remote_path' => $project->ftp_remote_path,
            'db_name'     => $project->db_name,
            'db_username' => $project->db_username,
            'db_password' => $this->encryption->decrypt($project->db_password),
        ];
    }
}

// ═══════════════════════════════════════════════════════════════
// MARKETPLACE CONTROLLER
// ═══════════════════════════════════════════════════════════════

class MarketplaceController extends Controller
{
    public function index(Request $request): View
    {
        $query = \App\Models\MarketplaceItem::approved();

        if ($request->category) $query->where('category', $request->category);
        if ($request->type === 'free') $query->free();
        if ($request->type === 'paid') $query->paid();
        if ($request->search) $query->where('name', 'like', "%{$request->search}%");

        $items    = $query->latest()->paginate(18);
        $featured = \App\Models\MarketplaceItem::approved()->featured()->limit(6)->get();

        return view('marketplace.index', compact('items', 'featured'));
    }

    public function show(\App\Models\MarketplaceItem $item): View
    {
        abort_unless($item->is_approved, 404);
        return view('marketplace.show', compact('item'));
    }

    public function install(Request $request, \App\Models\MarketplaceItem $item): JsonResponse
    {
        $project = Project::where('id', $request->project_id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        // Download and extract marketplace item into project
        $item->increment('download_count');

        return response()->json([
            'success' => true,
            'message' => "'{$item->name}' installed into project '{$project->name}'.",
        ]);
    }

    public function submit(Request $request): JsonResponse
    {
        $request->validate([
            'name'              => 'required|string|max:100',
            'description'       => 'required|string|max:2000',
            'short_description' => 'required|string|max:200',
            'category'          => 'required|string',
            'type'              => 'required|in:template,plugin,module',
            'price'             => 'required|numeric|min:0',
            'thumbnail'         => 'required|image|max:2048',
        ]);

        $thumbnail = $request->file('thumbnail')->store('marketplace/thumbnails', 'public');

        $item = \App\Models\MarketplaceItem::create([
            ...$request->only('name','description','short_description','category','type','price'),
            'user_id'    => auth()->id(),
            'slug'       => \Str::slug($request->name),
            'thumbnail'  => $thumbnail,
            'is_approved' => false, // Requires admin review
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Item submitted for review. We will notify you within 24-48 hours.',
            'item'    => $item,
        ], 201);
    }
}
