@if(!user()->freePages->isEmpty())
<div class="card">
    <div class="card-content">
        <div class="card-title">@lang('print.free')</div>
        <div id="free-page-table"></div>
        <blockquote>@lang('print.free_pages_description')</blockquote>
    </div>
</div>
<script type="text/javascript" src="{{ mix('js/moment.min.js') }}"></script>
<script type="application/javascript">
    $(document).ready(function () {
        var dateFormatter = function(cell, formatterParams){
                var value = cell.getValue();
                if(value){
                    value = moment(value).format("YYYY. MM. DD. HH:mm");
                }
                return value;
            }
        var table = new Tabulator("#free-page-table", {
            paginationSize: 10,
            layout: "fitColumns",
            pagination: "remote", //enable remote pagination
            ajaxSorting: true,
            ajaxFiltering: true,
            columnMinWidth: 150,
            headerSort: false,
            ajaxURL: "{{ $route }}", //set url for ajax request
            placeholder: "@lang('print.no_free_pages')",
            columns: [
                @if($admin)
                @can('viewAny', App\Models\FreePages::class)
                {
                    title: "@lang('internet.created_at')",
                    field: "created_at",
                    sorter: "datetime",
                    formatter:dateFormatter,
                    headerFilter: 'input'
                },
                {
                    title: "@lang('print.user')",
                    field: "user.name",
                    sorter: "string",
                    headerFilter: 'input'
                },
                @endif
                @endcan
                {
                    title: "@lang('print.free')",
                    field: "amount",
                    sorter: "number",
                    @can('viewAny', App\Models\FreePages::class) headerFilter: 'input' @endcan
                },
                {
                    title: "@lang('print.deadline')",
                    field: "deadline",
                    sorter: "datetime",
                    formatter:dateFormatter,
                    @can('viewAny', App\Models\FreePages::class) headerFilter: 'input' @endif
                },
                {
                    title: "@lang('print.last_modified_by')",
                    field: "modifier.name",
                    sorter: "string",
                    @can('viewAny', App\Models\FreePages::class) headerFilter: 'input' @endif
                },
                {
                    title: "@lang('general.comment')",
                    field: "comment",
                    sorter: "string",
                    @can('viewAny', App\Models\FreePages::class) headerFilter: 'input' @endif
                },
            ],
        });
    });
</script>
@endif
