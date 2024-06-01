<ul class="p-0 m-0">
    @if ($transactions->isEmpty())
    <li class="d-flex mb-4 pb-1">
        <div class="d-flex w-100 flex-wrap align-items-center justify-content-center gap-2">
            <div class="me-2">
                <h2 class="text-muted d-block mb-1">No transaction found</h2>
            </div>
        </div>
    </li>
    @else
    @foreach ($transactions as $transaction)
    <a href="{{url('wallet/wallet-transaction/view/'.$transaction->id)}}">
        <li class="d-flex mb-4 pb-1">
            <div class="avatar flex-shrink-0 me-3">
                <img src="{{ url('public/assets/img/icons/unicons/wallet.png') }}" alt="User" class="rounded" />
            </div>
            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                <div class="me-2">
                    <small class="text-muted d-block mb-1">Wallet</small>
                    <h6 class="mb-0">{{$transaction->getType()}}</h6>
                    <p>{{date('Y-m-d H:i:s A', strtotime($transaction->created_at))}}</p>
                </div>
                <div class="user-progress d-flex align-items-center gap-1">
                    <h6 class="mb-0">{{$transaction->amount}}</h6>
                </div>
            </div>
        </li>
    </a>

    @endforeach
    <div>
        {{ $transactions->links('pagination::bootstrap-5') }}
    </div>
    @endif
</ul>