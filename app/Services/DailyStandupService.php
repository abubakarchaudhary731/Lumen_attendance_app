<?php

namespace App\Services;

use App\Repositories\DailyStandupRepository;

class DailyStandupService
{
    protected $standupRepository;

    /**
     * Create a new DailyStandupService instance.
     *
     * @param DailyStandupRepository $standupRepository
     * @return void
     */
    public function __construct(DailyStandupRepository $standupRepository)
    {
        $this->standupRepository = $standupRepository;
    }

    public function create(array $data): array
    {
        if ($this->standupRepository->getTodayRecord($data['user_id'])) {
            throw new \Exception(__('You have already created a standup for today'), 400);
        }
        return $this->standupRepository->createDailyStandup($data)
            ->toArray();
    }
}
