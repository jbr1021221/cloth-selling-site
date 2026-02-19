<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SMSCampaignController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return \App\Models\SMSCampaign::with('creator')->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'message' => 'required|string|max:160',
            'recipients' => 'required|array',
            'recipient_count' => 'required|integer',
            'status' => 'in:Draft,Sent,Failed',
            'sent_at' => 'nullable|date',
            'created_by' => 'nullable|exists:users,id',
        ]);

        return \App\Models\SMSCampaign::create($validated);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return \App\Models\SMSCampaign::with('creator')->findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $campaign = \App\Models\SMSCampaign::findOrFail($id);
        
        $validated = $request->validate([
            'message' => 'string|max:160',
            'recipients' => 'array',
            'recipient_count' => 'integer',
            'status' => 'in:Draft,Sent,Failed',
            'sent_at' => 'nullable|date',
        ]);

        $campaign->update($validated);
        return $campaign;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        \App\Models\SMSCampaign::destroy($id);
        return response()->noContent();
    }
}
