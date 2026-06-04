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
            'disclaimer' => 'De xuat nay mang tinh tham khao hoc tap, khong thay the tu van y khoa hoac huan luyen vien ca nhan.',
        ];
    }

    private static function classifyBmi(float $bmi): array
    {
        if ($bmi < 18.5) {
            return [
                'status' => 'Thieu can',
                'risk_level' => 'warning',
                'goal' => 'Tang co va cai thien suc ben',
                'package_hint' => 'Goi tap 3-6 thang kem PT dinh ky',
                'class_hint' => 'Strength Foundation, Pilates co ban, lop ky thuat may tap',
                'weekly_plan' => [
                    '3 buoi tap khang luc toan than, uu tien squat, deadlift nhe, push, pull.',
                    '1 buoi mobility hoac yoga nhe de cai thien bien do van dong.',
                    '2 ngay nghi chu dong, di bo 20-30 phut.',
                ],
                'nutrition_tip' => 'Tang 300-500 kcal moi ngay, uu tien protein, tinh bot tot va bua phu sau tap.',
                'summary' => 'BMI dang thap, he thong uu tien giao an tang co an toan va tang nang luong nap vao.',
            ];
        }

        if ($bmi < 23) {
            return [
                'status' => 'Can doi',
                'risk_level' => 'success',
                'goal' => 'Duy tri the trang va tang hieu suat',
                'package_hint' => 'Goi tap linh hoat 3 thang hoac goi lop nhom',
                'class_hint' => 'Functional Training, HIIT vua phai, Yoga phuc hoi',
                'weekly_plan' => [
                    '2 buoi khang luc chia than tren/than duoi.',
                    '2 buoi cardio hoac lop nhom cuong do vua.',
                    '1 buoi mobility, core hoac yoga phuc hoi.',
                ],
                'nutrition_tip' => 'Duy tri protein 1.6-2.0g/kg can nang, can bang tinh bot quanh buoi tap.',
                'summary' => 'BMI dang trong vung can doi, muc tieu phu hop la giu phong do va nang hieu suat.',
            ];
        }

        if ($bmi < 25) {
            return [
                'status' => 'Can theo doi',
                'risk_level' => 'info',
                'goal' => 'Giam mo nhe va giu khoi co',
                'package_hint' => 'Goi tap 3 thang ket hop lop cardio',
                'class_hint' => 'HIIT beginner, Boxing co ban, Strength circuit',
                'weekly_plan' => [
                    '3 buoi tap khang luc theo vong de giu co.',
                    '2 buoi cardio 25-35 phut, uu tien zone 2 hoac HIIT nhe.',
                    'Theo doi can nang va vong eo moi tuan.',
                ],
                'nutrition_tip' => 'Giam nhe 200-300 kcal moi ngay, khong cat protein de tranh mat co.',
                'summary' => 'BMI hoi cao so voi muc ly tuong Chau A, nen uu tien giam mo co kiem soat.',
            ];
        }

        return [
            'status' => 'Thua can',
            'risk_level' => 'danger',
            'goal' => 'Giam mo, tang suc ben tim mach',
            'package_hint' => 'Goi 6 thang co PT theo doi tien do',
            'class_hint' => 'Cardio, Boxing, HIIT beginner, lop giam mo',
            'weekly_plan' => [
                '3 buoi cardio cuong do thap-vua, moi buoi 30-45 phut.',
                '2 buoi khang luc toan than de bao ve khoi co.',
                '1 buoi stretching/yoga de giam dau moi va tang kha nang duy tri.',
            ],
            'nutrition_tip' => 'Tao tham hut 300-500 kcal moi ngay, uu tien rau, protein nac va han che do uong co duong.',
            'summary' => 'BMI dang cao, he thong uu tien lich tap giam mo ben vung va an toan cho khop.',
        ];
    }

    private static function bodyFatNote(?float $bodyFat): string
    {
        if ($bodyFat === null) {
            return 'Nhap them body fat de he thong ca nhan hoa chinh xac hon.';
        }

        if ($bodyFat >= 30) {
            return 'Ty le mo cao, nen tang cardio va kiem soat nang luong nap vao.';
        }

        if ($bodyFat >= 22) {
            return 'Ty le mo can theo doi, nen ket hop khang luc va cardio deu dan.';
        }

        if ($bodyFat < 10) {
            return 'Ty le mo rat thap, can chu y phuc hoi va dinh duong.';
        }

        return 'Ty le mo o muc on, co the tap trung vao hieu suat va duy tri.';
    }

    private static function trend(float $bmi, ?float $previousBmi): string
    {
        if ($previousBmi === null) {
            return 'Chua co du lieu cu de danh gia xu huong.';
        }

        $delta = round($bmi - $previousBmi, 1);

        if (abs($delta) < 0.2) {
            return 'BMI gan nhu on dinh so voi lan do truoc.';
        }

        return $delta > 0
            ? "BMI tang {$delta} diem so voi lan do truoc, nen kiem tra lai muc an va lich cardio."
            : 'BMI dang giam so voi lan do truoc, tiep tuc theo doi de tranh giam qua nhanh.';
    }
}
