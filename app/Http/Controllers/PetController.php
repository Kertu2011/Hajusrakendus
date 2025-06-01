<?php

namespace App\Http\Controllers;

use Inertia\Response;

class PetController extends Controller
{
    public function index(): Response
    {
        return inertia('Pets/Index');
    }

    public function create(): Response
    {
        return inertia('Pets/Create');
    }
}
