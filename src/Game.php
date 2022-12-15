<?php

declare(strict_types=1);

namespace Nubapp\Kata;

use Nubapp\Kata\Exceptions\GameFinishedException;
use Nubapp\Kata\Exceptions\InvalidScoreException;

class Game
{
    private int $turn = 1;
    private int $stage = 1;
    private array $score = [];

    private bool $finished = false;

    /**
     * @throws InvalidScoreException|GameFinishedException
     */
    public function roll(int $score): void
    {
        if ($this->finished) {
            throw new GameFinishedException();
        }

        if (!isset($this->score[$this->turn - 1][$this->stage - 1])) {
            $this->score[$this->turn - 1][$this->stage - 1] = 0;
        }

        if ($score > 10 || $score < 0 || ((array_sum($this->score[$this->turn - 1]) + $score) > 10 && $this->turn < 10)) {
            throw new InvalidScoreException();
        }

        $this->score[$this->turn - 1][$this->stage - 1] += $score;

        $this->applyBonus($score);

        if ($this->isTurnFinished()) {
            $this->turn++;
            $this->stage = 1;

            if ($this->turn > 10) {
                $this->finished = true;
            }

            return;
        }

        $this->stage++;
    }

    public function score(): int
    {
        return array_reduce($this->score, static fn ($carry, $scores) => array_sum($scores) + $carry, 0);
    }

    public function turn(): int
    {
        return $this->turn;
    }

    public function finished(): bool
    {
        return $this->finished;
    }

    private function applyBonus(int $score): void
    {
        if ($this->stage === 3) {
            return;
        }

        if (isset($this->score[$this->turn -3]) && count($this->score[$this->turn -3]) === 1) {
            $this->score[$this->turn - 3][0] += $score;
        }

        if (isset($this->score[$this->turn -2]) &&
            (count($this->score[$this->turn -2]) === 1 || (count($this->score[$this->turn -2]) === 2 && array_sum($this->score[$this->turn -2]) === 10))) {
            $this->score[$this->turn -2][0] += $score;
        }
    }

    private function isTurnFinished(): bool
    {
        return ($this->turn < 10 && array_sum($this->score[$this->turn - 1]) === 10)
            || ($this->turn < 10 && $this->stage === 2)
            || ($this->turn === 10 && $this->stage === 2 && array_sum(end($this->score)) < 10 )
            || ($this->turn === 10 && $this->stage === 3);
    }
}