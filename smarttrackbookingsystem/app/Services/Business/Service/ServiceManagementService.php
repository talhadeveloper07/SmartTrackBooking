<?php

namespace App\Services\Business\Service;

use App\Models\Service;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ServiceManagementService
{
    public function create($business, array $data)
    {
        return DB::transaction(function () use ($business, $data) {

        $this->validateDuplicateDurations($data['durations']);
            // 1️⃣ Create main service
            $service = Service::create([
                'business_id' => $business->id,
                'name' => $data['name'],
                'slug' => $this->generateUniqueSlug($business->id, $data['name']),
                'description' => $data['description'] ?? null,
                'status' => $data['status'],
            ]);

            // 2️⃣ Create durations
            $this->storeDurations($service, $data['durations']);

            return $service;
        });
    }

    private function storeDurations($service, array $durations)
    {
        foreach ($durations as $duration) {

            $service->durations()->create([
                'duration_name'   => $duration['duration_name'] ?? null,
                'duration_minutes'=> $duration['duration_minutes'],
                'price'           => $duration['price'],
                'deposit'         => $duration['deposit'] ?? 0,
                'status'          => $duration['status'],
            ]);
        }
    }
    public function update($business, $service, array $data)
    {
        if ($service->business_id !== $business->id) {
            abort(404);
        }

        return DB::transaction(function () use ($business, $service, $data) {

            // 1️⃣ Prevent duplicate duration minutes
            $this->validateDuplicateDurations($data['durations']);

            // 2️⃣ Update basic service info
            $service->update([
                'name' => $data['name'],
                'slug' => $service->name !== $data['name']
                    ? $this->generateUniqueSlug($business->id, $data['name'], $service->id)
                    : $service->slug,
                'description' => $data['description'] ?? null,
                'status' => $data['status'],
            ]);

            // 3️⃣ Sync durations
            $this->syncDurations($service, $data['durations']);

            return $service;
        });
    }
    private function syncDurations($service, array $durations)
    {
        $incomingIds = [];

        foreach ($durations as $row) {

            if (!empty($row['id'])) {

                $incomingIds[] = (int) $row['id'];

                $duration = $service->durations()
                    ->where('id', $row['id'])
                    ->first();

                if ($duration) {
                    $duration->update([
                        'duration_name'    => $row['duration_name'] ?? null,
                        'duration_minutes' => $row['duration_minutes'],
                        'price'            => $row['price'],
                        'deposit'          => $row['deposit'] ?? 0,
                        'status'           => $row['status'],
                    ]);
                }

            } else {

                $new = $service->durations()->create([
                    'duration_name'    => $row['duration_name'] ?? null,
                    'duration_minutes' => $row['duration_minutes'],
                    'price'            => $row['price'],
                    'deposit'          => $row['deposit'] ?? 0,
                    'status'           => $row['status'],
                ]);

                $incomingIds[] = $new->id;
            }
        }

        // Delete removed durations
        $service->durations()
            ->whereNotIn('id', $incomingIds)
            ->delete();
    }
        private function generateUniqueSlug($businessId, $name)
    {
        $baseSlug = Str::slug($name);
        $slug = $baseSlug;
        $counter = 1;

        while (
            Service::where('business_id', $businessId)
                ->where('slug', $slug)
                ->exists()
        ) {
            $slug = $baseSlug . '-' . $counter++;
        }

        return $slug;
    }
    private function validateDuplicateDurations(array $durations)
    {
        $duplicates = collect($durations)
            ->pluck('duration_minutes')
            ->duplicates();

        if ($duplicates->isNotEmpty()) {
            throw new \Exception('Duplicate duration minutes are not allowed.');
        }
    }

}