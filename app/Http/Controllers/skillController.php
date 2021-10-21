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
        $skillScores = session()->get('skillScores');
        $skillScores = collect($skillScores)->sort()->reverse();

        // Retrive result
        $compareResult = session()->get('compareResult');

        return view(
            'skills.index',
            [
                'compareResult' => $compareResult ?? [],
                'skillScores' => $skillScores ?? [],
            ]
        );
    }

    /**
     * Return input two options' name
     */
    public function compare()
    {
        // If schedule empty, rebuild one and refresh score and compare-result sessions
        // else pop out one set from the schedule
        if (empty(session()->get('compareSchedule'))) {
            $list = self::SKILLS;
            $len = count($list);
            $rounds = $len - 1;
            $start = 1;
            $compareSchedule = [];

            // Build schedule
            for ($i = 0; $i < $len - 1; $i++) {
                for ($j = $start; $j < $len; $j++) {
                    // Redis::lpush('skills', $list[$i] . ' vs. ' . $list[$rounds  - $j]);
                    $compareSchedule[]= [$i, $j];
                }
                $start++;
            }

            $pop = array_pop($compareSchedule);
        
            // Init score table
            $skillScores = [];
            foreach (self::SKILLS as $skill) {
                $skillScores[$skill] = 0;
            }

            session([
                'skillScores' => $skillScores,
                'compareResult' => [],
                'compareSchedule' => $compareSchedule
            ]);
        } else {
            $schedule = session()->get('compareSchedule');
            $pop =  array_pop($schedule);
            session([
                'compareSchedule' => $schedule
            ]);
        }

        $opA = (object) [
            'id' => $pop[0],
            'name' => self::SKILLS[$pop[0]]
        ];

        $opB = (object) [
            'id' => $pop[1],
            'name' => self::SKILLS[$pop[1]]
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
    public function store()
    {
        /**
         * result = [
         *    'id1, id2' => winner
         *    'id1, id3' => winner
         *     ...
         * ]
         */
        $result = session()->has('compareResult') ? session()->get('compareResult') : [];
        $result[request('compareSet')] = request('winner');

        $winnerSkill = self::SKILLS[(int) request('winner')];
        $skillScores = session()->get('skillScores');
        $skillScores[$winnerSkill] += 1;

        session([
            'compareResult' => $result,
            'skillScores' => $skillScores
        ]);

        // If has rest set to compare
        if (!empty(session()->get('compareSchedule'))) {
            return redirect()->route('skills.compare');
        }

        return redirect(route('skills.index'));
    }
}
