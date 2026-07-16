<?php

use App\Actions\TrafficLight\TrafficLightEngine;
use App\Enums\TrafficLight;
use Illuminate\Support\Carbon;

beforeEach(function () {
    $this->engine = new TrafficLightEngine;
    $this->deadline = Carbon::parse('2026-01-31');
});

test('Normal jauh sebelum tarikh had', function () {
    expect($this->engine->evaluate($this->deadline, Carbon::parse('2025-12-01'), 30))
        ->toBe(TrafficLight::Normal);
});

test('Kuning apabila menghampiri tarikh had', function () {
    expect($this->engine->evaluate($this->deadline, Carbon::parse('2026-01-15'), 30))
        ->toBe(TrafficLight::Kuning);
});

test('Kuning pada hari tarikh had (belum melepasi)', function () {
    expect($this->engine->evaluate($this->deadline, Carbon::parse('2026-01-31'), 30))
        ->toBe(TrafficLight::Kuning);
});

test('Merah selepas melepasi tarikh had', function () {
    expect($this->engine->evaluate($this->deadline, Carbon::parse('2026-02-05'), 30))
        ->toBe(TrafficLight::Merah);
});
