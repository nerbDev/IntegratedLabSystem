<?php

namespace App\Http\Controllers;

use App\Models\ArchivedAppointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ArchiveController extends Controller
{
    public function index(Request $request)
    {
        $archives = ArchivedAppointment::with('result')
            ->when($request->search, fn($q) => $q->where(function($sub) use ($request) {
                $sub->where('first_name', 'like', '%'.$request->search.'%')
                    ->orWhere('last_name', 'like', '%'.$request->search.'%');
            }))
            ->when($request->date_from, fn($q) => $q->whereDate('archived_at', '>=', $request->date_from))
            ->when($request->date_to, fn($q) => $q->whereDate('archived_at', '<=', $request->date_to))
            ->orderBy('archived_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        return view('archive_records', compact('archives'));
    }

    public function download($id)
    {
        $archive = ArchivedAppointment::with('result')->findOrFail($id);

        if (!$archive->result || !$archive->result->file_path) {
            return redirect()->back()->with('error', 'Archived result file not found.');
        }

        if (!Storage::disk('public')->exists($archive->result->file_path)) {
            return redirect()->back()->with('error', 'File no longer exists on the server.');
        }

        return Storage::disk('public')->download(
            $archive->result->file_path,
            'Archived_Lab_Result_' . $archive->first_name . '_' . $archive->last_name . '.pdf'
        );
    }
}