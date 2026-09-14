<?php

namespace App\Support;

class Grades
{
    public static function for(float $score): array
    {
        return match (true) {
            $score >= 75 => ['grade' => 'A', 'remark' => 'Excellent'],
            $score >= 65 => ['grade' => 'B', 'remark' => 'Very Good'],
            $score >= 55 => ['grade' => 'C', 'remark' => 'Good'],
            $score >= 45 => ['grade' => 'D', 'remark' => 'Pass'],
            $score >= 40 => ['grade' => 'E', 'remark' => 'Weak Pass'],
            default      => ['grade' => 'F', 'remark' => 'Fail'],
        };
    }
}
