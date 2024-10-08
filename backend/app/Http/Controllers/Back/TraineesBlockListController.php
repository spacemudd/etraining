<?php

namespace App\Http\Controllers\Back;

use App\Exports\TraineesBlocklistExport;
use App\Http\Controllers\Controller;
use App\Imports\TraineesBlocklistImport;
use App\Models\Back\TraineeBlockList;
use App\Models\User;
use App\Notifications\TraineeRestoredNotification;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

class TraineesBlockListController extends Controller
{
    public function index()
    {
        return Inertia::render('Back/Trainees/BlockedList/Index', [
            'blocked_list' => TraineeBlockList::latest()->paginate(30),
        ]);
    }

    public function create()
    {
        return Inertia::render('Back/Trainees/BlockedList/Create');
    }

    /**
     * Store a new blocked trainee record.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'english_name' => 'required|string|max:255',
            'identity_number' => 'required|string|max:255',
            'phone' => 'nullable|string|max:255',
            'phone_additional' => 'nullable|string|max:255',
            'reason' => 'required|string|max:255',
        ]);

        TraineeBlockList::create($request->except('_token'));

        return redirect()->route('back.trainees.block-list.index');
    }

    /**
     *
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $trainee = TraineeBlockList::findOrFail($id);

        optional(User::where('email', 'sara@ptc-ksa.net')
            ->first())
            ->notify(new TraineeRestoredNotification($trainee->name, $trainee->phone, $trainee->email, auth()->user(), $trainee->deleted_remark));

        $trainee->delete();

        return response()->json([
            'success' => true,
        ]);
    }

    public function import()
    {
        return Inertia::render('Back/Trainees/BlockedList/Import');
    }

    public function importCsv(Request $request)
    {
        $request->validate([
            'excel_file' => 'required',
        ]);

        $path = request()->file('excel_file')->store('tmp');
        $filepath = storage_path('app').'/'.$path;

        Excel::import(new TraineesBlocklistImport(), $filepath);

        return response()->json([
            'success' => true,
        ]);
    }

    public function download()
    {
        return Excel::download(new TraineesBlocklistExport(), 'block_list_'.now()->format('d-m-Y').'.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }
}
