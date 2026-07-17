<?php

namespace App\Modules\History\Controllers;

use App\Http\Controllers\Controller;
use App\Models\GeneratedContent;
use App\Models\ActivityLog;
use App\Services\ExportService;
use App\Modules\History\Repositories\HistoryRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HistoryController extends Controller
{
    protected HistoryRepository $historyRepo;
    protected ExportService $exportService;

    public function __construct(HistoryRepository $historyRepo, ExportService $exportService)
    {
        $this->historyRepo = $historyRepo;
        $this->exportService = $exportService;
    }

    /**
     * Display the generation history log.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $search = $request->query('search', '');
        $toolType = $request->query('tool_type', '');
        
        $isFavorite = null;
        if ($request->has('favorite') && $request->query('favorite') !== '') {
            $isFavorite = filter_var($request->query('favorite'), FILTER_VALIDATE_BOOLEAN);
        }

        // Get paginated user history (12 items per page for card layout, or standard list)
        $history = $this->historyRepo->getUserHistory($user->id, $search, $toolType, $isFavorite, 12);

        // Get unique tool types for filtering dropdown
        $toolTypes = GeneratedContent::where('user_id', $user->id)
            ->distinct()
            ->pluck('tool_type')
            ->toArray();

        return view('history.index', compact('history', 'search', 'toolType', 'isFavorite', 'toolTypes'));
    }

    /**
     * Toggle the favorite state of a history entry.
     */
    public function toggleFavorite(int $id)
    {
        $user = Auth::user();
        $content = $this->historyRepo->toggleFavorite($id, $user->id);

        if ($content) {
            return response()->json([
                'success' => true,
                'is_favorite' => $content->is_favorite,
                'message' => $content->is_favorite ? 'Added to favorites!' : 'Removed from favorites!',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'History record not found.',
        ], 404);
    }

    /**
     * Delete a generation history log.
     */
    public function destroy(int $id)
    {
        $user = Auth::user();
        $content = GeneratedContent::where('id', $id)->where('user_id', $user->id)->firstOrFail();
        
        $content->delete();

        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'history_delete',
            'details' => 'Deleted generation record #' . $id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return back()->with('success', 'History record deleted successfully.');
    }

    /**
     * Download history entry as TXT.
     */
    public function downloadTxt(int $id)
    {
        $user = Auth::user();
        $content = GeneratedContent::where('id', $id)->where('user_id', $user->id)->firstOrFail();
        
        return $this->exportService->exportToTxt($content->title, $content->result_text);
    }

    /**
     * Download history entry as PDF.
     */
    public function downloadPdf(int $id)
    {
        $user = Auth::user();
        $content = GeneratedContent::where('id', $id)->where('user_id', $user->id)->firstOrFail();
        
        return $this->exportService->exportToPdf($content->title, $content->result_text);
    }
}
