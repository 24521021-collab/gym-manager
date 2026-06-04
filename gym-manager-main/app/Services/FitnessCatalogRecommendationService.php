<?php

namespace App\Services;

use App\Models\GymClass;
use App\Models\GymPackage;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class FitnessCatalogRecommendationService
{
    public static function forBmi(?float $bmi): array
    {
        if ($bmi === null) {
            return [
                'category' => 'general',
                'packages' => collect(),
                'classes' => collect(),
            ];
        }

        $category = self::category($bmi);

        return [
            'category' => $category,
            'packages' => self::packages($category),
            'classes' => self::classes($category),
        ];
    }

    public static function forBmiAsArray(?float $bmi): array
    {
        $recommendations = self::forBmi($bmi);

        return [
            'category' => $recommendations['category'],
            'packages' => $recommendations['packages']->map(fn (GymPackage $package) => [
                'id' => $package->id,
                'name' => $package->package_name,
                'duration_days' => $package->duration_days,
                'price' => (float) $package->price,
                'url' => url('/#membership-packages'),
            ])->values(),
            'classes' => $recommendations['classes']->map(fn (GymClass $class) => [
                'id' => $class->id,
                'name' => $class->name,
                'price' => (float) $class->price,
                'total_sessions' => $class->total_sessions,
                'url' => route('classes.index', ['recommended_class' => $class->id]),
            ])->values(),
        ];
    }

    public static function packageIds(?float $bmi): array
    {
        return self::forBmi($bmi)['packages']->pluck('id')->all();
    }

    private static function category(float $bmi): string
    {
        if ($bmi < 18.5) {
            return 'muscle_gain';
        }

        if ($bmi < 23) {
            return 'balanced';
        }

        return 'fat_loss';
    }

    private static function packages(string $category): Collection
    {
        $packages = GymPackage::orderBy('price')->get();

        $matched = $packages->filter(function (GymPackage $package) use ($category) {
            $text = self::normalize($package->package_name . ' ' . $package->description);

            return match ($category) {
                'muscle_gain' => self::containsAny($text, ['tang co', 'gym', 'co bap', 'cam ket', 'tac phong']),
                'balanced' => self::containsAny($text, ['khoe', 'bao luu', 'stress', 'hoc ky']),
                'fat_loss' => self::containsAny($text, ['dang chuan', 'dot mo', 'stress', 'cardio', 'inbody']),
                default => false,
            };
        });

        if ($matched->isEmpty()) {
            $matched = match ($category) {
                'muscle_gain' => $packages->sortByDesc('duration_days'),
                'balanced' => $packages->sortBy(fn (GymPackage $package) => abs($package->duration_days - 90)),
                'fat_loss' => $packages->sortByDesc('duration_days'),
                default => $packages,
            };
        }

        return $matched->take(2)->values();
    }

    private static function classes(string $category): Collection
    {
        $classes = GymClass::with('pt.user')->get();

        $matched = $classes->filter(function (GymClass $class) use ($category) {
            $text = self::normalize($class->name . ' ' . $class->description);

            return match ($category) {
                'muscle_gain' => self::containsAny($text, ['gym', 'tang co', 'strength', 'co bap']),
                'balanced' => self::containsAny($text, ['yoga', 'pilates', 'phuc hoi', 'can bang', 'functional']),
                'fat_loss' => self::containsAny($text, ['cardio', 'boxing', 'kickboxing', 'hiit', 'spinning', 'dot mo']),
                default => false,
            };
        });

        return ($matched->isEmpty() ? $classes : $matched)->take(3)->values();
    }

    private static function normalize(string $value): string
    {
        return Str::of($value)
            ->ascii()
            ->lower()
            ->replaceMatches('/[^a-z0-9\s]+/', ' ')
            ->squish()
            ->toString();
    }

    private static function containsAny(string $text, array $keywords): bool
    {
        foreach ($keywords as $keyword) {
            if (Str::contains($text, $keyword)) {
                return true;
            }
        }

        return false;
    }
}
