<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Choice;
use App\Models\Devision;
use App\Models\User;
use App\Models\Vote;
use Illuminate\Http\Request;

class UtilController extends Controller
{

    static function countDevision()
    {
        return Devision::all()->count();
    }

    static function countAllDevisionMember()
    {
        $devisions = Devision::all(['id']);
        $membersCount = [];
        foreach ($devisions as $dev) {
                $membersCount[$dev->id] = User::all()->where('devision_id', $dev->id)->count();
        }
        return $membersCount;
    }
    
    static function getChoicesByPollId($poll_id)
    {
        return Choice::all()->where('poll_id', $poll_id);
    }

    static function getDevisionPoint($poll_id, $devision_id)
    {
        $votes = Vote::all()->where('poll_id', $poll_id)->where('devision_id', $devision_id);
        $choices = UtilController::getChoicesByPollId($poll_id);
        $data = [
            'devision_id' => $devision_id,
            'poll_id' => $poll_id,
            'vote_counts' => [
            ],
            'points' => [
            ],
        ];
        foreach ($choices as $choice) {
            $data['vote_counts'][$choice->id] = $votes->where('choice_id', $choice->id)->count(); 
        }

        if(max($data['vote_counts']) < 1){
            return $data;
        }

        // get max point
        $maxPoint = max($data['vote_counts']);
        // check if the max point doubled
        $maxPointChoice = array_keys($data['vote_counts'],$maxPoint);
        if(count($maxPointChoice) >= 2){
            $countSame = count($maxPointChoice);
            foreach ($maxPointChoice as $value) {
                $data['points'][$value] = 1 / $countSame;
            }
        }else{
            foreach ($maxPointChoice as $value) {
                $data['points'][$value] = 1;
            }
        }
        return $data;
    }

    static function averageAll($poll_id)
    {
        $sumChoices = [];
        $sumAllChoice = 0;
        $choices = UtilController::getChoicesByPollId($poll_id);
        $devisions = Devision::all(['id']);
        $data = [];

        foreach ($choices as $key => $choice) {
            $sumChoices[$choice->id] = 0;
        }

        foreach($devisions as $dev){
            $points = UtilController::getDevisionPoint($poll_id, $dev->id)['points'];
            foreach($points as $key => $val){
                $sumChoices[$key] += $val;
                $sumAllChoice += $val;
            }
        }

        if ($sumAllChoice === 0) {
            foreach($choices as $choice){
                $data[$choice->id] = 0;
            }
            return $data;
        }

        foreach ($choices as $choice) {
            $data[$choice->id] = round($sumChoices[$choice->id] / $sumAllChoice * 100);
        }

        return $data;

    }

    static function getSumByPollId($poll_id)
    {
        $sumChoices = [];
        $sumAllChoice = 0;
        $choices = UtilController::getChoicesByPollId($poll_id);
        $devisions = Devision::all(['id']);

        foreach ($choices as $key => $choice) {
            $sumChoices[$choice->id] = 0;
        }

        foreach($devisions as $dev){
            $points = UtilController::getDevisionPoint($poll_id, $dev->id)['points'];
            foreach($points as $key => $val){
                $sumChoices[$key] += $val;
                $sumAllChoice += $val;
            }
        }

        return $sumChoices;

    }

}
