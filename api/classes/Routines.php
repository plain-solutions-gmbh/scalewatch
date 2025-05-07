<?php

require_once(__DIR__ . '/../autoload.php');

class Routines
{

    private array $data = [];
    private Csv $handler;

    public function __construct()
    {
        $this->handler = Csv::loadFromModul(
            'routines',
            fn($file) => static::startNewDay($file)
        );
    }


    public function get(?int $index = null)
    {
        $this->data = $this->handler->read(
            fn($item, $i) => Routine::factory($item, $i)
        );

        $selection = ($index !== null) ? $this->data[$index] : $this;
        return $selection->toArray();
    }

    public function setStatus($index, $status) {

        $this->handler->updateByQuery($index, [
            'user'      => User::factory()->name(),
            'status'    => $status,
            'modified'  => date("Y-m-d H:i:s")
        ]);
        return $this->get();
    }

    private static function startNewDay($routine_file)
    {
        $schedule = Schedule::factory();

        $routine_handler = Csv::loadFromFile($routine_file);

        foreach (Config::routines() as $name => $file) {

            $routine_data = Csv::loadFromFile($file)->read(function($item) use ($name, $schedule) {
                if ($schedule->checkInterval($item['interval'])) {
                    return $item + ['routine' => $name];
                }
            });

            $routine_handler->write($routine_data);

        }

        return $routine_file;

    }

    public function toArray() {

        $data = $this->data;

        usort($data, function ($a, $b) {
            return $a->priority() <=> $b->priority();
        });

        return array_map(fn($routine) => $routine->toArray(), $data);
        
    }

}
