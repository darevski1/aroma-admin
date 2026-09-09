@extends('layouts.app')

@section('title', 'ITPlus | Почетна')
@section('meta_description', 'Комплетни ИТ решенија за вашиот бизнис: инфраструктура, одржување, софтверски решенија,
безбедност, принтери и техничка поддршка.')


@section('content')

{{-- Map of section slug → partial --}}
@php
$sectionPartials = [
    'hero'           => 'homepage-sections._hero',
    'sticky-banner'  => 'homepage-sections._sticky-banner',
    'brand-marquee'  => 'homepage-sections._brand-marquee',
    'services'       => 'homepage-sections._services',
    'cenobots'       => 'homepage-sections._cenobots',
    'printers'       => 'homepage-sections._printers',
    'clients'        => 'homepage-sections._clients',
    'garmin'         => 'homepage-sections._garmin',
    'cta'            => 'homepage-sections._cta',
];
@endphp

@foreach ($sections as $section)
    @if (isset($sectionPartials[$section->slug]))
        @include($sectionPartials[$section->slug])
    @elseif (!empty($section->content))
        {{-- Admin-created section with its own content --}}
        @include('homepage-sections._custom')
    @endif
@endforeach

@endsection
