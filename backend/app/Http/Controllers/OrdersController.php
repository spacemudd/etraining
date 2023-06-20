<?php

namespace App\Http\Controllers;

use App\Mail\AcceptNewEmailMail;
use App\Mail\RejectNewEmailMail;
use App\Models\NewEmail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Spatie\QueryBuilder\QueryBuilder;

class OrdersController extends Controller
{
    public function index () {
        {
            return Inertia::render('Orders/Index');
        }
    }
    public function IT () {
        {
            return Inertia::render('Orders/IT/Index');
        }
    }
    public function HR () {
        {
            return Inertia::render('Orders/HR/Index');
        }
    }
    public function finance () {
        {
            return Inertia::render('Orders/Finance/Index');
        }
    }
    public function collection () {
        {
            return Inertia::render('Orders/Collection/Index');
        }
    }

    public function orders(){
//        $this->authorize('view-orders');

        $reports = QueryBuilder::for(NewEmail::class)
            ->allowedSorts(['number'])
            ->paginate()
            ->withQueryString();

        return Inertia::render('Orders/Orders', [
            'new_emails' => $reports,
        ])->table(function ($table) {
            $table->disableGlobalSearch();

            $table->addFilter('status', __('words.status'), [
                NewEmail::STATUS_PENDING => __('words.pending'),
                NewEmail::STATUS_APPROVED => __('words.approved'),
                NewEmail::STATUS_REJECTED => __('words.rejected'),
            ]);
        });

    }

    // IT Functions
    public function approveMail($id, Request $request)
    {
        $this->authorize('accept-reject-new-email');

        \DB::beginTransaction();
        $email = NewEmail::find($id);
        $email->status = NewEmail::STATUS_APPROVED;
        $email->save();
        \DB::commit();

        $users = User::permission('accept-reject-new-email')->get();
        Mail::to([$users, $email->personal_email])
            ->cc(['ceo@ptc-ksa.net'])
            ->queue(new AcceptNewEmailMail($email));

        return redirect()->route('new_email.orders');
    }
    public function rejectMail($id, Request $request)
    {
        $this->authorize('accept-reject-new-email');

        \DB::beginTransaction();
        $email = NewEmail::find($id);
        $email->status = NewEmail::STATUS_REJECTED;
        $email->save();
        \DB::commit();

        $users = User::permission('accept-reject-new-email')->get();
        Mail::to([$users, $email->personal_email])
            ->cc(['ceo@ptc-ksa.net'])
            ->queue(new RejectNewEmailMail($email));

        return redirect()->route('new_email.orders');
    }

}
