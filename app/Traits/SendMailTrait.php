<?php
namespace App\Traits;

use App\Jobs\SendConfirmMailJob;

trait SendMailTrait
{

    public function SendMailAthlete($athleteId){
        $athlete = $this->athleteRepo->find($athleteId)->toArray();
        $athlete['tournament_competitions'] = [];
        $tournamentCompetitionIds = explode(',', $athlete['tournament_competition_id']);
        $tournament = $this->tournamentRepo->find($athlete['tournament_id']);
        $athlete['tournament_start_date'] = $tournament['tournament_start_date'];
        $athlete['tournament_end_date'] = $tournament['tournament_end_date'];
        foreach ($tournamentCompetitionIds as $tournamentCompetitionId){
            if($tournamentCompetitionId){
                $tournamentCompetition = $this->tournamentCompetitionRepo->find($tournamentCompetitionId)->toArray();
                $athlete['tournament_competitions'][] = [
                    'name' => $tournamentCompetition['name'],
                ];
            }
        }
        dispatch(new SendConfirmMailJob($athlete));
    }
}
