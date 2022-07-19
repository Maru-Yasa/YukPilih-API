<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Choice;
use Illuminate\Http\Request;
use App\Models\Poll;
use App\Models\User;
use App\Models\Vote;
use Illuminate\Contracts\Validation\Validator as ValidationValidator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ItemNotFoundException;
use Spatie\FlareClient\Http\Exceptions\NotFound;

class PollController extends Controller
{
    private function sendValidatorError(ValidationValidator $validator)
    {
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'data' => [],
                'msg' => $validator->errors()
            ], 422);
        }

    }

    public function create(Request $req)
    {
        $validator = Validator::make($req->all(), [
           'title' => 'required',
           'description' => 'required',
           'deadline' => 'required',
           'choices' => 'required' 
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'data' => [],
                'msg' => $validator->errors()
            ], 422);
        }

        $poll = Poll::create([
            'title' => $req->title,
            'description' => $req->description,
            'deadline' => $req->deadline,
            'created_by' => auth()->user()->id
        ]);

        foreach($req->choices as $choice){
            Choice::create([
                'choice' => $choice,
                'poll_id' => $poll->id
            ]);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'poll' => $poll
            ],
            'msg' => 'success created poll'
        ], 200);

    }

    public function getAll(Request $req)
    {
        $polls = Poll::all();
        $data = [];
        foreach($polls as $poll){
            $author_id = $poll['created_by'];
            $author = User::all()->where('id', $author_id)->first();
            $poll['author'] = $author->username;
            $data[] = $poll;
        }
        return response()->json([
            'status' => 'success',
            'data' => $data,
            'msg' => 'success get all polls'
        ], 200);
    }

    public function getById(Request $req, $poll_id)
    {
        /* 
        TODO:
            1.return result
            2.count result with the formula given
        */
        try {
            $poll = Poll::all()->where('id', $poll_id)->firstOrFail();
            $result = UtilController::averageAll($poll_id);
            $poll['creator'] = User::all()->where('id', $poll->created_by)->first()->username;
            $poll['result'] = $result;
            $poll['choices'] = [...UtilController::getChoicesByPollId($poll_id)];
            return response()->json([
                'status' => 'success',
                'data' => $poll,
                'msg' => 'success fetching poll'
            ], 200);
        } catch (ItemNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'data' => [],
                'msg' => 'poll not found'
            ], 404);
        }
    }

    public function delete(Request $req, $poll_id)
    {
        try {
            $poll = Poll::all()->where('id', $poll_id)->firstOrFail();
            $poll->delete();
            return response()->json([
                'status' => 'success',
                'data' => [],
                'msg' => 'success deleting poll'
            ], 200);
        } catch (ItemNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'data' => [],
                'msg' => 'poll not found'
            ], 404);
        }
    }

    static function isExpired($date)
    {
        $carbonDate = new Carbon($date);
        $now = Carbon::now();
        return $carbonDate->greaterThanOrEqualTo($now);
    }

    public function vote(Request $req, $poll_id, $choice_id)
    {
        /* 
        
        TODO:
            1.make admin can't vote
            2.after vote user can't vote
            3.after dedline can't vote
        */
        $poll = Poll::all()->where('id', $poll_id)->firstOrFail();
        if (!PollController::isExpired($poll->deadline)) {
            return response()->json([
                'status' => 'error',
                'data' => [],
                'msg' => 'The poll is expired'
            ]);
        }

        $user = auth()->user();
        $voted = Vote::all()->where('poll_id', $poll_id)->where('user_id', $user->id)->first();
        if ($voted) {
            return response()->json([
                'status' => 'error',
                'data' => [],
                'msg' => 'user alredy voted'
            ]);
        }

        $vote = Vote::create([
            'choice_id' => $choice_id,
            'poll_id' => $poll_id,
            'user_id' => $user->id,
            'devision_id' => $user->devision_id
        ]);
        return response()->json([
            'status' => 'success',
            'data' => $vote,
            'msg' => 'success voted'
        ], 200);

    }

}
