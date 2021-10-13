<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SkillController extends Controller
{
    private const SKILLS = [
        'planning',
        'imaginate',
        'teach',
        'console',
        'research',
    ];

    public function index()
    {
        dd(self::SKILLS);
    }

    public function compare()
    {
        $list = self::SKILLS;
        $len = count($list);
        $rounds = $len - 1;
        $gameSchedule = [];

        // 車輪戰
        for ($i = 0; $i < $len - 1; $i++) {
            for ($j = 0; $j < $rounds; $j++) {
                printf(
                    "%s:%s vs %s:%s</br>",
                    $i,
                    $list[$i],
                    $rounds  - $j,
                    $list[$rounds  - $j]
                );
                $gameSchedule[]= [$i, $rounds  - $j];
            }
            $rounds--;
        }
        dd($gameSchedule);
    }
}
