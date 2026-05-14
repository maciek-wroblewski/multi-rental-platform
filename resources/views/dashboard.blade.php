<div class="grid grid-cols-2 gap-4">

    <div class="bg-gray-800 p-4 rounded">
        <h2 class="text-lg font-bold">My Items</h2>
        <p>{{ $itemsCount }}</p>
    </div>

    <div class="bg-gray-800 p-4 rounded">
        <h2 class="text-lg font-bold">Active Rentals</h2>
        <p>{{ $activeRentals }}</p>
    </div>

    <div class="bg-gray-800 p-4 rounded">
        <h2 class="text-lg font-bold">Returned Rentals</h2>
        <p>{{ $returnedRentals }}</p>
    </div>

    <div class="bg-gray-800 p-4 rounded">
        <h2 class="text-lg font-bold">Cancelled Rentals</h2>
        <p>{{ $cancelledRentals }}</p>
    </div>

</div>