<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{

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
        } while (! $this->isEmployeeNoExist($employeeNo));

        return $employeeNo;
    }

    private function isEmployeeNoExist($employeeNo)
    {
        return !! Employee::whereEmployeeNo($employeeNo);
    }
}
