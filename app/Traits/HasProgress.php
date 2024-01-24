<?php

namespace App\Traits;
use App\Models\VmmfgItem;
use App\Models\VmmfgUnit;
use DB;

trait HasProgress
{
  public function syncProgress(VmmfgUnit $vmmfgUnit)
  {
      $stats = $vmmfgUnit
          ->vmmfgTasks()
          ->leftJoin('vmmfg_items', 'vmmfg_items.id', '=', 'vmmfg_tasks.vmmfg_item_id')
          ->leftJoin('vmmfg_titles', 'vmmfg_titles.id', '=', 'vmmfg_items.vmmfg_title_id')
          ->leftJoin('vmmfg_title_categories', 'vmmfg_title_categories.id', '=', 'vmmfg_titles.vmmfg_title_category_id')
          ->groupBy('vmmfg_title_categories.id')
          ->select(
              'vmmfg_title_categories.id AS vmmfg_title_category_id',
              'vmmfg_title_categories.name AS vmmfg_title_category_name',
              DB::raw('COUNT(vmmfg_tasks.id) AS total'),
              DB::raw('CAST(SUM(vmmfg_tasks.is_done) + COUNT(CASE WHEN vmmfg_tasks.cancelled_by IS NOT NULL THEN 1 END) AS SIGNED) AS done'),
              DB::raw('COUNT(vmmfg_tasks.id)  - CAST(SUM(vmmfg_tasks.is_done) AS SIGNED) AS undone'),
              DB::raw('CAST(SUM(vmmfg_tasks.is_checked) AS SIGNED) AS checked'),
          )
          ->selectRaw('CAST(SUM(CASE WHEN vmmfg_items.sequence = ? THEN 1 ELSE 0 END) AS SIGNED) AS is_stocked', [VmmfgItem::STOCK_IN_SEQUENCE])
          ->get();

      $vmmfgUnit->update([
          'progress_json' => [
            'data' => $stats,
            'total' => $stats->sum('total'),
            'done' => $stats->sum('done'),
            'undone' => $stats->sum('undone'),
            'checked' => $stats->sum('checked'),
          ],
      ]);
  }
}
