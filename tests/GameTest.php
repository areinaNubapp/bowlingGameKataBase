<?php

declare(strict_types=1);

require '../vendor/autoload.php';

use Nubapp\Kata\Game;
use Nubapp\Kata\Exceptions\InvalidScoreException;
use PHPUnit\Framework\TestCase;

class GameTest extends TestCase
{
    private Game $game;

    protected function setUp(): void
    {
        parent::setUp();

        $this->game = new Game();
    }

    public function testSimpleRoll(): void
    {
        $this->game->roll(5);

        self::assertEquals(5, $this->game->score());
    }

    public function testSumRolls(): void
    {
        $this->game->roll(4);
        $this->game->roll(6);

        self::assertEquals(10, $this->game->score());
    }

    public function testMaxScoreIs10(): void
    {
        $this->expectException(InvalidScoreException::class);

        $this->game->roll(12);
    }

    public function testMaxScoreIs10InDubleRoll(): void
    {
        $this->expectException(InvalidScoreException::class);

        $this->game->roll(7);
        $this->game->roll(5);
    }

    public function testScoreCannotBeUnder0(): void
    {
        $this->expectException(InvalidScoreException::class);

        $this->game->roll(-1);
    }

    public function testStrikeEndsTurn(): void
    {
        $this->game->roll(10);

        self::assertEquals(2, $this->game->turn());
    }

    public function testRollIsUnder10NotEndsTurn(): void
    {
        $this->game->roll(8);

        self::assertEquals(1, $this->game->turn());
    }

    public function testATurnCannotHaveMoreThan2StagesIfNotLastTurn(): void
    {
        $this->game->roll(1);
        $this->game->roll(2);
        $this->game->roll(3);

        self::assertEquals(2, $this->game->turn());
    }

    public function testAGameEndsWith10Turns(): void
    {
        for ($i = 1; $i <= 10; $i ++) {
            $this->game->roll(9);
            $this->game->roll(0);
        }

        self::assertTrue($this->game->finished());
    }

    public function testLastTurnCanHave3Stages(): void
    {
        for ($i = 1; $i <= 12; $i ++) {
            $this->game->roll(10);
        }

        self::assertTrue($this->game->finished());
    }

    public function testStrikeGivesX2Bonus()
    {
        $this->game->roll(10);

        $this->game->roll(5);
        $this->game->roll(2);

        self::assertEquals(24, $this->game->score());
    }

    public function testSpareGivesX1Bonus()
    {
        $this->game->roll(5);
        $this->game->roll(5);

        $this->game->roll(2);

        self::assertEquals(14, $this->game->score());
    }

    public function testPerfectGameScoreIs300(): void
    {
        for ($i = 1; $i <= 12; $i ++) {
            $this->game->roll(10);
        }

        self::assertEquals(300, $this->game->score());
    }
}
