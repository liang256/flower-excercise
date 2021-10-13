<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

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
        $len = Redis::llen('skills');
        
        // $list = Redis::get('skills.0');
        // Redis::del('skills');
        $task = Redis::lpop('skills');
        var_dump('handle '.$task."\n");

        $list = Redis::lrange('skills', 0, $len-1);
        dd('rest tasks:', $list);

    }

    public function compare()
    {
        $list = self::SKILLS;
        // Redis::set('skills', $list);
        $len = count($list);
        $rounds = $len - 1;
        $gameSchedule = [];

        // 車輪戰
        for ($i = 0; $i < $len - 1; $i++) {
            for ($j = 0; $j < $rounds; $j++) {
                Redis::lpush('skills', $list[$i] . ' vs. ' . $list[$rounds  - $j]);
                printf(
                    "%s:%s vs %s:%s</br>",
                    $i,
                    $list[$i],
                    $rounds  - $j,
                    $list[$rounds - $j]
                );
                $gameSchedule[]= [$i, $rounds - $j];
            }
            $rounds--;
        }
        dd($gameSchedule);
    }
}
