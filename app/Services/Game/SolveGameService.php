<?php


namespace App\Services\Game;

use App\Models\Board;
use App\Models\Game;
use Carbon\Carbon;

class SolveGameService
{
    public $game;
    public $boardPattern;
    public $gameBoard;
    public $steps;

    private $isSolved = true;

    public function __construct(Game $game, $steps)
    {
        $this->boardPattern = (new Board())->pattern();
        $this->game = $game;
        $this->gameBoard = $game->board;
        $this->steps = $steps;
    }

    //метод обработки решения отправляет каждый шаг на валидацию, затем сравнивает итоговое состояние доски с паттерном доски
    public function solve()
    {
        foreach ($this->steps as $step) {
            if ($this->validateStep($step) === false) {
                break;
            }
        }

        $this->check();
        return $this->isSolved;
    }

    //метод получения времени прохождения
    public function getTime($solutionTime)
    {
        $solvedAt = Carbon::parse($solutionTime);
        $createdAt = Carbon::parse($this->game->created_at);
        return $solvedAt->diffAsCarbonInterval($createdAt);
    }

    //метод валидации шагов
    private function validateStep($step)
    {
        $value = $step['value'];
        $oldX = $step['oldCoords']['x'];
        $oldY = $step['oldCoords']['y'];
        $newX = $step['newCoords']['x'];
        $newY = $step['newCoords']['y'];

        //проверяем, что игровое поле не перемешивалось после создания:
        //значение клетки в БД по старым координатам совпадает со значением клетки из шага
        if ($this->gameBoard[$oldX][$oldY]['value'] != $value) {
            return false;
        }

        //Проверяем, что новые координаты клетки являются координатами пустой(нулевой) клетки.
        //Если координаты совпадают, меняем клетки местами
        if ($this->gameBoard[$newX][$newY]['value'] != 0) {
            return false;
        }

        $this->gameBoard[$newX][$newY]['value'] = $value;
        $this->gameBoard[$oldX][$oldY]['value'] = 0;

        return true;
    }

    //метод сравнения итоговой доски с правильным решением
    private function check()
    {
        for ($i = 0; $i < 4; $i++) {
            $gameBoardRow = array_column($this->gameBoard[$i], 'value');
            if ($gameBoardRow != $this->boardPattern[$i]) {
                $this->isSolved = false;
            }
        }

        return $this->isSolved;
    }
}
