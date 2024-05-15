<?php

namespace App\Http\Controllers;

use App\Models\PinCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Traits\Permission;
use Illuminate\Support\Str;
use DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
class UserController extends Controller
{
    public function index()
    {
        try {
            // if (User::isAdmin()) {
            $model = new User();
            return view('user.index', compact('model'));
            // }
        } catch (\Exception $e) {
            return redirect(User::isAdmin() ? 'admin/order' : 'client/order')->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
    public function create(Request $request)
    {
        try {

            $model  = new User();
            return view('user.add', compact('model'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }



    public function update(Request $request, $id)
    {
        try {

            $model =  User::find($id);
            if (empty($model)) {
                return redirect('user/0')->with('error', 'User does not exist');
            }
            if (!User::isAdmin()) {
                if ($model->role_id == User::ROLE_ADMIN) {
                    return redirect('user/0')->with('error', 'You are not allowed to perform this action.');
                }
                if ($model->id != Auth::user()->id && $model->created_by_id != Auth::user()->id) {
                    return redirect('user/0')->with('error', 'You are not allowed to perform this action.');
                }
            }
            if ($this->validator($request->all(), $id)->fails()) {
                $message = $this->validator($request->all(), $id)->messages()->first();
                return redirect()->back()->withInput()->with('error', $message);
            }
            $model->update($request->all());
            return redirect("user/view/$model->id")->with('success', 'User updated  successfully');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }
    public $s_no = 1;

    public $setFilteredRecords = 0;
    public function getUserList(Request $request, $role_id = null)
    {
        $query  = User::where('role_id', '!=', User::ROLE_ADMIN)->orderBy('id', 'Desc');

        if (!User::isAdmin())
            $query->my();


        if ($role_id != null)
            $query->where('role_id', $role_id);


        return Datatables::of($query)
            ->addIndexColumn()

            ->addColumn('created_by', function ($data) {
                return !empty($data->createdBy && $data->createdBy->first_name) ? $data->createdBy->first_name : 'N/A';
            })
            ->addColumn('name', function ($data) {
                return !empty($data->name) ? (strlen($data->name) > 60 ? substr(ucfirst($data->name), 0, 60) . '...' : ucfirst($data->name)) : 'N/A';
            })
            ->addColumn('role_id', function ($data) {
                return  $data->getRole();
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
                $html .= ' <a class="btn btn-primary mt-1" href="' . url('user/edit/' . $data->id) . '" ><i class="fa fa-edit"></i></a>';
                $html .=    '  <a class="btn btn-primary mt-1" href="' . url('user/view/' . $data->id) . '"  ><i class="fa fa-eye
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
                                ->Where('first_name', 'like', "%$term%")
                                ->orWhere('last_name', 'like', "%$term%")
                                ->orWhere('unique_id', 'like', "%$term%")
                                ->orWhere('email', 'like', "%$term%")
                                ->orWhere('total_bv', 'like', "%$term%")
                                ->orWhere('phone', 'like', "%$term%")
                                ->orWhere('created_at', 'like', "%$term%")
                                ->orWhereHas('createdBy', function ($query) use ($term) {
                                    $query->Where('first_name', 'like', "%$term%");
                                    $query->orWhere('last_name', 'like', "%$term%");
                                })->orWhere(function ($query) use ($term) {
                                    $query->searchRole($term);
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
            "first_name" => "required|string",
            "last_name" => "string",
            "phone" => "required|numeric|digits:10",
            "age" => "required|numeric|min:15",
            "email" => "required|email",
        ];
        if ($id === null) {
            $rules = array_merge($rules, [
                "password" => "required|string|min:4",
                "confirm_password" => "required|same:password",
                "referral_unique_id" => "required|exists:users,unique_id",

            ]);
        }
        if (User::isAdmin()) {
            $userMOdel = User::find($id);
            if (empty($userMOdel) || $userMOdel->role_id != User::ROLE_ADMIN) {
                $rules = array_merge($rules, [
                    "role_id" => "required",
                    "referral_unique_id" => "required|exists:users,unique_id",

                ]);
            }
        }

        return Validator::make($data, $rules);
    }

    public function add(Request $request)
    {
        try {
            if ($this->validator($request->all())->fails()) {
                $message = $this->validator($request->all())->messages()->first();
                return redirect()->back()->withInput()->with('error', $message);
            }
            $model = new User();
            $model->fill($request->all());
            if (!User::isAdmin()) {
                $model->role_id = User::ROLE_USER;
            }
            $model->created_by_id = Auth::user()->id;
            $model->referral_id = User::where('unique_id', $request->referral_unique_id)->first()->id;
            $model->generateCustomerId();
            $model->password();
            $accounts_with_email = User::where('email', $request->email)->get();
            $accounts_with_phone = User::where('phone', $request->phone)->get();
            if (count($accounts_with_email) <= 5 || count($accounts_with_phone) <= 5) {
                if ($model->save()) {
                    return redirect('/user/view/' . $model->id)->with('success', 'User created successfully!');
                } else {
                    return redirect('/user')->with('error', 'Unable to save the User!');
                }
            } else {
                return redirect('/user')->with('error', 'You can have upto 5 accounts only with same Email or Phone number!');
            }
        } catch (\Exception $e) {
            $bug = $e->getMessage();
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function edit(Request $request)
    {
        try {
            $id = $request->id;
            $model  = User::find($id);
            if ($model) {
                if (!User::isAdmin()) {
                    if ($model->role_id == User::ROLE_ADMIN) {
                        return redirect('user/0')->with('error', 'You are not allowed to perform this action.');
                    }
                    if ($model->id != Auth::user()->id && $model->created_by_id != Auth::user()->id) {
                        return redirect('user/0')->with('error', 'You are not allowed to perform this action.');
                    }
                }
                return view('user.update', compact('model'));
            } else {
                return redirect('user')->with('error', 'User not found.');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function view(Request $request)
    {
        try {
            $id = $request->id;
            $model  = User::find($id);
            if ($model) {
                if (!User::isAdmin()) {
                    if ($model->role_id == User::ROLE_ADMIN) {
                        return redirect('user/0')->with('error', 'You are not allowed to perform this action.');
                    }
                    if ($model->id != Auth::user()->id && $model->created_by_id != Auth::user()->id) {
                        return redirect('user/0')->with('error', 'You are not allowed to perform this action.');
                    }
                }
                return view('user.view', compact('model'));
            } else {
                return redirect('/user')->with('error', 'User does not exist');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }


    public function registerReport()
    {
        try {
            $model = new User();
            return view('user.register_report', compact('model'));
        } catch (\Exception $e) {
            return redirect('admin/order')->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }


    public function getUserRegistrationData(Request $request)
    {

        $dataType = $request->input('type');
        $users = null;
        if ($dataType === 'dailyData') {
            $users = User::where('role_id', User::ROLE_USER)->whereDate('created_at', now())->get();
        } elseif ($dataType === 'weeklyData') {
            $users = User::where('role_id', User::ROLE_USER)->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->get();
        } elseif ($dataType === 'monthlyData') {
            $users = User::where('role_id', User::ROLE_USER)->whereYear('created_at', now()->year)
                ->whereMonth('created_at', now()->month)
                ->get();
        } elseif ($dataType === 'yearlyData') {
            $users = User::where('role_id', User::ROLE_USER)->whereYear('created_at', now()->year)->get();
        }

        $userData = [];
        $userCount = 1;
        foreach ($users as $order) {
            $userData[] = [
                'date' => $order->created_at->format('Y-m-d'), // Assuming 'created_at' is the order date field
                'user' => $userCount++,
            ];
        }

        // Group user data by date
        $groupedUserData = [];
        foreach ($userData as $data) {
            $groupedUserData[$data['date']][] = $data['user'];
        }

        // Calculate total user for each date
        $userByDate = [];
        foreach ($groupedUserData as $date => $users) {
            $userByDate[] = [
                'date' => $date,
                'totalUser' => array_sum($users),
            ];
        }

        // Return user registration data as JSON
        return response()->json($userByDate);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();

        $request->session()->regenerateToken();

      

        return $request->wantsJson()
            ? new Response('', 204)
            : redirect('/login');
    }
}
