@extends('layouts.app')
<livewire:navbar />
@section('title', 'ITPlus | Dynamic Page')
@section('meta_description', 'Dynamic ITPlus page with service and navigation information.')
@section('content')


<h1>Parent: {{ $parent }}</h1>

@endsection
