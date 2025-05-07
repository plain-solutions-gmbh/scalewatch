<?php

require_once(__DIR__ . '/../autoload.php');

class Csv
{

    public static string $data_location;
    public static string $daily_location;
    public static ?array $last_locations = null;
    private ?array $header = null;

    public function __construct(private string $file) {
    }

    public static function loadFromModul(string $modul, Closure $fallback)
    {
        static::$data_location ??= Config::location();

        //Set or/and create daily folder
        static::$daily_location ??= static::dailyLocation();

        $file = static::$daily_location . $modul . '.csv';

        //Check module file exists
        if (file_exists($file) === false) {
            $file = $fallback($file) ?? fn($file) => null;
        }

        return new static($file);

    }

    public static function loadFromFile(...$params): self
    {
        return new self(...$params);
    }

    private function open(bool $readonly = true)
    {
        
        $file = $this->file;

        //Create/open and lock file
        if ($readonly === false){
            $handle = fopen($file, 'a+');
            flock($handle, LOCK_EX);
        }
        
        if (file_exists($file) === false) {
            $filename = pathinfo($file, PATHINFO_BASENAME);
            throw new Exception("File '{$filename}' not exists");
        }

        //Open file for read
        $handle ??= fopen($file, 'r');
        
        $header = fgetcsv($handle);
        $this->header = ($header) ? $header : null;

        return $handle;

    }

    private function close($handle)
    {
        //Release file
        flock($handle, LOCK_UN);
        fclose($handle);
    }

    // public function get(null|int|array|string $query, ?string $file = null): ?array
    // {

    //     //Return all results
    //     if ($query === null) {
    //         return $this->read(null, $file);
    //     } 

    //     return $this->read(function ($data, $i) use ($query) {

    //         //Query is integer: Get by index
    //         if (is_int($query)) {
    //             return ($i === $query) ? $data : null;
    //         }

    //         //Query is string: Get columns
    //         if (is_string($query) && array_key_exists($query, $data)) {
    //             return $data[$query];
    //         }
            
    //         //Query is array: Get by query (AND-Operator)
    //         $match = true;
    //         foreach ($query as $key => $value) {
    //             if (($data[$key] ?? $value) !== $value) {
    //                 $match = false;
    //             }
    //         }

    //         return $match ? $data : null;


    //     }, $file);

    // }

    private function parseValue($value) {
        if ($value === '') {
            return null;
        }
        if (is_numeric($value)) {
            return (int) $value;
        }
        return $value;
    }

    public function read(?Closure $filter = null, ?string $file = null): array
    {
        $filter ??= fn($data, $i) => $data;
        $result = [];
        $i = 0;

        $handle = $this->open();
        $this->header ?? throw new Exception("Invalid CSV (Header missing)", 1);
        

        while (($data = fgetcsv($handle)) !== false) {
            $data = array_map(fn($item) => $this->parseValue($item), $data);
            if ($item = $filter(array_combine($this->header, $data), $i)) {
                $result[] = $item;
            }
            $i++;
        }

        $this->close($handle);

        return $result;
    }

    public function updateByQuery(array|int $where, array $update) {


        return $this->update(function ($item, $index) use ($where, $update){

            
            if (is_int($where)) {
                $match = $where === $index;
            } else {   
                $match = true;
                foreach ($where as $key => $value) {
                    if (($item[$key] ?? null) !== $value) {
                        $match = false;
                    }
                }
            }

            if ($match) {
                $item = array_merge($item, $update);
            }

            return $item;

        });

    }

    public function update(Closure $callback) {

        $handle = $this->open(false);

        $this->header ?? throw new Exception("Invalid CSV (Header missing)", 1);

        $updated = [];
        $i = 0;

        while (($row = fgetcsv($handle)) !== false) {
            $data = array_combine($this->header, $row);
            $updated[] = $callback($data, $i);
            $i++;
        }

        //Empty file and jump to start
        ftruncate($handle, 0);
        rewind($handle);

        //Write back
        fputcsv($handle, $this->header);
        foreach ($updated as $item) {
            if (array_keys($item) !== $this->header) {
                throw new Exception("Error in updating value: Wrong header definition detected!", 1);
            }

            fputcsv($handle, array_values($item));
        }

        $this->close($handle);

        return $updated;

    }

    public function write(array $data = [])
    {

        if (empty($data)) {
            return;
        }

        $handle = $this->open(false);

        foreach ($data as $item) {

            if ($this->header === null) {
                fputcsv($handle, $this->header = array_keys($item));
            }
            
            if (array_keys($item) !== $this->header) {
                throw new Exception("Error in updating value: Wrong header definition detected!", 1);
            }

            fputcsv($handle, array_values($item));
        }

        $this->close($handle);
    }

    private static function dailyLocation()
    {
        //Get formated date
        $date = date(Config::date_format());
        $daily_location = static::$data_location . $date . '/';

        //Create folder if not exists
        if (is_dir($daily_location) === false){
            try {
                mkdir($daily_location);
            } catch (\Throwable $th) {
                throw new Exception("Unable to create daily folder '$date", 1);
                
            };
        }

        return static::$daily_location = $daily_location;
    }

    //Return ths last daily folders
    public static function lastLocations(): array
    {

        //Return cached
        return static::$last_locations ??= array_filter(
            scandir(static::$data_location, SCANDIR_SORT_DESCENDING),
            function($foldername) {
                $dir = static::$data_location . $foldername;
                return (
                    is_dir($dir) &&
                    $foldername !== '.' &&
                    $foldername !== '..' &&
                    //Not empty
                    (count(scandir($dir)) !== 2)
                );

            }, 
        );

    }
}
