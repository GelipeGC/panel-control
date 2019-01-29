<?php

namespace App\Http\Controllers;

use App\Skill;
use Illuminate\Http\Request;

class SkillController extends Controller
{
    public function index()
    {
        $skills = Skill::orderBy('name')->get();

        return view('skills.index',[
            'skills' => $skills
        ]);
    }

    public function destroy(Skill $skill)
    {
        //abort_if($profession->profiles()->exists(), 400, 'cannot delete a profession linked to a profile');
        $skill->delete();

        return redirect('habilidades');
    }
}
