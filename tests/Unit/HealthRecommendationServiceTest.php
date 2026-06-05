<?php

namespace Tests\Unit;

use App\Services\HealthRecommendationService;
use PHPUnit\Framework\TestCase;

class HealthRecommendationServiceTest extends TestCase
{
    public function test_it_recommends_weight_gain_plan_for_underweight_bmi(): void
    {
        $recommendation = HealthRecommendationService::build(17.8, 12.0);

        $this->assertSame('Thiếu cân', $recommendation['status']);
        $this->assertSame('warning', $recommendation['risk_level']);
        $this->assertStringContainsString('Tăng cơ', $recommendation['goal']);
        $this->assertNotEmpty($recommendation['weekly_plan']);
    }

    public function test_it_recommends_fat_loss_plan_for_high_bmi(): void
    {
        $recommendation = HealthRecommendationService::build(27.2, 31.0, 26.6);

        $this->assertSame('Thừa cân', $recommendation['status']);
        $this->assertSame('danger', $recommendation['risk_level']);
        $this->assertStringContainsString('Giảm mỡ', $recommendation['goal']);
        $this->assertStringContainsString('cardio', strtolower($recommendation['body_fat_note']));
        $this->assertStringContainsString('BMI tăng', $recommendation['trend']);
    }
}
