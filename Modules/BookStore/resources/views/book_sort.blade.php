@php
    $books = DB::table('books')
        // ->where('course_id', $id)
        ->orderBy('sort')
    ->get(); @endphp
<div class="row">
    <div class="col-12">
        <div id="section-list" class="list-group d-grid gap-2 border-0 mb-3">
            @foreach ($books as $key => $book)
                <div class="list-group-item rounded-3 py-2 px-2 border-1 draggable-item hover-parent d-flex" id="{{ $book->id }}">
                    {{ $book->title }} <span class="ms-auto"><i class="fi-rr-apps-sort text-muted ps-2 border-start me-2 mt-1 hover-show cursor-move"></i></span>
                </div>
            @endforeach
        </div>
        <button class="btn ol-btn-primary" onclick="sort()" >{{ get_phrase('Save Changes') }}</button>
    </div> <!-- end col -->
</div>

<script>
    'use strict';

    $(function() {
        $("#section-list").sortable({
            axis: "y"
        });
    });

    function sort() {
        var containerArray = ['section-list'];
        var itemArray = [];
        var itemJSON;
   

        
        for (var i = 0; i < containerArray.length; i++) {
            $('#' + containerArray[i]).each(function() {
                $(this).find('.draggable-item').each(function() {
                    itemArray.push(this.id);
                });
            });
        }

        itemJSON = JSON.stringify(itemArray);
        $.ajax({
            url: "{{ route('bookstore.sort') }}",
            type: 'POST',
            data: {
                itemJSON: itemJSON,
                _token: "{{ csrf_token() }}", 
            
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                location.reload();
            }
        });
    }
</script>
