<div class="mt-8" id="paginationWrapper">
    {{ $users->appends(request()->query())->links() }}
</div>