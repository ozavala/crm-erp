@extends('layouts.app')

@section('title', 'Payments Details')

@section('content')
<div class='container'>
    <div class='card'>
        <div class='card-header'>
            Paymets Details
        </div>
        <div class='card-body'>
            <p><strong>Date:</strong> {{$payable->payment_date}}</p>
            <p><strong>Amount:</strong> {{$payable->amount}}</p>
            <p><strong> Status:</strong> {{$payable->status}}</p>
        </div>
        <div class='card-footer'>
            <a href="{{ route('payments.index')}}" class="btn btn-secondary">Back to List</a>
        
        </div>
        
            </br>
        </div>
    </div>
</div>'   

@endsection