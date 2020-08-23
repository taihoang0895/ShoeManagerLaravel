<div class="autocomplete" style="width:100%;">
    <input class="form-control" id="{{$autocomplete_id}}" type="text"
           placeholder="{{$autocomplete_placeholder}}" value="{{$autocomplete_value}}" autocomplete="off"
           style="background-color:white;">
</div>

<script>
    var autocomplete_data = JSON.parse({!!json_encode($autocomplete_data)!!});
    $(document).ready(function () {
        $("#{{$autocomplete_id}}").autocomplete({
            source: autocomplete_data
        });
    })

</script>
