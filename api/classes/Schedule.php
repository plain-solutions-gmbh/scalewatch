<?php

use Carbon\Carbon;

class Schedule
{
    protected Carbon $now;
    protected Carbon $startDate;

    public function __construct(?Carbon $now = null, ?Carbon $startDate = null)
    {
        $this->now = $now ?? Carbon::now();
        $this->startDate = $startDate ?? Carbon::createFromDate(2024, 1, 1);
    }

    public static function factory(...$params): self
    {
        return new self(...$params);
    }

    public function checkInterval(?string $pattern = ""): bool
    {
        $pattern = strtolower(trim($pattern));

        // Einfache Begriffe
        $map = [
            'daily'       => fn() => true,
            'weekday'     => fn() => $this->now->isWeekday(),
            'weekend'     => fn() => $this->now->isWeekend(),
            'monday'      => fn() => $this->now->isMonday(),
            'tuesday'     => fn() => $this->now->isTuesday(),
            'wednesday'   => fn() => $this->now->isWednesday(),
            'thursday'    => fn() => $this->now->isThursday(),
            'friday'      => fn() => $this->now->isFriday(),
            'saturday'    => fn() => $this->now->isSaturday(),
            'sunday'      => fn() => $this->now->isSunday(),
            'odd weeks'   => fn() => $this->now->weekOfYear % 2 === 1,
            'even weeks'  => fn() => $this->now->weekOfYear % 2 === 0,
            'monthly'     => fn() => $this->now->day === 1,
            'quarterly'   => fn() => in_array($this->now->month, [1, 4, 7, 10]) && $this->now->day === 1,
            'yearly'      => fn() => $this->now->isSameDay(Carbon::create($this->now->year, 1, 1)),
        ];

        if (isset($map[$pattern])) {
            return $map[$pattern]();
        }

        // Woche: week16
        if (preg_match('/^week(\d{1,2})$/', $pattern, $m)) {
            return intval($m[1]) === $this->now->weekOfYear;
        }

        // Monat: month5 (Mai)
        if (preg_match('/^month(\d{1,2})$/', $pattern, $m)) {
            return intval($m[1]) === $this->now->month;
        }

        // Tag im Monat: day15
        if (preg_match('/^day(\d{1,2})$/', $pattern, $m)) {
            return intval($m[1]) === $this->now->day;
        }

        // Kombination wie "monday in even weeks"
        if (preg_match('/^(monday|tuesday|wednesday|thursday|friday|saturday|sunday) in (odd|even) weeks$/', $pattern, $m)) {
            return $this->checkInterval($m[1]) && $this->checkInterval($m[2] . ' weeks');
        }

        // Intervall: "every X days|weeks|months|years"
        if (preg_match('/^every (\d{1,2}) (day|week|month|year)s?$/', $pattern, $m)) {
            [$_, $interval, $unit] = $m;
            $diff = $this->startDate->diffInUnits($unit, $this->now);
            return $diff % intval($interval) === 0;
        }

        // Intervall mit Wochentag: "every 2nd monday"
        if (preg_match('/^every (\d)(st|nd|rd|th) (monday|tuesday|wednesday|thursday|friday|saturday|sunday)$/', $pattern, $m)) {
            $n = intval($m[1]);
            $day = $m[3];
            return $this->now->isSameDay(
                $this->now->copy()->startOfMonth()->next($day)->addWeeks($n - 1)
            );
        }

        return false;
    }
}

// Extension für Carbon (Helferfunktion)
Carbon::macro('diffInUnits', function (string $unit, Carbon $other): int {
    switch ($unit) {
        case 'day':
            return $this->diffInDays($other);
        case 'week':
            return $this->diffInWeeks($other);
        case 'month':
            return $this->diffInMonths($other);
        case 'year':
            return $this->diffInYears($other);
        default:
            throw new InvalidArgumentException("Unsupported unit: $unit");
    }
});
