<?php

namespace App\Filament\Widgets;

use App\Models\Logbook;
use App\Enums\EquipmentType;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class StatsOverview extends BaseWidget
{
  protected static ?string $pollingInterval = null;

  protected function getHeading(): ?string
  {
    return 'Stats Overview';
  }

  protected function getDescription(): ?string
  {
    return "Equipment usage statistics of " . now()->format('F') . " " . now()->format('Y');
  }

  protected function getColumns(): int
  {
    return 2;
  }

  protected function getStats(): array
  {
    $year = now()->year;
    $month = now()->month;

    $query = Logbook::query()
      ->whereYear('date', $year)
      ->whereMonth('date', $month)
      ->selectRaw('
        COALESCE(SUM(work_time), 0) as work,
        COALESCE(SUM(delivery_time), 0) as delivery,
        COALESCE(SUM(trailer_time), 0) as trailer
      ');

    $crane = $query->clone()->where('type', EquipmentType::CRANE)->first();
    $trailer = $query->clone()->where('type', EquipmentType::TRAILER)->first();

    return [
      Stat::make('Crane usage time', $crane->work + $crane->delivery)->description('Total time usage for crane equipment'),
      Stat::make('Trailer usage time', $trailer->trailer)->description('Total time usage for trailer equipment'),
    ];
  }
}
