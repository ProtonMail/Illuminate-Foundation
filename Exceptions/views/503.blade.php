@extends('errors::layout')

@section('code', '503')
@section('title', 'Service Unavailable')

@section('image')
<div style="background-image: url('/svg/503.svg');" class="absolute pin bg-cover bg-no-repeat md:bg-left lg:bg-center">
</div>
@endsection

@section('message', 'Sorry, we are doing some maintenance. Please check back soon.')
