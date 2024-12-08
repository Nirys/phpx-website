<?php

namespace App\Services;

use App\Enums\DomainStatus;
use App\Enums\RootDomains;
use App\Models\ExternalGroup;
use App\Models\Group;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Stringable;

class PHPX
{
    public function isGlobalSite()
    {
        return (collect(RootDomains::cases())
            ->map(fn(RootDomains $case) => $case->value)
            ->contains(request()->host()));
    }

    public function getNetwork()
    {
        return Cache::remember('phpx-network', now()->addWeek(), function () {
            $groups = Group::query()
                ->where('domain_status', DomainStatus::Confirmed)
                ->get()
                ->map(fn(Group $group) => [$group::class, $group->attributesToArray()]);

            $external = ExternalGroup::query()->get()
                ->map(fn(ExternalGroup $group) => [$group::class, $group->attributesToArray()]);

            return $groups->merge($external)->values()->toArray();
        });
    }

    public function getRoutingDomains()
    {
        //return Cache::remember('phpx-domains', now()->addWeek(), function () {
        return Group::where('domain_status', DomainStatus::Confirmed)->pluck('domain')->map(function ($item) {
            $item = new Stringable($item);
            $item = (App::isLocal()) ? $item->replaceEnd('.com', '.test') : $item;
            return $item->__toString();
        })->toArray();
        //});
    }
}
