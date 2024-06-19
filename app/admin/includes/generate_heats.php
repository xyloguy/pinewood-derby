<?php
class Chart
{
    public $lanes;
    public $cars;
    public $rounds;
    protected $chart;

    function __construct(int $lanes, array $cars, int $rounds = 1) {
        if (count($cars) < $lanes) {
            $lanes = count($cars);
        }
        if ($rounds < 1) {
            $rounds = 1;
        }
        $this->lanes = $lanes;
        $this->cars = $cars;
        $this->rounds = $rounds;
        $this->chart = [];
    }

    public function getChart() {
        $chart = [];
        foreach($this->chart as $heat) {
            $chart[] = $heat;
        }
        return $chart;
    }
}

class ChaoticChart extends Chart
{
    const LANE_FACTOR = 3.0;
    const OPPONENT_FACTOR = 1.0;
    const HEAT_FACTOR = 2.0;

    public function generate(): void {
        for ($tries = 0; $tries < 100; $tries++) {
            try {
                $assignedHeats = array_fill_keys($this->cars, 0);
                $lastHeats = array_fill_keys($this->cars, 0);
                $assignments = array_fill_keys($this->cars, array_fill(0, $this->lanes, 0));

                $opponents = [];
                foreach ($this->cars as  $key => $car) {
                    if (!array_key_exists($car, $opponents)) {
                        $opponents[$car] = [];
                    }
                    foreach ($this->cars as $key2 => $opp) {
                        if ($car === $opp) {
                            continue;
                        }
                        $opponents[$car][$opp] = 0;
                        if (!array_key_exists($opp, $opponents)) {
                            $opponents[$opp] = [];
                        }
                        $opponents[$opp][$car] = 0;
                    }
                }

                $this->chart = [];

                $totalHeats =  count($this->cars) * $this->rounds;
                for ($heat = 0; $heat < $totalHeats; $heat++) {
                    $currentHeat = [];

                    for ($lane = 0; $lane < $this->lanes; $lane++) {
                        $weights = [];

                        foreach ($this->cars as $key => $car) {
                            if (in_array($car, $currentHeat)) {
                                continue;
                            }
                            $weightFactor1 = self::LANE_FACTOR * $assignments[$car][$lane];
                            $weightFactor2 = array_sum(array_map(function ($opp) use ($opponents, $car) {
                                return isset($opponents[$car][$opp]) ? $opponents[$car][$opp] : 0;
                            }, $currentHeat)) * self::OPPONENT_FACTOR;
                            $weightFactor3 = 0;
                            if ($lastHeats[$car] > 0) {
                                $weightFactor3 = self::HEAT_FACTOR * (count($this->cars) / $this->lanes) / ($heat - $lastHeats[$car]);
                            }

                            $weights[$car] = $weightFactor1 + $weightFactor2 + $weightFactor3;
                        }

                        if (empty($weights)) {
                            throw new NoCarsException("No unassigned cars with valid slots remaining");
                        }

                        uasort($weights, function ($a, $b) {
                            return $a <=> $b;
                        });
                        $lowestWeight = reset($weights);
                        $carsWithLowestWeight = array_keys(array_filter($weights, function ($weight) use ($lowestWeight) {
                            return $weight === $lowestWeight;
                        }));
                        $car = $carsWithLowestWeight[array_rand($carsWithLowestWeight)];

                        $assignedHeats[$car]++;
                        $lastHeats[$car] = $heat;
                        $assignments[$car][$lane]++;
                        foreach ($currentHeat as $key => $opp) {
                            if ($car == $opp) {
                                continue;
                            }
                            $opponents[$car][$opp]++;
                            $opponents[$opp][$car]++;
                        }
                        $currentHeat[] = $car;
                    }
                    $this->chart[$heat] = $currentHeat;
                }
                return;
            } catch (NoCarsException $e) {
                continue;
            }
        }
    }
}

class NoCarsException extends Exception {}
