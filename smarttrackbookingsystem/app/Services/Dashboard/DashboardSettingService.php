<?php

namespace App\Services\Dashboard;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DashboardSettingService
{
    public function update(Model $owner, array $data): void
    {
        DB::transaction(function () use ($owner, $data) {

            $setting = $owner->dashboardSetting()->firstOrCreate([]);

            // Upload logo
            if (!empty($data['logo'])) {
                if ($setting->logo) Storage::disk('public')->delete($setting->logo);

                $folder = strtolower(class_basename($owner)) . '/' . $owner->id . '/branding';
                $setting->logo = $data['logo']->store($folder, 'public');
            }

            // Upload favicon (optional)
            if (!empty($data['favicon'])) {
                if ($setting->favicon) Storage::disk('public')->delete($setting->favicon);

                $folder = strtolower(class_basename($owner)) . '/' . $owner->id . '/branding';
                $setting->favicon = $data['favicon']->store($folder, 'public');
            }

            // Colors
            $setting->fill([
                'primary_color'   => $data['primary_color'] ?? $setting->primary_color,
                'secondary_color' => $data['secondary_color'] ?? $setting->secondary_color,
                'sidebar_bg'      => $data['sidebar_bg'] ?? $setting->sidebar_bg,
                'sidebar_text'    => $data['sidebar_text'] ?? $setting->sidebar_text,
                'topbar_bg'       => $data['topbar_bg'] ?? $setting->topbar_bg,
                'topbar_text'     => $data['topbar_text'] ?? $setting->topbar_text,
            ])->save();
        });
    }
}