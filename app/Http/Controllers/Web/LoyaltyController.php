<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\LoyaltyPoint;
use Illuminate\Http\Request;

class LoyaltyController extends Controller
{
    /**
     * Customer facing points history
     */
    public function index()
    {
        $user = auth()->user();
        $points = $user->loyaltyPoints()->latest()->paginate(15);
        return view('loyalty.index', compact('user', 'points'));
    }

    /**
     * Admin viewing all points
     */
    public function adminIndex(Request $request)
    {
        $query = User::orderByDesc('total_points');

        if ($request->has('search')) {
            $s = $request->search;
            $query->where('name', 'like', "%$s%")
                  ->orWhere('email', 'like', "%$s%");
        }

        $users = $query->paginate(20);
        return view('admin.loyalty.index', compact('users'));
    }

    /**
     * Admin manually adds/deducts points
     */
    public function adminStore(Request $request, User $user)
    {
        $request->validate([
            'points' => 'required|integer|not_in:0',
            'description' => 'required|string|max:255',
        ]);

        $type = $request->points > 0 ? 'earned' : 'revoked';

        $user->addPoints($request->points, $type, 'Admin: ' . $request->description);

        return back()->with('success', 'Points updated successfully.');
    }
}
