<?php
namespace App\Http\Controllers\Trainees;

use App\Http\Controllers\Controller;
use App\Models\Back\TraineeAgreement;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AgreementController extends Controller
{
    public function show()
    {
        $trainee = auth()->user()->trainee;
        return view('traineeAgreement', compact('trainee'));
    }

    public function accept(){

        $trainee = auth()->user()->trainee;
        $agreement = TraineeAgreement::firstOrNew(['trainee_id' => $trainee->id]);
        $agreement->accepted_at = now();
        $agreement->save();

        $trainee->trainee_agreement_id = $agreement->id;
        $trainee->save();
        return redirect()->route('dashboard');
    }   
    
}
