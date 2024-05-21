<?php

namespace App\Http\Controllers;

use App\Models\PinCode;
use App\Models\SubscribedPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\SubscriptionPlan;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Traits\Permission;
use Illuminate\Support\Str;
use DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class SubscribedPlanController extends Controller
{
    public function index()
    {
        try {
            $model = new SubscribedPlan();
            return view('subscription.subscribed_plan.index', compact('model'));
        } catch (\Exception $e) {
            return redirect('/dashboard')->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }




    public $s_no = 1;

    public function getSubscribedPlanList(Request $request, $id = null)
    {
        $query  = SubscribedPlan::orderBy('id', 'Desc');

        if (empty($id))
            if (!User::isAdmin())
                $query->my();

        if (!empty($id))
            $query->where('plan_id', $id);
        return Datatables::of($query)
            ->addIndexColumn()
            ->addColumn('created_by', function ($data) {
                return !empty($data->createdBy && $data->createdBy->name) ? $data->createdBy->name : 'N/A';
            })
            ->addColumn('plan_id', function ($data) {
                return !empty($data->subscriptionPlan && $data->subscriptionPlan->title) ? $data->subscriptionPlan->title : 'N/A';
            })
            ->addColumn('price', function ($data) {
                return !empty($data->subscriptionPlan && $data->subscriptionPlan->price) ? $data->subscriptionPlan->price : 'N/A';
            })

            ->addColumn('name', function ($data) {
                return !empty($data->name) ? (strlen($data->name) > 60 ? substr(ucfirst($data->name), 0, 60) . '...' : ucfirst($data->name)) : 'N/A';
            })
            ->addColumn('status', function ($data) {
                return '<span class="' . $data->getStateBadgeOption() . '">' . $data->getState() . '</span>';
            })
            ->addColumn('duration_type', function ($data) {
                return !empty($data->subscriptionPlan && $data->subscriptionPlan->getDurationType()) ? $data->subscriptionPlan->getDurationType() : 'N/A';
            })
            ->rawColumns(['created_by'])

            ->addColumn('created_at', function ($data) {
                return (empty($data->updated_at)) ? 'N/A' : date('Y-m-d', strtotime($data->updated_at));
            })
            ->addColumn('action', function ($data) {
                $html = '<div class="table-actions text-center">';
                $html .=    '  <a class="btn btn-icon btn-primary mt-1" href="' . url('subscription/subscribed-plan/view/' . $data->id) . '"  ><i class="fa fa-eye
                    "data-toggle="tooltip"  title="View"></i></a>';
                $html .=  '</div>';
                return $html;
            })->addColumn('customerClickAble', function ($data) {
                $html = 0;

                return $html;
            })
            ->rawColumns([
                'action',
                'created_at',
                'status',
                'customerClickAble'
            ])
            ->filter(function ($query) {
                if (!empty(request('search')['value'])) {
                    $searchValue = request('search')['value'];
                    $searchTerms = explode(' ', $searchValue);
                    $query->where(function ($q) use ($searchTerms) {
                        foreach ($searchTerms as $term) {
                            $q->where('id', 'like', "%$term%")
                                ->orWhere('created_at', 'like', "%$term%")
                                ->orWhereHas('createdBy', function ($query) use ($term) {
                                    $query->where('name', 'like', "%$term%");
                                })
                                ->orWhere(function ($query) use ($term) {
                                    $query->searchState($term);
                                })
                                ->orWhereHas('subscriptionPlan', function ($query) use ($term) {
                                    $query->where('title', 'like', "%$term%")
                                        ->orWhere('price', 'like', "%$term%")
                                        ->orWhere(function ($q) use ($term) {
                                            $q->durationType($term);
                                        });
                                });
                        }
                    });
                }
            })

            ->make(true);
    }


    protected static function validator(array $data, $id = null)
    {
        $rules = [
            "title" => "required|string||max:128",
            "description" => "required|max:128",
            "duration_type" => "required|max:128",
            "duration" => "required|numeric|max:128",
            "price" => "required|numeric|max:12800",
        ];
        return Validator::make($data, $rules);
    }


    public function view(Request $request)
    {
        try {
            $id = $request->id;
            $model  = SubscribedPlan::find($id);
            if ($model) {
                if (!User::isAdmin()) {
                    if ($model->created_by_id != Auth::user()->id) {
                        return redirect('subscription/subscribed-plan/')->with('error', 'You are not allowed to perform this action.');
                    }
                }
                return view('subscription.subscribed_plan.view', compact('model'));
            } else {
                return redirect('/subscription/subscribed-plan')->with('error', 'SubscribedPlan does not exist');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }


    public function add($id)
    {

        try {
            if (User::isAdmin()) {
                return redirect()->back()->with('error', 'You are not allowed to perform this action.');
            }

            $SubscriptionPlanModel =  SubscriptionPlan::findActive()->find($id);
            if (empty($SubscriptionPlanModel->exists)) {
                return redirect('/subscription/subscribed-plan')->with('error', 'SubscriptionPlan does not exist');
            } else {
                $startDate = Carbon::now();
                $activeSubscription = SubscribedPlan::where('created_by_id', Auth::id())
                    ->where('end_date', '>', Carbon::now())
                    ->first();

                // if ($activeSubscription) {
                //     return redirect('/subscription/subscribed-plan')->with('error', 'You already have an active subscription');
                // }
                DB::beginTransaction();
                $model = new SubscribedPlan();
                $model->state_id = SubscribedPlan::STATE_ACTIVE;
                $model->created_by_id = Auth::id();
                $model->plan_id = $SubscriptionPlanModel->id;
                if ($SubscriptionPlanModel->duration_type === SubscriptionPlan::DURATION_TYPE_MONTHLY) {
                    $endDate = $startDate->copy()->addMonths($SubscriptionPlanModel->duration);
                } else {
                    $endDate = $startDate->copy()->addYears($SubscriptionPlanModel->duration);
                }
                $model->roi_count = $startDate->diffInDays($endDate);
                $model->start_date = $startDate;
                $model->end_date = $endDate;
                if (($model->save())) {
                    $admin = User::find(1);
                    $adminWallet = Wallet::where('created_by_id', $admin->id)->first();
                    $adminWallet->balance += $SubscriptionPlanModel->price;
                    $adminWallet->save();
                    $walletTransactionModel = new WalletTransaction();
                    $walletTransactionModel->state_id = WalletTransaction::STATE_COMPLETED;
                    $walletTransactionModel->created_by_id = Auth::id();
                    $walletTransactionModel->transaction_type = WalletTransaction::TRANSACTION_USER_INVEST;
                    $walletTransactionModel->type_id = WalletTransaction::TYPE_CREDIT;
                    $walletTransactionModel->wallet_id = $adminWallet->id;
                    $walletTransactionModel->amount = $SubscriptionPlanModel->price;
                    $walletTransactionModel->description = Auth::user()->name . ' is purchased Plan successfully. ( Plan:-' . $SubscriptionPlanModel->title . ', Type:-' . $SubscriptionPlanModel->getDurationType() . ')';
                    $walletTransactionModel->save();
                    DB::commit();
                    return redirect('/subscription/subscribed-plan')->with('success', 'Subscribed Plan created successfully');
                }
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect('/dashboard')->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }



    public function testing()
    {
        try {

            $subscribedPlanModel =  SubscribedPlan::findActive()->whereColumn('roi_count', '!=', 'roi_complete_count')->get();
            foreach ($subscribedPlanModel as $subscribedPlan) {
                if ($subscribedPlan->roi_complete_count <= $subscribedPlan->roi_count) {
                    $subscribedPlan->roi_complete_count += 1;
                    $subscribedPlan->save();
                }
            }
            $model = new SubscribedPlan();
        } catch (\Exception $e) {
        }
    }
}
