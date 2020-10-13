<?php


namespace App\Services\Board;


class CreateBoardService
{
    public function get($request = [])
    {
        //если в запросе на создание игры не было исходного расположения клеток, генерим свое.
        //если исходные данные были, приводим к формату доски
        if (empty($request)) {
            $inputData = $this->generateInputData();
        } else {
            $inputData = $this->formatInputData($request['board']);
        }

        $newBoard = [];

        return $this->create($inputData, $newBoard);

    }

    private function formatInputData($inputData)
    {
        return array_chunk(explode(',', $inputData), 4);
    }

    private function generateInputData()
    {
        $array = range(0, 15);

        for ($i = 0; $i < count($array); $i++) {
            $first = rand(0, 15);
            $second = rand(0, 15);
            $tmp = $array[$first];
            $array[$first] = $array[$second];
            $array[$second] = $tmp;
        }

        return array_chunk($array, 4);
    }

    private function create($inputData, &$newBoard, $parentKey = null)
    {
        foreach (array_keys($inputData) as $key) {
            $value = &$inputData[$key];
            if (is_array($value)) {
                $this->create($value, $newBoard, $key);
            } else {
                $newBoard[$parentKey][$key] = [
                    'value' => $value,
                    'coords' => [
                        'x' => $parentKey,
                        'y' => $key
                    ]
                ];
            }
        }
        return $newBoard;
    }
}
