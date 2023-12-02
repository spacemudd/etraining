<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\Back\TraineeWithdraw;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\QueryBuilder\QueryBuilder;

class OrdersWithdrawalRequestsController extends Controller
{
    public function index()
    {
        $requests = QueryBuilder::for(TraineeWithdraw::class)
            ->with(['trainee', 'company', 'media', 'approved_by'])
            ->defaultSort('-created_at')
            ->allowedSorts(['created_at', 'number', 'approved_at'])
            ->allowedFields(['trainee.identity_number', 'trainee.name', 'company.name_ar', 'number'])
            ->allowedFields(['trainee.identity_number', 'trainee.name', 'company.name_ar', 'number', 'approved'])
            ->allowedFilters(['trainee.identity_number', 'trainee.name', 'company.name_ar', 'number', 'approved'])
            ->allowedIncludes(['company', 'trainee', 'media', 'approved_by'])
            ->paginate()
            ->withQueryString();

        return Inertia::render('Orders/WithdrawalRequests/Index', [
            'withdrawal_requests' => $requests,
        ])->table(function ($table) {
            $table->disableGlobalSearch();
            $table->addSearchRows([
                'number' => __('words.application-number'),
                'company.name_ar' => __('words.company'),
                'trainee.name' => __('words.name'),
                'trainee.identity_number' => __('words.identity_number'),
            ]);
            $table->addFilter('approved', __('words.status'), [
                null => '-',
                0 => __('words.pending'),
                1 => __('words.approved'),
            ]);
        });
    }

    public function destroy($id)
    {
        $withdraw = TraineeWithdraw::findOrFail($id);
        $withdraw->delete();

        return redirect()->route('orders.withdrawal-requests.index');
    }

    /**
     * @param $id
     * @return \Inertia\Response
     */
    public function approve($id)
    {
        $withdraw = TraineeWithdraw::findOrFail($id);
        $withdraw->approved_at = now();
        $withdraw->approved_by_id = auth()->id();
        $withdraw->approved = true;
        $withdraw->save();
        return redirect()->route('orders.withdrawal-requests.index');
    }
}
