<?php

namespace App\Http\Controllers;

use App\Http\Resources\BranchResource;
use App\Models\BranchOffice;
use Illuminate\Contracts\Console\Application;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function list()
    {
        return BranchResource::collection(BranchOffice::all());
    }
};
