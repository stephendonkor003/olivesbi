@extends('layout.base')

@section('title', __('Forbidden'))
@section('code', '403')
@section('page-content', __($exception->getMessage() ?: 'Forbidden'))
