<?php

namespace App\Services\Funnels;

use App\Models\Funnel;
use App\Models\FunnelElement;
use App\Models\FunnelResult;
use App\Models\FunnelStep;
use Illuminate\Support\Collection;

class FunnelExecutionEngine
{
    /**
     * Calculate total score based on submitted answers and element scoring definitions.
     */
    public function calculateScore(Funnel $funnel, array $answers): int
    {
        $totalScore = 0;
        $funnel->loadMissing('steps.elements');

        foreach ($funnel->steps as $step) {
            foreach ($step->elements as $element) {
                $val = $answers[$element->id] ?? $answers[$element->question_key] ?? null;
                if ($val === null) {
                    continue;
                }

                $properties = $element->properties ?? [];
                $options = $properties['options'] ?? [];

                if (is_array($options)) {
                    foreach ($options as $opt) {
                        $optVal = $opt['value'] ?? $opt['label'] ?? null;
                        $optScore = (int) ($opt['score'] ?? 0);

                        if (is_array($val)) {
                            if (in_array($optVal, $val, true)) {
                                $totalScore += $optScore;
                            }
                        } else {
                            if ((string) $val === (string) $optVal) {
                                $totalScore += $optScore;
                            }
                        }
                    }
                }
            }
        }

        return $totalScore;
    }

    /**
     * Determine the matching result for a response based on total score & conditions.
     */
    public function resolveResult(Funnel $funnel, int $score, array $answers): ?FunnelResult
    {
        $funnel->loadMissing('results');

        foreach ($funnel->results as $result) {
            $minScoreMatch = $result->min_score === null || $score >= $result->min_score;
            $maxScoreMatch = $result->max_score === null || $score <= $result->max_score;

            $logicConditions = $result->logic_conditions ?? [];
            $logicMatch = true;

            if (! empty($logicConditions['rules']) && is_array($logicConditions['rules'])) {
                $operator = strtoupper($logicConditions['operator'] ?? 'AND');
                $logicMatch = $this->evaluateRuleGroup($logicConditions['rules'], $operator, $answers);
            }

            if ($minScoreMatch && $maxScoreMatch && $logicMatch) {
                return $result;
            }
        }

        return $funnel->results->first();
    }

    /**
     * Evaluate rule group with AND/OR logical operator.
     */
    protected function evaluateRuleGroup(array $rules, string $groupOperator, array $answers): bool
    {
        $results = [];

        foreach ($rules as $rule) {
            $elementId = $rule['element_id'] ?? null;
            $operator = $rule['operator'] ?? '=';
            $targetValue = $rule['value'] ?? null;

            $userVal = $answers[$elementId] ?? null;
            $results[] = $this->evaluateCondition($userVal, $operator, $targetValue);
        }

        if (empty($results)) {
            return true;
        }

        return $groupOperator === 'OR'
            ? in_array(true, $results, true)
            : ! in_array(false, $results, true);
    }

    /**
     * Evaluate single condition logic operator.
     */
    public function evaluateCondition($actualValue, string $operator, $compareValue): bool
    {
        if (is_array($actualValue)) {
            $actualStr = implode(',', $actualValue);
        } else {
            $actualStr = (string) $actualValue;
        }

        $compareStr = (string) $compareValue;

        return match ($operator) {
            '=', '==' => $actualStr === $compareStr,
            '!=', '<>' => $actualStr !== $compareStr,
            '>' => is_numeric($actualStr) && is_numeric($compareStr) ? (float) $actualStr > (float) $compareStr : $actualStr > $compareStr,
            '<' => is_numeric($actualStr) && is_numeric($compareStr) ? (float) $actualStr < (float) $compareStr : $actualStr < $compareStr,
            '>=' => is_numeric($actualStr) && is_numeric($compareStr) ? (float) $actualStr >= (float) $compareStr : $actualStr >= $compareStr,
            '<=' => is_numeric($actualStr) && is_numeric($compareStr) ? (float) $actualStr <= (float) $compareStr : $actualStr <= $compareStr,
            'contains' => str_contains(strtolower($actualStr), strtolower($compareStr)),
            'not_contains', 'not contains' => ! str_contains(strtolower($actualStr), strtolower($compareStr)),
            default => $actualStr === $compareStr,
        };
    }
}
