<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Back\FinancialAccount;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FinancialAccountsController extends Controller
{
    public function index()
    {
        return Inertia::render('Back/Finance/Accounts/Index', [
            'accounts' => FinancialAccount::paginate(20),
        ]);
    }
}
