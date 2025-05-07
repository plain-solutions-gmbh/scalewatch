<?php

require_once(__DIR__ . '/../autoload.php');

class Journal
{

    private ?array $data = null;
    private Csv $handler;

    public function __construct()
    {
        $this->handler = Csv::loadFromModul(
            'journal',
            fn($file) => static::startNewDay($file)
        );
    }

    public function get()
    {
        $this->data ??= $this->handler->read();

        $result = [];
        foreach ($this->data as $item) {
            $result[$item['tank']] = $item;
        }
        return $result;

    }

    public function set($tank, $key, $value) {

        return $this->handler->update(function($data, $i) use ($tank, $key, $value){
            if ($data['tank'] === $tank) {
                $data[$key] = $value;
            }
            return $data;
        });
    }

    public function setStatus($index, $status) {

        $this->handler->updateByQuery($index, [
            'user'      => User::factory()->name(),
            'status'    => $status,
            'modified'  => date("Y-m-d H:i:s")
        ]);
        return $this->get();
    }

    private static function startNewDay($journal_file)
    {

        $empty_params = [];

        foreach (array_keys(Config::plant('params')) as $params_name) {
            $empty_params[$params_name] = null;
        }

        $tanks = array_map(fn($tank) => (['tank' => $tank] + $empty_params), Config::tanks());

        Csv::loadFromFile($journal_file)->write($tanks);

        return $journal_file;

    }

    public function toArray() {

        $data = $this->data;

        usort($data, function ($a, $b) {
            return $a->priority() <=> $b->priority();
        });

        return array_map(fn($routine) => $routine->toArray(), $data);
        
    }

}
