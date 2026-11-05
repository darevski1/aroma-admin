<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CenobotsClient;
use Illuminate\Http\Request;

/** Admin CRUD for the projects shown in the CenoBots "Проекти" slider. */
class CenobotsClientController extends Controller
{
    public function index()
    {
        $clients = CenobotsClient::with(['logo', 'photo'])->ordered()->get();

        return view('admin_pages.cenobots-clients.index', compact('clients'));
    }

    public function create()
    {
        return view('admin_pages.cenobots-clients.form', [
            'client' => new CenobotsClient([
                'is_active' => true,
                'sort_order' => (CenobotsClient::max('sort_order') ?? 0) + 1,
                'foot_text' => 'CenoBots автономно чистење',
            ]),
        ]);
    }

    public function store(Request $request)
    {
        CenobotsClient::create($this->validated($request));

        return redirect()->route('cenobots-clients.index')->with('success', 'Project added.');
    }

    public function edit(CenobotsClient $client)
    {
        return view('admin_pages.cenobots-clients.form', compact('client'));
    }

    public function update(Request $request, CenobotsClient $client)
    {
        $client->update($this->validated($request));

        return redirect()->route('cenobots-clients.index')->with('success', 'Project updated.');
    }

    public function destroy(CenobotsClient $client)
    {
        // Only the project record is removed; its logo and photo stay in the Media Library.
        $client->delete();

        return redirect()->route('cenobots-clients.index')->with('success', 'Project deleted.');
    }

    public function toggle(CenobotsClient $client)
    {
        $client->update(['is_active' => ! $client->is_active]);

        return response()->json(['success' => true, 'is_active' => $client->is_active]);
    }

    public function reorder(Request $request)
    {
        $data = $request->validate(['order' => 'required|array', 'order.*' => 'integer|exists:cenobots_clients,id']);

        foreach (array_values($data['order']) as $i => $id) {
            CenobotsClient::whereKey($id)->update(['sort_order' => $i + 1]);
        }

        return response()->json(['success' => true]);
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'logo_media_id' => 'nullable|integer|exists:media,id',
            'logo_alt' => 'nullable|string|max:255',
            'photo_media_id' => 'nullable|integer|exists:media,id',
            'website_url' => 'nullable|url:http,https|max:255',
            'description' => 'nullable|string|max:500',
            'foot_text' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer|min:0|max:100000',
        ]);

        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
