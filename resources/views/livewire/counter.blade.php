<div class="card">
    <div class="card-body">
        <h5 class="card-title">Test Counter</h5>
        <div class="d-flex align-items-center">
            <button wire:click="increment" class="btn btn-primary">+</button>
            <h1 class="mx-4">{{ $count }}</h1>
        </div>
    </div>
</div>
