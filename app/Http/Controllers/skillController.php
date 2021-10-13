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

    /**
     * Create compare sechule
     */
    public function index()
    {
        $list = self::SKILLS;
        // Redis::set('skills', $list);
        $len = count($list);
        $rounds = $len - 1;
        $compareSchedule = [];

        // Build schedule
        for ($i = 0; $i < $len - 1; $i++) {
            for ($j = 0; $j < $rounds; $j++) {
                // Redis::lpush('skills', $list[$i] . ' vs. ' . $list[$rounds  - $j]);
                // printf(
                //     "%s:%s vs %s:%s</br>",
                //     $i,
                //     $list[$i],
                //     $rounds  - $j,
                //     $list[$rounds - $j]
                // );
                $compareSchedule[]= [$i, $rounds - $j];
            }
            $rounds--;
        }
        // dd($compareSchedule);
        Redis::set(
            'compareSchedule',
            json_encode(
                $compareSchedule
            )
        );

        $compareResult = json_decode(
            Redis::get('compareResult'),
            true
        );

        return view(
            'skills.index',
            [
            'compareSchedule' => $compareSchedule,
            'compareResult' => $compareResult
            ]
        );
    }

    /**
     * Return input two options' name
     */
    public function compare()
    {
        $opA = (object) [
            'id' => request('a'),
            'name' => self::SKILLS[(int) request('a')]
        ];

        $opB = (object) [
            'id' => request('b'),
            'name' => self::SKILLS[(int) request('b')]
        ];

        // dd($opA, $opB);
        return view(
            'skills.compare',
            [
            'opA' => $opA,
            'opB' => $opB
            ]
        );
    }

    /**
     * Store compared result to the driver
     */
    public function store(Request $request)
    {
        /**
         * result = [
         *    'id1, id2' => winner
         *    'id1, id3' => winner
         *     ...
         * ]
         */
        $result = json_decode(
            Redis::get('compareResult'),
            true
        ) ?? [];
        $result[request('compareSet')] = request('winner');
        // dd($result);

        Redis::set(
            'compareResult',
            json_encode(
                $result
            )
        );

        // Pop new set from driver
        $schedule = json_decode(
            Redis::get('compareSchedule')
        );

        $popset = array_pop($schedule);

        Redis::set(
            'compareSchedule',
            json_encode($schedule)
        );

        if ($popset) {
            $opA = (object) [
                'id' => $popset[0],
                'name' => self::SKILLS[(int) $popset[0]]
            ];
    
            $opB = (object) [
                'id' => $popset[1],
                'name' => self::SKILLS[(int) $popset[1]]
            ];
    
            // dd($opA, $opB);
            return view(
                'skills.compare',
                [
                'opA' => $opA,
                'opB' => $opB
                ]
            );
        }

        return redirect(route('skills.index'));
    }
}
