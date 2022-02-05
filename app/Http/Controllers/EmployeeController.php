<?php

namespace App\Http\Controllers;

use App\Http\Resources\EmployeeResource;
use App\Models\Employee;
use App\Models\User;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    public function index()
    {
        return view('user.index');
    }

    /**
     * Generate Employee Number.
     *
     * @return bool
     */
    public function generateNo()
    {
        $i = 0;

        do {
            $num = sprintf('%02d', ++$i);
            $employeeNo = now()->format('Ymd') . $num;
        } while ($this->isEmployeeNoExist($employeeNo));

        return $employeeNo;
    }

    private function isEmployeeNoExist($employeeNo)
    {
        $result  = Employee::whereEmployeeNo($employeeNo)->get();

        return !! $result->count();
    }

    public function store(Request $request)
    {
        $validate = $request->validate([
            'employee_no' => 'required',
            'name'        => 'required',
            'branch_id'   => 'required',
            'phone'       => 'required|max:11',
            'email'       => 'required|email',
            'password'    => 'required|min:8',
        ]);

        try {
            $user = User::create([
                'name'             => $validate['name'],
                'email'            => $validate['email'],
                'password'         => Hash::make($validate['password']),
                'phone'            => $validate['phone'],
                'branch_office_id' => $validate['branch_id'],
                'role'             => 'rider',
            ]);
        } catch (QueryException $e) {
            $errorCode = $e->errorInfo[1];

            if ($errorCode == 1062){
                return response('이미 등록된 사원번호 또는 이메일 주소', 422);
            }

            return response($e->getMessage(), 422);
        }

        if ($user) {
            $user->employee()->create([
                'employee_no' => $validate['employee_no'],
                'branch_id'   => $validate['branch_id'],
            ]);
        }
    }

    public function all(Request $request)
    {
        $queryResults = Employee::when($request->filled(['search_field', 'search_keyword']), function ($query) use ($request) {
            if ($request->search_field === 'employee_no') {
                $query->where('employee_no', 'LIKE', "%$request->search_keyword%");
            } else if ($request->search_field === 'name') {
                $query->whereHas('user', function ($query) use ($request) {
                    $query->where('name', 'LIKE', "%$request->search_keyword%");
                });
            }
        })->latest()->paginate($request->per_page);

        return EmployeeResource::collection($queryResults);
    }

    public function delete(Request $request)
    {
        $validate = $request->validate([
            'ids' => 'required|array'
        ]);

        User::destroy($validate['ids']);
    }

    public function update(User $user, Request $request)
    {
        $validate = $request->validate([
            'name'        => 'required',
            'branch_id'   => 'required',
            'phone'       => 'required|max:11',
            'email'       => 'required|email',
            'password'    => 'min:8',
        ]);

        if (isset($validate['password'])) {
            $validate['password'] = Hash::make($validate['password']);
        }

        $user->update($validate);
        $user->employee->update(['branch_id' => $validate['branch_id']]);
    }
}
