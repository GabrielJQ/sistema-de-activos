<?php

namespace App\Http\Controllers;

use App\Models\ImportTask;
use Illuminate\Http\Request;

class ImportProgressController extends Controller
{
    public function show(ImportTask $task)
    {
        // Ensure user owns the task or is admin
        if ($task->user_id !== auth()->id() && !auth()->user()->isSuperAdmin()) {
            abort(403);
        }

        return view('assets.import_progress', compact('task'));
    }

    public function status(ImportTask $task)
    {
        if ($task->user_id !== auth()->id() && !auth()->user()->isSuperAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json([
            'status' => $task->status,
            'total_rows' => $task->total_rows,
            'processed_rows' => $task->processed_rows,
            'percentage' => $task->total_rows > 0 ? round(($task->processed_rows / $task->total_rows) * 100) : 0,
            'errors' => $task->errors,
        ]);
    }

    public function cancel(ImportTask $task)
    {
        if ($task->user_id !== auth()->id() && !auth()->user()->isSuperAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if ($task->status === 'processing' || $task->status === 'pending') {
            $task->update(['status' => 'canceled']);
            return response()->json(['message' => 'Importación cancelada correctamente']);
        }

        return response()->json(['error' => 'No se puede cancelar una importación en estado ' . $task->status], 400);
    }
}
