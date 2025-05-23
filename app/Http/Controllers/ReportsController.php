<?php

namespace k1fl1k\joyart\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use k1fl1k\joyart\Models\Artwork;
use k1fl1k\joyart\Models\Report;
use Illuminate\Support\Str;

class ReportsController extends Controller
{
    /**
     * Store a new report
     */
    public function store(Request $request, Artwork $artwork)
    {
        $request->validate([
            'reason' => 'required|string|in:inappropriate_content,copyright_violation,offensive,spam,other',
            'description' => 'nullable|string|max:1000',
        ]);

        // Check if user already reported this artwork
        $existingReport = Report::where('user_id', Auth::id())
            ->where('artwork_id', $artwork->id)
            ->where('status', Report::STATUS_PENDING)
            ->first();

        if ($existingReport) {
            return redirect()->back()->with('error', 'Ви вже поскаржились на цей пост. Ваша скарга розглядається.');
        }

        Report::create([
            'id' => (string) Str::ulid(),
            'artwork_id' => $artwork->id,
            'user_id' => Auth::id(),
            'reason' => $request->reason,
            'description' => $request->description,
            'status' => Report::STATUS_PENDING,
        ]);

        return redirect()->back()->with('status', 'Скаргу успішно надіслано.');
    }

    /**
     * Display a list of reports for admin
     */
    public function index(Request $request)
    {
        $query = Report::with(['artwork', 'user', 'artwork.user']);

        // Filter by status if provided
        if ($request->has('status') && in_array($request->status, [Report::STATUS_PENDING, Report::STATUS_REVIEWED, Report::STATUS_DISMISSED])) {
            $query->where('status', $request->status);
        }

        $reports = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('admin.reports.index', compact('reports'));
    }

    /**
     * Show a specific report
     */
    public function show(Report $report)
    {
        return view('admin.reports.show', compact('report'));
    }

    /**
     * Update report status
     */
    public function updateStatus(Request $request, Report $report)
    {
        $request->validate([
            'status' => 'required|in:pending,reviewed,dismissed',
        ]);

        $report->update([
            'status' => $request->status,
        ]);

        return redirect()->route('admin.reports.index')->with('status', 'Статус скарги оновлено.');
    }
}
