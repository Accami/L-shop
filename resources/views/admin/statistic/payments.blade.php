{{-- Layout and design by WhileD0S <https://vk.com/whiled0s>  --}}
@extends('layouts.shop')

@section('title')
    @lang('content.admin.other.statistics.payments.title')
@endsection

@section('content')
    <div id="content-container">
        <div id="cart-header" class="z-depth-1">
            <h1><i class="fa fa-list fa-lg fa-left-big"></i>@lang('content.admin.other.statistics.payments.title')</h1>
        </div>
        <div class="product-container">
            @if($payments->count())
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>@lang('content.profile.payments.table.id')</th>
                            <th>@lang('content.profile.payments.table.type')</th>
                            <th>@lang('content.profile.payments.table.products')</th>
                            <th>@lang('content.admin.other.statistics.payments.table.user')</th>
                            <th>@lang('content.profile.payments.table.sum')</th>
                            <th>@lang('content.all.server')</th>
                            <th>@lang('content.profile.payments.table.status')</th>
                            <th>@lang('content.profile.payments.table.created_at')</th>
                            <th>@lang('content.profile.payments.table.completed_at')</th>
                            <th>@lang('content.profile.payments.table.service')</th>
                            <th>@lang('content.profile.payments.table.actions')</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($payments as $payment)
                            <tr @if($payment->isCompleted()) class="table-success" @endif>
                                <td>{{ $payment->getId() }}</td>
                                <td>@if($payment->getProducts()) @lang('content.profile.payments.table.shopping') @else @lang('content.profile.payments.table.fillupbalance') @endif</td>
                                <td>@if($payment->getProducts())<a class="btn btn-info btn-sm profile-payments-info" data-url="{{ route('admin.statistic.payments.info', ['server' => $payment->getServerId(), 'payment' => $payment->getId()]) }}">@lang('content.profile.payments.table.more')</a> @endif</td>
                                <td>@if($payment->getUserId()){{ $payment->getUser()->getUsername() }}@else {{ $payment->getUsername() }} @endif</td>
                                <td>{{ $payment->getCost() }} {!! $currency !!}</td>
                                @foreach($servers as $server)
                                    @if($server->getId() === $payment->getServerId())
                                        <td>{{ $server->getName() }}</td>
                                    @endif
                                @endforeach
                                <td>@if($payment->isCompleted()) @lang('content.profile.payments.table.completed') @else @lang('content.profile.payments.table.not_completed') @endif</td>
                                <td>{{ $payment->getCreatedAt() }}</td>
                                <td>@if($payment->isCompleted()) {{ $payment->getUpdatedAt() }} @endif</td>
                                <td>@if($payment->getService()) {{ $payment->getService() }} @endif</td>
                                <td>@if(!$payment->isCompleted()) <a href="{{ route('admin.statistic.payments.complete', ['server' => $payment->getServerId(), 'payment' => $payment->getId()]) }}" class="btn green btn-sm">@lang('content.profile.payments.table.complete')</a> @endif</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                {{ $payments->links('components.pagination') }}
            @else
                <div class="text-center">
                    <h3>@lang('content.profile.payments.table.empty')</h3>
                </div>
            @endif
        </div>
    </div>
    @component('components.modal')
    @slot('id')
    profile-payments-modal
    @endslot
    @slot('title')
        @lang('content.profile.payments.table.details_modal.title')
    @endslot
    @slot('buttons')
    <button type="button" class="btn btn-warning" data-dismiss="modal">@lang('content.profile.payments.table.details_modal.btn')</button>
    @endslot
    <div class="md-form">
        <div class="table-responsive">
            <table class="table">
                <thead>
                <tr>
                    <th>@lang('content.profile.payments.table.details_modal.table.image')</th>
                    <th>@lang('content.profile.payments.table.details_modal.table.name')</th>
                    <th>@lang('content.profile.payments.table.details_modal.table.count')</th>
                </tr>
                </thead>
                <tbody id="profile-payments-modal-products">
                    {{-- Here, the content is inserted using ajax.  --}}
                </tbody>
            </table>
        </div>
    </div>
    @endcomponent
@endsection
