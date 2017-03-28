<?php

  final class MyDate {

      const DATE_DELIMITER = '/';

      private $year;
      private $month;
      private $day;
      private $jdn;

      public function __construct(int $year, int $month, int $day)
      {
          $this->year = $year;
          $this->month = $month;
          $this->day = $day;
          $this->jdn = $this->calcJDN();
      }

      public static function fromString(string $s): MyDate
      {
          [$year, $month, $day] = explode(self::DATE_DELIMITER, $s);
          return new self($year, $month, $day);
      }

      /**
       * @return int
       */
      public function getJdn(): int
      {
          return $this->jdn;
      }

      /**
       * Calculates Julian Day Number
       * https://en.wikipedia.org/wiki/Julian_day
       *
       * @return int
       */
      protected function calcJDN()
      {
          $a = (int) floor((14 - $this->month)/12);
          $y = $this->year + 4800 - $a;
          $m = $this->month + 12 * $a - 3;
          $jdn = $this->day + (int) floor((153*$m+2)/5) + 365*$y
                  + (int) floor($y/4) - (int) floor($y/100)
                  + (int) floor($y/400) - 32045;
          return $jdn;
      }

      public static function diff(string $start, string $end): stdClass
      {
          $start_date = self::fromString($start);
          $end_date = self::fromString($end);

          $total_days = $end_date->getJdn() - $start_date->getJdn();
          $invert = false;
          if ($total_days < 0) {
              $swp = clone $end_date;
              $end_date = clone $start_date;
              $start_date = $swp;
              $invert = true;
          }

          $years = $end_date->year - $start_date->year;

          $months = $end_date->month - $start_date->month;
          if ($months < 0) {
              $months = $end_date->month + 11 - $start_date->month;
              $years--;
          }
          // Sample object:
          return (object) array(
              'years' => $years,
              'months' => $months,
              'days' => $end_date->day - $start_date->day,
              'total_days' => $total_days,
              'invert' => $invert
          );

      }

  }
