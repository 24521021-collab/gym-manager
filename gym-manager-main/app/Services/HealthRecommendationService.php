<?php

namespace App\Services;

class HealthRecommendationService
{
    public static function build(float $bmi, ?float $bodyFat = null, ?float $previousBmi = null): array
    {
        $profile = self::classifyBmi($bmi);
        $bodyFatNote = self::bodyFatNote($bodyFat);
        $trend = self::trend($bmi, $previousBmi);

        return [
            'bmi' => round($bmi, 1),
            'status' => $profile['status'],
            'risk_level' => $profile['risk_level'],
            'goal' => $profile['goal'],
            'package_hint' => $profile['package_hint'],
            'class_hint' => $profile['class_hint'],
            'weekly_plan' => $profile['weekly_plan'],
            'nutrition_tip' => $profile['nutrition_tip'],
            'body_fat_note' => $bodyFatNote,
            'trend' => $trend,
            'summary' => trim($profile['summary'] . ' ' . $bodyFatNote . ' ' . $trend),
            'disclaimer' => 'Đề xuất này mang tính tham khảo học tập, không thay thế tư vấn y khoa hoặc huấn luyện viên cá nhân.',
        ];
    }

    private static function classifyBmi(float $bmi): array
    {
        if ($bmi < 18.5) {
            return [
                'status' => 'Thiếu cân',
                'risk_level' => 'warning',
                'goal' => 'Tăng cơ và cải thiện sức bền',
                'package_hint' => 'Gói tập 3-6 tháng kèm PT định kỳ',
                'class_hint' => 'Strength Foundation, Pilates cơ bản, lớp kỹ thuật máy tập',
                'weekly_plan' => [
                    '3 buổi tập kháng lực toàn thân, ưu tiên squat, deadlift nhẹ, push, pull.',
                    '1 buổi mobility hoặc yoga nhẹ để cải thiện biên độ vận động.',
                    '2 ngày nghỉ chủ động, đi bộ 20-30 phút.',
                ],
                'nutrition_tip' => 'Tăng 300-500 kcal mỗi ngày, ưu tiên protein, tinh bột tốt và bữa phụ sau tập.',
                'summary' => 'BMI đang thấp, hệ thống ưu tiên giáo án tăng cơ an toàn và tăng lượng năng lượng nạp vào.',
            ];
        }

        if ($bmi < 23) {
            return [
                'status' => 'Cân đối',
                'risk_level' => 'success',
                'goal' => 'Duy trì thể trạng và tăng hiệu suất',
                'package_hint' => 'Gói tập linh hoạt 3 tháng hoặc gói lớp nhóm',
                'class_hint' => 'Functional Training, HIIT vừa phải, Yoga phục hồi',
                'weekly_plan' => [
                    '2 buổi kháng lực chia thân trên/thân dưới.',
                    '2 buổi cardio hoặc lớp nhóm cường độ vừa.',
                    '1 buổi mobility, core hoặc yoga phục hồi.',
                ],
                'nutrition_tip' => 'Duy trì protein 1.6-2.0g/kg cân nặng, cân bằng tinh bột quanh buổi tập.',
                'summary' => 'BMI đang trong vùng cân đối, mục tiêu phù hợp là giữ phong độ và nâng hiệu suất.',
            ];
        }

        if ($bmi < 25) {
            return [
                'status' => 'Cần theo dõi',
                'risk_level' => 'info',
                'goal' => 'Giảm mỡ nhẹ và giữ khối cơ',
                'package_hint' => 'Gói tập 3 tháng kết hợp lớp cardio',
                'class_hint' => 'HIIT beginner, Boxing cơ bản, Strength circuit',
                'weekly_plan' => [
                    '3 buổi tập kháng lực theo vòng để giữ cơ.',
                    '2 buổi cardio 25-35 phút, ưu tiên zone 2 hoặc HIIT nhẹ.',
                    'Theo dõi cân nặng và vòng eo mỗi tuần.',
                ],
                'nutrition_tip' => 'Giảm nhẹ 200-300 kcal mỗi ngày, không cắt protein để tránh mất cơ.',
                'summary' => 'BMI hơi cao so với mức lý tưởng Châu Á, nên ưu tiên giảm mỡ có kiểm soát.',
            ];
        }

        return [
            'status' => 'Thừa cân',
            'risk_level' => 'danger',
            'goal' => 'Giảm mỡ, tăng sức bền tim mạch',
            'package_hint' => 'Gói 6 tháng có PT theo dõi tiến độ',
            'class_hint' => 'Cardio, Boxing, HIIT beginner, lớp giảm mỡ',
            'weekly_plan' => [
                '3 buổi cardio cường độ thấp-vừa, mỗi buổi 30-45 phút.',
                '2 buổi kháng lực toàn thân để bảo vệ khối cơ.',
                '1 buổi stretching/yoga để giảm đau mỏi và tăng khả năng duy trì.',
            ],
            'nutrition_tip' => 'Tạo thâm hụt 300-500 kcal mỗi ngày, ưu tiên rau, protein nạc và hạn chế đồ uống có đường.',
            'summary' => 'BMI đang cao, hệ thống ưu tiên lịch tập giảm mỡ bền vững và an toàn cho khớp.',
        ];
    }

    private static function bodyFatNote(?float $bodyFat): string
    {
        if ($bodyFat === null) {
            return 'Nhập thêm tỷ lệ mỡ (body fat) để hệ thống cá nhân hóa chính xác hơn.';
        }

        if ($bodyFat >= 30) {
            return 'Tỷ lệ mỡ cao, nên tăng cardio và kiểm soát năng lượng nạp vào.';
        }

        if ($bodyFat >= 22) {
            return 'Tỷ lệ mỡ cần theo dõi, nên kết hợp kháng lực và cardio đều đặn.';
        }

        if ($bodyFat < 10) {
            return 'Tỷ lệ mỡ rất thấp, cần chú ý phục hồi và dinh dưỡng.';
        }

        return 'Tỷ lệ mỡ ở mức ổn, có thể tập trung vào hiệu suất và duy trì.';
    }

    private static function trend(float $bmi, ?float $previousBmi): string
    {
        if ($previousBmi === null) {
            return 'Chưa có dữ liệu cũ để đánh giá xu hướng.';
        }

        $delta = round($bmi - $previousBmi, 1);

        if (abs($delta) < 0.2) {
            return 'BMI gần như ổn định so với lần đo trước.';
        }

        return $delta > 0
            ? "BMI tăng {$delta} điểm so với lần đo trước, nên kiểm tra lại mức ăn và lịch cardio."
            : 'BMI đang giảm so với lần đo trước, tiếp tục theo dõi để tránh giảm quá nhanh.';
    }
}
