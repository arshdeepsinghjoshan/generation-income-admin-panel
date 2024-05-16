<div>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.1/bootstrap3-typeahead.min.js"></script>
    <script>
    var $jq = jQuery.noConflict();
    $jq(document).ready(function() {
        @foreach($column as $row)
            // Initialize the typeahead
            $jq('#{{ isset($row['id']) ? $row['id'] : '' }}').typeahead({
                minLength: 3,
                source: function(query, process) {
                    return $jq.get("{{ isset($row['url']) ? url($row['url']) : ''}}", { query: query }, function(data) {
                        return process(data);
                    });
                },
                @if(isset($row['updater'])) 
                    updater: function(item) {
                        $jq('#{{ isset($row['updater']) ? $row['updater'] : '' }}').val(item.id);
                        return item;
                    }
                @endif
            });
        @endforeach
    });
</script>

</div>