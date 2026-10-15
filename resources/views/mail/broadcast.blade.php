@extends('mail.layout')

@section('title', $subject)

@section('content')
<div style="margin: 0;">
    {!! $html !!}
</div>
@endsection
