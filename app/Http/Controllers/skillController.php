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

        $popset = array_pop($compareSchedule);

        // Init score table
        $skillScores = session()->pull('skillScores');
        if (!$skillScores) {
            $skillScores = [];
            foreach ($list as $skill) {
                $skillScores[$skill] = 0;
            }

            session([
                'skillScores' => $skillScores
            ]);
        }
        $skillScores = collect($skillScores)->sort()->reverse();

        // Store compare schedule
        session([
            'compareSchedule' => $compareSchedule
        ]);

        // Retrive result
        if (session()->has('compareResult')) {
            $compareResult = session()->pull('compareResult');
        }

        return view(
            'skills.index',
            [
                'compareSchedule' => $compareSchedule,
                'compareResult' => $compareResult ?? [],
                'skillScores' => $skillScores,
                'set' => $popset
            ]
        );
    }

    /**
     * Return input two options' name
     */
    public function compare()
    {
        $skillScores = session()->get('skillScores');
        var_dump($skillScores);
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

        // Pop new set from driver
        $schedule = session()->get('compareSchedule');

        $popset = array_pop($schedule);

        session([
            'compareSchedule' => $schedule
        ]);

        if ($popset) {
            return redirect()->route('skills.compare', [
                'a' => $popset[0],
                'b' => $popset[1]
            ]);
        }

        return redirect(route('skills.index'));
    }
}
