<?php

namespace App\Traits;
use App\Models\VmmfgItem;
use App\Models\VmmfgUnit;
use DB;

trait HasProgress
{
  public function syncProgress(VmmfgUnit $vmmfgUnit)
  {
      // $stats = $vmmfgUnit
      //     ->vmmfgTasks()
      //     ->leftJoin('vmmfg_items', 'vmmfg_items.id', '=', 'vmmfg_tasks.vmmfg_item_id')
      //     ->leftJoin('vmmfg_titles', 'vmmfg_titles.id', '=', 'vmmfg_items.vmmfg_title_id')
      //     ->leftJoin('vmmfg_title_categories', 'vmmfg_title_categories.id', '=', 'vmmfg_titles.vmmfg_title_category_id')
      //     ->groupBy('vmmfg_title_categories.id')
      //     ->select(
      //         'vmmfg_title_categories.id AS vmmfg_title_category_id',
      //         DB::raw('COUNT(vmmfg_tasks.id) AS total'),
      //         DB::raw('CAST(SUM(vmmfg_tasks.is_done) + COUNT(CASE WHEN vmmfg_tasks.cancelled_by IS NOT NULL THEN 1 END) AS SIGNED) AS done'),
      //         DB::raw('COUNT(vmmfg_tasks.id)  - CAST(SUM(vmmfg_tasks.is_done) AS SIGNED) AS undone'),
      //         DB::raw('CAST(SUM(vmmfg_tasks.is_checked) AS SIGNED) AS checked'),
      //     )
      //     ->selectRaw('CAST(SUM(CASE WHEN vmmfg_items.sequence = ? THEN 1 ELSE 0 END) AS SIGNED) AS is_stocked', [VmmfgItem::STOCK_IN_SEQUENCE])
      //     ->get();

      $stats =
          VmmfgItem::query()
          ->leftJoin('vmmfg_titles', 'vmmfg_titles.id', '=', 'vmmfg_items.vmmfg_title_id')
          ->leftJoin('vmmfg_scopes', 'vmmfg_scopes.id', '=', 'vmmfg_titles.vmmfg_scope_id')
          ->leftJoin('vmmfg_units', 'vmmfg_units.vmmfg_scope_id', '=', 'vmmfg_scopes.id')
          ->leftJoin('vmmfg_title_categories', 'vmmfg_title_categories.id', '=', 'vmmfg_titles.vmmfg_title_category_id')
          ->leftJoin('vmmfg_tasks', function($join) {
              $join->on('vmmfg_tasks.vmmfg_item_id', '=', 'vmmfg_items.id')
                  ->on('vmmfg_tasks.vmmfg_unit_id', '=', 'vmmfg_units.id');

          })
          ->where('vmmfg_units.id', $vmmfgUnit->id)
          ->groupBy('vmmfg_title_categories.id')
          ->select(
              'vmmfg_title_categories.id AS vmmfg_title_category_id',
              DB::raw('COUNT(vmmfg_items.id) AS total'),
              DB::raw('COALESCE(CAST(SUM(vmmfg_tasks.is_done) + COUNT(CASE WHEN vmmfg_tasks.cancelled_by IS NOT NULL THEN 1 END) AS SIGNED), 0) AS done'),
              DB::raw('COALESCE(COUNT(vmmfg_items.id)  - CAST(SUM(vmmfg_tasks.is_done) AS SIGNED), 0) AS undone'),
              DB::raw('COALESCE(CAST(SUM(vmmfg_tasks.is_checked) AS SIGNED), 0) AS checked'),
          )
          ->selectRaw('CAST(SUM(CASE WHEN vmmfg_items.sequence = ? THEN 1 ELSE 0 END) AS SIGNED) AS is_stocked', [VmmfgItem::STOCK_IN_SEQUENCE])
          ->get();


      $progress = [
          'data' => $stats,
          'total' => $stats->sum('total'),
          'done' => $stats->sum('done'),
          'undone' => $stats->sum('undone'),
          'checked' => $stats->sum('checked'),
      ];

      $vmmfgUnit->update([
          'progress_json' => $progress,
      ]);
  }
}
